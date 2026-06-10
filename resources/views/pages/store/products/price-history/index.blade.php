@extends('layouts.app')

@section('title', 'Price History')

@section('content')

    <x-page-header
        title="Price History"
        subtitle="Track and monitor product price changes"
    />

    <x-card>
        <x-datatable
            id="priceHistoryTable"
            :columns="[
                'Actions',
                'Product',
                'Price Changes',
                'Remarks',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('products.price-history.data')"
            :datatableColumns="[
                [
                    'data' => 'actions',
                    'name' => 'actions',
                    'orderable' => false,
                    'searchable' => false,
                    'width' => '90px'
                ],
                [
                    'data' => 'product',
                    'name' => 'product.name'
                ],
                [
                    'data' => 'price_changes',
                    'name' => 'price_changes',
                    'orderable' => false,
                    'searchable' => false,
                    'width' => '450px'
                ],
                [
                    'data' => 'remarks',
                    'name' => 'remarks'
                ],
                [
                    'data' => 'status',
                    'name' => 'status',
                    'width' => '100px'
                ],
                [
                    'data' => 'created_at',
                    'name' => 'created_at',
                    'width' => '140px'
                ],
                [
                    'data' => 'createdBy',
                    'name' => 'createdBy.name',
                    'width' => '180px'
                ]
            ]"
        />
    </x-card>

@endsection
