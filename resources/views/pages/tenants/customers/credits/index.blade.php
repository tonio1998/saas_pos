@extends('layouts.app')
@section('title', 'Credit Accounts')
@section('shortText', 'Monitor customer credit balances and ledger')
@section('content')
    <x-card>
        <x-datatable
            id="customersTable"
            :columns="[
                'Actions',
                'Customer Code',
                'Customer Name',
                'Customer Address',
                'Credit',
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

@endsection
