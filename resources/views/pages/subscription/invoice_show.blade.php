@extends('layouts.app')

@section('title', 'Official Invoice #' . $invoice->invoice_no . ' — ' . ($invoice->tenant?->business_name ?? 'LikhaPOS'))

@section('content')
<div class="container-fluid py-3 px-4">

    {{-- Top Action Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-4 d-print-none" style="max-width: 860px; margin: 0 auto;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('subscription.billing') }}" class="text-decoration-none text-muted">Billing &amp; Invoices</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $invoice->invoice_no }}</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('subscription.billing') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Back to Billing History
            </a>
            <button type="button" class="btn btn-dark btn-sm rounded-pill px-3.5 fw-bold shadow-sm" onclick="window.print();">
                <i class="bi bi-printer-fill me-1"></i> Print / Save PDF
            </button>
        </div>
    </div>

    {{-- Printable Paper Statement --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mx-auto position-relative" style="max-width: 860px;">
        
        {{-- Status Watermark / Stamp --}}
        @php
            $isPaid = $invoice->isPaid();
        @endphp
        <div class="position-absolute top-0 end-0 p-4 d-print-inline">
            @if($isPaid)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 1px;">
                    <i class="bi bi-patch-check-fill me-1"></i> OFFICIAL PAID RECEIPT
                </span>
            @elseif($invoice->isPending())
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.85rem;">
                    <i class="bi bi-hourglass-split me-1"></i> PENDING VERIFICATION
                </span>
            @elseif($invoice->isRejected())
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.85rem;">
                    <i class="bi bi-x-circle-fill me-1"></i> REJECTED BILL
                </span>
            @else
                <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.85rem;">
                    <i class="bi bi-alarm-fill me-1"></i> PAYMENT OVERDUE
                </span>
            @endif
        </div>

        {{-- Top Header Brand --}}
        <div class="row align-items-center mb-4 pb-4 border-bottom">
            <div class="col-sm-7">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-dark text-white fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.75rem;">
                        LikhaPOS CLOUD
                    </span>
                    <span class="text-muted extra-small font-mono">SAAS POS PLATFORM</span>
                </div>
                <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.8rem;">
                    OFFICIAL BILLING STATEMENT
                </h3>
                <div class="extra-small text-muted font-mono">
                    Cloud Subscription &amp; POS Software Entitlement
                </div>
            </div>

            <div class="col-sm-5 text-sm-end mt-3 mt-sm-0">
                <div class="fw-bold font-mono text-dark fs-5">{{ $invoice->invoice_no }}</div>
                <div class="extra-small text-muted">Statement Date: {{ $invoice->billing_date ? $invoice->billing_date->format('F d, Y') : 'N/A' }}</div>
                <div class="extra-small text-muted">Due Date: {{ $invoice->due_date ? $invoice->due_date->format('F d, Y') : 'N/A' }}</div>
            </div>
        </div>

        {{-- Billed To & Merchant Information --}}
        <div class="row g-4 mb-4 pb-4 border-bottom">
            <div class="col-sm-6">
                <div class="extra-small text-muted text-uppercase fw-bold mb-2">Billed To:</div>
                <h5 class="fw-bold text-dark mb-1">{{ $invoice->tenant?->business_name ?? 'Store Account' }}</h5>
                <div class="small text-secondary mb-1">
                    <strong>Owner / Authorized Rep:</strong> {{ $invoice->tenant?->owner_name ?? 'Store Owner' }}
                </div>
                @if($invoice->tenant?->phone)
                    <div class="extra-small text-muted"><strong>Contact:</strong> {{ $invoice->tenant->phone }}</div>
                @endif
                @if($invoice->tenant?->address)
                    <div class="extra-small text-muted"><strong>Location:</strong> {{ $invoice->tenant->address }}</div>
                @endif
                @if($invoice->tenant?->tin)
                    <div class="extra-small text-muted"><strong>TIN:</strong> {{ $invoice->tenant->tin }}</div>
                @endif
            </div>

            <div class="col-sm-6 text-sm-end">
                <div class="extra-small text-muted text-uppercase fw-bold mb-2">Issued By:</div>
                <h6 class="fw-bold text-dark mb-1">LikhaPOS Retail Cloud Systems</h6>
                <div class="extra-small text-muted">Hotline Support: 0912-894-1731 (24/7)</div>
                <div class="extra-small text-muted">Email: billing@likhapos.ph</div>
                <div class="extra-small text-muted">Surigao City / Metro Manila, Philippines</div>
            </div>
        </div>

        {{-- Line Items Table --}}
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                    <tr>
                        <th class="ps-3 py-2.5">Subscription Plan / Service Description</th>
                        <th class="py-2.5 text-center">Duration</th>
                        <th class="py-2.5 text-center">POS Terminals</th>
                        <th class="py-2.5 text-end pe-3">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 py-3">
                            <div class="fw-bold text-dark fs-6">{{ $invoice->plan_name }}</div>
                            <div class="extra-small text-muted">
                                Recurrence: {{ ucfirst($invoice->billing_cycle) }}
                                • Inventory Catalog Limit: {{ number_format($invoice->max_products) }} SKUs
                            </div>
                            @if($invoice->period_start && $invoice->period_end)
                                <div class="extra-small text-success mt-1">
                                    <i class="bi bi-calendar-check me-1"></i> Validity Period: {{ $invoice->period_start->format('M d, Y') }} to {{ $invoice->period_end->format('M d, Y') }}
                                </div>
                            @endif
                        </td>
                        <td class="py-3 text-center font-mono">
                            {{ $invoice->duration_days }} Days
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold extra-small">
                                {{ $invoice->max_terminals }} POS Terminal{{ $invoice->max_terminals > 1 ? 's' : '' }}
                            </span>
                        </td>
                        <td class="py-3 text-end pe-3 font-mono fw-bold fs-6 text-dark">
                            ₱{{ number_format($invoice->net_amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Financial Summary & Payment Breakdown --}}
        <div class="row align-items-center mb-4 pb-4 border-bottom">
            <div class="col-sm-6">
                <div class="p-3 rounded-3 bg-light border">
                    <div class="extra-small text-uppercase fw-bold text-muted mb-1">Payment Method &amp; Reference</div>
                    <div class="fw-bold text-dark font-mono">{{ $invoice->payment_method_label }}</div>
                    @if($invoice->payment_reference)
                        <div class="extra-small text-secondary font-mono mt-0.5">Reference No: <strong>{{ $invoice->payment_reference }}</strong></div>
                    @endif
                    @if($invoice->payment_sender_name)
                        <div class="extra-small text-muted mt-0.5">Sender: {{ $invoice->payment_sender_name }} ({{ $invoice->payment_sender_phone }})</div>
                    @endif
                    @if($invoice->paid_at)
                        <div class="extra-small text-success mt-1">
                            <i class="bi bi-check-circle-fill me-1"></i> Paid On: {{ $invoice->paid_at->format('F d, Y h:i A') }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0 font-mono">
                <div class="d-flex justify-content-between justify-content-sm-end gap-4 extra-small text-muted mb-1">
                    <span>Gross Plan Price:</span>
                    <span>₱{{ number_format($invoice->amount, 2) }}</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="d-flex justify-content-between justify-content-sm-end gap-4 extra-small text-success mb-1">
                        <span>Promotional Discount:</span>
                        <span>-₱{{ number_format($invoice->discount_amount, 2) }}</span>
                    </div>
                @endif
                <div class="d-flex justify-content-between justify-content-sm-end gap-4 fs-4 fw-extrabold text-success border-top pt-2">
                    <span>Total Net Amount:</span>
                    <span>₱{{ number_format($invoice->net_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Footer Verification / Remarks --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 text-muted extra-small">
            <div>
                @if($invoice->verifier)
                    <div><strong>Verified &amp; Certified By:</strong> {{ $invoice->verifier->name }} (SuperAdmin)</div>
                @endif
                <div>For billing inquiries or BIR official receipts, contact support@likhapos.ph</div>
            </div>

            <div class="text-sm-end font-mono">
                <div>Document Hash: #{{ substr(md5($invoice->id . $invoice->invoice_no . $invoice->created_at), 0, 16) }}</div>
                <div>LikhaPOS Cloud Billing &copy; {{ date('Y') }}</div>
            </div>
        </div>

    </div>

</div>

<style>
@media print {
    body {
        background: #fff !important;
    }
    .d-print-none {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection
