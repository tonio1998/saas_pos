@extends('layouts.app')

@section('title', 'Add Cash Drawer')
@section('shortText', 'Create a new cash drawer')

@section('content')

    <x-page-header />

    <form
        action="{{ route('cashiering.cash-drawers.store') }}"
        method="POST"
    >

        @csrf

        <x-card>

            <div class="row g-3">

                <div class="col-md-4">
                    <x-form.input
                        name="drawer_name"
                        label="Drawer Name"
                        placeholder="e.g. Main Counter"
                        :value="old('drawer_name')"
                        required
                    />
                </div>

                <div class="col-md-4">
                    <x-form.input
                        name="drawer_code"
                        label="Drawer Code"
                        value="Auto Generated"
                        readonly
                        disabled
                    />
                </div>

                <div class="col-12">
                    <x-form.textarea
                        name="remarks"
                        label="Remarks"
                        rows="3"
                        placeholder="Optional remarks..."
                    />
                </div>

            </div>

        </x-card>

        <div class="d-flex justify-content-end gap-2 mt-3">

            <a
                href="{{ route('cashiering.cash-drawers.index') }}"
                class="btn btn-light"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-success"
            >
                <i class="bi bi-check-lg me-1"></i>
                Save Drawer
            </button>

        </div>

    </form>

@endsection
