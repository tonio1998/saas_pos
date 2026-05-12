@extends('layouts.app')
@section('title','Strand Management')

@section('content')

    <x-page-header
        title="Strand Management"
        subtitle="Manage SHS strands"
    >

        <x-slot:action>

            <a
                href="{{ route('strands.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Strand
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="strandsTable"

            :columns="[
                'Actions',
                'Strand Code',
                'Strand Name',
                'Description',
                'Status',
                'Created At'
            ]"

            :ajax="route('strands.data')"

            :datatableColumns="[
                [
                    'data'=>'actions',
                    'orderable'=>false,
                    'searchable'=>false
                ],

                [
                    'data'=>'strand_code'
                ],

                [
                    'data'=>'strand_name'
                ],

                [
                    'data'=>'description'
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
