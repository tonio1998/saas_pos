<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSProducts;
use App\Models\POS\InventoryMovement;
use App\Models\POS\POSCashShift;
use App\Models\POS\POSCashMovement;
use App\Models\POS\POSPayment;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    use TCommonFunctions;

    /**
     * Parse a raw product_id which may be:
     *   - plain numeric:              "42"
     *   - encrypted:                  "eyJ..."
     *   - compound (variant):         "eyJ...:variant:5"
     * Returns ['product_id' => int, 'variant_id' => int|null]
     */
    private function parseProductSelection($rawId): array
    {
        if (empty($rawId)) return ['product_id' => 0, 'variant_id' => null];

        // Compound format: "<encryptedProductId>:variant:<variantId>"
        if (str_contains($rawId, ':variant:')) {
            [$encProductId, , $variantId] = explode(':', $rawId, 3);
            $productId = $this->parseProductId($encProductId);
            return ['product_id' => $productId, 'variant_id' => (int) $variantId];
        }

        return ['product_id' => $this->parseProductId($rawId), 'variant_id' => null];
    }

    private function parseProductId($rawId): int
    {
        if (empty($rawId)) return 0;
        if (is_numeric($rawId)) return (int)$rawId;

        $dec = decryptId($rawId);
        if (!empty($dec) && is_numeric($dec)) return (int)$dec;

        try {
            $decCrypt = decrypt($rawId);
            if (!empty($decCrypt) && is_numeric($decCrypt)) return (int)$decCrypt;
        } catch (\Throwable $e) {}

        return 0;
    }

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $returnMovements = InventoryMovement::where('tenant_id', $tenantId)
            ->whereIn('movement_type', ['return', 'RETURN', 'sale_return', 'refund'])
            ->with('product')
            ->get();

        $totalReturnsCount = $returnMovements->count();
        $totalReturnedQty = $returnMovements->sum('qty');
        $totalRefundValue = $returnMovements->sum(function($m) {
            return (float)$m->qty * (float)($m->product?->selling_price ?? 0);
        });

        return view('pages.pos.returns.index', compact(
            'totalReturnsCount',
            'totalReturnedQty',
            'totalRefundValue'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required',
            'qty'        => 'required|numeric|min:0.01',
            'invoice_no' => 'nullable|string|max:100',
            'reason'     => 'nullable|string|max:500',
        ]);

        $parsed    = $this->parseProductSelection($validated['product_id']);
        $productId = $parsed['product_id'];
        $variantId = $parsed['variant_id'];

        DB::transaction(function () use ($validated, $productId, $variantId) {
            $tenantId = auth()->user()->tenant_id;
            $product  = POSProducts::findOrFail($productId);
            $variant  = $variantId ? $product->variants()->find($variantId) : null;
            $qty      = (float) $validated['qty'];
            $price    = (float) ($variant?->selling_price ?? $product->selling_price ?? 0);
            $refundTotal = -1 * ($qty * $price);
            $refundPositive = $qty * $price;

            // ── Find original sale (if invoice provided) ──────────────────
            $originalSale = null;
            if (!empty($validated['invoice_no'])) {
                $originalSale = POSSale::where('tenant_id', $tenantId)
                    ->where(function($q) use ($validated) {
                        $q->where('invoice_no', $validated['invoice_no'])
                          ->orWhere('sale_code', $validated['invoice_no']);
                    })->first();
            }

            // ── Find active cash shift / drawer ───────────────────────────
            $activeShift = POSCashShift::where('tenant_id', $tenantId)
                ->where('status', 'open')
                ->where('cashier_id', auth()->id())
                ->latest('id')
                ->first();

            if (!$activeShift) {
                $activeShift = POSCashShift::where('tenant_id', $tenantId)
                    ->where('status', 'open')
                    ->latest('id')
                    ->first();
            }

            $shiftId  = $activeShift?->id ?? $originalSale?->cash_shift_id;
            $drawerId = $activeShift?->drawer_id ?? $originalSale?->drawer_id;
            $invRef   = $originalSale?->invoice_no ?: ($originalSale?->sale_code ?: ($validated['invoice_no'] ?? null));

            // ── 1. Create negative refund sale (deducts from revenue) ─────
            $refundSale = new POSSale();
            $refundSale->tenant_id       = $tenantId;
            $refundSale->customer_id     = $originalSale?->customer_id;
            $refundSale->cashier_id      = auth()->id();
            $refundSale->cash_shift_id   = $shiftId;
            $refundSale->drawer_id       = $drawerId;
            $refundSale->reference_number= $invRef;
            $refundSale->invoice_no      = 'RTN-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $refundSale->subtotal        = $refundTotal;
            $refundSale->total_amount    = $refundTotal;  // NEGATIVE → auto-deducts from SUM
            $refundSale->tendered_amount = 0;
            $refundSale->change_amount   = 0;
            $refundSale->sale_status     = 'refund';      // refund transaction record
            $refundSale->sale_date       = now();
            $refundSale->notes           = 'REFUND: ' . ($validated['reason'] ?? 'Customer Return')
                . ($invRef ? ' [Ref: ' . $invRef . ']' : '');
            $this->setCommonFields($refundSale);
            $refundSale->save();

            // ── 2. Create refund sale item ─────────────────────────────────
            $refundItem = new POSSaleItem();
            $refundItem->sale_id      = $refundSale->id;
            $refundItem->product_id   = $product->id;
            $refundItem->variant_id   = $variantId;
            $refundItem->product_name = $variant
                ? "{$product->name} ({$variant->variant_name})"
                : $product->name;
            $refundItem->qty          = -1 * $qty;  // negative qty
            $refundItem->unit_price   = $price;
            $refundItem->line_total   = $refundTotal;
            $this->setCommonFields($refundItem);
            $refundItem->save();

            // ── 3. Restock inventory ──────────────────────────────────────
            if ($variant) {
                $variant->increment('stock_on_hand', $qty);
                $product->increment('stock_on_hand', $qty * ($variant->qty_per_pack ?? 1));
            } else {
                $product->increment('stock_on_hand', $qty);
            }

            // ── 4. Inventory movement log ─────────────────────────────────
            $baseRemarks = $validated['reason'] ?? 'Customer Return';
            $inv = new InventoryMovement();
            $inv->tenant_id      = $tenantId;
            $inv->product_id     = $product->id;
            $inv->variant_id     = $variantId;
            $inv->movement_type  = 'return';
            $inv->reference_type = 'sale_return';
            $inv->reference_id   = $refundSale->id;
            $inv->qty            = $qty;
            $inv->remarks        = ($variant ? '[Variant: ' . $variant->variant_name . '] ' : '')
                . $baseRemarks . ($invRef ? ' [Ref: ' . $invRef . ']' : '');
            $this->setCommonFields($inv);
            $inv->save();

            // ── 5. Cash Drawer Deduction (POSCashMovement & POSPayment) ───
            if ($refundPositive > 0 && ($shiftId || $drawerId)) {
                $cm = new POSCashMovement();
                $cm->tenant_id     = $tenantId;
                $cm->movement_code = 'CM-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $cm->cash_shift_id = $shiftId;
                $cm->drawer_id     = $drawerId;
                $cm->cashier_id    = auth()->id();
                $cm->type          = 'OUT';
                $cm->category      = 'refund';
                $cm->amount        = $refundPositive;
                $cm->reference_no  = $invRef ?: $refundSale->invoice_no;
                $cm->remarks       = 'Sales Return / Refund Payout [Ref: ' . ($invRef ?: $refundSale->invoice_no) . ']';
                $cm->movement_date = now();
                $cm->status        = 'active';
                $this->setCommonFields($cm);
                $cm->save();

                $payment = new POSPayment();
                $payment->tenant_id        = $tenantId;
                $payment->customer_id      = $originalSale?->customer_id;
                $payment->sale_id          = $refundSale->id;
                $payment->shift_id         = $shiftId;
                $payment->drawer_id        = $drawerId;
                $payment->payment_method   = $originalSale?->payment_method ?: 'cash';
                $payment->amount           = $refundTotal;
                $payment->reference_number = $invRef ?: $refundSale->invoice_no;
                $payment->payment_date     = now();
                $payment->notes            = 'Refund payout from register drawer';
                $payment->status           = 'active';
                $this->setCommonFields($payment);
                $payment->save();
            }

            // ── 6. Update Original Sale Status if Applicable ──────────────
            if ($originalSale) {
                $this->syncOriginalSaleStatus($originalSale);
            }
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Return processed. Cash deducted from drawer & inventory restocked.']);
        }
        return redirect()->route('returns.index')->with('success', 'Return processed. Cash deducted from drawer & inventory restocked.');
    }

    public function searchInvoice(Request $request)
    {
        $invoiceNo = trim($request->get('invoice_no'));
        if (!$invoiceNo) {
            return response()->json(['success' => false, 'message' => 'Please enter a Sales Invoice or Receipt Number.']);
        }

        $tenantId = auth()->user()->tenant_id;

        // Find exact or partial match
        $sale = POSSale::where('tenant_id', $tenantId)
            ->where(function($q) use ($invoiceNo) {
                $q->where('invoice_no', $invoiceNo)
                  ->orWhere('sale_code', $invoiceNo);
            })
            ->with(['customer', 'items.product', 'items.variant', 'cashier'])
            ->first();

        if (!$sale) {
            $sale = POSSale::where('tenant_id', $tenantId)
                ->where(function($q) use ($invoiceNo) {
                    $q->where('invoice_no', 'LIKE', '%' . $invoiceNo . '%')
                      ->orWhere('sale_code', 'LIKE', '%' . $invoiceNo . '%');
                })
                ->with(['customer', 'items.product', 'items.variant', 'cashier'])
                ->first();
        }

        if (!$sale) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice or Sale reference "' . $invoiceNo . '" was not found.'
            ]);
        }

        $status = strtolower($sale->sale_status ?? 'completed');

        // Check if status is invalid for return
        if ($status === 'refunded') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice [' . ($sale->invoice_no ?: $sale->sale_code) . '] has ALREADY been fully refunded.'
            ]);
        }

        if ($status === 'refund') {
            return response()->json([
                'success' => false,
                'message' => 'This record is a refund transaction, not an original sale invoice.'
            ]);
        }

        if ($status === 'voided') {
            return response()->json([
                'success' => false,
                'message' => 'This invoice has been voided and cannot be returned.'
            ]);
        }

        if (!in_array($status, ['completed', 'partial_refund'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only completed sales can be processed for return (Current Status: ' . ucfirst($status) . ').'
            ]);
        }

        // ── Calculate previous refunds for this sale ───────────────────────
        $ref1 = $sale->invoice_no;
        $ref2 = $sale->sale_code;

        $refundSales = POSSale::where('tenant_id', $tenantId)
            ->where('sale_status', 'refund')
            ->where(function($q) use ($ref1, $ref2) {
                if ($ref1) {
                    $q->where('reference_number', $ref1)
                      ->orWhere('notes', 'LIKE', '%[Ref: ' . $ref1 . ']%');
                }
                if ($ref2) {
                    $q->orWhere('reference_number', $ref2)
                      ->orWhere('notes', 'LIKE', '%[Ref: ' . $ref2 . ']%');
                }
            })
            ->pluck('id');

        $returnedMap = [];
        if ($refundSales->isNotEmpty()) {
            $returnedItems = POSSaleItem::whereIn('sale_id', $refundSales)
                ->select('product_id', 'variant_id', DB::raw('SUM(ABS(qty)) as total_returned'))
                ->groupBy('product_id', 'variant_id')
                ->get();

            foreach ($returnedItems as $ri) {
                $key = ($ri->product_id ?? 0) . '_' . ($ri->variant_id ?? 0);
                $returnedMap[$key] = (float) $ri->total_returned;
            }
        }

        // ── Filter only items that still have returnable quantity ──────────
        $availableItems = [];
        foreach ($sale->items as $item) {
            $key = ($item->product_id ?? 0) . '_' . ($item->variant_id ?? 0);
            $alreadyReturned = $returnedMap[$key] ?? 0;
            $purchasedQty    = (float) $item->qty;
            $remainingQty    = max(0, $purchasedQty - $alreadyReturned);

            if ($remainingQty > 0.0001) {
                $item->purchased_qty        = $purchasedQty;
                $item->already_returned_qty = $alreadyReturned;
                $item->remaining_qty        = $remainingQty;
                $item->qty                  = $remainingQty; // default returnable qty

                $item->item_name = $item->product_name ?: ($item->product?->name ?? 'Product');
                if (preg_match('/\((.+)\)$/', $item->item_name, $m)) {
                    $item->variant_name = trim($m[1]);
                } else {
                    $item->variant_name = $item->variant?->variant_name ?? null;
                }
                $availableItems[] = $item;
            }
        }

        if (empty($availableItems)) {
            // Update sale status to refunded if everything was already returned
            $sale->update(['sale_status' => 'refunded']);

            return response()->json([
                'success' => false,
                'message' => 'All items from Invoice [' . ($sale->invoice_no ?: $sale->sale_code) . '] have already been fully returned/refunded.'
            ]);
        }

        $sale->setRelation('items', collect($availableItems));

        return response()->json(['success' => true, 'sale' => $sale]);
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'invoice_no'         => 'required|string',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.qty'        => 'required|numeric|min:0.01',
            'reason'             => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $tenantId  = auth()->user()->tenant_id;
            $invoiceNo = trim($request->input('invoice_no'));
            $reason    = $request->input('reason') ?: 'Partial/Full Return';

            $originalSale = POSSale::where('tenant_id', $tenantId)
                ->where(function($q) use ($invoiceNo) {
                    $q->where('invoice_no', $invoiceNo)
                      ->orWhere('sale_code', $invoiceNo);
                })->first();

            // ── Find active cash shift / drawer ───────────────────────────
            $activeShift = POSCashShift::where('tenant_id', $tenantId)
                ->where('status', 'open')
                ->where('cashier_id', auth()->id())
                ->latest('id')
                ->first();

            if (!$activeShift) {
                $activeShift = POSCashShift::where('tenant_id', $tenantId)
                    ->where('status', 'open')
                    ->latest('id')
                    ->first();
            }

            $shiftId  = $activeShift?->id ?? $originalSale?->cash_shift_id;
            $drawerId = $activeShift?->drawer_id ?? $originalSale?->drawer_id;
            $invRef   = $originalSale?->invoice_no ?: ($originalSale?->sale_code ?: $invoiceNo);

            // ── 1. Create one refund sale for the whole batch ─────────────
            $refundSale = new POSSale();
            $refundSale->tenant_id       = $tenantId;
            $refundSale->customer_id     = $originalSale?->customer_id;
            $refundSale->cashier_id      = auth()->id();
            $refundSale->cash_shift_id   = $shiftId;
            $refundSale->drawer_id       = $drawerId;
            $refundSale->reference_number= $invRef;
            $refundSale->invoice_no      = 'RTN-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $refundSale->sale_status     = 'refund';
            $refundSale->sale_date       = now();
            $refundSale->subtotal        = 0;  // computed below
            $refundSale->total_amount    = 0;  // computed below
            $refundSale->tendered_amount = 0;
            $refundSale->change_amount   = 0;
            $refundSale->notes           = 'REFUND: ' . $reason
                . ' [Ref: ' . $invRef . ']';
            $this->setCommonFields($refundSale);
            $refundSale->save();

            $batchTotal = 0;

            foreach ($request->input('items') as $item) {
                $parsed    = $this->parseProductSelection($item['product_id']);
                $productId = $parsed['product_id'];
                $variantId = $parsed['variant_id'];
                $qty       = (float) $item['qty'];

                if ($qty <= 0 || !$productId) continue;

                $product = POSProducts::find($productId);
                if (!$product) continue;

                $variant = $variantId ? $product->variants()->find($variantId) : null;
                $price   = (float) ($variant?->selling_price ?? $product->selling_price ?? 0);
                $lineTotal = -1 * ($qty * $price);
                $batchTotal += $lineTotal;

                // ── 2. Refund sale item ───────────────────────────────────
                $refundItem = new POSSaleItem();
                $refundItem->sale_id      = $refundSale->id;
                $refundItem->product_id   = $product->id;
                $refundItem->variant_id   = $variantId;
                $refundItem->product_name = $variant
                    ? "{$product->name} ({$variant->variant_name})"
                    : $product->name;
                $refundItem->qty          = -1 * $qty;
                $refundItem->unit_price   = $price;
                $refundItem->line_total   = $lineTotal;
                $this->setCommonFields($refundItem);
                $refundItem->save();

                // ── 3. Restock inventory ──────────────────────────────────
                if ($variant) {
                    $variant->increment('stock_on_hand', $qty);
                    $product->increment('stock_on_hand', $qty * ($variant->qty_per_pack ?? 1));
                } else {
                    $product->increment('stock_on_hand', $qty);
                }

                // ── 4. Inventory movement log ─────────────────────────────
                $inv = new InventoryMovement();
                $inv->tenant_id      = $tenantId;
                $inv->product_id     = $product->id;
                $inv->variant_id     = $variantId;
                $inv->movement_type  = 'return';
                $inv->reference_type = 'sale_return';
                $inv->reference_id   = $refundSale->id;
                $inv->qty            = $qty;
                $inv->remarks        = ($variant ? '[Variant: ' . $variant->variant_name . '] ' : '')
                    . $reason . ' [Ref: ' . $invRef . ']';
                $this->setCommonFields($inv);
                $inv->save();
            }

            // ── 5. Update refund sale totals ──────────────────────────────
            $refundSale->subtotal     = $batchTotal;
            $refundSale->total_amount = $batchTotal;  // negative → auto-deducts from SUM
            $refundSale->save();

            // ── 6. Cash Drawer Deduction (POSCashMovement & POSPayment) ───
            $refundPositive = abs($batchTotal);
            if ($refundPositive > 0 && ($shiftId || $drawerId)) {
                $cm = new POSCashMovement();
                $cm->tenant_id     = $tenantId;
                $cm->movement_code = 'CM-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
                $cm->cash_shift_id = $shiftId;
                $cm->drawer_id     = $drawerId;
                $cm->cashier_id    = auth()->id();
                $cm->type          = 'OUT';
                $cm->category      = 'refund';
                $cm->amount        = $refundPositive;
                $cm->reference_no  = $invRef ?: $refundSale->invoice_no;
                $cm->remarks       = 'Sales Return / Refund Payout [Ref: ' . ($invRef ?: $refundSale->invoice_no) . ']';
                $cm->movement_date = now();
                $cm->status        = 'active';
                $this->setCommonFields($cm);
                $cm->save();

                $payment = new POSPayment();
                $payment->tenant_id        = $tenantId;
                $payment->customer_id      = $originalSale?->customer_id;
                $payment->sale_id          = $refundSale->id;
                $payment->shift_id         = $shiftId;
                $payment->drawer_id        = $drawerId;
                $payment->payment_method   = $originalSale?->payment_method ?: 'cash';
                $payment->amount           = $batchTotal;
                $payment->reference_number = $invRef ?: $refundSale->invoice_no;
                $payment->payment_date     = now();
                $payment->notes            = 'Refund payout from register drawer';
                $payment->status           = 'active';
                $this->setCommonFields($payment);
                $payment->save();
            }

            // ── 7. Update Original Sale Status ────────────────────────────
            if ($originalSale) {
                $this->syncOriginalSaleStatus($originalSale);
            }
        });

        return response()->json(['success' => true, 'message' => 'Return processed successfully. Cash deducted from drawer & inventory restocked.']);
    }

    /**
     * Helper to synchronize the original sale status (refunded or partial_refund)
     */
    private function syncOriginalSaleStatus(POSSale $originalSale): void
    {
        $tenantId = $originalSale->tenant_id;
        $ref1 = $originalSale->invoice_no;
        $ref2 = $originalSale->sale_code;

        $refundSales = POSSale::where('tenant_id', $tenantId)
            ->where('sale_status', 'refund')
            ->where(function($q) use ($ref1, $ref2) {
                if ($ref1) {
                    $q->where('reference_number', $ref1)
                      ->orWhere('notes', 'LIKE', '%[Ref: ' . $ref1 . ']%');
                }
                if ($ref2) {
                    $q->orWhere('reference_number', $ref2)
                      ->orWhere('notes', 'LIKE', '%[Ref: ' . $ref2 . ']%');
                }
            })
            ->pluck('id');

        $totalOriginalQty = (float) $originalSale->items()->sum('qty');
        $totalReturnedQty = 0;

        if ($refundSales->isNotEmpty()) {
            $totalReturnedQty = (float) POSSaleItem::whereIn('sale_id', $refundSales)->sum(DB::raw('ABS(qty)'));
        }

        if ($totalOriginalQty > 0 && $totalReturnedQty >= ($totalOriginalQty - 0.001)) {
            $originalSale->update(['sale_status' => 'refunded']);
        } elseif ($totalReturnedQty > 0.001) {
            $originalSale->update(['sale_status' => 'partial_refund']);
        }
    }

    public function ajaxData()
    {
        $movements = InventoryMovement::where('tenant_id', auth()->user()->tenant_id)
            ->whereIn('movement_type', ['return', 'RETURN', 'sale_return', 'refund'])
            ->with(['product', 'creator'])
            ->latest();

        return DataTables::of($movements)
            ->addColumn('product_name', function($row) {
                return '<div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:34px;height:34px;">
                        <i class="bi bi-arrow-return-left text-danger"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">' . e($row->product?->name ?? 'N/A') . '</div>
                        <div class="text-muted extra-small font-mono">' . e($row->product?->barcode ?? 'SKU: N/A') . '</div>
                    </div>
                </div>';
            })
            ->editColumn('qty', function($row) {
                return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono fw-bold">+' . number_format($row->qty, 2) . ' units</span>';
            })
            ->addColumn('estimated_refund', function($row) {
                $val = (float)$row->qty * (float)($row->product?->selling_price ?? 0);
                return '<span class="font-mono fw-bold text-dark">₱' . number_format($val, 2) . '</span>';
            })
            ->editColumn('remarks', function($row) {
                return '<span class="text-muted extra-small">' . e($row->remarks ?: 'Customer Return') . '</span>';
            })
            ->editColumn('created_at', function($row) {
                return '<span class="font-mono extra-small text-dark">' . ($row->created_at ? $row->created_at->format('M d, Y h:i A') : '-') . '</span>';
            })
            ->addColumn('processed_by', function($row) {
                return '<span class="fw-semibold text-dark">' . e($row->creator?->name ?? 'System') . '</span>';
            })
            ->rawColumns(['product_name', 'qty', 'estimated_refund', 'remarks', 'created_at', 'processed_by'])
            ->make(true);
    }
}
