@extends('layouts.app')
@section('title','School Year Management')

@section('content')

    <x-page-header
        title="School Year Management"
        subtitle="Manage academic school years"
    >

        <x-slot:action>

            <button
                class="btn btn-primary btn-md"
                data-bs-toggle="modal"
                data-bs-target="#schoolYearModal"
            >
                <i class="bi bi-plus"></i>
                Add School Year
            </button>

        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="schoolYearsTable"

            :columns="[
                'Actions',
                'School Year',
                'Start Date',
                'End Date',
                'Status',
                'Created At'
            ]"

            :ajax="route('school_years.data')"

            :datatableColumns="[
                [
                    'data'=>'actions',
                    'orderable'=>false,
                    'searchable'=>false
                ],

                [
                    'data'=>'SchoolYear'
                ],

                [
                    'data'=>'StartDate'
                ],

                [
                    'data'=>'EndDate'
                ],

                [
                    'data'=>'IsActive'
                ],

                [
                    'data'=>'created_at'
                ]
            ]"
        />

    </x-card>

    <div
        class="modal fade"
        id="schoolYearModal"
        tabindex="-1"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0 shadow">

                <form
                    method="POST"
                    action="{{ route('school_years.store') }}"
                    id="schoolYearForm"
                >

                    @csrf

                    <input
                        type="hidden"
                        name="_method"
                        id="formMethod"
                        value="POST"
                    >

                    <div class="modal-header border-0">

                        <div>

                            <h5 class="modal-title fw-semibold">
                                School Year
                            </h5>

                            <div class="text-muted small">
                                Create or update school year
                            </div>

                        </div>

                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <x-form.group
                                name="SchoolYear"
                                label="School Year"
                                class="col-12"
                                required
                            >

                                <x-form.input
                                    name="SchoolYear"
                                    id="SchoolYear"
                                    placeholder="2025-2026"
                                />

                            </x-form.group>

                            <x-form.group
                                name="StartDate"
                                label="Start Date"
                                class="col-md-6"
                                required
                            >

                                <input
                                    type="date"
                                    name="StartDate"
                                    id="StartDate"
                                    class="form-control"
                                >

                            </x-form.group>

                            <x-form.group
                                name="EndDate"
                                label="End Date"
                                class="col-md-6"
                                required
                            >

                                <input
                                    type="date"
                                    name="EndDate"
                                    id="EndDate"
                                    class="form-control"
                                >

                            </x-form.group>

                            <div class="col-12">

                                <div class="form-check form-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="IsActive"
                                        id="IsActive"
                                        value="1"
                                    >

                                    <label
                                        class="form-check-label"
                                        for="IsActive"
                                    >
                                        Set as Active School Year
                                    </label>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer border-0">

                        <button
                            type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-check"></i>
                            Save School Year
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script>

        document.addEventListener('DOMContentLoaded',function(){
            const form=document.getElementById('schoolYearForm');
            const modal=document.getElementById('schoolYearModal');

            $(document).on('click','.editBtn',function(){
                const id=$(this).data('id');
                form.action=`/school-years/${id}`;
                $('#formMethod').val('PUT');
                $('#SchoolYear').val($(this).data('schoolyear'));
                $('#StartDate').val($(this).data('startdate'));
                $('#EndDate').val($(this).data('enddate'));
                $('#IsActive').prop(
                    'checked',
                    $(this).data('active') == 1
                );

                const modalInstance=
                    new bootstrap.Modal(modal);

                modalInstance.show();
            });

            modal.addEventListener('hidden.bs.modal',function(){
                form.reset();
                form.action=`{{ route('school_years.store') }}`;
                $('#formMethod').val('POST');

            });

            form.addEventListener('submit',function(e){
                const schoolYear=
                    $('#SchoolYear').val().trim();

                const startDate=
                    $('#StartDate').val();

                const endDate=
                    $('#EndDate').val();

                if(!schoolYear){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'School year is required'
                    });

                    e.preventDefault();

                    return;
                }

                if(!/^\\d{4}-\\d{4}$/.test(schoolYear)){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Invalid school year format'
                    });

                    e.preventDefault();

                    return;
                }

                if(!startDate || !endDate){

                    Swal.fire({
                        icon:'warning',
                        title:'Validation',
                        text:'Start date and end date are required'
                    });

                    e.preventDefault();

                    return;
                }

            });

        });

    </script>

@endsection
