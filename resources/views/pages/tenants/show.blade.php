@extends('layouts.sa')

@section('title', $school->name)

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">{{ $school->name }}</h3>
                <p class="text-muted mb-0">
                    {{ $school->code }}
                </p>
            </div>

            <div class="d-flex gap-2">
                <a
                    href="{{ route('store.edit', $school->id) }}"
                    class="btn btn-primary"
                >
                    Edit
                </a>

                <a
                    href="{{ route('store.index') }}"
                    class="btn btn-light border"
                >
                    Back
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            School Name
                        </label>

                        <div class="fw-semibold">
                            {{ $school->name }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            Alias Name
                        </label>

                        <div class="fw-semibold">
                            {{ $school->alias_name }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            School Code
                        </label>

                        <div class="fw-semibold">
                            {{ $school->code }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            Slug
                        </label>

                        <div class="fw-semibold">
                            {{ $school->slug }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label text-muted">
                            Address
                        </label>

                        <div class="fw-semibold">
                            {{ $school->address ?? 'N/A' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            Created At
                        </label>

                        <div class="fw-semibold">
                            {{ $school->created_at?->format('F d, Y h:i A') }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted">
                            Updated At
                        </label>

                        <div class="fw-semibold">
                            {{ $school->updated_at?->format('F d, Y h:i A') }}
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection
