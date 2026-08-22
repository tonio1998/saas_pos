@extends('layouts.app')

@section('title', 'Low Stock & Reorder Point Watchlist | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Low Stock & Reorder Point Watchlist</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Automated alerts for items reaching reorder thresholds requiring supplier replenishment.
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
        $totalAlertItems = $lowStockProducts->count();

        $getProdStock = function($p) {
            if ($p->variants && $p->variants->count() > 0) {
                $sum = (float) $p->variants->sum('stock_on_hand');
                return ($sum > 0 || (float)$p->stock_on_hand <= 0) ? $sum : (float)$p->stock_on_hand;
            }
            return (float)$p->stock_on_hand;
        };

        $outOfStockCount = $lowStockProducts->filter(fn($p) => $getProdStock($p) <= 0)->count();
        $lowStockCount = $lowStockProducts->filter(fn($p) => $getProdStock($p) > 0)->count();

        $totalReplenishCost = $lowStockProducts->sum(function($p) use ($getProdStock) {
            $reorderLevel = (float)($p->reorder_level ?? 10);
            $stockQty = $getProdStock($p);
            $deficit = max(0, $reorderLevel - $stockQty);
            return $deficit * (float)$p->cost_price;
        });
    @endphp

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Reorder Watchlist</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fff1f2;color:#e11d48;"><i class="bi bi-bell-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">{{ number_format($totalAlertItems) }} <span class="fs-6 text-muted fw-normal">SKUs</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Action Required</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Critical Out of Stock</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef2f2;color:#dc2626;"><i class="bi bi-x-circle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">{{ number_format($outOfStockCount) }} <span class="fs-6 text-muted fw-normal">items</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">0 Stock Remaining</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Low Stock Warning</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-exclamation-triangle-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-warning-emphasis mb-0">{{ number_format($lowStockCount) }} <span class="fs-6 text-muted fw-normal">items</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Below Reorder Threshold</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Est. Replenishment Cost</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-cart-plus-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-primary mb-0">₱{{ number_format($totalReplenishCost, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Estimated PO Budget</div>
            </div>
        </div>
    </div>

    {{-- Watchlist Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-shield-exclamation text-danger fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Low Stock Reorder Watchlist</h5>
            </div>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">Alert Items: {{ $totalAlertItems }}</span>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive rounded-3 border overflow-hidden">
                <table id="lowStockTable" class="likha-data-table align-middle w-100">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:120px;">Action</th>
                            <th>Item Details</th>
                            <th>Category</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Reorder Point</th>
                            <th class="text-end">Stock Deficit</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Replenish Cost</th>
                            <th>Alert Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts as $p)
                            @php
                                $hasVariants = $p->variants && $p->variants->count() > 0;
                                $variantStockSum = $hasVariants ? (float) $p->variants->sum('stock_on_hand') : 0;
                                $stockQty = ($hasVariants && ($variantStockSum > 0 || (float)$p->stock_on_hand <= 0))
                                    ? $variantStockSum
                                    : (float) $p->stock_on_hand;
                                $reorderLevel = (float) ($p->reorder_level ?? 10);
                                $deficit = max(0, $reorderLevel - $stockQty);
                                $cost = (float) $p->cost_price;
                                $replenishCost = $deficit * $cost;
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <a href="{{ route('products.stock.receive', encrypt($p->id)) }}" class="btn btn-success btn-sm font-mono fw-bold px-2.5 py-1 rounded-3 extra-small shadow-xs hover-lift d-inline-flex align-items-center gap-1" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                                        <i class="bi bi-box-arrow-in-down"></i> Receive
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:38px;height:38px;">
                                            @if($p->image)
                                                <img src="{{ asset('storage/' . $p->image) }}" class="rounded-3" style="width:100%;height:100%;object-fit:cover;" alt="Img">
                                            @else
                                                <i class="bi bi-box-seam text-secondary fs-6"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-1.5">
                                                <a href="{{ route('products.edit', encrypt($p->id)) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block" style="font-size:0.88rem;">
                                                    {{ $p->name }}
                                                </a>
                                                @if($hasVariants)
                                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle extra-small" style="background:#f3e8ff;color:#7e22ce;border-color:#e9d5ff;font-size:0.68rem;">
                                                        <i class="bi bi-layers-fill me-1"></i>{{ $p->variants->count() }} Variants
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="extra-small text-muted font-mono">SKU: {{ $p->sku ?: 'N/A' }}</div>
                                            @if($hasVariants)
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    @foreach($p->variants as $v)
                                                        <span class="badge bg-light text-dark border font-mono extra-small py-0.5 px-1.5" style="font-size:0.68rem;">
                                                            {{ $v->variant_name }}: <strong class="{{ (float)$v->stock_on_hand > 0 ? 'text-success' : 'text-danger' }}">{{ number_format($v->stock_on_hand) }}</strong>
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light border text-dark font-mono extra-small">
                                        {{ $p->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="text-end font-mono fs-6 fw-black {{ $stockQty <= 0 ? 'text-danger' : 'text-warning-emphasis' }}">
                                    {{ number_format($stockQty) }} <span class="extra-small text-muted fw-normal">{{ $p->unit->name ?? 'pcs' }}</span>
                                </td>
                                <td class="text-end font-mono fw-bold text-dark">
                                    {{ number_format($reorderLevel) }} <span class="extra-small text-muted fw-normal">{{ $p->unit->name ?? 'pcs' }}</span>
                                </td>
                                <td class="text-end font-mono fw-black text-danger">
                                    Short {{ number_format($deficit) }}
                                </td>
                                <td class="text-end font-mono fw-semibold text-dark">
                                    ₱{{ number_format($cost, 2) }}
                                </td>
                                <td class="text-end font-mono fw-bold text-primary">
                                    ₱{{ number_format($replenishCost, 2) }}
                                </td>
                                <td>
                                    @if($stockQty <= 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Out of Stock</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Low Stock Warning</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-check text-success fs-2 d-block mb-2"></i>
                                    No low stock warnings. All inventory SKUs are healthy!
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
            window.$('#lowStockTable').DataTable({
                responsive: true,
                order: [[2, 'asc']],
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
