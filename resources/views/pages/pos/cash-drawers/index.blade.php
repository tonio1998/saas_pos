@extends('layouts.app')

@section('title', 'Cash Drawers')
@section('shortText', 'Manage cash drawers')

@section('content')

    <x-page-header>

        <x-slot:action>
            <a
                href="{{ route('cashiering.cash-drawers.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus"></i>
                Add Drawer
            </a>
        </x-slot:action>

    </x-page-header>

    <x-card>

        <x-datatable
            id="cashDrawersTable"
            :columns="[
                'Actions',
                'Drawer Code',
                'Drawer Name',
                'Status',
                'Created At',
                'Created By'
            ]"
            :ajax="route('cashiering.cash-drawers.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'drawer_code'],
                ['data' => 'drawer_name'],
                ['data' => 'status'],
                ['data' => 'created_at'],
                ['data' => 'createdBy']
            ]"
        />

    </x-card>

@endsection
