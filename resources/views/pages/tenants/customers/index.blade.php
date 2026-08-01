@extends('layouts.app')
@section('title', 'Customers')
@section('shortText', 'Manage customers')
@section('content')

    <x-page-header>
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
                'Customer Address',
                'Customer Type',
                'Credit',
                'Total Points',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('customers.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'CustomerCode'],
                ['data' => 'CustomerName'],
                ['data' => 'CustomerAddress'],
                ['data' => 'customer_type'],
                ['data' => 'credit'],
                ['data' => 'TotalPoints'],
                ['data' => 'status'],
                ['data' => 'createdAt'],
                ['data' => 'createdBy']
            ]"
        />

    </x-card>

@endsection
