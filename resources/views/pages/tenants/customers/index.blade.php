@extends('layouts.app')

@section('title', 'Customer CRM & Loyalty Management')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Customer CRM & Loyalty Management</h4>
                </div>
            </div>
            <p class="text-muted small mb-0">
                Suki rewards tracking, credit ledger balances, VIP tier management & customer spend analytics.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Credit Ledger Shortcut --}}
            <a href="{{ route('customers.credit.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-danger extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-book-half text-danger fs-6"></i>
                <span>Credit & Utang Ledger</span>
            </a>

            {{-- Add Customer Button --}}
            <a href="{{ route('customers.create') }}" class="btn btn-success rounded-3 px-3.5 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>+ Add Customer</span>
            </a>
        </div>
    </div>

    {{-- Asynchronous CRM KPI Insight Cards (Standard Layout) --}}
    <div class="row g-3 mb-4">
        {{-- Total Customers --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Total Customer Base</span>
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-dark font-mono mb-0" id="kpiTotalCustomers">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                    <span class="text-muted extra-small">Registered</span>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1.5">
                    <span class="badge bg-success-subtle text-success fw-bold extra-small border border-success-subtle">Active Suki</span>
                    <span>in Store CRM</span>
                </div>
            </div>
        </div>

        {{-- VIP & Business Accounts --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-warning-emphasis extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">VIP & Business Tiers</span>
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-crown-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-warning-emphasis font-mono mb-0" id="kpiVipCustomers">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                    <span class="text-muted extra-small">VIP Accounts</span>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1.5">
                    <span class="badge bg-warning-subtle text-warning-emphasis fw-bold extra-small border border-warning-subtle">Loyal Members</span>
                    <span>high spenders</span>
                </div>
            </div>
        </div>

        {{-- Total Loyalty Points --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-success extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Loyalty Rewards Pool</span>
                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-star-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-success font-mono mb-0" id="kpiTotalPoints">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                    <span class="text-muted extra-small">pts</span>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1.5">
                    <span class="badge bg-success-subtle text-success fw-bold extra-small border border-success-subtle">Reward Bank</span>
                    <span>redeemable at checkout</span>
                </div>
            </div>
        </div>

        {{-- Active Credit / Utang Receivables --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-danger extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Outstanding Utang Balances</span>
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-wallet-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-danger font-mono mb-0" id="kpiTotalUtang">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1.5">
                    <span class="badge bg-danger-subtle text-danger fw-bold extra-small border border-danger-subtle" id="kpiUtangAccounts">-- Accounts</span>
                    <span>with active balance</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Comprehensive Standard DataTable Card --}}
    <x-card>
        <x-datatable
            id="customersTable"
            :columns="[
                'Actions',
                'Customer Profile & Code',
                'Contact Details',
                'Tier & Privileges',
                'Loyalty Points',
                'Credit / Utang Health',
                'Orders & Spend',
                'Status',
                'Registered'
            ]"
            :ajax="route('customers.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'customer_info', 'name' => 'CustomerName'],
                ['data' => 'contact_details', 'name' => 'mobile_number'],
                ['data' => 'customer_type', 'name' => 'customer_type'],
                ['data' => 'TotalPoints', 'name' => 'TotalPoints'],
                ['data' => 'credit_health', 'orderable' => false, 'searchable' => false],
                ['data' => 'orders_spend', 'orderable' => false, 'searchable' => false],
                ['data' => 'status', 'name' => 'status'],
                ['data' => 'createdAt', 'name' => 'created_at']
            ]"
        />
    </x-card>

</div>

@push('styles')
<style>
.hover-lift {
    transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.06) !important;
}
.avatar-initials {
    border-radius: 9px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    color: #ffffff;
    flex-shrink: 0;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    pullCustomerKpis();
});

async function pullCustomerKpis() {
    try {
        const res = await fetch("{{ route('customers.kpis') }}");
        if (!res.ok) return;
        const d = await res.json();

        const tEl = document.getElementById('kpiTotalCustomers');
        const vEl = document.getElementById('kpiVipCustomers');
        const pEl = document.getElementById('kpiTotalPoints');
        const uEl = document.getElementById('kpiTotalUtang');
        const aEl = document.getElementById('kpiUtangAccounts');

        if (tEl) tEl.textContent = d.total_customers;
        if (vEl) vEl.textContent = d.vip_customers;
        if (pEl) pEl.textContent = d.total_points;
        if (uEl) uEl.textContent = d.total_utang;
        if (aEl) aEl.textContent = d.customers_with_utang_count + ' Accounts';
    } catch (e) {
        console.error('Customer KPIs error:', e);
    }
}
</script>
@endpush
@endsection
