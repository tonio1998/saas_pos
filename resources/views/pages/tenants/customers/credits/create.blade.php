@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Add Customer"
            subtitle="Create customer profile"
        />

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('customers.store') }}"
                >
                    @csrf

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                First Name
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                class="form-control @error('first_name') is-invalid @enderror"
                                value="{{ old('first_name') }}"
                                required
                            >

                            @error('first_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Middle Name
                            </label>

                            <input
                                type="text"
                                name="middle_name"
                                class="form-control @error('middle_name') is-invalid @enderror"
                                value="{{ old('middle_name') }}"
                            >

                            @error('middle_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Last Name
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                class="form-control @error('last_name') is-invalid @enderror"
                                value="{{ old('last_name') }}"
                                required
                            >

                            @error('last_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Company Name
                            </label>

                            <input
                                type="text"
                                name="company_name"
                                class="form-control"
                                value="{{ old('company_name') }}"
                            >

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Customer Type
                            </label>

                            <select
                                name="customer_type"
                                class="form-select"
                                required
                            >
                                <option value="regular">
                                    Regular
                                </option>

                                <option value="business">
                                    Business
                                </option>

                                <option value="senior">
                                    Senior Citizen
                                </option>

                                <option value="pwd">
                                    PWD
                                </option>

                                <option value="credit">
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
                                value="{{ old('mobile_number') }}"
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
                                value="{{ old('email') }}"
                            >

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            rows="3"
                            class="form-control"
                        >{{ old('address') }}</textarea>

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
                                value="{{ old('discount_percent', 0) }}"
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
                                value="{{ old('credit_limit', 0) }}"
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
                        >{{ old('remarks') }}</textarea>

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
                            Save Customer
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
