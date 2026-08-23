<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSUnits;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $totalUnits = POSUnits::where('tenant_id', $tenantId)->count();
        $activeUnits = POSUnits::where('tenant_id', $tenantId)->where('status', 'active')->count();
        $totalAssignedProducts = \App\Models\POS\POSProducts::where('tenant_id', $tenantId)->whereNotNull('unit_id')->count();

        $topUnit = POSUnits::where('tenant_id', $tenantId)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->first();

        $topUnitName  = $topUnit ? $topUnit->name : 'N/A';
        $topUnitCount = $topUnit ? $topUnit->products_count : 0;

        return view('pages.tenants.products.units.index', compact(
            'totalUnits',
            'activeUnits',
            'totalAssignedProducts',
            'topUnitName',
            'topUnitCount'
        ));
    }

    public function create()
    {
        return view('pages.tenants.products.units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $unit = new POSUnits();
        $unit->tenant_id = auth()->user()->tenant_id;
        $unit->name = $data['name'];
        $unit->description = $data['description'] ?? null;
        $unit->status = $data['status'] ?? 'active';

        $this->setCommonFields($unit);
        $unit->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product Unit created successfully.',
                'unit'   => $unit,
            ]);
        }

        return redirect()
            ->route('products.units.index')
            ->with('success', 'Product Unit created successfully.');
    }

    public function edit(string $id)
    {
        $realId = is_numeric($id) ? (int)$id : decrypt($id);
        $unit = POSUnits::where('tenant_id', auth()->user()->tenant_id)->findOrFail($realId);

        return view('pages.tenants.products.units.edit', compact('unit'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $realId = is_numeric($id) ? (int)$id : decrypt($id);
        $unit = POSUnits::where('tenant_id', auth()->user()->tenant_id)->findOrFail($realId);

        $unit->name = $data['name'];
        $unit->description = $data['description'] ?? null;
        if (isset($data['status'])) {
            $unit->status = $data['status'];
        }

        $this->setCommonFields($unit);
        $unit->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product Unit updated successfully.',
                'unit'   => $unit,
            ]);
        }

        return redirect()
            ->route('products.units.index')
            ->with('success', 'Product Unit updated successfully.');
    }

    public function destroy(string $id)
    {
        $realId = is_numeric($id) ? (int)$id : decrypt($id);
        $unit = POSUnits::where('tenant_id', auth()->user()->tenant_id)->findOrFail($realId);

        // Unlink unit from products
        \App\Models\POS\POSProducts::where('unit_id', $unit->id)
            ->update(['unit_id' => null]);

        $unit->delete();

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Product Unit deleted successfully.',
            ]);
        }

        return redirect()
            ->route('products.units.index')
            ->with('success', 'Product Unit deleted successfully.');
    }

    public function ajaxData(Request $request)
    {
        $query = POSUnits::with(['createdBy'])
            ->withCount('products')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->latest();

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($unit) {
                $encId = encrypt($unit->id);
                return '
                <div class="dropdown">
                    <button class="btn btn-light border btn-sm rounded-2 extra-small font-mono fw-bold px-2.5 shadow-xs" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config=\'{"strategy":"fixed"}\'>
                        Actions <i class="bi bi-chevron-down ms-1"></i>
                    </button>
                    <ul class="dropdown-menu shadow-lg border-0 font-mono small rounded-3 p-1.5" style="z-index:1080; min-width: 170px;">
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-primary fw-semibold py-1.5 rounded-2 btn-edit-unit" data-id="' . $encId . '" data-name="' . e($unit->name) . '" data-description="' . e($unit->description ?? '') . '" data-status="' . e($unit->status ?? 'active') . '">
                                <i class="bi bi-pencil"></i> Edit Unit
                            </button>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <button type="button" class="dropdown-item d-flex align-items-center gap-2 text-danger fw-semibold py-1.5 rounded-2 btn-delete-unit" data-id="' . $encId . '" data-name="' . e($unit->name) . '">
                                <i class="bi bi-trash"></i> Delete Unit
                            </button>
                        </li>
                    </ul>
                </div>
                ';
            })

            ->addColumn('name', function ($unit) {
                return '
                    <div class="fw-bold fs-6 lh-sm" style="color:#0f172a;">' . e($unit->name) . '</div>
                ';
            })

            ->addColumn('description', function ($unit) {
                return $unit->description
                    ? '<span style="color:#64748b;" class="font-mono extra-small">' . e($unit->description) . '</span>'
                    : '<span style="color:#94a3b8;" class="font-mono extra-small">No description</span>';
            })

            ->addColumn('products_count', function ($unit) {
                return '<span class="badge extra-small font-mono fw-bold" style="background:#f3e8ff;color:#6b21a8;border:1px solid #d8b4fe;"><i class="bi bi-box-seam me-1"></i>' . number_format($unit->products_count ?? 0) . ' Products</span>';
            })

            ->addColumn('status', function ($unit) {
                return ($unit->status ?? 'active') === 'active'
                    ? '<span class="badge extra-small fw-bold" style="background:#dcfce7;color:#166534;border:1px solid #86efac;"><i class="bi bi-check-circle-fill me-1"></i>Active</span>'
                    : '<span class="badge extra-small fw-bold" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;"><i class="bi bi-x-circle-fill me-1"></i>Inactive</span>';
            })

            ->addColumn('createdAt', function ($unit) {
                return '
                    <div>
                        <div class="fw-bold extra-small font-mono" style="color:#1e293b;">' . ($unit->created_at ? $unit->created_at->format('M d, Y') : 'N/A') . '</div>
                        <small class="extra-small font-mono" style="color:#64748b;"><i class="bi bi-clock me-1"></i>' . ($unit->created_at ? $unit->created_at->format('h:i A') : '') . '</small>
                    </div>
                ';
            })

            ->addColumn('createdBy', function ($unit) {
                $userName = $unit->createdBy ? e($unit->createdBy->name) : 'System';
                $initial = strtoupper(substr($userName, 0, 1));
                return '
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center extra-small" style="width:26px;height:26px;background:#e0f2fe;color:#0369a1;font-size:0.75rem;">
                            ' . $initial . '
                        </div>
                        <span class="fw-bold extra-small" style="color:#334155;">' . $userName . '</span>
                    </div>
                ';
            })

            ->rawColumns([
                'actions',
                'name',
                'description',
                'products_count',
                'status',
                'createdAt',
                'createdBy'
            ])

            ->make(true);
    }
}
