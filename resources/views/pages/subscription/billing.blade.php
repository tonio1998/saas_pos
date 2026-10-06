@extends('layouts.app')

@section('title', 'Subscription & Billing History — ' . ($tenant->business_name ?? 'My Store'))

@section('content')
<div class="container-fluid py-3 px-3 px-md-4" style="max-width: 1200px; margin: 0 auto;">

    {{-- Breadcrumb & Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Subscription &amp; Billing History</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('subscription.checkout') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm" style="background:#059669; border-color:#059669;">
                <i class="bi bi-arrow-repeat me-1"></i> Renew or Upgrade Plan
            </a>
        </div>
    </div>

    {{-- ── Active Subscription Status Card ─────────────────── --}}
    @php
        $sub = $tenant->subscription;
        $isPaid = $tenant->isPaid();
        $isSuspended = $tenant->isSuspended();
    @endphp

    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 position-relative overflow-hidden" style="border-left: 5px solid {{ $isPaid ? '#059669' : ($isSuspended ? '#ef4444' : '#f59e0b') }} !important;">
        <div class="row align-items-center g-4">
            {{-- Left: Plan Badge & Name --}}
            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge {{ $isPaid ? 'bg-success' : ($isSuspended ? 'bg-danger' : 'bg-warning text-dark') }} fw-bold px-2.5 py-1 rounded-pill" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                        <i class="bi {{ $isPaid ? 'bi-patch-check-fill' : ($isSuspended ? 'bi-shield-x' : 'bi-alarm-fill') }} me-1"></i>
                        {{ $isPaid ? 'ACTIVE SUBSCRIBER' : ($isSuspended ? 'SUSPENDED / LOCKED' : 'PAYMENT DUE') }}
                    </span>
                    @if($sub?->is_promo)
                        <span class="badge bg-warning text-dark fw-bold rounded-pill extra-small">⚡ Promo Tier</span>
                    @endif
                </div>

                <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.7rem;">
                    {{ $sub?->name ?? 'Standard Retail Plan' }}
                </h3>
                <p class="text-muted small mb-3">
                    {{ $sub?->description ?? 'All-in-one POS cashiering, inventory tracking, and CRM ledger for retail stores.' }}
                </p>

                <div class="d-flex flex-wrap align-items-center gap-2">
                    <a href="{{ route('subscription.checkout') }}" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold">
                        <i class="bi bi-arrow-up-right-circle me-1"></i> Change / Renew Tier
                    </a>
                    @if($tenant->latestInvoice)
                        <a href="{{ route('subscription.billing.invoice', $tenant->latestInvoice->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold">
                            <i class="bi bi-receipt me-1"></i> Latest Invoice
                        </a>
                    @endif
                </div>
            </div>

            {{-- Middle: Hardware & Quota Capacities --}}
            <div class="col-lg-4 border-start-lg ps-lg-4">
                <div class="extra-small text-muted text-uppercase fw-bold mb-2" style="letter-spacing: 0.5px;">Hardware &amp; System Entitlements</div>
                
                <div class="d-flex flex-column gap-2 extra-small">
                    {{-- Terminals Capacity --}}
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                        <span><i class="bi bi-tablet-landscape-fill text-primary me-2"></i><strong>POS Terminals Capacity</strong></span>
                        <span class="badge bg-primary text-white rounded-pill px-2.5 py-1 fw-bold">
                            {{ $sub?->max_terminals ?? 1 }} Physical Terminal{{ ($sub?->max_terminals ?? 1) > 1 ? 's' : '' }}
                        </span>
                    </div>

                    {{-- Products SKU Cap --}}
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                        <span><i class="bi bi-box-seam-fill text-info me-2"></i><strong>Product Catalog Quota</strong></span>
                        <span class="fw-bold text-dark">
                            {{ number_format($currentProductsCount) }} / {{ number_format($sub?->max_products ?? 1000) }} SKUs
                        </span>
                    </div>

                    {{-- Cashier Accounts --}}
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 bg-light">
                        <span><i class="bi bi-people-fill text-warning me-2"></i><strong>Cashier Shift Accounts</strong></span>
                        <span class="fw-bold text-dark">{{ $sub?->max_cashier_accounts ?? 2 }} Logins ({{ $sub?->max_users ?? 3 }} Total)</span>
                    </div>
                </div>
            </div>

            {{-- Right: Due Date & Countdown --}}
            <div class="col-lg-3 text-lg-end">
                <div class="p-3 rounded-4 bg-light text-start text-lg-end">
                    <div class="extra-small text-muted text-uppercase fw-bold mb-1">Subscription Expiration</div>
                    <div class="fs-4 fw-extrabold text-dark font-mono mb-1">
                        {{ $tenant->subscription_end ? \Carbon\Carbon::parse($tenant->subscription_end)->format('M d, Y') : 'N/A' }}
                    </div>

                    @if($daysRemaining !== null)
                        @if($daysRemaining < 0)
                            <div class="badge bg-danger rounded-pill px-2.5 py-1 extra-small fw-bold">
                                <i class="bi bi-exclamation-octagon me-1"></i> Overdue by {{ abs($daysRemaining) }} Day(s)
                            </div>
                        @elseif($daysRemaining <= 7)
                            <div class="badge bg-warning text-dark rounded-pill px-2.5 py-1 extra-small fw-bold">
                                <i class="bi bi-alarm me-1"></i> Renews in {{ $daysRemaining }} Day(s)
                            </div>
                        @else
                            <div class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">
                                <i class="bi bi-check2-circle me-1"></i> {{ $daysRemaining }} Days Remaining
                            </div>
                        @endif
                    @endif

                    {{-- Progress Bar --}}
                    <div class="progress mt-3" style="height: 6px;">
                        <div class="progress-bar {{ $isPaid ? 'bg-success' : 'bg-warning' }}" role="progressbar" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Pending Verification Alert Box ──────────────────── --}}
    @if($pendingInvoice || $tenant->isPendingVerification())
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">Payment Verification in Progress</h5>
                    <p class="small text-muted mb-0">
                        We received your payment proof for <strong>{{ $pendingInvoice?->plan_name ?? $tenant->pendingPlan?->name ?? 'Subscription' }}</strong>
                        (Ref: <code>{{ $pendingInvoice?->payment_reference ?? $tenant->payment_reference }}</code>). 
                        Our team is reviewing it. Your account will automatically activate once confirmed.
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if($pendingInvoice)
                    <form action="{{ route('subscription.billing.cancel', $pendingInvoice->id) }}" method="POST" onsubmit="return confirm('Cancel this pending payment submission?');">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3">
                            Cancel &amp; Change Plan
                        </button>
                    </form>
                @endif
                <a href="tel:09128941731" class="btn btn-warning btn-sm rounded-pill fw-bold px-3 text-dark">
                    <i class="bi bi-telephone-fill me-1"></i> Fast Track: 0912 894 1731
                </a>
            </div>
        </div>
    @endif

    {{-- ── Subscription & Billing History Table ────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">Billing &amp; Subscription Invoices Ledger</h5>
                    <div class="extra-small text-muted">Complete historical record of all renewals, plan upgrades, and official receipts</div>
                </div>
            </div>

            <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill extra-small fw-bold">
                {{ $invoices->count() }} Invoice Record(s)
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                    <tr>
                        <th class="ps-4 py-3">Invoice # / Date</th>
                        <th class="py-3">Subscription Tier</th>
                        <th class="py-3">Terminals Limit</th>
                        <th class="py-3">Amount &amp; Method</th>
                        <th class="py-3">Covered Period</th>
                        <th class="py-3">Payment Status</th>
                        <th class="text-end pe-4 py-3">Official Statement</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                        @php $badge = $inv->status_badge; @endphp
                        <tr>
                            {{-- Invoice # & Date --}}
                            <td class="ps-4 py-3">
                                <a href="{{ route('subscription.billing.invoice', $inv->id) }}" class="fw-bold text-decoration-none text-dark font-mono" style="font-size: 0.9rem;">
                                    {{ $inv->invoice_no }}
                                </a>
                                <div class="extra-small text-muted mt-0.5">
                                    <i class="bi bi-calendar-event me-1"></i> {{ $inv->billing_date ? $inv->billing_date->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>

                            {{-- Subscription Tier --}}
                            <td class="py-3">
                                <span class="badge bg-light text-dark border px-2.5 py-1 rounded-pill fw-semibold">
                                    {{ $inv->plan_name }}
                                </span>
                                <div class="extra-small text-muted mt-0.5">
                                    {{ ucfirst($inv->billing_cycle) }} ({{ $inv->duration_days }} Days)
                                </div>
                            </td>

                            {{-- Hardware Terminals Limit (1 unit = 1 terminal) --}}
                            <td class="py-3">
                                <div class="extra-small fw-bold text-dark">
                                    <i class="bi bi-tablet-landscape-fill text-primary me-1"></i>
                                    {{ $inv->max_terminals }} Terminal{{ $inv->max_terminals > 1 ? 's' : '' }}
                                </div>
                                <div class="extra-small text-muted">
                                    Up to {{ number_format($inv->max_products) }} SKUs
                                </div>
                            </td>

                            {{-- Amount & Method --}}
                            <td class="py-3 font-mono">
                                <div class="fw-bold text-dark fs-6">₱{{ number_format($inv->net_amount, 2) }}</div>
                                <div class="extra-small text-muted mt-0.5">
                                    <span class="badge bg-light text-secondary border px-1.5 py-0.5">
                                        {{ $inv->payment_method_label }}
                                    </span>
                                    @if($inv->payment_reference)
                                        <span class="text-muted ms-1" title="Ref: {{ $inv->payment_reference }}">
                                            #{{ Str::limit($inv->payment_reference, 10) }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Covered Period --}}
                            <td class="py-3 extra-small">
                                @if($inv->period_start && $inv->period_end)
                                    <div class="fw-semibold text-dark">
                                        {{ $inv->period_start->format('M d, Y') }} &rarr;
                                    </div>
                                    <div class="text-muted">
                                        {{ $inv->period_end->format('M d, Y') }}
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">Pending Activation</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3">
                                <span class="badge {{ $badge['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold d-inline-flex align-items-center gap-1">
                                    <i class="bi {{ $badge['icon'] }}"></i>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                                @if($inv->paid_at)
                                    <div class="extra-small text-muted mt-0.5" style="font-size: 0.68rem;">
                                        Paid: {{ $inv->paid_at->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>

                            {{-- Printable Official Receipt Link --}}
                            <td class="text-end pe-4 py-3">
                                <a href="{{ route('subscription.billing.invoice', $inv->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold extra-small" title="View Official Receipt Slip">
                                    <i class="bi bi-printer-fill me-1"></i> View Receipt
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-receipt display-5 text-secondary"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No billing history yet</h6>
                                <p class="text-muted small mb-3">Your subscription invoices and renewal payment statements will be recorded here.</p>
                                <a href="{{ route('subscription.checkout') }}" class="btn btn-success btn-sm rounded-pill px-4 fw-bold">
                                    Select Subscription Plan
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── 24/7 Support Hotline Banner ─────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white text-center">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-8 text-md-start">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-warning text-dark fw-bold px-2 py-0.5 rounded-pill extra-small">24/7 SUPPORT</span>
                    <h5 class="fw-bold mb-0 text-white">Need help with your invoice or billing?</h5>
                </div>
                <p class="text-white-50 small mb-0">
                    Reach our technical concierge team anytime. We assist with custom multi-terminal packages, invoice BIR details, and payment verification.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="tel:09128941731" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">
                    <i class="bi bi-telephone-fill me-1"></i> Call 0912 894 1731
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
