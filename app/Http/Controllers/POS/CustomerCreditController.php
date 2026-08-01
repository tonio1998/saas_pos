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
                $btn = '';

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
                        data-bs-toggle="modal"
                        data-bs-target="#actionModal"
                        data-title="Customer Actions"
                        data-template="actions-' . $customer->id . '"
                    >
                        <i class="bi bi-gear"></i>
                        Actions
                    </button>

                    <template id="actions-' . $customer->id . '">
                        <div class="d-grid gap-2">
                            ' . $btn . '
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
                return '₱' . number_format($balance, 2);
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
                return $row->created_at->format('M d, Y h:i A');
            })
            ->editColumn('reference', function ($row) {
                return $row->reference ?? '-';
            })
            ->editColumn('CustomerName', function ($row) {
                return $row->customer->CustomerName ?? '-';
            })
            ->editColumn('debit', function ($row) {
                return $row->debit > 0
                    ? '<span class="text-danger fw-semibold">₱'.number_format($row->debit, 2).'</span>'
                    : '-';
            })
            ->editColumn('credit', function ($row) {
                return $row->credit > 0
                    ? '<span class="text-success fw-semibold">₱'.number_format($row->credit, 2).'</span>'
                    : '-';
            })
            ->editColumn('running_balance', function ($row) {
                return '<span class="fw-bold">₱'.number_format($row->running_balance, 2).'</span>';
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
