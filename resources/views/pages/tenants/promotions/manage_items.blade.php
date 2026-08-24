@extends('layouts.app')

@section('title', 'Manage Promotion Items - ' . $promo->title)

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Top Navigation & Header Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white p-3.5 mb-3">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <a href="{{ route('promotions.index') }}" class="btn btn-xs btn-light border text-muted fw-bold rounded-pill px-2.5 py-1 text-decoration-none extra-small font-mono">
                        <i class="bi bi-arrow-left me-1"></i> Back to Promotions
                    </a>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-0.5 rounded-pill extra-small font-mono fw-bold">
                        <i class="bi bi-boxes me-1"></i> ITEM SELECTOR
                    </span>
                </div>
                <h3 class="h4 fw-black text-dark font-mono mb-1">{{ $promo->title }}</h3>
                
                {{-- Promo Badges --}}
                <div class="d-flex align-items-center gap-2 flex-wrap mt-1.5">
                    @if($promo->promo_type === 'percentage')
                        <span class="badge bg-primary text-white px-2.5 py-1 rounded-pill font-mono fw-bold">
                            <i class="bi bi-percent me-1"></i> {{ (float)$promo->discount_value }}% OFF
                        </span>
                    @elseif($promo->promo_type === 'fixed_amount')
                        <span class="badge bg-success text-white px-2.5 py-1 rounded-pill font-mono fw-bold">
                            <i class="bi bi-tag-fill me-1"></i> ₱{{ number_format($promo->discount_value, 2) }} OFF
                        </span>
                    @elseif($promo->promo_type === 'bulk_tier')
                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill font-mono fw-bold">
                            <i class="bi bi-boxes me-1"></i> ₱{{ number_format($promo->discount_value, 2) }} / pc ({{ $promo->min_quantity }}+ pcs)
                        </span>
                    @elseif($promo->promo_type === 'buy_x_get_y')
                        <span class="badge bg-purple text-white px-2.5 py-1 rounded-pill font-mono fw-bold" style="background:#6d28d9;">
                            <i class="bi bi-gift-fill me-1"></i> Buy {{ $promo->min_quantity }} Get {{ $promo->get_quantity }} FREE
                        </span>
                    @endif

                    @if($promo->start_date || $promo->end_date)
                        <span class="badge bg-light text-dark border px-2.5 py-1 font-mono small">
                            <i class="bi bi-calendar-event me-1 text-primary"></i>
                            {{ $promo->start_date ? $promo->start_date->format('M d, Y') : 'Anytime' }} — {{ $promo->end_date ? $promo->end_date->format('M d, Y') : 'Ongoing' }}
                        </span>
                    @else
                        <span class="badge bg-light text-success border px-2.5 py-1 font-mono small">
                            <i class="bi bi-infinity me-1"></i> Always Active
                        </span>
                    @endif

                    <span class="badge {{ $promo->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary text-white' }} px-2.5 py-1 font-mono fw-bold">
                        {{ $promo->is_active ? '● LIVE & ACTIVE' : '○ INACTIVE' }}
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnRemoveAll" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3 py-2">
                    <i class="bi bi-trash3 me-1"></i> Clear All Included Items
                </button>
            </div>
        </div>
    </div>

    {{-- Split Panels: Left (Catalog / Scanner) & Right (Active Included Items Table) --}}
    <div class="row g-3">
        
        {{-- LEFT PANEL: Add Items from Store Catalog --}}
        <div class="col-12 col-xl-5">
            <div class="card border border-slate-200 shadow-sm rounded-4 bg-white p-3.5 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-black text-dark font-mono mb-0"><i class="bi bi-plus-circle-fill text-primary me-1.5"></i>Add Items to Promo</h6>
                        <div class="text-muted extra-small">Search catalog, check variants, or scan barcodes</div>
                    </div>
                </div>

                {{-- Mode Switch Tabs: Products/Variants vs Categories --}}
                <ul class="nav nav-pills nav-fill bg-light p-1 rounded-3 mb-3 border" id="addItemTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-2 small fw-bold rounded-3" id="tab-products" data-bs-toggle="pill" data-bs-target="#panel-products" type="button" role="tab">
                            <i class="bi bi-box-seam me-1 text-primary"></i> Products & Variants
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 small fw-bold rounded-3" id="tab-categories" data-bs-toggle="pill" data-bs-target="#panel-categories" type="button" role="tab">
                            <i class="bi bi-tags me-1 text-info"></i> Entire Categories
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="addItemTabContent">
                    {{-- TAB 1: Products & Variants --}}
                    <div class="tab-pane fade show active" id="panel-products" role="tabpanel">
                        
                        {{-- Barcode Gun Quick Input --}}
                        <div class="p-2.5 bg-light rounded-3 border mb-3">
                            <label class="form-label extra-small text-uppercase fw-bold text-dark mb-1 d-flex align-items-center gap-1">
                                <i class="bi bi-upc-scan text-primary"></i> Barcode Scanner Gun (Instant Add)
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc"></i></span>
                                <input type="text" id="inpBarcodeScan" class="form-control font-mono border-start-0" placeholder="Scan barcode & press enter..." autofocus>
                                <button type="button" id="btnAddBarcode" class="btn btn-dark fw-bold px-3">
                                    <i class="bi bi-plus"></i> Add
                                </button>
                            </div>
                        </div>

                        {{-- Catalog Filters --}}
                        <div class="row g-2 mb-2.5">
                            <div class="col-12 col-md-6">
                                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Filter by Category</label>
                                <select id="filterCatalogCategory" class="form-select form-select-sm rounded-3">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label extra-small text-uppercase fw-bold text-muted mb-1">Search Products / SKU</label>
                                <input type="text" id="inpCatalogSearch" class="form-control form-select-sm rounded-3" placeholder="Type name, barcode...">
                            </div>
                        </div>

                        {{-- Select All & Count Toolbar --}}
                        <div class="d-flex align-items-center justify-content-between py-1.5 px-2 bg-light rounded-3 border mb-2">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="checkSelectAllCatalog">
                                <label class="form-check-label small fw-bold text-dark cursor-pointer" for="checkSelectAllCatalog">
                                    Select All Visible (<span id="txtCatalogVisibleCount">0</span>)
                                </label>
                            </div>
                            <span class="badge bg-primary text-white font-mono" id="txtSelectedCount">0 selected</span>
                        </div>

                        {{-- Scrollable Products List with Variants --}}
                        <div id="catalogListContainer" class="border rounded-3 p-2 bg-white overflow-y-auto mb-3" style="max-height: 340px; min-height: 220px;">
                            <div class="text-center py-5 text-muted small">
                                <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                                <div>Loading store products...</div>
                            </div>
                        </div>

                        <button type="button" id="btnAddSelectedProducts" class="btn btn-primary fw-bold w-100 rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-1.5" disabled>
                            <i class="bi bi-plus-circle-fill"></i>
                            <span id="txtBtnAddProductsLabel">Add Selected Items to Promo</span>
                        </button>
                    </div>

                    {{-- TAB 2: Categories --}}
                    <div class="tab-pane fade" id="panel-categories" role="tabpanel">
                        <div class="p-3 mb-3 bg-info-subtle border border-info-subtle rounded-3 text-info-emphasis small">
                            <i class="bi bi-info-circle-fill me-1"></i> Selecting whole categories will apply this discount to <strong>all products</strong> under them!
                        </div>

                        <label class="form-label extra-small text-uppercase fw-bold text-muted mb-2">Select Categories Included in Promo</label>
                        <div class="border rounded-3 p-2 bg-white overflow-y-auto mb-3" style="max-height: 320px;">
                            @foreach($categories as $c)
                                <div class="form-check d-flex align-items-center py-2 px-2.5 border-bottom rounded-2 mb-1 hover-bg-light">
                                    <input class="form-check-input category-checkbox me-2" type="checkbox" value="{{ $c->id }}" id="catCheck_{{ $c->id }}">
                                    <label class="form-check-label fw-bold text-dark w-100 cursor-pointer small mb-0" for="catCheck_{{ $c->id }}">
                                        <i class="bi bi-tag text-info me-1"></i> {{ $c->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <button type="button" id="btnAddSelectedCategories" class="btn btn-info text-white fw-bold w-100 rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-1.5">
                            <i class="bi bi-tags-fill"></i>
                            <span>Add Selected Categories to Promo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: Active Items Currently in Promo --}}
        <div class="col-12 col-xl-7">
            <div class="card border border-slate-200 shadow-sm rounded-4 bg-white p-3.5 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-black text-dark font-mono mb-0"><i class="bi bi-check-circle-fill text-success me-1.5"></i>Items Included in this Promo</h6>
                        <div class="text-muted extra-small">These items will automatically get discounted at POS checkout</div>
                    </div>
                    <button type="button" id="btnRefreshItems" class="btn btn-sm btn-light border fw-bold rounded-pill px-2.5 py-1 text-muted" title="Refresh Table">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="promoItemsTable" class="table table-hover align-middle mb-0" style="width:100%;">
                        <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                            <tr>
                                <th>Item / Product Name</th>
                                <th>Category</th>
                                <th>Regular SRP</th>
                                <th>Promo Price / Rule</th>
                                <th style="width:80px;" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
(function () {
    const promoId = {{ $promo->id }};

    function initManageItems($) {
        // DataTables for Active Promo Items
        const itemsTable = $('#promoItemsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('promotions.items.data', $promo->id) }}",
            columns: [
                { data: 'item_name', name: 'item_name', orderable: false },
                { data: 'category_badge', name: 'category_badge', orderable: false },
                { data: 'regular_price', name: 'regular_price', orderable: false, searchable: false },
                { data: 'sale_price', name: 'sale_price', orderable: false, searchable: false },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
            ],
            pageLength: 10,
            order: [],
            language: {
                emptyTable: "No items added to this promo yet. Use the left panel to search or scan products."
            }
        });

        $('#btnRefreshItems').on('click', function () {
            itemsTable.ajax.reload();
            loadCatalogProducts();
        });

        // Load Catalog Available Products on Left Panel
        function loadCatalogProducts() {
            const catId = $('#filterCatalogCategory').val();
            const search = $('#inpCatalogSearch').val();

            $('#catalogListContainer').html(`
                <div class="text-center py-4 text-muted small">
                    <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                    <div>Loading catalog...</div>
                </div>
            `);

            $.ajax({
                url: "{{ route('promotions.items.catalog', $promo->id) }}",
                data: { category_id: catId, search: search },
                success: function (res) {
                    if (res && res.products && res.products.length > 0) {
                        let html = '';
                        let visibleCount = 0;

                        res.products.forEach(p => {
                            visibleCount++;
                            const priceFormatted = '₱' + Number(p.selling_price || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            const barcode = p.barcode ? `<span class="extra-small text-muted font-mono ms-1">(${p.barcode})</span>` : '';
                            const sku = p.sku ? `<span class="badge bg-light text-muted border extra-small font-mono me-1">${p.sku}</span>` : '';
                            const inPromoBadge = p.is_added ? '<span class="badge bg-success-subtle text-success border border-success-subtle extra-small ms-auto">✓ In Promo</span>' : '';
                            const disabledAttr = p.is_added ? 'disabled checked' : '';

                            // Check if has variants
                            const hasVariants = p.variants && p.variants.length > 0;

                            html += `
                                <div class="catalog-product-row p-2 border-bottom rounded-2 mb-1.5 ${p.is_added ? 'bg-light opacity-75' : 'bg-white'}">
                                    <div class="form-check d-flex align-items-center mb-0">
                                        <input class="form-check-input catalog-item-check me-2" type="checkbox" data-type="product" value="${p.id}" id="prodCheck_${p.id}" ${disabledAttr}>
                                        <label class="form-check-label d-flex align-items-center gap-1 w-100 cursor-pointer small mb-0" for="prodCheck_${p.id}">
                                            ${sku}
                                            <span class="fw-bold text-dark">${p.name}</span>
                                            ${barcode}
                                            <span class="font-mono text-success fw-bold ms-2">${priceFormatted}</span>
                                            ${inPromoBadge}
                                        </label>
                                    </div>
                            `;

                            // If product has variants, render nested variants list
                            if (hasVariants) {
                                html += `<div class="ms-4 ps-2 border-start mt-1.5 pt-1">`;
                                p.variants.forEach(v => {
                                    visibleCount++;
                                    const vPrice = '₱' + Number(v.selling_price || 0).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    const vBarcode = v.barcode ? `<span class="extra-small text-muted font-mono ms-1">(${v.barcode})</span>` : '';
                                    const vInPromo = v.is_added ? '<span class="badge bg-success-subtle text-success border border-success-subtle extra-small ms-auto">✓ In Promo</span>' : '';
                                    const vDisabled = v.is_added ? 'disabled checked' : '';

                                    html += `
                                        <div class="form-check d-flex align-items-center py-1 mb-0">
                                            <input class="form-check-input catalog-item-check me-2" type="checkbox" data-type="variant" value="${v.id}" id="varCheck_${v.id}" ${vDisabled}>
                                            <label class="form-check-label d-flex align-items-center gap-1 w-100 cursor-pointer extra-small text-dark mb-0" for="varCheck_${v.id}">
                                                <i class="bi bi-diagram-2 text-muted me-1"></i>
                                                <span class="fw-bold text-dark">${v.variant_name}</span>
                                                ${vBarcode}
                                                <span class="font-mono text-success fw-bold ms-1.5">${vPrice}</span>
                                                ${vInPromo}
                                            </label>
                                        </div>
                                    `;
                                });
                                html += `</div>`;
                            }

                            html += `</div>`;
                        });

                        $('#catalogListContainer').html(html);
                        $('#txtCatalogVisibleCount').text(visibleCount);
                    } else {
                        $('#catalogListContainer').html('<div class="text-center py-5 text-muted small"><i class="bi bi-inbox fs-3 d-block mb-1 opacity-50"></i>No eligible products found.</div>');
                        $('#txtCatalogVisibleCount').text('0');
                    }
                    updateSelectedCount();
                }
            });
        }

        loadCatalogProducts();

        $('#filterCatalogCategory').on('change', loadCatalogProducts);
        
        let searchTimer;
        $('#inpCatalogSearch').on('keyup', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(loadCatalogProducts, 300);
        });

        // Select All Visible Checkbox
        $('#checkSelectAllCatalog').on('change', function () {
            const isChecked = $(this).prop('checked');
            $('.catalog-item-check:not(:disabled)').prop('checked', isChecked);
            updateSelectedCount();
        });

        $(document).on('change', '.catalog-item-check', updateSelectedCount);

        function updateSelectedCount() {
            const count = $('.catalog-item-check:checked:not(:disabled)').length;
            $('#txtSelectedCount').text(`${count} selected`);
            if (count > 0) {
                $('#btnAddSelectedProducts').prop('disabled', false).html(`
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Add ${count} Selected Item(s) to Promo</span>
                `);
            } else {
                $('#btnAddSelectedProducts').prop('disabled', true).html(`
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>Add Selected Items to Promo</span>
                `);
            }
        }

        // Add Batch Products & Variants
        $('#btnAddSelectedProducts').on('click', function () {
            const items = [];
            $('.catalog-item-check:checked:not(:disabled)').each(function () {
                items.push({
                    id: parseInt($(this).val()),
                    type: $(this).data('type') || 'product'
                });
            });

            if (items.length === 0) return;

            const btn = $(this);
            const orig = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Adding Items...');

            $.ajax({
                url: "{{ route('promotions.items.add_batch', $promo->id) }}",
                type: 'POST',
                data: {
                    items: items,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res && res.success) {
                        $('#checkSelectAllCatalog').prop('checked', false);
                        itemsTable.ajax.reload();
                        loadCatalogProducts();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to Promotion!',
                                text: res.message,
                                timer: 1600,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to add items.');
                },
                complete: function () {
                    btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Add Batch Categories
        $('#btnAddSelectedCategories').on('click', function () {
            const checkedIds = $('.category-checkbox:checked').map(function () {
                return $(this).val();
            }).get();

            if (checkedIds.length === 0) {
                alert('Please check at least one category.');
                return;
            }

            const btn = $(this);
            const orig = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Adding Categories...');

            $.ajax({
                url: "{{ route('promotions.items.add_batch', $promo->id) }}",
                type: 'POST',
                data: {
                    item_ids: checkedIds,
                    item_type: 'category',
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    if (res && res.success) {
                        $('.category-checkbox').prop('checked', false);
                        itemsTable.ajax.reload();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Categories Added!',
                                text: res.message,
                                timer: 1600,
                                showConfirmButton: false
                            });
                        }
                    }
                },
                error: function (xhr) {
                    alert(xhr.responseJSON?.message || 'Failed to add categories.');
                },
                complete: function () {
                    btn.prop('disabled', false).html(orig);
                }
            });
        });

        // Add by Barcode Gun
        function submitBarcode() {
            const barcode = $('#inpBarcodeScan').val().trim();
            if (!barcode) return;

            $.ajax({
                url: "{{ route('promotions.items.add_barcode', $promo->id) }}",
                type: 'POST',
                data: {
                    barcode: barcode,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    $('#inpBarcodeScan').val('').focus();
                    itemsTable.ajax.reload();
                    loadCatalogProducts();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Item Added!',
                            text: res.message,
                            timer: 1200,
                            showConfirmButton: false
                        });
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Product or barcode not found.';
                    alert(msg);
                    $('#inpBarcodeScan').select().focus();
                }
            });
        }

        $('#btnAddBarcode').on('click', submitBarcode);
        $('#inpBarcodeScan').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitBarcode();
            }
        });

        // Remove Single Item
        $(document).on('click', '.btn-remove-item', function () {
            const itemId = $(this).data('id');
            $.ajax({
                url: `/promotions/items/remove/${itemId}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function () {
                    itemsTable.ajax.reload(null, false);
                    loadCatalogProducts();
                }
            });
        });

        // Remove All Items
        $('#btnRemoveAll').on('click', function () {
            if (!confirm('Are you sure you want to remove ALL items from this promotion?')) return;

            $.ajax({
                url: "{{ route('promotions.items.remove_all', $promo->id) }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                success: function () {
                    itemsTable.ajax.reload();
                    loadCatalogProducts();
                }
            });
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
            initManageItems(window.$);
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
