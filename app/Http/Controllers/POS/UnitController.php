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
        return view(
            'pages.tenants.products.units.index'
        );
    }

    public function create()
    {
        return view(
            'pages.tenants.products.units.create'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50'
            ],
            'description' => [
                'nullable',
                'string',
                'max:255'
            ]
        ]);

        $unit = new POSUnits();
        $unit->tenant_id = auth()->user()->tenant_id;
        $unit->name = $data['name'];
        $unit->description = $data['description'] ?? null;

        $this->setCommonFields(
            $unit
        );

        $unit->save();

        return redirect()
            ->route(
                'products.units.index'
            )
            ->with(
                'success',
                'Unit created successfully.'
            );
    }

    public function edit(string $id)
    {
        $unit = POSUnits::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.tenants.products.units.edit',
            compact('unit')
        );
    }

    public function update(
        Request $request,
        string $id
    ) {

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:50'
            ],
            'description' => [
                'nullable',
                'string',
                'max:255'
            ]
        ]);

        $unit = POSUnits::findOrFail(
            decrypt($id)
        );

        $unit->name = $data['name'];
        $unit->description = $data['description'] ?? null;

        $this->setCommonFields(
            $unit
        );

        $unit->save();

        return redirect()
            ->route(
                'products.units.index'
            )
            ->with(
                'success',
                'Unit updated successfully.'
            );
    }

    public function ajaxData(Request $request)
    {
        $query = POSUnits::with([
            'createdBy'
        ])
            ->where(
                'tenant_id',
                auth()->user()->tenant_id
            )
            ->latest();

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($unit) {

                return '
                    <a
                        href="' . route(
                        'products.units.edit',
                        encrypt($unit->id)
                    ) . '"
                        class="btn btn-soft-primary btn-sm"
                    >
                        <i class="bi bi-pencil"></i>
                        Edit Unit
                    </a>
                ';
            })

            ->addColumn('name', function ($unit) {
                return $unit->name;
            })

            ->addColumn('description', function ($unit) {
                return $unit->description;
            })

            ->addColumn('status', function ($unit) {

                return $unit->status === 'active'
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';
            })

            ->addColumn('createdAt', function ($unit) {

                return $unit->created_at
                    ? format_date(
                        $unit->created_at
                    )
                    : 'N/A';
            })

            ->addColumn('createdBy', function ($unit) {

                return $unit->createdBy
                    ? '<span class="fw-semibold">' .
                    $unit->createdBy->name .
                    '</span>'
                    : '<span class="badge bg-light text-dark">System</span>';
            })

            ->filterColumn('name', function (
                $query,
                $keyword
            ) {
                $query->where(
                    'name',
                    'like',
                    "%{$keyword}%"
                );
            })

            ->rawColumns([
                'actions',
                'status',
                'createdBy'
            ])

            ->make(true);
    }
}
