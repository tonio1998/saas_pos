@extends('layouts.app')

@section('title', 'Enterprise Product Masterlist & Inventory CRM')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <i class="bi bi-box-seam-fill fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Product Masterlist & Inventory CRM</h4>
                </div>
            </div>
            <p class="text-muted small mb-0">
                Enterprise multi-variant catalogue, live profit analytics, reorder tracking & cross-channel stock control.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Export CSV --}}
            <a href="{{ route('products.export-csv') }}" id="btnExportCsv" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-6"></i>
                <span>Export CSV</span>
            </a>

            {{-- Price History --}}
            <a href="{{ route('products.price-history.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-clock-history text-secondary fs-6"></i>
                <span>Price Audit History</span>
            </a>

            {{-- Add Product --}}
            <a href="{{ route('products.create') }}" class="btn btn-success rounded-3 px-3.5 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>+ Add Product</span>
            </a>
        </div>
    </div>

    {{-- Asynchronous CRM KPI Insight Cards (Non-blocking AJAX) --}}
    <div class="row g-3 mb-4">
        {{-- Total SKUs --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer kpi-card" onclick="setQuickTab('')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Active Master Catalogue</span>
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-boxes fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-dark font-mono mb-0" id="kpiTotalProducts">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                    <span class="text-muted extra-small">SKUs</span>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1.5">
                    <span class="badge bg-success-subtle text-success fw-bold extra-small border border-success-subtle" id="kpiActiveBadge">Active</span>
                    <span>in POS Terminal</span>
                </div>
            </div>
        </div>

        {{-- Low Stock Alerts --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer kpi-card" onclick="setQuickTab('low_stock')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-warning-emphasis extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Reorder Alert</span>
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-warning font-mono mb-0" id="kpiLowStock">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                    <span class="text-muted extra-small">SKUs</span>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1">
                    <i class="bi bi-arrow-down-circle text-warning"></i>
                    <span>At or below reorder threshold</span>
                </div>
            </div>
        </div>

        {{-- Out of Stock --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white hover-lift cursor-pointer kpi-card" onclick="setQuickTab('out_of_stock')">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-danger extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Out of Stock</span>
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-danger font-mono mb-0" id="kpiOutOfStock">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                    <span class="text-muted extra-small">items</span>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center gap-1">
                    <i class="bi bi-ban text-danger"></i>
                    <span>Depleted items (0 balance)</span>
                </div>
            </div>
        </div>

        {{-- Total Asset Valuation --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3.5 h-100 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;">Inventory Asset Value</span>
                    <div class="rounded-3 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                        <i class="bi bi-cash-coin fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-black text-success font-mono mb-0" id="kpiInventoryValue">
                        <span class="spinner-border spinner-border-sm text-muted"></span>
                    </h3>
                </div>
                <div class="mt-2 text-muted extra-small d-flex align-items-center justify-content-between">
                    <span>Retail Potential:</span>
                    <strong class="text-dark font-mono fw-bold" id="kpiRetailValue">...</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Preset Tabs --}}
    <div class="d-flex align-items-center gap-2 overflow-x-auto pb-2 mb-3">
        <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs quick-tab active" data-tab="">
            <i class="bi bi-grid-fill text-primary me-1"></i> All Products
        </button>
        <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs quick-tab" data-tab="in_stock">
            <i class="bi bi-check-circle-fill text-success me-1"></i> In Stock
        </button>
        <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs quick-tab" data-tab="low_stock">
            <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Low Stock Alert
        </button>
        <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs quick-tab" data-tab="out_of_stock">
            <i class="bi bi-x-circle-fill text-danger me-1"></i> Out of Stock
        </button>
        <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs quick-tab" data-type="fractional">
            <i class="bi bi-calculator text-info me-1"></i> ⚖️ Fractional / Weighed
        </button>
        <button type="button" class="btn btn-sm btn-white border rounded-pill px-3 py-1.5 fw-bold extra-small shadow-xs quick-tab" data-type="variants">
            <i class="bi bi-boxes text-purple me-1"></i> 📦 Multi-Variants
        </button>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        
        {{-- Advanced Filter Bar --}}
        <div class="card-header bg-transparent border-bottom p-3.5">
            <div class="row g-2.5 align-items-center">
                
                {{-- Category Filter --}}
                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Category</label>
                    <select name="category_id" id="filterCategory" class="form-select form-select-sm datatable-external-filter rounded-3 shadow-xs">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Stock Status Filter --}}
                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Stock Level</label>
                    <select name="stock_status" id="filterStockStatusSelect" class="form-select form-select-sm datatable-external-filter rounded-3 shadow-xs">
                        <option value="">All Stock Levels</option>
                        <option value="in_stock">✅ In Stock (> 0)</option>
                        <option value="low_stock">⚠️ Low Stock (≤ Reorder)</option>
                        <option value="out_of_stock">❌ Out of Stock (0 qty)</option>
                    </select>
                </div>

                {{-- Product Type Filter --}}
                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Product Type</label>
                    <select name="product_type" id="filterProductType" class="form-select form-select-sm datatable-external-filter rounded-3 shadow-xs">
                        <option value="">All Types</option>
                        <option value="standard">Standard Products</option>
                        <option value="fractional">⚖️ Weighed / Loose Goods</option>
                        <option value="variants">📦 With Multi-Variants</option>
                    </select>
                </div>

                {{-- Margin Tier Filter --}}
                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Profit Margin</label>
                    <select name="margin_tier" id="filterMarginTier" class="form-select form-select-sm datatable-external-filter rounded-3 shadow-xs">
                        <option value="">All Margins</option>
                        <option value="high">🚀 High Margin (≥ 30%)</option>
                        <option value="medium">⚖️ Healthy Margin (15-30%)</option>
                        <option value="low">⚠️ Low Margin (0-15%)</option>
                        <option value="negative">🚨 Negative Margin (Loss)</option>
                    </select>
                </div>

                {{-- Price Range Filter --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Price Range (₱)</label>
                    <div class="input-group input-group-sm">
                        <input type="number" name="price_min" id="filterPriceMin" class="form-control datatable-external-filter font-mono" placeholder="Min">
                        <span class="input-group-text bg-light text-muted px-1.5">-</span>
                        <input type="number" name="price_max" id="filterPriceMax" class="form-control datatable-external-filter font-mono" placeholder="Max">
                    </div>
                </div>

                {{-- Status & Reset --}}
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Status / Reset</label>
                    <div class="d-flex gap-1.5">
                        <select name="status" id="filterStatus" class="form-select form-select-sm datatable-external-filter rounded-3 shadow-xs">
                            <option value="">All Statuses</option>
                            <option value="active">Active in POS</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button type="button" id="btnResetFilters" class="btn btn-sm btn-light border rounded-3 px-2.5 text-muted shadow-xs hover-lift" title="Reset All Filters">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- Floating / Sticky Bulk Batch Operations Bar --}}
        <div id="bulkActionsBar" class="d-none bg-dark text-white p-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success font-mono fs-6 fw-bold" id="selectedCountBadge">0</span>
                <span class="fw-bold small text-light">products selected</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 extra-small fw-bold" id="btnBulkActivate">
                    <i class="bi bi-check-circle me-1 text-success"></i> Set Active
                </button>
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 extra-small fw-bold" id="btnBulkDeactivate">
                    <i class="bi bi-slash-circle me-1 text-warning"></i> Set Inactive
                </button>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 extra-small fw-bold text-dark shadow-xs" data-bs-toggle="modal" data-bs-target="#bulkCategoryModal">
                    <i class="bi bi-folder-fill me-1 text-primary"></i> Change Category
                </button>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 extra-small fw-bold text-dark shadow-xs" data-bs-toggle="modal" data-bs-target="#bulkPriceModal">
                    <i class="bi bi-percent me-1 text-success"></i> Batch Markup %
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 extra-small fw-bold" id="btnBulkDelete">
                    <i class="bi bi-trash3-fill me-1"></i> Delete Selected
                </button>
                <button type="button" class="btn btn-sm btn-link text-white-50 p-1 extra-small text-decoration-none" id="btnDeselectAll">
                    Deselect All
                </button>
            </div>
        </div>

        {{-- Table Body --}}
        <div class="card-body pt-3">
            @php
                $dtColumns = [
                    '<div class="text-center"><input type="checkbox" class="form-check-input shadow-xs" id="selectAllProducts" style="cursor:pointer;"></div>',
                    'Product & Code',
                    'Category & Unit',
                    'Stock Balance',
                    'Pricing Matrix & Margin',
                    'Status',
                    'Actions',
                ];

                $dtDefs = [
                    ['data' => 'checkbox', 'orderable' => false, 'searchable' => false, 'width' => '40px', 'className' => 'align-middle text-center'],
                    ['data' => 'product_info', 'name' => 'product_info', 'orderable' => true, 'className' => 'align-middle'],
                    ['data' => 'category_unit', 'orderable' => false, 'className' => 'align-middle'],
                    ['data' => 'stock_status', 'orderable' => false, 'className' => 'align-middle'],
                    ['data' => 'pricing_matrix', 'orderable' => false, 'className' => 'align-middle'],
                    ['data' => 'status_badge', 'orderable' => false, 'className' => 'align-middle text-center'],
                    ['data' => 'actions', 'orderable' => false, 'searchable' => false, 'width' => '100px', 'className' => 'align-middle text-center'],
                ];
            @endphp

            <x-datatable
                id="productssTables"
                :columns="$dtColumns"
                :ajax="route('products.data')"
                :datatableColumns="$dtDefs"
            />
        </div>

    </div>

</div>

{{-- 360° CRM Product Quick View Offcanvas Drawer --}}
<div class="offcanvas offcanvas-end shadow-lg border-0" tabindex="-1" id="productQuickViewDrawer" style="width:560px;max-width:95vw;">
    <div class="offcanvas-header bg-white border-bottom p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-4 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                <i class="bi bi-boxes fs-4"></i>
            </div>
            <div>
                <h5 class="offcanvas-title fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.3px;">360° Product CRM View</h5>
                <p class="text-muted extra-small mb-0">Real-time inventory intelligence & sales performance</p>
            </div>
        </div>
        <button type="button" class="btn-close text-reset p-2" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-4 p-md-4" id="drawerContent">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
            <p class="text-muted small mt-3 fw-semibold">Loading 360° product intelligence...</p>
        </div>
    </div>
</div>

{{-- Modal: Bulk Category Change --}}
<div class="modal fade" id="bulkCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-folder-fill text-primary me-2"></i>Change Category (Bulk)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Assign a new category to all selected products:</p>
                <div class="mb-3">
                    <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Target Category</label>
                    <select id="bulkTargetCategory" class="form-select">
                        <option value="">-- Remove Category (Uncategorized) --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-3 px-4 fw-bold" id="btnConfirmBulkCategory">Apply Category</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Bulk Price Markup --}}
<div class="modal fade" id="bulkPriceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-percent text-success me-2"></i>Batch Price Adjustment / Markup
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Adjust retail selling price for all selected items:</p>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Percentage Adjustment (+ or -)</label>
                        <div class="input-group">
                            <input type="number" step="0.1" id="bulkMarkupPercent" class="form-control font-mono fw-bold" placeholder="e.g. 10 for +10%, -5 for -5%">
                            <span class="input-group-text font-mono fw-bold bg-light">%</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Or Fixed Amount Adjustment (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text font-mono fw-bold bg-light">₱</span>
                            <input type="number" step="0.01" id="bulkMarkupFixed" class="form-control font-mono fw-bold" placeholder="e.g. 5.00 for +₱5.00">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success rounded-3 px-4 fw-bold" id="btnConfirmBulkPrice">Update Selling Prices</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Asynchronous KPI Stats Fetcher
    function loadKpiStats() {
        $.getJSON("{{ route('products.kpi-stats') }}", function (data) {
            $('#kpiTotalProducts').text(data.total_products.toLocaleString());
            $('#kpiActiveBadge').text(`${data.active_products.toLocaleString()} Active`);
            $('#kpiLowStock').text(data.low_stock_count.toLocaleString());
            $('#kpiOutOfStock').text(data.out_of_stock_count.toLocaleString());
            $('#kpiInventoryValue').text(data.formatted_inventory_value);
            $('#kpiRetailValue').text(data.formatted_retail_value);
        });
    }

    function updateExportLink() {
        const params = new URLSearchParams();
        const cat = $('#filterCategory').val();
        const stock = $('#filterStockStatusSelect').val();
        const type = $('#filterProductType').val();
        const status = $('#filterStatus').val();

        if (cat) params.append('category_id', cat);
        if (stock) params.append('stock_status', stock);
        if (type) params.append('product_type', type);
        if (status) params.append('status', status);

        const baseUrl = "{{ route('products.export-csv') }}";
        const queryStr = params.toString();
        $('#btnExportCsv').attr('href', queryStr ? `${baseUrl}?${queryStr}` : baseUrl);
    }

    function setQuickTab(status) {
        $('.quick-tab').removeClass('active');
        $(`.quick-tab[data-tab="${status}"]`).addClass('active');

        $('#filterStockStatusSelect').val(status);
        $('#filterProductType').val('');
        reloadTable();
    }

    function reloadTable() {
        if (window.$ && $.fn.DataTable.isDataTable('#productssTables')) {
            $('#productssTables').DataTable().ajax.reload(null, false);
        }
        updateExportLink();
        updateBulkBar();
    }

    function updateBulkBar() {
        const checked = $('.product-checkbox:checked');
        const count = checked.length;
        const bar = document.getElementById('bulkActionsBar');
        const badge = document.getElementById('selectedCountBadge');

        if (count > 0) {
            bar.classList.remove('d-none');
            badge.textContent = count;
        } else {
            bar.classList.add('d-none');
            $('#selectAllProducts').prop('checked', false);
        }
    }

    function getSelectedIds() {
        const ids = [];
        $('.product-checkbox:checked').each(function () {
            ids.push(parseInt($(this).val()));
        });
        return ids;
    }

    async function executeBulkAction(action, extraData = {}) {
        const ids = getSelectedIds();
        if (!ids.length) {
            await appAlert({
                title: 'No Products Selected',
                text: 'Please select at least one product from the table to perform this bulk action.',
                type: 'warning',
                confirmText: 'Got It'
            });
            return;
        }

        if (action === 'delete') {
            const confirmed = await appConfirm({
                title: `Delete ${ids.length} Selected Products?`,
                text: 'These products will be removed from your catalog and POS terminal inventory.',
                type: 'danger',
                confirmText: 'Delete Products',
                cancelText: 'Cancel'
            });
            if (!confirmed) return;
        } else if (action === 'activate') {
            const confirmed = await appConfirm({
                title: `Activate ${ids.length} Products?`,
                text: 'All selected items will become active and available immediately in the POS Terminal.',
                type: 'success',
                confirmText: 'Set Active',
                cancelText: 'Cancel'
            });
            if (!confirmed) return;
        } else if (action === 'deactivate') {
            const confirmed = await appConfirm({
                title: `Deactivate ${ids.length} Products?`,
                text: 'All selected items will be hidden from cashiers in the POS Terminal.',
                type: 'warning',
                confirmText: 'Set Inactive',
                cancelText: 'Cancel'
            });
            if (!confirmed) return;
        }

        $.ajax({
            url: "{{ route('products.bulk-action') }}",
            type: 'POST',
            data: {
                _token: document.querySelector('meta[name="csrf-token"]').content,
                action: action,
                ids: ids,
                ...extraData
            },
            async success(response) {
                if (response.success) {
                    await appAlert({
                        title: 'Success!',
                        text: response.message,
                        type: 'success',
                        confirmText: 'Done'
                    });
                    
                    // Close modals if open
                    bootstrap.Modal.getInstance(document.getElementById('bulkCategoryModal'))?.hide();
                    bootstrap.Modal.getInstance(document.getElementById('bulkPriceModal'))?.hide();

                    reloadTable();
                    loadKpiStats();
                } else {
                    await appAlert({
                        title: 'Operation Failed',
                        text: response.message || 'Bulk operation failed.',
                        type: 'danger',
                        confirmText: 'Close'
                    });
                }
            },
            async error(xhr) {
                await appAlert({
                    title: 'Server Error',
                    text: xhr.responseJSON?.message || 'A server error occurred during bulk operation.',
                    type: 'danger',
                    confirmText: 'Close'
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Load KPIs asynchronously in background
        loadKpiStats();

        // Filter change triggers table reload
        let debounceTimer;
        $('.datatable-external-filter').on('change input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                reloadTable();
            }, 250);
        });

        // Quick Tabs
        $('.quick-tab').on('click', function () {
            $('.quick-tab').removeClass('active');
            $(this).addClass('active');

            const tab = $(this).data('tab');
            const type = $(this).data('type');

            if (tab !== undefined) {
                $('#filterStockStatusSelect').val(tab);
                $('#filterProductType').val('');
            } else if (type !== undefined) {
                $('#filterProductType').val(type);
                $('#filterStockStatusSelect').val('');
            }
            reloadTable();
        });

        // Reset Filters
        $('#btnResetFilters').on('click', function () {
            $('.datatable-external-filter').val('');
            $('.quick-tab').removeClass('active');
            $('.quick-tab[data-tab=""]').addClass('active');
            reloadTable();
        });

        // Table Draw listener to sync checkboxes on page / filter changes
        $('#productssTables').on('draw.dt', function () {
            updateBulkBar();
        });

        // Checkbox events
        $('#selectAllProducts').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('.product-checkbox').prop('checked', isChecked);
            updateBulkBar();
        });

        $(document).on('change', '.product-checkbox', function () {
            updateBulkBar();
        });

        $('#btnDeselectAll').on('click', function () {
            $('.product-checkbox, #selectAllProducts').prop('checked', false);
            updateBulkBar();
        });

        // Bulk Actions Triggers
        $('#btnBulkActivate').on('click', () => executeBulkAction('activate'));
        $('#btnBulkDeactivate').on('click', () => executeBulkAction('deactivate'));
        $('#btnBulkDelete').on('click', () => executeBulkAction('delete'));

        $('#btnConfirmBulkCategory').on('click', function () {
            const catId = $('#bulkTargetCategory').val();
            executeBulkAction('update_category', { category_id: catId });
        });

        $('#btnConfirmBulkPrice').on('click', function () {
            const percent = $('#bulkMarkupPercent').val();
            const fixed = $('#bulkMarkupFixed').val();
            executeBulkAction('price_markup', { markup_percent: percent, markup_fixed: fixed });
        });

        // 360° Quick CRM View Drawer trigger
        $(document).on('click', '.btn-quick-view', function (e) {
            e.preventDefault();
            const id = $(this).data('id');
            const drawerEl = document.getElementById('productQuickViewDrawer');
            const drawer = bootstrap.Offcanvas.getOrCreateInstance(drawerEl);
            const content = document.getElementById('drawerContent');

            content.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted small mt-2">Loading product CRM analytics...</p>
                </div>
            `;
            drawer.show();

            $.getJSON(`/products/quick-view/${id}`, function (data) {
                let variantsHtml = '';
                if (data.variants && data.variants.length > 0) {
                    variantsHtml = `
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-boxes text-primary fs-5"></i>
                                <h6 class="fw-black text-dark mb-0 font-mono">Packaging & Variant Hierarchy (${data.variants.length})</h6>
                            </div>
                            <div class="table-responsive rounded-3 border overflow-hidden">
                                <table class="table table-hover align-middle mb-0 extra-small">
                                    <thead class="bg-light text-muted text-uppercase">
                                        <tr>
                                            <th class="py-2.5 px-3">Variant Name</th>
                                            <th class="py-2.5 px-3 text-center">Qty / Pack</th>
                                            <th class="py-2.5 px-3 text-end">Retail Price</th>
                                            <th class="py-2.5 px-3 text-end">Wholesale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.variants.map(v => `
                                            <tr>
                                                <td class="py-2.5 px-3 fw-bold text-dark">${v.name}</td>
                                                <td class="py-2.5 px-3 text-center font-mono fw-bold">${v.qty_per_pack}</td>
                                                <td class="py-2.5 px-3 text-end font-mono fw-black text-success">₱${v.selling_price.toFixed(2)}</td>
                                                <td class="py-2.5 px-3 text-end font-mono text-muted">${v.wholesale_price ? '₱' + v.wholesale_price.toFixed(2) : '-'}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                }

                let recentStocksHtml = '';
                if (data.recent_stocks && data.recent_stocks.length > 0) {
                    recentStocksHtml = `
                        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-clock-history text-secondary fs-5"></i>
                                <h6 class="fw-black text-dark mb-0 font-mono">Recent Stock Movements</h6>
                            </div>
                            <div class="d-flex flex-column gap-2.5">
                                ${data.recent_stocks.map(s => `
                                    <div class="p-3.5 px-4 rounded-4 bg-light border d-flex align-items-center justify-content-between">
                                        <div>
                                            <div class="fw-bold text-dark fs-6">${s.note}</div>
                                            <div class="text-muted small font-mono mt-0.5"><i class="bi bi-calendar3 me-1"></i>${s.date}</div>
                                        </div>
                                        <span class="badge ${s.qty >= 0 ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'} font-mono fw-black fs-6 px-3 py-2 rounded-pill">
                                            ${s.qty >= 0 ? '+' : ''}${s.qty}
                                        </span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                content.innerHTML = `
                    {{-- Hero Product Banner (Generous Padding) --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                        <div class="d-flex align-items-center gap-3.5">
                            <img src="${data.image_url}" class="rounded-4 border shadow-sm object-fit-cover flex-shrink-0" style="width:84px;height:84px;" alt="" onerror="this.src='/images/no_image.jpg';">
                            <div class="min-w-0 flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1.5">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small fw-bold px-2.5 py-1 rounded-pill">${data.category_name}</span>
                                    <span class="badge ${data.status === 'active' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-secondary-subtle text-secondary'} border extra-small fw-bold text-uppercase px-2.5 py-1 rounded-pill">${data.status}</span>
                                    ${data.allow_decimal_qty ? '<span class="badge bg-info-subtle text-info border border-info-subtle extra-small fw-bold px-2.5 py-1 rounded-pill">⚖️ Loose/Weighed</span>' : ''}
                                </div>
                                <h4 class="fw-black text-dark mb-1 lh-sm text-truncate" title="${data.name}">${data.name}</h4>
                                <div class="font-mono text-muted small mt-1">
                                    ${data.barcode ? `<i class="bi bi-upc me-1 text-secondary"></i>${data.barcode}` : ''}
                                    ${data.sku ? `<span class="ms-2 text-secondary">| SKU: ${data.sku}</span>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Barcode SVG --}}
                    ${data.barcode_svg ? `
                        <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4 bg-white">
                            <div class="extra-small fw-bold text-muted text-uppercase mb-2" style="letter-spacing:0.5px;">Scannable Barcode Symbol</div>
                            <div class="d-flex justify-content-center py-2">${data.barcode_svg}</div>
                            <div class="font-mono text-dark fw-bold fs-6 mt-1">${data.barcode}</div>
                        </div>
                    ` : ''}

                    {{-- Financial & Margin KPI Cards (Generous Padding) --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                                <span class="extra-small text-muted fw-bold text-uppercase" style="letter-spacing:0.5px;">Retail Selling Price</span>
                                <div class="font-mono fw-black text-success fs-3 my-1">₱${data.selling_price.toFixed(2)}</div>
                                <div class="small text-muted font-mono">Cost Price: <strong class="text-dark">₱${data.cost_price.toFixed(2)}</strong></div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                                <span class="extra-small text-muted fw-bold text-uppercase" style="letter-spacing:0.5px;">Profit Margin</span>
                                <div class="font-mono fw-black text-primary fs-3 my-1">+₱${data.profit.toFixed(2)}</div>
                                <div class="small text-muted font-mono"><strong class="text-dark">${data.margin}%</strong> Gross Margin</div>
                            </div>
                        </div>
                    </div>

                    {{-- Stock Status Box (Generous Padding) --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 ${data.stock_on_hand <= 0 ? 'bg-danger-subtle border border-danger-subtle' : data.stock_on_hand <= data.reorder_level ? 'bg-warning-subtle border border-warning-subtle' : 'bg-success-subtle border border-success-subtle'}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="extra-small fw-bold text-uppercase text-muted" style="letter-spacing:0.5px;">Current Stock on Hand</span>
                                <div class="font-mono fw-black text-dark fs-2 mt-1">${data.stock_on_hand} <small class="text-muted fs-5">${data.unit_name}</small></div>
                            </div>
                            <div class="text-end">
                                <span class="badge ${data.stock_on_hand <= 0 ? 'bg-danger' : data.stock_on_hand <= data.reorder_level ? 'bg-warning text-dark' : 'bg-success'} fw-bold font-mono px-3 py-2 rounded-pill fs-6">
                                    ${data.stock_on_hand <= 0 ? 'Out of Stock' : data.stock_on_hand <= data.reorder_level ? 'Low Stock Alert' : 'Healthy Stock'}
                                </span>
                                <div class="small text-muted mt-1.5 font-mono">Reorder threshold: ${data.reorder_level} ${data.unit_name}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Lifetime Sales Performance (Generous Padding) --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-graph-up-arrow text-success fs-5"></i>
                            <h6 class="fw-black text-dark mb-0 font-mono">Lifetime Sales Performance</h6>
                        </div>
                        <div class="row g-3 pt-1">
                            <div class="col-6">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted extra-small fw-bold text-uppercase">Total Units Sold</span>
                                    <div class="font-mono fw-black text-dark fs-5 mt-1">${data.total_units_sold} ${data.unit_name}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 bg-light border">
                                    <span class="text-muted extra-small fw-bold text-uppercase">Total Sales Revenue</span>
                                    <div class="font-mono fw-black text-success fs-5 mt-1">₱${data.total_revenue.toFixed(2)}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${variantsHtml}

                    ${recentStocksHtml}

                    {{-- Actions Footer --}}
                    <div class="pt-4 pb-2 border-top d-flex gap-3 sticky-bottom bg-white">
                        <a href="/products/edit/${data.encrypted_id}" class="btn btn-primary w-100 py-3 rounded-4 fw-bold extra-small shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift">
                            <i class="bi bi-pencil-square fs-6"></i> Full Edit Product
                        </a>
                        <a href="/products/stock/receive/${data.encrypted_id}" class="btn btn-success w-100 py-3 rounded-4 fw-bold extra-small shadow-sm d-flex align-items-center justify-content-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                            <i class="bi bi-box-arrow-in-down fs-6"></i> Stock In / Restock
                        </a>
                    </div>
                `;
            }).fail(function () {
                content.innerHTML = `
                    <div class="text-center py-5 text-danger">
                        <i class="bi bi-exclamation-octagon-fill fs-1"></i>
                        <p class="mt-2 fw-bold">Failed to load product CRM details.</p>
                    </div>
                `;
            });
        });
    });
</script>
@endpush
@endsection
