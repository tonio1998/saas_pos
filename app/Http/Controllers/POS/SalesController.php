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
use App\Models\POS\POSTerminal;
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
            ->with(['variants', 'unit'])
            ->where('tenant_id', auth()->user()->tenant_id);

        if ($request->has('q') && !empty($request->q)) {
            $keyword = trim($request->q);
            $query->where(function ($b) use ($keyword) {
                $b->where('name', 'like', "%{$keyword}%")
                  ->orWhere('barcode', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%")
                  ->orWhereHas('variants', function ($v) use ($keyword) {
                      $v->where('variant_name', 'like', "%{$keyword}%")
                        ->orWhere('barcode', 'like', "%{$keyword}%")
                        ->orWhere('sku', 'like', "%{$keyword}%");
                  });
            });
        }

        $products = $query->get();
        $products->transform(function ($product) {
            if ($product->variants && $product->variants->count() > 0) {
                $variantStockSum = (float) $product->variants->sum('stock_on_hand');
                if ($variantStockSum > 0 || (float) $product->stock_on_hand <= 0) {
                    $product->stock_on_hand = $variantStockSum;
                }
            }
            return $product;
        });

        return response()->json($products);
    }

    public function sales_details(Request $request, $sale)
    {
        $sale = POSSale::with([
            'customer',
            'items.product',
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
        $sale->load(['customer', 'cashier', 'payments', 'items.product']);
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
        ]);

        $profit = $sale->items->sum(function ($item) {

            $cost =
                $item->product?->cost_price ?? 0;

            return (
                ($item->unit_price - $cost)
                * $item->qty
            );
        });

        return response()->json([

            'id' => $sale->id,

            'invoice_no' => $sale->invoice_no,

            'sale_date' => $sale->sale_date
                ? format_date($sale->sale_date)
                : '-',

            'customer' => $sale->customer?->name
                ?? 'Walk-in Customer',

            'cashier' => $sale->cashier?->name
                ?? 'System',

            'status' => ucfirst(
                $sale->status
            ),

            'subtotal' => '₱' . number_format(
                    $sale->subtotal ?? 0,
                    2
                ),

            'discount' => '₱' . number_format(
                    $sale->discount_amount ?? 0,
                    2
                ),

            'total' => '₱' . number_format(
                    $sale->total_amount ?? 0,
                    2
                ),

            'tendered' => '₱' . number_format(
                    $sale->tendered_amount ?? 0,
                    2
                ),

            'change' => '₱' . number_format(
                    $sale->change_amount ?? 0,
                    2
                ),

            'profit' => '₱' . number_format(
                    $profit,
                    2
                ),

            'notes' => $sale->notes ?: '-',

            'payments' => $sale->payments->map(function ($payment) {

                return [

                    'method' => ucfirst(
                        str_replace(
                            '_',
                            ' ',
                            $payment->payment_method
                        )
                    ),

                    'reference' => $payment->reference_number
                        ?: '-',

                    'amount' => '₱' . number_format(
                            $payment->amount ?? 0,
                            2
                        ),

                    'tendered_amount' => '₱' . number_format(
                            $payment->tendered_amount ?? 0,
                            2
                        ),

                    'change_amount' => '₱' . number_format(
                            $payment->change_amount ?? 0,
                            2
                        ),

                    'payment_date' => $payment->payment_date
                        ? format_date(
                            $payment->payment_date
                        )
                        : '-',

                    'notes' => $payment->notes ?: '-',
                ];
            })->values(),

            'items' => $sale->items->map(function ($item) {

                $cost =
                    $item->product?->cost_price ?? 0;

                $profit =
                    (
                        ($item->unit_price - $cost)
                        * $item->qty
                    );

                return [

                    'barcode' => $item->barcode
                        ?: '-',

                    'product' => $item->product_name
                        ?: ($item->product?->name ?? '-'),

                    'quantity' => $item->qty,

                    'price' => '₱' . number_format(
                            $item->unit_price ?? 0,
                            2
                        ),

                    'total' => '₱' . number_format(
                            $item->line_total ?? 0,
                            2
                        ),

                    'profit' => '₱' . number_format(
                            $profit,
                            2
                        ),
                ];
            })->values(),
        ]);
    }

    public function ajaxData(Request $request)
    {
        $query = POSSale::with(['customer', 'cashier', 'payments', 'items', 'items.product',])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->latest();

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($sale) {
                return '
                    <div class="btn-group">
                        <button
                            type="button"
                            class="btn btn-soft-primary btn-sm btn-view-sale"
                            data-id="' . $sale->id . '"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-soft-success btn-sm btn-print-sale"
                            data-id="' . $sale->id . '"
                        >
                            <i class="bi bi-printer"></i>
                        </button>
                    </div>
                ';
            })
            ->addColumn('invoice_number', function ($sale) {
                return '
                <span class="fw-semibold">
                    ' . ($sale->sale_code ?? '-') . '
                </span>
            ';
            })
            ->addColumn('sale_date', function ($sale) {
                return $sale->sale_date
                    ? format_date($sale->sale_date)
                    : 'N/A';
            })
            ->addColumn('customer', function ($sale) {
                return $sale->customer
                    ? '<span class="fw-semibold">' .
                    e($sale->customer->CustomerName) .
                    '</span>'
                    : '<span class="badge bg-light text-dark">
                    Walk-in
                </span>';
            })
            ->addColumn('total_items', function ($sale) {
                return '
                    <span class="badge bg-info">
                        ' . $sale->items->sum('qty') . '
                    </span>
                ';
            })
            ->addColumn('subtotal', function ($sale) {
                return '₱' . number_format(
                        $sale->subtotal ?? 0,
                        2
                    );
            })
            ->addColumn('discount', function ($sale) {
                return '₱' . number_format(
                        $sale->discount_amount ?? 0,
                        2
                    );
            })
            ->addColumn('total', function ($sale) {
                return '
                    <span class="fw-bold text-success">
                        ₱' . number_format(
                            $sale->total_amount ?? 0,
                            2
                        ) . '
                    </span>
                ';
            })
            ->addColumn('profit', function ($sale) {
                $profit = $sale->items->sum(function ($item) {
                    $cost = $item->product->cost_price ?? 0;
                    return (($item->selling_price - $cost) * $item->qty);
                });
                return '
                        <span class="fw-bold text-primary">
                            ₱' . number_format(
                                $profit,
                                2
                            ) . '
                        </span>
                    ';
            })
            ->addColumn('payment_method', function ($sale) {
                return StatusHelper::badge($sale->payment_method);
            })
            ->addColumn('tendered', function ($sale) {
                return '₱' . number_format(
                        $sale->tendered_amount ?? 0,
                        2
                    );
            })
            ->addColumn('change_amount', function ($sale) {
                return '₱' . number_format(
                        $sale->change_amount ?? 0,
                        2
                    );
            })
            ->addColumn('status', function ($sale) {
                return StatusHelper::badge($sale->sale_status);
            })
            ->addColumn('cashier', function ($sale) {
                return $sale->cashier
                    ? '<span class="fw-semibold">' .
                    e($sale->cashier->name) .
                    '</span>'
                    : '<span class="badge bg-light text-dark">
                    System
                </span>';
            })
            ->editColumn('created_at', function ($sale) {
                return $sale->created_at ? $sale->created_at->format('M d, Y h:i A') : 'N/A';
            })
            ->filterColumn('invoice_number', function ($query, $keyword) {
                    $query->where(
                        'invoice_no',
                        'like',
                        "%{$keyword}%"
                    );
            })
            ->rawColumns([
                'actions',
                'invoice_number',
                'customer',
                'total_items',
                'total',
                'profit',
                'payment_method',
                'status',
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
            $customerId = POSSale::where('id', $saleID)->value('customer_id');

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
            $sale->payment_method = count($data['payments']) === 1
                ? $data['payments'][0]['method']
                : 'split';

            $sale->subtotal = $data['subtotal'];
            $sale->discount_amount = $data['discount'];
            $sale->discount_type = $data['discount_type'] ?? null;
            $sale->discount_holder = $data['discount_holder'] ?? null;
            $sale->discount_id_no = $data['discount_id_no'] ?? null;
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
        $salesToday = POSSale::query()
            ->where('tenant_id', $tenantId)
            ->where('sale_date', '>=', now()->subDays(1))
            ->where('sale_date', '<=', now())
            ->sum('subtotal');

        $transactionsToday = POSSale::query()
            ->where('tenant_id', $tenantId)
            ->where('sale_date', '>=', now()->subDays(1))
            ->where('sale_date', '<=', now())
            ->count();

        $averageSale = POSSale::query()
            ->where('tenant_id', $tenantId)
            ->where('sale_date', '>=', now()->subDays(1))
            ->where('sale_date', '<=', now())
            ->avg('subtotal');

        return view('pages.tenants.terminal.index', [
            'salesToday' => $salesToday,
            'transactionsToday' => $transactionsToday,
            'averageSale' => $averageSale,
        ]);
    }

    protected function createSale(Request $request){
        $terminalId = session('terminal_id');
        $drawerId = session('drawer_id');
        $cashShiftId = session('cash_shift_id');

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
            'sales.new',
            [
                encryptId($sale->id)
            ]
        );
    }

    public function create(Request $request)
    {
        if ($request->query('q') === 'new') {
            return $this->createSale($request);
        }

        $sale = POSSale::with([
            'customer',
            'terminal',
            'drawer',
            'cashShift.cashier',
        ])->findOrFail(decryptId($request->segment(3)));

        abort_if($sale->tenant_id !== auth()->user()->tenant_id, 403);

        $categories = POSCategories::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->get();

        $previousSale = POSSale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('id', '<', $sale->id)
            ->orderByDesc('id')
            ->first();

        $nextSale = POSSale::query()
            ->where('tenant_id', auth()->user()->tenant_id)
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
