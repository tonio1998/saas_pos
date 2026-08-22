@extends('layouts.app')

@section('title', 'Credit Accounts & Utang Management | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                <i class="bi bi-arrow-left fs-6"></i>
            </a>
            <div class="kpi-icon-box rose" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-book-half"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Customer Credit & Utang Accounts</h4>
                <p class="text-muted extra-small mb-0">Monitor receivables, customer credit limits, outstanding balances, and payment histories</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('customers.collections.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-success extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-cash-stack text-success fs-6"></i>
                <span>Collections Log</span>
            </a>

            <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-people me-1"></i>
                <span>Customer Directory</span>
            </a>
        </div>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        {{-- Accounts with Active Utang --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Debtor Accounts</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-person-exclamation"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber">{{ number_format($accountsWithUtang) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-exclamation-circle-fill me-1"></i>Accounts with utang</span>
                </div>
            </div>
        </div>

        {{-- Total Outstanding Balance --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card rose h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Outstanding Receivables</span>
                    <div class="kpi-icon-box rose"><i class="bi bi-wallet-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-danger">₱{{ number_format($totalUtangBalance, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-danger extra-small fw-bold"><i class="bi bi-arrow-down-right me-1"></i>Total store utang balance</span>
                </div>
            </div>
        </div>

        {{-- Average Utang per Account --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Average Utang / Account</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-calculator"></i></div>
                </div>
                <div class="kpi-value font-mono">₱{{ number_format($avgUtangPerAccount, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-bar-chart me-1"></i>Mean credit exposure</span>
                </div>
            </div>
        </div>

        {{-- Highest Single Account Utang --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card purple h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Highest Single Utang</span>
                    <div class="kpi-icon-box purple"><i class="bi bi-trophy"></i></div>
                </div>
                <div class="kpi-value font-mono">₱{{ number_format($maxSingleUtang, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-secondary extra-small fw-bold"><i class="bi bi-award me-1"></i>Max debtor balance</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Standard DataTable Card --}}
    <x-card>
        <x-datatable
            id="customersTable"
            :columns="[
                'Actions',
                'Customer Code',
                'Customer Profile',
                'Address',
                'Outstanding Balance',
                'Total Points',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('customers.credit.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'CustomerCode'],
                ['data' => 'CustomerName'],
                ['data' => 'CustomerAddress'],
                ['data' => 'credit'],
                ['data' => 'TotalPoints'],
                ['data' => 'status'],
                ['data' => 'createdAt'],
                ['data' => 'createdBy']
            ]"
        />
    </x-card>

</div>
@endsection
