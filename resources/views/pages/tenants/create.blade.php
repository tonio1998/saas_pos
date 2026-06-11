@extends('layouts.sa')

@section('title', 'Tenant Management')

@section('content')

    @php
        $isEdit = isset($tenant);
    @endphp

    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{
            $isEdit
                ? route('sa.tenants.update', encrypt($tenant->id))
                : route('sa.tenants.store')
        }}"
    >

        @csrf

        @if($isEdit)
            @method('PUT')
        @endif
        <div class="page-shell">
            <div class="page-hero">
                <div class="page-hero-left">
                    <div class="page-hero-icon">
                        <i class="bi bi-buildings"></i>
                    </div>
                    <div>
                        <div class="page-hero-title">
                            {{
                                $isEdit
                                    ? 'Edit Tenant'
                                    : 'New Tenant'
                            }}

                        </div>

                        <div class="page-hero-subtitle">
                            Manage business information, subscriptions, and tenant access
                        </div>

                    </div>

                </div>

                <div class="page-hero-actions">
                    <div class="page-badge">
                        <i class="bi bi-circle-fill"></i>
                        SaaS Platform
                    </div>

                </div>

            </div>

            <div class="row g-4">

                <div class="col-12">

                    <div class="page-glass-card">

                        <div class="section-header">

                            <div class="section-title-wrap">

                                <div class="section-icon">
                                    <i class="bi bi-building"></i>
                                </div>

                                <div>

                                    <div class="section-title">
                                        Business Information
                                    </div>

                                    <div class="section-subtitle">
                                        Business profile and contact information
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-4">

                            <x-form.group
                                name="subscription_id"
                                label="Subscription Plan"
                                class="col-xl-6"
                                required
                            >

                                <select
                                    name="subscription_id"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Subscription
                                    </option>

                                    @foreach($subscriptions ?? [] as $subscription)

                                        <option
                                            value="{{ $subscription->id }}"
                                            {{
                                                old(
                                                    'subscription_id',
                                                    $tenant->subscription_id ?? ''
                                                ) == $subscription->id
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ $subscription->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </x-form.group>

                            <x-form.group
                                name="business_name"
                                label="Business Name"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="business_name"
                                    value="{{ old('business_name', $tenant->business_name ?? '') }}"
                                />

                            </x-form.group>

                            <x-form.group
                                name="business_code"
                                label="Business Code"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="business_code"
                                    value="{{ old('business_code', $tenant->business_code ?? '') }}"
                                    style="text-transform:uppercase"
                                />

                            </x-form.group>

                            <x-form.group
                                name="owner_name"
                                label="Owner Name"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="owner_name"
                                    value="{{ old('owner_name', $tenant->owner_name ?? '') }}"
                                />

                            </x-form.group>

                            <x-form.group
                                name="email"
                                label="Email Address"
                                class="col-xl-6"
                            >

                                <x-form.input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $tenant->email ?? '') }}"
                                />

                            </x-form.group>

                            <x-form.group
                                name="phone"
                                label="Phone Number"
                                class="col-xl-6"
                            >

                                <x-form.input
                                    name="phone"
                                    value="{{ old('phone', $tenant->phone ?? '') }}"
                                />

                            </x-form.group>

                            <x-form.group
                                name="address"
                                label="Business Address"
                                class="col-12"
                            >

                                <textarea
                                    name="address"
                                    rows="3"
                                    class="form-control"
                                >{{ old('address', $tenant->address ?? '') }}</textarea>

                            </x-form.group>

                        </div>

                    </div>

                </div>

                <div class="col-12">

                    <div class="page-glass-card">

                        <div class="section-header">

                            <div class="section-title-wrap">

                                <div class="section-icon">
                                    <i class="bi bi-calendar-check"></i>
                                </div>

                                <div>

                                    <div class="section-title">
                                        Subscription Information
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-4">

                            <x-form.group
                                name="subscription_start"
                                label="Subscription Start"
                                class="col-xl-3"
                            >

                                <input
                                    type="date"
                                    name="subscription_start"
                                    class="form-control"
                                    value="{{ old('subscription_start', isset($tenant) && $tenant->subscription_start ? $tenant->subscription_start->format('Y-m-d') : '') }}"
                                >

                            </x-form.group>

                            <x-form.group
                                name="subscription_end"
                                label="Subscription End"
                                class="col-xl-3"
                            >

                                <input
                                    type="date"
                                    name="subscription_end"
                                    class="form-control"
                                    value="{{ old('subscription_end', isset($tenant) && $tenant->subscription_end ? $tenant->subscription_end->format('Y-m-d') : '') }}"
                                >

                            </x-form.group>

                            <x-form.group
                                name="trial_ends_at"
                                label="Trial Ends At"
                                class="col-xl-3"
                            >

                                <input
                                    type="datetime-local"
                                    name="trial_ends_at"
                                    class="form-control"
                                    value="{{ old('trial_ends_at', isset($tenant) && $tenant->trial_ends_at ? $tenant->trial_ends_at->format('Y-m-d\TH:i') : '') }}"
                                >

                            </x-form.group>

                            <x-form.group
                                name="status"
                                label="Status"
                                class="col-xl-3"
                            >

                                <select
                                    name="status"
                                    class="form-select"
                                >

                                    @foreach([
                                        'active',
                                        'inactive',
                                        'locked',
                                        'unlocked'
                                    ] as $status)

                                        <option
                                            value="{{ $status }}"
                                            {{
                                                old(
                                                    'status',
                                                    $tenant->status ?? 'active'
                                                ) == $status
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ ucfirst($status) }}
                                        </option>

                                    @endforeach

                                </select>

                            </x-form.group>

                        </div>

                    </div>

                </div>

                <div class="col-12">

                    <div class="page-glass-card">

                        <div class="section-header">

                            <div class="section-title-wrap">

                                <div class="section-icon">
                                    <i class="bi bi-image"></i>
                                </div>

                                <div>

                                    <div class="section-title">
                                        Branding
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-4">

                            <x-form.group
                                name="logo"
                                label="Business Logo"
                                class="col-12"
                            >

                                <input
                                    type="file"
                                    name="logo"
                                    class="form-control"
                                    accept="image/*"
                                >

                            </x-form.group>

                            @if(
                                $isEdit &&
                                !empty($tenant->logo)
                            )

                                <div class="col-12">

                                    <img
                                        src="{{ asset('storage/' . $tenant->logo) }}"
                                        class="img-thumbnail"
                                        style="max-height:150px"
                                    >

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            </div>

            <div class="page-glass-card sticky-bottom mt-4">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('sa.tenants.index') }}"
                        class="btn btn-light border px-4"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >
                        <i class="bi bi-check-circle me-1"></i>

                        {{
                            $isEdit
                                ? 'Update Tenant'
                                : 'Save Tenant'
                        }}
                    </button>

                </div>

            </div>

        </div>

    </form>

@endsection

@section('styles')

    <style>

        .sticky-bottom{
            position:sticky;
            bottom:0;
            z-index:10;
            backdrop-filter:blur(12px);
        }

        .is-invalid{
            border-color:#dc3545 !important;
        }

    </style>

@endsection
