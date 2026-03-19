@extends('layouts.app')
@section('title','Teacher Management')
@section('content')
    <x-page-header title="Teacher Management" subtitle="Manage teacher">
        <x-slot:action>
            <a href="{{ route('teachers.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Teacher
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="teachersTable"
            :columns="['Actions','Name','Phone Number','Address','Residents','Created At', 'Created By']"
            :ajax="route('teachers.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'name'],
                ['data'=>'phone_number'],
                ['data'=>'address'],
                ['data'=>'residents'],
                ['data'=>'created_at'],
                ['data'=>'createdBy']
            ]"
        />
    </x-card>
@endsection
