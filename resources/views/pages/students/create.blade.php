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
            <a href="{{ route('residents.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('residents.update',encrypt($student->id)) : route('residents.store') }}"
              enctype="multipart/form-data">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="LRN" label="LRN" class="col-md-6" required>
                    <x-form.input
                        name="LRN"
                        value="{{ old('LRN',$student->LRN ?? '') }}"
                        placeholder="Enter learner reference number"
                        maxlength="12"
                    />
                </x-form.group>

                <x-form.group name="YearLevel" label="Year Level" class="col-md-6" required>
                    <select name="YearLevel" class="form-select">
                        <option value="">Select year level</option>
                        @foreach([7,8,9,10,11,12] as $year)
                            <option value="{{ $year }}" {{ old('YearLevel',$student->YearLevel ?? '') == $year ? 'selected':'' }}>
                                Grade {{ $year }}
                            </option>
                        @endforeach
                    </select>
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

                <x-form.group name="filepath" label="Upload Photo" class="col-md-6">
                    <x-form.input type="file" name="filepath" id="photo"/>
                </x-form.group>

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

                    <a href="{{ route('residents.index') }}" class="btn btn-light">
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

            const form=document.querySelector('form');

            form.addEventListener('submit',function(e){

                const LRN=form.querySelector('[name="LRN"]').value.trim();
                const FirstName=form.querySelector('[name="FirstName"]').value.trim();
                const LastName=form.querySelector('[name="LastName"]').value.trim();
                const Phone=form.querySelector('[name="PhoneNumber"]').value.trim();
                const YearLevel=form.querySelector('[name="YearLevel"]').value;
                const Section=form.querySelector('[name="Section"]').value.trim();

                if(!LRN){
                    Swal.fire({icon:'warning',title:'Validation',text:'LRN is required'});
                    e.preventDefault();
                    return;
                }

                if(!/^\d{12}$/.test(LRN)){
                    Swal.fire({icon:'warning',title:'Validation',text:'LRN must be 12 digits'});
                    e.preventDefault();
                    return;
                }

                if(!FirstName){
                    Swal.fire({icon:'warning',title:'Validation',text:'First name is required'});
                    e.preventDefault();
                    return;
                }

                if(!LastName){
                    Swal.fire({icon:'warning',title:'Validation',text:'Last name is required'});
                    e.preventDefault();
                    return;
                }

                if(Phone && !/^\+639\d{9}$/.test(Phone)){
                    Swal.fire({icon:'warning',title:'Validation',text:'Phone number must start with +639'});
                    e.preventDefault();
                    return;
                }

                if(!YearLevel){
                    Swal.fire({icon:'warning',title:'Validation',text:'Please select year level'});
                    e.preventDefault();
                    return;
                }

                if(!Section){
                    Swal.fire({icon:'warning',title:'Validation',text:'Section is required'});
                    e.preventDefault();
                    return;
                }

            });

            const photo=document.getElementById('photo');
            const preview=document.getElementById('preview');

            photo.addEventListener('change',function(){

                const file=this.files[0];
                if(!file)return;

                const reader=new FileReader();

                reader.onload=function(e){
                    preview.src=e.target.result;
                    preview.style.display='block';
                }

                reader.readAsDataURL(file);

            });

        });

    </script>

@endsection
