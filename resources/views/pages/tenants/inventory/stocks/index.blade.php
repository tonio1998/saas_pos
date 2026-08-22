@extends('layouts.app')

@section('title', 'Current Stocks & Inventory Valuation | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-boxes fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Current Stocks & Inventory Valuation</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Monitor real-time inventory balances, capital valuation, retail value, and SKU stock status across all product variants.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('stocks.adjustments.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-sliders text-warning"></i>
                <span>Stock Adjustments</span>
            </a>

            <a href="{{ route('stocks.low-stocks.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-danger extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                <span>Low Stock Watchlist</span>
            </a>

            <a href="{{ route('products.create') }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>Add Product SKU</span>
            </a>
        </div>
    </div>

    @php
        $totalSkus = $products->count();

        $getProdStock = function($p) {
            if ($p->variants && $p->variants->count() > 0) {
                $sum = (float) $p->variants->sum('stock_on_hand');
                return ($sum > 0 || (float)$p->stock_on_hand <= 0) ? $sum : (float)$p->stock_on_hand;
            }
            return (float)$p->stock_on_hand;
        };

        $inStockCount = $products->filter(fn($p) => $getProdStock($p) > 0)->count();
        $lowStockCount = $products->filter(fn($p) => $getProdStock($p) > 0 && $getProdStock($p) <= ($p->reorder_level ?? 10))->count();
        $outOfStockCount = $products->filter(fn($p) => $getProdStock($p) <= 0)->count();
    @endphp

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- 1. Total Active SKUs --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Catalog SKUs</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-box-seam fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">{{ number_format($totalSkus) }} <span class="fs-6 text-muted fw-normal">items</span></div>
                <div class="mt-1">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 extra-small fw-bold">{{ $inStockCount }} In-Stock</span>
                </div>
            </div>
        </div>

        {{-- 2. Stock Health Alerts --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Inventory Health</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fff1f2;color:#e11d48;"><i class="bi bi-shield-exclamation fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-danger mb-0">{{ number_format($outOfStockCount) }} <span class="fs-6 text-muted fw-normal">out of stock</span></div>
                <div class="mt-1">
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 extra-small fw-bold">{{ $lowStockCount }} Low Stock</span>
                </div>
            </div>
        </div>

        {{-- 3. Capital Cost Valuation --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Capital Cost</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-tag-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-primary mb-0">₱{{ number_format($totalValuation, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Inventory Purchase Value</div>
            </div>
        </div>

        {{-- 4. Retail Potential Value --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Potential Retail Value</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-wallet2 fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-purple mb-0" style="color:#7c3aed;">₱{{ number_format($totalRetailValuation, 2) }}</div>
                <div class="text-success extra-small mt-1 fw-bold">
                    Est. Margin: ₱{{ number_format($totalRetailValuation - $totalValuation, 2) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Main Inventory Masterlist Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-table text-success fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Inventory Stock Balance Ledger</h5>
            </div>
            <span class="badge bg-light text-dark border font-mono">Total SKUs: {{ $totalSkus }}</span>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive rounded-3 border">
                <table id="stocksMasterTable" class="likha-data-table align-middle w-100">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:100px;">Actions</th>
                            <th>Item & Barcode</th>
                            <th>Category</th>
                            <th class="text-end">On-Hand Stock</th>
                            <th>Stock Status</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end">Selling Price</th>
                            <th class="text-end">Capital Valuation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $prod)
                            @php
                                $hasVariants = $prod->variants && $prod->variants->count() > 0;
                                $reorderLevel = (float) ($prod->reorder_level ?? 10);
                            @endphp

                            @if($hasVariants)
                                {{-- EACH VARIANT GETS ITS OWN INDIVIDUAL ROW --}}
                                @foreach($prod->variants as $v)
                                    @php
                                        $vStock = (float) $v->stock_on_hand;
                                        $vCost = (float) ($v->cost_price ?? $prod->cost_price);
                                        $vPrice = (float) ($v->selling_price ?? $prod->selling_price);
                                        $vValuation = $vStock * $vCost;
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-light border btn-sm rounded-2 extra-small font-mono fw-bold px-2.5" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}'>
                                                    Actions <i class="bi bi-chevron-down ms-1"></i>
                                                </button>
                                                <ul class="dropdown-menu shadow-lg border-0 font-mono small rounded-3" style="z-index:1080;">
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 text-success fw-semibold" href="{{ route('products.stock.receive', encrypt($prod->id)) }}?variant_id={{ $v->id }}">
                                                            <i class="bi bi-box-arrow-in-down"></i> Receive Stock In
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 text-warning-emphasis fw-semibold" href="{{ route('products.stock.adjustment', encrypt($prod->id)) }}">
                                                            <i class="bi bi-sliders"></i> Adjust Stock
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 text-primary fw-semibold" href="{{ route('products.stock.history', encrypt($prod->id)) }}">
                                                            <i class="bi bi-clock-history"></i> Movement History
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item d-flex align-items-center gap-2 text-dark" href="{{ route('products.edit', encrypt($prod->id)) }}">
                                                            <i class="bi bi-pencil"></i> Edit Product Master
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2.5">
                                                <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                                                    @if($prod->image)
                                                        <img src="{{ asset('storage/' . $prod->image) }}" class="rounded-3" style="width:100%;height:100%;object-fit:cover;" alt="Img">
                                                    @else
                                                        <i class="bi bi-box-seam text-secondary fs-5"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                                        <a href="{{ route('products.edit', encrypt($prod->id)) }}" class="fw-bold text-dark text-decoration-none hover-primary" style="font-size:0.88rem;">
                                                            {{ $prod->name }}
                                                        </a>
                                                        <span class="text-purple font-mono fw-bold" style="font-size:0.85rem;color:#7c3aed;">
                                                            — {{ $v->variant_name }}
                                                        </span>
                                                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle extra-small" style="background:#f3e8ff;color:#7e22ce;border-color:#e9d5ff;font-size:0.65rem;">
                                                            Variant
                                                        </span>
                                                    </div>
                                                    <div class="extra-small text-muted font-mono mt-0.5">
                                                        SKU: <span class="fw-semibold text-dark me-2">{{ $v->sku ?: ($prod->sku ?: 'N/A') }}</span>
                                                        Bar: <span class="fw-semibold text-dark">{{ $v->barcode ?: ($prod->barcode ?: 'N/A') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light border text-dark font-mono extra-small">
                                                {{ $prod->category->name ?? 'Uncategorized' }}
                                            </span>
                                        </td>
                                        <td class="text-end font-mono fs-6 fw-black text-dark">
                                            {{ number_format($vStock) }} <span class="extra-small text-muted fw-normal">{{ $prod->unit->name ?? 'pcs' }}</span>
                                        </td>
                                        <td>
                                            @if($vStock <= 0)
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Out of Stock</span>
                                            @elseif($vStock <= $reorderLevel)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Low Stock Warning</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">In Stock</span>
                                            @endif
                                        </td>
                                        <td class="text-end font-mono fw-semibold text-dark">
                                            ₱{{ number_format($vCost, 2) }}
                                        </td>
                                        <td class="text-end font-mono fw-bold text-success">
                                            ₱{{ number_format($vPrice, 2) }}
                                        </td>
                                        <td class="text-end font-mono fw-black text-purple" style="color:#7c3aed;">
                                            ₱{{ number_format($vValuation, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- SINGLE NORMAL PRODUCT ROW (WITHOUT VARIANTS) --}}
                                @php
                                    $stockQty = (float) $prod->stock_on_hand;
                                    $cost = (float) $prod->cost_price;
                                    $selling = (float) $prod->selling_price;
                                    $itemValuation = $stockQty * $cost;
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light border btn-sm rounded-2 extra-small font-mono fw-bold px-2.5" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}'>
                                                Actions <i class="bi bi-chevron-down ms-1"></i>
                                            </button>
                                            <ul class="dropdown-menu shadow-lg border-0 font-mono small rounded-3" style="z-index:1080;">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-success fw-semibold" href="{{ route('products.stock.receive', encrypt($prod->id)) }}">
                                                        <i class="bi bi-box-arrow-in-down"></i> Receive Stock In
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-warning-emphasis fw-semibold" href="{{ route('products.stock.adjustment', encrypt($prod->id)) }}">
                                                        <i class="bi bi-sliders"></i> Adjust Stock
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-primary fw-semibold" href="{{ route('products.stock.history', encrypt($prod->id)) }}">
                                                        <i class="bi bi-clock-history"></i> Movement History
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-dark" href="{{ route('products.edit', encrypt($prod->id)) }}">
                                                        <i class="bi bi-pencil"></i> Edit Product Master
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;">
                                                @if($prod->image)
                                                    <img src="{{ asset('storage/' . $prod->image) }}" class="rounded-3" style="width:100%;height:100%;object-fit:cover;" alt="Img">
                                                @else
                                                    <i class="bi bi-box-seam text-secondary fs-5"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <a href="{{ route('products.edit', encrypt($prod->id)) }}" class="fw-bold text-dark text-decoration-none hover-primary" style="font-size:0.88rem;">
                                                    {{ $prod->name }}
                                                </a>
                                                <div class="extra-small text-muted font-mono mt-0.5">
                                                    SKU: <span class="fw-semibold text-dark me-2">{{ $prod->sku ?: 'N/A' }}</span>
                                                    Bar: <span class="fw-semibold text-dark">{{ $prod->barcode ?: 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light border text-dark font-mono extra-small">
                                            {{ $prod->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="text-end font-mono fs-6 fw-black text-dark">
                                        {{ number_format($stockQty) }} <span class="extra-small text-muted fw-normal">{{ $prod->unit->name ?? 'pcs' }}</span>
                                    </td>
                                    <td>
                                        @if($stockQty <= 0)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Out of Stock</span>
                                        @elseif($stockQty <= $reorderLevel)
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">Low Stock Warning</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">In Stock</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-mono fw-semibold text-dark">
                                        ₱{{ number_format($cost, 2) }}
                                    </td>
                                    <td class="text-end font-mono fw-bold text-success">
                                        ₱{{ number_format($selling, 2) }}
                                    </td>
                                    <td class="text-end font-mono fw-black text-purple" style="color:#7c3aed;">
                                        ₱{{ number_format($itemValuation, 2) }}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No inventory items found.
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
            window.$('#stocksMasterTable').DataTable({
                responsive: true,
                order: [[1, 'asc']],
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
