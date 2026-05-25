@extends('layouts.app')

@section('title','Users')

@section('content')
    <div class="logs-monitor-wrapper">
        <div class="logs-monitor-header">
            <div class="logs-monitor-left">
                <div class="logs-monitor-icon">
                    <i class="bi bi-activity"></i>
                </div>
                <div>
                    <div class="logs-monitor-title">
                        Attendance Logs
                    </div>
                    <div class="logs-monitor-subtitle">
                        Real-time student and employee scanner logs
                    </div>
                </div>
            </div>
            <x-slot:action>

                <a
                    href="{{ route('scanner.index') }}"
                    class="btn btn-primary btn-md px-3"
                >
                    <i class="bi bi-upc-scan me-1"></i>
                    Scanner
                </a>

            </x-slot:action>
        </div>

        <x-card class="logs-monitor-card">
            <x-datatable
                id="usersTable"
                :columns="['Actions','Name','Mode','Date']"
                :ajax="route('logs.data')"
                :datatableColumns="[
                    ['data'=>'actions','orderable'=>false,'searchable'=>false],
                    ['data'=>'name'],
                    ['data'=>'mode'],
                    ['data'=>'created_at']
                ]"
                :filters="[
                    [
                        'name'=>'mode',
                        'label'=>'All Modes',
                        'type'=>'select',
                        'options'=>[
                            '1'=>'IN',
                            '0'=>'OUT'
                        ]
                    ],
                    [
                        'name'=>'date',
                        'label'=>'Date',
                        'type'=>'date'
                    ]
                ]"
            />

        </x-card>

    </div>

    @if(session('success'))

        <script>

            document.addEventListener('DOMContentLoaded',function(){

                Swal.fire({
                    icon:'success',
                    title:'Success',
                    text:'{{ session('success') }}',
                    timer:2000,
                    showConfirmButton:false
                });

            });

        </script>

    @endif

@endsection
