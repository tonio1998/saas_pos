@extends('layouts.app')

@section('title','Employees Management')

@section('content')

    @php
        $isEdit = isset($teacher);
    @endphp

    <div class="container-fluid px-0">

        <x-page-header
            title="{{ $isEdit ? 'Edit Employee' : 'Add Employee' }}"
            subtitle="Registrar employee management"
        >

            <x-slot:action>

                <a
                    href="{{ route('employees.index') }}"
                    class="btn btn-light border"
                >
                    <i class="bi bi-arrow-left-short fs-5"></i>
                    Back
                </a>

            </x-slot:action>

        </x-page-header>

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

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                <div class="card-header bg-white border-bottom py-4 px-4">

                    <div class="d-flex align-items-center gap-3">

                        <div
                            class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                            style="width:56px;height:56px;"
                        >
                            <i class="bi bi-person-badge-fill fs-3 text-primary"></i>
                        </div>

                        <div>

                            <h4 class="mb-1 fw-bold">
                                {{
                                    $isEdit
                                        ? 'Edit Employee Record'
                                        : 'New Employee Record'
                                }}
                            </h4>

                            <div class="text-muted">
                                Manage employee information and registrar records
                            </div>

                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="border rounded-4 p-4 bg-light-subtle">

                        <div class="d-flex align-items-center gap-2 mb-4">

                            <i class="bi bi-person-lines-fill text-primary"></i>

                            <div class="fw-semibold">
                                Employee Information
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
                    <div
                        class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top sticky-bottom bg-white"
                        style="z-index:10;"
                    >

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

                            <i class="bi bi-check-circle"></i>

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

    </div>

@endsection

@section('styles')

    <style>

        .card{
            transition:.2s ease;
        }

        .form-control,
        .form-select{
            border-radius:12px;
            min-height:48px;
            border-color:#dbe1ea;
        }

        .form-control:focus,
        .form-select:focus{
            box-shadow:none;
            border-color:#0d6efd;
        }

        .bg-light-subtle{
            background:#f8fafc;
        }

        .sticky-bottom{
            position:sticky;
            bottom:0;
        }

    </style>

@endsection

@section('scripts')

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function(){

                const form =
                    document.querySelector('form');

                if(!form) return;

                const submitBtn =
                    form.querySelector(
                        'button[type="submit"]'
                    );

                const fields = {

                    FirstName:
                        form.querySelector(
                            '[name="FirstName"]'
                        ),

                    LastName:
                        form.querySelector(
                            '[name="LastName"]'
                        ),

                    PhoneNumber:
                        form.querySelector(
                            '[name="PhoneNumber"]'
                        ),

                };

                const showError = (
                    message,
                    field = null
                ) => {

                    if(field){

                        field.focus();

                        field.classList.add(
                            'is-invalid'
                        );

                        field.addEventListener(
                            'input',
                            () => {

                                field.classList.remove(
                                    'is-invalid'
                                );

                            },
                            { once:true }
                        );

                    }

                    Swal.fire({

                        icon:'warning',

                        title:'Validation Error',

                        text:message,

                        confirmButtonColor:'#0d6efd'

                    });

                };

                form.addEventListener(
                    'submit',
                    function(e){

                        e.preventDefault();

                        const FirstName =
                            fields.FirstName?.value.trim() || '';

                        const LastName =
                            fields.LastName?.value.trim() || '';

                        const Phone =
                            fields.PhoneNumber?.value.trim() || '';

                        if(!FirstName){

                            return showError(
                                'First name is required.',
                                fields.FirstName
                            );

                        }

                        if(!LastName){

                            return showError(
                                'Last name is required.',
                                fields.LastName
                            );

                        }

                        if(
                            Phone &&
                            !/^(\+639\d{9}|09\d{9})$/.test(Phone)
                        ){

                            return showError(
                                'Phone number format is invalid.',
                                fields.PhoneNumber
                            );

                        }

                        if(submitBtn){

                            submitBtn.disabled = true;

                            submitBtn.innerHTML = `

                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Processing...

                            `;

                        }

                        form.submit();

                    }
                );

            }
        );

    </script>

@endsection
