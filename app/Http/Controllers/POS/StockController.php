<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use App\Models\POS\Stocks;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    use TCommonFunctions;
    public function receive(string $id)
    {
        $product = POSProducts::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.tenants.products.stock.receive',
            compact('product')
        );
    }

    public function storeReceive(
        Request $request,
        string $id
    ) {
        $request->validate([
            'quantity' => [
                'required',
                'numeric',
                'gt:0'
            ],
            'unit_cost' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'remarks' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ]);

        DB::beginTransaction();

        try {

            $product = POSProducts::lockForUpdate()
                ->findOrFail(
                    decrypt($id)
                );

            $stockBefore = $product->stock_on_hand;

            $quantity = (float) $request->quantity;

            $stockAfter = $stockBefore + $quantity;

            $newStock = new Stocks();
            $newStock->tenant_id = auth()->user()->tenant_id;
            $newStock->product_id = $product->id;
            $newStock->transaction_type = Stocks::TYPE_IN;
            $newStock->quantity = $quantity;
            $newStock->stock_before = $stockBefore ?? 0;
            $newStock->stock_after = $stockAfter ?? 0;
            $newStock->unit_cost = $request->unit_cost;
            $newStock->reference_type = 'STOCK_RECEIVING';
            $newStock->reference_id = null;
            $newStock->remarks = $request->remarks;
            $newStock->created_by = auth()->id();
            $this->setCommonFields($newStock);
            $newStock->save();

            $product->update([
                'stock_on_hand' => $stockAfter,
                'cost_price' => $request->filled('unit_cost')
                    ? $request->unit_cost
                    : $product->cost_price,
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'products.stock.history',
                    encrypt($product->id)
                )
                ->with(
                    'success',
                    'Stock received successfully.'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function history(string $id)
    {
        $product = POSProducts::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.tenants.products.stock.history',
            compact('product')
        );
    }

    public function adjustment(string $id)
    {
        $product = POSProducts::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.tenants.products.stock.adjustment',
            compact('product')
        );
    }

    public function storeAdjustment(
        Request $request,
        string $id
    ) {
        $request->validate([
            'actual_stock' => [
                'required',
                'numeric',
                'min:0'
            ],
            'reason' => [
                'required',
                'string',
                'max:255'
            ],
            'remarks' => [
                'nullable',
                'string',
                'max:1000'
            ]
        ]);

        DB::beginTransaction();

        try {

            $product = POSProducts::lockForUpdate()
                ->findOrFail(
                    decrypt($id)
                );

            $stockBefore = (float) $product->stock_on_hand;

            $stockAfter = (float) $request->actual_stock;

            $adjustmentQty = $stockAfter - $stockBefore;

            if ($adjustmentQty == 0) {

                return back()
                    ->with(
                        'warning',
                        'No stock adjustment detected.'
                    );

            }

            Stocks::create([
                'tenant_id' => auth()->user()->tenant_id,
                'product_id' => $product->id,
                'transaction_type' => Stocks::TYPE_ADJUSTMENT,
                'quantity' => abs($adjustmentQty),
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'unit_cost' => null,
                'reference_type' => 'STOCK_ADJUSTMENT',
                'reference_id' => null,
                'remarks' => $request->reason .
                    ($request->remarks
                        ? ' - ' . $request->remarks
                        : ''),
                'created_by' => auth()->id(),
            ]);

            $product->update([
                'stock_on_hand' => $stockAfter
            ]);

            DB::commit();

            return redirect()
                ->route(
                    'products.stock.history',
                    encrypt($product->id)
                )
                ->with(
                    'success',
                    'Stock adjusted successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

}
