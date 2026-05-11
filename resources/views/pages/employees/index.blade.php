@extends('layouts.app')
@section('title','Employees Management')
@section('content')
    <x-page-header title="Employee Management" subtitle="Manage teacher">
        <x-slot:action>
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Employee
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="teachersTable"
            :columns="['Actions','Name','Phone Number','Address','Created At', 'Created By']"
            :ajax="route('employees.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'name'],
                ['data'=>'phone_number'],
                ['data'=>'address'],
                ['data'=>'created_at'],
                ['data'=>'createdBy']
            ]"
        />
    </x-card>
@endsection
