@extends('layouts.app')

@section('title', 'Credit Accounts & Utang Management')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('customers.index') }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-6"></i>
                </a>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Customer Credit & Utang Accounts</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Monitor receivables, customer credit limits, outstanding balances, and payment histories.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('customers.collections.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-success extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-cash-stack text-success fs-6"></i>
                <span>Collections Log</span>
            </a>

            <a href="{{ route('customers.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-people me-1"></i> Customer Directory
            </a>
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
