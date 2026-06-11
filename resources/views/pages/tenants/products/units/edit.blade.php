@extends('layouts.app')

@section('title', 'Edit Unit')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Edit Unit"
            subtitle="{{ $unit->name }}"
        />

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('products.units.update', encrypt($unit->id)) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label class="form-label">
                            Unit Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $unit->name) }}"
                            placeholder="e.g. Piece, Box, Pack, Bottle"
                            required
                        >

                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Optional description"
                        >{{ old('description', $unit->description) }}</textarea>

                        @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <div class="d-flex justify-content-end gap-2">

                        <a
                            href="{{ route('products.units.index') }}"
                            class="btn btn-light"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save"></i>
                            Update Unit
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
