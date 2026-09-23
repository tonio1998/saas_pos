@extends('layouts.app')

@section('title', 'Sales History & Revenue Audit Log | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-receipt-cutoff fs-5"></i>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Sales History & Revenue Audit Log</h4>
                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1 extra-small fw-bold">
                        <i class="bi bi-shield-check me-1"></i>Audit Ledger
                    </span>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Real-time ledger of completed checkout transactions, gross revenue, product cost deductions, and net profit margins.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cashiering.cash-shifts.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Cash Shifts</span>
            </a>

            <a href="{{ route('terminal.index') }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-calculator-fill fs-6"></i>
                <span>Open POS Terminal</span>
            </a>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- 1. Sales Today --}}
        <div class="col-6 col-lg-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Today's Revenue</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-cash-stack fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">₱{{ number_format($salesToday ?? 0, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold d-flex align-items-center gap-1">
                    <i class="bi bi-check-circle-fill text-success" style="font-size:0.7rem;"></i> Gross Completed Sales
                </div>
            </div>
        </div>

        {{-- 2. Net Profit Today --}}
        <div class="col-6 col-lg-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Today's Net Profit</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-graph-up-arrow fs-6"></i></div>
                </div>
                <div class="d-flex align-items-baseline gap-2 flex-wrap">
                    <div class="kpi-value font-mono fs-4 fw-black {{ ($profitToday ?? 0) >= 0 ? 'text-dark' : 'text-danger' }} mb-0">
                        ₱{{ number_format($profitToday ?? 0, 2) }}
                    </div>
                    <span class="badge rounded-pill px-2 py-0.5 fw-bold {{ ($profitMarginToday ?? 0) >= 20 ? 'bg-success text-white' : (($profitMarginToday ?? 0) >= 5 ? 'bg-warning-subtle text-warning-emphasis' : 'bg-danger text-white') }}" style="font-size:0.68rem;">
                        {{ ($profitToday ?? 0) >= 0 ? '▲' : '▼' }} {{ number_format($profitMarginToday ?? 0, 1) }}% margin
                    </span>
                </div>
                <div class="text-muted extra-small mt-1 fw-semibold">Net profit after cost deduction</div>
            </div>
        </div>

        {{-- 3. Transactions Count Today --}}
        <div class="col-6 col-lg-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Orders Processed</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-bag-check-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-primary mb-0">{{ number_format($transactionsToday ?? 0) }} <span class="fs-6 text-muted fw-normal">receipts</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Completed checkout orders</div>
            </div>
        </div>

        {{-- 4. Average Ticket Sale --}}
        <div class="col-6 col-lg-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Average Basket</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-calculator fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">₱{{ number_format($averageSale ?? 0, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Avg revenue per customer</div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar Card --}}
    <div class="card border rounded-4 shadow-xs bg-white mb-3">
        <div class="card-body p-3">
            <div class="row g-2.5 align-items-center">

                {{-- Date Presets Quick Pills --}}
                <div class="col-12 col-xl-5">
                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                        <span class="extra-small text-muted fw-bold text-uppercase me-1"><i class="bi bi-calendar-event me-1"></i>Date:</span>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 btn-date-preset active" data-preset="all">All Time</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 btn-date-preset" data-preset="today">Today</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 btn-date-preset" data-preset="yesterday">Yesterday</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 btn-date-preset" data-preset="this_week">This Week</button>
                        <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2.5 py-1 btn-date-preset" data-preset="this_month">This Month</button>
                    </div>
                </div>

                {{-- Custom Date Range --}}
                <div class="col-6 col-md-3 col-xl-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0 extra-small">From</span>
                        <input type="date" name="date_from" id="filterDateFrom" class="form-control form-control-sm rounded-end-3 font-mono datatable-external-filter" placeholder="Start Date">
                    </div>
                </div>

                <div class="col-6 col-md-3 col-xl-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0 extra-small">To</span>
                        <input type="date" name="date_to" id="filterDateTo" class="form-control form-control-sm rounded-end-3 font-mono datatable-external-filter" placeholder="End Date">
                    </div>
                </div>

                {{-- Payment Method Filter --}}
                <div class="col-6 col-md-3 col-xl-1.5" style="min-width:130px;">
                    <select name="payment_method" id="filterPaymentMethod" class="form-select form-select-sm rounded-3 datatable-external-filter">
                        <option value="">All Payment</option>
                        <option value="cash">Cash</option>
                        <option value="gcash">GCash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="utang">Utang / Credit</option>
                    </select>
                </div>

                {{-- Cashier Filter --}}
                <div class="col-6 col-md-3 col-xl-1.5" style="min-width:130px;">
                    <select name="cashier_id" id="filterCashier" class="form-select form-select-sm rounded-3 datatable-external-filter">
                        <option value="">All Cashiers</option>
                        @if(isset($cashiers) && $cashiers->count() > 0)
                            @foreach($cashiers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Reset Button --}}
                <div class="col-auto">
                    <button type="button" id="btnResetFilters" class="btn btn-sm btn-light border rounded-3 px-2.5 py-1 text-muted d-flex align-items-center gap-1 hover-scale" title="Clear all filters">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="extra-small fw-semibold">Reset</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Sales History Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-2 p-1.5 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                    <i class="bi bi-receipt fs-6"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0 fs-6">Completed Sales Ledger</h5>
                    <span class="text-muted extra-small">Click any Receipt # or View button to inspect item costs, discounts, and net profit</span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnReloadTable" class="btn btn-sm btn-white border rounded-3 px-2.5 py-1.5 fw-semibold text-muted extra-small shadow-2xs hover-scale d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Refresh Data</span>
                </button>
            </div>
        </div>

        <div class="card-body p-3">
            <x-datatable
                id="salesTable"
                :columns="[
                    'Actions',
                    'Receipt #',
                    'Date & Time',
                    'Customer',
                    'Items',
                    'Subtotal',
                    'Discount',
                    'Total Sales',
                    'Net Profit',
                    'Payment',
                    'Tendered / Change',
                    'Cashier'
                ]"
                :ajax="route('sales.data')"
                :datatableColumns="[
                    ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'className' => 'text-center align-middle'],
                    ['data' => 'invoice_number', 'className' => 'align-middle'],
                    ['data' => 'sale_date', 'className' => 'align-middle'],
                    ['data' => 'customer', 'className' => 'align-middle'],
                    ['data' => 'total_items', 'className' => 'align-middle'],
                    ['data' => 'subtotal', 'className' => 'align-middle font-mono'],
                    ['data' => 'discount', 'className' => 'align-middle'],
                    ['data' => 'total', 'className' => 'align-middle font-mono'],
                    ['data' => 'profit', 'className' => 'align-middle font-mono'],
                    ['data' => 'payment_method', 'className' => 'align-middle'],
                    ['data' => 'tendered', 'className' => 'align-middle font-mono'],
                    ['data' => 'cashier', 'className' => 'align-middle']
                ]"
            />
        </div>
    </div>
</div>

{{-- ============================================================
     COMPREHENSIVE TRANSACTION DETAILS MODAL
     ============================================================ --}}
<div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            {{-- Header --}}
            <div class="modal-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="background:#ede9fe;color:#7c3aed;width:38px;height:38px;">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-6">Transaction Audit Details</h5>
                            <span id="detailStatusBadge" class="badge rounded-pill px-2.5 py-0.5 fw-bold extra-small"></span>
                        </div>
                        <small class="font-mono text-muted fw-semibold" id="detailInvoice"></small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2.5 ms-auto">
                    <a href="javascript:void(0)" id="btnModalPrintReceipt" target="_blank" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs d-flex align-items-center gap-1.5 text-dark">
                        <i class="bi bi-printer-fill text-success"></i>
                        <span>Print Receipt</span>
                    </a>
                    <button type="button" class="btn-close shadow-none ms-1" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <div class="modal-body p-0">

                {{-- ── Section 1: Meta Info ── --}}
                <div class="p-4 border-bottom" style="background:#f8fafc;">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-white border h-100 shadow-2xs">
                                <div class="text-muted extra-small fw-extrabold text-uppercase mb-1">Customer</div>
                                <div class="fw-bold text-dark small" id="detailCustomer"></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-white border h-100 shadow-2xs">
                                <div class="text-muted extra-small fw-extrabold text-uppercase mb-1">Cashier</div>
                                <div class="fw-bold text-dark small" id="detailCashier"></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-white border h-100 shadow-2xs">
                                <div class="text-muted extra-small fw-extrabold text-uppercase mb-1">Payment Method</div>
                                <div class="fw-bold text-dark small" id="detailPaymentMethod"></div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-3 bg-white border h-100 shadow-2xs">
                                <div class="text-muted extra-small fw-extrabold text-uppercase mb-1">Date & Time</div>
                                <div class="fw-bold text-dark small font-mono" id="detailDate"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Transaction Narrative ── --}}
                <div class="px-4 pt-4 pb-0" id="detailNarrativeWrap">
                    <div id="detailNarrative" class="rounded-3 px-4 py-3 border d-flex align-items-start gap-3" style="background:linear-gradient(135deg,#eff6ff 0%,#dbeafe 100%);border-color:#bfdbfe!important;">
                        <i class="bi bi-lightbulb-fill mt-1 flex-shrink-0" style="color:#2563eb;font-size:1.1rem;"></i>
                        <div id="detailNarrativeText" class="small text-dark" style="line-height:1.65;"></div>
                    </div>
                </div>

                {{-- ── Section 2: Purchased Items (full profit breakdown) ── --}}
                <div class="p-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-bag-check text-indigo" style="color:#6366f1;"></i>
                            Line Items &amp; Profit Breakdown
                        </h6>
                        <small class="text-muted extra-small">Item cost deducted from effective selling price</small>
                    </div>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size:.82rem;">
                            <thead style="background:#f1f5f9;">
                                <tr>
                                    <th class="ps-3 fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;letter-spacing:.04em;">Item / Variant</th>
                                    <th class="text-center fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Qty</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Cost/Unit</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Effective Price</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Line Total</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Net Profit</th>
                                    <th class="text-center fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Margin %</th>
                                </tr>
                            </thead>
                            <tbody id="detailItemsBody"></tbody>
                        </table>
                    </div>
                    <div class="text-muted mt-2 d-flex align-items-center gap-1.5 extra-small">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span><strong>Effective Price:</strong> unit price after promotions or wholesale discounts applied. <strong>Net Profit:</strong> Line Total minus total cost of goods.</span>
                    </div>
                </div>

                {{-- ── Section 3: Financial Summary + Profit Panel ── --}}
                <div class="p-4 border-bottom">
                    <div class="row g-3">

                        {{-- Sale Summary --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-calculator" style="color:#0ea5e9;"></i> Sale Summary
                            </h6>
                            <div class="p-3.5 rounded-3 border bg-white shadow-2xs">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Subtotal (before discount)</span>
                                    <span id="detailSubtotal" class="font-mono fw-semibold text-dark">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small d-flex align-items-center gap-1">
                                        Discount Applied
                                        <span id="detailDiscountBadge" class="badge rounded-pill px-2" style="font-size:.65rem;background:#fee2e2;color:#dc2626;display:none!important;"></span>
                                    </span>
                                    <span id="detailDiscount" class="font-mono fw-semibold text-danger">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 pt-2 border-top">
                                    <span class="fw-black text-dark small">Total Amount Due</span>
                                    <span id="detailTotal" class="font-mono fw-black text-dark fs-6">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Tendered</span>
                                    <span id="detailTendered" class="font-mono fw-semibold text-dark">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted small">Change Returned</span>
                                    <span id="detailChange" class="font-mono fw-semibold text-info">₱0.00</span>
                                </div>
                            </div>
                        </div>

                        {{-- Profit Summary --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-graph-up-arrow" style="color:#10b981;"></i> Profit Breakdown
                            </h6>
                            <div class="p-3.5 rounded-3 border shadow-2xs" style="background:linear-gradient(135deg,#f0fdf4 0%,#dcfce7 100%);">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Net Revenue</span>
                                    <span id="profitRevenue" class="font-mono fw-semibold text-dark">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Cost of Goods</span>
                                    <span id="profitCost" class="font-mono fw-semibold text-danger">₱0.00</span>
                                </div>
                                <div class="d-flex justify-content-between pt-2 border-top mb-3">
                                    <span class="fw-black text-dark small">Net Earnings / Profit</span>
                                    <span id="profitNet" class="font-mono fw-black fs-6" style="color:#16a34a;">₱0.00</span>
                                </div>
                                {{-- Profit margin bar --}}
                                <div class="mb-1 d-flex justify-content-between align-items-center">
                                    <small class="text-muted fw-bold extra-small text-uppercase">Profit Margin</small>
                                    <small id="profitMarginPct" class="fw-bold font-mono" style="color:#16a34a;">0%</small>
                                </div>
                                <div class="progress rounded-pill" style="height:8px;background:rgba(0,0,0,0.06);">
                                    <div id="profitMarginBar" class="progress-bar rounded-pill" role="progressbar" style="width:0%;background:linear-gradient(90deg,#10b981,#059669);" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Section 4: Payment Records ── --}}
                <div class="p-4" id="detailPaymentsSection">
                    <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card" style="color:#8b5cf6;"></i> Payment Ledger Records
                    </h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size:.82rem;">
                            <thead style="background:#f1f5f9;">
                                <tr>
                                    <th class="ps-3 fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Method</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Amount Paid</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Tendered</th>
                                    <th class="text-end fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Change</th>
                                    <th class="fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Reference No.</th>
                                    <th class="fw-extrabold text-uppercase" style="font-size:.7rem;color:#64748b;">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody id="detailPaymentsBody"></tbody>
                        </table>
                    </div>
                </div>

            </div>{{-- /modal-body --}}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSalesHistory($) {

        // ── Date Presets logic ──
        $('.btn-date-preset').on('click', function () {
            $('.btn-date-preset').removeClass('active');
            $(this).addClass('active');

            const preset = $(this).data('preset');
            const today = new Date();
            const formatDate = (d) => d.toISOString().split('T')[0];

            if (preset === 'today') {
                const todayStr = formatDate(today);
                $('#filterDateFrom').val(todayStr);
                $('#filterDateTo').val(todayStr);
            } else if (preset === 'yesterday') {
                const yest = new Date(today);
                yest.setDate(yest.getDate() - 1);
                const yestStr = formatDate(yest);
                $('#filterDateFrom').val(yestStr);
                $('#filterDateTo').val(yestStr);
            } else if (preset === 'this_week') {
                const day = today.getDay();
                const diff = today.getDate() - day + (day === 0 ? -6 : 1); // Monday start
                const startOfWeek = new Date(today.setDate(diff));
                $('#filterDateFrom').val(formatDate(startOfWeek));
                $('#filterDateTo').val(formatDate(new Date()));
            } else if (preset === 'this_month') {
                const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                $('#filterDateFrom').val(formatDate(startOfMonth));
                $('#filterDateTo').val(formatDate(new Date()));
            } else {
                // All time
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
            }

            reloadTable();
        });

        $('#btnResetFilters').on('click', function () {
            $('.btn-date-preset').removeClass('active');
            $('.btn-date-preset[data-preset="all"]').addClass('active');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            $('#filterPaymentMethod').val('');
            $('#filterCashier').val('');
            reloadTable();
        });

        $('#btnReloadTable').on('click', function () {
            reloadTable();
        });

        function reloadTable() {
            const dt = $('#salesTable').DataTable();
            if (dt) {
                dt.ajax.reload(null, false);
            }
        }

        // ── Transaction Details Modal ──
        $(document).on('click', '.btn-view-sale', function () {
            const saleId = $(this).data('id');
            if (!saleId) return;

            // Set print receipt button target URL
            $('#btnModalPrintReceipt').attr('href', `/sales/${saleId}/bir-receipt`);

            $.getJSON(`/sales/${saleId}/details`, function (data) {

                // ── Header ──
                $('#detailInvoice').text(data.invoice_no ?? '');

                const rawStatus = (data.status ?? 'completed').toLowerCase().replace(/\s+/g, '_');
                const statusColors = {
                    completed:      'background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;',
                    pending:        'background:#fef9c3;color:#ca8a04;border:1px solid #fef08a;',
                    cancelled:      'background:#fee2e2;color:#dc2626;border:1px solid #fecaca;',
                    refunded:       'background:#fee2e2;color:#dc2626;border:1px solid #fecaca;',
                    partial_refund: 'background:#fef3c7;color:#d97706;border:1px solid #fde68a;',
                    refund:         'background:#fee2e2;color:#dc2626;border:1px solid #fecaca;',
                };
                $('#detailStatusBadge')
                    .text(data.status ?? 'Completed')
                    .attr('style', statusColors[rawStatus] ?? statusColors.completed);

                // ── Meta ──
                $('#detailCustomer').text(data.customer ?? 'Walk-in Customer');
                $('#detailCashier').text(data.cashier ?? '-');
                $('#detailPaymentMethod').text(data.payment_method ?? '-');
                $('#detailDate').text(data.sale_date ?? '-');

                // ── Sale Summary ──
                $('#detailSubtotal').text(data.subtotal ?? '₱0.00');
                $('#detailDiscount').text(data.discount ?? '₱0.00');
                if (data.is_refunded) {
                    $('#detailTotal').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_total}</span><span class="text-danger fw-bold font-mono">₱0.00</span> <span class="badge bg-danger text-white extra-small py-0.5 px-1 ms-1">Refunded</span>`);
                } else if (data.is_partial) {
                    $('#detailTotal').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_total}</span><span class="text-dark fw-bold font-mono">${data.total}</span> <span class="badge bg-warning-subtle text-warning-emphasis extra-small py-0.5 px-1 ms-1">Retained</span>`);
                } else {
                    $('#detailTotal').text(data.total ?? '₱0.00');
                }

                // For split payments, sum from payments array
                const paymentsArr   = data.payments || [];
                const totalTendered = paymentsArr.reduce((s, p) => s + parseFloat((p.tendered_amount ?? '0').replace(/[^0-9.]/g, '')), 0);
                const totalChange   = paymentsArr.reduce((s, p) => s + parseFloat((p.change_amount  ?? '0').replace(/[^0-9.]/g, '')), 0);
                const saleTendered  = parseFloat((data.tendered ?? '0').replace(/[^0-9.]/g, ''));
                const saleCng       = parseFloat((data.change   ?? '0').replace(/[^0-9.]/g, ''));
                const isSplit       = paymentsArr.length > 1;
                $('#detailTendered').text('₱' + (saleTendered > 0 ? saleTendered : totalTendered).toFixed(2) + (isSplit ? ' (split)' : ''));
                $('#detailChange').text('₱' + (saleCng > 0 ? saleCng : totalChange).toFixed(2));

                // Show discount type badge if applicable
                if (data.discount_type) {
                    $('#detailDiscountBadge').text(data.discount_type).css('display', 'inline-block');
                } else {
                    $('#detailDiscountBadge').hide();
                }

                // ── Profit Summary ──
                const margin     = parseFloat(data.profit_margin_pct ?? 0);
                const profitRaw  = parseFloat(data.net_profit_raw ?? 0);
                const profitColor = data.is_refunded ? '#64748b' : (profitRaw >= 0 ? '#16a34a' : '#dc2626');
                const barColor    = data.is_refunded
                    ? '#cbd5e1'
                    : (profitRaw >= 0 ? 'linear-gradient(90deg,#10b981,#059669)' : 'linear-gradient(90deg,#ef4444,#dc2626)');

                if (data.is_refunded) {
                    $('#profitRevenue').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_total}</span><span class="text-muted fw-bold font-mono">₱0.00</span>`);
                    $('#profitCost').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_cost}</span><span class="text-muted fw-bold font-mono">₱0.00</span>`);
                    $('#profitNet').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_profit}</span><span class="text-muted fw-bold font-mono">₱0.00</span>`).css('color', '#64748b');
                } else if (data.is_partial) {
                    $('#profitRevenue').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_total}</span><span class="text-dark fw-bold font-mono">${data.total}</span>`);
                    $('#profitCost').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_cost}</span><span class="text-danger fw-bold font-mono">${data.total_cost}</span>`);
                    $('#profitNet').html(`<span class="text-decoration-line-through text-muted small me-2 font-mono">${data.original_profit}</span><span class="fw-bold font-mono" style="color:${profitColor};">${data.net_profit}</span>`).css('color', profitColor);
                } else {
                    $('#profitRevenue').text(data.total ?? '₱0.00');
                    $('#profitCost').text(data.total_cost ?? '₱0.00');
                    $('#profitNet').text(data.net_profit ?? '₱0.00').css('color', profitColor);
                }
                $('#profitMarginPct').text(margin.toFixed(1) + '%').css('color', profitColor);
                $('#profitMarginBar')
                    .css({ width: Math.min(Math.abs(margin), 100) + '%', background: barColor })
                    .attr('aria-valuenow', Math.abs(margin));

                // ── Transaction Narrative ──
                (function buildNarrative() {
                    const items      = data.items || [];
                    const payments   = data.payments || [];
                    const totalItems = items.reduce((s, i) => s + parseFloat(i.quantity ?? 0), 0);
                    const promoItems = items.filter(i => i.promo_id || i.has_discount);
                    const discAmt    = parseFloat((data.discount ?? '0').replace(/[^0-9.]/g, ''));
                    const netP       = parseFloat(data.net_profit_raw ?? 0);

                    if (data.is_refunded) {
                        $('#detailNarrative')
                            .attr('style', 'background:linear-gradient(135deg,#fef2f2 0%,#fee2e2 100%);border-color:#fca5a5!important;')
                            .find('i')
                            .attr('class', 'bi bi-arrow-counterclockwise mt-1 flex-shrink-0')
                            .css('color', '#dc2626');

                        let narrativeHtml = `⚠️ Transaction <strong>${data.invoice_no}</strong> was <strong>FULLY REFUNDED</strong>. All <strong>${totalItems} units</strong> across <strong>${items.length} products</strong> were returned & restocked to inventory. The total original amount of <strong>${data.original_total}</strong> was returned to customer <strong>${data.customer}</strong>. Net sales revenue and earnings for this transaction are <strong>₱0.00 (0.0% margin)</strong>.`;
                        $('#detailNarrativeText').html(narrativeHtml);
                        return;
                    }

                    if (data.is_partial) {
                        $('#detailNarrative')
                            .attr('style', 'background:linear-gradient(135deg,#fffbeb 0%,#fef3c7 100%);border-color:#fde68a!important;')
                            .find('i')
                            .attr('class', 'bi bi-percent mt-1 flex-shrink-0')
                            .css('color', '#d97706');

                        let narrativeHtml = `⚠️ Transaction <strong>${data.invoice_no}</strong> had a <strong>PARTIAL RETURN</strong>. A total of <strong>${data.refunded_qty} units</strong> were returned & restocked to inventory for a refund payout of <strong style="color:#dc2626">-${data.refunded_amount}</strong>. Retained net sales revenue is <strong>${data.total}</strong> with an adjusted net profit of <strong style="color:#16a34a">${data.net_profit} (${margin.toFixed(1)}% margin)</strong>.`;
                        $('#detailNarrativeText').html(narrativeHtml);
                        return;
                    }

                    $('#detailNarrative')
                        .attr('style', 'background:linear-gradient(135deg,#eff6ff 0%,#dbeafe 100%);border-color:#bfdbfe!important;')
                        .find('i')
                        .attr('class', 'bi bi-lightbulb-fill mt-1 flex-shrink-0')
                        .css('color', '#2563eb');

                    let parts = [];

                    // Who & when
                    parts.push(`Transaction <strong>${data.invoice_no}</strong> was processed by <strong>${data.cashier}</strong> on <strong>${data.sale_date}</strong> for <strong>${data.customer}</strong>.`);

                    // Items
                    const prodNames = items.map(i => `<em>${i.product}</em> (×${i.quantity})`).join(', ');
                    parts.push(`A total of <strong>${totalItems} unit${totalItems !== 1 ? 's' : ''}</strong> were sold across <strong>${items.length} product${items.length !== 1 ? 's' : ''}</strong>: ${prodNames}.`);

                    // Promos / discounts
                    if (promoItems.length > 0) {
                        const promoNames = promoItems.map(i => `<em>${i.product}</em>`).join(', ');
                        parts.push(`🏷️ Promo or discount pricing was applied on: ${promoNames}${discAmt > 0 ? `, saving the customer a total of <strong>₱${discAmt.toFixed(2)}</strong>` : ''}.`);
                    } else if (discAmt > 0) {
                        parts.push(`💸 A discount of <strong>₱${discAmt.toFixed(2)}</strong> was applied to this order.`);
                    } else {
                        parts.push(`No discounts or promos were applied — all items sold at regular price.`);
                    }

                    // Payment
                    if (payments.length > 1) {
                        const payDetails = payments.map(p => `${p.method} <strong>${p.amount}</strong>`).join(' and ');
                        parts.push(`💳 Payment was split: ${payDetails}.`);
                    } else {
                        const pm = payments[0];
                        if (pm) {
                            parts.push(`💳 Paid via <strong>${pm.method}</strong> — tendered <strong>${pm.tendered_amount}</strong>, change returned <strong>${pm.change_amount}</strong>.`);
                        }
                    }

                    // Profit verdict
                    if (netP >= 0) {
                        parts.push(`✅ This transaction was <strong>profitable</strong>: net earnings of <strong style="color:#16a34a">${data.net_profit}</strong> at a <strong>${margin.toFixed(1)}%</strong> margin.`);
                    } else {
                        parts.push(`⚠️ This transaction resulted in a <strong style="color:#dc2626">loss of ${data.net_profit}</strong>. Review cost pricing — the recorded cost of goods exceeds the selling price.`);
                    }

                    $('#detailNarrativeText').html(parts.join(' '));
                })();

                // ── Line Items ──
                let rows = '';
                (data.items || []).forEach(item => {
                    const hasDisc  = item.has_discount;
                    const promoTag = item.promo_id
                        ? `<span class="badge ms-1 px-1 rounded-1 fw-semibold" style="font-size:.6rem;background:#ede9fe;color:#7c3aed;">PROMO</span>`
                        : '';
                    const discTag  = hasDisc
                        ? `<span class="badge ms-1 px-1 rounded-1 fw-semibold" style="font-size:.6rem;background:#fee2e2;color:#dc2626;">-${item.discount_amount}</span>`
                        : '';
                    let refundTag = '';
                    if (data.is_refunded || item.is_item_refunded) {
                        refundTag = `<span class="badge ms-1 px-1.5 py-0.5 rounded-pill fw-bold bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size:.62rem;"><i class="bi bi-arrow-counterclockwise me-0.5"></i>Refunded</span>`;
                    } else if (item.is_item_partial || (item.returned_qty > 0)) {
                        refundTag = `<span class="badge ms-1 px-1.5 py-0.5 rounded-pill fw-bold bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle" style="font-size:.62rem;"><i class="bi bi-arrow-return-left me-0.5"></i>${item.returned_qty} returned</span>`;
                    }

                    let priceDisplay;
                    if (hasDisc && item.original_price !== item.effective_price) {
                        priceDisplay = `<span class="text-muted text-decoration-line-through me-1" style="font-size:.75rem;">${item.original_price}</span><br><span class="fw-bold text-success font-mono">${item.effective_price}</span>`;
                    } else {
                        priceDisplay = `<span class="fw-semibold font-mono">${item.effective_price}</span>`;
                    }

                    const profitRaw  = parseFloat(item.net_profit_raw ?? 0);
                    const profitCls  = data.is_refunded ? 'text-muted' : (profitRaw >= 0 ? 'text-success' : 'text-danger');
                    const profitIcon = data.is_refunded ? '' : (profitRaw >= 0 ? '▲ ' : '▼ ');
                    const margin     = parseFloat(item.profit_margin_pct ?? 0);

                    let qtyDisplay = `<span class="font-mono fw-semibold">${item.quantity}</span>`;
                    let lineTotalDisplay = `<span class="text-dark font-mono fw-bold">${item.line_total}</span>`;
                    let profitDisplay = `<span class="font-mono fw-bold ${profitCls}">${profitIcon}${item.net_profit}</span>`;

                    if (data.is_refunded) {
                        lineTotalDisplay = `<div><span class="text-decoration-line-through text-muted extra-small font-mono d-block">${item.original_line_total}</span><span class="font-mono fw-bold text-muted">₱0.00</span></div>`;
                        profitDisplay = `<div><span class="text-decoration-line-through text-muted extra-small font-mono d-block">${item.original_net_profit}</span><span class="font-mono fw-bold text-muted">₱0.00</span></div>`;
                    } else if (item.returned_qty > 0) {
                        qtyDisplay = `<div class="font-mono"><span class="fw-bold text-dark">${item.retained_qty}</span> <span class="text-muted extra-small">(${item.quantity} orig)</span></div>`;
                        lineTotalDisplay = `<div><span class="text-decoration-line-through text-muted extra-small font-mono d-block">${item.original_line_total}</span><span class="font-mono fw-bold text-dark">${item.line_total}</span></div>`;
                        profitDisplay = `<div><span class="text-decoration-line-through text-muted extra-small font-mono d-block">${item.original_net_profit}</span><span class="font-mono fw-bold ${profitCls}">${profitIcon}${item.net_profit}</span></div>`;
                    }

                    rows += `
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold text-dark d-flex align-items-center flex-wrap gap-1">
                                    ${item.product ?? '-'} ${promoTag} ${discTag} ${refundTag}
                                </div>
                                <small class="text-muted font-mono">${item.barcode !== '-' ? item.barcode : ''}</small>
                            </td>
                            <td class="text-center">${qtyDisplay}</td>
                            <td class="text-end font-mono text-muted small">${item.cost_price}</td>
                            <td class="text-end">${priceDisplay}</td>
                            <td class="text-end">${lineTotalDisplay}</td>
                            <td class="text-end">${profitDisplay}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-2 fw-bold font-mono" style="font-size:.72rem;background:${data.is_refunded?'#f1f5f9':(margin>=20?'#dcfce7':margin>=5?'#fef9c3':'#fee2e2')};color:${data.is_refunded?'#64748b':(margin>=20?'#16a34a':margin>=5?'#ca8a04':'#dc2626')};">
                                    ${margin.toFixed(1)}%
                                </span>
                            </td>
                        </tr>
                    `;
                });
                $('#detailItemsBody').html(rows || '<tr><td colspan="7" class="text-center text-muted py-3">No items found</td></tr>');

                // ── Payment Records ──
                let payRows = '';
                (data.payments || []).forEach(pay => {
                    payRows += `
                        <tr>
                            <td class="ps-3 fw-semibold">${pay.method}</td>
                            <td class="text-end font-mono">${pay.amount}</td>
                            <td class="text-end font-mono">${pay.tendered_amount}</td>
                            <td class="text-end font-mono text-info">${pay.change_amount}</td>
                            <td class="font-mono text-muted">${pay.reference}</td>
                            <td class="text-muted small">${pay.payment_date}</td>
                        </tr>
                    `;
                });
                $('#detailPaymentsBody').html(payRows || '<tr><td colspan="6" class="text-center text-muted py-3">No payment records</td></tr>');

                $('#saleDetailsModal').modal('show');
            }).fail(function () {
                alert('Failed to load transaction details. Please try again.');
            });
        });
    }

    function checkJQuery() {
        if (window.$) {
            initSalesHistory(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        checkJQuery();
    } else {
        document.addEventListener('DOMContentLoaded', checkJQuery);
    }
})();
</script>
@endpush


