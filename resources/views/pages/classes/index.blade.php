@extends('layouts.app')
@section('title','Classes Management')

@section('content')

    <x-page-header
        title="Section Management"
        subtitle="Manage academic sections and masterlists"
    >

        <x-slot:action>

            <a
                href="{{ route('classes.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Section
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="sectionsTable"

            :columns="[
                'Actions',
                'School Year',
                'Grade Level',
                'Strand',
                'Classes',
                'Adviser',
                'Capacity',
                'Students',
                'Status'
            ]"

            :ajax="route('classes.data')"

            :datatableColumns="[
                [
                    'data'=>'actions',
                    'orderable'=>false,
                    'searchable'=>false
                ],

                [
                    'data'=>'school_year'
                ],

                [
                    'data'=>'grade_level'
                ],

                [
                    'data'=>'strand'
                ],

                [
                    'data'=>'section_name'
                ],

                [
                    'data'=>'adviser'
                ],

                [
                    'data'=>'capacity'
                ],

                [
                    'data'=>'student_count'
                ],

                [
                    'data'=>'status'
                ]
            ]"
        />

    </x-card>

@endsection
