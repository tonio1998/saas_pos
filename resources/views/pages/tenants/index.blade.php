@extends('layouts.sa')

@section('title', 'Tenant Management')

@section('content')

    <div class="container-fluid px-0">

        <x-page-header
            title="Tenant Management"
            subtitle="Manage businesses, subscriptions, tenant access, and platform configuration"
        >

            <x-slot:action>

                <a
                    href="{{ route('sa.tenants.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-circle"></i>
                    Add Tenant
                </a>

            </x-slot:action>

        </x-page-header>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body p-4">

                <x-datatable
                    id="tenantsTable"

                    :columns="[
                        'Actions',
                        'Logo',
                        'Business',
                        'Code',
                        'Owner',
                        'Subscription',
                        'Products',
                        'Users',
                        'Status',
                        'Created At'
                    ]"

                    :ajax="route('sa.tenants.data')"

                    :datatableColumns="[

                        [
                            'data' => 'actions',
                            'orderable' => false,
                            'searchable' => false,
                            'width' => '90px'
                        ],

                        [
                            'data' => 'logo',
                            'orderable' => false,
                            'searchable' => false,
                            'width' => '80px'
                        ],

                        [
                            'data' => 'business'
                        ],

                        [
                            'data' => 'business_code'
                        ],

                        [
                            'data' => 'owner'
                        ],

                        [
                            'data' => 'subscription'
                        ],

                        [
                            'data' => 'products_count'
                        ],

                        [
                            'data' => 'users_count'
                        ],

                        [
                            'data' => 'status'
                        ],

                        [
                            'data' => 'created_at'
                        ]

                    ]"
                />

            </div>

        </div>

    </div>

@endsection
