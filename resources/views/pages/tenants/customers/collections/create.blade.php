@extends('layouts.app')

@section('title', 'Customer Credits')
@section('shortText', 'Monitor customer credit balances and ledger')
@section('content')

    <style>

        .summary-card{
            border:0;
            border-radius:18px;
            box-shadow:0 .125rem .5rem rgba(0,0,0,.05);
        }

        .balance-box{
            border-radius:16px;
            padding:22px;
            background:linear-gradient(135deg,#dc3545,#f06595);
            color:#fff;
        }

        .balance-label{
            font-size:.82rem;
            opacity:.9;
            letter-spacing:.5px;
            text-transform:uppercase;
        }

        .balance-value{
            font-size:2rem;
            font-weight:700;
            margin-top:6px;
        }

        .remaining-box{
            border:1px solid #e9ecef;
            border-radius:16px;
            padding:18px;
            background:#fff;
        }

        .payment-card{
            border:0;
            border-radius:18px;
            box-shadow:0 .125rem .5rem rgba(0,0,0,.05);
        }

        .section-title{
            font-size:1rem;
            font-weight:600;
            color:#495057;
        }

        .payment-input{
            font-size:2rem;
            font-weight:700;
            text-align:center;
            height:70px;
        }

        .quick-btn{
            min-width:80px;
        }

        .summary-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:.9rem 0;
            border-bottom:1px dashed #ececec;
        }

        .summary-item:last-child{
            border-bottom:0;
        }

        .summary-total{
            font-size:1.3rem;
            font-weight:700;
        }

        .badge-payment{
            font-size:.85rem;
            padding:.55rem .8rem;
        }

        .form-label{
            font-weight:600;
        }

        .card-header-clean{
            background:#fff;
            border-bottom:1px solid #f1f1f1;
            padding:1rem 1.25rem;
        }

        .card-header-clean h5{
            margin:0;
            font-weight:600;
        }

    </style>

    @if(($customer->credit->running_balance ?? 0) <= 0)

        <div class="container-fluid">

            <div class="row justify-content-center">

                <div class="col-lg-6">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center py-5">

                            <div
                                class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4"
                                style="width:90px;height:90px;"
                            >
                                <i class="bi bi-check-circle-fill text-success fs-1"></i>
                            </div>

                            <h3 class="fw-bold mb-2">
                                No Outstanding Balance
                            </h3>

                            <p class="text-muted mb-4">
                                This customer has no outstanding balance at the moment.
                                There is no payment to collect.
                            </p>

                            <div class="alert alert-success mb-4">

                                <strong>Current Balance:</strong>

                                ₱0.00

                            </div>

                            <a
                                href="{{ route('customers.credit.show', encryptId($customer->id)) }}"
                                class="btn btn-primary px-4"
                            >
                                <i class="bi bi-arrow-left me-2"></i>
                                Back to Collections
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @else
    <div class="container-fluid">
        <form
            method="POST"
            action="{{ route('customers.collections.store') }}"
        >

            @csrf

            <input
                type="hidden"
                name="customer_id"
                value="{{ encryptId($customer->id) }}"
            >

            <div class="row g-4">

                <div class="col-lg-4">

                    <div class="card summary-card">

                        <div class="card-body">

                            <div class="d-flex align-items-center mb-4">

                                <div
                                    class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center"
                                    style="width:55px;height:55px;"
                                >
                                    <i class="bi bi-person fs-3"></i>
                                </div>

                                <div class="ms-3">

                                    <h5 class="mb-1">
                                        {{ $customer->CustomerName }}
                                    </h5>

                                    <small class="text-muted">
                                        {{ $customer->CustomerAddress }}
                                    </small>

                                </div>

                            </div>

                            <div class="balance-box mb-3">

                                <div class="balance-label">
                                    Outstanding Balance
                                </div>

                                <div
                                    class="balance-value"
                                    id="currentBalanceText"
                                >
                                    ₱{{ number_format($customer->credit->running_balance ?? 0,2) }}
                                </div>

                            </div>

                            <div class="remaining-box mb-3">

                                <div class="d-flex justify-content-between">

                            <span class="text-muted">
                                Remaining Balance
                            </span>

                                    <strong
                                        id="remainingBalance"
                                        class="text-success"
                                    >
                                        ₱{{ number_format($customer->credit->running_balance??0,2) }}
                                    </strong>

                                </div>

                            </div>

                            <div class="remaining-box mb-3">

                                <div class="d-flex justify-content-between align-items-center">

                            <span class="text-muted">
                                Payment Status
                            </span>

                                    <span
                                        id="paymentStatus"
                                        class="badge bg-warning-subtle text-warning badge-payment"
                                    >
                                Pending
                            </span>

                                </div>

                            </div>

                            <div class="remaining-box">

                                <div class="summary-item">

                            <span>
                                Outstanding
                            </span>

                                    <strong>
                                        ₱{{ number_format($customer->credit->running_balance ?? 0,2) }}
                                    </strong>

                                </div>

                                <div class="summary-item">

                            <span>
                                Payment
                            </span>

                                    <strong
                                        id="summaryPayment"
                                    >
                                        ₱0.00
                                    </strong>

                                </div>

                                <div class="summary-item">

                            <span class="summary-total">
                                Remaining
                            </span>

                                    <span
                                        id="summaryRemaining"
                                        class="summary-total text-success"
                                    >
                                ₱{{ number_format($customer->credit->running_balance??0,2) }}
                            </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-lg-8">

                    <div class="card payment-card">

                        <div class="card-header-clean">

                            <h5>
                                <i class="bi bi-credit-card me-2"></i>
                                Payment Information
                            </h5>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Payment Date
                                    </label>

                                    <input
                                        type="date"
                                        name="payment_date"
                                        class="form-control"
                                        value="{{ old('payment_date',now()->format('Y-m-d')) }}"
                                        required
                                    >

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Payment Method
                                    </label>

                                    <select
                                        name="payment_method"
                                        class="form-select"
                                        required
                                    >
                                        <option value="cash">Cash</option>
                                        <option value="gcash">GCash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>

                                </div>

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Amount Received
                                </label>

                                <div class="input-group input-group-lg">

                            <span class="input-group-text">
                                ₱
                            </span>

                                    <input
                                        id="amount"
                                        name="amount"
                                        type="number"
                                        min="0.01"
                                        step="0.01"
                                        class="form-control payment-input @error('amount') is-invalid @enderror"
                                        value="{{ old('amount') }}"
                                        autocomplete="off"
                                        required
                                    >

                                </div>

                                @error('amount')

                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>

                                @enderror

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Quick Amount
                                </label>

                                <div class="d-flex flex-wrap gap-2">

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary quick-btn quick-payment"
                                        data-percent="25"
                                    >
                                        25%
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary quick-btn quick-payment"
                                        data-percent="50"
                                    >
                                        50%
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary quick-btn quick-payment"
                                        data-percent="75"
                                    >
                                        75%
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-success quick-payment"
                                        data-percent="100"
                                    >
                                        Full Balance
                                    </button>

                                </div>

                            </div>
                            <div class="row">

                                <div class="col-md-6 mb-4">

                                    <label class="form-label">
                                        Reference No.
                                    </label>

                                    <input
                                        type="text"
                                        name="reference_no"
                                        class="form-control"
                                        value="{{ old('reference_no') }}"
                                        placeholder="Optional reference number"
                                    >

                                </div>

                                <div class="col-md-6 mb-4">

                                    <label class="form-label">
                                        Collection Date & Time
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ now()->format('F d, Y h:i A') }}"
                                        readonly
                                    >

                                </div>

                            </div>

                            <div class="mb-4">

                                <label class="form-label">
                                    Remarks
                                </label>

                                <textarea
                                    name="remarks"
                                    rows="4"
                                    class="form-control"
                                    placeholder="Optional remarks..."
                                >{{ old('remarks') }}</textarea>

                            </div>

                            <hr class="my-4">

                            <div class="row">

                                <div class="col-lg-6">

                                    <div
                                        class="border rounded-4 p-3 bg-light h-100"
                                    >

                                        <div class="fw-semibold mb-3">
                                            Payment Summary
                                        </div>

                                        <div class="summary-item">

                                    <span>
                                        Outstanding Balance
                                    </span>

                                            <strong>
                                                ₱{{ number_format($customer->credit->running_balance??0,2) }}
                                            </strong>

                                        </div>

                                        <div class="summary-item">

                                    <span>
                                        Amount Received
                                    </span>

                                            <strong
                                                id="footerPayment"
                                            >
                                                ₱0.00
                                            </strong>

                                        </div>

                                        <div class="summary-item">

                                    <span class="summary-total">
                                        Remaining
                                    </span>

                                            <span
                                                id="footerRemaining"
                                                class="summary-total text-success"
                                            >
                                        ₱{{ number_format($customer->credit->running_balance??0,2) }}
                                    </span>

                                        </div>

                                    </div>

                                </div>

                                <div
                                    class="col-lg-6 d-flex justify-content-end align-items-end mt-4 mt-lg-0"
                                >

                                    <div
                                        class="d-flex gap-2"
                                    >

                                        <a
                                            href="{{ route('customers.collections.index') }}"
                                            class="btn btn-outline-secondary btn-lg px-4"
                                        >
                                            Cancel
                                        </a>

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-lg px-5"
                                        >
                                            <i class="bi bi-check-circle me-2"></i>

                                            Receive Payment
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

    <script>

        const currentBalance = Number({{ $customer->credit->running_balance??0 }});

        const amountInput = document.getElementById('amount');

        const remainingBalance = document.getElementById('remainingBalance');

        const summaryPayment = document.getElementById('summaryPayment');

        const summaryRemaining = document.getElementById('summaryRemaining');

        const footerPayment = document.getElementById('footerPayment');

        const footerRemaining = document.getElementById('footerRemaining');

        const paymentStatus = document.getElementById('paymentStatus');

        function peso(value){

            return '₱' + value.toLocaleString(
                undefined,
                {
                    minimumFractionDigits:2,
                    maximumFractionDigits:2
                }
            );

        }

        function updateSummary(){

            let payment = parseFloat(amountInput.value);

            if(isNaN(payment)){

                payment = 0;

            }

            let remaining = currentBalance - payment;

            if(remaining < 0){

                remaining = 0;

            }

            remainingBalance.innerHTML = peso(remaining);

            summaryPayment.innerHTML = peso(payment);

            footerPayment.innerHTML = peso(payment);

            summaryRemaining.innerHTML = peso(remaining);

            footerRemaining.innerHTML = peso(remaining);

            paymentStatus.className = 'badge badge-payment';

            if(payment <= 0){

                paymentStatus.classList.add(
                    'bg-secondary-subtle',
                    'text-secondary'
                );

                paymentStatus.innerHTML = 'Pending';

                return;

            }

            if(payment < currentBalance){

                paymentStatus.classList.add(
                    'bg-warning-subtle',
                    'text-warning'
                );

                paymentStatus.innerHTML = 'Partial Payment';

                return;

            }

            if(payment == currentBalance){

                paymentStatus.classList.add(
                    'bg-success-subtle',
                    'text-success'
                );

                paymentStatus.innerHTML = 'Full Payment';

                return;

            }

            paymentStatus.classList.add(
                'bg-danger-subtle',
                'text-danger'
            );

            paymentStatus.innerHTML = 'Overpayment';

        }

        amountInput.addEventListener(
            'input',
            updateSummary
        );

        document
            .querySelectorAll('.quick-payment')
            .forEach(function(button){

                button.addEventListener(
                    'click',
                    function(){

                        let percent = Number(
                            this.dataset.percent
                        );

                        let value = currentBalance * (percent / 100);

                        amountInput.value = value.toFixed(2);

                        updateSummary();

                        amountInput.focus();

                    }
                );

            });

        updateSummary();

    </script>

    @endif

@endsection
