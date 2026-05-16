@extends('layouts.app')

@section('title','Roles Management')

@section('content')

    @php
        $isEdit = isset($role);
    @endphp

    <div class="page-shell">

        <div class="page-hero role-hero">

            <div class="page-hero-left">

                <div class="role-hero-icon">
                    <i class="bi bi-person-badge-fill"></i>
                </div>

                <div>

                    <div class="page-hero-title">
                        {{
                            $isEdit
                                ? 'Edit Role'
                                : 'Create Role'
                        }}
                    </div>

                    <div class="page-hero-subtitle">
                        Manage system roles and permission access
                    </div>

                </div>

            </div>

            <div class="page-hero-actions">

                <a
                    href="{{ route('roles.index') }}"
                    class="btn btn-light border"
                >
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>

            </div>

        </div>

        <form
            id="roleForm"
            method="POST"
            action="{{
                $isEdit
                    ? route('roles.update',encrypt($role->id))
                    : route('roles.store')
            }}"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="page-glass-card">

                <div class="section-header">

                    <div class="section-title-wrap">

                        <div class="section-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>

                        <div>

                            <div class="section-title">
                                Role Information
                            </div>

                            <div class="section-subtitle">
                                Configure role details and access permissions
                            </div>

                        </div>

                    </div>

                </div>

                <div class="row g-4">

                    <x-form.group
                        name="name"
                        label="Role Name"
                        class="col-xl-6"
                        required
                    >

                        <x-form.input
                            name="name"
                            value="{{ old('name',$role->name ?? '') }}"
                            placeholder="Enter role name"
                        />

                        <div class="field-helper">
                            Example:
                            Administrator,
                            Registrar,
                            Faculty
                        </div>

                    </x-form.group>

                    <x-form.group
                        name="description"
                        label="Description"
                        class="col-xl-6"
                    >

                        <x-form.input
                            name="details"
                            value="{{ old('details',$role->details ?? '') }}"
                            placeholder="Role description"
                        />

                        <div class="field-helper">
                            Internal notes for administrators
                        </div>

                    </x-form.group>

                </div>

            </div>

            <div class="page-glass-card">

                <div class="permission-header">

                    <div>

                        <div class="permission-title">
                            Permissions
                        </div>

                        <div class="permission-subtitle">
                            Assign permissions to this role
                        </div>

                    </div>

                    <div class="permission-search">

                        <i class="bi bi-search"></i>

                        <input
                            type="text"
                            id="searchPermissions"
                            placeholder="Search permissions..."
                        >

                    </div>

                </div>

                <div class="row g-4 mt-1">

                    <div class="col-xl-6">

                        <div class="permission-box-header">

                            <div class="permission-box-title">
                                Available
                            </div>

                            <div
                                class="permission-badge"
                                id="availableCount"
                            >
                                0
                            </div>

                        </div>

                        <div
                            id="availablePermissions"
                            class="perm-box modern"
                        >

                            @foreach($permissions as $permission)

                                @if(!in_array($permission->id, old('permissions',$rolePermissions ?? [])))

                                    <div
                                        class="perm-item modern"
                                        data-permission="{{ $permission->id }}"
                                    >

                                        <div class="perm-icon">
                                            <i class="bi bi-key-fill"></i>
                                        </div>

                                        <div class="perm-content">

                                            <div class="perm-title">
                                                {{ $permission->name }}
                                            </div>

                                        </div>

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    </div>

                    <div class="col-xl-6">

                        <div class="permission-box-header">

                            <div class="permission-box-title">
                                Assigned
                            </div>

                            <div
                                class="permission-badge success"
                                id="assignedCount"
                            >
                                0
                            </div>

                        </div>

                        <div
                            id="assignedPermissions"
                            class="perm-box modern assigned"
                        >

                            @foreach($permissions as $permission)

                                @if(in_array($permission->id, old('permissions',$rolePermissions ?? [])))

                                    <div
                                        class="perm-item modern assigned"
                                        data-permission="{{ $permission->id }}"
                                    >

                                        <div class="perm-icon success">
                                            <i class="bi bi-check-lg"></i>
                                        </div>

                                        <div class="perm-content">

                                            <div class="perm-title">
                                                {{ $permission->name }}
                                            </div>

                                        </div>

                                    </div>

                                @endif

                            @endforeach

                        </div>

                    </div>

                </div>

                <div id="permissionInputs"></div>

            </div>

            <div class="page-glass-card sticky-bottom">

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="{{ route('roles.index') }}"
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
                                ? 'Update Role'
                                : 'Save Role'
                        }}

                    </button>

                </div>

            </div>

        </form>

    </div>

    <style>

        .page-shell{
            display:flex;
            flex-direction:column;
            gap:.8rem;
        }

        .page-glass-card{
            padding:1rem 1.05rem;

            border-radius:18px;

            background:rgba(255,255,255,.76);

            border:1px solid rgba(226,232,240,.8);

            backdrop-filter:blur(18px);

            box-shadow:
                0 8px 24px rgba(15,23,42,.04);
        }

        .role-hero{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            flex-wrap:wrap;

            padding:.9rem 1rem;

            border-radius:18px;

            background:
                linear-gradient(
                    135deg,
                    rgba(99,102,241,.08),
                    rgba(59,130,246,.04)
                );

            border:1px solid rgba(226,232,240,.8);
        }

        .page-hero-left{
            display:flex;
            align-items:center;
            gap:.85rem;
        }

        .role-hero-icon{
            width:48px;
            height:48px;

            border-radius:15px;

            display:flex;
            align-items:center;
            justify-content:center;

            background:
                linear-gradient(
                    135deg,
                    #6366f1,
                    #2563eb
                );

            color:#fff;

            font-size:1.05rem;

            box-shadow:
                0 10px 24px rgba(99,102,241,.18);
        }

        .page-hero-title{
            font-size:1rem;
            font-weight:700;

            color:#0f172a;

            margin-bottom:.1rem;
        }

        .page-hero-subtitle{
            font-size:.78rem;

            color:#64748b;
        }

        .section-header{
            margin-bottom:.9rem;
        }

        .section-title-wrap{
            display:flex;
            align-items:center;
            gap:.8rem;
        }

        .section-icon{
            width:42px;
            height:42px;

            border-radius:13px;

            display:flex;
            align-items:center;
            justify-content:center;

            background:#eef2ff;

            color:#4f46e5;

            font-size:.92rem;
        }

        .section-title{
            font-size:.9rem;
            font-weight:700;

            color:#0f172a;

            margin-bottom:.1rem;
        }

        .section-subtitle{
            font-size:.76rem;

            color:#64748b;
        }

        .field-helper{
            margin-top:.35rem;

            font-size:.72rem;

            color:#64748b;
        }

        .permission-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            flex-wrap:wrap;

            margin-bottom:.8rem;
        }

        .permission-title{
            font-size:.92rem;
            font-weight:700;

            color:#0f172a;

            margin-bottom:.1rem;
        }

        .permission-subtitle{
            font-size:.75rem;

            color:#64748b;
        }

        .permission-search{
            position:relative;

            width:230px;
        }

        .permission-search i{
            position:absolute;

            top:50%;
            left:12px;

            transform:translateY(-50%);

            color:#94a3b8;

            font-size:.82rem;
        }

        .permission-search input{
            width:100%;
            height:40px;

            padding:0 14px 0 38px;

            border-radius:12px;

            border:1px solid #dbe2ea;

            background:#fff;

            font-size:.82rem;
        }

        .permission-box-header{
            display:flex;
            align-items:center;
            justify-content:space-between;

            margin-bottom:.65rem;
        }

        .permission-box-title{
            font-size:.82rem;
            font-weight:700;

            color:#0f172a;
        }

        .permission-badge{
            min-width:28px;
            height:28px;

            padding:0 .7rem;

            border-radius:999px;

            display:flex;
            align-items:center;
            justify-content:center;

            background:#eef2ff;

            color:#4f46e5;

            font-size:.72rem;
            font-weight:700;
        }

        .permission-badge.success{
            background:#ecfdf3;

            color:#16a34a;
        }

        .perm-box.modern{
            min-height:320px;
            max-height:320px;

            overflow:auto;

            padding:.8rem;

            border-radius:16px;

            background:#f8fafc;

            border:1px dashed #dbe2ea;
        }

        .perm-item.modern{
            display:flex;
            align-items:center;
            gap:.7rem;

            padding:.72rem .8rem;

            margin-bottom:.55rem;

            border-radius:13px;

            background:#fff;

            border:1px solid #eef2f7;

            cursor:pointer;

            transition:.18s ease;
        }

        .perm-item.modern:hover{
            transform:translateY(-1px);

            box-shadow:
                0 8px 18px rgba(15,23,42,.06);
        }

        .perm-item.modern.assigned{
            background:#f0fdf4;

            border-color:#bbf7d0;
        }

        .perm-icon{
            width:36px;
            height:36px;

            border-radius:11px;

            display:flex;
            align-items:center;
            justify-content:center;

            background:#eef2ff;

            color:#4f46e5;

            flex-shrink:0;

            font-size:.82rem;
        }

        .perm-icon.success{
            background:#dcfce7;

            color:#16a34a;
        }

        .perm-title{
            font-size:.8rem;
            font-weight:700;

            color:#0f172a;
        }

        .form-control,
        .form-select{
            min-height:42px !important;

            border-radius:12px !important;

            border:1px solid #dbe2ea !important;

            font-size:.85rem !important;

            box-shadow:none !important;
        }

        .form-control:focus,
        .form-select:focus{
            border-color:#6366f1 !important;

            box-shadow:
                0 0 0 4px rgba(99,102,241,.08) !important;
        }

        .form-label{
            margin-bottom:.38rem;

            font-size:.82rem;
            font-weight:600;
        }

        .btn{
            min-height:40px;

            border-radius:12px !important;

            font-size:.84rem;
        }

        .sticky-bottom{
            position:sticky;
            bottom:0;
            z-index:10;
        }

        .row.g-4{
            --bs-gutter-x:1rem;
            --bs-gutter-y:1rem;
        }

        @media(max-width:992px){

            .permission-search{
                width:100%;
            }

            .perm-box.modern{
                min-height:260px;
                max-height:260px;
            }

        }

    </style>

@endsection
