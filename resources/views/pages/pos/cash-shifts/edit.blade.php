@extends('layouts.app')

@section('title', 'Edit Cash Drawer')
@section('shortText', 'Update cash drawer')

@section('content')

    <x-page-header />

    <form
        action="{{ route('cashiering.cash-drawers.update', encrypt($drawer->id)) }}"
        method="POST"
    >

        @csrf
        @method('PUT')

        <x-card>

            <div class="row g-3">

                <div class="col-md-4">
                    <x-form.input
                        name="drawer_name"
                        label="Drawer Name"
                        placeholder="e.g. Main Counter"
                        :value="old('drawer_name', $drawer->drawer_name)"
                        required
                    />
                </div>

                <div class="col-md-4">
                    <x-form.input
                        name="drawer_code"
                        label="Drawer Code"
                        value="{{ $drawer->drawer_code }}"
                        readonly
                        disabled
                    />
                </div>

                <div class="col-12">
                    <x-form.textarea
                        name="remarks"
                        label="Remarks"
                        rows="3"
                        value="{{ $drawer->remarks }}"
                        placeholder="Optional remarks..."
                    />
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
                class="btn btn-primary"
            >
                <i class="bi bi-check-lg"></i>
                Update Drawer
            </button>

        </div>

    </form>

@endsection
