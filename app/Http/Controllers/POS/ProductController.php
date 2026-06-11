<?php

namespace App\Http\Controllers\POS;

use App\Actions\POS\Products\StoreProduct;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCategories;
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
        return view('pages.tenants.products.index');
    }

    public function create()
    {
        $units = POSUnits::query()
            ->with('createdBy')
            ->orderBy('name')
            ->get();

        $categories = POSCategories::query()
            ->with('createdBy')
            ->orderBy('name')
            ->get();

        return view('pages.tenants.products.create', compact('units', 'categories'));
    }

    public function edit(Request $request)
    {
        $id = decrypt($request->segment(3));
        $product = POSProducts::find($id);
        $units = POSUnits::query()
            ->with('createdBy')
            ->orderBy('name')
            ->get();

        $categories = POSCategories::query()
            ->with('createdBy')
            ->orderBy('name')
            ->get();

        return view('pages.tenants.products.create', [
            'product' => $product,
            'units' => $units,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer'],
            'unit_id' => ['nullable', 'integer'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'sku' => ['nullable', 'string', 'max:100'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        app(StoreProduct::class)->handle(
            $validated,
            auth()->user(),
            $request->file('image')
        );

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function update(
        Request $request
    ) {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'category_id' => ['nullable'],
            'unit_id' => ['nullable'],

            'barcode' => ['nullable', 'string'],
            'sku' => ['nullable', 'string'],

            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'wholesale_price' => ['nullable', 'numeric', 'min:0'],

            'reorder_level' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = POSProducts::findOrFail(
            decrypt($request->segment(3))
        );

        DB::transaction(function () use (
            $product,
            $validated
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
                    'Product price updated';

                $priceHistory->effective_date =
                    now();

                $this->setCommonFields(
                    $priceHistory,
                    $validated
                );

                $priceHistory->save();
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
            ]);
        });

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    public function ajaxData(Request $request)
    {
        $query = POSProducts::with([
            'category',
            'unit',
            'createdBy',
        ]);

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($product) {

                $encryptedId = encrypt($product->id);

                $viewUrl = route(
                    'products.show',
                    $encryptedId
                );

                $editUrl = route(
                    'products.edit',
                    $encryptedId
                );

                $receiveStockUrl = route(
                    'products.stock.receive',
                    $encryptedId
                );

                $stockHistoryUrl = route(
                    'products.stock.history',
                    $encryptedId
                );

                $stockAdjustmentUrl = route(
                    'products.stock.adjustment',
                    $encryptedId
                );

                $modalId = 'productActionModal' . $product->id;

                return '
        <button
            type="button"
            class="btn btn-soft-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#' . $modalId . '"
        >
            <i class="bi bi-gear"></i>
            Actions
        </button>

        <div
            class="modal fade"
            id="' . $modalId . '"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            Product Actions
                        </h5>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>

                    <div class="modal-body">

                        <div class="d-grid gap-2">

                            <a
                                href="' . $viewUrl . '"
                                class="btn btn-light text-start"
                            >
                                <i class="bi bi-eye text-info me-2"></i>
                                View Product
                            </a>

                            <a
                                href="' . $editUrl . '"
                                class="btn btn-light text-start"
                            >
                                <i class="bi bi-pencil text-primary me-2"></i>
                                Edit Product
                            </a>

                            <hr class="my-2">

                            <a
                                href="' . $receiveStockUrl . '"
                                class="btn btn-success text-start"
                            >
                                <i class="bi bi-box-arrow-in-down me-2"></i>
                                Receive Stock
                            </a>

                            <a
                                href="' . $stockAdjustmentUrl . '"
                                class="btn btn-warning text-start"
                            >
                                <i class="bi bi-sliders me-2"></i>
                                Stock Adjustment
                            </a>

                            <a
                                href="' . $stockHistoryUrl . '"
                                class="btn btn-secondary text-start"
                            >
                                <i class="bi bi-clock-history me-2"></i>
                                Stock History
                            </a>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    ';
            })

            ->addColumn('image', function ($product) {

                $image = $product->image
                    ? Storage::url($product->image)
                    : asset('images/no_image.jpg');

                return '
                    <img
                        src="' . $image . '"
                        class="img-thumbnail"
                        style="max-height:120px"
                        alt="Product Image"
                    >
                ';
            })
            ->addColumn('barcode', function ($product) {

                if (!$product->barcode) {
                    return '<span class="badge bg-light text-muted">No Barcode</span>';
                }

                $barcode = new DNS1D();

                return $barcode->getBarcodeSVG(
                    $product->barcode,
                    'C128',
                    1.5,
                    40
                );
            })
            ->addColumn('sku', function ($product) {
                return $product->sku
                    ?: '<span class="badge bg-light text-muted">No SKU</span>';
            })
            ->addColumn('name', function ($product) {
                $html = '
                    <div class="fw-bold">
                        ' . e($product->name ?: 'Unnamed Product') . '
                    </div>
                ';

                return $html;
            })
            ->addColumn('category', function ($product) {
                return $product->category?->name
                    ?: '<span class="badge bg-light text-muted">Uncategorized</span>';
            })
            ->addColumn('unit', function ($product) {
                return $product->unit?->name
                    ?: '<span class="badge bg-light text-muted">No Unit</span>';
            })
            ->addColumn('estimated_profit', function ($product) {
                if (
                    $product->cost_price === null ||
                    $product->selling_price === null
                ) {
                    return '<span class="badge bg-light text-muted">N/A</span>';
                }

                $profit = $product->selling_price - $product->cost_price;

                return '
                    <span class="fw-bold text-success">
                        ₱' . number_format($profit, 2) . '
                    </span>
                ';
            })
            ->addColumn('cost_price', function ($product) {
                return $product->cost_price !== null
                    ? '₱' . number_format($product->cost_price, 2)
                    : '<span class="badge bg-light text-muted">N/A</span>';
            })

            ->addColumn('selling_price', function ($product) {
                return $product->selling_price !== null
                    ? '₱' . number_format($product->selling_price, 2)
                    : '<span class="badge bg-light text-muted">N/A</span>';
            })

            ->addColumn('wholesale_price', function ($product) {
                return $product->wholesale_price !== null
                    ? '₱' . number_format($product->wholesale_price, 2)
                    : '<span class="badge bg-light text-muted">N/A</span>';
            })

            ->addColumn('status', function ($product) {

                return match ($product->status) {
                    'active' => '<span class="badge bg-success">Active</span>',
                    'inactive' => '<span class="badge bg-secondary">Inactive</span>',
                    default => '<span class="badge bg-warning">Unknown</span>',
                };
            })

            ->editColumn('created_at', function ($product) {
                return $product->created_at
                    ? $product->created_at->format('M d, Y h:i A')
                    : '<span class="badge bg-light text-muted">No Date</span>';
            })

            ->addColumn('createdBy', function ($product) {
                return $product->createdBy?->name
                    ?: '<span class="badge bg-light text-muted">System</span>';
            })

            ->filterColumn('name', function ($query, $keyword) {

                $query->where(
                    'name',
                    'like',
                    "%{$keyword}%"
                );
            })

            ->rawColumns([
                'actions',
                'image',
                'barcode',
                'sku',
                'name',
                'category',
                'unit',
                'cost_price',
                'selling_price',
                'wholesale_price',
                'status',
                'created_at',
                'createdBy',
                'estimated_profit'
            ])

            ->make(true);
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
}
