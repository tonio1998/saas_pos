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
        return view('pages.tenants.customers.credit.index');
    }

    public function create()
    {
        return view('pages.tenants.customers.credit.create');
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
            ->orderBy('id');

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
