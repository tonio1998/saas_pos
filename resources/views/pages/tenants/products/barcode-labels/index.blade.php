@extends('layouts.app')

@section('title', 'Print Shelf Price Tags & Barcode Labels')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-dark text-white px-2.5 py-1 rounded-pill extra-small font-mono fw-bold">
                    <i class="bi bi-upc-scan me-1"></i> BARCODE & LABELS
                </span>
                <span class="text-muted extra-small font-mono">/ {{ $appStoreConfig['business_name'] ?? 'Store' }} Shelf Merchandising</span>
            </div>
            <h3 class="h4 fw-black text-dark font-mono mt-1 mb-0">Shelf Price Tags & Barcode Stickers Generator</h3>
            <p class="text-muted small mb-0">Generate and print high-resolution barcode stickers for shelves, repacked items, or product packaging</p>
        </div>
    </div>

    <form id="formPrintLabels" action="{{ route('products.barcode-labels.print') }}" method="POST" target="_blank">
        @csrf
        <div class="row g-3">
            {{-- Left Column: Print Configuration Card --}}
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-xs rounded-4 bg-white p-4 sticky-top" style="top: 80px;">
                    <h5 class="fw-black text-dark font-mono fs-6 mb-3">
                        <i class="bi bi-sliders text-primary me-2"></i>Label Print Settings
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-dark">Label / Paper Format</label>
                        <select name="paper_size" id="selectPaperSize" class="form-select rounded-3">
                            <option value="a4_3col">A4 Sheet (3 Columns - 65 Labels/Sheet)</option>
                            <option value="shelf_tag">Shelf Price Tags (Large SRP & Barcode)</option>
                            <option value="thermal_50x30">Thermal Roll Sticker (50mm x 30mm)</option>
                        </select>
                        <div class="text-muted extra-small mt-1">Calibrated for standard A4 sticker paper and thermal printers</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="chkShowPrice" name="show_price" value="1" checked>
                            <label class="form-check-label fw-bold small text-dark" for="chkShowPrice">Show Selling Price (₱ SRP)</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="chkShowStoreName" name="show_store_name" value="1" checked>
                            <label class="form-check-label fw-bold small text-dark" for="chkShowStoreName">Show Store Name Header</label>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="small fw-bold text-muted font-mono">Selected Items:</span>
                            <span class="fw-black text-primary font-mono fs-6" id="lblSelectedCount">0 products</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="small fw-bold text-muted font-mono">Total Stickers to Print:</span>
                            <span class="fw-black text-success font-mono fs-6" id="lblTotalStickersCount">0 stickers</span>
                        </div>
                    </div>

                    <button type="submit" id="btnPrintStickers" class="btn btn-primary fw-bold w-100 rounded-pill py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2" disabled>
                        <i class="bi bi-printer-fill fs-6"></i>
                        <span>Generate & Print Labels</span>
                    </button>
                </div>
            </div>

            {{-- Right Column: Product Picker Table --}}
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-xs rounded-4 bg-white p-3.5">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-2 mb-3 pb-3 border-bottom">
                        <div>
                            <h5 class="fw-black text-dark font-mono fs-6 mb-0">Select Products & Variants</h5>
                            <span class="text-muted extra-small">Check products and specify how many sticker copies you need</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="btnSelectAll" class="btn btn-sm btn-light border fw-bold rounded-pill px-3">Select All</button>
                            <button type="button" id="btnDeselectAll" class="btn btn-sm btn-light border fw-bold rounded-pill px-3">Clear</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="productsPickerTable" class="table table-hover align-middle mb-0" style="width:100%;">
                            <thead class="bg-light text-muted extra-small text-uppercase font-mono">
                                <tr>
                                    <th style="width:38px;"><input type="checkbox" id="chkMasterSelect" class="form-check-input"></th>
                                    <th style="min-width: 300px !important; width: 300px !important;">Product Details</th>
                                    <th>Barcode / SKU</th>
                                    <th>Price</th>
                                    <th style="width:120px;">Copies</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $idx => $product)
                                    @if($product->variants && $product->variants->count() > 0)
                                        @foreach($product->variants as $v)
                                            @if((float)$v->selling_price > 0)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="form-check-input chk-product-item" data-index="{{ $idx }}_{{ $v->id }}">
                                                        <input type="hidden" name="items[{{ $idx }}_{{ $v->id }}][id]" value="{{ $product->id }}" disabled class="inp-prod-id">
                                                        <input type="hidden" name="items[{{ $idx }}_{{ $v->id }}][variant_id]" value="{{ $v->id }}" disabled class="inp-variant-id">
                                                        <input type="hidden" name="items[{{ $idx }}_{{ $v->id }}][price]" value="{{ number_format($v->selling_price, 2, '.', '') }}" disabled class="inp-price">
                                                    </td>
                                                    <td style="min-width: 300px !important; width: 300px !important;">
                                                        <div class="fw-bold text-dark text-wrap" style="word-break: break-word;">{{ $product->name }}</div>
                                                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle extra-small" style="background:#ede9fe; color:#6d28d9;">
                                                            <i class="bi bi-layers me-1"></i>{{ $v->variant_name }}
                                                        </span>
                                                    </td>
                                                    <td class="font-mono small text-muted">
                                                        {{ $v->barcode ?: ($product->barcode ?: 'AUTO') }}
                                                    </td>
                                                    <td style="width: 140px;">
                                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 fw-bold btn-quick-price font-mono shadow-2xs"
                                                                data-id="{{ $product->id }}"
                                                                data-variant-id="{{ $v->id }}"
                                                                data-name="{{ $product->name }}"
                                                                data-variant-name="{{ $v->variant_name }}"
                                                                data-sku="{{ $v->sku ?: ($product->sku ?: 'AUTO') }}"
                                                                data-barcode="{{ $v->barcode ?: ($product->barcode ?: '') }}"
                                                                data-cost="{{ $v->cost_price ?: ($product->cost_price ?: 0) }}"
                                                                data-selling="{{ $v->selling_price }}"
                                                                data-wholesale="{{ $v->wholesale_price ?: 0 }}"
                                                                title="Click to change or update database price">
                                                            <span class="lbl-price-val text-dark">₱{{ number_format($v->selling_price, 2) }}</span>
                                                            <i class="bi bi-pencil-fill text-primary" style="font-size: 10px;"></i>
                                                        </button>
                                                    </td>
                                                    <td>
                                                        <input type="number" name="items[{{ $idx }}_{{ $v->id }}][qty]" class="form-control form-control-sm font-mono text-center inp-qty" value="5" min="1" max="100" disabled>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @elseif((float)$product->selling_price > 0)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input chk-product-item" data-index="{{ $idx }}">
                                                <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $product->id }}" disabled class="inp-prod-id">
                                                <input type="hidden" name="items[{{ $idx }}][variant_id]" value="" disabled class="inp-variant-id">
                                                <input type="hidden" name="items[{{ $idx }}][price]" value="{{ number_format($product->selling_price, 2, '.', '') }}" disabled class="inp-price">
                                            </td>
                                            <td style="min-width: 300px !important; width: 300px !important;">
                                                <div class="fw-bold text-dark text-wrap" style="word-break: break-word;">{{ $product->name }}</div>
                                                <span class="text-muted extra-small">{{ $product->category?->name ?? 'Grocery' }}</span>
                                            </td>
                                            <td class="font-mono small text-muted">
                                                {{ $product->barcode ?: ($product->sku ?: 'AUTO') }}
                                            </td>
                                            <td style="width: 140px;">
                                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 fw-bold btn-quick-price font-mono shadow-2xs"
                                                        data-id="{{ $product->id }}"
                                                        data-variant-id=""
                                                        data-name="{{ $product->name }}"
                                                        data-variant-name=""
                                                        data-sku="{{ $product->sku ?: 'AUTO' }}"
                                                        data-barcode="{{ $product->barcode ?: '' }}"
                                                        data-cost="{{ $product->cost_price ?: 0 }}"
                                                        data-selling="{{ $product->selling_price }}"
                                                        data-wholesale="{{ $product->wholesale_price ?: 0 }}"
                                                        title="Click to change or update database price">
                                                    <span class="lbl-price-val text-dark">₱{{ number_format($product->selling_price, 2) }}</span>
                                                    <i class="bi bi-pencil-fill text-primary" style="font-size: 10px;"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $idx }}][qty]" class="form-control form-control-sm font-mono text-center inp-qty" value="5" min="1" max="100" disabled>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

{{-- Quick Price Adjustment Modal --}}
<div class="modal fade" id="modalQuickPrice" tabindex="-1" aria-labelledby="modalQuickPriceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3.5 px-4 border-0">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-white bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-currency-exchange text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold font-mono text-white mb-0" id="modalQuickPriceLabel">Price Adjustment</h6>
                        <span class="text-white text-opacity-75 extra-small font-mono">Quickly update price or override printable stickers</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                {{-- Target Product Info Card --}}
                <div class="p-3 rounded-3 bg-light border mb-3.5">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <h6 class="fw-black text-dark font-mono mb-1" id="qpModalProdName">Product Name</h6>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="badge bg-purple-subtle text-purple font-mono extra-small d-none" id="qpModalVariantBadge" style="background:#ede9fe; color:#6d28d9;">
                                    <i class="bi bi-layers me-1"></i><span id="qpModalVariantName">Variant</span>
                                </span>
                                <span class="text-muted extra-small font-mono">SKU: <strong class="text-dark" id="qpModalSku">SKU</strong></span>
                                <span class="text-muted extra-small font-mono">BARCODE: <strong class="text-dark" id="qpModalBarcode">CODE</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Price Stats Row --}}
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <span class="text-muted extra-small font-mono d-block text-uppercase">Cost Price</span>
                            <span class="fw-bold text-secondary font-mono fs-6" id="qpModalCostPrice">₱0.00</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2.5 rounded-3 border bg-light text-center">
                            <span class="text-muted extra-small font-mono d-block text-uppercase">Current Selling</span>
                            <span class="fw-black text-dark font-mono fs-6" id="qpModalCurrentSelling">₱0.00</span>
                        </div>
                    </div>
                </div>

                {{-- Input: New Selling Price --}}
                <div class="mb-3">
                    <label class="form-label fw-black text-dark font-mono small mb-1">
                        New Selling Price (₱ SRP) <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-2 border-end-0 fw-black text-primary font-mono fs-5">₱</span>
                        <input type="number" step="0.01" min="0.01" id="qpInputNewSelling" class="form-control border-2 border-start-0 font-mono fw-black text-dark fs-4" placeholder="0.00" required>
                    </div>
                    <div class="text-muted extra-small font-mono mt-1">This price will be used for labels and optional permanent DB update.</div>
                </div>

                {{-- Reason / Remarks (Optional) --}}
                <div class="mb-2">
                    <label class="form-label fw-bold text-dark font-mono extra-small mb-1">Reason for Adjustment (Optional)</label>
                    <select id="qpSelectReason" class="form-select form-select-sm rounded-3 font-mono">
                        <option value="Price Correction">Price Correction / Fix typo</option>
                        <option value="Supplier Cost Increase">Supplier Cost Increase</option>
                        <option value="Store Promotion / Markdown">Store Promotion / Markdown</option>
                        <option value="Repacked / Shelf SRP Adjust">Repacked / Shelf SRP Adjust</option>
                        <option value="Seasonal Price Update">Seasonal Price Update</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer bg-light p-3 px-4 border-top d-flex align-items-center justify-content-between gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold text-dark" id="btnOverridePrintOnly">
                    <i class="bi bi-tag-fill text-muted me-1"></i> Print Override Only
                </button>

                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm" id="btnSaveToDatabase">
                    <i class="bi bi-cloud-arrow-up-fill fs-6"></i>
                    <span>Save to Database</span>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    let currentQuickPriceRow = null;

    function initBarcodeLabelPicker($) {
        const table = $('#productsPickerTable').DataTable({
            pageLength: 25,
            ordering: true,
            columnDefs: [
                { orderable: false, targets: [0, 3, 4] }
            ],
            order: [[1, 'asc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search product, variant, barcode..."
            }
        });

        function updateCounts() {
            let selectedCount = 0;
            let totalStickers = 0;

            table.$('.chk-product-item:checked').each(function () {
                selectedCount++;
                const row = $(this).closest('tr');
                const qty = parseInt(row.find('.inp-qty').val()) || 1;
                totalStickers += qty;
            });

            $('#lblSelectedCount').text(selectedCount + ' product' + (selectedCount === 1 ? '' : 's'));
            $('#lblTotalStickersCount').text(totalStickers + ' sticker' + (totalStickers === 1 ? '' : 's'));
            $('#btnPrintStickers').prop('disabled', selectedCount === 0);
        }

        // Individual item checkbox change
        $(document).on('change', '.chk-product-item', function () {
            const row = $(this).closest('tr');
            const isChecked = this.checked;

            row.find('.inp-prod-id, .inp-variant-id, .inp-qty, .inp-price').prop('disabled', !isChecked);
            updateCounts();
        });

        // Copies qty change
        $(document).on('input change', '.inp-qty', function () {
            updateCounts();
        });

        // Master Header Checkbox (1-click Instant Batch Toggle)
        $('#chkMasterSelect').on('click', function (e) {
            e.stopPropagation();
            const isChecked = this.checked;

            table.$('.chk-product-item').each(function () {
                this.checked = isChecked;
                const row = $(this).closest('tr');
                row.find('.inp-prod-id, .inp-variant-id, .inp-qty, .inp-price').prop('disabled', !isChecked);
            });

            updateCounts();
        });

        // Top "Select All" Button (1-click Instant Batch Select)
        $('#btnSelectAll').on('click', function (e) {
            e.preventDefault();
            $('#chkMasterSelect').prop('checked', true);

            table.$('.chk-product-item').each(function () {
                this.checked = true;
                const row = $(this).closest('tr');
                row.find('.inp-prod-id, .inp-variant-id, .inp-qty, .inp-price').prop('disabled', false);
            });

            updateCounts();
        });

        // Top "Clear" Button (1-click Instant Batch Clear)
        $('#btnDeselectAll').on('click', function (e) {
            e.preventDefault();
            $('#chkMasterSelect').prop('checked', false);

            table.$('.chk-product-item').each(function () {
                this.checked = false;
                const row = $(this).closest('tr');
                row.find('.inp-prod-id, .inp-variant-id, .inp-qty, .inp-price').prop('disabled', true);
            });

            updateCounts();
        });

        // Click Price Button -> Open Modal
        $(document).on('click', '.btn-quick-price', function (e) {
            e.preventDefault();
            const btn = $(this);
            currentQuickPriceRow = btn.closest('tr');

            const prodId = btn.data('id');
            const variantId = btn.data('variant-id');
            const prodName = btn.data('name');
            const variantName = btn.data('variant-name');
            const sku = btn.data('sku') || 'N/A';
            const barcode = btn.data('barcode') || 'N/A';
            const costPrice = parseFloat(btn.data('cost')) || 0;
            const sellingPrice = parseFloat(btn.data('selling')) || 0;

            $('#qpModalProdName').text(prodName);
            if (variantName) {
                $('#qpModalVariantName').text(variantName);
                $('#qpModalVariantBadge').removeClass('d-none');
            } else {
                $('#qpModalVariantBadge').addClass('d-none');
            }

            $('#qpModalSku').text(sku);
            $('#qpModalBarcode').text(barcode);
            $('#qpModalCostPrice').text('₱' + costPrice.toFixed(2));
            $('#qpModalCurrentSelling').text('₱' + sellingPrice.toFixed(2));

            // Set default value in input
            $('#qpInputNewSelling').val(sellingPrice.toFixed(2));

            // Store metadata on modal
            $('#modalQuickPrice').data({
                productId: prodId,
                variantId: variantId,
                button: btn,
                row: currentQuickPriceRow
            });

            const modalInstance = new bootstrap.Modal(document.getElementById('modalQuickPrice'));
            modalInstance.show();

            setTimeout(() => {
                $('#qpInputNewSelling').focus().select();
            }, 350);
        });

        // Modal Action 1: Print Override Only
        $('#btnOverridePrintOnly').on('click', function () {
            const newPrice = parseFloat($('#qpInputNewSelling').val());
            if (isNaN(newPrice) || newPrice <= 0) {
                alert('Please enter a valid price.');
                return;
            }

            const data = $('#modalQuickPrice').data();
            if (data && data.row) {
                const row = data.row;
                row.find('.lbl-price-val').text('₱' + newPrice.toFixed(2));
                row.find('.inp-price').val(newPrice.toFixed(2));
                row.find('.btn-quick-price').data('selling', newPrice);
            }

            bootstrap.Modal.getInstance(document.getElementById('modalQuickPrice')).hide();
        });

        // Modal Action 2: Save to Database via AJAX
        $('#btnSaveToDatabase').on('click', function () {
            const newPrice = parseFloat($('#qpInputNewSelling').val());
            if (isNaN(newPrice) || newPrice <= 0) {
                alert('Please enter a valid price.');
                return;
            }

            const data = $('#modalQuickPrice').data();
            const btnSubmit = $(this);
            const originalBtnHtml = btnSubmit.html();

            btnSubmit.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

            $.ajax({
                url: "{{ route('products.barcode-labels.update-price') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    product_id: data.productId,
                    variant_id: data.variantId || null,
                    selling_price: newPrice,
                    reason: $('#qpSelectReason').val()
                },
                success: function (res) {
                    if (res && res.success) {
                        const row = data.row;
                        if (row) {
                            row.find('.lbl-price-val').text(res.formatted_price);
                            row.find('.inp-price').val(res.new_price.toFixed(2));
                            row.find('.btn-quick-price').data('selling', res.new_price);
                            
                            // Visual pulse animation
                            row.find('.btn-quick-price').addClass('btn-success text-white').removeClass('btn-light');
                            setTimeout(() => {
                                row.find('.btn-quick-price').removeClass('btn-success text-white').addClass('btn-light');
                            }, 1200);
                        }

                        bootstrap.Modal.getInstance(document.getElementById('modalQuickPrice')).hide();
                    } else {
                        alert(res.message || 'Failed to update price.');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error updating price in database.';
                    alert(msg);
                },
                complete: function () {
                    btnSubmit.prop('disabled', false).html(originalBtnHtml);
                }
            });
        });

        // Form submit handler: include inputs from all DataTables pages
        $('#formPrintLabels').on('submit', function (e) {
            const form = this;
            // Remove previous temporary inputs if any
            $(form).find('.dt-hidden-input').remove();

            table.$('.chk-product-item:checked').each(function () {
                const row = $(this).closest('tr');
                // If the row is not in current DOM view, append hidden inputs so all selected pages are printed
                if (!$.contains(document, row[0])) {
                    const prodId = row.find('.inp-prod-id').val();
                    const variantId = row.find('.inp-variant-id').val();
                    const qty = row.find('.inp-qty').val() || 1;
                    const price = row.find('.inp-price').val();
                    const idx = $(this).data('index');

                    $(form).append(`<input type="hidden" class="dt-hidden-input" name="items[${idx}][id]" value="${prodId}">`);
                    if (variantId) {
                        $(form).append(`<input type="hidden" class="dt-hidden-input" name="items[${idx}][variant_id]" value="${variantId}">`);
                    }
                    if (price !== undefined && price !== '') {
                        $(form).append(`<input type="hidden" class="dt-hidden-input" name="items[${idx}][price]" value="${price}">`);
                    }
                    $(form).append(`<input type="hidden" class="dt-hidden-input" name="items[${idx}][qty]" value="${qty}">`);
                }
            });
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable && window.bootstrap) {
            initBarcodeLabelPicker(window.$);
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
