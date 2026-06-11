@extends('layouts.app')

@section('title', 'Product Units')

@section('content')

    <x-page-header
        title="Product Units"
        subtitle="Manage Product Units"
    >
        <x-slot:action>
            <a
                href="{{ route('products.units.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Unit
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <x-datatable
            id="unitsTable"
            :columns="[
                'Actions',
                'Unit Name',
                'Description',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('products.units.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'name'],
                ['data' => 'description'],
                ['data' => 'status'],
                ['data' => 'createdAt'],
                ['data' => 'createdBy']
            ]"
        />

    </x-card>

@endsection
