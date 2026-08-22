@extends('layouts.app')

@section('title', 'Cash Transaction Details')
@section('shortText', 'View cash transaction details')

@section('content')

    <x-page-header>
        <x-slot:action>
            <a href="{{ route('cashiering.cash-transactions.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back to List
            </a>
        </x-slot:action>
    </x-page-header>

    <div class="row g-4">

        <div class="col-lg-6">
            <x-card title="Transaction Info">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Shift Code</dt>
                    <dd class="col-sm-7">{{ $transaction->shift?->shift_code ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Drawer</dt>
                    <dd class="col-sm-7">{{ $transaction->drawer?->drawer_name ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Cashier</dt>
                    <dd class="col-sm-7">{{ $transaction->cashier?->name ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Type</dt>
                    <dd class="col-sm-7">{!! \App\Helpers\StatusHelper::badge($transaction->transaction_type) !!}</dd>

                    <dt class="col-sm-5 text-muted">Amount</dt>
                    <dd class="col-sm-7 fw-bold fs-5">&#8369;{{ number_format($transaction->amount, 2) }}</dd>

                    <dt class="col-sm-5 text-muted">Date</dt>
                    <dd class="col-sm-7">{{ $transaction->created_at ? \App\Helpers\StatusHelper::formatDateTime($transaction->created_at) : '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Reference #</dt>
                    <dd class="col-sm-7">{{ $transaction->reference_no ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Remarks</dt>
                    <dd class="col-sm-7">{{ $transaction->remarks ?? '-' }}</dd>
                </dl>
            </x-card>
        </div>

        <div class="col-lg-6">
            <x-card title="Audit Info">
                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Created By</dt>
                    <dd class="col-sm-7">{{ $transaction->creator?->name ?? '-' }}</dd>

                    <dt class="col-sm-5 text-muted">Updated By</dt>
                    <dd class="col-sm-7">{{ $transaction->updater?->name ?? '-' }}</dd>
                </dl>
            </x-card>
        </div>

    </div>

@endsection
