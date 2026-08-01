<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\Customers;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSSale;
use App\Models\SchoolUsers;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use TCommonFunctions;

    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => ['required','integer'],
            'customer_name' => ['required','string','max:255'],
            'customer_address' => ['nullable','string','max:255'],
        ]);

        $customer = new POSCustomers();
        $customer->tenant_id = auth()->user()->tenant_id;
        $customer->CustomerName = $validated['customer_name'];
        $customer->CustomerAddress = $validated['customer_address'];
        $this->setCommonFields($customer);
        $customer->save();

        if($validated['sale_id']){
            $sale = POSSale::find($validated['sale_id']);
            if($sale){
                $sale->customer_id = $customer->id;
                $sale->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully.',
            'customer' => [
                'id' => $customer->id,
                'text' => $customer->CustomerName . ' [' . $customer->CustomerAddress . ']'
            ]
        ]);
    }

    public function customers_search(Request $request)
    {
        $search = $request->search;
        $users = POSCustomers::query()
            ->when($search,function($q) use ($search){
                $q->where('CustomerName','like',"%{$search}%");
            })
            ->where('tenant_id',auth()->user()->tenant_id)
            ->limit(10)
            ->get();
        return $users->map(function($user){
            return [
                'id'=>$user->id,
                'text'=>$user->CustomerName . ' [' . $user->CustomerAddress . ']'
            ];
        });
    }

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
            'CustomerName' => [
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
            'CustomerAddress' => [
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
        $customer->CustomerName = $data['CustomerName'];
        $customer->company_name = $data['company_name'] ?? null;
        $customer->email = $data['email'] ?? null;
        $customer->mobile_number = $data['mobile_number'] ?? null;
        $customer->CustomerAddress = $data['CustomerAddress'] ?? null;
        $customer->customer_type = $data['customer_type'];
        $customer->discount_percent = $data['discount_percent'] ?? 0;
        $customer->credit_limit = $data['credit_limit'] ?? 0;
        $customer->remarks = $data['remarks'] ?? null;
        $this->setCommonFields($customer);
        $customer->save();

        $customer->customer_code = getCustomerCode($customer->id);
        $customer->save();

        $customer->ledger()->create([
            'tenant_id' => auth()->user()->tenant_id,
            'customer_id' => $customer->id,
            'transaction_type' => 'CUSTOMER_REGISTRATION',
            'debit' => 0,
            'credit' => 0,
            'running_balance' => 0,
            'remarks' => 'Customer registration',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
            'status' => 'active',
            'archived' => 0
        ]);

        return redirect()->route('customers.index')
            ->with(
                'success',
                'Customer created successfully.'
            );
    }

    public function edit(string $id)
    {
        $customer = POSCustomers::findOrFail(decryptId($id));
        return view('pages.tenants.customers.edit', compact('customer'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'CustomerName' => ['required', 'string', 'max:100'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:30'],
            'CustomerAddress' => ['nullable', 'string'],
            'customer_type' => ['required', 'in:walkin,regular,business,senior,pwd,credit'],
            'discount_percent' => ['nullable', 'numeric', 'min:0'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string']
        ]);

        $customer = POSCustomers::findOrFail(decrypt($id));

        $customer->CustomerName = $data['CustomerName'];
        $customer->company_name = $data['company_name'] ?? null;
        $customer->email = $data['email'] ?? null;
        $customer->mobile_number = $data['mobile_number'] ?? null;
        $customer->CustomerAddress = $data['CustomerAddress'] ?? null;
        $customer->customer_type = $data['customer_type'];
        $customer->discount_percent = $data['discount_percent'] ?? 0;
        $customer->credit_limit = $data['credit_limit'] ?? 0;
        $customer->remarks = $data['remarks'] ?? null;
        $this->setCommonFields($customer);
        $customer->save();

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function ajaxData(Request $request)
    {
        $query = POSCustomers::with(['createdBy'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with('credit')
            ->latest();

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($customer) {

                $btn = '';

                $btn .= '
                    <a
                        href="' . route('customers.edit', encryptId($customer->id)) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-pencil me-2 text-primary"></i>
                        Edit Customer
                    </a>
                ';

                            $btn .= '
                    <a
                        href="' . route('customers.credit.show', encryptId($customer->id)) . '"
                        class="btn btn-light text-start"
                    >
                        <i class="bi bi-credit-card me-2 text-danger"></i>
                        Credit History
                    </a>
                ';

                            return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions"
                        data-title="Customer Actions"
                        data-template="customer-actions-' . $customer->id . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>

                    <template id="customer-actions-' . $customer->id . '">
                        <div class="d-grid gap-2">
                            ' . $btn . '
                        </div>
                    </template>
                ';
            })
            ->addColumn('CustomerCode', function ($customer) {
                return $customer->customer_code != '0' ? $customer->customer_code : getCustomerCode($customer->id);
            })
            ->addColumn('createdAt', function ($customer) {
                return $customer->created_at ? format_date($customer->created_at) : 'N/A';
            })
            ->addColumn('credit', function ($customer) {
                $balance = optional($customer->credit)->running_balance ?? 0;

                return $balance > 0
                    ? '<span class="text-danger fw-bold">₱' . number_format($balance, 2) . '</span>'
                    : '<span class="text-muted">₱0.00</span>';
            })
            ->addColumn('createdBy', function ($customer) {
                return $customer->createdBy ? $customer->createdBy->name : 'System';
            })
            ->editColumn('status', function ($customer) {
                return StatusHelper::badge($customer->status);
            })
            ->editColumn('customer_type', function ($customer) {
                return StatusHelper::badge($customer->customer_type);
            })
            ->rawColumns([
                'actions',
                'createdBy',
                'status',
                'credit',
                'customer_type'
            ])
            ->make(true);
    }
}
