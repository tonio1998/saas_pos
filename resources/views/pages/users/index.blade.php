@extends('layouts.app')

@section('title','Users')

@section('content')
    <x-page-header title="Users" subtitle="Manage system users">
    </x-page-header>
    <x-card>
        <x-datatable
            id="usersTable"
            :columns="['Actions','Photo','NFC','Name','Email', 'Logs','Role']"
            :ajax="route('users.data')"
            :datatableColumns="[
                ['data'=>'actions','orderable'=>false,'searchable'=>false],
                ['data'=>'filepath'],
                ['data' => 'nfc_code'],
                ['data'=>'name'],
                ['data'=>'email'],
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
