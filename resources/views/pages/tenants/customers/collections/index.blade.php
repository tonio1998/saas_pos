@extends('layouts.app')

@section('title', 'Customer Collections & Receivables Stream')

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
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Customer Collections & Receivables</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Active customer accounts with unsettled utang balances, total sales charges, and payment tracking.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('customers.credit.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-danger extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-book-half text-danger fs-6"></i>
                <span>Credit Accounts</span>
            </a>

            <a href="{{ route('customers.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift">
                <i class="bi bi-people me-1"></i> Customer Directory
            </a>
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
                ['data' => 'total_debit', 'className' => 'text-end'],
                ['data' => 'total_credit', 'className' => 'text-end'],
                ['data' => 'balance', 'className' => 'text-end fw-bold'],
                ['data' => 'last_transaction']
            ]"
        />
    </x-card>

</div>
@endsection
