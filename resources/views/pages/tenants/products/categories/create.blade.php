@extends('layouts.app')

@section('title', 'Add Category')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Add Category"
            subtitle="Create product category"
        />

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('products.categories.store') }}"
                >
                    @csrf

                    <div class="mb-3">

                        <label class="form-label">
                            Category Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
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
                            class="form-control"
                        >{{ old('description') }}</textarea>

                    </div>

                    <div class="d-flex justify-content-end gap-2">

                        <a
                            href="{{ route('products.categories.index') }}"
                            class="btn btn-light"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Save Category
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
