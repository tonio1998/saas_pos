@extends('layouts.app')

@section('title', 'Product Masterlist')

@section('content')

    <x-page-header>
        <x-slot:action>
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Add Product
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
        <x-datatable
            id="productssTables"
            :columns="[
                'Actions',
                'Image',
                'Barcode',
                'SKU',
                'Product Name',
                'Stock',
                'Category',
                'Unit',
                'Cost Price',
                'Selling Price',
                'Wholesale Price',
                'Estimated Profit',
            ]"
            :ajax="route('products.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'image'],
                ['data' => 'barcode'],
                ['data' => 'sku'],
                ['data' => 'name'],
                ['data' => 'stock_on_hand'],
                ['data' => 'category'],
                ['data' => 'unit'],
                ['data' => 'cost_price'],
                ['data' => 'selling_price'],
                ['data' => 'wholesale_price'],
                ['data' => 'estimated_profit'],
            ]"
        />
    </x-card>

@endsection
