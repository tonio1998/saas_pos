@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')

    <div class="container-fluid">

        <x-page-header
            title="Edit Category"
            subtitle="{{ $category->name }}"
        />

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('products.categories.update', encrypt($category->id)) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="mb-3">

                        <label class="form-label">
                            Category Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name', $category->name) }}"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="form-control"
                        >{{ old('description', $category->description) }}</textarea>

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
                            Update Category
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
