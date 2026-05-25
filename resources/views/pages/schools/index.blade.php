@extends('layouts.app')

@section('title', 'School Management')

@section('content')

    <div class="container-fluid px-0">

        <x-page-header
            title="School Management"
            subtitle="Manage tenant schools, branding, SMS, and platform configuration"
        >

            <x-slot:action>

                <a
                    href="{{ route('schools.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-circle"></i>
                    Add School
                </a>

            </x-slot:action>

        </x-page-header>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            <div class="card-body p-4">

                <x-datatable
                    id="schoolsTable"

                    :columns="[
                    'Actions',
                    'Logo',
                    'School',
                    'Code',
                    'Education',
                    'Theme',
                    'SMS',
                    'Users',
                    'Status',
                    'Created At'
                ]"

                    :ajax="route('schools.data')"

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
                        'data' => 'school'
                    ],

                    [
                        'data' => 'code'
                    ],

                    [
                        'data' => 'education_level'
                    ],

                    [
                        'data' => 'theme',
                        'orderable' => false,
                        'searchable' => false
                    ],

                    [
                        'data' => 'sms'
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
