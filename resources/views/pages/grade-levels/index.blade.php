@extends('layouts.app')
@section('title','Grade Level Management')

@section('content')

    <x-page-header
        title="Grade Level Management"
        subtitle="Manage academic grade levels"
    >

        <x-slot:action>

            <a
                href="{{ route('grade-levels.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Grade Level
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="gradeLevelsTable"

            :columns="[
                'Actions',
                'Grade Level',
                'Education Level',
                'Has Semester',
                'Status',
                'Created At'
            ]"

            :ajax="route('grade-levels.data')"

            :datatableColumns="[
                [
                    'data'=>'actions',
                    'orderable'=>false,
                    'searchable'=>false
                ],

                [
                    'data'=>'grade_level'
                ],

                [
                    'data'=>'education_level'
                ],

                [
                    'data'=>'has_semester'
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
