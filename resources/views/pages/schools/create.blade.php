@extends('layouts.sa')

@section('title', 'School Management')

@section('content')

    @php
        $isEdit = isset($school);
    @endphp

    <form
        method="POST"
        enctype="multipart/form-data"
        action="{{
        $isEdit
            ? route('sa.schools.update', encrypt($school->id))
            : route('sa.schools.store')
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
                                    ? 'Edit School'
                                    : 'New School'
                            }}

                        </div>

                        <div class="page-hero-subtitle">
                            Manage tenant schools, branding, attendance, and SMS configuration
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
                                        School Information
                                    </div>

                                    <div class="section-subtitle">
                                        Tenant branding and school profile
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-4">

                            <x-form.group
                                name="SystemTitle"
                                label="System Title"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="SystemTitle"
                                    value="{{ old('SystemTitle', $school->SystemTitle ?? '') }}"
                                    placeholder="SAFETRACK"
                                />

                            </x-form.group>

                            <x-form.group
                                name="SchoolName"
                                label="School Name"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="SchoolName"
                                    value="{{ old('SchoolName', $school->SchoolName ?? '') }}"
                                    placeholder="Surigao National School"
                                />

                            </x-form.group>

                            <x-form.group
                                name="SchoolCode"
                                label="School Code"
                                class="col-xl-3"
                            >

                                <x-form.input
                                    name="SchoolCode"
                                    value="{{ old('SchoolCode', $school->SchoolCode ?? '') }}"
                                    placeholder="XXXXXXX"
                                    style="text-transform:uppercase"
                                />

                            </x-form.group>
                            <x-form.group
                                name="alias_name"
                                label="Alias Name"
                                class="col-xl-3"
                            >

                                <x-form.input
                                    name="alias_name"
                                    value="{{ old('alias_name', $school->alias_name ?? '') }}"
                                    placeholder="SNSU"
                                />

                            </x-form.group>

                            <x-form.group
                                name="EducationLevel"
                                label="Education Level"
                                class="col-xl-3"
                            >

                                <select
                                    name="EducationLevel"
                                    class="form-select"
                                >

                                    @foreach([
                                        'JHS',
                                        'SHS',
                                        'INTEGRATED'
                                    ] as $level)

                                        <option
                                            value="{{ $level }}"
                                            {{
                                                old(
                                                    'EducationLevel',
                                                    $school->EducationLevel ?? 'INTEGRATED'
                                                ) == $level
                                                    ? 'selected'
                                                    : ''
                                            }}
                                        >
                                            {{ $level }}
                                        </option>

                                    @endforeach

                                </select>

                            </x-form.group>

                            <x-form.group
                                name="EmailAddress"
                                label="Email Address"
                                class="col-xl-6"
                            >

                                <x-form.input
                                    type="text"
                                    name="EmailAddress"
                                    value="{{ old('EmailAddress', $school->EmailAddress ?? '') }}"
                                    placeholder="school@email.com"
                                />

                            </x-form.group>

                            <x-form.group
                                name="ContactNumber"
                                label="Contact Number"
                                class="col-xl-6"
                            >

                                <x-form.input
                                    name="ContactNumber"
                                    value="{{ old('ContactNumber', $school->ContactNumber ?? '') }}"
                                    placeholder="+639123456789"
                                />

                            </x-form.group>

                            <x-form.group
                                name="Region"
                                label="Region"
                                class="col-xl-6"
                            >

                                <x-form.input
                                    name="Region"
                                    value="{{ old('Region', $school->Region ?? '') }}"
                                />

                            </x-form.group>

                            <x-form.group
                                name="Division"
                                label="Division"
                                class="col-xl-6"
                            >

                                <x-form.input
                                    name="Division"
                                    value="{{ old('Division', $school->Division ?? '') }}"
                                />

                            </x-form.group>

                            <x-form.group
                                name="Address"
                                label="Address"
                                class="col-12"
                            >

                            <textarea
                                name="Address"
                                rows="3"
                                class="form-control"
                            >{{ old('Address', $school->Address ?? '') }}</textarea>

                            </x-form.group>

                        </div>

                    </div>

                </div>

                <div class="col-12">

                    <div class="page-glass-card">

                        <div class="section-header">

                            <div class="section-title-wrap">

                                <div class="section-icon">
                                    <i class="bi bi-palette"></i>
                                </div>

                                <div>

                                    <div class="section-title">
                                        Branding & Appearance
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-4">

                            <x-form.group
                                name="ThemeColor"
                                label="Theme Color"
                                class="col-xl-3"
                            >

                                <input
                                    type="color"
                                    name="ThemeColor"
                                    class="form-control form-control-color w-100"
                                    value="{{ old('ThemeColor', $school->ThemeColor ?? '#004D1A') }}"
                                >

                            </x-form.group>

                            <x-form.group
                                name="Logo"
                                label="School Logo"
                                class="col-xl-9"
                            >

                                <input
                                    type="file"
                                    name="Logo"
                                    class="form-control"
                                    accept="image/*"
                                >

                            </x-form.group>

                        </div>

                    </div>

                </div>

                <div class="col-12">

                    <div class="page-glass-card">

                        <div class="section-header">

                            <div class="section-title-wrap">

                                <div class="section-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>

                                <div>

                                    <div class="section-title">
                                        Attendance Configuration
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="row g-4">

                            <x-form.group
                                name="OfficialTimeIn"
                                label="Official Time In"
                                class="col-xl-4"
                            >

                                <input
                                    type="time"
                                    name="OfficialTimeIn"
                                    class="form-control"
                                    value="{{ old('OfficialTimeIn', $school->OfficialTimeIn ?? '07:00') }}"
                                >

                            </x-form.group>

                            <x-form.group
                                name="OfficialTimeOut"
                                label="Official Time Out"
                                class="col-xl-4"
                            >

                                <input
                                    type="time"
                                    name="OfficialTimeOut"
                                    class="form-control"
                                    value="{{ old('OfficialTimeOut', $school->OfficialTimeOut ?? '17:00') }}"
                                >

                            </x-form.group>

                            <x-form.group
                                name="LateGraceMinutes"
                                label="Late Grace Minutes"
                                class="col-xl-4"
                            >

                                <x-form.input
                                    type="number"
                                    name="LateGraceMinutes"
                                    value="{{ old('LateGraceMinutes', $school->LateGraceMinutes ?? 15) }}"
                                />

                            </x-form.group>

                        </div>

                    </div>

                </div>

            </div>

            <div class="page-glass-card sticky-bottom mt-4">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('sa.schools.index') }}"
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
                                ? 'Update School'
                                : 'Save School'
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
