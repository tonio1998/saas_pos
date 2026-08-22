<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCategories;
use App\Models\POS\POSProducts;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $totalCategories = POSCategories::where('tenant_id', $tenantId)->count();
        $activeCategories = POSCategories::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $totalAssignedProducts = POSProducts::where('tenant_id', $tenantId)->whereNotNull('category_id')->count();

        $topCategory = POSCategories::withCount('products')
            ->where('tenant_id', $tenantId)
            ->orderBy('products_count', 'desc')
            ->first();

        $topCategoryName = $topCategory ? $topCategory->name : 'None';
        $topCategoryCount = $topCategory ? $topCategory->products_count : 0;

        return view('pages.tenants.products.categories.index', compact(
            'totalCategories',
            'activeCategories',
            'totalAssignedProducts',
            'topCategoryName',
            'topCategoryCount'
        ));
    }

    public function create()
    {
        return view('pages.tenants.products.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $category = new POSCategories();
        $category->tenant_id = auth()->user()->tenant_id;
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        if (isset($data['status'])) {
            $category->status = $data['status'];
        }
        $this->setCommonFields($category);
        $category->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category "' . $category->name . '" created successfully!',
            ]);
        }

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $realId = is_numeric($id) ? (int)$id : decryptId($id);
        $category = POSCategories::where('tenant_id', auth()->user()->tenant_id)->findOrFail($realId);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'encrypted_id' => encryptId($category->id)
            ]);
        }

        return view('pages.tenants.products.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $realId = is_numeric($id) ? (int)$id : decryptId($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $category = POSCategories::where('tenant_id', auth()->user()->tenant_id)->findOrFail($realId);
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        if (isset($data['status'])) {
            $category->status = $data['status'];
        }
        $this->setCommonFields($category);
        $category->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category "' . $category->name . '" updated successfully!',
            ]);
        }

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $realId = is_numeric($id) ? (int)$id : decryptId($id);
        $category = POSCategories::where('tenant_id', auth()->user()->tenant_id)->findOrFail($realId);
        $categoryName = $category->name;

        // Reassign products to null category if any
        POSProducts::where('category_id', $category->id)->update(['category_id' => null]);
        $category->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category "' . $categoryName . '" deleted successfully!',
            ]);
        }

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    public function ajaxData(Request $request)
    {
        $query = POSCategories::with(['createdBy'])
            ->withCount('products')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->latest();

        return DataTables::of($query)
            ->addColumn('actions', function ($category) {
                $encId = encryptId($category->id);
                return '<div class="d-flex align-items-center gap-1.5">
                    <button type="button" class="btn btn-sm btn-light border font-mono fw-bold px-2.5 py-1 rounded-2 text-dark shadow-xs hover-lift btn-edit-category" data-id="' . $encId . '" data-name="' . e($category->name) . '" data-description="' . e($category->description) . '" data-status="' . e($category->status) . '">
                        <i class="bi bi-pencil-square me-1 text-primary"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-light border border-danger-subtle font-mono fw-bold px-2.5 py-1 rounded-2 text-danger shadow-xs hover-lift btn-delete-category" data-id="' . $encId . '" data-name="' . e($category->name) . '">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>';
            })
            ->editColumn('name', function ($category) {
                return '<span class="fw-black text-dark fs-6 font-mono"><i class="bi bi-folder-fill text-warning me-1.5"></i>' . e($category->name) . '</span>';
            })
            ->editColumn('description', function ($category) {
                return '<span class="text-muted extra-small font-mono">' . e($category->description ?: 'No description provided') . '</span>';
            })
            ->addColumn('products_count', function ($category) {
                return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono fw-bold px-2.5 py-1"><i class="bi bi-box-seam me-1"></i>' . number_format($category->products_count) . ' SKUs</span>';
            })
            ->editColumn('status', function ($category) {
                return StatusHelper::badge($category->status);
            })
            ->addColumn('createdAt', function ($category) {
                return '<span class="font-mono extra-small text-dark">' . ($category->created_at ? format_date($category->created_at) : 'N/A') . '</span>';
            })
            ->addColumn('createdBy', function ($category) {
                return '<span class="fw-semibold text-dark font-mono">' . e($category->createdBy ? $category->createdBy->name : 'System') . '</span>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->rawColumns([
                'actions',
                'name',
                'description',
                'products_count',
                'status',
                'createdAt',
                'createdBy',
            ])
            ->make(true);
    }
}
