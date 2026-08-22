<?php

namespace App\Http\Controllers\POS;

use App\Actions\POS\Products\StoreProduct;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCategories;
use App\Models\POS\POSProductVariant;
use App\Models\POS\POSProducts;
use App\Models\POS\POSUnits;
use App\Models\POS\ProductPriceHistory;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Milon\Barcode\DNS1D;

class ProductController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $categories = POSCategories::query()
            ->where(function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)
                  ->orWhere('tenant_id', 0)
                  ->orWhereNull('tenant_id');
            })
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.tenants.products.index', compact('categories'));
    }

    public function kpiStats()
    {
        $tenantId = auth()->user()->tenant_id;

        $totalProducts = POSProducts::where('tenant_id', $tenantId)->count();
        $activeProducts = POSProducts::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $lowStockCount = POSProducts::where('tenant_id', $tenantId)
            ->where('stock_on_hand', '>', 0)
            ->whereColumn('stock_on_hand', '<=', 'reorder_level')
            ->count();
        $outOfStockCount = POSProducts::where('tenant_id', $tenantId)
            ->where('stock_on_hand', '<=', 0)
            ->count();

        // Fast-Moving Top Mover calculation
        $topMoverItem = \DB::table('pos_sale_items')
            ->join('pos_sales', 'pos_sales.id', '=', 'pos_sale_items.sale_id')
            ->where('pos_sales.tenant_id', $tenantId)
            ->selectRaw('pos_sale_items.product_id, SUM(pos_sale_items.qty) as total_qty')
            ->groupBy('pos_sale_items.product_id')
            ->orderByDesc('total_qty')
            ->first();

        $topMoverName = 'None';
        $topMoverQty = 0;
        if ($topMoverItem) {
            $p = POSProducts::find($topMoverItem->product_id);
            if ($p) {
                $topMoverName = $p->name;
                $topMoverQty = (float)$topMoverItem->total_qty;
            }
        }

        $fastMovingCount = \DB::table('pos_sale_items')
            ->join('pos_sales', 'pos_sales.id', '=', 'pos_sale_items.sale_id')
            ->where('pos_sales.tenant_id', $tenantId)
            ->distinct('pos_sale_items.product_id')
            ->count('pos_sale_items.product_id');

        $totalInventoryValue = POSProducts::where('tenant_id', $tenantId)
            ->selectRaw('SUM(COALESCE(stock_on_hand, 0) * COALESCE(cost_price, 0)) as total_val')
            ->value('total_val') ?? 0;
        $totalRetailValue = POSProducts::where('tenant_id', $tenantId)
            ->selectRaw('SUM(COALESCE(stock_on_hand, 0) * COALESCE(selling_price, 0)) as total_val')
            ->value('total_val') ?? 0;

        return response()->json([
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'fast_moving_count' => $fastMovingCount,
            'top_mover_name' => $topMoverName,
            'top_mover_qty' => $topMoverQty,
            'total_inventory_value' => (float)$totalInventoryValue,
            'total_retail_value' => (float)$totalRetailValue,
            'formatted_inventory_value' => '₱' . number_format($totalInventoryValue, 2),
            'formatted_retail_value' => '₱' . number_format($totalRetailValue, 2),
        ]);
    }

    public function create()
    {
        $units = POSUnits::query()
            ->select('name', 'id')
            ->distinct()
            ->orderBy('name')
            ->get();

        $categories = POSCategories::query()
            ->select('name', 'id')
            ->distinct()
            ->orderBy('name')
            ->get();

        $variants = collect();

        return view('pages.tenants.products.create', compact('units', 'categories', 'variants'));
    }

    public function edit(Request $request)
    {
        $id      = decrypt($request->segment(3));
        $product = POSProducts::with('variants')->find($id);
        $units   = POSUnits::query()->with('createdBy')->orderBy('name')->get();
        $categories = POSCategories::query()->with('createdBy')->orderBy('name')->get();
        $variants = $product->variants;

        // Determine which variants have POS transaction records (sale items or inventory movements)
        // These must NOT be deletable — only status toggle allowed
        $variantsWithRecords = collect();
        if ($variants->isNotEmpty()) {
            $variantIds = $variants->pluck('id');

            $saleItemVariantIds = \App\Models\POS\POSSaleItem::whereIn('variant_id', $variantIds)
                ->pluck('variant_id')
                ->unique();

            $movementVariantIds = \App\Models\POS\InventoryMovement::whereIn('variant_id', $variantIds)
                ->pluck('variant_id')
                ->unique();

            $variantsWithRecords = $saleItemVariantIds->merge($movementVariantIds)->unique();
        }

        return view('pages.tenants.products.create', [
            'product'              => $product,
            'units'                => $units,
            'categories'           => $categories,
            'variants'             => $variants,
            'variantsWithRecords'  => $variantsWithRecords,  // Collection of variant IDs that have POS records
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                              => ['required', 'string', 'max:255'],
            'description'                       => ['nullable', 'string'],
            'category_id'                       => ['nullable', 'integer'],
            'unit_id'                           => ['nullable', 'integer'],
            'barcode'                           => ['nullable', 'string', 'max:100'],
            'sku'                               => ['nullable', 'string', 'max:100'],
            'cost_price'                        => ['required', 'numeric', 'min:0'],
            'selling_price'                     => ['required', 'numeric', 'min:0'],
            'wholesale_price'                   => ['nullable', 'numeric', 'min:0'],
            'reorder_level'                     => ['nullable', 'integer', 'min:0'],
            'image'                             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants'                          => ['nullable', 'array'],
            'variants.*.variant_name'           => ['required_with:variants', 'string', 'max:255'],
            'variants.*.stock_on_hand'          => ['nullable', 'numeric', 'min:0'],
            'variants.*.qty_per_pack'           => ['required_with:variants', 'numeric', 'min:0.0001'],
            'variants.*.cost_price'             => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.selling_price'          => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.wholesale_price'        => ['nullable', 'numeric', 'min:0'],
            'variants.*.barcode'                => ['nullable', 'string', 'max:100'],
            'variants.*.sku'                    => ['nullable', 'string', 'max:100'],
            'variants.*.unit_id'                => ['nullable', 'integer'],
        ]);

        $product = app(StoreProduct::class)->handle(
            $validated,
            auth()->user(),
            $request->file('image')
        );

        // Save variants
        if (!empty($validated['variants'])) {
            $this->syncVariants($product, $validated['variants'], auth()->user()->tenant_id);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'       => 'success',
                'message'      => 'Product SKU created successfully.',
                'redirect_url' => route('products.index'),
                'product_id'   => $product->id,
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function update(
        Request $request
    ) {

        $validated = $request->validate([
            'name'                              => ['required', 'string', 'max:255'],
            'description'                       => ['nullable', 'string'],
            'category_id'                       => ['nullable'],
            'unit_id'                           => ['nullable'],
            'barcode'                           => ['nullable', 'string'],
            'sku'                               => ['nullable', 'string'],
            'cost_price'                        => ['required', 'numeric', 'min:0'],
            'selling_price'                     => ['required', 'numeric', 'min:0'],
            'wholesale_price'                   => ['nullable', 'numeric', 'min:0'],
            'reorder_level'                     => ['nullable', 'integer', 'min:0'],
            'image'                             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'variants'                          => ['nullable', 'array'],
            'variants.*.id'                     => ['nullable'],
            'variants.*.variant_name'           => ['required_with:variants', 'string', 'max:255'],
            'variants.*.stock_on_hand'          => ['nullable', 'numeric', 'min:0'],
            'variants.*.qty_per_pack'           => ['required_with:variants', 'numeric', 'min:0.0001'],
            'variants.*.cost_price'             => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.selling_price'          => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.wholesale_price'        => ['nullable', 'numeric', 'min:0'],
            'variants.*.barcode'                => ['nullable', 'string', 'max:100'],
            'variants.*.sku'                    => ['nullable', 'string', 'max:100'],
            'variants.*.unit_id'                => ['nullable'],
            'variants.*.status'                 => ['nullable', 'string'],
        ]);

        $product = POSProducts::findOrFail(
            decrypt($request->segment(3))
        );

        DB::transaction(function () use (
            $product,
            $validated,
            $request
        ) {

            $oldCostPrice =
                (float) $product->cost_price;

            $oldSellingPrice =
                (float) $product->selling_price;

            $oldWholesalePrice =
                (float) ($product->wholesale_price ?? 0);

            $newCostPrice =
                (float) $validated['cost_price'];

            $newSellingPrice =
                (float) $validated['selling_price'];

            $newWholesalePrice =
                (float) ($validated['wholesale_price'] ?? 0);

            $priceChanged =
                $oldCostPrice !== $newCostPrice
                || $oldSellingPrice !== $newSellingPrice
                || $oldWholesalePrice !== $newWholesalePrice;

            if ($priceChanged) {

                $priceHistory = new ProductPriceHistory();

                $priceHistory->tenant_id =
                    auth()->user()->tenant_id;

                $priceHistory->product_id =
                    $product->id;

                $priceHistory->variant_id =
                    null;

                $priceHistory->cost_price =
                    $oldCostPrice;

                $priceHistory->new_cost_price =
                    $newCostPrice;

                $priceHistory->selling_price =
                    $oldSellingPrice;

                $priceHistory->new_selling_price =
                    $newSellingPrice;

                $priceHistory->wholesale_price =
                    $oldWholesalePrice;

                $priceHistory->new_wholesale_price =
                    $newWholesalePrice;

                $priceHistory->remarks =
                    'Product base price updated';

                $priceHistory->effective_date =
                    now();

                $this->setCommonFields(
                    $priceHistory,
                    $validated
                );

                $priceHistory->save();
            }

            $image = $validated['image'] ?? false;

            if ($image) {
                $product->image = $image->store(
                    'products',
                    'public'
                );
            }

            $product->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'unit_id' => $validated['unit_id'] ?? null,
                'barcode' => $validated['barcode'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'cost_price' => $newCostPrice,
                'selling_price' => $newSellingPrice,
                'wholesale_price' => $newWholesalePrice,
                'reorder_level' => $validated['reorder_level'] ?? 0,
                'allow_decimal_qty' => !empty($request->input('allow_decimal_qty')),
            ]);

            // Sync variants and record price histories
            if ($request->has('variants')) {
                $this->syncVariants($product, $request->input('variants', []), auth()->user()->tenant_id);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status'       => 'success',
                'message'      => 'Product SKU updated successfully.',
                'redirect_url' => route('products.index'),
                'product_id'   => $product->id,
            ]);
        }

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    /**
     * Sync product variants (preserve records, upsert existing/new).
     */
    private function syncVariants(POSProducts $product, array $variantsData, int $tenantId): void
    {
        $keptIds = [];

        foreach ($variantsData as $vData) {
            if (empty($vData['variant_name'])) {
                continue;
            }

            $variant = isset($vData['id']) && $vData['id']
                ? POSProductVariant::find($vData['id'])
                : new POSProductVariant();

            if (!$variant) {
                $variant = new POSProductVariant();
            }

            $oldCostPrice      = $variant->exists ? (float)$variant->cost_price : null;
            $oldSellingPrice   = $variant->exists ? (float)$variant->selling_price : null;
            $oldWholesalePrice = $variant->exists ? (float)$variant->wholesale_price : null;

            $newCostPrice      = (float)($vData['cost_price'] ?? 0);
            $newSellingPrice   = (float)($vData['selling_price'] ?? 0);
            $newWholesalePrice = isset($vData['wholesale_price']) && $vData['wholesale_price'] !== '' ? (float)$vData['wholesale_price'] : null;

            $variant->tenant_id       = $tenantId;
            $variant->product_id      = $product->id;
            $variant->variant_name    = $vData['variant_name'];
            $variant->stock_on_hand   = isset($vData['stock_on_hand']) ? (float) $vData['stock_on_hand'] : 0;
            $variant->qty_per_pack    = $vData['qty_per_pack'] ?? 1;
            $variant->cost_price      = $newCostPrice;
            $variant->selling_price   = $newSellingPrice;
            $variant->wholesale_price = $newWholesalePrice;
            $variant->barcode         = $vData['barcode'] ?? null;
            $variant->sku             = $vData['sku'] ?? null;
            $variant->unit_id         = $vData['unit_id'] ?? null;
            $variant->reorder_level   = $vData['reorder_level'] ?? 0;
            $variant->status          = $vData['status'] ?? 'active';  // respect form value
            $variant->created_by      = auth()->id();
            $variant->updated_by      = auth()->id();
            $variant->save();

            // Track variant price history if prices changed or if newly created
            if ($oldSellingPrice === null || $oldCostPrice != $newCostPrice || $oldSellingPrice != $newSellingPrice || $oldWholesalePrice != $newWholesalePrice) {
                ProductPriceHistory::create([
                    'tenant_id'           => $tenantId,
                    'product_id'          => $product->id,
                    'variant_id'          => $variant->id,
                    'cost_price'          => $oldCostPrice ?? $newCostPrice,
                    'new_cost_price'      => $newCostPrice,
                    'selling_price'       => $oldSellingPrice ?? $newSellingPrice,
                    'new_selling_price'   => $newSellingPrice,
                    'wholesale_price'     => $oldWholesalePrice ?? $newWholesalePrice,
                    'new_wholesale_price' => $newWholesalePrice,
                    'reason'              => $oldSellingPrice === null ? 'Initial Variant Price Setup' : 'Variant Price Update',
                    'remarks'             => "Variant: {$variant->variant_name}",
                    'effective_date'      => now(),
                    'status'              => 'active',
                    'created_by'          => auth()->id(),
                    'updated_by'          => auth()->id(),
                ]);
            }

            $keptIds[] = $variant->id;
        }

        // Only delete variants that were removed from the form AND have NO POS records
        // (variants with records are preserved to maintain data integrity)
        $removedIds = $product->variants()->whereNotIn('id', $keptIds)->pluck('id');

        if ($removedIds->isNotEmpty()) {
            $safeToDelete = $removedIds->filter(function ($variantId) {
                $hasSaleItems  = \App\Models\POS\POSSaleItem::where('variant_id', $variantId)->exists();
                $hasMovements  = \App\Models\POS\InventoryMovement::where('variant_id', $variantId)->exists();
                return !$hasSaleItems && !$hasMovements;
            });

            if ($safeToDelete->isNotEmpty()) {
                $product->variants()->whereIn('id', $safeToDelete)->delete();
            }
        }
    }

    public function suggestions(Request $request)
    {
        $keyword = trim($request->keyword);

        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $tenantId = auth()->user()->tenant_id;

        $products = POSProducts::query()
            ->select([
                'barcode', 'name', 'description', 'category_id',
                'unit_id', 'image', 'cost_price', 'selling_price', 'wholesale_price', 'sku',
                DB::raw('COUNT(*) as usage_count')
            ])
            ->where('tenant_id', '!=', $tenantId)
            ->with([
                'category' => fn($q) => $q->select('name', 'id')->distinct(),
                'unit'     => fn($q) => $q->select('name', 'id')->distinct(),
            ])
            ->where(function ($query) use ($keyword) {
                foreach (preg_split('/\s+/', $keyword) as $word) {
                    $query->where(fn($q) => $q
                        ->where('name', 'like', "%{$word}%")
                        ->orWhere('barcode', 'like', "%{$word}%")
                    );
                }
            })
            ->groupBy(['barcode','name','description','category_id','unit_id','image','cost_price','selling_price','wholesale_price','sku'])
            ->orderByDesc('usage_count')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return response()->json($products->map(fn($p) => [
            'barcode'         => $p->barcode,
            'name'            => $p->name,
            'description'     => $p->description,
            'category_id'     => $p->category_id,
            'unit_id'         => $p->unit_id,
            'image'           => $p->image ? '/storage/'.$p->image : null,
            'usage_count'     => $p->usage_count,
            'cost_price'      => $p->cost_price,
            'selling_price'   => $p->selling_price,
            'wholesale_price' => $p->wholesale_price,
            'sku'             => $p->sku,
            'category'        => $p->category?->name,
            'unit'            => $p->unit?->name,
        ]));
    }

    /**
     * Cross-tenant import search — returns products from other stores.
     */
    public function importSearch(Request $request)
    {
        $keyword  = trim($request->input('q', ''));
        $tenantId = auth()->user()->tenant_id;

        if (strlen($keyword) < 2) {
            return response()->json([]);
        }

        $products = POSProducts::query()
            ->select([
                'id', 'barcode', 'name', 'description', 'category_id',
                'unit_id', 'image', 'cost_price', 'selling_price', 'sku',
                DB::raw('COUNT(*) as store_count')
            ])
            ->where('tenant_id', '!=', $tenantId)
            ->with([
                'category' => fn($q) => $q->select('name', 'id'),
                'unit'     => fn($q) => $q->select('name', 'id'),
            ])
            ->where(fn($query) => $query
                ->where('name', 'like', "%{$keyword}%")
                ->orWhere('barcode', 'like', "%{$keyword}%")
            )
            ->groupBy(['id','barcode','name','description','category_id','unit_id','image','cost_price','selling_price','sku'])
            ->orderByDesc('store_count')
            ->orderBy('name')
            ->limit(12)
            ->get();

        return response()->json($products->map(fn($p) => [
            'barcode'       => $p->barcode,
            'name'          => $p->name,
            'description'   => $p->description,
            'category_id'   => $p->category_id,
            'unit_id'       => $p->unit_id,
            'image'         => $p->image ? '/storage/'.$p->image : null,
            'store_count'   => $p->store_count,
            'cost_price'    => $p->cost_price,
            'selling_price' => $p->selling_price,
            'wholesale_price'=> null,
            'sku'           => $p->sku,
            'category'      => $p->category?->name,
            'unit'          => $p->unit?->name,
        ]));
    }    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = POSProducts::with(['category', 'unit', 'createdBy', 'variants'])
            ->where('tenant_id', $tenantId);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock_on_hand', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock_on_hand', '>', 0)
                      ->whereColumn('stock_on_hand', '<=', 'reorder_level');
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock_on_hand', '>', 0);
            } elseif ($request->stock_status === 'fast_moving') {
                $query->whereIn('id', function($sub) use ($tenantId) {
                    $sub->select('product_id')
                        ->from('pos_sale_items')
                        ->join('pos_sales', 'pos_sales.id', '=', 'pos_sale_items.sale_id')
                        ->where('pos_sales.tenant_id', $tenantId)
                        ->groupBy('product_id');
                });
            }
        }

        if ($request->filled('product_type')) {
            if ($request->product_type === 'fractional') {
                $query->where('allow_decimal_qty', true);
            } elseif ($request->product_type === 'variants') {
                $query->has('variants');
            } elseif ($request->product_type === 'standard') {
                $query->where('allow_decimal_qty', false)->doesntHave('variants');
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('price_min')) {
            $query->where('selling_price', '>=', (float)$request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('selling_price', '<=', (float)$request->price_max);
        }

        if ($request->filled('margin_tier')) {
            if ($request->margin_tier === 'high') {
                $query->whereRaw('(COALESCE(selling_price, 0) - COALESCE(cost_price, 0)) / NULLIF(COALESCE(selling_price, 0), 0) >= 0.30');
            } elseif ($request->margin_tier === 'medium') {
                $query->whereRaw('(COALESCE(selling_price, 0) - COALESCE(cost_price, 0)) / NULLIF(COALESCE(selling_price, 0), 0) BETWEEN 0.15 AND 0.2999');
            } elseif ($request->margin_tier === 'low') {
                $query->whereRaw('(COALESCE(selling_price, 0) - COALESCE(cost_price, 0)) / NULLIF(COALESCE(selling_price, 0), 0) BETWEEN 0 AND 0.1499');
            } elseif ($request->margin_tier === 'negative') {
                $query->whereRaw('COALESCE(selling_price, 0) < COALESCE(cost_price, 0)');
            }
        }

        return datatables()
            ->eloquent($query)
            ->addColumn('checkbox', function ($product) {
                return '
                    <div class="text-center">
                        <input type="checkbox" class="form-check-input product-checkbox shadow-xs" value="' . $product->id . '" data-name="' . e($product->name) . '" data-price="' . ($product->selling_price ?? 0) . '">
                    </div>
                ';
            })
            ->addColumn('actions', function ($product) {
                $id = encrypt($product->id);
                return '
                    <div class="dropdown text-center">
                        <button class="btn btn-sm btn-light border rounded-pill px-3 py-1 text-dark fw-bold extra-small shadow-xs dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear-fill text-primary me-1"></i> Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border rounded-4 p-2 extra-small" style="min-width:200px;">
                            <li>
                                <button type="button" class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold text-dark btn-quick-view" data-id="' . $product->id . '">
                                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width:26px;height:26px;">
                                        <i class="bi bi-eye-fill" style="font-size:0.75rem;"></i>
                                    </div>
                                    <span>360° Quick CRM View</span>
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold text-dark" href="' . route('products.edit', $id) . '">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:26px;height:26px;">
                                        <i class="bi bi-pencil-fill" style="font-size:0.75rem;"></i>
                                    </div>
                                    <span>Edit Product Details</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold text-dark" href="' . route('products.stock.receive', $id) . '">
                                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:26px;height:26px;">
                                        <i class="bi bi-box-arrow-in-down" style="font-size:0.75rem;"></i>
                                    </div>
                                    <span>Receive Stock In</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold text-dark" href="' . route('products.stock.adjustment', $id) . '">
                                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:26px;height:26px;">
                                        <i class="bi bi-sliders" style="font-size:0.75rem;"></i>
                                    </div>
                                    <span>Stock Adjustment</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold text-dark" href="' . route('products.stock.history', $id) . '">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center" style="width:26px;height:26px;">
                                        <i class="bi bi-clock-history" style="font-size:0.75rem;"></i>
                                    </div>
                                    <span>Stock Movement Log</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->addColumn('product_info', function ($product) {
                $imageSrc = $product->image ? Storage::url($product->image) : null;
                $firstChar = strtoupper(mb_substr($product->name ?: 'P', 0, 1));

                $imgHtml = $imageSrc
                    ? '<img src="'.$imageSrc.'" class="rounded-3 border object-fit-cover shadow-xs flex-shrink-0 me-3" style="width:44px;height:44px;min-width:44px;" alt="" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'flex\';"><div class="rounded-3 bg-primary bg-opacity-10 border border-primary-subtle align-items-center justify-content-center fw-black text-primary shadow-xs flex-shrink-0 me-3" style="display:none;width:44px;height:44px;min-width:44px;font-size:1.1rem;">'.$firstChar.'</div>'
                    : '<div class="rounded-3 bg-primary bg-opacity-10 border border-primary-subtle d-flex align-items-center justify-content-center fw-black text-primary shadow-xs flex-shrink-0 me-3" style="width:44px;height:44px;min-width:44px;font-size:1.1rem;">'.$firstChar.'</div>';

                $variantCount = $product->variants->count();
                $variantBadge = $variantCount > 0
                    ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold"><i class="bi bi-boxes me-1"></i>'.$variantCount.' Variants</span>'
                    : '';

                $fractionalBadge = $product->allow_decimal_qty
                    ? '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle extra-small fw-bold">⚖️ Tinitimbang</span>'
                    : '';

                $codePill = ($product->barcode || $product->sku)
                    ? '<span class="font-mono text-muted extra-small"><i class="bi bi-upc me-1 text-secondary"></i>'.e($product->barcode ?: $product->sku).'</span>'
                    : '<span class="text-muted extra-small fst-italic">No Code</span>';

                return '
                    <div class="d-flex align-items-center py-1" style="min-width:280px;">
                        <div class="cursor-pointer btn-quick-view flex-shrink-0" data-id="'.$product->id.'" title="360° Quick View">
                            '.$imgHtml.'
                        </div>
                        <div class="min-w-0 flex-grow-1">
                            <a href="javascript:void(0)" class="fw-bold text-dark text-decoration-none d-block text-truncate hover-primary btn-quick-view fs-6 mb-1" data-id="'.$product->id.'" title="'.e($product->name).'">
                                '.e($product->name ?: 'Unnamed Product').'
                            </a>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                '.$codePill.'
                                '.$variantBadge.'
                                '.$fractionalBadge.'
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('category_unit', function ($product) {
                $cat = $product->category?->name
                    ? '<span class="badge bg-light text-dark border extra-small fw-semibold text-truncate d-inline-block" style="max-width:150px;"><i class="bi bi-folder2 text-primary me-1"></i>'.e($product->category->name).'</span>'
                    : '<span class="badge bg-light text-muted border extra-small">Uncategorized</span>';

                $unit = '<div class="text-muted extra-small mt-1 font-mono"><i class="bi bi-box me-1"></i>Unit: <strong class="text-dark">'.e($product->unit?->name ?? 'pcs').'</strong></div>';

                return '<div class="py-1" style="min-width:140px;">' . $cat . $unit . '</div>';
            })
            ->addColumn('stock_status', function ($product) {
                $stock = (float)($product->stock_on_hand ?? 0);
                $formattedStock = $product->allow_decimal_qty ? rtrim(rtrim(number_format($stock, 3), '0'), '.') : number_format($stock);
                $unitName = e($product->unit?->name ?? 'pcs');

                if ($stock <= 0) {
                    $badge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold extra-small"><i class="bi bi-x-circle-fill me-1"></i>Out of Stock</span>';
                } elseif ($stock <= ($product->reorder_level ?? 0)) {
                    $badge = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold extra-small"><i class="bi bi-exclamation-triangle-fill me-1"></i>Low Stock</span>';
                } else {
                    $badge = '<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold extra-small"><i class="bi bi-check-circle-fill me-1"></i>In Stock</span>';
                }

                $reorderInfo = $product->reorder_level > 0
                    ? '<div class="text-muted extra-small mt-1" style="font-size:0.75rem;">Reorder: '.$product->reorder_level.' '.$unitName.'</div>'
                    : '';

                return '
                    <div class="py-1" style="min-width:130px;">
                        <div class="d-flex align-items-baseline gap-1.5">
                            <span class="font-mono fw-black text-dark fs-6">'.$formattedStock.'</span>
                            <span class="text-muted extra-small">'.$unitName.'</span>
                        </div>
                        <div class="mt-1">
                            '.$badge.'
                            '.$reorderInfo.'
                        </div>
                    </div>
                ';
            })
            ->addColumn('pricing_matrix', function ($product) {
                $cost = (float)($product->cost_price ?? 0);
                $retail = (float)($product->selling_price ?? 0);
                $wholesale = $product->wholesale_price !== null ? (float)$product->wholesale_price : null;

                $profit = $retail - $cost;
                $marginPercent = $retail > 0 ? round(($profit / $retail) * 100, 1) : 0;

                $wholesaleHtml = $wholesale !== null
                    ? '<div class="extra-small text-muted font-mono d-flex justify-content-between gap-2 mt-0.5"><span class="text-secondary">Wholesale:</span> <strong class="text-primary">₱'.number_format($wholesale, 2).'</strong></div>'
                    : '';

                $marginBadge = $retail > 0
                    ? '<span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold font-mono">+₱'.number_format($profit, 2).' ('.$marginPercent.'%)</span>'
                    : '';

                return '
                    <div class="py-1" style="min-width:160px;">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <span class="extra-small text-muted">Retail:</span>
                            <strong class="font-mono text-dark fs-6 fw-black">₱'.number_format($retail, 2).'</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between gap-3 extra-small text-muted font-mono">
                            <span>Cost:</span>
                            <span>₱'.number_format($cost, 2).'</span>
                        </div>
                        '.$wholesaleHtml.'
                        <div class="mt-1">
                            '.$marginBadge.'
                        </div>
                    </div>
                ';
            })
            ->addColumn('status_badge', function ($product) {
                return match ($product->status) {
                    'active' => '<div class="text-center py-1"><span class="badge bg-success-subtle text-success border border-success-subtle fw-bold extra-small text-nowrap"><i class="bi bi-circle-fill me-1" style="font-size:0.45rem;"></i>Active</span></div>',
                    'inactive' => '<div class="text-center py-1"><span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-bold extra-small text-nowrap"><i class="bi bi-circle-fill me-1" style="font-size:0.45rem;"></i>Inactive</span></div>',
                    default => '<div class="text-center py-1"><span class="badge bg-warning-subtle text-warning border border-warning-subtle fw-bold extra-small text-nowrap">Unknown</span></div>',
                };
            })
            ->filterColumn('product_info', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                      ->orWhere('barcode', 'like', "%{$keyword}%")
                      ->orWhere('sku', 'like', "%{$keyword}%");
                });
            })
            ->rawColumns([
                'checkbox',
                'actions',
                'product_info',
                'category_unit',
                'stock_status',
                'pricing_matrix',
                'status_badge'
            ])
            ->make(true);
    }

    /**
     * Enterprise CRM 360° Quick View Payload
     */
    public function quickView($id)
    {
        $tenantId = auth()->user()->tenant_id;
        $realId = is_numeric($id) ? (int)$id : decrypt($id);

        $product = POSProducts::with([
            'category',
            'unit',
            'variants',
            'createdBy',
            'updatedBy',
            'stocks' => fn($q) => $q->latest()->limit(6)
        ])
        ->where('tenant_id', $tenantId)
        ->findOrFail($realId);

        // Sales Performance
        $totalUnitsSold = DB::table('pos_sale_items')
            ->where('product_id', $product->id)
            ->sum('qty') ?? 0;

        $totalRevenue = DB::table('pos_sale_items')
            ->where('product_id', $product->id)
            ->sum('line_total') ?? 0;

        $barcodeSvg = null;
        if ($product->barcode) {
            try {
                $dns = new DNS1D();
                $barcodeSvg = $dns->getBarcodeSVG($product->barcode, 'C128', 1.5, 45);
            } catch (\Throwable $e) {
                $barcodeSvg = null;
            }
        }

        $cost = (float)($product->cost_price ?? 0);
        $retail = (float)($product->selling_price ?? 0);
        $profit = $retail - $cost;
        $margin = $retail > 0 ? round(($profit / $retail) * 100, 1) : 0;

        return response()->json([
            'id' => $product->id,
            'encrypted_id' => encrypt($product->id),
            'name' => $product->name,
            'description' => $product->description,
            'barcode' => $product->barcode,
            'sku' => $product->sku,
            'image_url' => $product->image ? Storage::url($product->image) : asset('images/no_image.jpg'),
            'category_name' => $product->category?->name ?? 'Uncategorized',
            'unit_name' => $product->unit?->name ?? 'pcs',
            'cost_price' => $cost,
            'selling_price' => $retail,
            'wholesale_price' => $product->wholesale_price ? (float)$product->wholesale_price : null,
            'profit' => $profit,
            'margin' => $margin,
            'stock_on_hand' => (float)$product->stock_on_hand,
            'reorder_level' => $product->reorder_level,
            'allow_decimal_qty' => (bool)$product->allow_decimal_qty,
            'status' => $product->status,
            'created_at' => $product->created_at?->format('M d, Y h:i A'),
            'updated_at' => $product->updated_at?->format('M d, Y h:i A'),
            'created_by' => $product->createdBy?->name ?? 'System',
            'barcode_svg' => $barcodeSvg,
            'total_units_sold' => (float)$totalUnitsSold,
            'total_revenue' => (float)$totalRevenue,
            'variants' => $product->variants->map(fn($v) => [
                'id' => $v->id,
                'name' => $v->variant_name,
                'qty_per_pack' => (float)$v->qty_per_pack,
                'selling_price' => (float)$v->selling_price,
                'wholesale_price' => $v->wholesale_price ? (float)$v->wholesale_price : null,
                'cost_price' => (float)$v->cost_price,
                'barcode' => $v->barcode,
            ]),
            'recent_stocks' => $product->stocks->map(fn($s) => [
                'type' => $s->type ?? 'movement',
                'qty' => (float)($s->qty ?? $s->quantity ?? 0),
                'note' => $s->notes ?? $s->reason ?? 'Stock adjustment',
                'date' => $s->created_at?->format('M d, Y h:i A'),
            ]),
        ]);
    }

    /**
     * Enterprise Bulk Batch Actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,update_category,price_markup,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        $query = POSProducts::where('tenant_id', $tenantId)->whereIn('id', $ids);
        $count = $query->count();

        if ($count === 0) {
            return response()->json(['success' => false, 'message' => 'No matching products found.'], 404);
        }

        if ($action === 'activate') {
            $query->update(['status' => 'active', 'updated_by' => auth()->id()]);
            return response()->json(['success' => true, 'message' => "Successfully activated {$count} products."]);
        }

        if ($action === 'deactivate') {
            $query->update(['status' => 'inactive', 'updated_by' => auth()->id()]);
            return response()->json(['success' => true, 'message' => "Successfully deactivated {$count} products."]);
        }

        if ($action === 'update_category') {
            $catId = $request->input('category_id');
            $query->update(['category_id' => $catId ?: null, 'updated_by' => auth()->id()]);
            return response()->json(['success' => true, 'message' => "Category updated for {$count} products."]);
        }

        if ($action === 'price_markup') {
            $percent = (float)$request->input('markup_percent', 0);
            $fixed   = (float)$request->input('markup_fixed', 0);

            $products = $query->with('variants')->get();
            foreach ($products as $p) {
                $oldSelling = (float)$p->selling_price;
                $newSelling = $oldSelling;

                if ($percent != 0) {
                    $newSelling = round($newSelling * (1 + ($percent / 100)), 2);
                }
                if ($fixed != 0) {
                    $newSelling = max(0, round($newSelling + $fixed, 2));
                }

                if ($oldSelling != $newSelling) {
                    $p->selling_price = $newSelling;
                    $p->updated_by = auth()->id();
                    $p->save();

                    ProductPriceHistory::create([
                        'tenant_id'           => $tenantId,
                        'product_id'          => $p->id,
                        'variant_id'          => null,
                        'cost_price'          => $p->cost_price,
                        'new_cost_price'      => $p->cost_price,
                        'selling_price'       => $oldSelling,
                        'new_selling_price'   => $newSelling,
                        'wholesale_price'     => $p->wholesale_price,
                        'new_wholesale_price' => $p->wholesale_price,
                        'reason'              => 'Bulk Price Markup Adjustment',
                        'remarks'             => 'Applied via Catalogue Bulk Actions',
                        'effective_date'      => now(),
                        'status'              => 'active',
                        'created_by'          => auth()->id(),
                        'updated_by'          => auth()->id(),
                    ]);
                }

                // Also markup variants
                if ($p->variants && $p->variants->isNotEmpty()) {
                    foreach ($p->variants as $v) {
                        $oldVSelling = (float)$v->selling_price;
                        $newVSelling = $oldVSelling;

                        if ($percent != 0) {
                            $newVSelling = round($newVSelling * (1 + ($percent / 100)), 2);
                        }
                        if ($fixed != 0) {
                            $newVSelling = max(0, round($newVSelling + $fixed, 2));
                        }

                        if ($oldVSelling != $newVSelling) {
                            $v->selling_price = $newVSelling;
                            $v->updated_by = auth()->id();
                            $v->save();

                            ProductPriceHistory::create([
                                'tenant_id'           => $tenantId,
                                'product_id'          => $p->id,
                                'variant_id'          => $v->id,
                                'cost_price'          => $v->cost_price,
                                'new_cost_price'      => $v->cost_price,
                                'selling_price'       => $oldVSelling,
                                'new_selling_price'   => $newVSelling,
                                'wholesale_price'     => $v->wholesale_price,
                                'new_wholesale_price' => $v->wholesale_price,
                                'reason'              => 'Bulk Price Markup Adjustment',
                                'remarks'             => "Variant: {$v->variant_name}",
                                'effective_date'      => now(),
                                'status'              => 'active',
                                'created_by'          => auth()->id(),
                                'updated_by'          => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            return response()->json(['success' => true, 'message' => "Price adjustment applied to {$count} products and their variants."]);
        }

        if ($action === 'delete') {
            $query->delete();
            return response()->json(['success' => true, 'message' => "Deleted {$count} products from catalogue."]);
        }

        return response()->json(['success' => false, 'message' => 'Unknown bulk action.'], 400);
    }

    /**
     * Export Products to CSV/Excel
     */
    public function exportCsv(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = POSProducts::with(['category', 'unit'])->where('tenant_id', $tenantId);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock_on_hand', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock_on_hand', '>', 0)->whereColumn('stock_on_hand', '<=', 'reorder_level');
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock_on_hand', '>', 0);
            }
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Product_Masterlist_' . date('Y-m-d_His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Barcode', 'SKU', 'Product Name', 'Category', 'Unit',
                'Cost Price', 'Selling Price (Retail)', 'Wholesale Price', 'Stock On Hand',
                'Reorder Level', 'Is Weighed/Fractional', 'Status', 'Date Created'
            ]);

            foreach ($products as $p) {
                fputcsv($file, [
                    $p->id,
                    $p->barcode ?? '',
                    $p->sku ?? '',
                    $p->name,
                    $p->category?->name ?? 'Uncategorized',
                    $p->unit?->name ?? 'pcs',
                    number_format((float)$p->cost_price, 2, '.', ''),
                    number_format((float)$p->selling_price, 2, '.', ''),
                    $p->wholesale_price !== null ? number_format((float)$p->wholesale_price, 2, '.', '') : 'N/A',
                    (float)$p->stock_on_hand,
                    $p->reorder_level ?? 0,
                    $p->allow_decimal_qty ? 'Yes' : 'No',
                    $p->status,
                    $p->created_at ? $p->created_at->format('Y-m-d H:i') : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(POSProducts $product)
    {
        return view('pages.store.products.show', [
            'product' => $product,
        ]);
    }

    public function destroy(POSProducts $product)
    {
        $product->delete();

        return redirect()->route('products.index');
    }

    public function products_search(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $search = $request->get('q');

        $products = POSProducts::query()
            ->where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('archived', 0)->orWhereNull('archived');
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%")
                       ->orWhere('barcode', 'LIKE', "%{$search}%")
                       ->orWhere('sku', 'LIKE', "%{$search}%")
                       // also match variant names/barcodes/skus
                       ->orWhereHas('variants', function($qv) use ($search) {
                           $qv->where('variant_name', 'LIKE', "%{$search}%")
                              ->orWhere('barcode', 'LIKE', "%{$search}%")
                              ->orWhere('sku', 'LIKE', "%{$search}%");
                       });
                });
            })
            ->with(['variants' => function($q) {
                $q->select('id', 'product_id', 'variant_name', 'barcode', 'sku', 'stock_on_hand', 'cost_price', 'selling_price');
            }])
            ->select('id', 'name', 'barcode', 'sku', 'stock_on_hand', 'cost_price', 'selling_price')
            ->orderBy('name')
            ->limit(30)
            ->get();

        $results = [];

        foreach ($products as $item) {
            $hasVariants = $item->variants && $item->variants->count() > 0;

            if ($hasVariants) {
                // Return as Select2 optgroup — each variant is a child option
                $children = $item->variants->map(function ($v) use ($item) {
                    $vStock = (float) $v->stock_on_hand;
                    $vCost  = (float) ($v->cost_price ?? $item->cost_price);
                    $vPrice = (float) ($v->selling_price ?? $item->selling_price);
                    $subText = 'Stock: ' . number_format($vStock)
                        . ($v->barcode ? ' | Bar: ' . $v->barcode : '')
                        . ($v->sku     ? ' | SKU: ' . $v->sku     : '');

                    return [
                        'id'          => encryptId($item->id) . ':variant:' . $v->id,
                        'text'        => $v->variant_name . ' (' . $subText . ')',
                        'name'        => $item->name . ' — ' . $v->variant_name,
                        'product_id'  => encryptId($item->id),
                        'variant_id'  => $v->id,
                        'variant_name'=> $v->variant_name,
                        'stock'       => $vStock,
                        'cost_price'  => $vCost,
                        'selling_price'=> $vPrice,
                        'has_variant' => true,
                    ];
                })->values()->toArray();

                $results[] = [
                    'text'     => $item->name,     // This becomes the optgroup label
                    'children' => $children,
                ];
            } else {
                // Simple flat product
                $stock = (float) $item->stock_on_hand;
                $subText = 'Stock: ' . number_format($stock)
                    . ($item->barcode ? ' | Barcode: ' . $item->barcode : '')
                    . ($item->sku     ? ' | SKU: ' . $item->sku          : '');

                $results[] = [
                    'id'          => encryptId($item->id),
                    'text'        => $item->name . ' (' . $subText . ')',
                    'name'        => $item->name,
                    'product_id'  => encryptId($item->id),
                    'variant_id'  => null,
                    'variant_name'=> null,
                    'stock'       => $stock,
                    'cost_price'  => (float) $item->cost_price,
                    'selling_price'=> (float) $item->selling_price,
                    'has_variant' => false,
                ];
            }
        }

        return response()->json(['results' => $results]);
    }
}
