@extends('layouts.sa')

@section('title', 'Subscription History: ' . $tenant->business_name . ' — SuperAdmin')

@section('content')
<div class="container-fluid py-3 px-4">

    {{-- Breadcrumb & Back button --}}
    <div class="d-flex align-items-center justify-content-between mb-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('sa.dashboard.index') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sa.subscriptions.billing') }}" class="text-decoration-none text-muted">Billing &amp; Invoices</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">Store History: {{ $tenant->business_name }}</li>
            </ol>
        </nav>

        <a href="{{ route('sa.subscriptions.billing') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Back to Platform Ledger
        </a>
    </div>

    {{-- Store Profile Summary Card --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                    <i class="bi bi-shop fs-4"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-0.5">
                        <h4 class="fw-black text-dark mb-0">{{ $tenant->business_name }}</h4>
                        @if($tenant->status === 'active')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small fw-bold">Active Store</span>
                        @elseif($tenant->status === 'locked' || $tenant->status === 'suspended')
                            <span class="badge bg-danger rounded-pill extra-small fw-bold">Suspended</span>
                        @else
                            <span class="badge bg-secondary rounded-pill extra-small fw-bold">{{ ucfirst($tenant->status) }}</span>
                        @endif
                    </div>
                    <div class="text-muted small">
                        Owner: <strong>{{ $tenant->owner_name }}</strong> • Contact: {{ $tenant->phone ?? 'N/A' }} • Email: {{ $tenant->email ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="text-end">
                    <div class="extra-small text-muted text-uppercase fw-bold">Active Plan &amp; Expiry</div>
                    <div class="fw-bold text-dark fs-6">{{ $tenant->subscription?->name ?? 'No Plan' }}</div>
                    <div class="extra-small text-muted">
                        Expires: <strong>{{ $tenant->subscription_end ? \Carbon\Carbon::parse($tenant->subscription_end)->format('M d, Y') : 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Billing & Subscription History Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between">
            <h5 class="fw-bold text-dark mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>Subscription &amp; Payment History ({{ $invoices->count() }} Records)
            </h5>
            <span class="text-muted extra-small font-mono">Store ID: #{{ $tenant->id }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                    <tr>
                        <th class="ps-4 py-3">Invoice # / Date</th>
                        <th class="py-3">Plan Tier</th>
                        <th class="py-3">Terminals Cap</th>
                        <th class="py-3">Amount &amp; Method</th>
                        <th class="py-3">Coverage Period</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Verified By</th>
                        <th class="text-end pe-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                        @php $badge = $inv->status_badge; @endphp
                        <tr>
                            <td class="ps-4 py-3">
                                <a href="{{ route('sa.subscriptions.billing.invoice', $inv->id) }}" class="fw-bold text-decoration-none text-dark font-mono">
                                    {{ $inv->invoice_no }}
                                </a>
                                <div class="extra-small text-muted mt-0.5">
                                    {{ $inv->billing_date ? $inv->billing_date->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>

                            <td class="py-3">
                                <span class="badge bg-light text-dark border px-2 py-0.5 rounded-pill fw-semibold">
                                    {{ $inv->plan_name }}
                                </span>
                            </td>

                            <td class="py-3 extra-small fw-bold text-dark">
                                <i class="bi bi-tablet-landscape-fill text-primary me-1"></i>
                                {{ $inv->max_terminals }} Terminal{{ $inv->max_terminals > 1 ? 's' : '' }}
                            </td>

                            <td class="py-3 font-mono">
                                <div class="fw-bold text-dark">₱{{ number_format($inv->net_amount, 2) }}</div>
                                <div class="extra-small text-muted">{{ $inv->payment_method_label }}</div>
                            </td>

                            <td class="py-3 extra-small">
                                @if($inv->period_start && $inv->period_end)
                                    <div>{{ $inv->period_start->format('M d, Y') }} &rarr; {{ $inv->period_end->format('M d, Y') }}</div>
                                @else
                                    <span class="text-muted fst-italic">N/A</span>
                                @endif
                            </td>

                            <td class="py-3">
                                <span class="badge {{ $badge['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold">
                                    <i class="bi {{ $badge['icon'] }} me-1"></i>{{ $badge['label'] }}
                                </span>
                            </td>

                            <td class="py-3 extra-small text-muted">
                                {{ $inv->verifier?->name ?? 'System' }}
                            </td>

                            <td class="text-end pe-4 py-3">
                                <a href="{{ route('sa.subscriptions.billing.invoice', $inv->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" title="Print Invoice Statement">
                                    <i class="bi bi-receipt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No billing records found for this store.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
