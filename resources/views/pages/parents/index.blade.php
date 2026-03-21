@extends('layouts.app')
@section('title','Parent Management')
@section('content')
    <x-page-header title="Parent Management" subtitle="Manage student">
        <x-slot:action>
            <a href="{{ route('parents.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Parent
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="studentsTable"
            :columns="['Actions','Name','Phone Number','Address','Students','Created At', 'Created By']"
            :ajax="route('parents.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'name'],
                ['data'=>'phone_number'],
                ['data'=>'address'],
                ['data'=>'students'],
                ['data'=>'created_at'],
                ['data'=>'createdBy']
            ]"
        />
    </x-card>
@endsection
