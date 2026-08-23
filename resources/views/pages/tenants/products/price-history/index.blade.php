@extends('layouts.app')

@section('title', 'Price History Audit & Intelligence - Enterprise POS & CRM')

@section('content')
<div class="container-fluid px-3 px-md-4 py-2.5">

    {{-- Page Header (Semi-Compact) --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-0.5">
                <div class="rounded-3 p-1.5 bg-purple bg-opacity-10 text-purple d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                    <i class="bi bi-graph-up-arrow text-purple fs-6"></i>
                </div>
                <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.3px;">Price History Audit & Intelligence</h4>
            </div>
            <p class="text-muted extra-small mb-0 ms-1">Track selling price adjustments, cost updates, and margin shifts across base products and variants</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-white border rounded-3 px-2.5 py-1.5 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-box-seam-fill text-primary"></i> Product Masterlist
            </a>
            @if($selectedProduct || request()->filled('item_type') || request()->filled('reason'))
                <a href="{{ route('products.price-history.index') }}" class="btn btn-light border border-danger-subtle rounded-3 px-2.5 py-1.5 fw-bold extra-small text-danger shadow-xs hover-lift d-flex align-items-center gap-1.5">
                    <i class="bi bi-x-circle-fill text-danger"></i> Reset Filters
                </a>
            @endif
        </div>
    </div>

    {{-- KPI Metrics Banner (Semi-Compact) --}}
    <div class="row g-2.5 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="extra-small fw-bold text-uppercase" style="color:#64748b;letter-spacing:0.5px;font-size:0.7rem;">Total Price Audits</span>
                    <div class="rounded-3 p-1 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                        <i class="bi bi-journal-text fs-6 text-primary"></i>
                    </div>
                </div>
                <h4 class="fw-black font-mono mb-0" style="color:#0f172a;">{{ number_format($totalLogs ?? 0) }}</h4>
                <div class="extra-small mt-0.5 font-mono" style="color:#64748b;font-size:0.7rem;">Recorded price changes</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="extra-small fw-bold text-uppercase" style="color:#64748b;letter-spacing:0.5px;font-size:0.7rem;">Variant Changes</span>
                    <div class="rounded-3 p-1 bg-purple bg-opacity-10 text-purple d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                        <i class="bi bi-tag-fill text-purple fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-black font-mono mb-0" style="color:#7e22ce;">{{ number_format($variantLogs ?? 0) }}</h4>
                <div class="extra-small mt-0.5 font-mono" style="color:#64748b;font-size:0.7rem;">Per-variant price updates</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="extra-small fw-bold text-uppercase" style="color:#64748b;letter-spacing:0.5px;font-size:0.7rem;">Base Item Updates</span>
                    <div class="rounded-3 p-1 bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                        <i class="bi bi-box-seam-fill text-info fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-black font-mono mb-0" style="color:#0284c7;">{{ number_format($baseLogs ?? 0) }}</h4>
                <div class="extra-small mt-0.5 font-mono" style="color:#64748b;font-size:0.7rem;">Base product price revisions</div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="extra-small fw-bold text-uppercase" style="color:#64748b;letter-spacing:0.5px;font-size:0.7rem;">Price Increases</span>
                    <div class="rounded-3 p-1 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                        <i class="bi bi-graph-up-arrow text-success fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-black font-mono mb-0" style="color:#166534;">{{ number_format($priceUpLogs ?? 0) }}</h4>
                <div class="extra-small mt-0.5 font-mono" style="color:#64748b;font-size:0.7rem;">Upward retail adjustments</div>
            </div>
        </div>
    </div>

    {{-- Single Product Active Filter Banner --}}
    @if($selectedProduct)
        <div class="alert border rounded-3 p-2.5 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background:#f3e8ff;border-color:#d8b4fe;color:#581c87;">
            <div class="d-flex align-items-center gap-2.5">
                <div class="rounded-circle p-1.5 d-flex align-items-center justify-content-center text-white" style="width:32px;height:32px;background:#7e22ce;">
                    <i class="bi bi-funnel-fill fs-6"></i>
                </div>
                <div>
                    <div class="fw-bold extra-small mb-0" style="color:#3b0764;">
                        Filtered Product: <span style="color:#7e22ce;">{{ $selectedProduct->name }}</span>
                    </div>
                    <div class="extra-small font-mono" style="color:#6b21a8;font-size:0.725rem;">
                        Audit logs for base product & all variants
                    </div>
                </div>
            </div>
            <a href="{{ route('products.price-history.index') }}" class="btn btn-sm btn-white border rounded-pill px-2.5 py-1 fw-bold extra-small shadow-xs hover-lift" style="color:#334155;">
                <i class="bi bi-x-circle-fill text-danger me-1"></i> Show All Products
            </a>
        </div>
    @endif

    {{-- Filter Bar (Semi-Compact) --}}
    <div class="card border-0 shadow-sm rounded-3 p-3 mb-3 bg-white">
        <div class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#475569;font-size:0.7rem;">Item Type Filter</label>
                <select id="filterItemType" class="form-select select2 form-select-sm" style="font-size:0.825rem;color:#0f172a;background-color:#fff;">
                    <option value="">All Item Types (Base & Variants)</option>
                    <option value="base" {{ request('item_type') == 'base' ? 'selected' : '' }}>Base Products Only</option>
                    <option value="variant" {{ request('item_type') == 'variant' ? 'selected' : '' }}>Variants Only</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label extra-small fw-bold text-uppercase mb-1" style="color:#475569;font-size:0.7rem;">Adjustment Reason</label>
                <select id="filterReason" class="form-select select2 form-select-sm" style="font-size:0.825rem;color:#0f172a;background-color:#fff;">
                    <option value="">All Adjustment Reasons</option>
                    <option value="Initial">Initial Price Setup</option>
                    <option value="Variant Price Update">Variant Price Update</option>
                    <option value="Stock Receive">Stock Receive Cost Adjustment</option>
                    <option value="Bulk Price Markup">Bulk Price Markup</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2 pt-2 pt-md-0">
                <button type="button" id="applyFiltersBtn" class="btn btn-primary btn-sm w-100 py-1.5 rounded-3 fw-bold extra-small shadow-xs">
                    <i class="bi bi-filter me-1"></i> Filter Logs
                </button>
                <button type="button" id="resetFiltersBtn" class="btn btn-light border btn-sm py-1.5 px-2.5 rounded-3 fw-bold extra-small" style="color:#475569;" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Datatable Card (Semi-Compact) --}}
    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
        <x-datatable
            id="priceHistoryTable"
            :columns="[
                'Actions',
                'Product & Item Identity',
                'Price Audit & Margin Shift',
                'Reason & Remarks',
                'Status',
                'Audit Date & Time',
                'Changed By'
            ]"
            :ajax="route('products.price-history.data', request()->only('product_id', 'item_type', 'reason'))"
            :datatableColumns="[
                [
                    'data' => 'actions',
                    'name' => 'actions',
                    'orderable' => false,
                    'searchable' => false,
                    'width' => '75px'
                ],
                [
                    'data' => 'product',
                    'name' => 'product.name',
                    'width' => '230px'
                ],
                [
                    'data' => 'price_changes',
                    'name' => 'price_changes',
                    'orderable' => false,
                    'searchable' => false,
                    'width' => '360px'
                ],
                [
                    'data' => 'remarks',
                    'name' => 'remarks',
                    'width' => '190px'
                ],
                [
                    'data' => 'status',
                    'name' => 'status',
                    'width' => '85px'
                ],
                [
                    'data' => 'created_at',
                    'name' => 'created_at',
                    'width' => '125px'
                ],
                [
                    'data' => 'createdBy',
                    'name' => 'createdBy.name',
                    'width' => '140px'
                ]
            ]"
        />
    </div>

</div>

{{-- Price History Audit Detail Modal --}}
<div class="modal fade" id="priceHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-2.5 px-3.5 bg-white d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-purple bg-opacity-10 text-purple d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                        <i class="bi bi-graph-up-arrow text-purple fs-6"></i>
                    </div>
                    <h6 class="modal-title fw-bold font-mono fs-6 mb-0" style="color:#0f172a;">Price Audit Details</h6>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3.5 bg-light" id="priceHistoryModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="extra-small mt-2 font-mono" style="color:#64748b;">Loading price audit record...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Filter Controls ──────────────────────────────────
    const applyBtn = document.getElementById('applyFiltersBtn');
    const resetBtn = document.getElementById('resetFiltersBtn');

    applyBtn?.addEventListener('click', function () {
        const itemType = document.getElementById('filterItemType').value;
        const reason   = document.getElementById('filterReason').value;

        const table = $('#priceHistoryTable').DataTable();
        let url = new URL(table.ajax.url(), window.location.origin);
        
        if (itemType) url.searchParams.set('item_type', itemType);
        else url.searchParams.delete('item_type');

        if (reason) url.searchParams.set('reason', reason);
        else url.searchParams.delete('reason');

        table.ajax.url(url.toString()).load();
    });

    resetBtn?.addEventListener('click', function () {
        document.getElementById('filterItemType').value = '';
        document.getElementById('filterReason').value   = '';
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#filterItemType, #filterReason').trigger('change');
        }

        const table = $('#priceHistoryTable').DataTable();
        let url = new URL(table.ajax.url(), window.location.origin);
        url.searchParams.delete('item_type');
        url.searchParams.delete('reason');
        table.ajax.url(url.toString()).load();
    });

    // ── Detail Modal View ────────────────────────────────
    $(document).on('click', '.btn-view-price-history', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const modalEl = document.getElementById('priceHistoryModal');
        const body = document.getElementById('priceHistoryModalBody');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        body.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-purple" role="status"></div>
                <p class="extra-small mt-2 font-mono" style="color:#64748b;">Loading price audit record...</p>
            </div>
        `;
        modal.show();

        $.getJSON(`/products/price-history/view/${id}`, function (res) {
            if (!res.success) return;

            const variantBadgeHtml = res.is_variant
                ? `<span class="badge extra-small fw-bold" style="background:#f3e8ff;color:#6b21a8;border:1px solid #d8b4fe;"><i class="bi bi-tag-fill me-1"></i>Variant: ${res.variant_name}</span>`
                : '<span class="badge extra-small fw-bold" style="background:#e0f2fe;color:#0369a1;border:1px solid #7dd3fc;"><i class="bi bi-box-seam me-1"></i>Base Product</span>';

            body.innerHTML = `
                <div class="card border-0 shadow-xs rounded-3 p-3 mb-3 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        ${variantBadgeHtml}
                        <span class="badge extra-small font-mono" style="background:#f1f5f9;color:#334155;border:1px solid #cbd5e1;">${res.reason}</span>
                    </div>
                    <h5 class="fw-black mb-1" style="color:#0f172a;">${res.product_name}</h5>
                    <div class="font-mono extra-small" style="color:#64748b;">
                        Barcode: <strong style="color:#334155;">${res.barcode}</strong> &bull; SKU: <strong style="color:#334155;">${res.sku}</strong>
                    </div>
                </div>

                <div class="card border-0 shadow-xs rounded-3 p-3 mb-3 bg-white">
                    <div class="extra-small fw-bold text-uppercase mb-2 font-mono" style="color:#64748b;letter-spacing:0.5px;font-size:0.7rem;">Pricing & Margin Audit Matrix</div>
                    
                    <div class="d-flex flex-column gap-2">
                        <div class="p-2 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <span class="extra-small font-mono fw-bold" style="color:#334155;">Selling Price</span>
                            <div class="font-mono extra-small">
                                <span class="text-decoration-line-through" style="color:#94a3b8;">₱${res.old_selling}</span>
                                <i class="bi bi-arrow-right mx-1" style="color:#94a3b8;"></i>
                                <span class="fw-black fs-6" style="color:#166534;">₱${res.new_selling}</span>
                            </div>
                        </div>

                        <div class="p-2 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <span class="extra-small font-mono fw-bold" style="color:#334155;">Capital Cost</span>
                            <div class="font-mono extra-small">
                                <span class="text-decoration-line-through" style="color:#94a3b8;">₱${res.old_cost}</span>
                                <i class="bi bi-arrow-right mx-1" style="color:#94a3b8;"></i>
                                <span class="fw-bold" style="color:#0f172a;">₱${res.new_cost}</span>
                            </div>
                        </div>

                        <div class="p-2 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                            <span class="extra-small font-mono fw-bold" style="color:#334155;">Profit Margin %</span>
                            <div class="font-mono extra-small">
                                <span style="color:#64748b;">${res.old_margin}%</span>
                                <i class="bi bi-arrow-right mx-1" style="color:#94a3b8;"></i>
                                <span class="fw-bold" style="color:${res.margin_diff >= 0 ? '#166534' : '#b91c1c'};">${res.new_margin}% (${res.margin_diff >= 0 ? '+' : ''}${res.margin_diff}%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-xs rounded-3 p-3 bg-white">
                    <div class="extra-small fw-bold text-uppercase mb-2 font-mono" style="color:#64748b;letter-spacing:0.5px;font-size:0.7rem;">Audit Metadata</div>
                    <div class="extra-small d-flex flex-column gap-1" style="color:#475569;">
                        <div><strong style="color:#1e293b;">Effective Date:</strong> ${res.effective_date}</div>
                        <div><strong style="color:#1e293b;">Recorded By:</strong> ${res.created_by} (${res.created_at})</div>
                        ${res.remarks ? `<div><strong style="color:#1e293b;">Remarks:</strong> ${res.remarks}</div>` : ''}
                    </div>
                </div>
            `;
        }).fail(function () {
            body.innerHTML = `
                <div class="text-center py-4 text-danger">
                    <i class="bi bi-exclamation-octagon-fill fs-3"></i>
                    <p class="extra-small mt-2 font-mono">Failed to load price audit details.</p>
                </div>
            `;
        });
    });

});
</script>
@endpush
@endsection
