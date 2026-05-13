@extends('layouts.app')

@section('title','Users')

@section('content')

    <x-page-header title="Logs Monitoring" subtitle="Manage system users">
        <x-slot:action>
            <a href="{{ route('scanner.index') }}" class="btn btn-primary btn-md">
                <i class="bi bi-plus"></i> Scanner
            </a>
        </x-slot:action>
    </x-page-header>

    <x-card>
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
