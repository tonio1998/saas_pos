@extends('layouts.app')

@section('title','Student Management')

@section('content')

    <div class="container-fluid px-0">

        <x-page-header
            title="Student Management"
            subtitle="Registrar student records"
        >

            <x-slot:action>

                <a
                    href="{{ route('students.create') }}"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-circle"></i>
                    Add Student
                </a>

            </x-slot:action>

        </x-page-header>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <x-datatable
                    id="studentsTable"

                    :columns="[
                        'Actions',
                        'Photo',
                        'Student',
                        'LRN',
                        'Phone Number',
                        'Year',
                        'Strand',
                        'Created At',
                        'Created By'
                    ]"

                    :ajax="route('students.data')"

                    :datatableColumns="[

                        [
                            'data'=>'actions',
                            'orderable'=>false,
                            'searchable'=>false,
                            'width'=>'90px'
                        ],
[
                            'data'=>'photo'
                        ],
                        [
                            'data'=>'name'
                        ],

                        [
                            'data'=>'lrn'
                        ],

                        [
                            'data'=>'phone_number'
                        ],

                        [
                            'data'=>'year'
                        ],

                        [
                            'data'=>'Strand'
                        ],

                        [
                            'data'=>'created_at'
                        ],

                        [
                            'data'=>'createdBy'
                        ]

                    ]"
                />

            </div>

        </div>

    </div>

@endsection

@section('styles')


@endsection
