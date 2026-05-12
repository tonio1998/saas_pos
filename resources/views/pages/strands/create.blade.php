@extends('layouts.app')
@section('title','Strand Management')

@section('content')

    @php
        $isEdit = isset($strand);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Strand' : 'Add Strand' }}"
        subtitle="Manage SHS strands"
    >

        <x-slot:action>

            <a
                href="{{ route('strands.index') }}"
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
                    ? route('strands.update',encrypt($strand->id))
                    : route('strands.store')
            }}"
            id="strandForm"
        >

            @csrf

            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group
                    name="StrandCode"
                    label="Strand Code"
                    class="col-md-4"
                    required
                >

                    <x-form.input
                        name="StrandCode"
                        value="{{ old('StrandCode',$strand->StrandCode ?? '') }}"
                        placeholder="STEM"
                    />

                </x-form.group>

                <x-form.group
                    name="StrandName"
                    label="Strand Name"
                    class="col-md-8"
                    required
                >

                    <x-form.input
                        name="StrandName"
                        value="{{ old('StrandName',$strand->StrandName ?? '') }}"
                        placeholder="Science, Technology, Engineering and Mathematics"
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
                                ? 'Update Strand'
                                : 'Save Strand'
                        }}

                    </button>

                    <a
                        href="{{ route('strands.index') }}"
                        class="btn btn-light"
                    >
                        Cancel
                    </a>

                </div>

            </div>

        </form>

    </x-card>

@endsection
