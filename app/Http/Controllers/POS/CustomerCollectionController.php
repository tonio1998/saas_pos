<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\POSCustomerLedger;
use App\Models\POS\POSCustomers;
use App\Models\POS\POSPayment;
use App\Traits\TCommonFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
