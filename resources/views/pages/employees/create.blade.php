@extends('layouts.app')

@section('title','Employees Management')

@section('content')

    @php
        $isEdit = isset($teacher);
    @endphp

        <form
            method="POST"
            action="{{
                $isEdit
                    ? route('employees.update',encrypt($teacher->id))
                    : route('employees.store')
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
                            <i class="bi bi-person-badge-fill"></i>
                        </div>

                        <div>

                            <div class="page-hero-title">
                                {{
                                    $isEdit
                                        ? 'Edit Employee Record'
                                        : 'New Employee Record'
                                }}
                            </div>

                            <div class="page-hero-subtitle">
                                Manage employee information and registrar records
                            </div>

                        </div>

                    </div>

                    <div class="page-hero-actions">

                        <div class="page-badge">
                            <i class="bi bi-circle-fill"></i>
                            Registrar Module
                        </div>

                    </div>

                </div>

                <div class="card border-0 bg-transparent shadow-none">

                    <div class="card-body p-0">

                        <div class="page-glass-card">

                            <div class="section-header">

                                <div class="section-title-wrap">

                                    <div class="section-icon">
                                        <i class="bi bi-person-lines-fill"></i>
                                    </div>

                                    <div>

                                        <div class="section-title">
                                            Employee Information
                                        </div>

                                        <div class="section-subtitle">
                                            Employee and personnel profile details
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="row g-4">

                                <x-form.group
                                    name="FirstName"
                                    label="First Name"
                                    class="col-xl-3 col-md-6"
                                    required
                                >

                                    <x-form.input
                                        name="FirstName"
                                        value="{{ old('FirstName',$teacher->FirstName ?? '') }}"
                                        placeholder="Enter first name"
                                    />

                                </x-form.group>

                                <x-form.group
                                    name="MiddleName"
                                    label="Middle Name"
                                    class="col-xl-3 col-md-6"
                                >

                                    <x-form.input
                                        name="MiddleName"
                                        value="{{ old('MiddleName',$teacher->MiddleName ?? '') }}"
                                        placeholder="Enter middle name"
                                    />

                                </x-form.group>

                                <x-form.group
                                    name="LastName"
                                    label="Last Name"
                                    class="col-xl-3 col-md-6"
                                    required
                                >

                                    <x-form.input
                                        name="LastName"
                                        value="{{ old('LastName',$teacher->LastName ?? '') }}"
                                        placeholder="Enter last name"
                                    />

                                </x-form.group>

                                <x-form.group
                                    name="Suffix"
                                    label="Suffix"
                                    class="col-xl-3 col-md-6"
                                >

                                    <select
                                        name="Suffix"
                                        class="form-select"
                                    >

                                        <option value="">
                                            Select suffix
                                        </option>

                                        @foreach(['Jr','Sr','III'] as $suffix)

                                            <option
                                                value="{{ $suffix }}"
                                                {{
                                                    old(
                                                        'Suffix',
                                                        $teacher->Suffix ?? ''
                                                    ) == $suffix
                                                        ? 'selected'
                                                        : ''
                                                }}
                                            >
                                                {{ $suffix }}
                                            </option>

                                        @endforeach

                                    </select>

                                </x-form.group>

                                <x-form.group
                                    name="PhoneNumber"
                                    label="Phone Number"
                                    class="col-xl-6 col-md-12"
                                    required
                                >

                                    <x-form.input
                                        name="PhoneNumber"
                                        value="{{ old('PhoneNumber',$teacher->PhoneNumber ?? '') }}"
                                        placeholder="+639XXXXXXXXX"
                                    />

                                </x-form.group>

                                <x-form.group
                                    name="Address"
                                    label="Address"
                                    class="col-xl-6 col-md-12"
                                >

                                    <x-form.input
                                        name="Address"
                                        value="{{ old('Address',$teacher->Address ?? '') }}"
                                        placeholder="Enter address"
                                    />

                                </x-form.group>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="page-glass-card sticky-bottom">

                    <div class="d-flex justify-content-end gap-2">

                        <a
                            href="{{ route('employees.index') }}"
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
                                    ? 'Update Employee'
                                    : 'Save Employee'
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
        }

        .is-invalid{
            border-color:#dc3545 !important;
        }

    </style>

@endsection

@section('scripts')
@endsection
