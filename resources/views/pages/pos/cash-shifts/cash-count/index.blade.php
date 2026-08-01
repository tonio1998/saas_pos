@extends('layouts.app')

@section('title', 'Cash Shifts')
@section('shortText', 'Manage cash shifts')

@section('content')

    <x-page-header />

    <x-card>

        <x-datatable
            id="cashShiftsTable"
            :columns="[
                'Actions',
                'Shift Code',
                'Drawer',
                'Cashier',
                'Opening Cash',
                'Opened At',
                'Status'
            ]"
            :ajax="route('cashiering.cash-shifts.cash-count.data')"
            :datatableColumns="[
                ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                ['data' => 'shift_code'],
                ['data' => 'drawer'],
                ['data' => 'cashier'],
                ['data' => 'opening_cash'],
                ['data' => 'opened_at'],
                ['data' => 'status']
            ]"
        />

    </x-card>

@endsection
