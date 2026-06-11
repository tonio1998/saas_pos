@extends('layouts.app')

@section('title', 'Customers')

@section('content')

    <x-page-header
        title="Customers"
        subtitle="Manage Customers"
    >
        <x-slot:action>
            <a
                href="{{ route('customers.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Customer
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <x-datatable
            id="customersTable"
            :columns="[
                'Actions',
                'Customer Code',
                'Customer Name',
                'Customer Type',
                'Mobile Number',
                'Email',
                'Current Balance',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('customers.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'customer_code'],
                ['data' => 'customer_name'],
                ['data' => 'customer_type'],
                ['data' => 'mobile_number'],
                ['data' => 'email'],
                ['data' => 'current_balance'],
                ['data' => 'status'],
                ['data' => 'createdAt'],
                ['data' => 'createdBy']
            ]"
        />

    </x-card>

@endsection
