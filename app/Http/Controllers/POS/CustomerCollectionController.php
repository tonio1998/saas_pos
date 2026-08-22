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
                return '<span class="fw-bold font-mono text-primary">₱' . number_format($row->total_debit, 2) . '</span>';
            })
            ->editColumn('total_credit', function ($row) {
                return '<span class="fw-bold font-mono text-success">₱' . number_format($row->total_credit, 2) . '</span>';
            })
            ->editColumn('balance', function ($row) {
                $class = $row->balance > 0 ? 'text-danger' : 'text-success';
                return '<span class="fw-black font-mono ' . $class . '" style="font-size:0.9rem;">₱' . number_format($row->balance, 2) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                $payUrl = route('customers.collections.create', encryptId($row->customer_id));
                $ledgerUrl = route('customers.credit.show', encryptId($row->customer_id));
                $customerName = e($row->customer?->CustomerName ?? 'Customer');

                return '
                    <button
                        type="button"
                        class="btn btn-soft-primary btn-sm btn-actions rounded-pill px-3 py-1 fw-bold shadow-xs hover-lift d-inline-flex align-items-center gap-1.5"
                        data-title="Options: ' . $customerName . '"
                        data-template="col-actions-' . $row->customer_id . '"
                        style="font-size:0.75rem;background:#f0f7ff;color:#0284c7;border:1px solid #bae6fd;"
                    >
                        <i class="bi bi-gear-fill text-primary"></i>
                        <span>Actions</span>
                    </button>

                    <template id="col-actions-' . $row->customer_id . '">
                        <div class="d-grid gap-2 p-1">
                            <a href="' . $payUrl . '" class="btn btn-success text-start d-flex align-items-center gap-2 py-2 px-3 rounded-3 fw-bold" style="background:#059669;border-color:#059669;color:#fff;">
                                <i class="bi bi-cash-coin fs-5"></i>
                                <div>
                                    <div>Receive Payment</div>
                                    <small class="opacity-75 font-mono" style="font-size:0.75rem;">Settle uncollected balance</small>
                                </div>
                            </a>

                            <a href="' . $ledgerUrl . '" class="btn btn-outline-danger text-start d-flex align-items-center gap-2 py-2 px-3 rounded-3 fw-bold">
                                <i class="bi bi-book-half fs-5 text-danger"></i>
                                <div>
                                    <div class="text-danger">Account Ledger History</div>
                                    <small class="text-muted" style="font-size:0.75rem;">View debit and credit entries</small>
                                </div>
                            </a>
                        </div>
                    </template>
                ';
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
            if ($request->has('amount')) {
                $request->merge([
                    'amount' => (float) str_replace(',', '', (string)$request->amount)
                ]);
            }

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
                $payment->payment_date = $request->payment_date;
                $payment->payment_method = $request->payment_method;
                $payment->amount = $request->amount;
                $payment->reference_number = $request->reference_no;
                $payment->notes = $request->remarks;
                $this->setCommonFields($payment);
                $payment->save();

                $runningBalance = max(0, $lastBalance - $request->amount);

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

            return redirect()->route('customers.credit.show', encryptId($customerId))
                ->with('success', 'Payment of ₱' . number_format($request->amount, 2) . ' collected and posted successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }
    }
}
