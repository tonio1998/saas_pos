@extends('layouts.sa')

@section('title', 'Platform Billing, Invoices & Revenue Ledger — SuperAdmin Console')

@section('content')
<div class="container-fluid py-2 px-3">

    {{-- ── Header ─────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-emerald-subtle text-emerald fw-bold px-2.5 py-1 rounded-pill" style="background:#ecfdf5; color:#059669; font-size: 0.72rem; letter-spacing: 0.5px;">
                    <i class="bi bi-receipt-cutoff me-1"></i> REVENUE &amp; BILLING SENTINEL
                </span>
                <span class="text-muted extra-small">Platform Invoicing, Payment Verification Ledger &amp; Subscription History</span>
            </div>
            <h3 class="fw-black text-dark font-mono mb-1" style="font-size: 1.6rem; letter-spacing: -0.02em;">
                Platform Billing &amp; Invoices Studio
            </h3>
            <p class="text-muted small mb-0">
                Track subscription revenues, audit past invoices, review submitted QRPH/GCash proofs, and issue manual billing statements.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </button>
            <button type="button" class="btn btn-primary btn-sm rounded-pill fw-bold px-3.5 shadow-sm d-flex align-items-center gap-1.5"
                    data-bs-toggle="modal" data-bs-target="#manualInvoiceModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Record Offline Bill / Payment</span>
            </button>
            <a href="{{ route('sa.subscriptions.plans') }}" class="btn btn-outline-success btn-sm rounded-pill fw-bold px-3">
                <i class="bi bi-tags-fill me-1"></i> Plans Studio
            </a>
        </div>
    </div>

    {{-- ── Financial & Invoicing KPI Cards ──────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Total Revenue Collected --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">All-Time Revenue</span>
                    <div class="rounded-circle bg-success-subtle text-success p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-cash-stack fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-dark font-mono">₱{{ number_format($totalCollected, 2) }}</div>
                <div class="extra-small text-muted mt-1">
                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>{{ number_format($paidCount) }}</span> paid invoice(s) settled
                </div>
            </div>
        </div>

        {{-- This Month's Inflow --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">{{ now()->format('F Y') }} Inflow</span>
                    <div class="rounded-circle bg-emerald-subtle text-emerald p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#ecfdf5;color:#059669;">
                        <i class="bi bi-calendar-check-fill fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-success font-mono">₱{{ number_format($thisMonthCollected, 2) }}</div>
                <div class="extra-small text-muted mt-1">Current calendar month collected</div>
            </div>
        </div>

        {{-- Pending Invoices --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">Pending Review</span>
                    <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-hourglass-split fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-warning font-mono">₱{{ number_format($pendingAmount, 2) }}</div>
                <div class="extra-small text-muted mt-1">
                    <span class="text-warning fw-bold">{{ $pendingCount }}</span> invoice(s) awaiting verification
                </div>
            </div>
        </div>

        {{-- Overdue / Uncollected --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small text-uppercase fw-bold">Overdue / Arrears</span>
                    <div class="rounded-circle bg-danger-subtle text-danger p-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-exclamation-octagon-fill fs-6"></i>
                    </div>
                </div>
                <div class="fs-3 fw-extrabold text-danger font-mono">{{ $overdueCount }}</div>
                <div class="extra-small text-muted mt-1">Store(s) past grace period / due date</div>
            </div>
        </div>
    </div>

    {{-- ── Filter Tabs & Search Controls ────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                {{-- Status Pills --}}
                <ul class="nav nav-pills gap-1">
                    <li class="nav-item">
                        <a href="{{ route('sa.subscriptions.billing', ['tab' => 'all', 'search' => $search]) }}" 
                           class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'all' ? 'active bg-dark text-white' : 'text-secondary' }}">
                            All Invoices ({{ $allCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sa.subscriptions.billing', ['tab' => 'paid', 'search' => $search]) }}" 
                           class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'paid' ? 'active bg-success text-white' : 'text-secondary' }}">
                            <i class="bi bi-check-circle-fill me-1"></i> Paid ({{ $paidCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sa.subscriptions.billing', ['tab' => 'pending', 'search' => $search]) }}" 
                           class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'pending' ? 'active bg-warning text-dark' : 'text-secondary' }}">
                            <i class="bi bi-hourglass-split me-1"></i> Pending Verification ({{ $pendingCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sa.subscriptions.billing', ['tab' => 'overdue', 'search' => $search]) }}" 
                           class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'overdue' ? 'active bg-danger text-white' : 'text-secondary' }}">
                            <i class="bi bi-alarm-fill me-1"></i> Overdue ({{ $overdueCount }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('sa.subscriptions.billing', ['tab' => 'rejected', 'search' => $search]) }}" 
                           class="nav-link rounded-pill px-3 py-1.5 fw-bold {{ $tab === 'rejected' ? 'active bg-secondary text-white' : 'text-secondary' }}">
                            <i class="bi bi-x-circle me-1"></i> Rejected ({{ $rejectedCount }})
                        </a>
                    </li>
                </ul>

                {{-- Search & Date Filters Form --}}
                <form action="{{ route('sa.subscriptions.billing') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search invoice, store, ref..." value="{{ $search }}">
                    </div>
                    <input type="date" name="date_from" class="form-control form-control-sm" style="width: 135px;" value="{{ $dateFrom }}" title="Filter from date">
                    <input type="date" name="date_to" class="form-control form-control-sm" style="width: 135px;" value="{{ $dateTo }}" title="Filter to date">
                    <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-bold">Filter</button>
                    @if(!empty($search) || !empty($dateFrom) || !empty($dateTo))
                        <a href="{{ route('sa.subscriptions.billing', ['tab' => $tab]) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5" title="Clear filters">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- ── Invoices Ledger Table ───────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                    <tr>
                        <th class="ps-4 py-3">Invoice # / Date</th>
                        <th class="py-3">Store / Merchant</th>
                        <th class="py-3">Subscription Tier</th>
                        <th class="py-3">Terminals &amp; SKUs</th>
                        <th class="py-3">Amount &amp; Method</th>
                        <th class="py-3">Coverage Period</th>
                        <th class="py-3">Payment Status</th>
                        <th class="text-end pe-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $inv)
                        @php
                            $badge = $inv->status_badge;
                        @endphp
                        <tr>
                            {{-- Invoice # & Date --}}
                            <td class="ps-4 py-3">
                                <a href="{{ route('sa.subscriptions.billing.invoice', $inv->id) }}" class="fw-bold text-decoration-none text-dark font-mono" style="font-size: 0.88rem;">
                                    {{ $inv->invoice_no }}
                                </a>
                                <div class="extra-small text-muted mt-0.5">
                                    <i class="bi bi-calendar3 me-1"></i> {{ $inv->billing_date ? $inv->billing_date->format('M d, Y') : 'N/A' }}
                                </div>
                            </td>

                            {{-- Store / Merchant --}}
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $inv->tenant?->business_name ?? 'Archived Store' }}</div>
                                <div class="extra-small text-muted">
                                    <span>{{ $inv->tenant?->owner_name ?? '' }}</span>
                                    @if($inv->tenant?->phone)
                                        <span class="mx-1">•</span><span>{{ $inv->tenant->phone }}</span>
                                    @endif
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

                            {{-- Terminals & SKUs --}}
                            <td class="py-3">
                                <div class="extra-small fw-bold text-dark">
                                    <i class="bi bi-tablet-landscape-fill text-primary me-1"></i>
                                    {{ $inv->max_terminals }} Terminal{{ $inv->max_terminals > 1 ? 's' : '' }}
                                </div>
                                <div class="extra-small text-muted">
                                    <i class="bi bi-box-seam text-info me-1"></i>
                                    {{ number_format($inv->max_products) }} SKUs
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

                            {{-- Coverage Period --}}
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

                            {{-- Payment Status Badge --}}
                            <td class="py-3">
                                <span class="badge {{ $badge['class'] }} rounded-pill px-2.5 py-1 extra-small fw-bold d-inline-flex align-items-center gap-1">
                                    <i class="bi {{ $badge['icon'] }}"></i>
                                    <span>{{ $badge['label'] }}</span>
                                </span>
                                @if($inv->paid_at)
                                    <div class="extra-small text-muted mt-0.5" style="font-size: 0.68rem;">
                                        Paid: {{ $inv->paid_at->format('M d, Y h:i A') }}
                                    </div>
                                @elseif($inv->due_date)
                                    <div class="extra-small text-muted mt-0.5" style="font-size: 0.68rem;">
                                        Due: {{ $inv->due_date->format('M d, Y') }}
                                    </div>
                                @endif
                            </td>

                            {{-- Action Controls --}}
                            <td class="text-end pe-4 py-3">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    {{-- Printable Invoice Link --}}
                                    <a href="{{ route('sa.subscriptions.billing.invoice', $inv->id) }}" class="btn btn-sm btn-outline-secondary rounded-3 px-2 py-1" title="View / Print Invoice Slip">
                                        <i class="bi bi-receipt"></i>
                                    </a>

                                    {{-- Store Billing History --}}
                                    @if($inv->tenant_id)
                                        <a href="{{ route('sa.subscriptions.billing.tenant', $inv->tenant_id) }}" class="btn btn-sm btn-outline-info rounded-3 px-2 py-1" title="View Store Complete History">
                                            <i class="bi bi-clock-history"></i>
                                        </a>
                                    @endif

                                    {{-- Quick Approve for Pending --}}
                                    @if($inv->payment_status === 'pending')
                                        <form action="{{ route('sa.subscriptions.billing.approve', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve Invoice #{{ $inv->invoice_no }} and activate store subscription?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success rounded-3 px-2 py-1" title="Approve Payment">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1" title="Reject Payment" onclick="openRejectModal({{ $inv->id }}, '{{ $inv->invoice_no }}');">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-receipt display-5 text-secondary"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No billing records found</h6>
                                <p class="text-muted small mb-3">There are no subscription invoices matching your current filter.</p>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#manualInvoiceModal">
                                    + Record First Manual Invoice
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <span class="small text-muted">Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} invoices</span>
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>

{{-- ── Modal: Record Manual Offline Payment / Bill ─────────── --}}
<div class="modal fade" id="manualInvoiceModal" tabindex="-1" aria-labelledby="manualInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="manualInvoiceModalLabel">Record Offline Payment / Bill</h5>
                        <div class="extra-small text-white-50">Generate invoice &amp; credit store subscription directly</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('sa.subscriptions.billing.manual') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Select Tenant Store <span class="text-danger">*</span></label>
                        <select name="tenant_id" class="form-select" required onchange="prefillStorePlan(this);">
                            <option value="">-- Choose Store --</option>
                            @foreach($tenantsList as $t)
                                <option value="{{ $t->id }}" data-sub="{{ $t->subscription_id }}">
                                    {{ $t->business_name }} ({{ $t->owner_name ?? 'Owner' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Subscription Plan <span class="text-danger">*</span></label>
                        <select name="subscription_id" id="manual_subscription_id" class="form-select" required onchange="updateManualAmount(this);">
                            <option value="">-- Choose Plan --</option>
                            @foreach($activePlans as $p)
                                <option value="{{ $p->id }}" data-price="{{ $p->effectivePrice() }}">
                                    {{ $p->name }} (₱{{ number_format($p->effectivePrice(), 2) }} • {{ $p->max_terminals }} Terminals)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Amount Paid (₱) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="amount" id="manual_amount" class="form-control font-mono fw-bold" placeholder="0.00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-dark">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Direct Cash</option>
                                <option value="bank_transfer">Bank Transfer (BDO/BPI)</option>
                                <option value="gcash">GCash Mobile</option>
                                <option value="maya">Maya Wallet</option>
                                <option value="manual_sa">SuperAdmin Ledger</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Bank / Receipt Reference # (Optional)</label>
                        <input type="text" name="payment_reference" class="form-control font-mono" placeholder="e.g. BDO-DEP-98124">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Internal Billing Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Paid in cash at office / Annual upfront agreement"></textarea>
                    </div>

                    <div class="form-check form-switch p-3 bg-white rounded-3 border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="auto_activate" id="manual_auto_activate" value="1" checked>
                        <label class="form-check-label small fw-bold text-success" for="manual_auto_activate">
                            Activate Subscription Immediately &amp; Mark as Paid
                        </label>
                        <div class="extra-small text-muted mt-1 ps-4">
                            If checked, the store's due date sentinel will automatically extend and the invoice will be marked as settled.
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-white border-top py-2.5 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Save &amp; Record Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ── Modal: Reject Payment Reason ────────────────────────── --}}
<div class="modal fade" id="rejectInvoiceModal" tabindex="-1" aria-labelledby="rejectInvoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-danger text-white py-2.5 px-3">
                <h6 class="modal-title fw-bold mb-0" id="rejectInvoiceModalLabel">Reject Payment Submission</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectInvoiceForm" method="POST">
                @csrf
                <div class="modal-body p-3">
                    <p class="small text-muted mb-2">
                        Specify reason for rejecting <strong id="rejectInvoiceNoText"></strong>:
                    </p>
                    <textarea name="rejection_reason" class="form-control form-control-sm" rows="3" required placeholder="e.g. Invalid reference number, screenshot blurred, amount mismatch..."></textarea>
                </div>
                <div class="modal-footer bg-light py-2 px-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill fw-bold">Reject Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function prefillStorePlan(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const subId = option.getAttribute('data-sub');
        if (subId) {
            const planSelect = document.getElementById('manual_subscription_id');
            planSelect.value = subId;
            updateManualAmount(planSelect);
        }
    }

    function updateManualAmount(planSelect) {
        const option = planSelect.options[planSelect.selectedIndex];
        const price = option.getAttribute('data-price');
        if (price) {
            document.getElementById('manual_amount').value = price;
        }
    }

    function openRejectModal(invoiceId, invoiceNo) {
        document.getElementById('rejectInvoiceNoText').innerText = invoiceNo;
        document.getElementById('rejectInvoiceForm').action = '/sa/subscriptions/billing/' + invoiceId + '/reject';
        new bootstrap.Modal(document.getElementById('rejectInvoiceModal')).show();
    }
</script>
@endpush
@endsection
