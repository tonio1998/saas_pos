@extends('layouts.sa')

@section('title','Permissions Management')

@section('content')

    @php
        $isEdit = isset($permission);
    @endphp

        <form
            method="POST"
            action="{{
                $isEdit
                    ? route('permissions.update',encrypt($permission->id))
                    : route('permissions.store')
            }}"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="page-shell">

                <div class="page-hero permission-hero">

                    <div class="page-hero-left">

                        <div class="permission-hero-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>

                        <div>

                            <div class="page-hero-title">
                                {{
                                    $isEdit
                                        ? 'Edit Permission'
                                        : 'Create Permission'
                                }}
                            </div>

                            <div class="page-hero-subtitle">
                                Configure system access and authorization rules
                            </div>

                        </div>

                    </div>

                    <div class="permission-status">

                        <span class="permission-status-dot"></span>

                        Active Security Layer

                    </div>

                </div>

                <div class="permission-layout">

                    <div class="permission-main-card">

                        <div class="permission-section-header">

                            <div class="permission-section-icon">
                                <i class="bi bi-key-fill"></i>
                            </div>

                            <div>

                                <div class="permission-section-title">
                                    Permission Information
                                </div>

                                <div class="permission-section-subtitle">
                                    Define permission identifiers and administrator notes
                                </div>

                            </div>

                        </div>

                        <div class="row g-4 mt-1">

                            <x-form.group
                                name="name"
                                label="Permission Name"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="name"
                                    value="{{ old('name',$permission->name ?? '') }}"
                                    placeholder="ex: create users"
                                />

                                <div class="field-helper">
                                    Recommended:
                                    create users,
                                    update students,
                                    delete reports
                                </div>

                            </x-form.group>

                            <x-form.group
                                name="details"
                                label="Permission Details"
                                class="col-xl-6"
                                required
                            >

                                <x-form.input
                                    name="details"
                                    value="{{ old('details',$permission->details ?? '') }}"
                                    placeholder="Describe permission purpose"
                                />

                                <div class="field-helper">
                                    Internal description for system administrators
                                </div>

                            </x-form.group>

                        </div>

                    </div>

                    <div class="permission-side-card">

                        <div class="permission-side-header">

                            <div class="permission-side-icon">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>

                            <div>

                                <div class="permission-side-title">
                                    Best Practices
                                </div>

                                <div class="permission-side-subtitle">
                                    Standard permission naming
                                </div>

                            </div>

                        </div>

                        <div class="permission-example-list">

                            <div class="permission-example">
                                <i class="bi bi-check2-circle"></i>
                                view users
                            </div>

                            <div class="permission-example">
                                <i class="bi bi-check2-circle"></i>
                                create users
                            </div>

                            <div class="permission-example">
                                <i class="bi bi-check2-circle"></i>
                                update students
                            </div>

                            <div class="permission-example">
                                <i class="bi bi-check2-circle"></i>
                                export reports
                            </div>

                            <div class="permission-example">
                                <i class="bi bi-check2-circle"></i>
                                manage roles
                            </div>

                        </div>

                    </div>

                </div>

                <div class="page-glass-card sticky-bottom">

                    <div class="d-flex justify-content-end gap-2">

                        <a
                            href="{{ route('permissions.index') }}"
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
                                    ? 'Update Permission'
                                    : 'Save Permission'
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

        .form-helper{
            margin-top:.45rem;

            font-size:.76rem;

            color:#64748b;
        }

        .permission-tips{
            padding:1rem 1.1rem;

            border-radius:16px;

            background:
                linear-gradient(
                    135deg,
                    rgba(59,130,246,.06),
                    rgba(37,99,235,.03)
                );

            border:1px dashed rgba(59,130,246,.18);
        }

        .permission-tip-title{
            font-size:.82rem;
            font-weight:700;

            color:#0f172a;

            margin-bottom:.8rem;
        }

        .permission-tags{
            display:flex;
            flex-wrap:wrap;
            gap:.65rem;
        }

        .permission-tag{
            padding:.55rem .9rem;

            border-radius:999px;

            background:#fff;

            border:1px solid #dbeafe;

            color:#2563eb;

            font-size:.78rem;
            font-weight:600;

            transition:.18s ease;
        }

        .permission-tag:hover{
            transform:translateY(-1px);

            background:#eff6ff;
        }

        .is-invalid{
            border-color:#dc3545 !important;
        }

        @media(max-width:768px){

            .permission-tags{
                gap:.5rem;
            }

            .permission-tag{
                width:100%;
                text-align:center;
            }

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

                const permissionName =
                    form.querySelector(
                        '[name="name"]'
                    );

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

                        const permission =
                            permissionName?.value.trim() || '';

                        if(!permission){

                            return showError(
                                'Permission name is required.',
                                permissionName
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
