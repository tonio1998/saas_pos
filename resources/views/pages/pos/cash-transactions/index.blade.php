@extends('layouts.app')

@section('title', 'Cash Transactions')
@section('shortText', 'View all recorded cash transactions')

@section('content')

    <x-page-header>

        <x-slot:action>
            <a
                href="{{ route('cashiering.cash-transactions.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus"></i>
                Add Transaction
            </a>
        </x-slot:action>

    </x-page-header>

    <x-card>

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
                ['data' => 'createdBy'],
            ]"
        />

    </x-card>

@endsection
