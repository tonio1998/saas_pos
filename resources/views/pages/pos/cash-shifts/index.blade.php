@extends('layouts.app')

@section('title', 'Cash Shifts & Drawer Session Audit Log | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-clock-history fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Cash Shifts & Drawer Session Audit Log</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Real-time tracking of cashier shifts, opening cash drawers, cash counts, closing balances, and shift audit logs.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cashiering.cash-transactions.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-cash-stack text-success"></i>
                <span>Cash Transactions</span>
            </a>

            <a href="{{ route('terminal.index') }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-calculator-fill fs-6"></i>
                <span>Open POS Terminal</span>
            </a>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Shift Tracking</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-clock-history fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">Shift Ledger</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Drawer Operations</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Shift Status</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-door-open-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">Active</div>
                <div class="text-success extra-small mt-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>System Monitoring</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Reconciliation</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-calculator-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-warning-emphasis mb-0">Cash Count</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Audit Check</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Drawer Control</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-shield-lock-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-purple mb-0" style="color:#7c3aed;">Secured</div>
                <div class="text-muted extra-small mt-1 fw-semibold">LikhaPOS Cashiering</div>
            </div>
        </div>
    </div>

    {{-- Main Cash Shifts Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-text text-primary fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Cashier Shift Sessions Ledger</h5>
            </div>
        </div>

        <div class="card-body p-3">
            <x-datatable
                id="cashShiftsTable"
                :columns="[
                    'Actions',
                    'Shift Code',
                    'Drawer',
                    'Cashier',
                    'Opening Cash',
                    'Opened At',
                    'Status'
                ]"
                :ajax="route('cashiering.cash-shifts.data')"
                :datatableColumns="[
                    ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                    ['data' => 'shift_code'],
                    ['data' => 'drawer'],
                    ['data' => 'cashier'],
                    ['data' => 'opening_cash'],
                    ['data' => 'opened_at'],
                    ['data' => 'status']
                ]"
            />
        </div>
    </div>
</div>
@endsection
