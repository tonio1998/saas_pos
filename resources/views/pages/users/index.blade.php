@extends('layouts.sa')
@section('title','Users')

@section('content')
    <x-page-header title="User Masterlist">
        <x-slot name="description">
            Manage all users in the system.
        </x-slot>
    </x-page-header>
    <x-card>
        <x-datatable
            id="usersTable"
            :columns="['Actions','Photo','NFC','Name','Email', 'School','Logs','Role']"
            :ajax="route('users.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'filepath'],
                ['data' => 'nfc_code'],
                ['data'=>'name'],
                ['data'=>'email'],
                ['data'=>'school'],
                ['data'=>'logs'],
                ['data'=>'role'],
            ]"
            :filters="[
                [
                    'name'=>'role',
                    'label'=>'All Roles',
                    'type'=>'select',
                    'options'=>[
                        'admin'=>'Admin',
                        'staff'=>'Staff',
                        'user'=>'User'
                    ]
                ]
            ]"
        >
        </x-datatable>
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
