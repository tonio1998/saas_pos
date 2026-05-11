@extends('layouts.app')
@section('title','Semester Management')

@section('content')

    @php
        $isEdit = isset($semester);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Semester' : 'Add Semester' }}"
        subtitle="Manage school semesters"
    >

        <x-slot:action>

            <a
                href="{{ route('semesters.index') }}"
                class="btn btn-light btn-md"
            >
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card class="row col-md-5 d-flex justify-content-center">

        <form
            method="POST"
            action="{{
                $isEdit
                    ? route('semesters.update',$semester->id)
                    : route('semesters.store')
            }}"
            id="semesterForm"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group
                    name="SemesterName"
                    label="Semester Name"
                    class="col-md-8"
                    required
                >

                    <x-form.input
                        name="SemesterName"
                        value="{{ old('SemesterName',$semester->SemesterName ?? '') }}"
                        placeholder="1"
                        maxlength="1"
                    />

                </x-form.group>

                <x-form.group
                    name="SemesterOrder"
                    label="Semester Order"
                    class="col-md-4"
                    required
                >

                    <x-form.input
                        type="number"
                        min="1"
                        name="SemesterOrder"
                        value="{{ old('SemesterOrder',$semester->SemesterOrder ?? '') }}"
                    />

                </x-form.group>

                <div class="col-md-12">

                    <div class="form-check form-switch">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="IsActive"
                            value="1"
                            {{
                                old(
                                    'IsActive',
                                    $semester->IsActive ?? true
                                )
                                    ? 'checked'
                                    : ''
                            }}
                        >

                        <label class="form-check-label">
                            Active Semester
                        </label>

                    </div>

                </div>

            </div>

            <div class="d-flex justify-content-end">

                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-check"></i>

                        {{
                            $isEdit
                                ? 'Update Semester'
                                : 'Save Semester'
                        }}

                    </button>

                    <a
                        href="{{ route('semesters.index') }}"
                        class="btn btn-light"
                    >
                        Cancel
                    </a>

                </div>

            </div>

        </form>

    </x-card>

@endsection

@section('scripts')

    <script>

        document.addEventListener('DOMContentLoaded',function(){

            const form=document.getElementById('semesterForm');

            form.addEventListener('submit',function(e){

                const semesterName=form
                    .querySelector('[name="SemesterName"]')
                    .value
                    .trim();

                const semesterOrder=form
                    .querySelector('[name="SemesterOrder"]')
                    .value;

                if(!semesterName){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Semester name is required'
                    });

                    e.preventDefault();

                    return;
                }

                if(!semesterOrder){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Semester order is required'
                    });

                    e.preventDefault();

                    return;
                }

            });

        });

    </script>

@endsection
