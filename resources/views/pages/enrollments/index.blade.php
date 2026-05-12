@extends('layouts.app')
@section('title','Enrollment Management')

@section('content')

    <x-page-header
        title="Enrollment Management"
        subtitle="Manage student enrollments"
    >

        <x-slot:action>

            <a
                href="{{ route('enrollments.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus"></i>
                Add Enrollment
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="enrollmentTable"

            :columns="[
                'Actions',
                'Student',
                'LRN',
                'Grade Level',
                'Strand',
                'Classes',
                'Semester',
                'Academic Year',
                'Status'
            ]"

            :ajax="route('enrollments.data')"

            :datatableColumns="[
                [
                    'data'=>'actions',
                    'orderable'=>false,
                    'searchable'=>false
                ],
                ['data'=>'student'],
                ['data'=>'lrn'],
                ['data'=>'grade_level'],
                ['data'=>'strand'],
                ['data'=>'section'],
                ['data'=>'semester'],
                ['data'=>'academic_year'],
                ['data'=>'status']
            ]"
        />

    </x-card>

@endsection
