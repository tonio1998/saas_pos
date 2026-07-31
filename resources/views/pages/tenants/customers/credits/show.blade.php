@extends('layouts.app')

@section('title', 'Customer Credits')
@section('shortText', 'Monitor customer credit balances and ledger')

@section('content')

    <x-card class="mb-3" style="border-radius: 10px;">
        <div class="row">
            <div class="col-md-3">
                <small class="text-muted">Customer</small>
                <h5>{{ $customer->CustomerName }}</h5>
            </div>

            <div class="col-md-2">
                <small class="text-muted">Customer Code</small>
                <h5>{{ getCustomerCode($customer->id) }}</h5>
            </div>

            <div class="col-md-2">
                <small class="text-muted">Current Balance</small>
                <h4 class="text-danger">
                    ₱{{ number_format(optional($customer->credit)->running_balance ?? 0,2) }}
                </h4>
            </div>

            <div class="col-md-2">
                <small class="text-muted">Credit Limit</small>
                <h5>₱{{ number_format($customer->CreditLimit ?? 0,2) }}</h5>
            </div>

            <div class="col-md-3 text-end">
                <a href="{{ route('customers.collections.create', [encryptId($customer->id)]) }}"
                   class="btn btn-success">
                    <i class="bi bi-cash"></i>
                    Receive Payment
                </a>
            </div>
        </div>
    </x-card>

    <x-card>

        <x-datatable
            id="customerLedgerTable"
            :columns="[
            '#',
            'Date',
            'Reference No.',
            'Transaction',
            'Debit',
            'Credit',
            'Running Balance',
            'Remarks'
        ]"

            :ajax="route('customers.credit.ledger.data', [
            'CustomerID' => encryptId($customer->id)
        ])"

            :datatableColumns="[
            ['data' => 'DT_RowIndex', 'searchable' => false],
            ['data' => 'date'],
            ['data' => 'reference_no'],
            ['data' => 'transaction_type'],
            ['data' => 'debit'],
            ['data' => 'credit'],
            ['data' => 'running_balance'],
            ['data' => 'remarks']
        ]"

        />
    </x-card>


    <div
        class="modal fade"
        id="saleDetailsModal"
        tabindex="-1"
    >

        <div class="modal-dialog modal-xl">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Sale Details
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div
                    class="modal-body"
                    id="saleDetailsContent"
                >

                    <div class="text-center py-5">

                        <div
                            class="spinner-border text-primary"
                        ></div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <script>

    </script>
@endsection
