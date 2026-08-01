@extends('layouts.app')

@section('title', 'Open Cash Shift')
@section('shortText', 'Start a new cash shift')

@section('content')

    <x-page-header />

    <div class="row justify-content-center">

        <div class="col-xl-6 col-lg-7 col-md-9">

            <form
                action="{{ route('cashiering.cash-shifts.store') }}"
                method="POST"
            >
                @csrf

                <input
                    type="hidden"
                    name="drawer_id"
                    value="{{ $drawer->id }}"
                >

                <x-card class="border-0 shadow-sm">

                    <div class="text-center mb-4">

                        <div class="display-6 text-success mb-2">
                            <i class="bi bi-cash-stack"></i>
                        </div>

                        <h4 class="fw-bold mb-1">
                            Open Cash Shift
                        </h4>

                        <p class="text-muted small mb-0">
                            Confirm the drawer details and enter the opening cash.
                        </p>

                    </div>

                    <div class="border rounded-3 bg-light p-3 mb-4">

                        <div class="row g-2 small">

                            <div class="col-6 text-muted">
                                Cash Drawer
                            </div>

                            <div class="col-6 text-end fw-semibold">
                                {{ $drawer->drawer_name }}
                            </div>

                            <div class="col-6 text-muted">
                                Drawer Code
                            </div>

                            <div class="col-6 text-end fw-semibold">
                                {{ $drawer->drawer_code }}
                            </div>

                            <div class="col-6 text-muted">
                                Cashier
                            </div>

                            <div class="col-6 text-end fw-semibold">
                                {{ auth()->user()->name }}
                            </div>

                            <div class="col-6 text-muted">
                                Opening Time
                            </div>

                            <div class="col-6 text-end fw-semibold">
                                {{ now()->format('M d, Y h:i A') }}
                            </div>

                        </div>

                    </div>

                    <div class="mx-auto mb-4" style="max-width: 320px;">

                        <x-form.input
                            name="opening_cash"
                            label="Opening Cash"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            :value="old('opening_cash', 0)"
                            class="form-control-lg text-center fw-bold fs-4"
                            required
                        />

                        <div class="form-text text-center">
                            Enter the cash currently inside the drawer.
                        </div>

                    </div>

                    <x-form.textarea
                        name="remarks"
                        label="Remarks"
                        rows="3"
                        placeholder="Optional remarks..."
                    />

                    <div class="d-flex justify-content-center gap-2 mt-4">

                        <a
                            href="{{ route('cashiering.cash-shifts.index') }}"
                            class="btn btn-light px-4"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-success px-4"
                        >
                            <i class="bi bi-unlock me-1"></i>
                            Open Shift
                        </button>

                    </div>

                </x-card>

            </form>

        </div>

    </div>

@endsection
