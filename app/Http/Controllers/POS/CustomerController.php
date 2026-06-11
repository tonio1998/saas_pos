<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomers;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view(
            'pages.tenants.customers.index'
        );
    }

    public function create()
    {
        return view(
            'pages.tenants.customers.create'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100'
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100'
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'mobile_number' => [
                'nullable',
                'string',
                'max:30'
            ],
            'address' => [
                'nullable',
                'string'
            ],
            'customer_type' => [
                'required',
                'in:walkin,regular,business,senior,pwd,credit'
            ],
            'discount_percent' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'credit_limit' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'remarks' => [
                'nullable',
                'string'
            ]
        ]);

        $customer = new POSCustomers();

        $customer->tenant_id = auth()->user()->tenant_id;
        $customer->customer_code = 'CUS-' . time();
        $customer->first_name = $data['first_name'];
        $customer->middle_name = $data['middle_name'] ?? null;
        $customer->last_name = $data['last_name'];
        $customer->company_name = $data['company_name'] ?? null;
        $customer->email = $data['email'] ?? null;
        $customer->mobile_number = $data['mobile_number'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->customer_type = $data['customer_type'];
        $customer->discount_percent = $data['discount_percent'] ?? 0;
        $customer->credit_limit = $data['credit_limit'] ?? 0;
        $customer->current_balance = 0;
        $customer->remarks = $data['remarks'] ?? null;

        $this->setCommonFields(
            $customer
        );

        $customer->save();

        return redirect()
            ->route(
                'customers.index'
            )
            ->with(
                'success',
                'Customer created successfully.'
            );
    }

    public function edit(string $id)
    {
        $customer = POSCustomers::findOrFail(
            decrypt($id)
        );

        return view(
            'pages.tenants.customers.edit',
            compact('customer')
        );
    }

    public function update(
        Request $request,
        string $id
    ) {

        $data = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:100'
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:100'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100'
            ],
            'company_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                'nullable',
                'email',
                'max:255'
            ],
            'mobile_number' => [
                'nullable',
                'string',
                'max:30'
            ],
            'address' => [
                'nullable',
                'string'
            ],
            'customer_type' => [
                'required',
                'in:walkin,regular,business,senior,pwd,credit'
            ],
            'discount_percent' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'credit_limit' => [
                'nullable',
                'numeric',
                'min:0'
            ],
            'remarks' => [
                'nullable',
                'string'
            ]
        ]);

        $customer = POSCustomers::findOrFail(
            decrypt($id)
        );

        $customer->first_name = $data['first_name'];
        $customer->middle_name = $data['middle_name'] ?? null;
        $customer->last_name = $data['last_name'];
        $customer->company_name = $data['company_name'] ?? null;
        $customer->email = $data['email'] ?? null;
        $customer->mobile_number = $data['mobile_number'] ?? null;
        $customer->address = $data['address'] ?? null;
        $customer->customer_type = $data['customer_type'];
        $customer->discount_percent = $data['discount_percent'] ?? 0;
        $customer->credit_limit = $data['credit_limit'] ?? 0;
        $customer->remarks = $data['remarks'] ?? null;

        $this->setCommonFields(
            $customer
        );

        $customer->save();

        return redirect()
            ->route(
                'customers.index'
            )
            ->with(
                'success',
                'Customer updated successfully.'
            );
    }

    public function ajaxData(Request $request)
    {
        $query = POSCustomers::with([
            'createdBy'
        ])
            ->where(
                'tenant_id',
                auth()->user()->tenant_id
            )
            ->latest();

        return datatables()
            ->eloquent($query)

            ->addColumn('actions', function ($customer) {

                return '
                    <a
                        href="' . route(
                        'customers.edit',
                        encrypt($customer->id)
                    ) . '"
                        class="btn btn-soft-primary btn-sm"
                    >
                        <i class="bi bi-pencil"></i>
                        Edit Customer
                    </a>
                ';
            })

            ->addColumn('customer_name', function ($customer) {

                return trim(
                    $customer->first_name . ' ' .
                    $customer->middle_name . ' ' .
                    $customer->last_name
                );
            })

            ->addColumn('customer_type', function ($customer) {

                return ucfirst(
                    $customer->customer_type
                );
            })

            ->addColumn('createdAt', function ($customer) {

                return $customer->created_at
                    ? format_date(
                        $customer->created_at
                    )
                    : 'N/A';
            })

            ->addColumn('createdBy', function ($customer) {

                return $customer->createdBy
                    ? '<span class="fw-semibold">' .
                    $customer->createdBy->name .
                    '</span>'
                    : '<span class="badge bg-light text-dark">System</span>';
            })

            ->rawColumns([
                'actions',
                'createdBy'
            ])

            ->make(true);
    }
}
