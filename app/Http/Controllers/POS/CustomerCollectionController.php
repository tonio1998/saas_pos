<?php

namespace App\Http\Controllers\POS;

use App\Helpers\StatusHelper;
use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerCollectionController extends Controller
{
    use TCommonFunctions;

    public function index()
    {
        return view('pages.tenants.customers.collections.index');
    }

    public function create(Request $request)
    {
        $customerId = decryptId($request->segment(3));
        $customer = POSCustomers::with('credit')->findOrFail($customerId);
        return view('pages.tenants.customers.collections.create', compact('customerId', 'customer'));
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
                return $row->customer?->customer_code;
            })
            ->addColumn('customer_name', function ($row) {
                return $row->customer?->CustomerName;
            })
            ->addColumn('customer_type', function ($row) {
                return StatusHelper::badge($row->customer?->customer_type);
            })
            ->addColumn('mobile_number', function ($row) {
                return $row->customer?->mobile_number ?? '---';
            })
            ->editColumn('last_transaction', function ($row) {
                return StatusHelper::formatDateTime($row->last_transaction);
            })
            ->editColumn('total_debit', function ($row) {
                return '<span class="fw-semibold text-primary">₱ ' . number_format($row->total_debit, 2) . '</span>';
            })
            ->editColumn('total_credit', function ($row) {
                return '<span class="fw-semibold text-success">₱ ' . number_format($row->total_credit, 2) . '</span>';
            })
            ->editColumn('balance', function ($row) {
                $class = $row->balance > 0
                    ? 'text-danger'
                    : 'text-success';

                return '<span class="fw-bold ' . $class . '">₱ ' . number_format($row->balance, 2) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $encryptedId = encrypt($row->customer_id);

                return 1;
            })
            ->rawColumns([
                'actions',
                'customer_type',
                'total_debit',
                'total_credit',
                'balance',
            ])
            ->make(true);
    }

    public function show(string $id)
    {
        $customerId = decrypt($id);

        $ledger = POSCustomerLedger::with([
            'customer',
            'sale',
            'payment'
        ])
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('customer_id', $customerId)
            ->orderBy('created_at')
            ->get();

        return response()->json($ledger);
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'payment_date'    => 'required|date',
                'amount'          => 'required|numeric|min:0.01',
                'payment_method'  => 'required|string',
                'reference_no'    => 'nullable|string|max:50',
                'remarks'         => 'nullable|string',
            ]);

            $customerId = decryptId($request->customer_id);

            DB::transaction(function () use ($request, $customerId) {

                $lastBalance = POSCustomerLedger::where('tenant_id', auth()->user()->tenant_id)
                    ->where('customer_id', $customerId)
                    ->latest('id')
                    ->value('running_balance') ?? 0;

                if ($request->amount > $lastBalance) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'amount' => 'Payment exceeds the customer outstanding balance.'
                    ]);
                }

                $payment = new POSPayment();
                $payment->customer_id = $customerId;
                $payment->tenant_id = auth()->user()->tenant_id;
                $payment->customer_id = $customerId;
                $payment->payment_date = $request->payment_date;
                $payment->payment_method = $request->payment_method;
                $payment->amount = $request->amount;
                $payment->reference_number = $request->reference_no;
                $payment->notes = $request->remarks;
                $this->setCommonFields($payment);
                $payment->save();

                $runningBalance = $lastBalance - $request->amount;

                $newPayment = new POSCustomerLedger();
                $newPayment->tenant_id = auth()->user()->tenant_id;
                $newPayment->customer_id = $customerId;
                $newPayment->payment_id = $payment->id;
                $newPayment->reference_no = $request->reference_no;
                $newPayment->transaction_type = 'PAYMENT';
                $newPayment->debit = 0;
                $newPayment->credit = $request->amount;
                $newPayment->running_balance = $runningBalance;
                $newPayment->remarks = $request->remarks;
                $this->setCommonFields($newPayment);
                $newPayment->save();
            });

            return redirect()->route('customers.credit.show', encryptId($customerId));
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
