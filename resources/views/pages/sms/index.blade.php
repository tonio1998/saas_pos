@extends('layouts.sa')

@section('title','SMS Management')

@section('content')

    <div class="container-fluid px-0">

        <x-page-header
            title="SMS Queue"
            subtitle="Send SMS to students, parents, and employees"
        >
        </x-page-header>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-4">
                <x-datatable
                    id="studentsTable"

                    :columns="[
                        'Actions',
                        'Phone Number',
                        'Message',
                        'Status',
                        'Created At',
                    ]"

                    :ajax="route('sms.data')"

                    :datatableColumns="[

                        [
                            'data'=>'actions',
                            'orderable'=>false,
                            'searchable'=>false,
                            'width'=>'90px'
                        ],
                        [
                            'data'=>'phone_number'
                        ],
                        [
                            'data'=>'message'
                        ],
                        [
                            'data'=>'status'
                        ],
[
                            'data'=>'created_at'
                        ],

                    ]"
                />

            </div>

        </div>

    </div>

@endsection

@section('styles')


@endsection
