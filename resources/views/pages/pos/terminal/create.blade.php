@extends('layouts.app')
@section('title', 'Create POS Terminal')
@section('shortText', 'Register a new POS terminal')
@section('content')
    <x-page-header />
    <form
        action="{{ route('terminal.store') }}"
        method="POST"
    >

        @csrf

        <div class="row justify-content-center">

            <div class="col-xl-8">

                <x-card>
                    @if ($errors->any())

                        <div class="alert alert-danger">

                            <div class="fw-semibold mb-2">
                                Please correct the following errors:
                            </div>

                            <ul class="mb-0 ps-3">

                                @foreach ($errors->all() as $error)

                                    <li>{{ $error }}</li>

                                @endforeach

                            </ul>

                        </div>

                    @endif
                    <div class="d-flex align-items-center mb-4">

                        <div
                            class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                            style="width:64px;height:64px;"
                        >
                            <i class="bi bi-pc-display-horizontal fs-2 text-primary"></i>
                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">
                                Register POS Terminal
                            </h4>

                            <div class="text-muted">
                                Configure a terminal that will be used for cashiering transactions.
                            </div>

                        </div>

                    </div>

                    <div class="border rounded-3 bg-light p-3 mb-4">

                        <div class="row g-3 align-items-center">

                            <div class="col-md-3">

                                <small class="text-muted d-block">
                                    Terminal Code
                                </small>

                                <div class="fw-semibold">
                                    Auto Generated
                                </div>

                            </div>

                            <div class="col-md-5">

                                <x-form.input
                                    name="terminal_name"
                                    label="Terminal Name"
                                    placeholder="e.g. Terminal 1"
                                    :value="old('terminal_name')"
                                    required
                                />

                            </div>

                            <div class="col-md-4">

                                <x-form.select
                                    name="drawer_id"
                                    label="Cash Drawer"
                                    ajax="{{ route('select2.cash-drawers') }}"
                                    required
                                >
                                </x-form.select>

                                @error('drawer_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>

                        </div>

                    </div>

                    <div class="row g-4">

                        <div class="col-md-4">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Terminal Status
                            </label>

                            <select
                                id="status"
                                name="status"
                                class="form-select"
                                required
                            >

                                <option
                                    value="active"
                                    @selected(old('status', 'active') == 'active')
                                >
                                    Active
                                </option>

                                <option
                                    value="inactive"
                                    @selected(old('status') == 'inactive')
                                >
                                    Inactive
                                </option>

                                <option
                                    value="maintenance"
                                    @selected(old('status') == 'maintenance')
                                >
                                    Maintenance
                                </option>

                            </select>

                            <div class="form-text">
                                Only active terminals are available for POS transactions.
                            </div>

                        </div>

                        <div class="col-md-8">

                            <x-form.textarea
                                name="remarks"
                                label="Remarks"
                                rows="4"
                                placeholder="Optional remarks..."
                            />

                        </div>

                    </div>

                </x-card>

                <div class="d-flex justify-content-between align-items-center mt-3">

                    <small class="text-muted">

                        <i class="bi bi-info-circle me-1"></i>

                        A terminal can only be assigned to one cash drawer.

                    </small>

                    <div class="d-flex gap-2">

                        <a
                            href="{{ route('terminal.index') }}"
                            class="btn btn-light px-4"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary px-4"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Create Terminal
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </form>

@endsection
