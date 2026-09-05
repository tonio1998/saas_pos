@extends('layouts.app')

@section('title', 'Payments Ledger & Reconciliation | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header Bar --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Payments Ledger & Reconciliation</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Real-time audit trail and ledger of all cash, e-wallet, card, and digital payment collections.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('sales.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-receipt-cutoff text-success"></i>
                <span>Sales History</span>
            </a>
            <a href="{{ route('returns.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-arrow-return-left text-danger"></i>
                <span>Returns</span>
            </a>
            <button type="button" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" id="btnExportPayments" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-file-earmark-spreadsheet-fill fs-6"></i>
                <span>Export Ledger</span>
            </button>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- 1. Total Collections --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Collections</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;">
                        <i class="bi bi-wallet2 fs-6"></i>
                    </div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">₱{{ number_format($totalAmountCollected, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">{{ number_format($totalPaymentsCount) }} total transactions</div>
            </div>
        </div>

        {{-- 2. Total Cash In Drawer --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Cash Collected</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;">
                        <i class="bi bi-cash fs-6"></i>
                    </div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">₱{{ number_format($totalCashCollected, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Physical Cash Drawer Tenders</div>
            </div>
        </div>

        {{-- 3. Digital & E-Wallets --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Digital / Non-Cash</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;">
                        <i class="bi bi-phone fs-6"></i>
                    </div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-warning-emphasis mb-0">₱{{ number_format($totalDigitalCollected, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">GCash, Maya, Card & Transfers</div>
            </div>
        </div>

        {{-- 4. Today's Collections --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Collected Today</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;">
                        <i class="bi bi-calendar-check fs-6"></i>
                    </div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black mb-0" style="color:#7c3aed;">₱{{ number_format($todayCollected, 2) }}</div>
                <div class="text-success extra-small mt-1 fw-bold"><i class="bi bi-lightning-charge-fill me-1"></i>Live Current Day Total</div>
            </div>
        </div>
    </div>

    {{-- Filter & Control Card --}}
    <div class="card border rounded-4 shadow-xs bg-white mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                {{-- Payment Method Filter --}}
                <div class="col-12 col-md-3">
                    <label class="form-label extra-small text-muted fw-bold mb-1">PAYMENT CHANNEL</label>
                    <select id="filterPaymentMethod" class="form-select form-select-sm rounded-3 font-mono">
                        <option value="">All Payment Methods</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method }}">{{ ucfirst($method) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Terminal Filter --}}
                <div class="col-6 col-md-2">
                    <label class="form-label extra-small text-muted fw-bold mb-1">TERMINAL</label>
                    <select id="filterTerminal" class="form-select form-select-sm rounded-3 font-mono">
                        <option value="">All Terminals</option>
                        @foreach($terminals as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Cashier Filter --}}
                <div class="col-6 col-md-2">
                    <label class="form-label extra-small text-muted fw-bold mb-1">CASHIER / USER</label>
                    <select id="filterCashier" class="form-select form-select-sm rounded-3 font-mono">
                        <option value="">All Cashiers</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range --}}
                <div class="col-6 col-md-2">
                    <label class="form-label extra-small text-muted fw-bold mb-1">FROM DATE</label>
                    <input type="date" id="filterDateFrom" class="form-control form-control-sm rounded-3 font-mono">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label extra-small text-muted fw-bold mb-1">TO DATE</label>
                    <input type="date" id="filterDateTo" class="form-control form-control-sm rounded-3 font-mono">
                </div>

                {{-- Reset Filter Button --}}
                <div class="col-12 col-md-1 d-flex align-items-end">
                    <button type="button" id="btnResetFilters" class="btn btn-sm btn-light border w-100 rounded-3 text-muted fw-bold extra-small mt-auto py-1.5" title="Reset Filters">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Payments Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-cash-stack text-success fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Payment Transactions & Audit Trail</h5>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-white btn-sm border font-mono fw-bold px-2.5 py-1.5 rounded-3 extra-small shadow-xs text-dark" id="btnRefreshTable">
                    <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                </button>
            </div>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="paymentsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Payment Ref #</th>
                            <th>Invoice / Sale</th>
                            <th>Customer</th>
                            <th>Method</th>
                            <th class="text-end">Amount Paid</th>
                            <th class="text-end">Tendered / Change</th>
                            <th>Terminal / Shift</th>
                            <th>Payment Date</th>
                            <th>Cashier</th>
                            <th class="text-end" style="width:60px;">Receipt</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initPaymentsPage($) {
        if ($('#paymentsTable').length) {
            const table = $('#paymentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('payments.data') }}",
                    data: function (d) {
                        d.payment_method = $('#filterPaymentMethod').val();
                        d.terminal_id    = $('#filterTerminal').val();
                        d.cashier_id     = $('#filterCashier').val();
                        d.date_from      = $('#filterDateFrom').val();
                        d.date_to        = $('#filterDateTo').val();
                    }
                },
                columns: [
                    { data: 'reference_no', name: 'reference_number' },
                    { data: 'sale_invoice', name: 'sale.invoice_no' },
                    { data: 'customer', name: 'customer.name' },
                    { data: 'payment_method', name: 'payment_method' },
                    { data: 'amount', name: 'amount', className: 'text-end' },
                    { data: 'tendered_change', name: 'tendered_amount', orderable: false, searchable: false },
                    { data: 'terminal_shift', name: 'terminal_id', orderable: false },
                    { data: 'payment_date', name: 'payment_date' },
                    { data: 'cashier', name: 'creator.name' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[7, 'desc']],
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search payments, ref #, invoice...",
                    processing: '<div class="spinner-border spinner-border-sm text-success" role="status"></div> Loading payments...'
                }
            });

            // Filter Change Triggers
            $('#filterPaymentMethod, #filterTerminal, #filterCashier, #filterDateFrom, #filterDateTo').on('change', function () {
                table.draw();
            });

            $('#btnResetFilters').on('click', function () {
                $('#filterPaymentMethod').val('');
                $('#filterTerminal').val('');
                $('#filterCashier').val('');
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                table.draw();
            });

            $('#btnRefreshTable').on('click', function () {
                table.ajax.reload(null, false);
            });

            $('#btnExportPayments').on('click', function () {
                if (typeof appAlert === 'function') {
                    appAlert({
                        title: 'Export Ledger',
                        text: 'Generating payments ledger CSV...',
                        type: 'info'
                    });
                } else {
                    alert('Exporting Payments Ledger...');
                }
            });
        }
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initPaymentsPage(window.$);
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
