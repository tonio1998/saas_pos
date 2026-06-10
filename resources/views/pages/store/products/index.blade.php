@extends('layouts.app')

@section('title', 'Product Masterlist')

@section('content')

    <x-page-header
        title="Product Masterlist"
        subtitle="Manage Products"
    >
        <x-slot:action>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Product
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="productsTables"
            :columns="[
                'Actions',
                'Image',
                'Barcode',
                'SKU',
                'Product Name',
                'Category',
                'Unit',
                'Cost Price',
                'Selling Price',
                'Wholesale Price',
                'Estimated Profit',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('products.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'image'],
                ['data' => 'barcode'],
                ['data' => 'sku'],
                ['data' => 'name'],
                ['data' => 'category'],
                ['data' => 'unit'],
                ['data' => 'cost_price'],
                ['data' => 'selling_price'],
                ['data' => 'wholesale_price'],
                ['data' => 'estimated_profit'],
                ['data' => 'status'],
                ['data' => 'created_at'],
                ['data' => 'createdBy']
            ]"
        />
    </x-card>

@endsection
