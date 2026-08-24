<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use App\Models\POS\Stocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $adjustments = Stocks::where('tenant_id', $tenantId)
            ->where('transaction_type', Stocks::TYPE_ADJUSTMENT)
            ->with(['product', 'creator'])
            ->latest()
            ->get();

        return view('pages.tenants.inventory.adjustments.index', compact('adjustments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => 'required',
            'actual_stock' => 'required|numeric|min:0',
            'reason'       => 'required|string|max:255',
            'remarks'      => 'nullable|string|max:1000',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $productId = decrypt($request->product_id);
        $product = POSProducts::where('tenant_id', $tenantId)->findOrFail($productId);

        $stockBefore = (float) $product->stock_on_hand;
        $stockAfter = (float) $request->actual_stock;
        $quantity = abs($stockAfter - $stockBefore);

        if ($quantity == 0) {
            return back()->with('info', 'Physical count matches current stock. No adjustment needed.');
        }

        DB::beginTransaction();
        try {
            $stock = new Stocks();
            $stock->tenant_id = $tenantId;
            $stock->product_id = $product->id;
            $stock->transaction_type = Stocks::TYPE_ADJUSTMENT;
            $stock->quantity = $quantity;
            $stock->stock_before = $stockBefore;
            $stock->stock_after = $stockAfter;
            $stock->unit_cost = $product->cost_price;
            $stock->reference_type = 'PHYSICAL_COUNT_AUDIT';
            $stock->remarks = '[Reason: ' . $request->reason . '] ' . ($request->remarks ?? '');
            $stock->created_by = auth()->id();
            $stock->save();

            $product->stock_on_hand = $stockAfter;
            $product->save();

            DB::commit();
            return redirect()->route('stocks.adjustments.index')->with('success', 'Stock adjustment recorded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Rapid Restock: Find product/variant by barcode for instant stock-in
     */
    public function findByBarcode(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $barcode = trim($request->get('barcode', ''));

        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Please provide a barcode or SKU.'], 422);
        }

        // 1. Try finding in variants first
        $variant = \App\Models\POS\POSProductVariant::where('tenant_id', $tenantId)
            ->where(function($q) use ($barcode) {
                $q->where('barcode', $barcode)->orWhere('sku', $barcode);
            })
            ->with(['product.unit', 'product.category'])
            ->first();

        if ($variant && $variant->product) {
            return response()->json([
                'success'        => true,
                'is_variant'     => true,
                'product_id'     => $variant->product_id,
                'variant_id'     => $variant->id,
                'name'           => $variant->product->name . ' - ' . $variant->variant_name,
                'sku'            => $variant->sku,
                'barcode'        => $variant->barcode,
                'current_stock'  => (float)$variant->stock_on_hand,
                'cost_price'     => (float)($variant->cost_price ?: $variant->product->cost_price),
                'price'          => (float)$variant->price,
                'unit'           => $variant->product->unit?->short_name ?? 'pcs',
            ]);
        }

        // 2. Try finding parent product
        $product = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)
            ->where(function($q) use ($barcode) {
                $q->where('barcode', $barcode)->orWhere('sku', $barcode);
            })
            ->with(['unit', 'category'])
            ->first();

        if ($product) {
            return response()->json([
                'success'        => true,
                'is_variant'     => false,
                'product_id'     => $product->id,
                'variant_id'     => null,
                'name'           => $product->name,
                'sku'            => $product->sku,
                'barcode'        => $product->barcode,
                'current_stock'  => (float)$product->stock_on_hand,
                'cost_price'     => (float)$product->cost_price,
                'price'          => (float)$product->price,
                'unit'           => $product->unit?->short_name ?? 'pcs',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No product or variant found matching barcode: ' . $barcode], 404);
    }

    /**
     * Rapid Restock: Quick stock-in processing
     */
    public function quickStockIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'qty_to_add' => 'required|numeric|min:0.01',
            'cost_price' => 'nullable|numeric|min:0',
            'expiry_date'=> 'nullable|date',
            'remarks'    => 'nullable|string|max:255',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $qtyToAdd = (float)$request->qty_to_add;

        DB::beginTransaction();
        try {
            if ($request->filled('variant_id')) {
                $variant = \App\Models\POS\POSProductVariant::where('tenant_id', $tenantId)->findOrFail($request->variant_id);
                $product = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)->findOrFail($variant->product_id);

                $stockBefore = (float)$variant->stock_on_hand;
                $stockAfter = $stockBefore + $qtyToAdd;

                $variant->stock_on_hand = $stockAfter;
                if ($request->filled('cost_price') && (float)$request->cost_price > 0) {
                    $variant->cost_price = (float)$request->cost_price;
                }
                $variant->save();

                $product->increment('stock_on_hand', $qtyToAdd);

                $stockRecord = new Stocks();
                $stockRecord->tenant_id = $tenantId;
                $stockRecord->product_id = $product->id;
                $stockRecord->variant_id = $variant->id;
                $stockRecord->transaction_type = Stocks::TYPE_IN;
                $stockRecord->quantity = $qtyToAdd;
                $stockRecord->stock_before = $stockBefore;
                $stockRecord->stock_after = $stockAfter;
                $stockRecord->unit_cost = (float)($request->cost_price ?: $variant->cost_price);
                $stockRecord->reference_type = 'QUICK_RESTOCK';
                $stockRecord->remarks = $request->remarks ?: 'Rapid Restock';
                $stockRecord->created_by = auth()->id();
                $stockRecord->save();

                DB::commit();

                return response()->json([
                    'success'       => true,
                    'message'       => 'Added +' . $qtyToAdd . ' to stock of ' . $variant->variant_name,
                    'new_stock'     => $stockAfter,
                    'product_name'  => $product->name . ' - ' . $variant->variant_name,
                ]);
            } else {
                $product = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)->findOrFail($request->product_id);

                $stockBefore = (float)$product->stock_on_hand;
                $stockAfter = $stockBefore + $qtyToAdd;

                $product->stock_on_hand = $stockAfter;
                if ($request->filled('cost_price') && (float)$request->cost_price > 0) {
                    $product->cost_price = (float)$request->cost_price;
                }
                $product->save();

                $stockRecord = new Stocks();
                $stockRecord->tenant_id = $tenantId;
                $stockRecord->product_id = $product->id;
                $stockRecord->transaction_type = Stocks::TYPE_IN;
                $stockRecord->quantity = $qtyToAdd;
                $stockRecord->stock_before = $stockBefore;
                $stockRecord->stock_after = $stockAfter;
                $stockRecord->unit_cost = (float)($request->cost_price ?: $product->cost_price);
                $stockRecord->reference_type = 'QUICK_RESTOCK';
                $stockRecord->remarks = $request->remarks ?: 'Rapid Restock';
                $stockRecord->created_by = auth()->id();
                $stockRecord->save();

                DB::commit();

                return response()->json([
                    'success'       => true,
                    'message'       => 'Added +' . $qtyToAdd . ' to stock of ' . $product->name,
                    'new_stock'     => $stockAfter,
                    'product_name'  => $product->name,
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to add stock: ' . $e->getMessage()], 500);
        }
    }
}
