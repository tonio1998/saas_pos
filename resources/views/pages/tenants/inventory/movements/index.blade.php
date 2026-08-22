@extends('layouts.app')

@section('title', 'Stock Movements & Audit Trail | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left-right fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Stock Movements & Audit Trail</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Complete system-wide audit trail of inventory receiving, POS sales deductions, physical stock adjustments, and returns.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('stocks.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-boxes text-success"></i>
                <span>Current Stocks</span>
            </a>

            <a href="{{ route('stocks.adjustments.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-sliders text-warning"></i>
                <span>Stock Adjustments</span>
            </a>
        </div>
    </div>

    @php
        $totalMovements = $movements->count();
        $totalStockIn = $movements->where('transaction_type', 'IN')->sum('quantity');
        $totalStockOut = $movements->where('transaction_type', 'OUT')->sum('quantity');
        $netMovement = $totalStockIn - $totalStockOut;
    @endphp

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Audit Logged</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-journal-text fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">{{ number_format($totalMovements) }} <span class="fs-6 text-muted fw-normal">logs</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Inventory Activity</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Stock In</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-arrow-down-left-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">+{{ number_format($totalStockIn) }} <span class="fs-6 text-muted fw-normal">units</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Supplier Restocks</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Stock Out</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fff1f2;color:#e11d48;"><i class="bi bi-arrow-up-right-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">-{{ number_format($totalStockOut) }} <span class="fs-6 text-muted fw-normal">units</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">POS Checkouts</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Net Stock Balance</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-calculator-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black {{ $netMovement >= 0 ? 'text-primary' : 'text-danger' }} mb-0">
                    {{ $netMovement >= 0 ? '+' : '' }}{{ number_format($netMovement) }} <span class="fs-6 text-muted fw-normal">units</span>
                </div>
                <div class="text-muted extra-small mt-1 fw-semibold">Inflow vs Outflow</div>
            </div>
        </div>
    </div>

    {{-- Main Movement Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-journal-check text-primary fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Global Inventory Movements Ledger</h5>
            </div>
            <span class="badge bg-light text-dark border font-mono">Total Logged Entries: {{ $totalMovements }}</span>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive rounded-3 border overflow-hidden">
                <table id="movementsTable" class="likha-data-table align-middle w-100">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Product Details</th>
                            <th>Movement Type</th>
                            <th class="text-end">Qty Change</th>
                            <th class="text-center">Stock Transition</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Value Impact</th>
                            <th>Reference</th>
                            <th>Remarks</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $m)
                            @php
                                $isOut = in_array($m->transaction_type, ['OUT', 'RETURN_OUT']);
                                $qtyVal = (float) $m->quantity;
                                $costVal = (float) ($m->unit_cost ?: $m->product?->cost_price ?? 0);
                                $valueImpact = $qtyVal * $costVal;
                            @endphp
                            <tr>
                                <td class="font-mono text-dark fw-semibold" style="font-size:0.8rem;">
                                    <i class="bi bi-clock me-1 text-muted"></i>{{ $m->created_at?->format('M d, Y h:i A') }}
                                </td>
                                <td>
                                    @if($m->product)
                                        <a href="{{ route('products.stock.history', encrypt($m->product->id)) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size:0.88rem;">
                                            {{ $m->product->name }}
                                        </a>
                                        <div class="extra-small text-muted font-mono">SKU: {{ $m->product->sku ?: 'N/A' }}</div>
                                    @else
                                        <span class="text-muted italic">Deleted Product</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($m->transaction_type)
                                        @case('IN')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-down-left me-1"></i>STOCK IN</span>
                                            @break
                                        @case('OUT')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-up-right me-1"></i>STOCK OUT</span>
                                            @break
                                        @case('ADJUSTMENT')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-sliders me-1"></i>ADJUSTMENT</span>
                                            @break
                                        @case('RETURN_IN')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-return-left me-1"></i>RETURN IN</span>
                                            @break
                                        @case('RETURN_OUT')
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1 fw-bold"><i class="bi bi-arrow-return-right me-1"></i>RETURN OUT</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold">{{ $m->transaction_type }}</span>
                                    @endswitch
                                </td>
                                <td class="text-end font-mono fw-bold {{ $isOut ? 'text-danger' : 'text-success' }}">
                                    {{ $isOut ? '-' : '+' }}{{ number_format($qtyVal) }}
                                </td>
                                <td class="text-center font-mono extra-small">
                                    <span class="text-muted">{{ number_format($m->stock_before) }}</span>
                                    <i class="bi bi-arrow-right mx-1 text-muted"></i>
                                    <strong class="text-dark fw-black">{{ number_format($m->stock_after) }}</strong>
                                </td>
                                <td class="text-end font-mono fw-semibold text-dark">
                                    ₱{{ number_format($costVal, 2) }}
                                </td>
                                <td class="text-end font-mono fw-bold {{ $isOut ? 'text-danger' : 'text-dark' }}">
                                    ₱{{ number_format($valueImpact, 2) }}
                                </td>
                                <td class="small">
                                    @if($m->reference_type)
                                        <span class="fw-bold text-dark font-mono d-block" style="font-size:0.75rem;">{{ $m->reference_type }}</span>
                                        @if($m->reference_id)
                                            <span class="text-muted font-mono extra-small">#{{ $m->reference_id }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="small text-muted">
                                    {{ $m->remarks ?: '-' }}
                                </td>
                                <td class="small fw-semibold text-dark">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <div class="rounded-circle bg-secondary bg-opacity-20 text-secondary d-flex align-items-center justify-content-center fw-bold extra-small" style="width:22px;height:22px;font-size:0.65rem;">
                                            {{ strtoupper(substr($m->creator?->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span>{{ $m->creator?->name ?: 'System' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    No stock movement records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            window.$('#movementsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 25
            });
        } else {
            setTimeout(checkJQuery, 50);
        }
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        checkJQuery();
    } else {
        document.addEventListener('DOMContentLoaded', checkJQuery);
    }
})();
</script>
@endpush
