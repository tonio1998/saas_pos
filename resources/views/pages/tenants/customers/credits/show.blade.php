@extends('layouts.app')

@section('title', 'Customer Credit Statement - ' . $customer->CustomerName)

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('customers.credit.index') }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-6"></i>
                </a>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Customer Credit Statement & Ledger</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Complete audit trail of sales on credit, partial settlements, and running account balances.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('customers.collections.create', [encryptId($customer->id)]) }}" class="btn btn-success rounded-3 px-3.5 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                <i class="bi bi-wallet2 fs-6"></i>
                <span>Receive / Settle Payment</span>
            </a>

            <a href="{{ route('customers.edit', encryptId($customer->id)) }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-pencil me-1"></i> Edit Profile
            </a>
        </div>
    </div>

    {{-- Customer Account Overview Hero Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-center">
                {{-- Customer Profile --}}
                <div class="col-xl-4 col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="avatar-initials me-3" style="background:#dc2626;width:52px;height:52px;min-width:52px;flex-shrink:0;font-size:1.15rem;border-radius:14px;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:900;">
                            {{ strtoupper(substr($customer->CustomerName, 0, 2)) }}
                        </div>
                        <div class="overflow-hidden">
                            <h5 class="fw-black text-dark mb-1 text-truncate font-mono" style="font-size:1.1rem;">{{ $customer->CustomerName }}</h5>
                            <div class="d-flex align-items-center gap-2 extra-small text-muted font-mono">
                                <span class="badge bg-light text-dark border font-mono fw-bold">{{ $customer->customer_code != '0' ? $customer->customer_code : getCustomerCode($customer->id) }}</span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono fw-bold">{{ ucfirst($customer->customer_type ?? 'regular') }}</span>
                                @if($customer->mobile_number)
                                    <span><i class="bi bi-telephone text-primary me-0.5"></i>{{ $customer->mobile_number }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Outstanding Balance --}}
                <div class="col-xl-3 col-md-3 col-6">
                    <div class="p-3 rounded-4 bg-danger-subtle border border-danger-subtle text-danger">
                        <div class="extra-small text-uppercase fw-bold font-mono" style="font-size:0.65rem;letter-spacing:0.5px;">Outstanding Utang Balance</div>
                        <div class="font-mono fw-black fs-3 mt-0.5" style="font-weight:900 !important;">
                            ₱{{ number_format(optional($customer->credit)->running_balance ?? 0, 2) }}
                        </div>
                    </div>
                </div>

                {{-- Credit Limit --}}
                <div class="col-xl-3 col-md-3 col-6">
                    <div class="p-3 rounded-4 bg-light border">
                        <div class="extra-small text-muted text-uppercase fw-bold font-mono" style="font-size:0.65rem;letter-spacing:0.5px;">Approved Credit Limit</div>
                        <div class="font-mono fw-black text-dark fs-4 mt-0.5" style="font-weight:800 !important;">
                            ₱{{ number_format($customer->credit_limit ?? 0, 2) }}
                        </div>
                    </div>
                </div>

                {{-- Quick Settle Action --}}
                <div class="col-xl-2 col-md-12 text-xl-end">
                    <a href="{{ route('customers.collections.create', [encryptId($customer->id)]) }}" class="btn btn-success w-100 rounded-3 py-2.5 fw-bold shadow-sm hover-lift d-flex align-items-center justify-content-center gap-1.5" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                        <i class="bi bi-wallet2 fs-6"></i>
                        <span>Pay Utang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Standard Ledger DataTable Card --}}
    <x-card>
        <x-datatable
            id="customerLedgerTable"
            :columns="[
                '#',
                'Date',
                'Reference / Sale Code',
                'Transaction Type',
                'Debit (Charges)',
                'Credit (Payments)',
                'Running Balance',
                'Remarks'
            ]"
            :pageLength="25"
            :ajax="route('customers.credit.ledger.data', [
                'CustomerID' => encryptId($customer->id)
            ])"
            :datatableColumns="[
                ['data' => 'DT_RowIndex', 'searchable' => false],
                ['data' => 'date'],
                ['data' => 'reference_no'],
                ['data' => 'transaction_type'],
                ['data' => 'debit', 'className' => 'text-end'],
                ['data' => 'credit', 'className' => 'text-end'],
                ['data' => 'running_balance', 'className' => 'text-end fw-black font-mono'],
                ['data' => 'remarks']
            ]"
        />
    </x-card>

    {{-- Sale Details Modal --}}
    <div class="modal fade" id="saleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header border-bottom p-3.5">
                    <h5 class="modal-title font-mono fw-bold">Sale Transaction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4" id="saleDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
@endsection
