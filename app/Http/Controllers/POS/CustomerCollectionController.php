<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Models\POS\POSCashTransaction;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CustomerCollectionController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        $summary = POSCustomerLedger::query()
            ->select([
                'customer_id',
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit'),
                DB::raw('SUM(debit - credit) as balance')
            ])
            ->where('tenant_id', $tenantId)
            ->groupBy('customer_id')
            ->havingRaw('SUM(debit - credit) > 0')
            ->get();

        $totalAccounts = $summary->count();
        $totalDebit = (float)$summary->sum('total_debit');
        $totalCredit = (float)$summary->sum('total_credit');
        $totalBalance = (float)$summary->sum('balance');

        return view('pages.tenants.customers.collections.index', compact(
            'totalAccounts',
            'totalDebit',
            'totalCredit',
            'totalBalance'
        ));
    }

    public function create(Request $request)
    {
        $customerId = decryptId($request->segment(3));
        $customer = POSCustomers::with('credit')->findOrFail($customerId);

        // Compute real dynamic running balance to guarantee 100% data consistency
        $realBalance = (float) POSCustomerLedger::where('customer_id', $customerId)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->sum(DB::raw('debit - credit'));

        if ($customer->credit) {
            $customer->credit->running_balance = $realBalance;
        }

        return view('pages.tenants.customers.collections.create', compact('customerId', 'customer'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;
        $customerId = decryptId($request->input('customer_id'));

        $request->validate([
            'customer_id' => 'required',
            'amount' => 'required|numeric|gt:0',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
        ]);

        $customer = POSCustomers::where('tenant_id', $tenantId)->findOrFail($customerId);
        $amount = (float)$request->input('amount');
        $paymentMethod = $request->input('payment_method');
        $paymentDate = $request->input('payment_date') . ' ' . now()->format('H:i:s');
        $referenceNo = $request->input('reference_no') ?: ('COL-' . strtoupper(Str::random(6)));
        $remarks = $request->input('remarks') ?: ('Utang payment collection via ' . strtoupper($paymentMethod));

        DB::transaction(function () use ($tenantId, $customerId, $customer, $amount, $paymentMethod, $paymentDate, $referenceNo, $remarks) {
            // Compute real current balance before payment
            $currentBalance = (float) POSCustomerLedger::where('customer_id', $customerId)
                ->where('tenant_id', $tenantId)
                ->sum(DB::raw('debit - credit'));

            $newBalance = max(0, $currentBalance - $amount);

            // Post Ledger Entry following standard controller instantiation pattern
            $ledger = new POSCustomerLedger();
            $ledger->tenant_id = $tenantId;
            $ledger->customer_id = $customerId;
            $ledger->reference_no = $referenceNo;
            $ledger->transaction_type = 'PAYMENT';
            $ledger->debit = 0;
            $ledger->credit = $amount;
            $ledger->running_balance = $newBalance;
            $ledger->remarks = $remarks;
            $this->setCommonFields($ledger);
            $ledger->created_at = $paymentDate;
            $ledger->save();

            // Record cash transaction if cash payment
            if (strtolower($paymentMethod) === 'cash') {
                $cash = new POSCashTransaction();
                $cash->tenant_id = $tenantId;
                $cash->transaction_code = 'IN-' . strtoupper(Str::random(6));
                $cash->cashier_id = auth()->id();
                $cash->transaction_type = 'cash_in';
                $cash->category = 'UTANG_COLLECTION';
                $cash->amount = $amount;
                $cash->reference_no = $referenceNo;
                $cash->remarks = 'Utang collection payment from ' . $customer->CustomerName . ' [Completed]';
                $this->setCommonFields($cash);
                $cash->created_at = $paymentDate;
                $cash->save();
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Utang payment of ₱' . number_format($amount, 2) . ' collected successfully for ' . $customer->CustomerName . '!',
                'redirect_url' => route('customers.collections.index')
            ]);
        }

        return redirect()->route('customers.collections.index')->with('success', 'Utang payment of ₱' . number_format($amount, 2) . ' collected successfully for ' . $customer->CustomerName . '!');
    }

    public function ajaxData(Request $request)
    {
        $collections = POSCustomerLedger::query()
            ->select([
                'customer_id',
                DB::raw('MAX(created_at) as last_transaction'),
                DB::raw('SUM(debit) as total_debit'),
                DB::raw('SUM(credit) as total_credit'),
                DB::raw('SUM(debit - credit) as balance')
            ])
            ->with('customer:id,customer_code,CustomerName,customer_type,mobile_number')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->groupBy('customer_id')
            ->havingRaw('SUM(debit - credit) > 0');

        return DataTables::eloquent($collections)
            ->addColumn('customer_code', function ($row) {
                return '<span class="font-mono fw-black text-primary">' . e($row->customer?->customer_code) . '</span>';
            })
            ->addColumn('customer_name', function ($row) {
                return '<span class="fw-black text-dark fs-6">' . e($row->customer?->CustomerName) . '</span>';
            })
            ->addColumn('customer_type', function ($row) {
                return StatusHelper::badge($row->customer?->customer_type);
            })
            ->addColumn('mobile_number', function ($row) {
                return '<span class="font-mono fw-bold text-dark">' . e($row->customer?->mobile_number ?? '---') . '</span>';
            })
            ->editColumn('total_debit', function ($row) {
                return '<span class="font-mono fw-bold text-dark">₱' . number_format($row->total_debit, 2) . '</span>';
            })
            ->editColumn('total_credit', function ($row) {
                return '<span class="font-mono fw-bold text-success">₱' . number_format($row->total_credit, 2) . '</span>';
            })
            ->editColumn('balance', function ($row) {
                $class = $row->balance > 0 ? 'text-danger fw-black' : 'text-success fw-black';
                return '<span class="font-mono ' . $class . ' fs-6">₱' . number_format($row->balance, 2) . '</span>';
            })
            ->editColumn('last_transaction', function ($row) {
                return '<span class="font-mono extra-small text-dark">' . StatusHelper::formatDateTime($row->last_transaction) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encryptId($row->customer_id);
                return '
                <div class="d-flex align-items-center gap-1.5">
                    <a href="' . route('customers.collections.create', $encryptedId) . '" class="btn btn-sm btn-success font-mono fw-bold px-2.5 py-1 rounded-2 shadow-xs d-inline-flex align-items-center gap-1" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                        <i class="bi bi-cash-stack"></i> Collect Payment
                    </a>
                    <a href="' . route('customers.credit.ledger.show', $row->customer_id) . '" class="btn btn-sm btn-light border font-mono fw-bold px-2 py-1 rounded-2 text-dark">
                        <i class="bi bi-journal-text"></i> Ledger
                    </a>
                </div>';
            })
            ->rawColumns(['customer_code', 'customer_name', 'customer_type', 'mobile_number', 'total_debit', 'total_credit', 'balance', 'last_transaction', 'actions'])
            ->make(true);
    }
}
