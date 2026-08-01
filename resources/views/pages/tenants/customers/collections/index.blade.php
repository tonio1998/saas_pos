@extends('layouts.app')
@section('title', 'Collections')
@section('shortText', 'Manage customer collections')

@section('content')

    <x-page-header />

    <x-card>

        <x-datatable
            id="collectionsTable"
            :columns="[
                'Actions',
                'Customer Code',
                'Customer Name',
                'Customer Type',
                'Mobile Number',
                'Total Sales',
                'Total Payments',
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

@endsection
