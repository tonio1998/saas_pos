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
                <span class="text-muted extra-small font-mono">/ Minimart Shelf Merchandising</span>
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
                                    <th style="width:30px;"><input type="checkbox" id="chkMasterSelect" class="form-check-input"></th>
                                    <th>Product Details</th>
                                    <th>Barcode / SKU</th>
                                    <th>Price</th>
                                    <th style="width:120px;">Copies</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $idx => $product)
                                    @if($product->variants && $product->variants->count() > 0)
                                        @foreach($product->variants as $v)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="form-check-input chk-product-item" data-index="{{ $idx }}_{{ $v->id }}">
                                                    <input type="hidden" name="items[{{ $idx }}_{{ $v->id }}][id]" value="{{ $product->id }}" disabled class="inp-prod-id">
                                                    <input type="hidden" name="items[{{ $idx }}_{{ $v->id }}][variant_id]" value="{{ $v->id }}" disabled class="inp-variant-id">
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle extra-small" style="background:#ede9fe; color:#6d28d9;">
                                                        <i class="bi bi-layers me-1"></i>{{ $v->variant_name }}
                                                    </span>
                                                </td>
                                                <td class="font-mono small text-muted">
                                                    {{ $v->barcode ?: ($product->barcode ?: 'AUTO') }}
                                                </td>
                                                <td class="font-mono fw-black text-dark">
                                                    ₱{{ number_format($v->price, 2) }}
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{ $idx }}_{{ $v->id }}][qty]" class="form-control form-control-sm font-mono text-center inp-qty" value="5" min="1" max="100" disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input chk-product-item" data-index="{{ $idx }}">
                                                <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $product->id }}" disabled class="inp-prod-id">
                                                <input type="hidden" name="items[{{ $idx }}][variant_id]" value="" disabled class="inp-variant-id">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $product->name }}</div>
                                                <span class="text-muted extra-small">{{ $product->category?->name ?? 'Grocery' }}</span>
                                            </td>
                                            <td class="font-mono small text-muted">
                                                {{ $product->barcode ?: ($product->sku ?: 'AUTO') }}
                                            </td>
                                            <td class="font-mono fw-black text-dark">
                                                ₱{{ number_format($product->price, 2) }}
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

@push('scripts')
<script>
(function () {
    function initBarcodeLabelPicker($) {
        const table = $('#productsPickerTable').DataTable({
            pageLength: 25,
            ordering: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search product, variant, barcode..."
            }
        });

        function updateCounts() {
            let selectedCount = 0;
            let totalStickers = 0;

            $('.chk-product-item:checked').each(function () {
                selectedCount++;
                const row = $(this).closest('tr');
                const qty = parseInt(row.find('.inp-qty').val()) || 1;
                totalStickers += qty;
            });

            $('#lblSelectedCount').text(selectedCount + ' item' + (selectedCount === 1 ? '' : 's'));
            $('#lblTotalStickersCount').text(totalStickers + ' sticker' + (totalStickers === 1 ? '' : 's'));

            $('#btnPrintStickers').prop('disabled', selectedCount === 0);
        }

        $(document).on('change', '.chk-product-item', function () {
            const row = $(this).closest('tr');
            const isChecked = $(this).is(':checked');

            row.find('.inp-prod-id, .inp-variant-id, .inp-qty').prop('disabled', !isChecked);
            updateCounts();
        });

        $(document).on('input change', '.inp-qty', function () {
            updateCounts();
        });

        $('#chkMasterSelect').on('change', function () {
            const isChecked = $(this).is(':checked');
            $('.chk-product-item').prop('checked', isChecked).trigger('change');
        });

        $('#btnSelectAll').on('click', function () {
            $('.chk-product-item').prop('checked', true).trigger('change');
            $('#chkMasterSelect').prop('checked', true);
        });

        $('#btnDeselectAll').on('click', function () {
            $('.chk-product-item').prop('checked', false).trigger('change');
            $('#chkMasterSelect').prop('checked', false);
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable) {
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
