<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSProducts;
use App\Models\POS\POSProductVariant;
use Illuminate\Http\Request;

class BarcodeLabelController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $products = POSProducts::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('selling_price', '>', 0)
                  ->orWhereHas('variants', function ($vq) {
                      $vq->where('selling_price', '>', 0);
                  });
            })
            ->with(['category', 'unit', 'variants' => function ($vq) {
                $vq->where('selling_price', '>', 0);
            }])
            ->orderBy('name')
            ->get();

        return view('pages.tenants.products.barcode-labels.index', compact('products'));
    }

    public function printPreview(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $items = $request->input('items', []); // array of ['id' => product_id, 'variant_id' => null, 'qty' => 10]
        $paperSize = $request->input('paper_size', 'a4_3col'); // 'a4_3col', 'shelf_tag', 'thermal_50x30'
        $showPrice = $request->boolean('show_price', true);
        $showStoreName = $request->boolean('show_store_name', true);

        $labels = [];

        foreach ($items as $item) {
            $productId = $item['id'] ?? null;
            $variantId = $item['variant_id'] ?? null;
            $qty = max(1, min((int)($item['qty'] ?? 1), 100)); // cap at 100 per product

            $product = POSProducts::where('tenant_id', $tenantId)->find($productId);
            if (!$product) continue;

            $variant = $variantId ? POSProductVariant::where('tenant_id', $tenantId)->find($variantId) : null;

            $barcode = $variant ? ($variant->barcode ?: $product->barcode) : $product->barcode;
            $sku = $variant ? ($variant->sku ?: $product->sku) : $product->sku;
            $name = $product->name . ($variant ? ' - ' . $variant->variant_name : '');
            
            // Allow quick changed price override from form input
            $customPrice = isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] : null;
            $price = $customPrice !== null ? $customPrice : ($variant ? $variant->selling_price : $product->selling_price);

            // If no barcode, use SKU or auto fallback
            $codeValue = $barcode ?: ($sku ?: sprintf('%08d', $product->id));

            for ($i = 0; $i < $qty; $i++) {
                $labels[] = [
                    'name'      => $name,
                    'barcode'   => $codeValue,
                    'price'     => (float)$price,
                    'sku'       => $sku,
                ];
            }
        }

        $tenant = auth()->user()->tenant ?? \App\Models\POS\POSTenant::find(auth()->user()->tenant_id) ?? \App\Models\POS\POSTenant::first();
        $storeName = $tenant?->business_name ?? config('app.name', 'POS Store');

        return view('pages.tenants.products.barcode-labels.print', compact('labels', 'paperSize', 'showPrice', 'showStoreName', 'storeName'));
    }

    public function quickUpdatePrice(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'product_id'     => ['required', 'integer'],
            'variant_id'     => ['nullable', 'integer'],
            'selling_price'  => ['required', 'numeric', 'min:0.01'],
            'cost_price'     => ['nullable', 'numeric', 'min:0'],
            'wholesale_price'=> ['nullable', 'numeric', 'min:0'],
            'reason'         => ['nullable', 'string', 'max:255'],
            'remarks'        => ['nullable', 'string', 'max:500'],
        ]);

        $product = POSProducts::where('tenant_id', $tenantId)->findOrFail($validated['product_id']);
        $variant = !empty($validated['variant_id'])
            ? POSProductVariant::where('tenant_id', $tenantId)->where('product_id', $product->id)->find($validated['variant_id'])
            : null;

        $target = $variant ?: $product;
        $oldSelling = (float)$target->selling_price;
        $oldCost = (float)$target->cost_price;
        $oldWholesale = (float)($target->wholesale_price ?? 0);

        $newSelling = (float)$validated['selling_price'];
        $newCost = isset($validated['cost_price']) && is_numeric($validated['cost_price']) ? (float)$validated['cost_price'] : $oldCost;
        $newWholesale = isset($validated['wholesale_price']) && is_numeric($validated['wholesale_price']) ? (float)$validated['wholesale_price'] : $oldWholesale;

        // Update Target (Product or Variant)
        $target->selling_price = $newSelling;
        if (isset($validated['cost_price']) && is_numeric($validated['cost_price'])) {
            $target->cost_price = $newCost;
        }
        if (isset($validated['wholesale_price']) && is_numeric($validated['wholesale_price'])) {
            $target->wholesale_price = $newWholesale;
        }
        $target->updated_by = auth()->id();
        $target->save();

        // Create Price History Record for audit tracking
        try {
            \App\Models\POS\ProductPriceHistory::create([
                'tenant_id'          => $tenantId,
                'product_id'         => $product->id,
                'variant_id'         => $variant?->id,
                'cost_price'         => $oldCost,
                'new_cost_price'     => $newCost,
                'selling_price'      => $oldSelling,
                'new_selling_price'  => $newSelling,
                'wholesale_price'    => $oldWholesale,
                'new_wholesale_price'=> $newWholesale,
                'reason'             => $validated['reason'] ?: 'Quick Price Adjustment from Barcode/Labels',
                'remarks'            => $validated['remarks'] ?: 'Adjusted by ' . (auth()->user()->name ?? 'Owner'),
                'effective_date'     => now(),
                'status'             => 'approved',
                'created_by'         => auth()->id(),
                'updated_by'         => auth()->id(),
            ]);
        } catch (\Exception $e) {
            \Log::warning('Price history log failed in quickUpdatePrice: ' . $e->getMessage());
        }

        return response()->json([
            'success'        => true,
            'message'        => 'Price updated and saved successfully in database!',
            'new_price'      => $newSelling,
            'formatted_price'=> '₱' . number_format($newSelling, 2),
            'product_id'     => $product->id,
            'variant_id'     => $variant?->id,
        ]);
    }
}
