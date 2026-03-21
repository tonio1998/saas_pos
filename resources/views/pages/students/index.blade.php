@extends('layouts.app')
@section('title','Student Management')
@section('content')
    <x-page-header title="Student Management" subtitle="Manage student">
        <x-slot:action>
            <a href="{{ route('students.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Students
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="studentsTable"
            :columns="['Actions','Image','Student Name','LRN','Phone Number','Section','Year','Created At', 'Created By']"
            :ajax="route('students.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'image'],
                ['data'=>'name'],
                ['data'=>'lrn'],
                ['data'=>'phone_number'],
                ['data'=>'section'],
                ['data'=>'year'],
                ['data'=>'created_at'],
                ['data'=>'createdBy']
            ]"
        />
    </x-card>
@endsection
