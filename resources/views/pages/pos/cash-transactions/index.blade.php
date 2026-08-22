@extends('layouts.app')

@section('title', 'Cash Transactions & Drawer In/Out Log | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-cash-stack fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Cash Transactions & Drawer In/Out Log</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Record and audit petty cash additions (cash in), cash payouts for store expenses (cash out), and drawer drops.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cashiering.cash-shifts.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Cash Shifts</span>
            </a>

            <a href="{{ route('cashiering.cash-transactions.create') }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>Record Cash In / Out</span>
            </a>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Transactions Logged</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-journal-text fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">Petty Cash</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Drawer Log</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Cash In / Additions</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-arrow-down-left-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">Petty In</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Float Additions</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Cash Out / Expenses</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fff1f2;color:#e11d48;"><i class="bi bi-arrow-up-right-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">Paid Out</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Store Payouts</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Drawer Audit</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-shield-check fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-purple mb-0" style="color:#7c3aed;">Verified</div>
                <div class="text-success extra-small mt-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Audit Enabled</div>
            </div>
        </div>
    </div>

    {{-- Main Cash Transactions Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-success fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Petty Cash & Drawer Transactions Ledger</h5>
            </div>
            <a href="{{ route('cashiering.cash-transactions.create') }}" class="btn btn-success btn-sm font-mono fw-bold px-3 py-1.5 rounded-3 extra-small shadow-xs text-white" style="background:#059669;border:none;">
                <i class="bi bi-plus-lg me-1"></i> Record Cash In / Out
            </a>
        </div>

        <div class="card-body p-3">
            <x-datatable
                id="cashTransactionsTable"
                :columns="[
                    'Actions',
                    'Shift Code',
                    'Drawer',
                    'Cashier',
                    'Type',
                    'Amount',
                    'Date',
                    'Created By'
                ]"
                :ajax="route('cashiering.cash-transactions.data')"
                :datatableColumns="[
                    ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                    ['data' => 'shift_code'],
                    ['data' => 'drawer_name'],
                    ['data' => 'cashier_name'],
                    ['data' => 'transaction_type_badge'],
                    ['data' => 'amount_formatted'],
                    ['data' => 'created_at_fmt'],
                    ['data' => 'createdBy']
                ]"
            />
        </div>
    </div>
</div>
@endsection
