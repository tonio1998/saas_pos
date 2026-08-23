<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use App\Models\POS\POSProductVariant;
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

                    if (round($currentCost, 2) !== round($newCostPrice, 2)) {
                        \App\Models\POS\ProductPriceHistory::create([
                            'tenant_id'           => auth()->user()->tenant_id,
                            'product_id'          => $product->id,
                            'variant_id'          => $variant->id,
                            'cost_price'          => $currentCost,
                            'new_cost_price'      => $newCostPrice,
                            'selling_price'       => (float)($variant->selling_price ?? 0),
                            'new_selling_price'   => (float)($variant->selling_price ?? 0),
                            'wholesale_price'     => $variant->wholesale_price !== null ? (float)$variant->wholesale_price : null,
                            'new_wholesale_price' => $variant->wholesale_price !== null ? (float)$variant->wholesale_price : null,
                            'reason'              => 'Stock Receive Cost Adjustment',
                            'remarks'             => "Stock Receive for Variant: {$variant->variant_name}",
                            'effective_date'      => now(),
                            'status'              => 'active',
                            'created_by'          => auth()->id(),
                            'updated_by'          => auth()->id(),
                        ]);
                    }
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

                    if (round($currentCost, 2) !== round($newCostPrice, 2)) {
                        \App\Models\POS\ProductPriceHistory::create([
                            'tenant_id'           => auth()->user()->tenant_id,
                            'product_id'          => $product->id,
                            'variant_id'          => null,
                            'cost_price'          => $currentCost,
                            'new_cost_price'      => $newCostPrice,
                            'selling_price'       => (float)($product->selling_price ?? 0),
                            'new_selling_price'   => (float)($product->selling_price ?? 0),
                            'wholesale_price'     => $product->wholesale_price !== null ? (float)$product->wholesale_price : null,
                            'new_wholesale_price' => $product->wholesale_price !== null ? (float)$product->wholesale_price : null,
                            'reason'              => 'Stock Receive Cost Adjustment',
                            'remarks'             => "Stock Receive for Product: {$product->name}",
                            'effective_date'      => now(),
                            'status'              => 'active',
                            'created_by'          => auth()->id(),
                            'updated_by'          => auth()->id(),
                        ]);
                    }
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
        $tenantId = auth()->user()->tenant_id;
        $realId = decrypt($id);

        $product = POSProducts::with([
            'category',
            'unit',
            'variants' => fn($q) => $q->where('status', 'active'),
        ])
        ->where('tenant_id', $tenantId)
        ->findOrFail($realId);

        $recentAdjustments = Stocks::with(['variant', 'createdBy'])
            ->where('tenant_id', $tenantId)
            ->where('product_id', $product->id)
            ->where('transaction_type', Stocks::TYPE_ADJUSTMENT)
            ->latest('created_at')
            ->limit(6)
            ->get();

        return view(
            'pages.tenants.products.stock.adjustment',
            compact('product', 'recentAdjustments')
        );
    }

    public function storeAdjustment(
        Request $request,
        string $id
    ) {
        $request->validate([
            'variant_id'   => ['nullable', 'integer'],
            'actual_stock' => ['required', 'numeric', 'min:0'],
            'reason'       => ['required', 'string', 'max:255'],
            'remarks'      => ['nullable', 'string', 'max:1000']
        ]);

        $tenantId = auth()->user()->tenant_id;
        $realId = decrypt($id);

        DB::beginTransaction();

        try {
            $product = POSProducts::lockForUpdate()
                ->where('tenant_id', $tenantId)
                ->findOrFail($realId);

            $targetVariant = null;
            if ($request->filled('variant_id')) {
                $targetVariant = POSProductVariant::lockForUpdate()
                    ->where('product_id', $product->id)
                    ->where('tenant_id', $tenantId)
                    ->find($request->variant_id);
            }

            if ($targetVariant) {
                $stockBefore = (float) $targetVariant->stock_on_hand;
                $unitCost    = (float) ($targetVariant->cost_price ?? $product->cost_price ?? 0);
            } else {
                $stockBefore = (float) $product->stock_on_hand;
                $unitCost    = (float) ($product->cost_price ?? 0);
            }

            $stockAfter = (float) $request->actual_stock;
            $adjustmentQty = $stockAfter - $stockBefore;

            if (round($adjustmentQty, 4) == 0) {
                return back()->with('warning', 'Physical count matches current stock. No adjustment needed.');
            }

            Stocks::create([
                'tenant_id'        => $tenantId,
                'product_id'       => $product->id,
                'variant_id'       => $targetVariant?->id,
                'transaction_type' => Stocks::TYPE_ADJUSTMENT,
                'quantity'         => abs($adjustmentQty),
                'stock_before'     => $stockBefore,
                'stock_after'      => $stockAfter,
                'unit_cost'        => $unitCost,
                'reference_type'   => 'STOCK_ADJUSTMENT',
                'reference_id'     => null,
                'remarks'          => $request->reason . ($request->remarks ? ' - ' . $request->remarks : ''),
                'created_by'       => auth()->id(),
            ]);

            if ($targetVariant) {
                $targetVariant->update(['stock_on_hand' => $stockAfter]);
            } else {
                $product->update(['stock_on_hand' => $stockAfter]);
            }

            DB::commit();

            return redirect()
                ->route('products.stock.history', encrypt($product->id))
                ->with('success', 'Physical stock adjustment successfully recorded.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('warning', 'Failed to save stock adjustment: ' . $e->getMessage());
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

