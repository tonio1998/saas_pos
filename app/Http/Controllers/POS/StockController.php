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
        $product = POSProducts::with('variants')->findOrFail(
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
            'variant_id' => [
                'nullable',
                'exists:pos_product_variants,id'
            ],
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
            'update_cost_price' => [
                'nullable',
                'boolean'
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

            $variant = null;
            if ($request->filled('variant_id')) {
                $variant = \App\Models\POS\POSProductVariant::lockForUpdate()
                    ->findOrFail($request->variant_id);
                $stockBefore = (float) $variant->stock_on_hand;
                $currentCost = (float) ($variant->cost_price ?? $product->cost_price);
            } else {
                $stockBefore = (float) $product->stock_on_hand;
                $currentCost = (float) $product->cost_price;
            }

            $quantity = (float) $request->quantity;
            $stockAfter = $stockBefore + $quantity;

            $newStock = new Stocks();
            $newStock->tenant_id = auth()->user()->tenant_id;
            $newStock->product_id = $product->id;
            $newStock->transaction_type = Stocks::TYPE_IN;
            $newStock->quantity = $quantity;
            $newStock->stock_before = $stockBefore;
            $newStock->stock_after = $stockAfter;
            $newStock->unit_cost = $request->unit_cost;
            $newStock->reference_type = 'STOCK_RECEIVING';
            $newStock->reference_id = null;
            $newStock->remarks = $variant 
                ? "[Variant: {$variant->variant_name}] " . ($request->remarks ?? '')
                : $request->remarks;
            $newStock->created_by = auth()->id();

            $this->setCommonFields(
                $newStock
            );

            $newStock->save();

            $newCostPrice = null;
            if (
                $request->boolean('update_cost_price') &&
                $request->filled('unit_cost')
            ) {
                $purchaseCost = (float) $request->unit_cost;

                if ($stockBefore > 0) {
                    $totalExistingValue = $stockBefore * $currentCost;
                    $totalNewValue = $quantity * $purchaseCost;
                    $newCostPrice = round(($totalExistingValue + $totalNewValue) / $stockAfter, 2);
                } else {
                    $newCostPrice = $purchaseCost;
                }
            }

            if ($variant) {
                $variantData = ['stock_on_hand' => $stockAfter];
                if ($newCostPrice !== null) {
                    $variantData['cost_price'] = $newCostPrice;
                }
                $variant->update($variantData);

                // Increment main product total stock if main product tracks aggregate stock
                if ((float)$product->stock_on_hand > 0 || $product->variants()->count() > 0) {
                    $factor = (float) ($variant->qty_per_pack ?? 1);
                    $product->increment('stock_on_hand', $quantity * $factor);
                }
            } else {
                $productData = ['stock_on_hand' => $stockAfter];
                if ($newCostPrice !== null) {
                    $productData['cost_price'] = $newCostPrice;
                }
                $product->update($productData);
            }

            DB::commit();

            return redirect()
                ->route(
                    'products.stock.history',
                    encrypt($product->id)
                )
                ->with(
                    'success',
                    'Stock received successfully' . ($variant ? " for variant {$variant->variant_name}." : ".")
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

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $products = POSProducts::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('archived', 0)->orWhereNull('archived');
            })
            ->with(['category', 'unit', 'variants'])
            ->orderBy('name')
            ->get();

        $totalValuation = 0;
        $totalRetailValuation = 0;

        foreach ($products as $p) {
            if ($p->variants && $p->variants->count() > 0) {
                $vValCost = $p->variants->sum(function($v) use ($p) {
                    $c = (float)($v->cost_price ?? $p->cost_price);
                    return (float)$v->stock_on_hand * $c;
                });
                $vValRetail = $p->variants->sum(function($v) use ($p) {
                    $r = (float)($v->selling_price ?? $p->selling_price);
                    return (float)$v->stock_on_hand * $r;
                });

                if ($vValCost == 0 && (float)$p->stock_on_hand > 0) {
                    $vValCost = (float)$p->stock_on_hand * (float)$p->cost_price;
                    $vValRetail = (float)$p->stock_on_hand * (float)$p->selling_price;
                }

                $totalValuation += $vValCost;
                $totalRetailValuation += $vValRetail;
            } else {
                $totalValuation += (float)$p->stock_on_hand * (float)$p->cost_price;
                $totalRetailValuation += (float)$p->stock_on_hand * (float)$p->selling_price;
            }
        }

        return view('pages.tenants.inventory.stocks.index', compact('products', 'totalValuation', 'totalRetailValuation'));
    }

}

