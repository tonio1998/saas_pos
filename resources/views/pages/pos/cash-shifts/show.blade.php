<?php
use App\Helpers\StatusHelper;

$difference = $shift->closed_at
    ? $shift->actual_cash - $expectedCash
    : 0;

$nonCashSales = $gcashSales + $bankTransferSales;
?>

@extends('layouts.app')

@section('title', 'View Cash Shift')
@section('shortText', 'Cash Shift Details')

@section('content')

    <x-page-header>

        <x-slot:action>

            <div class="d-flex gap-2">

                <a
                    href="{{ route('cashiering.cash-shifts.index') }}"
                    class="btn btn-light"
                >
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>

                @if(!$shift->closed_at)

                    <a
                        href="{{ route('cashiering.cash-shifts.edit', encryptId($shift->id)) }}"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-pencil-square"></i>
                        Edit
                    </a>

                @endif

                <button
                    class="btn btn-outline-secondary"
                    onclick="window.print()"
                >
                    <i class="bi bi-printer"></i>
                    Print
                </button>

            </div>

        </x-slot:action>

    </x-page-header>

    <div class="row g-3">

        <div class="col-12">

            <x-card>

                <div class="row align-items-center">

                    <div class="col-lg">

                        <small class="text-secondary">
                            Shift Code
                        </small>

                        <h3 class="fw-bold mb-1">
                            {{ $shift->shift_code }}
                        </h3>

                        <div class="small text-muted">

                            {{ $shift->drawer?->drawer_name }}

                            •

                            {{ $shift->cashier?->name }}

                        </div>

                    </div>

                    <div class="col-lg-auto text-lg-end mt-3 mt-lg-0">

                        {!! StatusHelper::badge($shift->status) !!}

                        <div class="small text-muted mt-2">

                            Opened

                            {{ StatusHelper::formatDateTime($shift->opened_at) }}

                        </div>

                        @if($shift->closed_at)

                            <div class="small text-muted">

                                Closed

                                {{ StatusHelper::formatDateTime($shift->closed_at) }}

                            </div>

                        @endif

                    </div>

                </div>

            </x-card>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Opening Cash
                    </small>

                    <h5 class="fw-bold text-primary mt-1 mb-0">
                        ₱{{ number_format($shift->opening_cash,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Cash Sales
                    </small>

                    <h5 class="fw-bold text-success mt-1 mb-0">
                        ₱{{ number_format($cashSales,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Non-Cash Sales
                    </small>

                    <h5 class="fw-bold text-info mt-1 mb-0">
                        ₱{{ number_format($nonCashSales,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Total Sales
                    </small>

                    <h5 class="fw-bold mt-1 mb-0">
                        ₱{{ number_format($totalSales,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Cash In
                    </small>

                    <h5 class="fw-bold text-success mt-1 mb-0">
                        ₱{{ number_format($cashIn,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Cash Out
                    </small>

                    <h5 class="fw-bold text-danger mt-1 mb-0">
                        ₱{{ number_format($cashOut,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Expected Cash
                    </small>

                    <h5 class="fw-bold text-warning mt-1 mb-0">
                        ₱{{ number_format($expectedCash,2) }}
                    </h5>

                </div>

            </div>

        </div>

        <div class="col-xl-3 col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body py-3">

                    <small class="text-secondary">
                        Actual Cash
                    </small>

                    <h5 class="fw-bold mt-1 mb-0">

                        @if($shift->closed_at)

                            ₱{{ number_format($shift->actual_cash,2) }}

                        @else

                            —

                        @endif

                    </h5>

                </div>

            </div>

        </div>

        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">

                    Cash Reconciliation

                </h6>

                <table class="table table-sm table-borderless align-middle mb-0">

                    <tr>

                        <td>Opening Cash</td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($shift->opening_cash,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td>Cash Sales</td>

                        <td class="text-end text-success fw-semibold">
                            + ₱{{ number_format($cashSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td>Cash In</td>

                        <td class="text-end text-success fw-semibold">
                            + ₱{{ number_format($cashIn,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td>Cash Out</td>

                        <td class="text-end text-danger fw-semibold">
                            - ₱{{ number_format($cashOut,2) }}
                        </td>

                    </tr>

                    <tr class="border-top">

                        <td class="fw-bold">
                            Expected Cash
                        </td>

                        <td class="text-end fw-bold text-primary">
                            ₱{{ number_format($expectedCash,2) }}
                        </td>

                    </tr>
                    @if($shift->closed_at)

                        <tr>

                            <td>
                                Actual Cash
                            </td>

                            <td class="text-end fw-semibold">
                                ₱{{ number_format($shift->actual_cash,2) }}
                            </td>

                        </tr>

                        <tr class="border-top">

                            <td class="fw-bold">
                                Difference
                            </td>

                            <td
                                class="text-end fw-bold
                            @class([
                                'text-success' => $difference > 0,
                                'text-danger' => $difference < 0,
                                'text-primary' => $difference == 0
                            ])"
                            >
                                {{ $difference > 0 ? '+' : '' }}
                                ₱{{ number_format($difference,2) }}
                            </td>

                        </tr>

                        <tr>

                            <td>Status</td>

                            <td class="text-end">

                                @if($difference > 0)

                                    <span class="badge bg-success">
                                    OVER
                                </span>

                                @elseif($difference < 0)

                                    <span class="badge bg-danger">
                                    SHORT
                                </span>

                                @else

                                    <span class="badge bg-primary">
                                    BALANCED
                                </span>

                                @endif

                            </td>

                        </tr>

                    @endif

                </table>

            </x-card>

        </div>

        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">
                    Sales Breakdown
                </h6>

                <table class="table table-sm table-borderless align-middle mb-0">

                    <tr>

                        <td width="55%">
                            Cash
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($cashSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td>
                            GCash
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($gcashSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td>
                            Bank Transfer
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($bankTransferSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td>
                            Non-Cash Sales
                        </td>

                        <td class="text-end fw-semibold text-info">
                            ₱{{ number_format($nonCashSales,2) }}
                        </td>

                    </tr>

                    <tr class="border-top">

                        <td class="fw-bold">
                            Total Sales
                        </td>

                        <td class="text-end fw-bold">
                            ₱{{ number_format($totalSales,2) }}
                        </td>

                    </tr>

                </table>

            </x-card>

        </div>

        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">
                    Shift Information
                </h6>

                <table class="table table-sm table-borderless align-middle mb-0">

                    <tr>

                        <td width="40%" class="text-secondary">
                            Shift Code
                        </td>

                        <td class="text-end fw-semibold">
                            {{ $shift->shift_code }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Cash Drawer
                        </td>

                        <td class="text-end">
                            {{ $shift->drawer?->drawer_name ?? '-' }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Cashier
                        </td>

                        <td class="text-end">
                            {{ $shift->cashier?->name ?? '-' }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Status
                        </td>

                        <td class="text-end">
                            {!! StatusHelper::badge($shift->status) !!}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Opened At
                        </td>

                        <td class="text-end">
                            {{ StatusHelper::formatDateTime($shift->opened_at) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Closed At
                        </td>

                        <td class="text-end">

                            {{ $shift->closed_at
                                ? StatusHelper::formatDateTime($shift->closed_at)
                                : '-' }}

                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Opening Cash
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($shift->opening_cash,2) }}
                        </td>

                    </tr>

                    @if($shift->closed_at)

                        <tr>

                            <td class="text-secondary">
                                Actual Cash
                            </td>

                            <td class="text-end fw-semibold">
                                ₱{{ number_format($shift->actual_cash,2) }}
                            </td>

                        </tr>

                    @endif

                </table>

            </x-card>

        </div>
        <div class="col-lg-6">

            <x-card>

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h6 class="fw-bold mb-0">
                        Cash Movement Summary
                    </h6>

                    <span class="badge bg-light text-dark">
                    Shift
                </span>

                </div>

                <table class="table table-sm table-borderless align-middle mb-0">

                    <tr>

                        <td width="45%" class="text-secondary">
                            Opening Cash
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($shift->opening_cash,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Cash Sales
                        </td>

                        <td class="text-end text-success fw-semibold">
                            ₱{{ number_format($cashSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Cash In
                        </td>

                        <td class="text-end text-success fw-semibold">
                            ₱{{ number_format($cashIn,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Cash Out
                        </td>

                        <td class="text-end text-danger fw-semibold">
                            ₱{{ number_format($cashOut,2) }}
                        </td>

                    </tr>

                    <tr class="border-top">

                        <td class="fw-bold">
                            Net Cash Movement
                        </td>

                        <td class="text-end fw-bold">
                            ₱{{ number_format($cashIn - $cashOut,2) }}
                        </td>

                    </tr>

                </table>

            </x-card>

        </div>

        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">
                    Sales Statistics
                </h6>

                <table class="table table-sm table-borderless align-middle mb-0">

                    <tr>

                        <td width="50%" class="text-secondary">
                            Cash Sales
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($cashSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Non-Cash Sales
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($nonCashSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Total Sales
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($totalSales,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Cash Percentage
                        </td>

                        <td class="text-end">

                            {{ $totalSales > 0
                                ? number_format(($cashSales / $totalSales) * 100,2)
                                : 0 }}%

                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Non-Cash Percentage
                        </td>

                        <td class="text-end">

                            {{ $totalSales > 0
                                ? number_format(($nonCashSales / $totalSales) * 100,2)
                                : 0 }}%

                        </td>

                    </tr>

                </table>

            </x-card>

        </div>

        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">
                    Verification
                </h6>

                <table class="table table-sm table-borderless mb-0">

                    <tr>

                        <td width="40%" class="text-secondary">
                            Verification
                        </td>

                        <td class="text-end">

                            @if(!$shift->closed_at)

                                <span class="badge bg-secondary">
                                Pending Closing
                            </span>

                            @elseif($difference == 0)

                                <span class="badge bg-success">
                                Balanced
                            </span>

                            @elseif($difference > 0)

                                <span class="badge bg-warning text-dark">
                                Over
                            </span>

                            @else

                                <span class="badge bg-danger">
                                Short
                            </span>

                            @endif

                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Expected Cash
                        </td>

                        <td class="text-end fw-semibold">
                            ₱{{ number_format($expectedCash,2) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Actual Cash
                        </td>

                        <td class="text-end fw-semibold">

                            {{ $shift->closed_at
                                ? '₱'.number_format($shift->actual_cash,2)
                                : '-' }}

                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Difference
                        </td>

                        <td
                            class="text-end fw-bold
                        @class([
                            'text-success'=>$difference>0,
                            'text-danger'=>$difference<0,
                            'text-primary'=>$difference==0
                        ])"
                        >

                            @if($shift->closed_at)

                                {{ $difference>0?'+':'' }}
                                ₱{{ number_format($difference,2) }}

                            @else

                                -

                            @endif

                        </td>

                    </tr>

                </table>

            </x-card>

        </div>
        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">
                    Remarks
                </h6>

                <div class="small text-muted">

                    {{ $shift->remarks ?: 'No remarks available.' }}

                </div>

            </x-card>

        </div>

        <div class="col-lg-6">

            <x-card>

                <h6 class="fw-bold mb-3">
                    Audit Trail
                </h6>

                <table class="table table-sm table-borderless align-middle mb-0">

                    <tr>

                        <td width="40%" class="text-secondary">
                            Created By
                        </td>

                        <td class="text-end fw-semibold">
                            {{ $shift->creator?->name ?? '-' }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Updated By
                        </td>

                        <td class="text-end fw-semibold">
                            {{ $shift->updater?->name ?? '-' }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Created At
                        </td>

                        <td class="text-end">
                            {{ StatusHelper::formatDateTime($shift->created_at) }}
                        </td>

                    </tr>

                    <tr>

                        <td class="text-secondary">
                            Updated At
                        </td>

                        <td class="text-end">
                            {{ StatusHelper::formatDateTime($shift->updated_at) }}
                        </td>

                    </tr>

                </table>

            </x-card>

        </div>

        @if($shift->closed_at)

            <div class="col-lg-6">

                <x-card>

                    <h6 class="fw-bold mb-3">
                        Owner Verification
                    </h6>

                    <table class="table table-sm table-borderless align-middle mb-3">

                        <tr>

                            <td width="40%" class="text-secondary">
                                Verification Status
                            </td>

                            <td class="text-end">

                                @if($difference == 0)

                                    <span class="badge bg-success">
                                    Ready for Approval
                                </span>

                                @elseif(abs($difference) <= 100)

                                    <span class="badge bg-warning text-dark">
                                    Needs Review
                                </span>

                                @else

                                    <span class="badge bg-danger">
                                    Investigation Required
                                </span>

                                @endif

                            </td>

                        </tr>

                        <tr>

                            <td class="text-secondary">
                                Expected Cash
                            </td>

                            <td class="text-end fw-semibold">
                                ₱{{ number_format($expectedCash,2) }}
                            </td>

                        </tr>

                        <tr>

                            <td class="text-secondary">
                                Actual Cash
                            </td>

                            <td class="text-end fw-semibold">
                                ₱{{ number_format($shift->actual_cash,2) }}
                            </td>

                        </tr>

                        <tr>

                            <td class="text-secondary">
                                Difference
                            </td>

                            <td
                                class="text-end fw-bold
                            @class([
                                'text-success'=>$difference>0,
                                'text-danger'=>$difference<0,
                                'text-primary'=>$difference==0
                            ])"
                            >

                                {{ $difference > 0 ? '+' : '' }}
                                ₱{{ number_format($difference,2) }}

                            </td>

                        </tr>

                    </table>

                    <div class="d-flex flex-wrap gap-2">

                        <button
                            type="button"
                            class="btn btn-success"
                        >
                            <i class="bi bi-check-circle me-1"></i>
                            Approve
                        </button>

                        <button
                            type="button"
                            class="btn btn-warning"
                        >
                            <i class="bi bi-search me-1"></i>
                            Review
                        </button>

                        <button
                            type="button"
                            class="btn btn-danger"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Reject
                        </button>

                    </div>

                </x-card>

            </div>

        @endif

        <div class="col-12">

            <x-card>

                <div class="d-flex justify-content-end flex-wrap gap-2">

                    <a
                        href="{{ route('cashiering.cash-shifts.index') }}"
                        class="btn btn-light"
                    >
                        <i class="bi bi-arrow-left me-1"></i>
                        Back
                    </a>

                    <button
                        type="button"
                        onclick="window.print()"
                        class="btn btn-outline-secondary"
                    >
                        <i class="bi bi-printer me-1"></i>
                        Print Summary
                    </button>

                    @if(!$shift->closed_at)

                        <a
                            href="{{ route('cashiering.cash-shifts.edit', encryptId($shift->id)) }}"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit Shift
                        </a>

                    @endif

                </div>

            </x-card>

        </div>

    </div>

@endsection
