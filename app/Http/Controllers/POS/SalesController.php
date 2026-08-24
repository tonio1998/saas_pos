<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\InventoryMovement;
use App\Models\POS\POSCategories;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Models\POS\POSProducts;
use App\Models\POS\POSProductVariant;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSSales;
use App\Models\POS\POSTenant;
use App\Models\POS\POSTerminal;
use App\Models\User;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    use TCommonFunctions;
    public function products(Request $request)
    {
        $query = POSProducts::select(
            'id',
            'name',
            'barcode',
            'sku',
            'cost_price',
            'selling_price',
            'wholesale_price',
            'stock_on_hand',
            'category_id',
            'unit_id',
            'allow_decimal_qty',
            'image',
            'updated_at'
        )
            ->with(['variants' => function ($v) {
                $v->where(function ($sq) {
                    $sq->whereNull('status')->orWhere('status', 'active');
                });
            }, 'unit'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            });

        if ($request->has('q') && !empty($request->q)) {
            $keyword = trim($request->q);
            $query->where(function ($b) use ($keyword) {
                $b->where('name', 'like', "%{$keyword}%")
                  ->orWhere('barcode', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhereHas('variants', function ($v) use ($keyword) {
                      $v->where(function ($sq) {
                          $sq->whereNull('status')->orWhere('status', 'active');
                      })
                      ->where(function ($vq) use ($keyword) {
                          $vq->where('variant_name', 'like', "%{$keyword}%")
                             ->orWhere('barcode', 'like', "%{$keyword}%")
                             ->orWhere('sku', 'like', "%{$keyword}%");
                      });
                  });
            });
        }

        $products = $query->get();
        $products = $products->filter(function ($product) {
            if ($product->variants && $product->variants->count() > 0) {
                // Keep only variants with stock > 0
                $inStockVariants = $product->variants->filter(function ($v) {
                    return (float) ($v->stock_on_hand ?? 0) > 0;
                })->values();

                $product->setRelation('variants', $inStockVariants);
                $product->stock_on_hand = (float) $inStockVariants->sum('stock_on_hand');

                return $inStockVariants->count() > 0;
            }

            return (float) ($product->stock_on_hand ?? 0) > 0;
        })->values();

        // Sort products by stock descending, then name ascending
        $products = $products->sort(function ($a, $b) {
            $stockA = (float) ($a->stock_on_hand ?? 0);
            $stockB = (float) ($b->stock_on_hand ?? 0);

            if ($stockA !== $stockB) {
                return $stockB <=> $stockA;
            }
            return strcasecmp($a->name, $b->name);
        })->values();

        return response()->json($products);
    }


    public function sales_details(Request $request, $sale)
    {
        $sale = POSSale::with([
            'customer',
            'items.product',
            'items.variant',
            'payments',
            'cashier'
        ])->findOrFail(($sale));

        return response()->json([
            'success' => true,
            'sale' => $sale
        ]);
    }

    public function birReceipt(POSSale $sale)
    {
        $sale->load(['customer', 'cashier', 'payments', 'items.product', 'items.variant']);
        $tenant = POSTenant::find($sale->tenant_id);

        return view('pages.pos.sales.bir_receipt', compact('sale', 'tenant'));
    }

    public function details(POSSale $sale)
    {
        abort_if(
            $sale->tenant_id !== auth()->user()->tenant_id,
            403
        );

        $sale->load([
            'customer',
            'cashier',
            'payments',
            'items.product',
            'items.variant',
        ]);

        $totalCost = $sale->items->sum(function ($item) {
            $cost = (float) ($item->variant?->cost_price ?: ($item->product?->cost_price ?? 0));
            return $cost * (float) ($item->qty ?? 1);
        });

        $netRevenue = (float) ($sale->total_amount ?? 0);
        $netProfit  = $netRevenue - $totalCost;

        return response()->json([

            'id'             => $sale->id,
            'invoice_no'     => $sale->sale_code ?? $sale->invoice_no,
            'sale_date'      => $sale->sale_date ? format_date($sale->sale_date) : '-',
            'customer'       => $sale->customer?->CustomerName ?: ($sale->customer?->name ?? 'Walk-in Customer'),
            'cashier'        => $sale->cashier?->name ?? 'System',
            'status'         => ucfirst($sale->sale_status ?? 'completed'),
            'payment_method' => $sale->payment_method ?? '-',
            'promo_id'       => $sale->promo_id,
            'discount_type'  => $sale->discount_type,
            'discount_holder'=> $sale->discount_holder ?: null,
            'notes'          => $sale->notes ?: '-',

            // Financial summary (raw + formatted)
            'subtotal'       => '₱' . number_format($sale->subtotal ?? 0, 2),
            'discount'       => '₱' . number_format($sale->discount_amount ?? 0, 2),
            'total'          => '₱' . number_format($sale->total_amount ?? 0, 2),
            'tendered'       => '₱' . number_format($sale->tendered_amount ?? 0, 2),
            'change'         => '₱' . number_format($sale->change_amount ?? 0, 2),

            // Profit summary
            'total_cost'     => '₱' . number_format($totalCost, 2),
            'net_profit'     => '₱' . number_format($netProfit, 2),
            'net_profit_raw' => round($netProfit, 2),
            'total_raw'      => $netRevenue,
            'profit_margin_pct' => $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0,

            'payments' => $sale->payments->map(function ($payment) {
                return [
                    'method'         => ucfirst(str_replace('_', ' ', $payment->payment_method)),
                    'reference'      => $payment->reference_number ?: '-',
                    'amount'         => '₱' . number_format($payment->amount ?? 0, 2),
                    'tendered_amount'=> '₱' . number_format($payment->tendered_amount ?? 0, 2),
                    'change_amount'  => '₱' . number_format($payment->change_amount ?? 0, 2),
                    'payment_date'   => $payment->payment_date ? format_date($payment->payment_date) : '-',
                    'notes'          => $payment->notes ?: '-',
                ];
            })->values(),

            'items' => $sale->items->map(function ($item) {
                $costPrice        = (float) ($item->variant?->cost_price ?: ($item->product?->cost_price ?? 0));
                $unitPrice        = (float) ($item->unit_price ?? 0);
                $discountAmt      = (float) ($item->discount_amount ?? 0);
                $qty              = (float) ($item->qty ?? 1);
                $lineTotal        = (float) ($item->line_total ?? ($unitPrice * $qty - $discountAmt));
                $effectiveUnit    = $qty > 0 ? ($lineTotal / $qty) : $unitPrice;
                $lineCost         = $costPrice * $qty;
                $grossProfit      = ($unitPrice - $costPrice) * $qty;
                $netProfit        = $lineTotal - $lineCost;
                $hasDiscount      = $discountAmt > 0.001;

                $productName = $item->product_name;
                if (!$productName) {
                    if ($item->variant) {
                        $productName = ($item->product?->name ?? 'Product') . ' (' . $item->variant->variant_name . ')';
                    } else {
                        $productName = $item->product?->name ?? '-';
                    }
                }

                return [
                    'barcode'           => $item->barcode ?: ($item->variant?->barcode ?: ($item->product?->barcode ?: '-')),
                    'product'           => $productName,
                    'quantity'          => $qty,
                    'cost_price'        => '₱' . number_format($costPrice, 2),
                    'original_price'    => '₱' . number_format($unitPrice, 2),
                    'effective_price'   => '₱' . number_format($effectiveUnit, 2),
                    'price'             => '₱' . number_format($effectiveUnit, 2), // alias for compatibility
                    'discount_amount'   => '₱' . number_format($discountAmt, 2),
                    'has_discount'      => $hasDiscount,
                    'promo_id'          => $item->promo_id,
                    'line_total'        => '₱' . number_format($lineTotal, 2),
                    'total'             => '₱' . number_format($lineTotal, 2), // alias
                    'cost_total'        => '₱' . number_format($lineCost, 2),
                    'gross_profit'      => '₱' . number_format($grossProfit, 2),
                    'net_profit'        => '₱' . number_format($netProfit, 2),
                    'net_profit_raw'    => round($netProfit, 2),
                    'profit_margin_pct' => $lineTotal > 0 ? round(($netProfit / $lineTotal) * 100, 1) : 0,
                ];
            })->values(),
        ]);
    }



    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = POSSale::with(['customer', 'cashier', 'payments', 'items.product', 'items.variant'])
            ->where('tenant_id', $tenantId)
            ->where('sale_status', 'completed');

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $query->latest('sale_date')->latest('id');

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($sale) {
                return '
                    <div class="d-flex align-items-center gap-1.5 justify-content-center">
                        <button
                            type="button"
                            class="btn btn-sm btn-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 btn-view-sale shadow-xs"
                            data-id="' . $sale->id . '"
                            title="View Full Breakdown & Profit Ledger"
                        >
                            <i class="bi bi-eye-fill me-1"></i><span class="extra-small fw-bold">View</span>
                        </button>
                        <a
                            href="' . route('sales.bir-receipt', $sale->id) . '"
                            target="_blank"
                            class="btn btn-sm btn-light border text-dark rounded-circle d-inline-flex align-items-center justify-content-center shadow-xs"
                            style="width:28px;height:28px;"
                            title="Print Customer Receipt"
                        >
                            <i class="bi bi-printer-fill" style="font-size:0.75rem;"></i>
                        </a>
                    </div>
                ';
            })
            ->addColumn('invoice_number', function ($sale) {
                $code = $sale->sale_code ?: $sale->invoice_no ?: ('#SALE-' . str_pad($sale->id, 5, '0', STR_PAD_LEFT));
                return '
                    <div>
                        <a href="javascript:void(0)" class="btn-view-sale font-mono fw-black text-primary text-decoration-none d-inline-flex align-items-center gap-1" data-id="' . $sale->id . '">
                            <i class="bi bi-receipt extra-small"></i> ' . e($code) . '
                        </a>
                    </div>
                ';
            })
            ->addColumn('sale_date', function ($sale) {
                if (!$sale->sale_date) return '<span class="text-muted">—</span>';
                return '
                    <div>
                        <div class="fw-bold text-dark small">' . $sale->sale_date->format('M d, Y') . '</div>
                        <div class="extra-small text-muted font-mono">' . $sale->sale_date->format('h:i A') . '</div>
                    </div>
                ';
            })
            ->addColumn('customer', function ($sale) {
                if ($sale->customer) {
                    $cName = $sale->customer->CustomerName ?: ($sale->customer->name ?? 'Customer');
                    $initials = strtoupper(substr($cName, 0, 1));
                    return '
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-black d-flex align-items-center justify-content-center extra-small flex-shrink-0" style="width:24px;height:24px;">
                                ' . e($initials) . '
                            </div>
                            <span class="fw-bold text-dark small text-truncate" style="max-width:140px;" title="' . e($cName) . '">' . e($cName) . '</span>
                        </div>
                    ';
                }
                return '<span class="badge rounded-pill bg-light border text-muted extra-small fw-semibold px-2 py-0.5">Walk-in</span>';
            })
            ->addColumn('total_items', function ($sale) {
                $totalQty = (float) $sale->items->sum('qty');
                $count = $sale->items->count();
                $qtyFormatted = fmod($totalQty, 1) === 0.0 ? (int)$totalQty : number_format($totalQty, 1);
                return '
                    <span class="badge rounded-pill px-2.5 py-1 fw-bold font-mono" style="background:#f1f5f9;color:#334155;font-size:0.75rem;">
                        ' . $count . ' item' . ($count > 1 ? 's' : '') . ' <span class="fw-normal text-muted">(' . $qtyFormatted . ' pcs)</span>
                    </span>
                ';
            })
            ->addColumn('subtotal', function ($sale) {
                return '<span class="font-mono text-muted small">₱' . number_format($sale->subtotal ?? 0, 2) . '</span>';
            })
            ->addColumn('discount', function ($sale) {
                $disc = (float) ($sale->discount_amount ?? 0);
                if ($disc > 0.001) {
                    $dType = $sale->discount_type ? '<div class="extra-small text-muted text-uppercase mt-0.5" style="font-size:0.62rem;">' . e($sale->discount_type) . '</div>' : '';
                    return '
                        <div>
                            <span class="badge rounded-pill px-2 py-0.5 fw-bold font-mono" style="background:#fee2e2;color:#dc2626;font-size:0.72rem;">
                                -₱' . number_format($disc, 2) . '
                            </span>
                            ' . $dType . '
                        </div>
                    ';
                }
                return '<span class="text-muted extra-small font-mono">—</span>';
            })
            ->addColumn('total', function ($sale) {
                return '
                    <span class="fw-black text-dark font-mono fs-6">
                        ₱' . number_format($sale->total_amount ?? 0, 2) . '
                    </span>
                ';
            })
            ->addColumn('profit', function ($sale) {
                $costTotal = $sale->items->sum(function ($item) {
                    $cost = (float) ($item->variant?->cost_price ?: ($item->product?->cost_price ?? 0));
                    return $cost * (float) ($item->qty ?? 1);
                });
                $netRevenue = (float) ($sale->total_amount ?? 0);
                $netProfit = $netRevenue - $costTotal;
                $margin = $netRevenue > 0 ? round(($netProfit / $netRevenue) * 100, 1) : 0;

                $isProfitable = $netProfit >= 0;
                $profitColor = $isProfitable ? '#16a34a' : '#dc2626';
                $marginBadgeBg = $margin >= 20 ? '#dcfce7' : ($margin >= 5 ? '#fef9c3' : '#fee2e2');
                $marginBadgeClr = $margin >= 20 ? '#166534' : ($margin >= 5 ? '#854d0e' : '#991b1b');
                $icon = $isProfitable ? '▲' : '▼';

                return '
                    <div>
                        <div class="fw-black font-mono small" style="color:' . $profitColor . ';">
                            ₱' . number_format($netProfit, 2) . '
                        </div>
                        <span class="badge rounded-1 px-1.5 py-0.5 fw-bold font-mono" style="font-size:0.65rem;background:' . $marginBadgeBg . ';color:' . $marginBadgeClr . ';">
                            ' . $icon . ' ' . number_format($margin, 1) . '%
                        </span>
                    </div>
                ';
            })
            ->addColumn('payment_method', function ($sale) {
                $payments = $sale->payments;
                if ($payments && $payments->count() > 1) {
                    $methods = $payments->map(function ($p) {
                        return ucfirst(str_replace('_', ' ', $p->payment_method));
                    })->unique()->implode(' + ');
                    return '<span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background:#ede9fe;color:#6d28d9;font-size:0.72rem;"><i class="bi bi-pie-chart-fill me-1"></i>Split (' . e($methods) . ')</span>';
                }

                $pm = strtolower($sale->payment_method ?? 'cash');
                $pmConfig = [
                    'cash' => ['bg' => '#ecfdf5', 'color' => '#059669', 'icon' => 'bi-cash-stack'],
                    'gcash' => ['bg' => '#eff6ff', 'color' => '#2563eb', 'icon' => 'bi-phone-fill'],
                    'bank_transfer' => ['bg' => '#f0fdfa', 'color' => '#0d9488', 'icon' => 'bi-bank'],
                    'utang' => ['bg' => '#fffbeb', 'color' => '#d97706', 'icon' => 'bi-journal-text'],
                    'credit' => ['bg' => '#fffbeb', 'color' => '#d97706', 'icon' => 'bi-journal-text'],
                ];
                $conf = $pmConfig[$pm] ?? ['bg' => '#f1f5f9', 'color' => '#475569', 'icon' => 'bi-credit-card-2-front'];
                $label = ucfirst(str_replace('_', ' ', $sale->payment_method ?: 'Cash'));

                return '
                    <span class="badge rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1" style="background:' . $conf['bg'] . ';color:' . $conf['color'] . ';font-size:0.72rem;">
                        <i class="bi ' . $conf['icon'] . '"></i> ' . e($label) . '
                    </span>
                ';
            })
            ->addColumn('tendered', function ($sale) {
                $tendered = (float) ($sale->tendered_amount ?? 0);
                $change = (float) ($sale->change_amount ?? 0);
                if ($tendered <= 0 && $sale->payments->count() > 0) {
                    $tendered = (float) $sale->payments->sum('tendered_amount');
                    $change = (float) $sale->payments->sum('change_amount');
                }
                return '
                    <div>
                        <div class="font-mono text-dark small fw-semibold">₱' . number_format($tendered, 2) . '</div>
                        ' . ($change > 0 ? '<div class="extra-small font-mono text-muted">Chg: ₱' . number_format($change, 2) . '</div>' : '') . '
                    </div>
                ';
            })
            ->addColumn('cashier', function ($sale) {
                if ($sale->cashier) {
                    return '
                        <div class="d-flex align-items-center gap-1.5">
                            <i class="bi bi-person-circle text-muted extra-small"></i>
                            <span class="small fw-semibold text-dark">' . e($sale->cashier->name) . '</span>
                        </div>
                    ';
                }
                return '<span class="text-muted extra-small">System</span>';
            })
            ->filterColumn('invoice_number', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('invoice_no', 'like', "%{$keyword}%")
                      ->orWhere('sale_code', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns([
                'actions',
                'invoice_number',
                'sale_date',
                'customer',
                'total_items',
                'subtotal',
                'discount',
                'total',
                'profit',
                'payment_method',
                'tendered',
                'cashier',
            ])
            ->make(true);
    }

    public function updateCustomer(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'exists:pos_customers,id'],
        ]);

        $sale = POSSale::with(['customer'])->find($request->sale_id);

        if($sale->sale_status === 'completed'){
            return response()->json([
                'status' => false,
                'type' => 'warning',
                'sale_status' => $sale->sale_status,
                'message' => 'Sale already completed. Create a new sale to continue.'
            ]);
        }

        $sale->update([
            'customer_id' => $data['customer_id'],
        ]);

        $sale->load('customer');

        return response()->json([
            'success' => true,
            'type' => 'success',
            'message' => 'Customer selected successfully.',
            'customer' => [
                'id' => $sale->customer?->id,
                'name' => $sale->customer?->CustomerName,
                'address' => $sale->customer?->CustomerAddress,
            ],
        ]);
    }

    public function complete(Request $request)
    {
        $data = $request->validate([
            'sale_id' => ['required'],
            'customer_id' => ['nullable'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string'],
            'discount_mode' => ['nullable', 'string'],
            'discount_value' => ['nullable', 'numeric'],
            'discount_holder' => ['nullable', 'string', 'max:255'],
            'discount_id_no' => ['nullable', 'string', 'max:255'],
            'discount_reference' => ['nullable', 'string', 'max:255'],
            'promo_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'string', 'in:cash,gcash,bank_transfer,utang'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.reference_number' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable'],
            'items.*.qty' => ['required', 'numeric', 'min:0.0001'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $saleID = decryptId($request->sale_id);

        $hasUtang = collect($data['payments'])
            ->contains(fn ($payment) => $payment['method'] === 'utang');

        if ($hasUtang) {
            $customerId = $data['customer_id'] ?? POSSale::where('id', $saleID)->value('customer_id');

            if (!$customerId) {
                return response()->json([
                    'success' => false,
                    'type' => 'warning',
                    'sale_status' => 'missing_customer',
                    'message' => 'Please select a customer before proceeding with a credit (Utang) sale.'
                ]);
            }
        }

        $totalPaid = collect($data['payments'])->sum('amount');
        if ($totalPaid < $data['total']) {
            return response()->json([
                'success' => false,
                'type' => 'warning',
                'sale_status' => "Insufficient payment.",
                'message' => 'The total amount paid is less than the total amount due.'
            ]);
        }

        $sale = POSSale::query()
            ->where('id', $saleID)
            ->lockForUpdate()
            ->firstOrFail();

        if ($sale->sale_status === 'completed') {
            return response()->json([
                'success' => false,
                'type' => 'warning',
                'sale_status' => $sale->sale_status,
                'message' => 'Sale already completed. Create a new sale to continue.'
            ]);
        }

        $change = max(0, $totalPaid - $data['total']);
        $sale = null;
        $nextSale = null;

        DB::transaction(function () use (
            $saleID,
            $data,
            $totalPaid,
            $change,
            &$sale,
            &$nextSale
        ) {

            $taxRate = 12;

            $vatableSales = $data['total'] / (1 + ($taxRate / 100));
            $taxAmount = $data['total'] - $vatableSales;

            $sale = POSSale::query()
                ->where('id', $saleID)
                ->lockForUpdate()
                ->firstOrFail();

            $sale->cashier_id = auth()->id();
            if (!empty($data['customer_id'])) {
                $sale->customer_id = $data['customer_id'];
            }
            $sale->payment_method = count($data['payments']) === 1
                ? $data['payments'][0]['method']
                : 'split';

            $sale->subtotal = $data['subtotal'];
            $sale->discount_amount = $data['discount'];
            $sale->discount_type = $data['discount_type'] ?? null;
            $sale->discount_holder = $data['discount_holder'] ?? null;
            $sale->discount_id_no = $data['discount_id_no'] ?? null;
            $sale->discount_reference = $data['discount_reference'] ?? ($data['discount_id_no'] ?? null);
            $sale->promo_id = $data['promo_id'] ?? null;
            $sale->tax_amount = round($taxAmount, 2);
            $sale->total_amount = $data['total'];
            $sale->sale_status = 'completed';
            $sale->sale_date = now();
            $sale->reference_number = collect($data['payments'])
                ->pluck('reference_number')
                ->filter()
                ->implode(', ') ?: null;

            $sale->notes = $data['notes'] ?? null;
            $sale->save();

            // Track promo campaign usage count
            if ($sale->promo_id) {
                \App\Models\POS\POSPromotion::where('id', $sale->promo_id)->increment('usage_count');
            }

            foreach ($data['items'] as $item) {
                $product = POSProducts::findOrFail($item['product_id']);
                $variant = !empty($item['variant_id']) ? POSProductVariant::find($item['variant_id']) : null;

                if ($variant) {
                    $variantStock = (float) ($variant->stock_on_hand ?? 0);
                    $productStock = (float) ($product->stock_on_hand ?? 0);

                    if ($variantStock > 0) {
                        if ($variantStock < $item['qty']) {
                            throw ValidationException::withMessages([
                                'stock' => ["{$product->name} ({$variant->variant_name}) has insufficient stock."]
                            ]);
                        }
                    } elseif ($productStock > 0 && $productStock < ($item['qty'] * ($variant->qty_per_pack ?? 1))) {
                        throw ValidationException::withMessages([
                            'stock' => ["{$product->name} has insufficient stock."]
                        ]);
                    }
                } else {
                    if ($product->stock_on_hand < $item['qty']) {
                        throw ValidationException::withMessages([
                            'stock' => ["{$product->name} has insufficient stock."]
                        ]);
                    }
                }

                $newSaleItem = new POSSaleItem();
                $newSaleItem->sale_id = $saleID;
                $newSaleItem->product_id = $product->id;
                $newSaleItem->variant_id = $variant?->id;
                $newSaleItem->barcode = $variant?->barcode ?: $product->barcode;
                $newSaleItem->sku = $variant?->sku ?: $product->sku;
                $newSaleItem->product_name = $variant ? "{$product->name} ({$variant->variant_name})" : $product->name;
                $newSaleItem->qty = $item['qty'];
                $newSaleItem->unit_price = $item['original_price'] ?? $item['price'];
                $newSaleItem->discount_amount = $item['discount_amount'] ?? 0;
                $newSaleItem->promo_id = $item['promo_id'] ?? null;
                $newSaleItem->tax_amount = 0;
                $newSaleItem->line_total = $item['subtotal'] ?? ($item['qty'] * $item['price']);
                $this->setCommonFields($newSaleItem);
                $newSaleItem->save();

                $newInv = new InventoryMovement();
                $newInv->tenant_id = auth()->user()->tenant_id;
                $newInv->product_id = $product->id;
                $newInv->variant_id = $variant?->id;
                $newInv->movement_type = 'sale';
                $newInv->reference_type = 'sale';
                $newInv->reference_id = $sale->id;
                $newInv->qty = -1 * $item['qty'];
                $this->setCommonFields($newInv);
                $newInv->save();

                if ($variant) {
                    if ($variant->stock_on_hand > 0) {
                        $variant->decrement('stock_on_hand', $item['qty']);
                    }
                    if ($product->stock_on_hand > 0) {
                        $product->decrement('stock_on_hand', $item['qty'] * ($variant->qty_per_pack ?? 1));
                    }
                } else {
                    $product->decrement('stock_on_hand', $item['qty']);
                }
            }

            $paymentIndex = 0;
            $utangAmount = 0;

            foreach ($data['payments'] as $payment) {

                if ($payment['method'] === 'utang') {
                    $utangAmount += $payment['amount'];
                    continue;
                }

                $newPayment = new POSPayment();
                $newPayment->sale_id = $saleID;
                $newPayment->tenant_id = auth()->user()->tenant_id;
                $newPayment->payment_method = $payment['method'];
                $newPayment->amount = $payment['amount'];
                $newPayment->tendered_amount = $payment['amount'];
                $newPayment->change_amount = $paymentIndex === 0 ? $change : 0;
                $newPayment->reference_number = $payment['reference_number'];
                $newPayment->notes = $data['notes'] ?? null;
                $newPayment->payment_date = now();
                $newPayment->terminal_id = $sale->terminal_id;
                $newPayment->drawer_id = $sale->drawer_id;
                $newPayment->shift_id = $sale->cash_shift_id;
                $this->setCommonFields($newPayment);
                $newPayment->save();

                $paymentIndex++;
            }

            if ($utangAmount > 0) {
                if (!$sale->customer_id) {
                    throw ValidationException::withMessages([
                        'customer' => ['Customer is required for credit sales.']
                    ]);
                }

                $lastBalance = POSCustomerLedger::query()
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->where('customer_id', $sale->customer_id)
                    ->latest('id')
                    ->value('running_balance') ?? 0;

                $newLedger = new POSCustomerLedger();
                $newLedger->tenant_id = auth()->user()->tenant_id;
                $newLedger->customer_id = $sale->customer_id;
                $newLedger->sale_id = $sale->id;
                $newLedger->payment_id = null;
                $newLedger->reference_no = $sale->invoice_no;
                $newLedger->transaction_type = 'SALE';
                $newLedger->debit = $utangAmount;
                $newLedger->credit = 0;
                $newLedger->running_balance = $lastBalance + $utangAmount;
                $newLedger->remarks = $data['notes'] ?? null;
                $this->setCommonFields($newLedger);
                $newLedger->save();
            }

            // Auto-create next pending sale for seamless continuous cashiering
            $nextSale = new POSSale();
            $nextSale->tenant_id = auth()->user()->tenant_id;
            $nextSale->terminal_id = $sale->terminal_id;
            $nextSale->drawer_id = $sale->drawer_id;
            $nextSale->cash_shift_id = $sale->cash_shift_id;
            $nextSale->cashier_id = auth()->id();
            $this->setCommonFields($nextSale);
            $nextSale->save();
        });

        return response()->json([
            'success' => true,
            'sale_id' => $saleID,
            'invoice_no' => $sale->invoice_no,
            'payment_method' => $sale->payment_method,
            'reference_number' => $sale->reference_number,
            'payments' => POSPayment::where('sale_id', $sale->id)->get(),
            'message' => 'Sale completed successfully.',
            'next_sale_id' => encryptId($nextSale->id),
            'next_sale_url' => route('sales.create', [
                encryptId($nextSale->id),
                encryptId($sale->terminal_id),
                encryptId($sale->cash_shift_id)
            ]),
        ]);

    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['nullable', 'integer'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'discount_type' => ['nullable', 'string'],
            'discount_mode' => ['nullable', 'string'],
            'discount_value' => ['nullable', 'numeric'],
            'discount_holder' => ['nullable', 'string', 'max:255'],
            'discount_id_no' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', 'string', 'in:cash,gcash,bank_transfer'],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.reference_number' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable'],
            'items.*.qty' => ['required', 'numeric', 'min:0.0001'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ]);

        $totalPaid = collect($data['payments'])->sum('amount');
        if ($totalPaid < $data['total']) {
            throw ValidationException::withMessages([
                'payments' => [
                    'Insufficient payment.'
                ]
            ]);
        }

        $change = max(0, $totalPaid - $data['total']);
        $sale = null;

        DB::transaction(function () use (
            $data,
            $totalPaid,
            $change,
            &$sale
        ) {
            $discountType = $data['discount_type'] ?? null;
            $isScPwd = in_array($discountType, ['sc_pwd', 'senior', 'pwd']) || !empty($data['discount_id_no']);

            $subtotal = $data['subtotal'];
            $discount = $data['discount'];
            $total = $data['total'];

            if ($isScPwd) {
                $vatExemptSales = round($subtotal / 1.12, 2);
                $vatableSales = 0;
                $taxAmount = 0;
                $zeroRatedSales = 0;
            } elseif ($discountType === 'zero_rated') {
                $vatExemptSales = 0;
                $vatableSales = 0;
                $taxAmount = 0;
                $zeroRatedSales = $total;
            } else {
                $vatableSales = round($total / 1.12, 2);
                $taxAmount = round($total - $vatableSales, 2);
                $vatExemptSales = 0;
                $zeroRatedSales = 0;
            }

            $sale = new POSSale();
            $sale->tenant_id = auth()->user()->tenant_id;
            $sale->invoice_no = generateSaleInvoiceNo();
            $sale->cashier_id = auth()->id();
            $sale->customer_id = $data['customer_id'];
            $sale->payment_method = count($data['payments']) === 1
                    ? $data['payments'][0]['method']
                    : 'split';
            $sale->subtotal = $subtotal;
            $sale->discount_amount = $discount;
            $sale->discount_type = $discountType;
            $sale->discount_holder = $data['discount_holder'] ?? null;
            $sale->discount_id_no = $data['discount_id_no'] ?? null;
            $sale->vatable_sales = $vatableSales;
            $sale->vat_exempt_sales = $vatExemptSales;
            $sale->zero_rated_sales = $zeroRatedSales;
            $sale->tax_amount = $taxAmount;
            $sale->total_amount = $total;
            $sale->tendered_amount = $totalPaid;
            $sale->change_amount = $change;
            $sale->sale_status = 'completed';
            $sale->sale_date = now();
            $sale->reference_number =
                collect(
                    $data['payments']
                )
                    ->pluck(
                        'reference_number'
                    )
                    ->filter()
                    ->implode(
                        ', '
                    ) ?: null;

            $sale->notes = $data['notes'] ?? null;
            $this->setCommonFields($sale);
            $sale->save();

            foreach ($data['items'] as $item) {
                $product = POSProducts::findOrFail($item['product_id']);
                $variant = !empty($item['variant_id']) ? POSProductVariant::find($item['variant_id']) : null;

                if ($variant) {
                    $variantStock = (float) ($variant->stock_on_hand ?? 0);
                    $productStock = (float) ($product->stock_on_hand ?? 0);

                    if ($variantStock > 0) {
                        if ($variantStock < $item['qty']) {
                            throw ValidationException::withMessages([
                                'stock' => ["{$product->name} ({$variant->variant_name}) has insufficient stock."]
                            ]);
                        }
                    } elseif ($productStock > 0 && $productStock < ($item['qty'] * ($variant->qty_per_pack ?? 1))) {
                        throw ValidationException::withMessages([
                            'stock' => ["{$product->name} has insufficient stock."]
                        ]);
                    }
                } else {
                    if ($product->stock_on_hand < $item['qty']) {
                        throw ValidationException::withMessages([
                            'stock' => ["{$product->name} has insufficient stock."]
                        ]);
                    }
                }

                $newSaleItem = new POSSaleItem();
                $newSaleItem->sale_id = $sale->id;
                $newSaleItem->product_id = $product->id;
                $newSaleItem->variant_id = $variant?->id;
                $newSaleItem->barcode = $variant?->barcode ?: $product->barcode;
                $newSaleItem->sku = $variant?->sku ?: $product->sku;
                $newSaleItem->product_name = $variant ? "{$product->name} ({$variant->variant_name})" : $product->name;
                $newSaleItem->qty = $item['qty'];
                $newSaleItem->unit_price = $item['price'];
                $newSaleItem->discount_amount = 0;
                $newSaleItem->tax_amount = 0;
                $newSaleItem->line_total = $item['qty'] * $item['price'];
                $this->setCommonFields($newSaleItem);
                $newSaleItem->save();

                $newInv = new InventoryMovement();
                $newInv->tenant_id = auth()->user()->tenant_id;
                $newInv->product_id = $product->id;
                $newInv->variant_id = $variant?->id;
                $newInv->movement_type = 'sale';
                $newInv->reference_type = 'sale';
                $newInv->reference_id = $sale->id;
                $newInv->qty = -1 * $item['qty'];
                $this->setCommonFields($newInv);
                $newInv->save();

                if ($variant) {
                    if ($variant->stock_on_hand > 0) {
                        $variant->decrement('stock_on_hand', $item['qty']);
                    }
                    if ($product->stock_on_hand > 0) {
                        $product->decrement('stock_on_hand', $item['qty'] * ($variant->qty_per_pack ?? 1));
                    }
                } else {
                    $product->decrement('stock_on_hand', $item['qty']);
                }
            }

            foreach ($data['payments'] as $index => $payment) {

                $newPayment = new POSPayment();
                $newPayment->sale_id = $sale->id;
                $newPayment->payment_method = $payment['method'];
                $newPayment->amount = $payment['amount'];
                $newPayment->tendered_amount = $payment['amount'];
                $newPayment->change_amount = $index === 0 ? $change : 0;
                $newPayment->reference_number = $payment['reference_number'];
                $newPayment->notes = $data['notes'] ?? null;
                $newPayment->payment_date = now();
                $this->setCommonFields($newPayment);
                $this->setCommonFields($newPayment);
                $newPayment->save();

            }

        });

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'invoice_no' => $sale->invoice_no,
            'payment_method' => $sale->payment_method,
            'reference_number' => $sale->reference_number,
            'payments' => POSPayment::where('sale_id', $sale->id)->get(),
            'message' => 'Sale completed successfully.',
        ]);

    }

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $startOfDay = now()->startOfDay();
        $endOfDay = now()->endOfDay();

        $todaySalesQuery = POSSale::query()
            ->with(['items.product', 'items.variant'])
            ->where('tenant_id', $tenantId)
            ->where('sale_status', 'completed')
            ->where('sale_date', '>=', $startOfDay)
            ->where('sale_date', '<=', $endOfDay);

        $todaySales = $todaySalesQuery->get();

        $salesToday = (float) $todaySales->sum('total_amount');
        $transactionsToday = $todaySales->count();
        $averageSale = $transactionsToday > 0 ? ($salesToday / $transactionsToday) : 0;

        $profitToday = (float) $todaySales->sum(function ($sale) {
            $cost = $sale->items->sum(function ($item) {
                $c = (float) ($item->variant?->cost_price ?: ($item->product?->cost_price ?? 0));
                return $c * (float) ($item->qty ?? 1);
            });
            return (float) ($sale->total_amount ?? 0) - $cost;
        });

        $profitMarginToday = $salesToday > 0 ? round(($profitToday / $salesToday) * 100, 1) : 0;

        $cashiers = User::where('tenant_id', $tenantId)->get(['id', 'name']);

        return view('pages.tenants.terminal.index', [
            'salesToday' => $salesToday,
            'transactionsToday' => $transactionsToday,
            'averageSale' => $averageSale,
            'profitToday' => $profitToday,
            'profitMarginToday' => $profitMarginToday,
            'cashiers' => $cashiers,
        ]);
    }

    protected function createSale(Request $request)
    {
        $saleParam = $request->route('sale') ?? $request->segment(3);
        $currentSale = null;
        try {
            $decrypted = decryptId($saleParam);
            if ($decrypted) {
                $currentSale = POSSale::find($decrypted);
            }
        } catch (\Exception $e) {}

        $terminalId = $currentSale?->terminal_id ?? session('terminal_id');
        $drawerId = $currentSale?->drawer_id ?? session('drawer_id');
        $cashShiftId = $currentSale?->cash_shift_id ?? session('cash_shift_id');

        if (!$terminalId) {
            $terminal = POSTerminal::where('tenant_id', auth()->user()->tenant_id)->first();
            $terminalId = $terminal?->id;
            $drawerId = $terminal?->drawer_id;
        }

        $sale = new POSSale();
        $sale->tenant_id = auth()->user()->tenant_id;
        $sale->terminal_id = $terminalId;
        $sale->drawer_id = $drawerId;
        $sale->cash_shift_id = $cashShiftId;
        $this->setCommonFields($sale);
        $sale->save();

        session()->put([
            'terminal_id'   => $terminalId,
            'drawer_id'     => $drawerId,
            'cash_shift_id' => $cashShiftId,
            'sale_id'       => $sale->id,
        ]);

        return redirect()->route(
            'sales.create',
            [
                encryptId($sale->id)
            ]
        );
    }

    public function ajaxNewSale(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $currentSaleId = $request->input('current_sale_id') ? decryptId($request->input('current_sale_id')) : null;
        $currentSale = $currentSaleId ? POSSale::find($currentSaleId) : null;

        $terminalId = $currentSale?->terminal_id ?? session('terminal_id');
        $drawerId = $currentSale?->drawer_id ?? session('drawer_id');
        $cashShiftId = $currentSale?->cash_shift_id ?? session('cash_shift_id');

        if (!$terminalId) {
            $terminal = POSTerminal::where('tenant_id', $tenantId)->first();
            $terminalId = $terminal?->id;
            $drawerId = $terminal?->drawer_id;
        }

        $sale = new POSSale();
        $sale->tenant_id = $tenantId;
        $sale->terminal_id = $terminalId;
        $sale->drawer_id = $drawerId;
        $sale->cash_shift_id = $cashShiftId;
        $this->setCommonFields($sale);
        $sale->save();

        session()->put([
            'terminal_id'   => $terminalId,
            'drawer_id'     => $drawerId,
            'cash_shift_id' => $cashShiftId,
            'sale_id'       => $sale->id,
        ]);

        return response()->json([
            'success' => true,
            'sale_id' => encryptId($sale->id),
            'sale_code' => $sale->sale_code,
            'sale_url' => route('sales.create', [encryptId($sale->id)]),
            'message' => 'New transaction started.'
        ]);
    }


    public function create(Request $request)
    {
        if ($request->query('q') === 'new') {
            return $this->createSale($request);
        }

        $saleParam = $request->route('sale') ?? $request->segment(3);
        $decryptedId = null;
        try {
            $decryptedId = decryptId($saleParam);
        } catch (\Exception $e) {}

        if (!$decryptedId) {
            return redirect()->route('terminal.index');
        }

        $sale = POSSale::with([
            'customer',
            'terminal',
            'drawer',
            'cashShift.cashier',
            'items.product.unit',
            'items.variant',
            'payments'
        ])->find($decryptedId);

        if (!$sale || $sale->tenant_id !== auth()->user()->tenant_id) {
            return redirect()->route('terminal.index');
        }

        $categories = POSCategories::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();

        $previousSale = POSSale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('sale_status', 'completed')
            ->where('id', '<', $sale->id)
            ->orderByDesc('id')
            ->first();

        $nextSale = POSSale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('sale_status', 'completed')
            ->where('id', '>', $sale->id)
            ->orderBy('id')
            ->first();

        $tenant = \App\Models\POS\POSTenant::with('subscription')->find(auth()->user()->tenant_id);
        $subscription = $tenant?->subscription;
        $planName = $subscription?->name ?? 'Tindahan Starter';
        $planTier = strtolower($planName);
        $isStarter = str_contains($planTier, 'starter') || str_contains($planTier, 'level i');
        $isGrowth = str_contains($planTier, 'growth') || str_contains($planTier, 'level ii');
        $isPro = str_contains($planTier, 'pro') || str_contains($planTier, 'level iii');

        return view(
            'pages.tenants.terminal.create',
            compact(
                'sale',
                'categories',
                'previousSale',
                'nextSale',
                'tenant',
                'subscription',
                'planName',
                'isStarter',
                'isGrowth',
                'isPro'
            )
        );
    }

    public function newSale(Request $request)
    {
        $terminal = POSTerminal::findOrFail(decryptId($request->segment(3)));
        $shift = POSCashShift::findOrFail(decryptId($request->segment(4)));

        $sale = new POSSale();
        $sale->tenant_id = auth()->user()->tenant_id;
        $sale->terminal_id = $terminal->id;
        $sale->drawer_id = $terminal->drawer_id;
        $sale->cash_shift_id = $shift->id;
        $sale->cashier_id = auth()->id();
        $this->setCommonFields($sale);
        $sale->save();

        return redirect()->route(
            'sales.create',
            [
                encryptId($sale->id),
                encryptId($terminal->id),
                encryptId($shift->id)
            ]
        );
    }

//    public function create1()
//    {
//        $sale = new POSSale();
//        $sale->tenant_id = auth()->user()->tenant_id;
//        $this->setCommonFields($sale);
//        $sale->save();
//
//        $sale->sale_code = generateSalesCode(auth()->user()->tenant_id);
//        $sale->save();
//
//        return redirect()->route(
//            'sales.create',
//            encryptId($sale->id)
//        );
//    }

    public function create1()
    {
        return redirect()->route('terminal.index');
    }
}
