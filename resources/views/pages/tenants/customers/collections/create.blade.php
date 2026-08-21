@extends('layouts.app')

@section('title', 'Receive / Settle Customer Payment | Collections')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('customers.credit.show', encryptId($customer->id)) }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-6"></i>
                </a>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Receive / Settle Customer Payment</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Record payment collection, calculate remaining balance, and post to the customer credit ledger.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('customers.credit.show', encryptId($customer->id)) }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-danger extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-book-half text-danger fs-6"></i>
                <span>Credit Ledger Statement</span>
            </a>

            <a href="{{ route('customers.collections.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-x-circle me-1"></i> Back to Collections
            </a>
        </div>
    </div>

    @if(($customer->credit->running_balance ?? 0) <= 0)
        <div class="row justify-content-center py-5">
            <div class="col-lg-6 col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width:70px;height:70px;">
                        <i class="bi bi-check2-circle fs-1 text-success"></i>
                    </div>
                    <h4 class="fw-black text-dark mb-2 font-mono">No Outstanding Balance</h4>
                    <p class="text-muted mb-4">
                        <strong class="text-dark">{{ $customer->CustomerName }}</strong> has zero outstanding balance. All previous credit transactions are fully settled.
                    </p>
                    <div class="d-inline-flex align-items-center gap-2 bg-success-subtle border border-success-subtle px-4 py-2 rounded-pill font-mono fw-bold text-success mx-auto mb-4" style="font-size:1rem;">
                        <i class="bi bi-shield-check"></i>
                        <span>Current Utang Balance: ₱0.00</span>
                    </div>
                    <div>
                        <a href="{{ route('customers.credit.show', encryptId($customer->id)) }}" class="btn btn-primary rounded-3 px-4 py-2 fw-bold" style="background:#2563eb;border-color:#2563eb;">
                            <i class="bi bi-arrow-left me-1"></i> View Statement Ledger
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('customers.collections.store') }}">
            @csrf
            <input type="hidden" name="customer_id" value="{{ encryptId($customer->id) }}">

            <div class="row g-4">
                {{-- Left Column: Customer & Balance Summary --}}
                <div class="col-lg-4">
                    {{-- Customer Profile Card --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        <div class="card-header bg-transparent border-bottom p-3.5 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="bi bi-person-badge fs-6"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-0">Customer Profile</h6>
                            </div>
                            <span class="badge bg-light text-muted border font-mono extra-small">CRM Account</span>
                        </div>

                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar-initials me-3" style="background:#059669;width:48px;height:48px;min-width:48px;flex-shrink:0;font-size:1rem;border-radius:12px;">
                                    {{ strtoupper(substr($customer->CustomerName, 0, 2)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <h5 class="fw-bold text-dark mb-1 text-truncate font-mono">{{ $customer->CustomerName }}</h5>
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <span class="badge bg-light text-dark border font-mono fw-bold" style="font-size:0.72rem;">
                                            {{ $customer->customer_code != '0' ? $customer->customer_code : getCustomerCode($customer->id) }}
                                        </span>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono fw-bold" style="font-size:0.72rem;">
                                            {{ ucfirst($customer->customer_type ?? 'regular') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Outstanding Balance Highlight Box --}}
                            <div class="p-4 rounded-4 text-white mb-4 shadow-sm" style="background:linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);">
                                <div class="extra-small text-uppercase fw-bold opacity-75 font-mono" style="letter-spacing:0.8px;">Total Outstanding Utang</div>
                                <div class="font-mono fw-black fs-2 mt-1" id="currentBalanceText">
                                    ₱{{ number_format($customer->credit->running_balance ?? 0, 2) }}
                                </div>
                                <div class="mt-2 opacity-75 extra-small d-flex align-items-center gap-1">
                                    <i class="bi bi-clock-history"></i>
                                    <span>Uncollected Credit Purchases</span>
                                </div>
                            </div>

                            {{-- Live Calculation Summary Box --}}
                            <div class="border rounded-4 p-3 bg-light font-mono">
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted small">Current Utang:</span>
                                    <span class="fw-bold text-dark">₱{{ number_format($customer->credit->running_balance ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted small">Amount Paying:</span>
                                    <span class="fw-bold text-success fs-6" id="summaryPayment">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                    <span class="text-muted small">Remaining Balance:</span>
                                    <span class="fw-bold text-danger fs-6" id="remainingBalance">₱{{ number_format($customer->credit->running_balance ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2.5">
                                    <span class="text-muted small">Settlement Status:</span>
                                    <span id="paymentStatus" class="badge bg-secondary-subtle text-secondary py-1 px-2.5 rounded-pill font-mono" style="font-size:0.75rem;">Pending</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Payment Form Details --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        <div class="card-header bg-transparent border-bottom p-3.5 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                    <i class="bi bi-wallet2 fs-6"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-0">Payment Transaction & Collection Details</h6>
                            </div>
                            <span class="badge bg-light text-muted border font-mono extra-small">Cashier Terminal</span>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                {{-- Payment Date --}}
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                                        Collection Date <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                                        <input type="date" name="payment_date" class="form-control border-start-0 font-mono" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                    </div>
                                </div>

                                {{-- Payment Method --}}
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                                        Payment Method <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-credit-card"></i></span>
                                        <select name="payment_method" class="form-select border-start-0" required>
                                            <option value="cash">💵 Cash Payment</option>
                                            <option value="gcash">📱 GCash e-Wallet</option>
                                            <option value="maya">🏦 Maya e-Wallet</option>
                                            <option value="bank_transfer">🏛️ Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment Amount (Large Hero Input) --}}
                            <div class="mb-4">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                                    Amount Received <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light font-mono fw-bold fs-4 px-3.5" style="font-weight:800 !important;">₱</span>
                                    <input id="amount" name="amount" type="text" inputmode="decimal" class="form-control font-mono text-dark @error('amount') is-invalid @enderror" style="font-weight:800 !important;font-size:1.65rem !important;" value="{{ old('amount') }}" placeholder="0.00" autocomplete="off" required autofocus>
                                </div>
                                @error('amount')
                                    <div class="text-danger extra-small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Quick Settlement Buttons --}}
                            <div class="mb-4">
                                <label class="form-label extra-small fw-bold text-muted text-uppercase mb-2">Quick Amount Preset</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-white border rounded-3 px-3 py-2 fw-bold extra-small shadow-xs hover-lift quick-payment" data-percent="25">
                                        25% (<strong class="fw-bold font-mono">₱{{ number_format(($customer->credit->running_balance ?? 0) * 0.25, 2) }}</strong>)
                                    </button>
                                    <button type="button" class="btn btn-white border rounded-3 px-3 py-2 fw-bold extra-small shadow-xs hover-lift quick-payment" data-percent="50">
                                        50% (<strong class="fw-bold font-mono">₱{{ number_format(($customer->credit->running_balance ?? 0) * 0.5, 2) }}</strong>)
                                    </button>
                                    <button type="button" class="btn btn-white border rounded-3 px-3 py-2 fw-bold extra-small shadow-xs hover-lift quick-payment" data-percent="75">
                                        75% (<strong class="fw-bold font-mono">₱{{ number_format(($customer->credit->running_balance ?? 0) * 0.75, 2) }}</strong>)
                                    </button>
                                    <button type="button" class="btn btn-success rounded-3 px-3.5 py-2 fw-bold extra-small shadow-sm hover-lift quick-payment" data-percent="100" style="background:#059669;border-color:#059669;color:#fff;">
                                        ⭐ Full Balance: <strong class="fw-bold font-mono">₱{{ number_format($customer->credit->running_balance ?? 0, 2) }}</strong>
                                    </button>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                {{-- Reference No --}}
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                                        Reference No. / Ref ID <span class="text-muted fw-normal">(Optional)</span>
                                    </label>
                                    <input type="text" name="reference_no" class="form-control font-mono" value="{{ old('reference_no') }}" placeholder="e.g. GCash Ref # 12345678">
                                </div>

                                {{-- Collector --}}
                                <div class="col-md-6">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Collecting Staff / Cashier</label>
                                    <input type="text" class="form-control bg-light font-mono text-muted" value="{{ auth()->user()->name }}" readonly>
                                </div>

                                {{-- Remarks --}}
                                <div class="col-12">
                                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">
                                        Collection Remarks / Notes <span class="text-muted fw-normal">(Optional)</span>
                                    </label>
                                    <textarea name="remarks" rows="2" class="form-control" placeholder="Add payment notes, or transaction remarks...">{{ old('remarks') }}</textarea>
                                </div>
                            </div>

                            {{-- Actions Footer --}}
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                                <a href="{{ route('customers.collections.index') }}" class="btn btn-light border rounded-3 px-4 py-2 fw-bold text-muted extra-small">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-success rounded-3 px-4.5 py-2.5 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;padding-left:1.75rem;padding-right:1.75rem;">
                                    <i class="bi bi-check-circle-fill fs-6"></i>
                                    <span>Confirm & Post Payment</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

</div>

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.06) !important;
}
.avatar-initials {
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #ffffff;
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script>
const currentBalance = Number({{ $customer->credit->running_balance ?? 0 }});
const amountInput = document.getElementById('amount');
const remainingBalance = document.getElementById('remainingBalance');
const summaryPayment = document.getElementById('summaryPayment');
const paymentStatus = document.getElementById('paymentStatus');

function formatCurrencyString(val) {
    if (!val && val !== 0) return '';
    let str = val.toString().replace(/[^0-9.]/g, '');
    let parts = str.split('.');
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    if (parts.length > 2) {
        parts = [parts[0], parts.slice(1).join('')];
    }
    return parts.join('.');
}

function parseRawNumber(val) {
    if (!val) return 0;
    let clean = val.toString().replace(/,/g, '');
    let num = parseFloat(clean);
    return isNaN(num) ? 0 : num;
}

function peso(val) {
    return '₱' + Number(val).toLocaleString(undefined, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function updateSummary() {
    if (!amountInput) return;
    let payment = parseRawNumber(amountInput.value);

    let remaining = currentBalance - payment;
    if (remaining < 0) remaining = 0;

    if (remainingBalance) remainingBalance.textContent = peso(remaining);
    if (summaryPayment) summaryPayment.textContent = peso(payment);

    if (paymentStatus) {
        paymentStatus.className = 'badge py-1 px-2.5 rounded-pill font-mono';
        if (payment <= 0) {
            paymentStatus.classList.add('bg-secondary-subtle', 'text-secondary');
            paymentStatus.textContent = 'Pending';
        } else if (payment < currentBalance) {
            paymentStatus.classList.add('bg-warning-subtle', 'text-warning');
            paymentStatus.textContent = 'Partial Payment';
        } else if (payment === currentBalance) {
            paymentStatus.classList.add('bg-success-subtle', 'text-success');
            paymentStatus.textContent = 'Full Settlement';
        } else {
            paymentStatus.classList.add('bg-danger-subtle', 'text-danger');
            paymentStatus.textContent = 'Overpayment';
        }
    }
}

if (amountInput) {
    // Real-time comma formatting on input
    amountInput.addEventListener('input', function (e) {
        let cursorPosition = this.selectionStart;
        let originalLength = this.value.length;

        let formatted = formatCurrencyString(this.value);
        this.value = formatted;

        // Restore sensible cursor position
        let newLength = this.value.length;
        cursorPosition = cursorPosition + (newLength - originalLength);
        this.setSelectionRange(cursorPosition, cursorPosition);

        updateSummary();
    });
}

// Quick Preset Click Handlers
document.querySelectorAll('.quick-payment').forEach(btn => {
    btn.addEventListener('click', function () {
        const percent = Number(this.getAttribute('data-percent'));
        const rawVal = currentBalance * (percent / 100);
        if (amountInput) {
            amountInput.value = formatCurrencyString(rawVal.toFixed(2));
            updateSummary();
            amountInput.focus();
        }
    });
});

// Strip commas before form submit so the server receives pure decimal
const collectionForm = amountInput ? amountInput.closest('form') : null;
if (collectionForm) {
    collectionForm.addEventListener('submit', function () {
        if (amountInput) {
            amountInput.value = parseRawNumber(amountInput.value).toFixed(2);
        }
    });
}

updateSummary();
</script>
@endpush
@endsection
