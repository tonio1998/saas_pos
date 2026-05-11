@extends('layouts.app')
@section('title','School Settings')

@section('content')

    @php
        $isEdit = isset($setting);
    @endphp

    <x-page-header
        title="School Settings"
        subtitle="Manage school information and system configuration"
    >
    </x-page-header>

    <x-card>

        <form method="POST"
              enctype="multipart/form-data"
              action="{{ route('settings.store') }}">
            @csrf

            <div class="row g-4">

                <div class="col-12">
                    <div class="border-bottom pb-2 mb-2">
                        <h5 class="mb-0">School Information</h5>
                    </div>
                </div>

                <x-form.group name="SchoolName" label="School Name" class="col-md-6" required>
                    <x-form.input
                        name="SchoolName"
                        value="{{ old('SchoolName',$setting->SchoolName ?? '') }}"
                        placeholder="Enter school name"
                    />
                </x-form.group>

                <x-form.group name="SchoolCode" label="School Code" class="col-md-3">
                    <x-form.input
                        name="SchoolCode"
                        value="{{ old('SchoolCode',$setting->SchoolCode ?? '') }}"
                        placeholder="Enter school code"
                    />
                </x-form.group>

                <x-form.group name="EducationLevel" label="Education Level" class="col-md-3" required>
                    <select name="EducationLevel" class="form-select">
                        @foreach(['JHS','SHS','INTEGRATED'] as $level)
                            <option value="{{ $level }}"
                                {{ old('EducationLevel',$setting->EducationLevel ?? '') == $level ? 'selected':'' }}>
                                {{ $level }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="Region" label="Region" class="col-md-4">
                    <x-form.input
                        name="Region"
                        value="{{ old('Region',$setting->Region ?? '') }}"
                        placeholder="Enter region"
                    />
                </x-form.group>

                <x-form.group name="Division" label="Division" class="col-md-4">
                    <x-form.input
                        name="Division"
                        value="{{ old('Division',$setting->Division ?? '') }}"
                        placeholder="Enter division"
                    />
                </x-form.group>

                <x-form.group name="ContactNumber" label="Contact Number" class="col-md-4">
                    <x-form.input
                        name="ContactNumber"
                        value="{{ old('ContactNumber',$setting->ContactNumber ?? '') }}"
                        placeholder="+639XXXXXXXXX"
                    />
                </x-form.group>

                <x-form.group name="EmailAddress" label="Email Address" class="col-md-6">
                    <x-form.input
                        type="email"
                        name="EmailAddress"
                        value="{{ old('EmailAddress',$setting->EmailAddress ?? '') }}"
                        placeholder="Enter email address"
                    />
                </x-form.group>

                <x-form.group name="ThemeColor" label="Theme Color" class="col-md-2">
                    <input
                        type="color"
                        name="ThemeColor"
                        class="form-control form-control-color w-100"
                        value="{{ old('ThemeColor',$setting->ThemeColor ?? '#004D1A') }}"
                    >
                </x-form.group>

                <x-form.group name="Logo" label="School Logo" class="col-md-4">
                    <input
                        type="file"
                        name="Logo"
                        class="form-control"
                        accept="image/*"
                    >
                </x-form.group>

                <x-form.group name="Address" label="Address" class="col-md-12">
                    <textarea
                        name="Address"
                        rows="3"
                        class="form-control"
                        placeholder="Enter school address"
                    >{{ old('Address',$setting->Address ?? '') }}</textarea>
                </x-form.group>

                <div class="col-12">
                    <div class="border-bottom pb-2 mb-2 mt-2">
                        <h5 class="mb-0">School Officials</h5>
                    </div>
                </div>

                <x-form.group name="PrincipalID" label="Principal" class="col-md-6">

                    <x-form.select
                        name="PrincipalID"
                        ajax="{{ route('select2.employees') }}"
                        value="{{ old('PrincipalID',$setting->PrincipalID ?? '') }}"
                        text="{{ ($setting->principal->FirstName ?? '').' '.($student->principal->LastName ?? '') }}"
                        placeholder="Select employee"
                    />

                </x-form.group>

                <x-form.group name="RegistrarID" label="Registrar" class="col-md-6">

                    <x-form.select
                        name="RegistrarID"
                        ajax="{{ route('select2.employees') }}"
                        value="{{ old('RegistrarID',$setting->RegistrarID ?? '') }}"
                        text="{{ ($setting->registrar->FirstName ?? '').' '.($student->registrar->LastName ?? '') }}"
                        placeholder="Select employee"
                    />

                </x-form.group>

                <div class="col-12">
                    <div class="border-bottom pb-2 mb-2 mt-2">
                        <h5 class="mb-0">Attendance Settings</h5>
                    </div>
                </div>

                <x-form.group name="OfficialTimeIn" label="Official Time In" class="col-md-3">

                    <input
                        type="time"
                        name="OfficialTimeIn"
                        class="form-control"
                        value="{{ old(
            'OfficialTimeIn',
            isset($setting->OfficialTimeIn)
                ? \Carbon\Carbon::parse($setting->OfficialTimeIn)->format('H:i')
                : '07:00'
        ) }}"
                    >

                </x-form.group>

                <x-form.group name="OfficialTimeOut" label="Official Time Out" class="col-md-3">

                    <input
                        type="time"
                        name="OfficialTimeOut"
                        class="form-control"
                        value="{{ old(
            'OfficialTimeOut',
            isset($setting->OfficialTimeOut)
                ? \Carbon\Carbon::parse($setting->OfficialTimeOut)->format('H:i')
                : '17:00'
        ) }}"
                    >

                </x-form.group>

                <x-form.group name="LateGraceMinutes" label="Late Grace Minutes" class="col-md-3">
                    <x-form.input
                        type="number"
                        min="0"
                        name="LateGraceMinutes"
                        value="{{ old('LateGraceMinutes',$setting->LateGraceMinutes ?? 15) }}"
                    />
                </x-form.group>

                <div class="col-md-3">
                    <label class="form-label d-block">Features</label>

                    <div class="d-flex flex-column gap-2">

                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="EnableNFC"
                                value="1"
                                {{ old('EnableNFC',$setting->EnableNFC ?? 1) ? 'checked':'' }}
                            >
                            <label class="form-check-label">
                                Enable NFC
                            </label>
                        </div>

                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="EnableQR"
                                value="1"
                                {{ old('EnableQR',$setting->EnableQR ?? 1) ? 'checked':'' }}
                            >
                            <label class="form-check-label">
                                Enable QR
                            </label>
                        </div>

                        <div class="form-check form-switch">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="EnableOfflineAttendance"
                                value="1"
                                {{ old('EnableOfflineAttendance',$setting->EnableOfflineAttendance ?? 1) ? 'checked':'' }}
                            >
                            <label class="form-check-label">
                                Offline Attendance
                            </label>
                        </div>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        Save Settings
                    </button>

                </div>
            </div>

        </form>

    </x-card>

@endsection

@section('scripts')

    <script>

        document.addEventListener('DOMContentLoaded',function(){

            const form=document.querySelector('form');

            form.addEventListener('submit',function(e){

                const SchoolName=form.querySelector('[name="SchoolName"]').value.trim();
                const Contact=form.querySelector('[name="ContactNumber"]').value.trim();
                const Email=form.querySelector('[name="EmailAddress"]').value.trim();

                if(!SchoolName){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'School name is required'
                    });

                    e.preventDefault();
                    return;
                }

                if(Contact && !/^\+639\d{9}$/.test(Contact)){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Contact number must start with +639'
                    });

                    e.preventDefault();
                    return;
                }

                if(Email){

                    const emailRegex=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if(!emailRegex.test(Email)){

                        Swal.fire({
                            icon:'warning',
                            title:'Validation',
                            text:'Invalid email address'
                        });

                        e.preventDefault();
                    }

                }

            });

        });

    </script>

@endsection
