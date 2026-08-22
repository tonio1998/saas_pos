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
            return back()->with('success', 'Physical stock adjustment recorded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
