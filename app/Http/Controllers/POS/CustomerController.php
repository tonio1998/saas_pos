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

    public function kpis()
    {
        $tenantId = auth()->user()->tenant_id;
        $totalCustomers = POSCustomers::where('tenant_id', $tenantId)->count();
        $vipCustomers = POSCustomers::where('tenant_id', $tenantId)->whereIn('customer_type', ['vip', 'regular', 'business'])->count();
        $totalPoints = POSCustomers::where('tenant_id', $tenantId)->sum('TotalPoints') ?? 0;

        $customersWithLedgers = POSCustomers::where('tenant_id', $tenantId)
            ->with(['credit'])
            ->get();

        $totalUtang = 0;
        $customersWithUtangCount = 0;
        foreach ($customersWithLedgers as $c) {
            $bal = optional($c->credit)->running_balance ?? 0;
            if ($bal > 0) {
                $totalUtang += $bal;
                $customersWithUtangCount++;
            }
        }

        return response()->json([
            'total_customers' => number_format($totalCustomers),
            'vip_customers' => number_format($vipCustomers),
            'total_points' => number_format($totalPoints),
            'total_utang' => '₱' . number_format($totalUtang, 2),
            'customers_with_utang_count' => $customersWithUtangCount
        ]);
    }

    public function ajaxData(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $query = POSCustomers::with(['createdBy', 'credit'])
            ->withCount('sales')
            ->withSum('sales', 'total_amount')
            ->where('tenant_id', $tenantId);

        // Quick Tab Filter
        if ($request->filled('quick_tab')) {
            $tab = $request->quick_tab;
            if ($tab === 'vip') {
                $query->whereIn('customer_type', ['vip', 'business']);
            } elseif ($tab === 'regular') {
                $query->where('customer_type', 'regular');
            } elseif ($tab === 'senior_pwd') {
                $query->whereIn('customer_type', ['senior', 'pwd']);
            } elseif ($tab === 'credit') {
                $query->where('customer_type', 'credit');
            }
        }

        if ($request->filled('customer_type_filter')) {
            $query->where('customer_type', $request->customer_type_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        $query->latest();

        return datatables()
            ->eloquent($query)
            ->addColumn('actions', function ($customer) {
                $editUrl = route('customers.edit', encryptId($customer->id));
                $creditUrl = route('customers.credit.show', encryptId($customer->id));
                $balance = optional($customer->credit)->running_balance ?? 0;
                $settleUrl = route('customers.collections.create', encryptId($customer->id));

                $settleOption = $balance > 0 ? '
                    <a href="' . $settleUrl . '" class="btn btn-success text-start d-flex align-items-center gap-2 py-2 px-3 rounded-3 fw-bold" style="background:#059669;border-color:#059669;color:#fff;">
                        <i class="bi bi-wallet2 fs-5"></i>
                        <div>
                            <div>Receive / Pay Utang</div>
                            <small class="opacity-75 font-mono" style="font-size:0.75rem;">Balance: ₱' . number_format($balance, 2) . '</small>
                        </div>
                    </a>
                ' : '';

                return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions rounded-pill px-3 py-1 fw-bold shadow-xs hover-lift d-inline-flex align-items-center gap-1.5"
                        data-title="Options: ' . e($customer->CustomerName) . '"
                        data-template="customer-actions-' . $customer->id . '"
                        style="font-size:0.75rem;background:#f0f7ff;color:#0284c7;border:1px solid #bae6fd;"
                    >
                        <i class="bi bi-gear-fill text-primary"></i>
                        <span>Actions</span>
                    </button>

                    <template id="customer-actions-' . $customer->id . '">
                        <div class="d-grid gap-2 p-1">
                            <a href="' . $editUrl . '" class="btn btn-primary text-start d-flex align-items-center gap-2 py-2 px-3 rounded-3 fw-bold" style="background:#2563eb;border-color:#2563eb;color:#fff;">
                                <i class="bi bi-pencil-square fs-5"></i>
                                <div>
                                    <div>Edit Customer Profile</div>
                                    <small class="opacity-75" style="font-size:0.75rem;">Update name, contact & credit limit</small>
                                </div>
                            </a>

                            <a href="' . $creditUrl . '" class="btn btn-outline-danger text-start d-flex align-items-center gap-2 py-2 px-3 rounded-3 fw-bold">
                                <i class="bi bi-book-half fs-5 text-danger"></i>
                                <div>
                                    <div class="text-danger">Credit Ledger & Statement</div>
                                    <small class="text-muted" style="font-size:0.75rem;">View complete audit trail & transactions</small>
                                </div>
                            </a>

                            ' . $settleOption . '
                        </div>
                    </template>
                ';
            })
            ->addColumn('customer_info', function ($customer) {
                $name = e($customer->CustomerName);
                $initials = $name ? collect(explode(' ', $name))->map(fn($n) => $n[0] ?? '')->take(2)->join('') : 'CU';
                $initials = strtoupper($initials);
                $code = $customer->customer_code != '0' ? $customer->customer_code : getCustomerCode($customer->id);
                $company = e($customer->company_name ?? '');

                $avatarColors = ['#059669', '#2563eb', '#7c3aed', '#d97706', '#0891b2', '#e11d48'];
                $color = $avatarColors[$customer->id % count($avatarColors)];

                $companyBadge = $company ? '<span class="badge bg-light text-muted border extra-small me-1"><i class="bi bi-building me-1"></i>' . $company . '</span>' : '';

                return '
                    <div class="d-flex align-items-center py-1.5">
                        <div class="avatar-initials me-3" style="background:' . $color . ';width:38px;height:38px;min-width:38px;flex-shrink:0;font-size:0.8rem;">
                            ' . $initials . '
                        </div>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark text-truncate" style="font-size:0.9rem;" title="' . $name . '">
                                ' . $name . '
                            </div>
                            <div class="d-flex align-items-center gap-1.5 mt-1">
                                <span class="badge bg-light text-dark border font-mono fw-bold" style="font-size:0.7rem;">' . e($code) . '</span>
                                ' . $companyBadge . '
                            </div>
                        </div>
                    </div>
                ';
            })
            ->addColumn('contact_details', function ($customer) {
                $phone = e($customer->mobile_number ?? '');
                $email = e($customer->email ?? '');
                $address = e($customer->CustomerAddress ?? '');

                $html = '<div class="extra-small text-muted font-mono" style="font-size:0.72rem;line-height:1.4;">';
                if ($phone) {
                    $html .= '<div><i class="bi bi-telephone text-primary me-1"></i>' . $phone . '</div>';
                }
                if ($email) {
                    $html .= '<div class="text-truncate" style="max-width:160px;" title="' . $email . '"><i class="bi bi-envelope text-secondary me-1"></i>' . $email . '</div>';
                }
                if ($address) {
                    $html .= '<div class="text-truncate" style="max-width:160px;" title="' . $address . '"><i class="bi bi-geo-alt text-danger me-1"></i>' . $address . '</div>';
                }
                if (!$phone && !$email && !$address) {
                    $html .= '<span class="text-muted opacity-75">No contact info</span>';
                }
                $html .= '</div>';
                return $html;
            })
            ->addColumn('customer_type', function ($customer) {
                $type = strtolower($customer->customer_type ?? 'regular');
                $badges = [
                    'vip' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-bold" style="font-size:0.7rem;"><i class="bi bi-crown me-1"></i>VIP</span>',
                    'regular' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold" style="font-size:0.7rem;"><i class="bi bi-person me-1"></i>Regular</span>',
                    'business' => '<span class="badge bg-info-subtle text-info border border-info-subtle fw-bold" style="font-size:0.7rem;"><i class="bi bi-briefcase me-1"></i>Business</span>',
                    'senior' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-bold" style="font-size:0.7rem;"><i class="bi bi-person-badge me-1"></i>Senior (20%)</span>',
                    'pwd' => '<span class="badge bg-purple-subtle text-purple border border-purple-subtle fw-bold" style="font-size:0.7rem;background:#f3e8ff;color:#6b21a8;"><i class="bi bi-heart-pulse me-1"></i>PWD (20%)</span>',
                    'credit' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size:0.7rem;"><i class="bi bi-credit-card me-1"></i>Credit</span>',
                ];

                $discount = (float)($customer->discount_percent ?? 0);
                $discountPill = $discount > 0 ? '<div class="mt-1"><span class="badge bg-success-subtle text-success border border-success-subtle font-mono" style="font-size:0.62rem;">' . number_format($discount, 0) . '% Off</span></div>' : '';

                return ($badges[$type] ?? '<span class="badge bg-light text-dark border">' . e(ucfirst($type)) . '</span>') . $discountPill;
            })
            ->addColumn('TotalPoints', function ($customer) {
                $points = $customer->TotalPoints ?? 0;
                return '
                    <div class="d-inline-flex align-items-center gap-1 bg-warning-subtle border border-warning-subtle px-2.5 py-1 rounded-pill font-mono fw-bold text-dark" style="font-size:0.75rem;">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span>' . number_format($points) . ' pts</span>
                    </div>
                ';
            })
            ->addColumn('credit_health', function ($customer) {
                $balance = optional($customer->credit)->running_balance ?? 0;
                $limit = (float)($customer->credit_limit ?? 0);

                $balanceHtml = $balance > 0
                    ? '<div class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono fw-black mb-0.5" style="font-size:0.78rem;padding:3px 8px;">₱' . number_format($balance, 2) . '</div>'
                    : '<div class="text-success font-mono fw-bold extra-small" style="font-size:0.72rem;"><i class="bi bi-check2 me-0.5"></i>₱0.00</div>';

                $limitHtml = $limit > 0
                    ? '<div class="extra-small text-muted font-mono" style="font-size:0.65rem;">Limit: ₱' . number_format($limit, 2) . '</div>'
                    : '<div class="extra-small text-muted" style="font-size:0.65rem;">No Limit</div>';

                return $balanceHtml . $limitHtml;
            })
            ->addColumn('orders_spend', function ($customer) {
                $count = $customer->sales_count ?? 0;
                $total = (float)($customer->sales_sum_total_amount ?? 0);

                return '
                    <div>
                        <div class="font-mono fw-black text-dark" style="font-size:0.82rem;">₱' . number_format($total, 2) . '</div>
                        <div class="extra-small text-muted font-mono" style="font-size:0.65rem;"><i class="bi bi-bag-check me-0.5 text-primary"></i>' . number_format($count) . ' orders</div>
                    </div>
                ';
            })
            ->addColumn('status', function ($customer) {
                $status = strtolower($customer->status ?? 'active');
                if ($status === 'active') {
                    return '<span class="badge bg-success-subtle text-success border border-success-subtle fw-bold rounded-pill" style="font-size:0.68rem;padding:3px 8px;"><i class="bi bi-check-circle-fill me-1"></i>Active</span>';
                }
                return '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-bold rounded-pill" style="font-size:0.68rem;padding:3px 8px;">Inactive</span>';
            })
            ->addColumn('createdAt', function ($customer) {
                $date = $customer->created_at ? format_date($customer->created_at) : 'N/A';
                $by = $customer->createdBy ? $customer->createdBy->name : 'System';

                return '
                    <div>
                        <div class="font-mono text-dark extra-small fw-bold">' . $date . '</div>
                        <div class="extra-small text-muted" style="font-size:0.65rem;">By ' . e($by) . '</div>
                    </div>
                ';
            })
            ->rawColumns([
                'actions',
                'customer_info',
                'contact_details',
                'customer_type',
                'TotalPoints',
                'credit_health',
                'orders_spend',
                'status',
                'createdAt'
            ])
            ->make(true);
    }
}
