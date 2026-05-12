@extends('layouts.app')

@section('title','Enrollment Form')

@section('content')

    @php

        $isEdit = isset($enrollment);

    @endphp

    <div class="container-fluid px-0">

        <x-page-header
            title="{{ $isEdit ? 'Edit Enrollment' : 'Student Enrollment' }}"
            subtitle="Registrar enrollment management"
        >

            <x-slot:action>

                <a
                    href="{{ route('enrollments.index') }}"
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
                ? route('enrollments.update', encrypt($enrollment->id))
                : route('enrollments.store')
        }}"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <div class="col-xl-12">

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

                        <div class="card-header bg-white border-bottom py-3 px-4">

                            <div class="d-flex align-items-center gap-3">

                                <div
                                    class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                                    style="width:52px;height:52px;"
                                >
                                    <i class="bi bi-person-vcard fs-4 text-primary"></i>
                                </div>

                                <div>

                                    <h5 class="mb-1 fw-bold">
                                        Learner Enrollment Details
                                    </h5>

                                    <div class="text-muted small">
                                        Manage student enrollment information
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card-body p-4">

                            <div class="row g-4">

                                <div class="col-12">

                                    <div class="border rounded-4 p-3 bg-light-subtle">

                                        <div class="fw-semibold mb-3">
                                            Student Information
                                        </div>

                                        <div class="row g-3">

                                            <x-form.group
                                                name="StudentID"
                                                label="Learner"
                                                class="col-md-12"
                                                required
                                            >

                                                <x-form.select
                                                    name="StudentID"
                                                    ajax="{{ route('select2.students') }}"
                                                    value="{{ old('StudentID',$enrollment->StudentID ?? '') }}"
                                                    text="{{ $enrollment->student->FullName ?? '' }}"
                                                    placeholder="Search learner"
                                                />

                                            </x-form.group>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-12">

                                    <div class="border rounded-4 p-3 bg-light-subtle">

                                        <div class="fw-semibold mb-3">
                                            Academic Placement
                                        </div>

                                        <div class="row g-3">

                                            <x-form.group
                                                name="ClassID"
                                                label="Class Placement"
                                                class="col-md-12"
                                                required
                                            >

                                                <x-form.select
                                                    name="ClassID"
                                                    ajax="{{ route('select2.classes') }}"
                                                    value="{{ old('ClassID',$enrollment->ClassID ?? '') }}"
                                                    text="{{ $enrollment->class?->ClassName ?? '' }}"
                                                    placeholder="Search section"
                                                />

                                            </x-form.group>

                                            <x-form.group
                                                name="EnrollmentDate"
                                                label="Enrollment Date"
                                                class="col-md-6"
                                            >

                                                <input
                                                    type="date"
                                                    name="EnrollmentDate"
                                                    class="form-control form-control-lg"

                                                    value="{{
                                                    old(
                                                        'EnrollmentDate',
                                                        isset($enrollment)
                                                            ? optional(
                                                                $enrollment->EnrollmentDate
                                                            )->format('Y-m-d')
                                                            : now()->format('Y-m-d')
                                                    )
                                                }}"
                                                >

                                            </x-form.group>

                                            <x-form.group
                                                name="EnrollmentStatus"
                                                label="Enrollment Status"
                                                class="col-md-6"
                                                required
                                            >

                                                <select
                                                    name="EnrollmentStatus"
                                                    class="form-select form-select-lg"
                                                >

                                                    @foreach([
                                                        'ENROLLED',
                                                        'PENDING',
                                                        'DROPPED',
                                                        'TRANSFERRED',
                                                        'COMPLETED'
                                                    ] as $status)

                                                        <option
                                                            value="{{ $status }}"

                                                            {{
                                                                old(
                                                                    'EnrollmentStatus',
                                                                    $enrollment->EnrollmentStatus
                                                                        ?? 'ENROLLED'
                                                                ) == $status
                                                                    ? 'selected'
                                                                    : ''
                                                            }}
                                                        >
                                                            {{ $status }}
                                                        </option>

                                                    @endforeach

                                                </select>

                                            </x-form.group>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-12">

                                    <div class="border rounded-4 p-3 bg-light-subtle">

                                        <div class="fw-semibold mb-3">
                                            Registrar Remarks
                                        </div>

                                        <x-form.group
                                            name="Remarks"
                                            label="Remarks"
                                            class="col-md-12"
                                        >

                                        <textarea
                                            name="Remarks"
                                            rows="4"
                                            class="form-control"
                                            placeholder="Optional registrar notes..."
                                        >{{ old('Remarks',$enrollment->Remarks ?? '') }}</textarea>

                                        </x-form.group>

                                    </div>

                                </div>

                            </div>

                        </div>
                        <div
                            class="d-flex justify-content-end gap-2 mt-4 me-4 pt-3 mb-4 border-top sticky-bottom bg-white"
                            style="z-index:10;"
                        >

                            <a
                                href="{{ route('enrollments.index') }}"
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
                                        ? 'Update Enrollment'
                                        : 'Save Enrollment'
                                }}

                            </button>

                        </div>
                    </div>

                </div>

            </div>

        </form>

    </div>

@endsection

@section('styles')

    <style>

        .preview-item{
            padding-bottom:12px;
            border-bottom:1px dashed #e5e7eb;
        }

        .preview-item:last-child{
            border-bottom:none;
            padding-bottom:0;
        }

        .preview-label{
            font-size:.75rem;
            font-weight:600;
            text-transform:uppercase;
            color:#6b7280;
            margin-bottom:4px;
            letter-spacing:.5px;
        }

        .preview-value{
            font-size:1rem;
            font-weight:600;
            color:#111827;
        }

        .card{
            transition:.2s ease;
        }

        .form-control,
        .form-select{
            border-radius:12px;
            min-height:48px;
        }

        textarea.form-control{
            min-height:auto;
        }

    </style>

@endsection

@section('scripts')

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function(){

                function updateSectionPreview(){

                    const selected =
                        $('#SectionID option:selected');

                    $('#academicYearPreview').text(
                        selected.data('ay')
                        || '-'
                    );

                    $('#semesterPreview').text(
                        selected.data('semester')
                        || '-'
                    );

                    $('#gradeLevelPreview').text(
                        selected.data('grade')
                        || '-'
                    );

                    $('#strandPreview').text(
                        selected.data('strand')
                        || '-'
                    );

                    $('#adviserPreview').text(
                        selected.data('adviser')
                        || '-'
                    );

                    $('#roomPreview').text(
                        selected.data('room')
                        || '-'
                    );

                }

                updateSectionPreview();

                $('#SectionID').on(
                    'change',
                    updateSectionPreview
                );

            }
        );

    </script>

@endsection
