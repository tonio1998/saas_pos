<?php
use App\Helpers\StatusHelper;
?>

@extends('layouts.app')
@section('title', 'View Cash Drawer')
@section('shortText', 'Cash drawer details')
@section('content')

    <x-page-header>

        <x-slot:action>
            <a
                href="{{ route('cashiering.cash-drawers.edit', encryptId($drawer->id)) }}"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil-square"></i>
                Edit
            </a>
        </x-slot:action>

    </x-page-header>

    <x-card>

        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Drawer Code
                </label>

                <div class="fw-semibold">
                    {{ $drawer->drawer_code }}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Drawer Name
                </label>

                <div class="fw-semibold">
                    {{ $drawer->drawer_name }}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Status
                </label>

                <div>
                    {!! StatusHelper::badge($drawer->status) !!}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Created At
                </label>

                <div>
                    {{ StatusHelper::formatDateTime($drawer->created_at) }}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Created By
                </label>

                <div>
                    {{ $drawer->creator?->name ?? '-' }}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Updated By
                </label>

                <div>
                    {{ $drawer->updater?->name ?? '-' }}
                </div>
            </div>

        </div>

    </x-card>

@endsection
