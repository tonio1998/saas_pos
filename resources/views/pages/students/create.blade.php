@extends('layouts.app')
@section('title','Student Management')

@section('content')

    @php
        $isEdit = isset($student);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Student' : 'Add Student' }}"
        subtitle="Manage student"
    >
        <x-slot:action>
            <a href="{{ route('students.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('students.update',encrypt($student->id)) : route('students.store') }}"
              enctype="multipart/form-data">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">
                <x-form.group name="LRN" label="LRN" class="col-md-4" required>
                    <x-form.input
                        name="LRN"
                        value="{{ old('LRN',$student->LRN ?? '') }}"
                        placeholder="Enter learner reference number"
                        maxlength="12"
                    />
                </x-form.group>

                <x-form.group name="YearLevel" label="Year Level" class="col-md-4" required>
                    <select name="YearLevel" class="form-select">
                        <option value="">Select year level</option>
                        @foreach([7,8,9,10,11,12] as $year)
                            <option value="{{ $year }}" {{ old('YearLevel',$student->YearLevel ?? '') == $year ? 'selected':'' }}>
                                Grade {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="Strand" label="Strand" class="col-md-4">
                    <x-form.input
                        name="Strand"
                        value="{{ old('Strand',$student->Strand ?? '') }}"
                        placeholder="Enter student strand"
                    />
                </x-form.group>

                <x-form.group name="FirstName" label="First Name" class="col-md-3" required>
                    <x-form.input
                        name="FirstName"
                        value="{{ old('FirstName',$student->FirstName ?? '') }}"
                        placeholder="Enter first name"
                    />
                </x-form.group>

                <x-form.group name="MiddleName" label="Middle Name" class="col-md-3">
                    <x-form.input
                        name="MiddleName"
                        value="{{ old('MiddleName',$student->MiddleName ?? '') }}"
                        placeholder="Enter middle name"
                    />
                </x-form.group>

                <x-form.group name="LastName" label="Last Name" class="col-md-3" required>
                    <x-form.input
                        name="LastName"
                        value="{{ old('LastName',$student->LastName ?? '') }}"
                        placeholder="Enter last name"
                    />
                </x-form.group>

                <x-form.group name="Suffix" label="Suffix" class="col-md-3">
                    <select name="Suffix" class="form-select">
                        <option value=""></option>
                        @foreach(['Jr','Sr','III'] as $suffix)
                            <option value="{{ $suffix }}" {{ old('Suffix',$student->Suffix ?? '') == $suffix ? 'selected':'' }}>
                                {{ $suffix }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="Section" label="Section" class="col-md-6" required>
                    <x-form.input
                        name="Section"
                        value="{{ old('Section',$student->Section ?? '') }}"
                        placeholder="Enter section"
                    />
                </x-form.group>

                <x-form.group name="PhoneNumber" label="Phone Number" class="col-md-6" required>
                    <x-form.input
                        name="PhoneNumber"
                        value="{{ old('PhoneNumber',$student->PhoneNumber ?? '') }}"
                        placeholder="+639XXXXXXXXX"
                    />
                </x-form.group>

                <x-form.group name="GuardianID" label="Guardian" class="col-md-6">

                    <x-form.select
                        name="GuardianID"
                        ajax="{{ route('select2.guardians') }}"
                        value="{{ old('GuardianID',$student->GuardianID ?? '') }}"
                        text="{{ ($student->guardian->FirstName ?? '').' '.($student->guardian->LastName ?? '') }}"
                        placeholder="Select guardian"
                    />

                </x-form.group>

{{--                <x-form.group name="filepath" label="Upload Photo" class="col-md-6">--}}
{{--                    <x-form.input type="file" name="filepath" id="photo"/>--}}
{{--                </x-form.group>--}}

                <div class="col-md-6 d-flex align-items-end">

                    @if($isEdit && $student->filepath)
                        <img
                            src="{{ asset('storage/'.$student->filepath) }}"
                            id="preview"
                            style="width:120px;height:120px;border-radius:8px;object-fit:cover;border:1px solid #ddd;padding:3px;"
                        >
                    @else
                        <img
                            id="preview"
                            style="width:120px;height:120px;border-radius:8px;object-fit:cover;display:none;border:1px solid #ddd;padding:3px;"
                        >
                    @endif

                </div>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Student' : 'Save Student' }}
                    </button>

                    <a href="{{ route('students.index') }}" class="btn btn-light">
                        Cancel
                    </a>

                </div>
            </div>

        </form>

    </x-card>
@endsection


@section('scripts')

    <script>

        document.addEventListener('DOMContentLoaded', () => {

            const form = document.querySelector('form');

            if (!form) return;

            const submitBtn = form.querySelector('button[type="submit"]');

            const fields = {
                LRN: form.querySelector('[name="LRN"]'),
                FirstName: form.querySelector('[name="FirstName"]'),
                LastName: form.querySelector('[name="LastName"]'),
                PhoneNumber: form.querySelector('[name="PhoneNumber"]'),
                YearLevel: form.querySelector('[name="YearLevel"]'),
                Section: form.querySelector('[name="Section"]'),
            };

            const showError = (message, field = null) => {

                if (field) {
                    field.focus();
                    field.classList.add('is-invalid');

                    field.addEventListener('input', () => {
                        field.classList.remove('is-invalid');
                    }, { once: true });
                }

                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: message,
                    confirmButtonColor: '#0d6efd'
                });

            };

            form.addEventListener('submit', (e) => {

                e.preventDefault();

                const LRN = fields.LRN?.value.trim() || '';
                const FirstName = fields.FirstName?.value.trim() || '';
                const LastName = fields.LastName?.value.trim() || '';
                const Phone = fields.PhoneNumber?.value.trim() || '';
                const YearLevel = fields.YearLevel?.value || '';
                const Section = fields.Section?.value.trim() || '';

                if (!LRN) {
                    return showError('LRN is required.', fields.LRN);
                }

                if (!/^\d{12}$/.test(LRN)) {
                    return showError('LRN must contain exactly 12 digits.', fields.LRN);
                }

                if (!FirstName) {
                    return showError('First name is required.', fields.FirstName);
                }

                if (!LastName) {
                    return showError('Last name is required.', fields.LastName);
                }

                if (!YearLevel) {
                    return showError('Please select a year level.', fields.YearLevel);
                }

                if (!Section) {
                    return showError('Section is required.', fields.Section);
                }

                if (Phone && !/^(\+639\d{9}|09\d{9})$/.test(Phone)) {
                    return showError(
                        'Phone number format is invalid.',
                        fields.PhoneNumber
                    );
                }

                if (submitBtn) {

                    submitBtn.disabled = true;

                    submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"></span>
                Processing...
            `;
                }

                form.submit();

            });

            const photo = document.getElementById('photo');
            const preview = document.getElementById('preview');

            if (photo && preview) {

                photo.addEventListener('change', function () {

                    const file = this.files?.[0];

                    if (!file) return;

                    const allowedTypes = [
                        'image/jpeg',
                        'image/png',
                        'image/jpg',
                        'image/webp'
                    ];

                    if (!allowedTypes.includes(file.type)) {

                        this.value = '';

                        return Swal.fire({
                            icon: 'error',
                            title: 'Invalid File',
                            text: 'Only JPG, PNG, and WEBP images are allowed.',
                            confirmButtonColor: '#dc3545'
                        });

                    }

                    if (file.size > 5 * 1024 * 1024) {

                        this.value = '';

                        return Swal.fire({
                            icon: 'error',
                            title: 'File Too Large',
                            text: 'Maximum upload size is 5MB.',
                            confirmButtonColor: '#dc3545'
                        });

                    }

                    const reader = new FileReader();

                    reader.onload = (e) => {

                        preview.src = e.target.result;
                        preview.style.display = 'block';

                    };

                    reader.onerror = () => {

                        Swal.fire({
                            icon: 'error',
                            title: 'Preview Error',
                            text: 'Unable to preview selected image.',
                            confirmButtonColor: '#dc3545'
                        });

                    };

                    reader.readAsDataURL(file);

                });

            }

        });

    </script>

@endsection
