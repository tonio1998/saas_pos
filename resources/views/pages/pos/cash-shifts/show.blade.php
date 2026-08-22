<?php
use App\Helpers\StatusHelper;

$difference = $shift->closed_at ? $shift->actual_cash - $expectedCash : 0;
$nonCashSales = $gcashSales + $bankTransferSales;

$isApproved = str_contains($shift->remarks ?? '', '[APPROVED BY SUPERVISOR');
$isRejected = str_contains($shift->remarks ?? '', '[REJECTED BY SUPERVISOR');
$isReviewed = str_contains($shift->remarks ?? '', '[REVIEW BY SUPERVISOR');

$cashPercent = $totalSales > 0 ? round(($cashSales / $totalSales) * 100, 1) : 0;
$gcashPercent = $totalSales > 0 ? round(($gcashSales / $totalSales) * 100, 1) : 0;
$bankPercent = $totalSales > 0 ? round(($bankTransferSales / $totalSales) * 100, 1) : 0;

$rawRemarks = trim($shift->remarks ?? '');
$remarksList = [];
if (!empty($rawRemarks)) {
    preg_match_all('/\[(.*?) BY SUPERVISOR \((.*?)\) ON (.*?)\]:\s*(.*?)(?=\[|$)/s', $rawRemarks, $matches, PREG_SET_ORDER);
    if (!empty($matches)) {
        foreach ($matches as $m) {
            $remarksList[] = [
                'action' => trim($m[1]),
                'supervisor' => trim($m[2]),
                'timestamp' => trim($m[3]),
                'note' => trim($m[4]),
            ];
        }
    }
}
?>

@extends('layouts.app')

@section('title', 'Executive Shift Audit #' . $shift->shift_code . ' | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header Bar --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <a href="{{ route('cashiering.cash-shifts.index') }}" class="btn btn-light border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                <i class="bi bi-arrow-left fs-6"></i>
            </a>
            <div class="kpi-icon-box blue" style="width:42px;height:42px;font-size:1.2rem;">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.4px;">Shift Audit: {{ $shift->shift_code }}</h4>
                    {!! StatusHelper::badge($shift->status) !!}
                    @if($isApproved)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 font-mono fw-bold extra-small">
                            <i class="bi bi-patch-check-fill me-1"></i>Approved
                        </span>
                    @endif
                </div>
                <p class="text-muted extra-small mb-0">
                    Cashier: <strong class="text-dark">{{ $shift->cashier?->name ?? 'Unassigned' }}</strong> &bull; 
                    Drawer: <strong class="text-dark">{{ $shift->drawer?->drawer_name ?? 'Default Register' }}</strong> &bull; 
                    Opened: <span class="font-mono text-dark">{{ StatusHelper::formatDateTime($shift->opened_at) }}</span>
                    @if($shift->closed_at)
                        &bull; Closed: <span class="font-mono text-dark">{{ StatusHelper::formatDateTime($shift->closed_at) }}</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cashiering.cash-shifts.index') }}" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-arrow-left"></i>
                <span>Masterlist</span>
            </a>

            <button type="button" onclick="window.print()" class="btn btn-light border rounded-3 px-3 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-printer text-secondary"></i>
                <span>Print Statement</span>
            </button>

            @if(!$shift->closed_at)
                <a href="{{ route('cashiering.cash-shifts.edit', encryptId($shift->id)) }}" class="btn btn-success rounded-3 px-3.5 py-1.5 fw-bold d-flex align-items-center gap-1.5 shadow-xs hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.85rem;">
                    <i class="bi bi-pencil-square fs-6"></i>
                    <span>Edit Shift</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Top 4 Standard Dashboard KPI Metric Cards --}}
    <div class="row g-3 mb-3">
        {{-- 1. Opening Cash Float --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card blue h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Opening Float</span>
                    <div class="kpi-icon-box blue"><i class="bi bi-wallet2"></i></div>
                </div>
                <div class="kpi-value font-mono">₱{{ number_format($shift->opening_cash, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-primary extra-small fw-bold"><i class="bi bi-info-circle-fill me-1"></i>Initial register float</span>
                </div>
            </div>
        </div>

        {{-- 2. Total Sales Revenue --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card emerald h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Shift Gross Sales</span>
                    <div class="kpi-icon-box emerald"><i class="bi bi-cash-coin"></i></div>
                </div>
                <div class="kpi-value font-mono text-success">₱{{ number_format($totalSales, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-success extra-small fw-bold"><i class="bi bi-graph-up-arrow me-1"></i>Total shift turnover</span>
                </div>
            </div>
        </div>

        {{-- 3. System Expected Cash --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card amber h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Expected Register Cash</span>
                    <div class="kpi-icon-box amber"><i class="bi bi-calculator"></i></div>
                </div>
                <div class="kpi-value font-mono text-amber">₱{{ number_format($expectedCash, 2) }}</div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    <span class="text-warning extra-small fw-bold"><i class="bi bi-calculator-fill me-1"></i>Calculated system cash</span>
                </div>
            </div>
        </div>

        {{-- 4. Audit Remittance Variance --}}
        <div class="col-6 col-md-3">
            <div class="likha-kpi-card @if($difference > 0) emerald @elseif($difference < 0) rose @else blue @endif h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label">Remittance Variance</span>
                    <div class="kpi-icon-box @if($difference > 0) emerald @elseif($difference < 0) rose @else blue @endif">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
                <div class="kpi-value font-mono @if($difference > 0) text-success @elseif($difference < 0) text-danger @else text-dark @endif">
                    @if($shift->closed_at)
                        {{ $difference > 0 ? '+' : '' }}₱{{ number_format($difference, 2) }}
                    @else
                        <span class="fs-6 text-muted font-mono">Session Active</span>
                    @endif
                </div>
                <div class="d-flex align-items-center justify-content-between gap-2 mt-auto pt-1">
                    @if(!$shift->closed_at)
                        <span class="text-primary extra-small fw-bold"><i class="bi bi-clock-history me-1"></i>Open Session</span>
                    @elseif($difference == 0)
                        <span class="text-success extra-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Perfect Balance</span>
                    @elseif($difference > 0)
                        <span class="text-success extra-small fw-bold"><i class="bi bi-plus-circle-fill me-1"></i>Overage Detected</span>
                    @else
                        <span class="text-danger extra-small fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Shortage Discrepancy</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Restructured 2-Column Grid Layout --}}
    <div class="row g-3">
        {{-- LEFT COLUMN: Financial Audit & Denomination Breakdown (col-lg-7) --}}
        <div class="col-lg-7">
            <div class="d-flex flex-column gap-3">
                {{-- 1. Cashier Remittance & Denomination Breakdown Table --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-bottom p-3.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <i class="bi bi-cash-stack fs-6"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Cashier Physical Remittance Breakdown</h6>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Denomination Audit</span>
                    </div>
                    <div class="card-body p-0">
                        @if(count($cashCounts) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 font-mono">
                                    <thead class="table-light extra-small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Denomination</th>
                                            <th class="text-center">Piece Count (Qty)</th>
                                            <th class="text-end pe-4">Subtotal Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cashCounts as $count)
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark">
                                                    @if($count->denomination >= 20)
                                                        💵 ₱{{ number_format($count->denomination) }} Bill
                                                    @elseif($count->denomination >= 1)
                                                        🪙 ₱{{ number_format($count->denomination) }} Coin
                                                    @else
                                                        🪙 Centavos / Loose Change
                                                    @endif
                                                </td>
                                                <td class="text-center fw-extrabold text-primary">
                                                    {{ number_format($count->quantity) }} pcs
                                                </td>
                                                <td class="text-end pe-4 fw-black text-dark fs-6">
                                                    ₱{{ number_format($count->amount, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light border-top">
                                        <tr>
                                            <th class="ps-4 fw-extrabold text-dark fs-6">Total Counted Remittance</th>
                                            <th class="text-center fw-extrabold text-primary fs-6">{{ number_format($cashCounts->sum('quantity')) }} pcs</th>
                                            <th class="text-end pe-4 font-mono fw-black text-success fs-5">₱{{ number_format($cashCounts->sum('amount'), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-safe2 fs-2 text-warning mb-2 d-block"></i>
                                <p class="mb-0 small fw-semibold">No denomination cash count recorded for this shift session yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. Cash Drawer Reconciliation Card --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-bottom p-3.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <i class="bi bi-calculator fs-6"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Cash Drawer Reconciliation Math</h6>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Formula Audit</span>
                    </div>
                    <div class="card-body p-3.5">
                        <table class="table table-hover align-middle mb-0 font-mono">
                            <tbody>
                                <tr>
                                    <td class="text-muted small py-2.5">Opening Register Float</td>
                                    <td class="text-end font-mono fw-bold text-dark py-2.5">₱{{ number_format($shift->opening_cash, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2.5">Add: Cash Sales Collected</td>
                                    <td class="text-end font-mono fw-bold text-success py-2.5">+ ₱{{ number_format($cashSales, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2.5">Add: Cash In (Petty Cash Added)</td>
                                    <td class="text-end font-mono fw-bold text-success py-2.5">+ ₱{{ number_format($cashIn, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2.5">Less: Cash Out (Payouts / Drops)</td>
                                    <td class="text-end font-mono fw-bold text-danger py-2.5">- ₱{{ number_format($cashOut, 2) }}</td>
                                </tr>
                                <tr class="table-light border-top">
                                    <td class="fw-extrabold text-dark py-3">System Expected Cash in Drawer</td>
                                    <td class="text-end font-mono fw-black text-primary fs-6 py-3">₱{{ number_format($expectedCash, 2) }}</td>
                                </tr>
                                @if($shift->closed_at)
                                    <tr>
                                        <td class="text-muted small py-2.5">Actual Counted Physical Cash</td>
                                        <td class="text-end font-mono fw-bold text-dark py-2.5">₱{{ number_format($shift->actual_cash, 2) }}</td>
                                    </tr>
                                    <tr class="border-top">
                                        <td class="fw-extrabold text-dark py-3">Discrepancy / Variance</td>
                                        <td class="text-end font-mono fw-black fs-6 py-3 @if($difference > 0) text-success @elseif($difference < 0) text-danger @else text-primary @endif">
                                            {{ $difference > 0 ? '+' : '' }}₱{{ number_format($difference, 2) }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3. Payment Channel Breakdown Card --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-bottom p-3.5 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <i class="bi bi-pie-chart fs-6"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Payment Channel Mix</h6>
                        </div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Revenue Mix</span>
                    </div>
                    <div class="card-body p-3.5">
                        <table class="table table-hover align-middle mb-3 font-mono">
                            <tbody>
                                <tr>
                                    <td class="text-muted small py-2.5">
                                        <i class="bi bi-cash-stack text-success me-1.5"></i> Cash Transactions
                                        <span class="badge bg-light text-dark border font-mono ms-2">{{ $cashPercent }}%</span>
                                    </td>
                                    <td class="text-end font-mono fw-bold text-dark py-2.5">₱{{ number_format($cashSales, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2.5">
                                        <i class="bi bi-qr-code text-primary me-1.5"></i> GCash Payments
                                        <span class="badge bg-light text-dark border font-mono ms-2">{{ $gcashPercent }}%</span>
                                    </td>
                                    <td class="text-end font-mono fw-bold text-dark py-2.5">₱{{ number_format($gcashSales, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2.5">
                                        <i class="bi bi-bank text-purple me-1.5"></i> Bank Transfer / Cards
                                        <span class="badge bg-light text-dark border font-mono ms-2">{{ $bankPercent }}%</span>
                                    </td>
                                    <td class="text-end font-mono fw-bold text-dark py-2.5">₱{{ number_format($bankTransferSales, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2.5">Subtotal Non-Cash Payments</td>
                                    <td class="text-end font-mono fw-bold text-info py-2.5">₱{{ number_format($nonCashSales, 2) }}</td>
                                </tr>
                                <tr class="table-light border-top">
                                    <td class="fw-extrabold text-dark py-3">Total Gross Sales Revenue</td>
                                    <td class="text-end font-mono fw-black text-success fs-6 py-3">₱{{ number_format($totalSales, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Visual Revenue Mix Progress Bar --}}
                        <div class="progress rounded-pill overflow-hidden" style="height:10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $cashPercent }}%" title="Cash: {{ $cashPercent }}%"></div>
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $gcashPercent }}%" title="GCash: {{ $gcashPercent }}%"></div>
                            <div class="progress-bar bg-purple" role="progressbar" style="width: {{ $bankPercent }}%" title="Bank: {{ $bankPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Supervisor Control, Metadata & Audit Trail (col-lg-5) --}}
        <div class="col-lg-5">
            <div class="d-flex flex-column gap-3">
                {{-- 1. Supervisor Approval & Verification Control Card --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-bottom p-3.5">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <i class="bi bi-check-all fs-6"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Supervisor Verification & Approval</h6>
                        </div>
                    </div>
                    <div class="card-body p-3.5">
                        @if($isApproved)
                            {{-- Verified Banner when Already Approved --}}
                            <div class="p-3.5 rounded-4 bg-success-subtle border border-success-subtle text-success mb-3 font-mono">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-patch-check-fill fs-3 text-success"></i>
                                        <div>
                                            <h6 class="fw-black mb-0 text-success">SHIFT VERIFIED & APPROVED</h6>
                                            <span class="extra-small text-success opacity-90">Audit verified & approved by Store Supervisor</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-success text-white rounded-pill px-3 py-1.5 extra-small font-mono fw-bold">APPROVED</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-success-subtle extra-small">
                                    <span class="text-success-emphasis">Audit Variance:</span>
                                    <span class="fw-bold @if($difference > 0) text-success @elseif($difference < 0) text-danger @else text-success @endif font-mono">
                                        {{ $difference > 0 ? '+' : '' }}₱{{ number_format($difference, 2) }}
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-outline-success border rounded-3 px-3 py-1.5 extra-small fw-bold hover-lift w-100" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                    <i class="bi bi-pencil-square me-1"></i> Update Audit Remarks
                                </button>
                            </div>
                        @elseif($shift->closed_at)
                            {{-- Unapproved Closed Shift: Show 3 Action Buttons --}}
                            <div class="p-3 rounded-3 bg-light border mb-3 font-mono">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Verification Status:</span>
                                    @if($difference == 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Ready for Approval</span>
                                    @elseif(abs($difference) <= 100)
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Needs Review</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Investigation Required</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Variance Amount:</span>
                                    <span class="font-mono fw-black fs-6 @if($difference > 0) text-success @elseif($difference < 0) text-danger @else text-dark @endif">
                                        {{ $difference > 0 ? '+' : '' }}₱{{ number_format($difference, 2) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Interactive Action Buttons --}}
                            <div class="d-flex flex-column gap-2">
                                {{-- 1. Approve Shift Button --}}
                                <form method="POST" action="{{ route('cashiering.cash-shifts.approve', encryptId($shift->id)) }}" class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-success fw-bold w-100 py-2 rounded-3 extra-small shadow-xs hover-lift d-flex align-items-center justify-content-center gap-1.5" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;" onclick="return confirm('Approve Shift #{{ $shift->shift_code }}?')">
                                        <i class="bi bi-check-circle-fill"></i>
                                        <span>Approve Shift</span>
                                    </button>
                                </form>

                                <div class="row g-2">
                                    <div class="col-6">
                                        {{-- 2. Review Discrepancy Button --}}
                                        <button type="button" class="btn btn-warning fw-bold w-100 py-2 rounded-3 extra-small text-dark shadow-xs hover-lift d-flex align-items-center justify-content-center gap-1.5" data-bs-toggle="modal" data-bs-target="#reviewModal" style="background:#f59e0b;border:none;">
                                            <i class="bi bi-search"></i>
                                            <span>Review Notes</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        {{-- 3. Reject & Audit Button --}}
                                        <button type="button" class="btn btn-danger fw-bold w-100 py-2 rounded-3 extra-small shadow-xs hover-lift d-flex align-items-center justify-content-center gap-1.5" data-bs-toggle="modal" data-bs-target="#rejectModal" style="background:#ef4444;border:none;">
                                            <i class="bi bi-x-circle-fill"></i>
                                            <span>Reject Shift</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-3 rounded-3 bg-light border text-center py-4">
                                <i class="bi bi-hourglass-split fs-3 text-warning mb-2 d-block"></i>
                                <h6 class="fw-bold text-dark mb-1 font-mono">Shift Session Active</h6>
                                <p class="text-muted extra-small mb-0">Supervisor verification will be unlocked once cashier closes the shift session and performs physical cash counting.</p>
                            </div>
                        @endif

                        {{-- Styled Audit Remarks & Verification Timeline --}}
                        <div class="mt-3.5 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-muted fw-bold extra-small text-uppercase font-mono">Audit Remarks & Verification Trail</small>
                                <span class="badge bg-light text-muted border font-mono extra-small">{{ count($remarksList) }} Entries</span>
                            </div>

                            @if(count($remarksList) > 0)
                                <div class="d-flex flex-column gap-2">
                                    @foreach($remarksList as $log)
                                        @php
                                            $act = strtoupper($log['action']);
                                            $badgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
                                            $icon = 'bi-info-circle-fill';
                                            if (str_contains($act, 'APPROV')) {
                                                $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                                $icon = 'bi-check-circle-fill';
                                            } elseif (str_contains($act, 'REJECT')) {
                                                $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                                                $icon = 'bi-x-circle-fill';
                                            } elseif (str_contains($act, 'REVIEW')) {
                                                $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                                                $icon = 'bi-search';
                                            }
                                        @endphp
                                        <div class="p-2.5 rounded-3 bg-light border font-mono">
                                            <div class="d-flex align-items-center justify-content-between mb-1.5">
                                                <span class="badge {{ $badgeClass }} border rounded-pill px-2.5 py-1 extra-small fw-bold">
                                                    <i class="bi {{ $icon }} me-1"></i>{{ $log['action'] }}
                                                </span>
                                                <small class="text-muted extra-small" style="font-size:0.7rem;">{{ $log['timestamp'] }}</small>
                                            </div>
                                            <div class="extra-small text-dark fw-bold mb-1">
                                                {{ $log['note'] }}
                                            </div>
                                            <div class="extra-small text-muted" style="font-size:0.7rem;">
                                                Supervisor: <strong class="text-dark">{{ $log['supervisor'] }}</strong>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(!empty($rawRemarks))
                                <div class="p-2.5 rounded-3 bg-light border font-mono extra-small text-dark">
                                    {{ $rawRemarks }}
                                </div>
                            @else
                                <p class="text-muted extra-small mb-0 italic">No additional audit notes recorded for this shift session.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 2. Shift Information & Cashier Metadata Card --}}
                <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                    <div class="card-header bg-transparent border-bottom p-3.5">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                <i class="bi bi-info-circle fs-6"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Shift Metadata & Cashier Info</h6>
                        </div>
                    </div>
                    <div class="card-body p-3.5">
                        <table class="table table-sm table-borderless align-middle mb-0 font-mono">
                            <tbody>
                                <tr>
                                    <td class="text-muted small py-2">Shift Reference Code</td>
                                    <td class="text-end font-mono fw-bold text-dark py-2">{{ $shift->shift_code }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2">Cash Drawer Assigned</td>
                                    <td class="text-end fw-semibold text-dark py-2">{{ $shift->drawer?->drawer_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2">Cashier Officer</td>
                                    <td class="text-end fw-semibold text-dark py-2">{{ $shift->cashier?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2">Shift Status</td>
                                    <td class="text-end py-2">{!! StatusHelper::badge($shift->status) !!}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2">Opened Date & Time</td>
                                    <td class="text-end font-mono text-dark py-2">{{ StatusHelper::formatDateTime($shift->opened_at) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted small py-2">Closed Date & Time</td>
                                    <td class="text-end font-mono text-dark py-2">{{ $shift->closed_at ? StatusHelper::formatDateTime($shift->closed_at) : 'Active Session' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 1: Review Discrepancy Modal --}}
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('cashiering.cash-shifts.verify-action', encryptId($shift->id)) }}">
                @csrf
                <input type="hidden" name="action_type" value="REVIEW">
                <div class="modal-header border-bottom p-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-search text-warning fs-5"></i>
                        <h6 class="modal-title font-mono fw-bold">Review Shift Discrepancy & Audit Notes</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted extra-small mb-3">Record supervisor findings or notes regarding the <strong>₱{{ number_format($difference, 2) }}</strong> variance for Shift <strong>#{{ $shift->shift_code }}</strong>.</p>
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Supervisor Review Findings / Remarks <span class="text-danger">*</span></label>
                        <textarea name="remarks" rows="3" class="form-control font-mono" placeholder="Enter findings (e.g., Valid cash drop found, missing petty cash voucher, etc.)..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-3 px-4 py-1.5 extra-small fw-bold text-dark">Save Audit Notes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 2: Reject & Audit Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('cashiering.cash-shifts.verify-action', encryptId($shift->id)) }}">
                @csrf
                <input type="hidden" name="action_type" value="REJECTED">
                <div class="modal-header border-bottom p-3.5 bg-danger text-white rounded-top-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-x-circle fs-5"></i>
                        <h6 class="modal-title font-mono fw-bold text-white">Reject Shift & Request Re-Audit</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted extra-small mb-3">Reject this shift remittance due to unresolved discrepancy (<strong>₱{{ number_format($difference, 2) }}</strong>) and flag for formal cashier re-audit.</p>
                    <div class="mb-3">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted">Rejection & Audit Instructions <span class="text-danger">*</span></label>
                        <textarea name="remarks" rows="3" class="form-control font-mono" placeholder="Specify reason for rejection and instructions for cashier/auditor..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-light border rounded-3 px-3 py-1.5 extra-small fw-bold text-muted" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-3 px-4 py-1.5 extra-small fw-bold">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
