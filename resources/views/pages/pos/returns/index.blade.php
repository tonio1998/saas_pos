@extends('layouts.app')

@section('title', 'Sales Returns & Restock Ledger | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Executive Header Bar --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-arrow-return-left fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Sales Returns & Restock Ledger</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Comprehensive tracking of customer product returns, invoice refunds, restocking, and inventory adjustments.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('sales.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-receipt-cutoff text-success"></i>
                <span>Sales History</span>
            </a>

            <button type="button" class="btn btn-danger fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" data-bs-toggle="modal" data-bs-target="#newReturnModal" style="background:linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-plus-circle-fill fs-6"></i>
                <span>Process Sales Return</span>
            </button>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- 1. Total Return Logs --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Total Returns</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-journal-arrow-down fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-dark mb-0">{{ number_format($totalReturnsCount) }} <span class="fs-6 text-muted fw-normal">logs</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Processed Returns</div>
            </div>
        </div>

        {{-- 2. Total Restocked Qty --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Items Restocked</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-box-arrow-in-down-left fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">+{{ number_format($totalReturnedQty, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Units Re-added to Inventory</div>
            </div>
        </div>

        {{-- 3. Total Refund Value --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Refund Value</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-cash-stack fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-warning-emphasis mb-0">₱{{ number_format($totalRefundValue, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Est. Refunded Value</div>
            </div>
        </div>

        {{-- 4. Status Indicator --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Inventory Impact</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-arrow-repeat fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-purple mb-0" style="color:#7c3aed;">Restocked</div>
                <div class="text-success extra-small mt-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Stock Auto-Adjusted</div>
            </div>
        </div>
    </div>

    {{-- Main Returns Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-arrow-return-left text-danger fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Sales Returns Audit Trail</h5>
            </div>

            <button type="button" class="btn btn-danger btn-sm font-mono fw-bold px-3 py-1.5 rounded-3 extra-small shadow-xs text-white" data-bs-toggle="modal" data-bs-target="#newReturnModal" style="background:#dc2626;border:none;">
                <i class="bi bi-plus-lg me-1"></i> Process Return
            </button>
        </div>

        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="returnsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Target Product</th>
                            <th>Returned Qty</th>
                            <th>Est. Refund Value</th>
                            <th>Reason / Reference</th>
                            <th>Date & Time</th>
                            <th>Processed By</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Process Product Return (Tabs: By Invoice Receipt OR By Single Product) --}}
<div class="modal fade" id="newReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-white border-bottom p-3.5">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 p-1.5 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-arrow-return-left fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6">Process Customer Sales Return</h5>
                        <small class="text-muted extra-small">Support per-item return or full/partial invoice return with auto-restock</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Mode Selection Navigation Tabs --}}
            <div class="bg-light px-3 pt-2 border-bottom">
                <ul class="nav nav-pills card-header-pills gap-2" id="returnModeTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold extra-small rounded-3 px-3 py-1.5" id="tab-invoice-btn" data-bs-toggle="pill" data-bs-target="#tab-invoice" type="button" role="tab">
                            <i class="bi bi-receipt me-1"></i> Return By Invoice # (Recommended)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold extra-small rounded-3 px-3 py-1.5" id="tab-single-btn" data-bs-toggle="pill" data-bs-target="#tab-single" type="button" role="tab">
                            <i class="bi bi-box-seam me-1"></i> Single Product Direct Return
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="returnModeTabsContent">
                {{-- TAB 1: Return By Invoice --}}
                <div class="tab-pane fade show active p-4" id="tab-invoice" role="tabpanel">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-bold small mb-1">Enter Sales Invoice Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="invoiceSearchInput" class="form-control rounded-start-3 font-mono" placeholder="e.g. INV-2026-00001">
                            <button type="button" id="btnSearchInvoice" class="btn btn-primary fw-bold font-mono px-3.5 rounded-end-3">
                                <i class="bi bi-search me-1"></i> Search Invoice
                            </button>
                        </div>
                    </div>

                    {{-- Invoice Result Container --}}
                    <div id="invoiceSearchResultContainer" class="d-none">
                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark font-mono fs-6" id="foundInvoiceNo"></span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 extra-small fw-bold" id="foundInvoiceCustomer"></span>
                            </div>
                            <small class="text-muted font-mono" id="foundInvoiceMeta"></small>
                        </div>

                        <form id="formBatchReturn">
                            @csrf
                            <input type="hidden" name="invoice_no" id="hiddenInvoiceNo">
                            <label class="form-label text-dark fw-bold small mb-2">Select Items & Quantities to Return:</label>
                            <div class="table-responsive border rounded-3 mb-3">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:40px;"><input type="checkbox" id="selectAllInvoiceItems"></th>
                                            <th>Product Item</th>
                                            <th class="text-center">Purchased Qty</th>
                                            <th class="text-end" style="width:140px;">Return Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoiceItemsTableBody"></tbody>
                                </table>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-dark fw-bold small mb-1">Return Reason / Audit Note</label>
                                <input type="text" name="reason" class="form-control rounded-3" placeholder="Explain reason for return (e.g. Customer exchange, defective packaging)...">
                            </div>

                            <button type="submit" class="btn btn-danger font-mono fw-bold w-100 py-2.5 rounded-3 text-white shadow-xs" style="background:#dc2626;border:none;">
                                <i class="bi bi-check-circle me-1.5"></i> Process Selected Invoice Returns & Restock
                            </button>
                        </form>
                    </div>
                </div>

                {{-- TAB 2: Single Product Direct Return --}}
                <div class="tab-pane fade p-4" id="tab-single" role="tabpanel">
                    <form id="formSingleReturn" action="{{ route('returns.store') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label text-dark fw-bold small mb-1">Search & Select Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id_select" class="form-select rounded-3" required></select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold small mb-1">Invoice Reference (Optional)</label>
                                <input type="text" name="invoice_no" class="form-control rounded-3 font-mono" placeholder="INV-2026-XXXX">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-dark fw-bold small mb-1">Quantity Returned <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="qty" class="form-control rounded-3 font-mono fw-bold text-danger fs-5" placeholder="0.00" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label text-dark fw-bold small mb-1">Return Reason & Customer Remarks</label>
                                <textarea name="reason" rows="2" class="form-control rounded-3" placeholder="Explain reason for return..."></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-danger font-mono fw-bold px-4 py-2 rounded-3 text-white shadow-xs" style="background:#dc2626;border:none;">
                                <i class="bi bi-check-circle me-1.5"></i> Submit Return
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initReturnsPage($) {
        const $modal = $('#newReturnModal');
        const $select = $('#product_id_select');

        // Modal shown event for Select2 initialization
        $modal.on('shown.bs.modal', function () {
            if ($select.length && !$select.hasClass("select2-hidden-accessible")) {
                $select.select2({
                    theme: 'bootstrap-5',
                    dropdownParent: $modal,
                    placeholder: 'Type SKU, Barcode, or Product Name...',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ route('select2.products') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data.results }; },
                        cache: true
                    }
                });
            }
        });

        // Initialize DataTables
        if ($('#returnsTable').length) {
            $('#returnsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('returns.data') }}",
                columns: [
                    { data: 'product_name', name: 'product.name' },
                    { data: 'qty', name: 'qty', className: 'text-center' },
                    { data: 'estimated_refund', name: 'estimated_refund', className: 'text-end' },
                    { data: 'remarks', name: 'remarks' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'processed_by', name: 'processed_by' }
                ],
                order: [[4, 'desc']],
                pageLength: 25
            });
        }

        // Invoice Lookup Handler
        $('#btnSearchInvoice').on('click', function () {
            const invNo = $('#invoiceSearchInput').val().trim();
            if (!invNo) {
                appAlert({ title: 'Invoice Required', text: 'Please enter an invoice number.', type: 'warning' });
                return;
            }

            $.getJSON("{{ route('returns.search-invoice') }}", { invoice_no: invNo }, function (res) {
                if (res.success && res.sale) {
                    const sale = res.sale;
                    const invRef = sale.invoice_no || sale.sale_code || sale.invoice_number || 'INV';
                    $('#foundInvoiceNo').text(invRef);
                    $('#hiddenInvoiceNo').val(invRef);
                    $('#foundInvoiceCustomer').text(sale.customer ? sale.customer.name : 'Walk-in Customer');
                    $('#foundInvoiceMeta').text(`Cashier: ${sale.cashier ? sale.cashier.name : '-'} | Total: ₱${parseFloat(sale.total_amount || 0).toFixed(2)}`);

                    let rows = '';
                    (sale.items || []).forEach((item, idx) => {
                        // item_name = stored product_name e.g. "Princess Bea (1kl Pack)"
                        const displayName = item.item_name || (item.product ? item.product.name : 'Product');
                        const vName       = item.variant_name || null;
                        const barcode     = item.variant ? (item.variant.barcode || item.variant.sku || '')
                                          : item.product ? (item.product.barcode || item.product.sku || '')
                                          : '';
                        const skuLine  = barcode ? `<span class="badge bg-light text-muted border font-mono" style="font-size:0.68rem;">${barcode}</span>` : '';
                        const varBadge = vName
                            ? `<span class="badge fw-bold d-inline-flex align-items-center gap-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.72rem;"><i class="bi bi-tag-fill"></i>${vName}</span>`
                            : '';

                        rows += `
                            <tr>
                                <td><input type="checkbox" class="item-chk" data-idx="${idx}" checked></td>
                                <td>
                                    <div class="fw-bold text-dark">${displayName}</div>
                                    <div class="d-flex align-items-center flex-wrap gap-1 mt-0.5">
                                        ${skuLine}${varBadge}
                                    </div>
                                    <input type="hidden" name="items[${idx}][product_id]" value="${item.product_id}">
                                    ${item.variant_id ? `<input type="hidden" name="items[${idx}][variant_id]" value="${item.variant_id}">` : ''}
                                </td>
                                <td class="text-center font-mono fw-bold">${item.qty}</td>
                                <td>
                                    <input type="number" step="0.01" max="${item.qty}" name="items[${idx}][qty]" value="${item.qty}" class="form-control form-control-sm font-mono text-end item-qty" required>
                                </td>
                            </tr>
                        `;
                    });


                    $('#invoiceItemsTableBody').html(rows);
                    $('#invoiceSearchResultContainer').removeClass('d-none');
                } else {
                    $('#invoiceSearchResultContainer').addClass('d-none');
                    appAlert({ title: 'Not Found', text: res.message || 'Invoice not found.', type: 'danger' });
                }
            }).fail(function () {
                appAlert({ title: 'Error', text: 'Failed to search invoice.', type: 'danger' });
            });
        });

        // Batch Form Submit
        $('#formBatchReturn').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post("{{ route('returns.store-batch') }}", formData, async function (res) {
                if (res.success) {
                    await appAlert({ title: 'Success!', text: res.message, type: 'success', confirmText: 'Done' });
                    bootstrap.Modal.getInstance(document.getElementById('newReturnModal'))?.hide();
                    if (window.$ && $.fn.DataTable.isDataTable('#returnsTable')) {
                        $('#returnsTable').DataTable().ajax.reload(null, false);
                    }
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    await appAlert({ title: 'Failed', text: res.message || 'Operation failed.', type: 'danger', confirmText: 'Close' });
                }
            }).fail(async function (xhr) {
                await appAlert({ title: 'Server Error', text: xhr.responseJSON?.message || 'Error processing return.', type: 'danger', confirmText: 'Close' });
            });
        });

        // Single Return Form Submit
        $('#formSingleReturn').on('submit', function (e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.post("{{ route('returns.store') }}", formData, async function (res) {
                if (res.success) {
                    await appAlert({ title: 'Success!', text: res.message, type: 'success', confirmText: 'Done' });
                    bootstrap.Modal.getInstance(document.getElementById('newReturnModal'))?.hide();
                    if (window.$ && $.fn.DataTable.isDataTable('#returnsTable')) {
                        $('#returnsTable').DataTable().ajax.reload(null, false);
                    }
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    await appAlert({ title: 'Failed', text: res.message || 'Operation failed.', type: 'danger', confirmText: 'Close' });
                }
            }).fail(async function (xhr) {
                await appAlert({ title: 'Server Error', text: xhr.responseJSON?.message || 'Error processing return.', type: 'danger', confirmText: 'Close' });
            });
        });
    }

    function checkJQuery() {
        if (window.$ && window.$.fn && window.$.fn.DataTable && window.$.fn.select2) {
            initReturnsPage(window.$);
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
