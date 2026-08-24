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
            ->with(['category', 'unit', 'variants'])
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
            $price = $variant ? $variant->price : $product->price;

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

        $storeName = auth()->user()->tenant?->business_name ?? 'MINIMART STORE';

        return view('pages.tenants.products.barcode-labels.print', compact('labels', 'paperSize', 'showPrice', 'showStoreName', 'storeName'));
    }
}
