<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerCreditController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.tenants.customers.credits.index');
    }

    public function create()
    {
        return view('pages.tenants.customers.credits.create');
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
                        style="font-size:0.75rem;background:#f0f7ff;color:#0284c7;border:1px solid #bae6fd;"
                    >
                        <i class="bi bi-gear-fill text-primary"></i>
                        <span>Actions</span>
                    </button>

                    <template id="credit-actions-' . $customer->id . '">
                        <div class="d-grid gap-2.5">
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
                return getCustomerCode($customer->id);
            })
            ->addColumn('createdAt', function ($customer) {
                return $customer->created_at ? format_date($customer->created_at) : 'N/A';
            })
            ->addColumn('credit', function ($customer) {
                $balance = optional($customer->credit)->running_balance ?? 0;
                return '<span class="fw-black text-danger font-mono" style="font-size:0.9rem;">₱' . number_format($balance, 2) . '</span>';
            })
            ->addColumn('createdBy', function ($customer) {
                return $customer->createdBy ? $customer->createdBy->name : 'System';
            })
            ->editColumn('status', function ($customer) {
                return StatusHelper::badge($customer->status);
            })
            ->rawColumns([
                'actions',
                'createdBy',
                'status',
                'credit'
            ])
            ->make(true);
    }

    public function show(Request $request)
    {
        $id = decryptId($request->segment(4));
        $customer = POSCustomers::with('credit')->findOrFail($id);
        return view('pages.tenants.customers.credits.show', compact('customer'));
    }

    public function ledgerData($CustomerID)
    {
        $customerId = decryptId($CustomerID);
        $ledger = POSCustomerLedger::query()
            ->with(['customer', 'sale'])
            ->where('customer_id', $customerId)
            ->orderByDesc('id');

        return DataTables::eloquent($ledger)
            ->addColumn('date', function ($row) {
                return '<span class="font-mono">' . $row->created_at->format('M d, Y h:i A') . '</span>';
            })
            ->editColumn('reference', function ($row) {
                return $row->reference ?? '-';
            })
            ->editColumn('CustomerName', function ($row) {
                return $row->customer->CustomerName ?? '-';
            })
            ->editColumn('debit', function ($row) {
                return $row->debit > 0
                    ? '<span class="text-danger fw-black font-mono">₱' . number_format($row->debit, 2) . '</span>'
                    : '<span class="text-muted font-mono">-</span>';
            })
            ->editColumn('credit', function ($row) {
                return $row->credit > 0
                    ? '<span class="text-success fw-black font-mono">₱' . number_format($row->credit, 2) . '</span>'
                    : '<span class="text-muted font-mono">-</span>';
            })
            ->editColumn('running_balance', function ($row) {
                return '<span class="fw-black font-mono text-dark" style="font-size:0.9rem;">₱' . number_format($row->running_balance, 2) . '</span>';
            })
            ->addColumn('status', function ($row) {
                return StatusHelper::badge($row->status);
            })
            ->editColumn('transaction_type', function ($row) {
                return StatusHelper::badge($row->transaction_type);
            })
            ->addColumn('reference_no', function ($row) {

                if (!$row->sale) {
                    return '---';
                }

                return '
                    <a
                        href="#"
                        class="text-decoration-none fw-semibold view-sale"
                        data-id="' . ($row->sale->id) . '"
                    >
                        ' . e($row->sale->sale_code) . '
                    </a>
                ';
            })
            ->rawColumns([
                'debit',
                'credit',
                'running_balance',
                'status',
                'transaction_type',
                'reference_no'
            ])
            ->make(true);
    }
}
