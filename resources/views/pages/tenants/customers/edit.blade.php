@extends('layouts.app')
@section('title', 'Edit Customer')
@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('customers.update', encrypt($customer->id)) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer Name</label>
                            <input
                                type="text"
                                name="CustomerName"
                                class="form-control @error('CustomerName') is-invalid @enderror"
                                value="{{ old('CustomerName', $customer->CustomerName) }}"
                                required
                            >
                            @error('CustomerName')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input
                                type="text"
                                name="company_name"
                                class="form-control"
                                value="{{ old('company_name', $customer->company_name) }}"
                            >
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Type</label>
                            <select name="customer_type" class="form-select" required>
                                <option
                                    value="regular"
                                    @selected(old('customer_type', $customer->customer_type) == 'regular')
                                >
                                    Regular
                                </option>

                                <option
                                    value="business"
                                    @selected(old('customer_type', $customer->customer_type) == 'business')
                                >
                                    Business
                                </option>

                                <option
                                    value="senior"
                                    @selected(old('customer_type', $customer->customer_type) == 'senior')
                                >
                                    Senior Citizen
                                </option>

                                <option
                                    value="pwd"
                                    @selected(old('customer_type', $customer->customer_type) == 'pwd')
                                >
                                    PWD
                                </option>

                                <option
                                    value="credit"
                                    @selected(old('customer_type', $customer->customer_type) == 'credit')
                                >
                                    Credit Customer
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Mobile Number
                            </label>

                            <input
                                type="text"
                                name="mobile_number"
                                class="form-control"
                                value="{{ old('mobile_number', $customer->mobile_number) }}"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', $customer->email) }}"
                            >

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="CustomerAddress"
                            rows="3"
                            class="form-control"
                        >{{ old('CustomerAddress', $customer->CustomerAddress) }}</textarea>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Discount (%)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="discount_percent"
                                class="form-control"
                                value="{{ old('discount_percent', $customer->discount_percent) }}"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Credit Limit
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="credit_limit"
                                class="form-control"
                                value="{{ old('credit_limit', $customer->credit_limit) }}"
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
                        >{{ old('remarks', $customer->remarks) }}</textarea>

                    </div>

                    <div class="d-flex justify-content-end gap-2">

                        <a
                            href="{{ route('customers.index') }}"
                            class="btn btn-light"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save"></i>
                            Update Customer
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
