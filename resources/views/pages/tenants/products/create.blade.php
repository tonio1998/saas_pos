@extends('layouts.app')
@section('title', 'Product Management')
@section('content')
    @php  $isEdit = isset($product);@endphp

    <form method="POST" enctype="multipart/form-data"
        action="{{
            $isEdit
                ? route('products.update', encrypt($product->id))
                : route('products.store')
        }}"
    >

        @csrf

        @if($isEdit)
            @method('PUT')
        @endif

        <div class="page-shell">
            <div class=" border-0 bg-transparent shadow-none">
                <div class="card-body p-0">
                    <div class="page-glass-card">
                        <div class="section-header">
                            <div class="section-title-wrap">
                                <div class="section-icon">
                                    <i class="bi bi-box"></i>
                                </div>
                                <div>
                                    <div class="section-title">
                                        Product Information
                                    </div>
                                    <div class="section-subtitle">
                                        Basic product details
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-4">
                            <div class="col-xl-12 col-md-12 position-relative">
                                <x-form.group
                                    name="name"
                                    label="Product Name"
                                    required
                                >
                                    <x-form.input
                                        id="productName"
                                        name="name"
                                        value="{{ old('name', $product->name ?? '') }}"
                                        placeholder="Enter product name"
                                        autocomplete="off"
                                    />
                                </x-form.group>
                                <div
                                    id="productSuggestions"
                                    class="product-suggestions d-none"
                                ></div>
                            </div>
                            <x-form.group
                                name="barcode"
                                label="Barcode"
                                class="col-xl-3 col-md-6"
                            >
                                <x-form.input
                                    id="barcode"
                                    name="barcode"
                                    value="{{ old('barcode', $product->barcode ?? '') }}"
                                    placeholder="Enter barcode"
                                />
                            </x-form.group>
                            <x-form.group
                                name="sku"
                                label="SKU"
                                class="col-xl-3 col-md-6"
                            >
                                <x-form.input
                                    name="sku"
                                    id="sku"
                                    value="{{ old('sku', $product->sku ?? '') }}"
                                    placeholder="Enter SKU"
                                />
                            </x-form.group>
                            <x-form.group
                                name="category_id"
                                label="Category"
                                class="col-xl-4 col-md-6"
                                required
                            >
                                <select
                                    id="category_id"
                                    name="category_id"
                                    class="form-select"
                                >
                                    <option value="">
                                        Select Category
                                    </option>

                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}"
                                            {{
                                                old('category_id', $product->category_id ?? '') == $category->id? 'selected': ''
                                            }}
                                        >
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form.group>

                            <x-form.group
                                name="unit_id"
                                label="Unit"
                                class="col-xl-4 col-md-6"
                                required
                            >
                                <select
                                    id="unit_id"
                                    name="unit_id"
                                    class="form-select"
                                >
                                    <option value="">
                                        Select Unit
                                    </option>
                                    @foreach($units ?? [] as $unit)
                                        <option
                                            value="{{ $unit->id }}"
                                            {{
                                                old(
                                                    'unit_id',
                                                    $product->unit_id ?? ''
                                                ) == $unit->id
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form.group>

                            <x-form.group
                                name="description"
                                label="Description"
                                class="col-12"
                            >
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    class="form-control"
                                    placeholder="Enter product description"
                                >{{ old('description', $product->description ?? '') }}</textarea>
                            </x-form.group>
                        </div>
                    </div>

                    <div class="page-glass-card mt-4">
                        <div class="section-header">
                            <div class="section-title-wrap">
                                <div class="section-icon">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div>
                                    <div class="section-title">
                                        Pricing Information
                                    </div>
                                    <div class="section-subtitle">
                                        Product pricing configuration
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <x-form.group
                                name="cost_price"
                                label="Cost Price"
                                class="col-xl-4 col-md-6"
                                required
                            >
                                <x-form.input
                                    type="number"
                                    step="0.01"
                                    id="cost_price"
                                    min="0"
                                    name="cost_price"
                                    value="{{ old('cost_price', $product->cost_price ?? 0) }}"
                                />
                            </x-form.group>

                            <x-form.group
                                name="selling_price"
                                label="Selling Price"
                                class="col-xl-4 col-md-6"
                                required
                            >
                                <x-form.input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="selling_price"
                                    id="selling_price"
                                    value="{{ old('selling_price', $product->selling_price ?? 0) }}"
                                />
                            </x-form.group>

                            <x-form.group
                                name="wholesale_price"
                                label="Wholesale Price"
                                class="col-xl-4 col-md-6"
                            >
                                <x-form.input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="wholesale_price"
                                    value="{{ old('wholesale_price', $product->wholesale_price ?? 0) }}"
                                />
                            </x-form.group>

                        </div>

                    </div>

                    <div class="page-glass-card mt-4">
                        <div class="section-header">
                            <div class="section-title-wrap">
                                <div class="section-icon">
                                    <i class="bi bi-boxes"></i>
                                </div>
                                <div>
                                    <div class="section-title">
                                        Inventory Settings
                                    </div>
                                    <div class="section-subtitle">
                                        Inventory and stock configuration
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <x-form.group
                                name="reorder_level"
                                label="Reorder Level"
                                class="col-xl-4 col-md-6"
                            >
                                <x-form.input
                                    type="number"
                                    min="0"
                                    name="reorder_level"
                                    value="{{ old('reorder_level', $product->reorder_level ?? 0) }}"
                                />
                            </x-form.group>

                            <x-form.group
                                name="image"
                                label="Product Image"
                                class="col-xl-8 col-md-12"
                            >

                                <input
                                    type="file"
                                    name="image"
                                    class="form-control"
                                    accept="image/*"
                                >

                                @if(!empty($product?->image))
                                    <div class="mt-3">
                                        <img
                                            src="{{ asset($product->image) }}"
                                            class="img-thumbnail"
                                            style="max-height:120px"
                                        >
                                    </div>
                                @endif

                            </x-form.group>

                        </div>

                    </div>

                </div>

            </div>

            <div class="page-glass-card sticky-bottom">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-light border px-4">Cancel</a>
                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >

                        <i class="bi bi-check-circle me-1"></i>

                        {{
                            $isEdit
                                ? 'Update Product'
                                : 'Save Product'
                        }}

                    </button>

                </div>

            </div>

        </div>

    </form>

    @vite('resources/js/pages/pos/product-suggestions.js')
@endsection

@section('styles')

    <style>

        .sticky-bottom{
            position:sticky;
            bottom:0;
            z-index:10;
        }

        .is-invalid{
            border-color:#dc3545 !important;
        }

    </style>

@endsection
