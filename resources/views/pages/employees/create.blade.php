@extends('layouts.app')
@section('title','Employees Management')

@section('content')

    @php
        $isEdit = isset($teacher);
    @endphp

    <x-page-header
        title="{{ $isEdit ? 'Edit Employee' : 'Add Employee' }}"
        subtitle="Manage Employee"

    >

        <x-slot:action>
            <a href="{{ route('employees.index') }}" class="btn btn-light btn-md">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>

        <form method="POST"
              action="{{ $isEdit ? route('employees.update',encrypt($teacher->id)) : route('employees.store') }}">

            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="row g-4">

                <x-form.group name="FirstName" label="First Name" class="col-md-3" required>
                    <x-form.input
                        name="FirstName"
                        value="{{ old('FirstName',$teacher->FirstName ?? '') }}"
                        placeholder="Enter first name"
                    />
                </x-form.group>

                <x-form.group name="MiddleName" label="Middle Name" class="col-md-3">
                    <x-form.input
                        name="MiddleName"
                        value="{{ old('MiddleName',$teacher->MiddleName ?? '') }}"
                        placeholder="Enter middle name"
                    />
                </x-form.group>

                <x-form.group name="LastName" label="Last Name" class="col-md-3" required>
                    <x-form.input
                        name="LastName"
                        value="{{ old('LastName',$teacher->LastName ?? '') }}"
                        placeholder="Enter last name"
                    />
                </x-form.group>

                <x-form.group name="Suffix" label="Suffix" class="col-md-3"> <select name="Suffix" class="form-select">

                        <option value=""></option>
                        @foreach(['Jr','Sr','III'] as $suffix)
                            <option value="{{ $suffix }}" {{ old('Suffix',$teacher->Suffix ?? '') == $suffix ? 'selected':'' }}>
                                {{ $suffix }}
                            </option>
                        @endforeach
                    </select>
                </x-form.group>

                <x-form.group name="PhoneNumber" label="Phone Number" class="col-md-6">
                    <x-form.input
                        name="PhoneNumber"
                        value="{{ old('PhoneNumber',$teacher->PhoneNumber ?? '') }}"
                        placeholder="+639XXXXXXXXX"
                    />
                </x-form.group>

                <x-form.group name="Address" label="Address" class="col-md-6">
                    <x-form.input
                        name="Address"
                        value="{{ old('Address',$teacher->Address ?? '') }}"
                        placeholder="Enter address"
                    />
                </x-form.group>

            </div>

            <div class="d-flex justify-content-end">
                <div class="form-actions mt-4 d-flex align-items-center gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check"></i>
                        {{ $isEdit ? 'Update Employee' : 'Save Employee' }}
                    </button>

                    <a href="{{ route('employees.index') }}" class="btn btn-light">
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

                const FirstName=form.querySelector('[name="FirstName"]').value.trim();
                const LastName=form.querySelector('[name="LastName"]').value.trim();
                const Phone=form.querySelector('[name="PhoneNumber"]').value.trim();

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

            });

        });

    </script>

@endsection
