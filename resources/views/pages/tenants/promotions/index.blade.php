@extends('layouts.app')

@section('title', 'Promotions & Deals Management')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill extra-small font-mono fw-bold">
                    <i class="bi bi-tag-fill me-1"></i> MARKETING & DEALS
                </span>
                <span class="text-muted extra-small font-mono">/ Minimart Discounts</span>
            </div>
            <h3 class="h4 fw-black text-dark font-mono mt-1 mb-0">Promotions, Wholesale & Bulk Discounts</h3>
            <p class="text-muted small mb-0">Schedule automated holiday sales, wholesale box discounts, buy-x-get-y freebies, or promo codes</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" id="btnOpenCreatePromo" class="btn btn-sm btn-primary fw-bold rounded-pill px-3.5 py-2 shadow-xs d-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalAddPromo">
                <i class="bi bi-plus-circle-fill"></i>
                <span>Create New Promotion</span>
            </button>
        </div>
    </div>

    {{-- 3 KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-xs rounded-4 bg-white p-3.5 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold font-mono text-uppercase">Active Promotions</span>
                    <div class="w-8 h-8 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center">
                        <i class="bi bi-check-circle-fill fs-6"></i>
                    </div>
                </div>
                <div class="h3 fw-black text-dark font-mono mb-1">{{ $activePromosCount }}</div>
                <div class="text-muted extra-small">Live & auto-applying in POS terminal</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-xs rounded-4 bg-white p-3.5 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold font-mono text-uppercase">Wholesale / Bulk Tiers</span>
                    <div class="w-8 h-8 rounded-circle bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center">
                        <i class="bi bi-boxes fs-6"></i>
                    </div>
                </div>
                <div class="h3 fw-black text-warning-emphasis font-mono mb-1">{{ $bulkPromosCount }}</div>
                <div class="text-muted extra-small">Box/Pack volume discounts configured</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-xs rounded-4 bg-white p-3.5 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted extra-small fw-bold font-mono text-uppercase">Total Promo Campaigns</span>
                    <div class="w-8 h-8 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                        <i class="bi bi-ticket-perforated fs-6"></i>
                    </div>
                </div>
                <div class="h3 fw-black text-primary font-mono mb-1">{{ $totalPromosCount }}</div>
                <div class="text-muted extra-small">Historical & scheduled deals</div>
            </div>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="card border-0 shadow-xs rounded-4 bg-white p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-md-4">
                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Promo Type</label>
                <select id="filterPromoType" class="form-select form-select-sm">
                    <option value="">All Promo Types</option>
                    @foreach($promoTypes as $k => $label)
                        <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Status</label>
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" selected>Active & Scheduled Only</option>
                    <option value="inactive">Inactive / Deactivated</option>
                </select>
            </div>
            <div class="col-12 col-md-4 d-flex align-items-end gap-1 mt-auto">
                <button type="button" id="btnApplyFilter" class="btn btn-sm btn-dark fw-bold rounded-3 w-100 py-1.5">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button type="button" id="btnResetFilter" class="btn btn-sm btn-light border rounded-3 py-1.5" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- DataTables Grid --}}
    <div class="card border-0 shadow-xs rounded-4 bg-white p-3">
        <div class="table-responsive">
            <table id="promotionsTable" class="table table-hover align-middle mb-0" style="width:100%;">
                <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                    <tr>
                        <th style="width:70px;">Actions</th>
                        <th>Promotion Title / Code</th>
                        <th>Type</th>
                        <th>Discount Rule</th>
                        <th>Applies To</th>
                        <th>Schedule / Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

</div>

{{-- Add / Edit Promo Modal --}}
<div class="modal fade" id="modalAddPromo" tabindex="-1" aria-labelledby="modalAddPromoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formAddPromo" action="javascript:void(0);" onsubmit="return false;">
                @csrf
                <input type="hidden" name="promo_id" id="inpPromoId" value="">

                <div class="modal-header bg-light border-0 px-4 py-3">
                    <div>
                        <h5 class="modal-title fw-black text-dark font-mono mb-0" id="modalAddPromoLabel">
                            <i class="bi bi-tag-fill text-primary me-2"></i><span id="txtModalHeaderTitle">Create New Promotion</span>
                        </h5>
                        <div class="text-muted extra-small">Setup automatic discount rules, promo codes, or wholesale prices</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Title --}}
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-bold small text-dark">Promotion Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="inpTitle" class="form-control rounded-3" placeholder="e.g. Christmas Ham Sale 10% / Noche Buena Bundle" required>
                        </div>

                        {{-- Promo Code (Optional) --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-bold small text-dark">Promo Code <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="text" name="promo_code" id="inpPromoCode" class="form-control font-mono text-uppercase rounded-3" placeholder="e.g. XMAS2026 (Leave empty for Auto)">
                            <div class="extra-small text-muted mt-0.5">Empty = Auto-applies at POS checkout</div>
                        </div>

                        {{-- Promo Type --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Promotion Type <span class="text-danger">*</span></label>
                            <select name="promo_type" id="selectPromoType" class="form-select rounded-3" required>
                                <option value="percentage">Percentage Discount (%)</option>
                                <option value="fixed_amount">Fixed Amount Off (₱)</option>
                            </select>
                        </div>

                        {{-- Discount Value --}}
                        <div class="col-12 col-md-6" id="boxDiscountValue">
                            <label class="form-label fw-bold small text-dark" id="lblDiscountValue">Discount Value <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light fw-bold font-mono" id="addonDiscountUnit">%</span>
                                <input type="number" step="0.01" name="discount_value" id="inpDiscountValue" class="form-control font-mono fw-black text-primary rounded-end-3" placeholder="10.00" required>
                            </div>
                        </div>

                        {{-- Min Quantity (For Bulk Tier & Buy X Get Y) --}}
                        <div class="col-12 col-md-6" id="boxMinQty" style="display:none;">
                            <label class="form-label fw-bold small text-dark">Min Quantity for Discount <span class="text-danger">*</span></label>
                            <input type="number" name="min_quantity" id="inpMinQty" class="form-control font-mono rounded-3" value="10" placeholder="e.g. 10">
                        </div>

                        {{-- Free Quantity (For Buy X Get Y) --}}
                        <div class="col-12 col-md-6" id="boxGetQty" style="display:none;">
                            <label class="form-label fw-bold small text-dark">Free Quantity (Get Y) <span class="text-danger">*</span></label>
                            <input type="number" name="get_quantity" id="inpGetQty" class="form-control font-mono rounded-3" value="1" placeholder="e.g. 1">
                        </div>

                        {{-- Min Spend --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Minimum Cart Spend (₱) <span class="text-muted fw-normal">(0 = None)</span></label>
                            <input type="number" step="0.01" name="min_spend" id="inpMinSpend" class="form-control font-mono rounded-3" value="0.00">
                        </div>

                        {{-- Applies To --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Applies To <span class="text-danger">*</span></label>
                            <select name="applies_to" id="selectAppliesTo" class="form-select rounded-3" required>
                                <option value="all">Entire Cart / All Products (Storewide)</option>
                                <option value="product">Specific Products (Search / Barcode Scanner)</option>
                                <option value="category">Specific Categories</option>
                            </select>
                        </div>

                        {{-- Guidance Notice for Entire Cart --}}
                        <div class="col-12" id="boxEntireCartGuide">
                            <div class="p-2.5 bg-success-subtle border border-success-subtle rounded-3 text-success-emphasis extra-small d-flex align-items-center gap-1.5">
                                <i class="bi bi-check-circle-fill fs-6 text-success"></i>
                                <span><strong>Storewide Sale:</strong> All products in your store inventory will automatically receive this discount.</span>
                            </div>
                        </div>

                        {{-- Guidance Notice for Specific Products --}}
                        <div class="col-12" id="boxProductGuide" style="display:none;">
                            <div class="p-2.5 bg-primary-subtle border border-primary-subtle rounded-3 text-primary-emphasis extra-small d-flex align-items-center gap-1.5">
                                <i class="bi bi-boxes fs-6 text-primary"></i>
                                <span><strong>Dedicated Items Page:</strong> After saving, you will be redirected to search, batch-select, or scan barcodes for items.</span>
                            </div>
                        </div>

                        {{-- Guidance Notice for Specific Categories --}}
                        <div class="col-12" id="boxCategoryGuide" style="display:none;">
                            <div class="p-2.5 bg-info-subtle border border-info-subtle rounded-3 text-info-emphasis extra-small d-flex align-items-center gap-1.5">
                                <i class="bi bi-tags-fill fs-6 text-info"></i>
                                <span><strong>Category Sale:</strong> After saving, you will select which categories (e.g. Beverages, Snacks) will receive this discount.</span>
                            </div>
                        </div>

                        {{-- Start Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Schedule Start Date <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="date" name="start_date" id="inpStartDate" class="form-control rounded-3" value="{{ date('Y-m-d') }}">
                            <div class="extra-small text-muted mt-0.5">Promo only activates on or after this date</div>
                        </div>

                        {{-- End Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold small text-dark">Schedule End Date <span class="text-muted fw-normal">(Optional)</span></label>
                            <input type="date" name="end_date" id="inpEndDate" class="form-control rounded-3">
                            <div class="extra-small text-muted mt-0.5">Promo automatically stops after this date</div>
                        </div>

                        {{-- Description / Remarks --}}
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">Description / Internal Notes</label>
                            <textarea name="description" id="inpDescription" class="form-control rounded-3" rows="2" placeholder="e.g. Approved Christmas fiesta discount for all soda & snacks"></textarea>
                        </div>

                        {{-- ════ LIVE PROFIT PREDICTOR — Light Mode ════ --}}
                        <div class="col-12" id="boxProfitPreview">
                            <div class="rounded-4 overflow-hidden" style="border:1.5px solid #e2e8f0;background:#f8fafc;">

                                {{-- Header --}}
                                <div class="d-flex align-items-center justify-content-between px-4 py-2" style="background:#f1f5f9;border-bottom:1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-graph-up-arrow" style="color:#6366f1;font-size:.9rem;"></i>
                                        <span class="fw-bold text-dark" style="font-size:.82rem;">Live Profit Predictor</span>
                                        <span class="badge rounded-pill px-2" id="previewLiveBadge"
                                              style="font-size:.6rem;background:#dcfce7;color:#16a34a;display:none;">● LIVE</span>
                                    </div>
                                    <span class="extra-small text-muted">Includes products &amp; variants · retail &amp; wholesale</span>
                                </div>

                                {{-- Idle --}}
                                <div id="previewIdle" class="px-4 py-4 text-center text-muted" style="font-size:.82rem;">
                                    <i class="bi bi-pencil-square me-1 text-primary"></i>
                                    Enter a discount value above to instantly preview profit impact
                                </div>

                                {{-- Loading --}}
                                <div id="previewLoading" class="px-4 py-4 text-center text-muted" style="display:none;font-size:.82rem;">
                                    <div class="spinner-border spinner-border-sm me-2 text-primary"></div>Analyzing products &amp; variants...
                                </div>

                                {{-- Results --}}
                                <div id="previewResults" style="display:none;">

                                    {{-- Retail / Wholesale tab strip --}}
                                    <div class="d-flex" style="border-bottom:1.5px solid #e2e8f0;">
                                        <button type="button" id="tabRetail"
                                            class="flex-fill py-2 border-0 fw-semibold small"
                                            style="background:#fff;color:#6366f1;border-bottom:2px solid #6366f1!important;transition:all .15s;">
                                            <i class="bi bi-shop me-1"></i>Retail Price
                                        </button>
                                        <button type="button" id="tabWholesale"
                                            class="flex-fill py-2 border-0 small text-muted"
                                            style="background:#f8fafc;transition:all .15s;">
                                            <i class="bi bi-boxes me-1"></i>Wholesale Price
                                            <span id="wsNoDataBadge" class="badge bg-secondary ms-1" style="font-size:.6rem;display:none;">N/A</span>
                                        </button>
                                    </div>

                                    {{-- KPI strip --}}
                                    <div class="d-flex" style="border-bottom:1px solid #f1f5f9;">
                                        <div class="flex-fill text-center py-3 px-2" style="border-right:1px solid #f1f5f9;">
                                            <div id="kpiVerdict" class="fw-black" style="font-size:.95rem;">—</div>
                                            <div class="extra-small text-muted mt-1">Verdict</div>
                                        </div>
                                        <div class="flex-fill text-center py-3 px-2" style="border-right:1px solid #f1f5f9;">
                                            <div id="kpiMargin" class="fw-black font-mono" style="font-size:.95rem;color:#475569;">—</div>
                                            <div class="extra-small text-muted mt-1">Avg Margin</div>
                                        </div>
                                        <div class="flex-fill text-center py-3 px-2" style="border-right:1px solid #f1f5f9;">
                                            <div id="kpiProfit" class="fw-black font-mono" style="font-size:.95rem;color:#475569;">—</div>
                                            <div class="extra-small text-muted mt-1">Avg Profit/Unit</div>
                                        </div>
                                        <div class="flex-fill text-center py-3 px-2">
                                            <div id="kpiScore" class="fw-black font-mono" style="font-size:.95rem;color:#475569;">—</div>
                                            <div class="extra-small text-muted mt-1">Profitable</div>
                                        </div>
                                    </div>

                                    {{-- Margin bar --}}
                                    <div class="px-4 pt-3 pb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted" style="font-size:.72rem;">Profit Margin</small>
                                            <small id="barMarginLabel" class="fw-bold font-mono" style="font-size:.72rem;color:#64748b;">0%</small>
                                        </div>
                                        <div class="rounded-pill" style="height:8px;background:#e2e8f0;">
                                            <div id="barMargin" class="rounded-pill" style="height:8px;width:0%;transition:width .45s ease;"></div>
                                        </div>
                                    </div>

                                    {{-- Alert / warning --}}
                                    <div id="previewMessage" class="px-4 pb-2" style="display:none;"></div>

                                    {{-- Sample table --}}
                                    <div class="px-4 pb-3 pt-1" id="previewSamplesWrap">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="extra-small fw-semibold text-muted text-uppercase" style="letter-spacing:.05em;">Products &amp; Variants Preview</span>
                                            <span id="previewSampleCount" class="extra-small text-muted"></span>
                                        </div>
                                        <div style="max-height:175px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;">
                                            <table class="table table-sm table-hover mb-0 align-middle" style="font-size:.76rem;">
                                                <thead style="position:sticky;top:0;background:#f8fafc;z-index:1;">
                                                    <tr class="text-muted">
                                                        <th class="ps-3 border-0 fw-semibold" style="min-width:140px;">Product / Variant</th>
                                                        <th class="text-end border-0 fw-semibold">Cost</th>
                                                        <th class="text-end border-0 fw-semibold" id="thSalePrice">Sale Price</th>
                                                        <th class="text-end border-0 fw-semibold">Profit</th>
                                                        <th class="text-center border-0 fw-semibold">Margin</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="previewSamplesBody"></tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>{{-- /previewResults --}}

                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-light border-0 px-4 py-3">
                    <button type="button" class="btn btn-sm btn-light border fw-bold rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSavePromo" class="btn btn-sm btn-primary fw-bold rounded-pill px-4 py-1.5 shadow-xs d-flex align-items-center gap-1.5">
                        <i class="bi bi-check-circle"></i>
                        <span id="txtBtnSaveLabel">Save Promotion</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    function initPromotions($) {
        const table = $('#promotionsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('promotions.data') }}",
                data: function (d) {
                    d.promo_type = $('#filterPromoType').val();
                    d.status = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
                { data: 'promo_badge', name: 'title' },
                { data: 'type_badge', name: 'promo_type' },
                { data: 'discount_rule', name: 'discount_value' },
                { data: 'applies_to_badge', name: 'applies_to' },
                { data: 'duration', name: 'start_date' },
                { data: 'status_toggle', name: 'is_active', orderable: false, searchable: false }
            ],
            order: [[1, 'asc']],
            pageLength: 25,
            language: {
                emptyTable: "No promotions configured yet. Click 'Create New Promotion' to get started."
            }
        });

        $('#btnApplyFilter').on('click', function () {
            table.ajax.reload();
        });

        $('#btnResetFilter').on('click', function () {
            $('#filterPromoType').val('');
            $('#filterStatus').val('active');
            table.ajax.reload();
        });

        // Dynamic Form Fields toggle based on Promo Type
        $('#selectPromoType').on('change', function () {
            const type = $(this).val();
            if (type === 'percentage') {
                $('#lblDiscountValue').text('Discount Percentage (%)');
                $('#addonDiscountUnit').text('%');
                $('#boxDiscountValue').show();
                $('#boxMinQty').hide();
                $('#boxGetQty').hide();
            } else if (type === 'fixed_amount') {
                $('#lblDiscountValue').text('Discount Amount (₱)');
                $('#addonDiscountUnit').text('₱');
                $('#boxDiscountValue').show();
                $('#boxMinQty').hide();
                $('#boxGetQty').hide();
            } else if (type === 'bulk_tier') {
                $('#lblDiscountValue').text('Special Bulk Price per Pc (₱)');
                $('#addonDiscountUnit').text('₱');
                $('#boxDiscountValue').show();
                $('#boxMinQty').show();
                $('#boxGetQty').hide();
            } else if (type === 'buy_x_get_y') {
                $('#boxDiscountValue').hide();
                $('#boxMinQty').show();
                $('#boxGetQty').show();
            }
        });

        // Dynamic Form Fields toggle based on Applies To
        $('#selectAppliesTo').on('change', function () {
            const applies = $(this).val();
            const isEdit = !!$('#inpPromoId').val();

            if (applies === 'product') {
                $('#boxEntireCartGuide').hide();
                $('#boxProductGuide').slideDown();
                $('#boxCategoryGuide').hide();
                $('#txtBtnSaveLabel').text(isEdit ? 'Update & Manage Items ➔' : 'Save & Select Products ➔');
            } else if (applies === 'category') {
                $('#boxEntireCartGuide').hide();
                $('#boxProductGuide').hide();
                $('#boxCategoryGuide').slideDown();
                $('#txtBtnSaveLabel').text(isEdit ? 'Update & Manage Items ➔' : 'Save & Select Categories ➔');
            } else {
                $('#boxEntireCartGuide').slideDown();
                $('#boxProductGuide').hide();
                $('#boxCategoryGuide').hide();
                $('#txtBtnSaveLabel').text(isEdit ? 'Update Promotion' : 'Save Promotion');
            }
        });

        // ════════════════════════════════════════════════════════════════
        // LIVE PROFIT PREDICTOR
        // ════════════════════════════════════════════════════════════════
        (function initProfitPreview() {
            let debounceTimer = null;
            let currentMode   = 'retail';
            let lastData      = null;
            const PREVIEW_URL = "{{ route('promotions.profit-preview') }}";

            // ── Tab switching ──────────────────────────────────────────
            function setTab(mode) {
                currentMode = mode;
                if (mode === 'retail') {
                    $('#tabRetail').css({ background: '#fff', color: '#6366f1', 'border-bottom': '2px solid #6366f1' });
                    $('#tabWholesale').css({ background: '#f8fafc', color: '#64748b', 'border-bottom': '2px solid transparent' });
                } else {
                    $('#tabWholesale').css({ background: '#fff', color: '#d97706', 'border-bottom': '2px solid #d97706' });
                    $('#tabRetail').css({ background: '#f8fafc', color: '#64748b', 'border-bottom': '2px solid transparent' });
                }
                if (lastData) updateDisplay(lastData);
            }
            $('#tabRetail').on('click', function () { setTab('retail'); });
            $('#tabWholesale').on('click', function () { setTab('wholesale'); });

            function getInputs() {
                return {
                    promo_type:     $('#selectPromoType').val(),
                    discount_value: parseFloat($('#inpDiscountValue').val()) || 0,
                    min_quantity:   parseInt($('#inpMinQty').val())  || 1,
                    get_quantity:   parseInt($('#inpGetQty').val())  || 0,
                    applies_to:     $('#selectAppliesTo').val(),
                };
            }

            function triggerPreview() {
                clearTimeout(debounceTimer);
                const inp = getInputs();
                if (inp.promo_type !== 'buy_x_get_y' && inp.discount_value <= 0) { showIdle(); return; }

                $('#previewIdle, #previewResults').hide();
                $('#previewLoading').show();
                $('#previewLiveBadge').hide();

                debounceTimer = setTimeout(function () {
                    $.getJSON(PREVIEW_URL, inp)
                        .done(function (data) {
                            $('#previewLoading').hide();
                            if (data.success) { lastData = data; updateDisplay(data); }
                            else { showMsg('warning', '<i class="bi bi-exclamation-circle me-1"></i>' + data.message); $('#previewResults').show(); }
                        })
                        .fail(function () {
                            $('#previewLoading').hide();
                            showMsg('danger', '<i class="bi bi-wifi-off me-1"></i>Could not load profit preview.');
                            $('#previewResults').show();
                        });
                }, 420);
            }

            function updateDisplay(data) {
                const isWs = currentMode === 'wholesale';
                const hasWs = (data.wholesale_item_count ?? 0) > 0;

                $('#wsNoDataBadge').toggle(!hasWs);

                if (isWs && !hasWs) {
                    renderKPI(null, null, 0, 0);
                    showMsg('info', '<i class="bi bi-info-circle me-1"></i>No products have a wholesale price set. Add wholesale prices in product settings.');
                    $('#previewSamplesWrap').hide();
                    $('#previewResults').show();
                    $('#previewLiveBadge').show();
                    return;
                }
                $('#previewSamplesWrap').show();

                const margin  = isWs ? (data.avg_ws_margin  ?? 0) : (data.avg_retail_margin  ?? 0);
                const profit  = isWs ? (data.avg_ws_profit  ?? 0) : (data.avg_retail_profit  ?? 0);
                const lossQty = isWs ? (data.wholesale_loss_count ?? 0) : (data.retail_loss_count ?? 0);
                const total   = isWs ? (data.wholesale_item_count ?? 0) : (data.total_evaluated ?? 0);

                renderKPI(margin, profit, lossQty, total);

                // Margin bar
                const m      = parseFloat(margin);
                const barW   = Math.min(Math.abs(m), 100);
                const barClr = m >= 20 ? '#22c55e' : m >= 5 ? '#f59e0b' : '#ef4444';
                $('#barMargin').css({ width: barW + '%', background: barClr });
                $('#barMarginLabel').text(m.toFixed(1) + '%').css('color', barClr);

                // Alert
                let msgHtml = '';
                if (lossQty > 0) {
                    const label = isWs ? 'wholesale' : 'retail';
                    msgHtml = `<div class="rounded-3 px-3 py-2 d-flex gap-2 align-items-start" style="background:#fef2f2;border:1px solid #fecaca;">`
                        + `<i class="bi bi-exclamation-triangle-fill" style="color:#dc2626;font-size:.85rem;"></i>`
                        + `<span class="small" style="color:#dc2626;"><strong>${lossQty} of ${total} items</strong> will sell at a LOSS at the ${label} price. Reduce the discount or fix cost prices.</span></div>`;
                } else if (m >= 0 && m < 8) {
                    msgHtml = `<div class="rounded-3 px-3 py-2 d-flex gap-2 align-items-start" style="background:#fffbeb;border:1px solid #fde68a;">`
                        + `<i class="bi bi-lightbulb-fill" style="color:#d97706;font-size:.85rem;"></i>`
                        + `<span class="small" style="color:#92400e;">Margin is very thin (&lt;8%). Consider lowering the discount slightly.</span></div>`;
                }
                msgHtml ? $('#previewMessage').html(msgHtml).show() : $('#previewMessage').hide();

                // Sample rows
                $('#thSalePrice').text(isWs ? 'WS Price' : 'Sale Price');
                const samples = data.samples || [];
                const visibleRows = isWs ? samples.filter(function(i){ return i.has_wholesale; }) : samples;
                $('#previewSampleCount').text(visibleRows.length + ' of ' + data.total_evaluated + ' items');

                let rows = '';
                visibleRows.forEach(function (item) {
                    const saleP   = isWs ? item.wholesale_sale   : item.retail_sale;
                    const profitV = isWs ? item.wholesale_profit : item.retail_profit;
                    const marg    = isWs ? item.wholesale_margin : item.retail_margin;
                    const ok      = isWs ? item.wholesale_ok     : item.retail_ok;
                    if (saleP == null) return;

                    const pClr = ok ? '#16a34a' : '#dc2626';
                    const mBg  = marg >= 20 ? '#dcfce7' : marg >= 5 ? '#fef9c3' : '#fee2e2';
                    const mClr = marg >= 20 ? '#166534' : marg >= 5 ? '#92400e' : '#991b1b';
                    const icon = ok ? '▲' : '▼';
                    const isVar = item.name.includes(' — ');

                    rows += `<tr>
                        <td class="ps-3 text-dark" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="${item.name}">
                            ${isVar ? '<span class="text-muted" style="font-size:.7rem;">↳ </span>' : ''}${item.name}
                        </td>
                        <td class="text-end font-mono text-muted">₱${parseFloat(item.cost).toFixed(2)}</td>
                        <td class="text-end font-mono text-dark">₱${parseFloat(saleP).toFixed(2)}</td>
                        <td class="text-end font-mono fw-bold" style="color:${pClr};">${icon} ₱${Math.abs(profitV).toFixed(2)}</td>
                        <td class="text-center"><span class="badge rounded-2 px-2" style="font-size:.65rem;background:${mBg};color:${mClr};">${parseFloat(marg).toFixed(1)}%</span></td>
                    </tr>`;
                });
                $('#previewSamplesBody').html(rows || `<tr><td colspan="5" class="text-center text-muted py-3">${isWs ? 'No wholesale-priced items' : 'No data'}</td></tr>`);

                $('#previewResults').show();
                $('#previewLiveBadge').show();
            }

            function renderKPI(margin, profit, lossQty, total) {
                const m = parseFloat(margin ?? 0);
                const p = parseFloat(profit ?? 0);

                let vHtml, vClr;
                if (margin === null)              { vHtml = '—';                                                    vClr = '#94a3b8'; }
                else if (lossQty === total && total > 0) { vHtml = '<i class="bi bi-x-circle-fill me-1"></i>LOSS';  vClr = '#dc2626'; }
                else if (lossQty > total / 2)    { vHtml = '<i class="bi bi-exclamation-triangle-fill me-1"></i>RISKY'; vClr = '#d97706'; }
                else if (m < 8)                  { vHtml = '<i class="bi bi-dash-circle me-1"></i>THIN';            vClr = '#d97706'; }
                else if (m >= 20)                { vHtml = '<i class="bi bi-check-circle-fill me-1"></i>GOOD';      vClr = '#16a34a'; }
                else                             { vHtml = '<i class="bi bi-check-circle me-1"></i>OK';             vClr = '#2563eb'; }

                $('#kpiVerdict').html(vHtml).css('color', vClr);
                $('#kpiMargin').text(margin !== null ? m.toFixed(1) + '%' : 'N/A').css('color', m >= 0 ? '#16a34a' : '#dc2626');
                $('#kpiProfit').text(profit !== null ? '₱' + p.toFixed(2) : 'N/A').css('color', p >= 0 ? '#16a34a' : '#dc2626');
                $('#kpiScore').text(total > 0 ? (total - lossQty) + '/' + total : '—')
                              .css('color', lossQty > 0 ? '#d97706' : '#16a34a');
            }

            function showIdle() {
                $('#previewLoading, #previewResults').hide();
                $('#previewMessage').hide();
                $('#previewLiveBadge').hide();
                $('#previewIdle').show();
                lastData = null;
            }

            function showMsg(type, html) {
                const s = type === 'danger'  ? 'background:#fef2f2;border:1px solid #fecaca;color:#dc2626'
                        : type === 'warning' ? 'background:#fffbeb;border:1px solid #fde68a;color:#92400e'
                                             : 'background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8';
                $('#previewMessage').html(`<div class="rounded-3 px-3 py-2 small" style="${s};">${html}</div>`).show();
            }

            // ── Watch inputs ───────────────────────────────────────────
            $('#inpDiscountValue, #inpMinQty, #inpGetQty').on('input', triggerPreview);
            $('#selectPromoType, #selectAppliesTo').on('change', triggerPreview);

            $('#modalAddPromo').on('show.bs.modal', function () {
                showIdle();
                setTab('retail');
                $('#previewSamplesWrap').show();
            });
        })();

        // Date Range Validation (End date must not be before start date)
        $('#inpStartDate').on('change input', function () {
            const startVal = $(this).val();
            if (startVal) {
                $('#inpEndDate').attr('min', startVal);
                if ($('#inpEndDate').val() && $('#inpEndDate').val() < startVal) {
                    $('#inpEndDate').val(startVal);
                }
            }
        });

        $('#inpEndDate').on('change input', function () {
            const startVal = $('#inpStartDate').val();
            const endVal = $(this).val();
            if (startVal && endVal && endVal < startVal) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date Range',
                        text: 'Schedule End Date cannot be earlier than Schedule Start Date.',
                    });
                } else {
                    alert('Schedule End Date cannot be earlier than Schedule Start Date.');
                }
                $(this).val(startVal);
            }
        });

        // Reset Modal on Create New
        $('#btnOpenCreatePromo').on('click', function () {
            $('#inpPromoId').val('');
            $('#formAddPromo')[0].reset();
            $('#txtModalHeaderTitle').text('Create New Promotion');
            $('#txtBtnSaveLabel').text('Save Promotion');
            $('#selectPromoType').val('percentage').trigger('change');
            $('#selectAppliesTo').val('all').trigger('change');
            const today = '{{ date('Y-m-d') }}';
            $('#inpStartDate').val(today);
            $('#inpEndDate').attr('min', today);
        });

        // EDIT PROMO: Load & Populate Data
        $(document).on('click', '.btn-edit-promo', function () {
            const id = $(this).data('id');
            if (!id) return;

            $.ajax({
                url: `/promotions/show/${id}`,
                type: 'GET',
                headers: { 'Accept': 'application/json' },
                success: function (res) {
                    if (res) {
                        $('#inpPromoId').val(res.id);
                        $('#txtModalHeaderTitle').text('Edit Promotion: ' + res.title);
                        $('#txtBtnSaveLabel').text('Update Promotion');

                        $('#inpTitle').val(res.title);
                        $('#inpPromoCode').val(res.promo_code || '');
                        $('#selectPromoType').val(res.promo_type).trigger('change');
                        $('#inpDiscountValue').val(res.discount_value);
                        $('#inpMinQty').val(res.min_quantity || 10);
                        $('#inpGetQty').val(res.get_quantity || 1);
                        $('#inpMinSpend').val(res.min_spend || 0);
                        $('#selectAppliesTo').val(res.applies_to).trigger('change');

                        $('#inpStartDate').val(res.start_date ? res.start_date.substring(0, 10) : '');
                        $('#inpEndDate').val(res.end_date ? res.end_date.substring(0, 10) : '');
                        $('#inpDescription').val(res.description || '');

                        const modalEl = document.getElementById('modalAddPromo');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.show();
                    }
                },
                error: function () {
                    alert('Failed to load promotion details.');
                }
            });
        });

        // Toggle Active status switch
        $(document).on('change', '.btn-toggle-status', function () {
            const id = $(this).data('id');
            const checkbox = $(this);

            $.ajax({
                url: `/promotions/toggle-active/${id}`,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                    alert('Failed to update promotion status.');
                }
            });
        });

        // AJAX Form Submit (Store or Update) with Button Spinner Loader
        $('#formAddPromo').on('submit', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const startVal = $('#inpStartDate').val();
            const endVal = $('#inpEndDate').val();
            if (startVal && endVal && endVal < startVal) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Schedule Dates',
                        text: 'Schedule End Date cannot be earlier than Schedule Start Date.',
                    });
                } else {
                    alert('Schedule End Date cannot be earlier than Schedule Start Date.');
                }
                return false;
            }

            const promoId = $('#inpPromoId').val();
            const isEdit = !!promoId;
            const btn = $('#btnSavePromo');
            const origHtml = btn.html();

            btn.prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span>${isEdit ? 'Updating Promotion...' : 'Saving Promotion...'}</span>
            `);

            const formData = new FormData(this);
            const url = isEdit ? `/promotions/update/${promoId}` : "{{ route('promotions.store') }}";
            if (isEdit) {
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (res && res.success) {
                        // If specific products / categories, redirect to manage items page
                        if (res.redirect_url) {
                            window.location.href = res.redirect_url;
                            return;
                        }

                        const modalEl = document.getElementById('modalAddPromo');
                        if (modalEl) {
                            const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modalInstance.hide();
                        }

                        $('#formAddPromo')[0].reset();
                        $('#inpPromoId').val('');
                        $('#selectPromoType').trigger('change');
                        $('#selectAppliesTo').trigger('change');

                        table.ajax.reload(null, false);

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: isEdit ? 'Promotion Updated!' : 'Promotion Saved!',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    } else {
                        alert(res.message || 'Validation error.');
                    }
                },
                error: function (xhr) {
                    console.error('Promo save error:', xhr);
                    let msg = 'Failed to save promotion. Please check required fields.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: msg
                        });
                    } else {
                        alert(msg);
                    }
                },
                complete: function () {
                    btn.prop('disabled', false).html(origHtml);
                }
            });

            return false;
        });

        // AJAX Delete Promo
        $(document).on('click', '.btn-delete-promo', function () {
            const id = $(this).data('id');
            const title = $(this).data('title');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Promotion?',
                    text: `Are you sure you want to remove "${title}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, Delete'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDelete(id);
                    }
                });
            } else {
                if (confirm(`Delete promotion "${title}"?`)) {
                    performDelete(id);
                }
            }
        });

        function performDelete(id) {
            $.ajax({
                url: `/promotions/delete/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function (res) {
                    if (res && res.success) {
                        table.ajax.reload(null, false);
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: res.message,
                                timer: 1800,
                                showConfirmButton: false
                            });
                        }
                    }
                }
            });
        }
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initPromotions(window.$);
        } else {
            setTimeout(checkJQuery, 30);
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

@endsection
