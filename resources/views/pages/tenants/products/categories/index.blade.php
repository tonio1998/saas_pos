@extends('layouts.app')

@section('title', 'Product Categories')

@section('content')

    <x-page-header
        title="Product Categories"
        subtitle="Manage Product Categories"
    >
        <x-slot:action>
            <a
                href="{{ route('products.categories.create') }}"
                class="btn btn-primary btn-md"
            >
                <i class="bi bi-plus"></i>
                Add Category
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <x-datatable
            id="categoriesTable"
            :columns="[
                'Actions',
                'Category Name',
                'Description',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('products.categories.data')"
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
