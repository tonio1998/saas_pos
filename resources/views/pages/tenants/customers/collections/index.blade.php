@extends('layouts.app')

@section('title', 'Customer Collections & Receivables Stream | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                <i class="bi bi-arrow-left fs-6"></i>
            </a>
            <div class="kpi-icon-box rose" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-wallet-fill"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Customer Collections & Receivables</h4>
                <p class="text-muted extra-small mb-0">Active customer accounts with unsettled utang balances, total sales charges, and payment tracking</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('customers.credit.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-danger extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-book-half text-danger fs-6"></i>
                <span>Credit Accounts</span>
            </a>

            <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-people me-1"></i>
                <span>Customer Directory</span>
            </a>
        </div>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        {{-- Total Active Accounts --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Accounts with Balance</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-person-exclamation"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber">{{ number_format($totalAccounts) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-exclamation-circle-fill me-1"></i>Active debtors</span>
                </div>
            </div>
        </div>

        {{-- Total Debit Charged --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Total Sales Charged</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-cart-check"></i></div>
                </div>
                <div class="kpi-value font-mono">₱{{ number_format($totalDebit, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-file-earmark-text me-1"></i>Total utang charges</span>
                </div>
            </div>
        </div>

        {{-- Total Credit Payments --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Payments Collected</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div class="kpi-value font-mono text-success">₱{{ number_format($totalCredit, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-check-all me-1"></i>Total payments settled</span>
                </div>
            </div>
        </div>

        {{-- Total Outstanding Balance --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card rose h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Uncollected Receivables</span>
                    <div class="kpi-icon-box rose"><i class="bi bi-wallet-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-danger">₱{{ number_format($totalBalance, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-danger extra-small fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Net uncollected balance</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Standard DataTable Card --}}
    <x-card>
        <x-datatable
            id="collectionsTable"
            :columns="[
                'Actions',
                'Customer Code',
                'Customer Name',
                'Customer Type',
                'Mobile Number',
                'Total Sales Charged',
                'Total Payments Collected',
                'Outstanding Balance',
                'Last Transaction'
            ]"
            :ajax="route('customers.collections.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'customer_code'],
                ['data' => 'customer_name'],
                ['data' => 'customer_type'],
                ['data' => 'mobile_number'],
                ['data' => 'total_debit'],
                ['data' => 'total_credit'],
                ['data' => 'balance'],
                ['data' => 'last_transaction']
            ]"
        />
    </x-card>

</div>
@endsection
