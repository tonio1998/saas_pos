@extends('layouts.app')

@section('title', 'Stock Movement History | ' . $product->name)

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

@php
    if (!function_exists('parseVariantRemark')) {
        function parseVariantRemark(?string $r): array {
            if ($r && preg_match('/^\[Variant:\s*(.+?)\]\s*(.*)/s', $r, $m)) {
                return ['vname' => trim($m[1]), 'note' => trim($m[2])];
            }
            return ['vname' => null, 'note' => $r];
        }
    }

    $product->load('variants');
    $hasVariants   = $product->variants && $product->variants->count() > 0;
    $stocksList    = $product->stocks()->with('creator')->latest()->get();
    $currentOnHand = $hasVariants
        ? (float) $product->variants->sum('stock_on_hand')
        : (float) $product->stock_on_hand;
    $currentValuation = 0;
    if ($hasVariants) {
        foreach ($product->variants as $_cv) {
            $currentValuation += (float)$_cv->stock_on_hand * (float)($_cv->cost_price ?? $product->cost_price);
        }
    } else {
        $currentValuation = (float)$product->stock_on_hand * (float)$product->cost_price;
    }
    $totalInQty  = $stocksList->where('transaction_type', 'IN')->sum('quantity');
    $totalOutQty = $stocksList->where('transaction_type', 'OUT')->sum('quantity');

    // Group movements by variant name
    $byVariant = [];
    foreach ($stocksList as $_s) {
        $_parsed = parseVariantRemark($_s->remarks);
        $_key = $_parsed['vname'] ?? '__base__';
        $byVariant[$_key][] = ['stock' => $_s, 'note' => $_parsed['note']];
    }
@endphp

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('stocks.index') }}" class="btn btn-white border rounded-3 p-2 text-dark shadow-xs hover-lift d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-left fs-6"></i>
                </a>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">{{ $product->name }}</h4>
                        @if($hasVariants)
                            <span class="badge fw-bold" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.72rem;">
                                <i class="bi bi-layers-fill me-1"></i>{{ $product->variants->count() }} Variants
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-0 mt-0.5">Stock Movement & Audit History</p>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('products.stock.receive', encrypt($product->id)) }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg,#059669 0%,#047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-box-arrow-in-down fs-6"></i> Receive Stock In
            </a>
            <a href="{{ route('products.edit', encrypt($product->id)) }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-pencil-square text-secondary"></i> Edit Product
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- VARIANT PRODUCT LAYOUT                                       --}}
    {{-- ============================================================ --}}
    @if($hasVariants)

        {{-- Per-Variant SKU Stock Cards --}}
        <div class="row g-3 mb-4">
            @foreach($product->variants as $pv)
                @php
                    $pvStock   = (float) $pv->stock_on_hand;
                    $pvCost    = (float) ($pv->cost_price ?? $product->cost_price);
                    $pvPrice   = (float) ($pv->selling_price ?? $product->selling_price);
                    $pvVal     = $pvStock * $pvCost;
                    $pvReorder = (float) ($pv->reorder_level ?? $product->reorder_level ?? 10);
                @endphp
                <div class="col-6 col-md-3">
                    <div class="card h-100 border rounded-4 shadow-xs bg-white overflow-hidden">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="badge fw-bold d-inline-flex align-items-center gap-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.7rem;">
                                    <i class="bi bi-tag-fill"></i> {{ $pv->variant_name }}
                                </span>
                                @if($pvStock <= 0)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill extra-small" style="font-size:0.62rem;">Out of Stock</span>
                                @elseif($pvStock <= $pvReorder)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill extra-small" style="font-size:0.62rem;">Low Stock</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small" style="font-size:0.62rem;">In Stock</span>
                                @endif
                            </div>
                            <div class="font-mono fw-black fs-4 text-dark mb-0">{{ number_format($pvStock) }} <span class="fs-6 text-muted fw-normal">{{ $pv->unit->name ?? 'pcs' }}</span></div>
                            <div class="d-flex align-items-center gap-3 mt-1 extra-small text-muted font-mono">
                                <span>Cost: <strong class="text-dark">₱{{ number_format($pvCost, 2) }}</strong></span>
                                <span>Price: <strong class="text-success">₱{{ number_format($pvPrice, 2) }}</strong></span>
                            </div>
                            <div class="extra-small text-muted mt-1">Val: <strong style="color:#7c3aed;">₱{{ number_format($pvVal, 2) }}</strong></div>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="col-6 col-md-3">
                <div class="card h-100 border-2 rounded-4 shadow-xs bg-white" style="border-color:#7c3aed!important;">
                    <div class="card-body p-3">
                        <div class="extra-small fw-extrabold text-uppercase text-muted mb-2">Combined Total</div>
                        <div class="font-mono fw-black fs-4 mb-0" style="color:#7c3aed;">{{ number_format($currentOnHand) }} <span class="fs-6 text-muted fw-normal">total</span></div>
                        <div class="extra-small text-muted mt-1">All Variants Combined</div>
                        <div class="extra-small mt-1">Val: <strong style="color:#7c3aed;">₱{{ number_format($currentValuation, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Variant Tabbed Ledger --}}
        <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
            <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center text-white" style="background:linear-gradient(135deg,#059669 0%,#047857 100%);width:30px;height:30px;">
                        <i class="bi bi-journal-text fs-6"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0 fs-6">Stock Movement Audit Ledger</h5>
                        <small class="text-muted">Filtered per Variant SKU</small>
                    </div>
                </div>
                <span class="badge bg-light text-dark border font-mono fw-bold px-2.5 py-1">{{ $stocksList->count() }} Total Entries</span>
            </div>

            <div class="border-bottom px-3 pt-2 pb-0 bg-light">
                <ul class="nav nav-pills gap-1 pb-2" id="variantHistoryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold extra-small px-3 py-1.5 rounded-3" data-bs-toggle="pill" data-bs-target="#pane-all" type="button">
                            <i class="bi bi-layers me-1"></i>All Variants
                            <span class="badge bg-white text-dark border ms-1 font-mono" style="font-size:0.65rem;">{{ $stocksList->count() }}</span>
                        </button>
                    </li>
                    @foreach($product->variants as $pv)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold extra-small px-3 py-1.5 rounded-3" data-bs-toggle="pill" data-bs-target="#pane-v{{ $pv->id }}" type="button" style="color:#7c3aed;">
                                <i class="bi bi-tag-fill me-1"></i>{{ $pv->variant_name }}
                                <span class="badge bg-white border ms-1 font-mono" style="font-size:0.65rem;color:#7c3aed;">{{ count($byVariant[$pv->variant_name] ?? []) }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body p-3">
                <div class="tab-content">

                    {{-- ALL TAB --}}
                    <div class="tab-pane fade show active" id="pane-all">
                        <div class="table-responsive rounded-3 border overflow-hidden">
                            <table id="historyTableAll" class="likha-data-table align-middle w-100">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Type</th>
                                        <th>Variant</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-center">Transition</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Value</th>
                                        <th>Ref</th>
                                        <th>Remarks</th>
                                        <th>By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stocksList as $s)
                                        @php $p = parseVariantRemark($s->remarks); $isOut = in_array($s->transaction_type,['OUT','RETURN_OUT']); $qty=(float)$s->quantity; $vi=$qty*(float)($s->unit_cost?:$product->cost_price); @endphp
                                        <tr>
                                            <td class="font-mono text-dark fw-semibold" style="font-size:0.8rem;"><i class="bi bi-clock me-1 text-muted"></i>{{ $s->created_at?->format('M d, Y h:i A') }}</td>
                                            <td>
                                                @if($s->transaction_type=='IN') <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-down-left me-1"></i>IN</span>
                                                @elseif($s->transaction_type=='OUT') <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-up-right me-1"></i>OUT</span>
                                                @elseif($s->transaction_type=='ADJUSTMENT') <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-sliders me-1"></i>ADJ</span>
                                                @elseif($s->transaction_type=='RETURN_IN') <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-return-left me-1"></i>RTN IN</span>
                                                @elseif($s->transaction_type=='RETURN_OUT') <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-return-right me-1"></i>RTN OUT</span>
                                                @else <span class="badge bg-light text-dark border rounded-pill fw-bold px-2 py-1 extra-small">{{ $s->transaction_type }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($p['vname'])
                                                    <span class="badge font-mono fw-bold d-inline-flex align-items-center gap-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.7rem;"><i class="bi bi-tag-fill"></i>{{ $p['vname'] }}</span>
                                                @else
                                                    <span class="text-muted extra-small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end font-mono fw-bold {{ $isOut?'text-danger':'text-success' }}">{{ $isOut?'-':'+' }}{{ number_format($qty) }}</td>
                                            <td class="text-center font-mono extra-small"><span class="text-muted">{{ number_format($s->stock_before) }}</span> <i class="bi bi-arrow-right mx-1 text-muted"></i> <strong>{{ number_format($s->stock_after) }}</strong></td>
                                            <td class="text-end font-mono fw-bold text-dark">{{ $s->unit_cost ? '₱'.number_format($s->unit_cost,2) : '-' }}</td>
                                            <td class="text-end font-mono fw-bold {{ $isOut?'text-danger':'text-dark' }}">₱{{ number_format($vi,2) }}</td>
                                            <td class="small">{{ $s->reference_type ?: '-' }}</td>
                                            <td class="small text-muted">{{ $p['note'] ?: '-' }}</td>
                                            <td class="small fw-semibold text-dark">{{ $s->creator?->name ?: 'System' }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="10" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>No records found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- PER-VARIANT TABS --}}
                    @foreach($product->variants as $pv)
                        <div class="tab-pane fade" id="pane-v{{ $pv->id }}">
                            @php $vRows = $byVariant[$pv->variant_name] ?? []; @endphp
                            @if(count($vRows) === 0)
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                                    No movements recorded for <strong>{{ $pv->variant_name }}</strong> yet.
                                </div>
                            @else
                                <div class="table-responsive rounded-3 border overflow-hidden">
                                    <table id="historyTable{{ $pv->id }}" class="likha-data-table align-middle w-100">
                                        <thead>
                                            <tr>
                                                <th>Date & Time</th>
                                                <th>Type</th>
                                                <th class="text-end">Qty</th>
                                                <th class="text-center">Transition</th>
                                                <th class="text-end">Unit Cost</th>
                                                <th class="text-end">Value</th>
                                                <th>Ref</th>
                                                <th>Remarks</th>
                                                <th>By</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vRows as $row)
                                                @php $s=$row['stock']; $isOut=in_array($s->transaction_type,['OUT','RETURN_OUT']); $qty=(float)$s->quantity; $vi=$qty*(float)($s->unit_cost?:$product->cost_price); @endphp
                                                <tr>
                                                    <td class="font-mono text-dark fw-semibold" style="font-size:0.8rem;"><i class="bi bi-clock me-1 text-muted"></i>{{ $s->created_at?->format('M d, Y h:i A') }}</td>
                                                    <td>
                                                        @if($s->transaction_type=='IN') <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-down-left me-1"></i>IN</span>
                                                        @elseif($s->transaction_type=='OUT') <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-up-right me-1"></i>OUT</span>
                                                        @elseif($s->transaction_type=='ADJUSTMENT') <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fw-bold px-2 py-1 extra-small">ADJ</span>
                                                        @else <span class="badge bg-light text-dark border rounded-pill fw-bold px-2 py-1 extra-small">{{ $s->transaction_type }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end font-mono fw-bold {{ $isOut?'text-danger':'text-success' }}">{{ $isOut?'-':'+' }}{{ number_format($qty) }}</td>
                                                    <td class="text-center font-mono extra-small"><span class="text-muted">{{ number_format($s->stock_before) }}</span> <i class="bi bi-arrow-right mx-1 text-muted"></i> <strong>{{ number_format($s->stock_after) }}</strong></td>
                                                    <td class="text-end font-mono fw-bold text-dark">{{ $s->unit_cost ? '₱'.number_format($s->unit_cost,2) : '-' }}</td>
                                                    <td class="text-end font-mono fw-bold {{ $isOut?'text-danger':'text-dark' }}">₱{{ number_format($vi,2) }}</td>
                                                    <td class="small">{{ $s->reference_type ?: '-' }}</td>
                                                    <td class="small text-muted">{{ $row['note'] ?: '-' }}</td>
                                                    <td class="small fw-semibold text-dark">{{ $s->creator?->name ?: 'System' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

    @else
    {{-- ============================================================ --}}
    {{-- NON-VARIANT PRODUCT LAYOUT                                   --}}
    {{-- ============================================================ --}}

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Current On-Hand</span>
                        <div class="rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-box-seam fs-6"></i></div>
                    </div>
                    <div class="font-mono fs-4 fw-black text-dark mb-0">{{ number_format($currentOnHand) }} <span class="fs-6 text-muted fw-normal">units</span></div>
                    <div class="mt-1">
                        @if($currentOnHand <= 0) <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 extra-small fw-bold">Out of Stock</span>
                        @elseif($currentOnHand <= ($product->reorder_level ?? 10)) <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 extra-small fw-bold">Low Stock</span>
                        @else <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 extra-small fw-bold">In Stock</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Received</span>
                        <div class="rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-arrow-down-left-circle-fill fs-6"></i></div>
                    </div>
                    <div class="font-mono fs-4 fw-black text-primary mb-0">+{{ number_format($totalInQty) }} <span class="fs-6 text-muted fw-normal">units</span></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Sold / Out</span>
                        <div class="rounded-3 p-1.5" style="background:#fff1f2;color:#e11d48;"><i class="bi bi-arrow-up-right-circle-fill fs-6"></i></div>
                    </div>
                    <div class="font-mono fs-4 fw-black text-danger mb-0">-{{ number_format($totalOutQty) }} <span class="fs-6 text-muted fw-normal">units</span></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Stock Value</span>
                        <div class="rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-wallet2 fs-6"></i></div>
                    </div>
                    <div class="font-mono fs-4 fw-black mb-0" style="color:#7c3aed;">₱{{ number_format($currentValuation, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card border rounded-4 shadow-xs bg-white overflow-hidden">
            <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 d-flex align-items-center justify-content-center text-white" style="background:linear-gradient(135deg,#059669 0%,#047857 100%);width:32px;height:32px;"><i class="bi bi-journal-text fs-6"></i></div>
                    <div>
                        <h5 class="fw-bold text-dark mb-0 fs-6">Stock Transaction Audit Ledger</h5>
                        <small class="text-muted">Detailed timeline of inventory changes</small>
                    </div>
                </div>
                <span class="badge bg-light text-dark border font-mono fw-bold px-2.5 py-1">{{ $stocksList->count() }} Entries</span>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive rounded-3 border overflow-hidden">
                    <table id="historyTableBase" class="likha-data-table align-middle w-100">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th class="text-end">Qty</th>
                                <th class="text-center">Transition</th>
                                <th class="text-end">Unit Cost</th>
                                <th class="text-end">Value</th>
                                <th>Ref</th>
                                <th>Remarks</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocksList as $s)
                                @php $p=parseVariantRemark($s->remarks); $isOut=in_array($s->transaction_type,['OUT','RETURN_OUT']); $qty=(float)$s->quantity; $vi=$qty*(float)($s->unit_cost?:$product->cost_price); @endphp
                                <tr>
                                    <td class="font-mono text-dark fw-semibold" style="font-size:0.8rem;"><i class="bi bi-clock me-1 text-muted"></i>{{ $s->created_at?->format('M d, Y h:i A') }}</td>
                                    <td>
                                        @if($s->transaction_type=='IN') <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-down-left me-1"></i>IN</span>
                                        @elseif($s->transaction_type=='OUT') <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-arrow-up-right me-1"></i>OUT</span>
                                        @elseif($s->transaction_type=='ADJUSTMENT') <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill fw-bold px-2 py-1 extra-small"><i class="bi bi-sliders me-1"></i>ADJ</span>
                                        @elseif($s->transaction_type=='RETURN_IN') <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fw-bold px-2 py-1 extra-small">RTN IN</span>
                                        @else <span class="badge bg-light text-dark border rounded-pill fw-bold px-2 py-1 extra-small">{{ $s->transaction_type }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-mono fw-bold {{ $isOut?'text-danger':'text-success' }}">{{ $isOut?'-':'+' }}{{ number_format($qty) }}</td>
                                    <td class="text-center font-mono extra-small"><span class="text-muted">{{ number_format($s->stock_before) }}</span> <i class="bi bi-arrow-right mx-1 text-muted"></i> <strong>{{ number_format($s->stock_after) }}</strong></td>
                                    <td class="text-end font-mono fw-bold text-dark">{{ $s->unit_cost ? '₱'.number_format($s->unit_cost,2) : '-' }}</td>
                                    <td class="text-end font-mono fw-bold {{ $isOut?'text-danger':'text-dark' }}">₱{{ number_format($vi,2) }}</td>
                                    <td class="small">{{ $s->reference_type ?: '-' }}</td>
                                    <td class="small text-muted">{{ $p['note'] ?: '-' }}</td>
                                    <td class="small fw-semibold text-dark">{{ $s->creator?->name ?: 'System' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center py-5 text-muted"><i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>No records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endif

</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('[id^="historyTable"]').each(function () {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({ responsive: true, order: [[0, 'desc']], pageLength: 25 });
        }
    });
    $('#variantHistoryTabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
        const pane = $(e.target).data('bs-target');
        $(pane + ' table').each(function () {
            if ($.fn.DataTable.isDataTable(this)) {
                $(this).DataTable().columns.adjust().draw();
            }
        });
    });
});
</script>
@endpush
