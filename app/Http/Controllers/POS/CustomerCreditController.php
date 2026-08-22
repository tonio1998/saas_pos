<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerCreditController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $customerBalances = POSCustomerLedger::query()
            ->select([
                'customer_id',
                DB::raw('SUM(debit - credit) as balance')
            ])
            ->where('tenant_id', $tenantId)
            ->groupBy('customer_id')
            ->havingRaw('SUM(debit - credit) > 0')
            ->get();

        $accountsWithUtang = $customerBalances->count();
        $totalUtangBalance = (float)$customerBalances->sum('balance');
        $maxSingleUtang = (float)($customerBalances->max('balance') ?? 0);
        $avgUtangPerAccount = $accountsWithUtang > 0 ? ($totalUtangBalance / $accountsWithUtang) : 0;

        return view('pages.tenants.customers.credits.index', compact(
            'accountsWithUtang',
            'totalUtangBalance',
            'maxSingleUtang',
            'avgUtangPerAccount'
        ));
    }

    public function create()
    {
        return view('pages.tenants.customers.credits.create');
    }

    public function show(Request $request, $CustomerID)
    {
        $tenantId = auth()->user()->tenant_id;
        $id = is_numeric($CustomerID) ? (int)$CustomerID : decryptId($CustomerID);

        $customer = POSCustomers::where('tenant_id', $tenantId)->findOrFail($id);

        // Calculate real current net balance
        $realBalance = (float) POSCustomerLedger::where('customer_id', $id)
            ->where('tenant_id', $tenantId)
            ->sum(DB::raw('debit - credit'));

        if ($customer->credit) {
            $customer->credit->running_balance = $realBalance;
        }

        return view('pages.tenants.customers.credits.show', compact('customer'));
    }

    public function ledgerData(Request $request, $CustomerID)
    {
        $tenantId = auth()->user()->tenant_id;
        $id = is_numeric($CustomerID) ? (int)$CustomerID : decryptId($CustomerID);

        $ledger = POSCustomerLedger::where('tenant_id', $tenantId)
            ->where('customer_id', $id)
            ->orderBy('id', 'desc');

        return DataTables::of($ledger)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return '<span class="font-mono extra-small text-dark">' . ($row->created_at ? $row->created_at->format('M d, Y h:i A') : '-') . '</span>';
            })
            ->addColumn('reference_no', function ($row) {
                return '<span class="font-mono fw-black text-primary">' . e($row->reference_no ?: ('REF-' . $row->id)) . '</span>';
            })
            ->addColumn('transaction_type', function ($row) {
                $type = strtoupper($row->transaction_type);
                $badge = in_array($type, ['PAYMENT', 'CREDIT', 'REFUND']) ? 'bg-success text-white' : 'bg-danger text-white';
                return '<span class="badge ' . $badge . ' rounded-pill px-2.5 py-1 font-mono fw-bold text-uppercase shadow-xs">' . $type . '</span>';
            })
            ->editColumn('debit', function ($row) {
                $val = (float)$row->debit;
                return $val > 0 ? '<span class="font-mono fw-bold text-danger">₱' . number_format($val, 2) . '</span>' : '<span class="text-muted font-mono">-</span>';
            })
            ->editColumn('credit', function ($row) {
                $val = (float)$row->credit;
                return $val > 0 ? '<span class="font-mono fw-bold text-success">₱' . number_format($val, 2) . '</span>' : '<span class="text-muted font-mono">-</span>';
            })
            ->editColumn('running_balance', function ($row) {
                return '<span class="font-mono fw-black text-dark fs-6">₱' . number_format($row->running_balance, 2) . '</span>';
            })
            ->editColumn('remarks', function ($row) {
                return '<span class="extra-small text-muted">' . e($row->remarks ?: '-') . '</span>';
            })
            ->rawColumns(['date', 'reference_no', 'transaction_type', 'debit', 'credit', 'running_balance', 'remarks'])
            ->make(true);
    }

    public function ajaxData(Request $request)
    {
        $customers = POSCustomers::with(['credit', 'createdBy'])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereHas('credit', function ($query) {
                $query->where('running_balance', '>', 0);
            })
            ->latest();

        return DataTables::eloquent($customers)
            ->addColumn('actions', function ($customer) {
                $ledgerUrl = route('customers.credit.show', encryptId($customer->id));
                $settleUrl = route('customers.collections.create', encryptId($customer->id));
                $editUrl = route('customers.edit', encryptId($customer->id));
                $balance = optional($customer->credit)->running_balance ?? 0;
                $settleOption = $balance > 0 ? '
                    <a href="' . $settleUrl . '" class="btn btn-success text-start d-flex align-items-center gap-3 p-3 rounded-3 shadow-xs hover-lift" style="background:#059669;border-color:#059669;color:#fff;">
                        <div class="rounded-3 bg-white bg-opacity-20 text-white d-flex align-items-center justify-content-center p-2" style="width:38px;height:38px;flex-shrink:0;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">Receive / Settle Payment</div>
                            <small class="opacity-90 font-mono" style="font-size:0.75rem;">Balance: ₱' . number_format($balance, 2) . '</small>
                        </div>
                        <i class="bi bi-chevron-right opacity-75 extra-small"></i>
                    </a>
                ' : '';

                return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions rounded-pill px-3 py-1 fw-bold shadow-xs hover-lift d-inline-flex align-items-center gap-1.5"
                        data-title="Options: ' . e($customer->CustomerName) . '"
                        data-template="credit-actions-' . $customer->id . '"
                    >
                        <i class="bi bi-gear-fill"></i> Actions
                    </button>

                    <template id="credit-actions-' . $customer->id . '">
                        <div class="d-flex flex-column gap-2 p-1">
                            ' . $settleOption . '

                            <a href="' . $ledgerUrl . '" class="btn btn-white border border-danger-subtle text-start d-flex align-items-center gap-3 p-3 rounded-3 shadow-xs hover-lift">
                                <div class="rounded-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center p-2" style="width:38px;height:38px;flex-shrink:0;">
                                    <i class="bi bi-book-half fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-danger" style="font-size:0.9rem;">Credit Ledger Statement</div>
                                    <small class="text-muted extra-small d-block">View history of purchases on credit</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted extra-small"></i>
                            </a>

                            <a href="' . $editUrl . '" class="btn btn-white border border-primary-subtle text-start d-flex align-items-center gap-3 p-3 rounded-3 shadow-xs hover-lift">
                                <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center p-2" style="width:38px;height:38px;flex-shrink:0;">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark" style="font-size:0.9rem;">Edit Customer Profile</div>
                                    <small class="text-muted extra-small d-block">Adjust credit limits & info</small>
                                </div>
                                <i class="bi bi-chevron-right text-muted extra-small"></i>
                            </a>
                        </div>
                    </template>
                ';
            })
            ->addColumn('CustomerCode', function ($customer) {
                return '<span class="font-mono fw-black text-primary">' . e(getCustomerCode($customer->id)) . '</span>';
            })
            ->addColumn('CustomerName', function ($customer) {
                return '<span class="fw-black text-dark fs-6">' . e($customer->CustomerName) . '</span>';
            })
            ->addColumn('createdAt', function ($customer) {
                return '<span class="font-mono extra-small text-dark">' . ($customer->created_at ? format_date($customer->created_at) : 'N/A') . '</span>';
            })
            ->addColumn('credit', function ($customer) {
                $balance = optional($customer->credit)->running_balance ?? 0;
                return '<span class="fw-black text-danger font-mono fs-6">₱' . number_format($balance, 2) . '</span>';
            })
            ->addColumn('createdBy', function ($customer) {
                return '<span class="fw-bold text-dark">' . e($customer->createdBy ? $customer->createdBy->name : 'System') . '</span>';
            })
            ->editColumn('status', function ($customer) {
                return StatusHelper::badge($customer->status);
            })
            ->rawColumns([
                'actions',
                'CustomerCode',
                'CustomerName',
                'createdAt',
                'createdBy',
                'status',
                'credit'
            ])
            ->make(true);
    }
}
