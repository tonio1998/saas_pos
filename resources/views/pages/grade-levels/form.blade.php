@extends('layouts.app')
@section('title','Grade Level Management')

@section('content')

    @php
        $isEdit = isset($gradeLevel);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Grade Level' : 'Add Grade Level' }}"
        subtitle="Manage academic grade levels"
    >

        <x-slot:action>

            <a
                href="{{ route('grade-levels.index') }}"
                class="btn btn-light btn-md"
            >
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <form
            method="POST"
            action="{{
                $isEdit
                    ? route('grade-levels.update',encrypt($gradeLevel->id))
                    : route('grade-levels.store')
            }}"
            id="gradeLevelForm"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group
                    name="GradeLevel"
                    label="Grade Level"
                    class="col-md-4"
                    required
                >

                    <x-form.input
                        name="GradeLevel"
                        value="{{ old('GradeLevel',$gradeLevel->GradeLevel ?? '') }}"
                        placeholder="Grade 11"
                    />

                </x-form.group>

                <x-form.group
                    name="EducationLevel"
                    label="Education Level"
                    class="col-md-4"
                    required
                >

                    <select
                        name="EducationLevel"
                        id="EducationLevel"
                        class="form-select"
                    >

                        <option value="">
                            Select Education Level
                        </option>

                        @foreach(['JHS','SHS'] as $level)

                            <option
                                value="{{ $level }}"
                                {{
                                    old(
                                        'EducationLevel',
                                        $gradeLevel->EducationLevel ?? ''
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

                <div class="col-md-4">

                    <label class="form-label d-block">
                        Semester Configuration
                    </label>

                    <div class="form-check form-switch mt-2">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="HasSemester"
                            id="HasSemester"
                            value="1"

                            {{
                                old(
                                    'HasSemester',
                                    $gradeLevel->HasSemester ?? false
                                )
                                    ? 'checked'
                                    : ''
                            }}
                        >

                        <label
                            class="form-check-label"
                            for="HasSemester"
                        >
                            Has Semester
                        </label>

                    </div>

                </div>

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
                                    $gradeLevel->IsActive ?? true
                                )
                                    ? 'checked'
                                    : ''
                            }}
                        >

                        <label class="form-check-label">
                            Active Grade Level
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
                                ? 'Update Grade Level'
                                : 'Save Grade Level'
                        }}

                    </button>

                    <a
                        href="{{ route('grade-levels.index') }}"
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

            const educationLevel=
                document.getElementById('EducationLevel');

            const hasSemester=
                document.getElementById('HasSemester');

            function handleSemesterLogic(){

                if(educationLevel.value === 'SHS'){

                    hasSemester.checked = true;

                    hasSemester.disabled = true;

                }else{

                    hasSemester.checked = false;

                    hasSemester.disabled = true;

                }

            }

            handleSemesterLogic();

            educationLevel.addEventListener(
                'change',
                handleSemesterLogic
            );

            const form=
                document.getElementById('gradeLevelForm');

            form.addEventListener('submit',function(e){

                const gradeLevel=form
                    .querySelector('[name="GradeLevel"]')
                    .value
                    .trim();

                const education=form
                    .querySelector('[name="EducationLevel"]')
                    .value;

                if(!gradeLevel){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Grade level is required'
                    });

                    e.preventDefault();

                    return;
                }

                if(!education){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Education level is required'
                    });

                    e.preventDefault();

                    return;
                }

            });

        });

    </script>

@endsection
