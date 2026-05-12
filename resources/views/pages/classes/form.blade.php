@extends('layouts.app')
@section('title','Classes Management')

@section('content')

    @php
        $isEdit = isset($section);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Classes' : 'Add Classes' }}"
        subtitle="Manage academic sections"
    >

        <x-slot:action>

            <a
                href="{{ route('classes.index') }}"
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
                    ? route('classes.update',encrypt($section->id))
                    : route('classes.store')
            }}"
            id="sectionForm"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">
                <x-form.group
                    name="GradeLevelID"
                    label="Grade Level"
                    class="col-md-4"
                    required
                >

                    <select
                        name="GradeLevelID"
                        id="GradeLevelID"
                        class="form-select"
                    >

                        <option value="">
                            Select Grade Level
                        </option>

                        @foreach($gradeLevels as $gradeLevel)

                            <option
                                value="{{ $gradeLevel->id }}"

                                data-level="{{ $gradeLevel->EducationLevel }}"

                                {{
                                    old(
                                        'GradeLevelID',
                                        $section->GradeLevelID ?? ''
                                    ) == $gradeLevel->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $gradeLevel->GradeLevel }}
                            </option>

                        @endforeach

                    </select>

                </x-form.group>

                <x-form.group
                    name="StrandID"
                    label="Strand"
                    class="col-md-4"
                >

                    <select
                        name="StrandID"
                        id="StrandID"
                        class="form-select"
                    >

                        <option value="">
                            Select Strand
                        </option>

                        @foreach($strands as $strand)

                            <option
                                value="{{ $strand->id }}"

                                {{
                                    old(
                                        'StrandID',
                                        $section->StrandID ?? ''
                                    ) == $strand->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $strand->StrandCode }}
                                -
                                {{ $strand->StrandName }}
                            </option>

                        @endforeach

                    </select>

                </x-form.group>

                <x-form.group
                    name="SectionName"
                    label="Section Name"
                    class="col-md-4"
                    required
                >

                    <x-form.input
                        name="SectionName"
                        value="{{ old('SectionName',$section->SectionName ?? '') }}"
                        placeholder="Einstein"
                    />

                </x-form.group>

                <x-form.group
                    name="Capacity"
                    label="Capacity"
                    class="col-md-3"
                >

                    <x-form.input
                        type="number"
                        min="1"
                        name="Capacity"
                        value="{{ old('Capacity',$section->Capacity ?? 50) }}"
                        placeholder="50"
                    />

                </x-form.group>

                <x-form.group
                    name="AdviserID"
                    label="Adviser"
                    class="col-md-6"
                >

                    <x-form.select
                        name="AdviserID"
                        ajax="{{ route('select2.employees') }}"
                        value="{{ old('AdviserID',$section->AdviserID ?? '') }}"
                        text="{{ ($section->adviser->FirstName ?? '').' '.($section->adviser->LastName ?? '') }}"
                        placeholder="Select Adviser"
                    />

                </x-form.group>

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
                                ? 'Update Classes'
                                : 'Save Classes'
                        }}

                    </button>

                    <a
                        href="{{ route('classes.index') }}"
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

        document.addEventListener(
            'DOMContentLoaded',
            function(){

                const sectionSelect =
                    $('#SectionID');

                function updatePreview(data){

                    $('#academicYearPreview').text(
                        data.academic_year || '-'
                    );

                    $('#semesterPreview').text(
                        data.semester || '-'
                    );

                    $('#gradeLevelPreview').text(
                        data.grade_level || '-'
                    );

                    $('#strandPreview').text(
                        data.strand || '-'
                    );

                    $('#adviserPreview').text(
                        data.adviser || '-'
                    );

                    $('#roomPreview').text(
                        data.room || '-'
                    );

                }

                sectionSelect.on(
                    'select2:select',
                    function(e){

                        const data =
                            e.params.data;

                        updatePreview(data);

                    }
                );

            }
        );

    </script>

@endsection
