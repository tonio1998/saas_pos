@extends('layouts.app')

@section('title', 'Receive Stock')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Receive Stock"
            subtitle="Add inventory to product"
        />

        <div class="row">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <form
                            method="POST"
                            action="{{ route('products.stock.receive.store', encrypt($product->id)) }}"
                        >
                            @csrf

                            <div class="mb-3">
                                <label class="form-label">
                                    Product
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{ $product->name }}"
                                    readonly
                                >
                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Current Stock
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        value="{{ number_format($product->stock_on_hand, 2) }}"
                                        readonly
                                    >
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Quantity Received
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        name="quantity"
                                        class="form-control"
                                        required
                                    >
                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Unit Cost
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="unit_cost"
                                        class="form-control"
                                    >
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Reference No.
                                    </label>

                                    <input
                                        type="text"
                                        name="reference_no"
                                        class="form-control"
                                    >
                                </div>

                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Remarks
                                </label>

                                <textarea
                                    name="remarks"
                                    rows="3"
                                    class="form-control"
                                ></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">

                                <a
                                    href="{{ route('products.index') }}"
                                    class="btn btn-light"
                                >
                                    Cancel
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    <i class="bi bi-box-arrow-in-down"></i>
                                    Receive Stock
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </div>

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        Product Details
                    </div>

                    <div class="card-body">

                        <table class="table table-sm">

                            <tr>
                                <th>Name</th>
                                <td>{{ $product->name }}</td>
                            </tr>

                            <tr>
                                <th>Barcode</th>
                                <td>{{ $product->barcode }}</td>
                            </tr>

                            <tr>
                                <th>SKU</th>
                                <td>{{ $product->sku }}</td>
                            </tr>

                            <tr>
                                <th>Cost Price</th>
                                <td>{{ number_format($product->cost_price, 2) }}</td>
                            </tr>

                            <tr>
                                <th>Selling Price</th>
                                <td>{{ number_format($product->selling_price, 2) }}</td>
                            </tr>

                            <tr>
                                <th>Stock</th>
                                <td>
                                <span class="badge bg-success">
                                    {{ number_format($product->stock_on_hand, 2) }}
                                </span>
                                </td>
                            </tr>

                        </table>

                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection
