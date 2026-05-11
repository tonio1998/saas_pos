@extends('layouts.app')
@section('title','Semester Management')

@section('content')

    <x-page-header
        title="Semester Management"
        subtitle="Manage school semesters"
    >

        <x-slot:action>

            <a
                href="{{ route('semesters.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Semester
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="semestersTable"

            :columns="[
                'Actions',
                'Semester Name',
                'Semester Order',
                'Status',
                'Created At'
            ]"

            :ajax="route('semesters.data')"

            :datatableColumns="[
                [
                    'data'=>'actions',
                    'orderable'=>false,
                    'searchable'=>false
                ],

                [
                    'data'=>'semester_name'
                ],

                [
                    'data'=>'semester_order'
                ],

                [
                    'data'=>'status'
                ],

                [
                    'data'=>'created_at'
                ]
            ]"
        />

    </x-card>

@endsection
