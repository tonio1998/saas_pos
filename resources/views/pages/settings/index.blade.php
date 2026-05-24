@extends('layouts.app')

@section('title','School Settings')

@section('content')

    @php
        $isEdit = isset($setting);
    @endphp

    <div class="page-shell">

        <div class="page-hero settings-hero page-glass-card">

            <div class="page-hero-left">

                <div class="settings-hero-icon">
                    <i class="bi bi-building-gear"></i>
                </div>

                <div>

                    <div class="page-hero-title">
                        School Settings
                    </div>

                    <div class="page-hero-subtitle">
                        Manage school information and system configuration
                    </div>

                </div>

            </div>

            <div class="settings-status">

                <span class="settings-status-dot"></span>

                System Configuration

            </div>

        </div>

        <form
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('settings.store') }}"
        >

            @csrf

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
                                Main school profile and identity settings
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
                            value="{!! html_entity_decode(old('SystemTitle',$setting->SystemTitle ?? '')) !!}"
                            placeholder="Enter system title"
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
                            value="{{ old('SchoolName',$setting->SchoolName ?? '') }}"
                            placeholder="Enter school name"
                        />

                    </x-form.group>

                    <x-form.group
                        name="SchoolCode"
                        label="School Code"
                        class="col-xl-3"
                    >

                        <x-form.input
                            name="SchoolCode"
                            value="{{ old('SchoolCode',$setting->SchoolCode ?? '') }}"
                            placeholder="School code"
                        />

                    </x-form.group>

                    <x-form.group
                        name="EducationLevel"
                        label="Education Level"
                        class="col-xl-3"
                        required
                    >

                        <select
                            name="EducationLevel"
                            class="form-select"
                        >

                            @foreach(['JHS','SHS','INTEGRATED'] as $level)

                                <option
                                    value="{{ $level }}"
                                    {{
                                        old(
                                            'EducationLevel',
                                            $setting->EducationLevel ?? ''
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
                        name="Region"
                        label="Region"
                        class="col-xl-3"
                    >

                        <x-form.input
                            name="Region"
                            value="{{ old('Region',$setting->Region ?? '') }}"
                            placeholder="Region"
                        />

                    </x-form.group>

                    <x-form.group
                        name="Division"
                        label="Division"
                        class="col-xl-3"
                    >

                        <x-form.input
                            name="Division"
                            value="{{ old('Division',$setting->Division ?? '') }}"
                            placeholder="Division"
                        />

                    </x-form.group>

                    <x-form.group
                        name="ContactNumber"
                        label="Contact Number"
                        class="col-xl-4"
                    >

                        <x-form.input
                            name="ContactNumber"
                            value="{{ old('ContactNumber',$setting->ContactNumber ?? '') }}"
                            placeholder="+639XXXXXXXXX"
                        />

                    </x-form.group>

                    <x-form.group
                        name="EmailAddress"
                        label="Email Address"
                        class="col-xl-4"
                    >

                        <x-form.input
                            type="text"
                            name="EmailAddress"
                            value="{{ old('EmailAddress',$setting->EmailAddress ?? '') }}"
                            placeholder="Email address"
                        />

                    </x-form.group>

                    <x-form.group
                        name="ThemeColor"
                        label="Theme Color"
                        class="col-xl-2"
                    >

                        <input
                            type="color"
                            name="ThemeColor"
                            class="form-control form-control-color w-100"
                            value="{{ old('ThemeColor',$setting->ThemeColor ?? '#004D1A') }}"
                        >

                    </x-form.group>

                    <x-form.group
                        name="Logo"
                        label="School Logo"
                        class="col-xl-2"
                    >

                        <input
                            type="file"
                            name="Logo"
                            class="form-control"
                            accept="image/*"
                        >

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
                            placeholder="School address"
                        >{{ old('Address',$setting->Address ?? '') }}</textarea>

                    </x-form.group>

                </div>

            </div>

            <div class="page-glass-card">

                <div class="section-header">

                    <div class="section-title-wrap">

                        <div class="section-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>

                        <div>

                            <div class="section-title">
                                School Officials
                            </div>

                            <div class="section-subtitle">
                                Configure authorized school personnel
                            </div>

                        </div>

                    </div>

                </div>

                <div class="row g-4">

                    <x-form.group
                        name="PrincipalID"
                        label="Principal"
                        class="col-xl-6"
                    >

                        <x-form.select
                            name="PrincipalID"
                            ajax="{{ route('select2.employees') }}"
                            value="{{ old('PrincipalID',$setting->PrincipalID ?? '') }}"
                            text="{{ ($setting->principal->FirstName ?? '').' '.($setting->principal->LastName ?? '') }}"
                            placeholder="Select employee"
                        />

                    </x-form.group>

                    <x-form.group
                        name="RegistrarID"
                        label="Registrar"
                        class="col-xl-6"
                    >

                        <x-form.select
                            name="RegistrarID"
                            ajax="{{ route('select2.employees') }}"
                            value="{{ old('RegistrarID',$setting->RegistrarID ?? '') }}"
                            text="{{ ($setting->registrar->FirstName ?? '').' '.($setting->registrar->LastName ?? '') }}"
                            placeholder="Select employee"
                        />

                    </x-form.group>

                </div>

            </div>

            <div class="page-glass-card">

                <div class="section-header">

                    <div class="section-title-wrap">

                        <div class="section-icon">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <div>

                            <div class="section-title">
                                Attendance Settings
                            </div>

                            <div class="section-subtitle">
                                Configure attendance behavior and features
                            </div>

                        </div>

                    </div>

                </div>

                <div class="row g-4">

                    <x-form.group
                        name="OfficialTimeIn"
                        label="Official Time In"
                        class="col-xl-3"
                    >

                        <input
                            type="time"
                            name="OfficialTimeIn"
                            class="form-control"
                            value="{{ old('OfficialTimeIn',isset($setting->OfficialTimeIn) ? \Carbon\Carbon::parse($setting->OfficialTimeIn)->format('H:i') : '07:00') }}"
                        >

                    </x-form.group>

                    <x-form.group
                        name="OfficialTimeOut"
                        label="Official Time Out"
                        class="col-xl-3"
                    >

                        <input
                            type="time"
                            name="OfficialTimeOut"
                            class="form-control"
                            value="{{ old('OfficialTimeOut',isset($setting->OfficialTimeOut) ? \Carbon\Carbon::parse($setting->OfficialTimeOut)->format('H:i') : '17:00') }}"
                        >

                    </x-form.group>

                    <x-form.group
                        name="LateGraceMinutes"
                        label="Late Grace Minutes"
                        class="col-xl-3"
                    >

                        <x-form.input
                            type="number"
                            min="0"
                            name="LateGraceMinutes"
                            value="{{ old('LateGraceMinutes',$setting->LateGraceMinutes ?? 15) }}"
                        />

                    </x-form.group>

                    <div class="col-xl-3">

                        <label class="form-label">
                            Features
                        </label>

                        <div class="settings-switches">

                            <div class="settings-switch">

                                <div>

                                    <div class="settings-switch-title">
                                        Enable NFC
                                    </div>

                                    <div class="settings-switch-subtitle">
                                        NFC attendance scanning
                                    </div>

                                </div>

                                <div class="form-check form-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="EnableNFC"
                                        value="1"
                                        {{
                                            old(
                                                'EnableNFC',
                                                $setting->EnableNFC ?? 1
                                            )
                                                ? 'checked'
                                                : ''
                                        }}
                                    >

                                </div>

                            </div>

                            <div class="settings-switch">

                                <div>

                                    <div class="settings-switch-title">
                                        Enable QR
                                    </div>

                                    <div class="settings-switch-subtitle">
                                        QR attendance scanning
                                    </div>

                                </div>

                                <div class="form-check form-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="EnableQR"
                                        value="1"
                                        {{
                                            old(
                                                'EnableQR',
                                                $setting->EnableQR ?? 1
                                            )
                                                ? 'checked'
                                                : ''
                                        }}
                                    >

                                </div>

                            </div>

                            <div class="settings-switch">

                                <div>

                                    <div class="settings-switch-title">
                                        Offline Attendance
                                    </div>

                                    <div class="settings-switch-subtitle">
                                        Enable local attendance sync
                                    </div>

                                </div>

                                <div class="form-check form-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="EnableOfflineAttendance"
                                        value="1"
                                        {{
                                            old(
                                                'EnableOfflineAttendance',
                                                $setting->EnableOfflineAttendance ?? 1
                                            )
                                                ? 'checked'
                                                : ''
                                        }}
                                    >

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="page-glass-card">

                <div class="section-header">

                    <div class="section-title-wrap">

                        <div class="section-icon">
                            <i class="bi bi-chat-dots-fill"></i>
                        </div>

                        <div>

                            <div class="section-title">
                                SMS Gateway Settings
                            </div>

                            <div class="section-subtitle">
                                Configure GSM modem and Python integration
                            </div>

                        </div>

                    </div>

                </div>

                <div class="row g-4">

                    <x-form.group
                        name="cacert_path"
                        label="Cacert Path"
                        class="col-xl-4"
                    >

                        <x-form.input
                            name="cacert_path"
                            value="{{ old('cacert_path',$setting->cacert_path ?? '') }}"
                        />

                    </x-form.group>

                    <x-form.group
                        name="python_path"
                        label="Python Path"
                        class="col-xl-4"
                    >

                        <x-form.input
                            name="python_path"
                            value="{{ old('python_path',$setting->python_path ?? '') }}"
                            placeholder="C:\Python313\python.exe"
                        />

                    </x-form.group>

                    <x-form.group
                        name="port_com"
                        label="COM Port"
                        class="col-xl-4"
                    >

                        <x-form.input
                            name="port_com"
                            value="{{ old('port_com',$setting->port_com ?? '') }}"
                            placeholder="COM3"
                        />

                    </x-form.group>

                </div>

            </div>

            <div class="page-glass-card sticky-bottom">

                <div class="d-flex justify-content-end">

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >

                        <i class="bi bi-check-circle me-1"></i>
                        Save Settings

                    </button>

                </div>

            </div>

        </form>

    </div>

@endsection

@section('styles')

    <style>

        .settings-hero{
            background:
                linear-gradient(
                    135deg,
                    rgba(16,185,129,.08),
                    rgba(5,150,105,.04)
                );
        }

        .settings-hero-icon{
            width:50px;
            height:50px;

            border-radius:16px;

            display:flex;
            align-items:center;
            justify-content:center;

            background:
                linear-gradient(
                    135deg,
                    #10b981,
                    #059669
                );

            color:#fff;

            font-size:1.1rem;

            box-shadow:
                0 10px 24px rgba(16,185,129,.18);
        }

        .settings-status{
            display:flex;
            align-items:center;
            gap:.55rem;

            padding:.65rem .95rem;

            border-radius:999px;

            background:#ecfdf3;

            color:#16a34a;

            font-size:.76rem;
            font-weight:700;
        }

        .settings-status-dot{
            width:8px;
            height:8px;

            border-radius:50%;

            background:#16a34a;
        }

        .settings-switches{
            display:flex;
            flex-direction:column;
            gap:.7rem;
        }

        .settings-switch{
            display:flex;
            align-items:center;
            justify-content:space-between;

            padding:.8rem .9rem;

            border-radius:14px;

            background:#f8fafc;

            border:1px solid #eef2f7;
        }

        .settings-switch-title{
            font-size:.82rem;
            font-weight:700;

            color:#0f172a;
        }

        .settings-switch-subtitle{
            font-size:.72rem;

            color:#64748b;
        }

        textarea.form-control{
            min-height:90px;
            resize:none;
        }

    </style>

@endsection
