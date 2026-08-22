<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSSale;
use App\Models\POS\POSSaleItem;
use App\Models\POS\POSProducts;
use App\Models\POS\InventoryMovement;
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

            // ── Find original sale (if invoice provided) ──────────────────
            $originalSale = null;
            if (!empty($validated['invoice_no'])) {
                $originalSale = POSSale::where('tenant_id', $tenantId)
                    ->where(function($q) use ($validated) {
                        $q->where('invoice_no', $validated['invoice_no'])
                          ->orWhere('sale_code', $validated['invoice_no']);
                    })->first();
            }

            // ── 1. Create negative refund sale (deducts from revenue) ─────
            $refundSale = new POSSale();
            $refundSale->tenant_id      = $tenantId;
            $refundSale->customer_id    = $originalSale?->customer_id;
            $refundSale->cashier_id     = auth()->id();
            $refundSale->invoice_no     = 'RTN-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $refundSale->subtotal       = $refundTotal;
            $refundSale->total_amount   = $refundTotal;  // NEGATIVE → auto-deducts from SUM
            $refundSale->tendered_amount = 0;
            $refundSale->change_amount  = 0;
            $refundSale->sale_status    = 'refund';      // new status for refund records
            $refundSale->sale_date      = now();
            $refundSale->notes          = 'REFUND: ' . ($validated['reason'] ?? 'Customer Return')
                . ($originalSale ? ' [Ref: ' . ($originalSale->invoice_no ?? $originalSale->sale_code) . ']' : '');
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
            $inv->movement_type  = 'return';
            $inv->reference_type = 'sale_return';
            $inv->reference_id   = $refundSale->id;
            $inv->qty            = $qty;
            $inv->remarks        = $variant
                ? '[Variant: ' . $variant->variant_name . '] ' . $baseRemarks
                : $baseRemarks;
            $this->setCommonFields($inv);
            $inv->save();
        });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Return processed. Revenue and inventory adjusted.']);
        }
        return redirect()->route('returns.index')->with('success', 'Return processed. Revenue and inventory adjusted.');
    }

    public function searchInvoice(Request $request)
    {
        $invoiceNo = trim($request->get('invoice_no'));
        if (!$invoiceNo) {
            return response()->json(['success' => false, 'message' => 'Please provide an invoice number.']);
        }

        $sale = POSSale::where('tenant_id', auth()->user()->tenant_id)
            ->where(function($q) use ($invoiceNo) {
                $q->where('invoice_no', $invoiceNo)
                  ->orWhere('sale_code', $invoiceNo)
                  ->orWhere('invoice_no', 'LIKE', '%' . $invoiceNo . '%')
                  ->orWhere('sale_code', 'LIKE', '%' . $invoiceNo . '%');
            })
            ->with(['customer', 'items.product', 'items.variant', 'cashier'])
            ->first();

        if (!$sale) {
            return response()->json(['success' => false, 'message' => 'Invoice or Sale reference "' . $invoiceNo . '" not found.']);
        }

        // product_name already stores "Princess Bea (1kl Pack)" — use it directly
        $sale->items->each(function ($item) {
            $item->item_name    = $item->product_name ?: ($item->product?->name ?? 'Product');
            // Extract variant part from product_name e.g. "Princess Bea (1kl Pack)" → "1kl Pack"
            if (preg_match('/\((.+)\)$/', $item->item_name, $m)) {
                $item->variant_name = trim($m[1]);
            } else {
                $item->variant_name = $item->variant?->variant_name ?? null;
            }
        });

        return response()->json(['success' => true, 'sale' => $sale]);
    }

    public function storeBatch(Request $request)
    {
        $request->validate([
            'invoice_no'       => 'required|string',
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required',
            'items.*.qty'      => 'required|numeric|min:0.01',
            'reason'           => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request) {
            $tenantId  = auth()->user()->tenant_id;
            $invoiceNo = $request->input('invoice_no');
            $reason    = $request->input('reason') ?: 'Partial/Full Return';

            $originalSale = POSSale::where('tenant_id', $tenantId)
                ->where(function($q) use ($invoiceNo) {
                    $q->where('invoice_no', $invoiceNo)
                      ->orWhere('sale_code', $invoiceNo);
                })->first();

            // ── 1. Create one refund sale for the whole batch ─────────────
            $refundSale = new POSSale();
            $refundSale->tenant_id       = $tenantId;
            $refundSale->customer_id     = $originalSale?->customer_id;
            $refundSale->cashier_id      = auth()->id();
            $refundSale->invoice_no      = 'RTN-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $refundSale->sale_status     = 'refund';
            $refundSale->sale_date       = now();
            $refundSale->subtotal        = 0;  // computed below
            $refundSale->total_amount    = 0;  // computed below
            $refundSale->tendered_amount = 0;
            $refundSale->change_amount   = 0;
            $refundSale->notes           = 'REFUND: ' . $reason
                . ' [Ref: ' . ($originalSale?->invoice_no ?? $originalSale?->sale_code ?? $invoiceNo) . ']';
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

                // ── 3. Restock ────────────────────────────────────────────
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
                $inv->movement_type  = 'return';
                $inv->reference_type = 'sale_return';
                $inv->reference_id   = $refundSale->id;
                $inv->qty            = $qty;
                $inv->remarks        = ($variant ? '[Variant: ' . $variant->variant_name . '] ' : '')
                    . $reason . ' [Ref Invoice: ' . $invoiceNo . ']';
                $this->setCommonFields($inv);
                $inv->save();
            }

            // ── 5. Update refund sale totals ──────────────────────────────
            $refundSale->subtotal     = $batchTotal;
            $refundSale->total_amount = $batchTotal;  // negative → auto-deducts from SUM
            $refundSale->save();
        });

        return response()->json(['success' => true, 'message' => 'Return processed. Revenue and inventory auto-adjusted.']);
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
