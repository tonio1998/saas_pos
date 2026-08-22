@extends('layouts.app')

@section('title', 'Customer CRM & Loyalty Management')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="kpi-icon-box emerald" style="width:40px;height:40px;font-size:1.15rem;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Customer CRM & Loyalty Management</h4>
                <p class="text-muted extra-small mb-0">Suki rewards tracking, credit ledger balances, VIP tier management & customer spend analytics</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Credit Ledger Shortcut --}}
            <a href="{{ route('customers.credit.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-danger extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-book-half text-danger fs-6"></i>
                <span>Credit & Utang Ledger</span>
            </a>

            {{-- Add Customer Button --}}
            <a href="{{ route('customers.create') }}" class="btn btn-success rounded-3 px-3 py-1.5 fw-bold d-flex align-items-center gap-2 shadow-xs hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.85rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>+ Add Customer</span>
            </a>
        </div>
    </div>

    {{-- 4 Dashboard-Style KPI Cards --}}
    <div class="row g-3 mb-3">
        {{-- Total Customers --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Total Customer Base</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-people-fill"></i></div>
                </div>
                <div class="kpi-value font-mono" id="kpiTotalCustomers">
                    <span class="spinner-border spinner-border-sm text-muted"></span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Active Suki CRM</span>
                </div>
            </div>
        </div>

        {{-- VIP & Business Accounts --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">VIP & Business Tiers</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-crown-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber" id="kpiVipCustomers">
                    <span class="spinner-border spinner-border-sm text-muted"></span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-star-fill me-1"></i>Loyal VIP members</span>
                </div>
            </div>
        </div>

        {{-- Total Loyalty Points --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Loyalty Rewards Pool</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-star-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-success" id="kpiTotalPoints">
                    <span class="spinner-border spinner-border-sm text-muted"></span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-gift-fill me-1"></i>Redeemable points</span>
                </div>
            </div>
        </div>

        {{-- Active Credit / Utang Receivables --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card rose h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Utang Receivables</span>
                    <div class="kpi-icon-box rose"><i class="bi bi-wallet-fill"></i></div>
                </div>
                <div class="kpi-value font-mono text-danger" id="kpiTotalUtang">
                    <span class="spinner-border spinner-border-sm text-muted"></span>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-danger extra-small fw-bold" id="kpiUtangAccounts"><i class="bi bi-exclamation-triangle-fill me-1"></i>Outstanding balance</span>
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
        if (pEl) pEl.textContent = d.total_points + ' pts';
        if (uEl) uEl.textContent = d.total_utang;
        if (aEl) aEl.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i>' + d.customers_with_utang_count + ' Accounts with balance';
    } catch (e) {
        console.error('Customer KPIs error:', e);
    }
}
</script>
@endpush
@endsection
