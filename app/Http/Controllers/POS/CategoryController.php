<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCategories;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.tenants.products.categories.index');
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
        ]);

        $category = new POSCategories();
        $category->tenant_id = auth()->user()->tenant_id;
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $this->setCommonFields($category);
        $category->save();

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = POSCategories::findOrFail($id);

        return view('pages.tenants.products.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = POSCategories::findOrFail($id);
        $category->name = $data['name'];
        $category->description = $data['description'] ?? null;
        $this->setCommonFields($category);
        $category->save();

        return redirect()
            ->route('products.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function ajaxData(Request $request)
    {
        $query = POSCategories::with([
            'createdBy',
        ])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->latest();

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($category) {
                return '<a href="' . route('products.categories.edit', $category->id) . '" class="btn btn-soft-primary btn-sm">
                    <i class="bi bi-pencil"></i>
                    Edit Category
                </a>';
            })
            ->addColumn('name', function ($category) {
                return $category->name;
            })
            ->addColumn('description', function ($category) {
                return $category->description;
            })
            ->addColumn('createdAt', function ($category) {
                return $category->created_at
                    ? format_date($category->created_at)
                    : 'N/A';
            })
            ->addColumn('createdBy', function ($category) {
                return $category->createdBy
                    ? '<span class="fw-semibold">' . $category->createdBy->name . '</span>'
                    : '<span class="badge bg-light text-dark">System</span>';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->rawColumns([
                'actions',
                'name',
                'description',
                'createdAt',
                'createdBy',
            ])
            ->make(true);
    }
}
