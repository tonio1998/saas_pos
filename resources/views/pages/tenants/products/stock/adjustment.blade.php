@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Stock Adjustment"
            subtitle="{{ $product->name }}"
        />

        <div class="row">

            <div class="col-lg-8">

                <div class="card border-0 shadow-sm">

                    <div class="card-body">

                        <form
                            method="POST"
                            action="{{ route('products.stock.adjustment.store', encrypt($product->id)) }}"
                        >
                            @csrf

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Current Stock
                                    </label>

                                    <input
                                        type="number"
                                        id="current_stock"
                                        class="form-control"
                                        value="{{ $product->stock_on_hand }}"
                                        readonly
                                    >

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Actual Counted Stock
                                    </label>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="actual_stock"
                                        id="actual_stock"
                                        class="form-control @error('actual_stock') is-invalid @enderror"
                                        value="{{ old('actual_stock') }}"
                                        required
                                    >

                                    @error('actual_stock')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Stock Difference
                                </label>

                                <input
                                    type="text"
                                    id="adjustment_qty"
                                    class="form-control fw-bold text-center"
                                    readonly
                                >

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Reason
                                </label>

                                <select
                                    name="reason"
                                    class="form-select @error('reason') is-invalid @enderror"
                                    required
                                >
                                    <option value="">
                                        Select Reason
                                    </option>

                                    <option
                                        value="Physical Count"
                                        @selected(old('reason') === 'Physical Count')
                                    >
                                        Physical Count
                                    </option>

                                    <option
                                        value="Damaged Item"
                                        @selected(old('reason') === 'Damaged Item')
                                    >
                                        Damaged Item
                                    </option>

                                    <option
                                        value="Expired Item"
                                        @selected(old('reason') === 'Expired Item')
                                    >
                                        Expired Item
                                    </option>

                                    <option
                                        value="Lost Item"
                                        @selected(old('reason') === 'Lost Item')
                                    >
                                        Lost Item
                                    </option>

                                    <option
                                        value="Inventory Correction"
                                        @selected(old('reason') === 'Inventory Correction')
                                    >
                                        Inventory Correction
                                    </option>

                                </select>

                                @error('reason')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Remarks
                                </label>

                                <textarea
                                    name="remarks"
                                    rows="4"
                                    class="form-control @error('remarks') is-invalid @enderror"
                                >{{ old('remarks') }}</textarea>

                                @error('remarks')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror

                            </div>

                            <div class="d-flex justify-content-end gap-2">

                                <a
                                    href="{{ route('products.stock.history', encrypt($product->id)) }}"
                                    class="btn btn-light"
                                >
                                    Cancel
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-warning"
                                >
                                    <i class="bi bi-sliders"></i>
                                    Save Adjustment
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-header">
                        Product Information
                    </div>

                    <div class="card-body">

                        <table class="table table-sm mb-0">

                            <tr>
                                <th>Name</th>
                                <td>{{ $product->name }}</td>
                            </tr>

                            <tr>
                                <th>Barcode</th>
                                <td>{{ $product->barcode ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>SKU</th>
                                <td>{{ $product->sku ?: '-' }}</td>
                            </tr>

                            <tr>
                                <th>Current Stock</th>
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
