{{-- Modal: Quick Refund & Customer Return for POS Terminal --}}
<div class="modal fade" id="quickRefundModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Modal Header --}}
            <div class="modal-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 p-2 bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-arrow-return-left fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6">Quick Sales Return & Refund</h5>
                        <small class="text-muted extra-small">Process full or partial item return with automatic inventory restocking & cash drawer payout</small>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body p-3.5 bg-slate-50">
                
                {{-- Invoice / Sale Code Search Bar --}}
                <div class="bg-white p-3 rounded-3 border shadow-xs mb-3">
                    <label class="form-label text-dark fw-bold extra-small text-uppercase mb-1.5 font-mono" style="letter-spacing: 0.5px;">
                        <i class="bi bi-receipt me-1 text-primary"></i>Sales Order / Invoice Number
                    </label>
                    <div class="input-group">
                        <input type="text" 
                               id="quickRefundSearchInput" 
                               class="form-control font-mono fw-bold text-dark" 
                               placeholder="Scan barcode or enter Order # (e.g. 260831-0001)..."
                               autocomplete="off">
                        <button type="button" id="btnQuickRefundSearch" class="btn btn-primary fw-bold font-mono px-3.5 shadow-xs">
                            <i class="bi bi-search me-1"></i> Search Order
                        </button>
                    </div>
                </div>

                {{-- Loading Spinner --}}
                <div id="quickRefundLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-danger mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="fw-bold text-dark small">Retrieving sale order items...</div>
                    <small class="text-muted extra-small">Checking return history and available quantities</small>
                </div>

                {{-- Empty Search Prompt State --}}
                <div id="quickRefundPrompt" class="text-center py-5 bg-white rounded-3 border">
                    <div class="p-3 rounded-circle bg-light d-inline-flex mb-2 text-muted">
                        <i class="bi bi-search fs-2"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Enter or Scan Order Number</h6>
                    <small class="text-muted">Type the invoice code above or click Quick Refund on a completed transaction.</small>
                </div>

                {{-- Sale Items & Refund Form Container --}}
                <div id="quickRefundFormContainer" class="d-none">
                    
                    {{-- Invoice Summary Meta Card --}}
                    <div class="p-3 rounded-3 bg-white border shadow-xs mb-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1.5">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark text-white font-mono fs-6 px-2.5 py-1" id="qrInvoiceCode">#260831-0001</span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 extra-small fw-bold" id="qrCustomerName">Walk-in Customer</span>
                            </div>
                            <div class="text-end font-mono">
                                <span class="text-muted extra-small">Original Total:</span>
                                <strong class="text-dark fs-6 ms-1" id="qrOriginalTotal">₱0.00</strong>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between extra-small text-muted font-mono pt-1.5 border-top">
                            <span id="qrCashierInfo"><i class="bi bi-person me-1"></i>Cashier: -</span>
                            <span id="qrDateInfo"><i class="bi bi-calendar3 me-1"></i>Date: -</span>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form id="formTerminalQuickRefund">
                        <input type="hidden" name="invoice_no" id="qrHiddenInvoiceNo">
                        
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label text-dark fw-bold small mb-0">
                                <i class="bi bi-check2-square text-danger me-1"></i>Select Items & Quantities to Return:
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary extra-small rounded-pill px-2 py-0.5" id="btnQrSelectAll">
                                    Select All
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary extra-small rounded-pill px-2 py-0.5" id="btnQrDeselectAll">
                                    Deselect All
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive bg-white border rounded-3 shadow-xs mb-3" style="max-height: 280px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0 font-sans" style="font-size: 0.85rem;">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th style="width: 36px;" class="ps-3"><input type="checkbox" id="chkQrMaster" checked></th>
                                        <th>Product Item</th>
                                        <th class="text-end" style="width: 100px;">Unit Price</th>
                                        <th class="text-center" style="width: 110px;">Returnable</th>
                                        <th class="text-end" style="width: 120px;">Return Qty</th>
                                        <th class="text-end pe-3" style="width: 110px;">Refund Total</th>
                                    </tr>
                                </thead>
                                <tbody id="qrItemsTableBody">
                                    {{-- Dynamically Populated --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Reason for Return --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-8">
                                <label class="form-label text-dark fw-bold extra-small mb-1">Return Reason / Cashier Remarks <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="reason" 
                                       id="qrReasonInput" 
                                       class="form-control rounded-3" 
                                       placeholder="e.g. Customer change of mind, damaged packaging, defective item" 
                                       required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-bold extra-small mb-1">Total Refund Payout</label>
                                <div class="p-2 bg-danger bg-opacity-10 border border-danger-subtle rounded-3 text-end font-mono">
                                    <span class="text-danger fw-black fs-5" id="qrEstimatedRefundDisplay">₱0.00</span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex align-items-center justify-content-between gap-2 pt-2 border-top">
                            <button type="button" class="btn btn-light border rounded-3 px-3 py-2 fw-semibold text-dark extra-small" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit" id="btnSubmitQuickRefund" class="btn btn-danger fw-bold px-4 py-2 rounded-3 text-white shadow-sm d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: none;">
                                <i class="bi bi-check-circle-fill fs-6"></i>
                                <span>CONFIRM & PROCESS REFUND</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('quickRefundModal');
    if (!modalEl) return;

    const searchInput = document.getElementById('quickRefundSearchInput');
    const searchBtn = document.getElementById('btnQuickRefundSearch');
    const loadingEl = document.getElementById('quickRefundLoading');
    const promptEl = document.getElementById('quickRefundPrompt');
    const formContainer = document.getElementById('quickRefundFormContainer');
    const itemsTbody = document.getElementById('qrItemsTableBody');
    const formEl = document.getElementById('formTerminalQuickRefund');
    const chkMaster = document.getElementById('chkQrMaster');
    const estRefundEl = document.getElementById('qrEstimatedRefundDisplay');
    const submitBtn = document.getElementById('btnSubmitQuickRefund');

    let currentSaleData = null;

    // Trigger Search
    function performInvoiceSearch(query) {
        const invNo = (query || searchInput.value || '').trim();
        if (!invNo) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'warning', title: 'Order Code Required', text: 'Please enter a Sales Order # or Invoice #.' });
            } else {
                alert('Please enter a Sales Order # or Invoice #.');
            }
            return;
        }

        searchInput.value = invNo;
        promptEl.classList.add('d-none');
        formContainer.classList.add('d-none');
        loadingEl.classList.remove('d-none');

        fetch(`/returns/search-invoice?invoice_no=${encodeURIComponent(invNo)}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            loadingEl.classList.add('d-none');

            if (!res.success || !res.sale) {
                promptEl.classList.remove('d-none');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Refund',
                        text: res.message || 'Invoice or Sale reference not found.'
                    });
                } else {
                    alert(res.message || 'Invoice not found.');
                }
                return;
            }

            currentSaleData = res.sale;
            renderSaleRefundForm(res.sale);
        })
        .catch(err => {
            loadingEl.classList.add('d-none');
            promptEl.classList.remove('d-none');
            console.error('Failed to search invoice:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Search Failed', text: 'An unexpected error occurred while searching for this invoice.' });
            }
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', () => performInvoiceSearch());
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                performInvoiceSearch();
            }
        });
    }

    // Render items
    function renderSaleRefundForm(sale) {
        const invRef = sale.invoice_no || sale.sale_code || 'INV';
        document.getElementById('qrInvoiceCode').textContent = '#' + (sale.sale_code || invRef);
        document.getElementById('qrHiddenInvoiceNo').value = invRef;
        document.getElementById('qrCustomerName').textContent = sale.customer ? (sale.customer.CustomerName || sale.customer.name || 'Walk-in') : 'Walk-in Customer';
        document.getElementById('qrOriginalTotal').textContent = `₱${parseFloat(sale.total_amount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.getElementById('qrCashierInfo').innerHTML = `<i class="bi bi-person me-1"></i>Cashier: <strong>${sale.cashier ? sale.cashier.name : '-'}</strong>`;
        document.getElementById('qrDateInfo').innerHTML = `<i class="bi bi-calendar3 me-1"></i>Date: ${sale.created_at ? new Date(sale.created_at).toLocaleString() : '-'}`;

        const items = sale.items || [];
        if (items.length === 0) {
            promptEl.classList.remove('d-none');
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'info', title: 'No Returnable Items', text: 'All items from this transaction have already been fully refunded.' });
            }
            return;
        }

        let rowsHtml = '';
        items.forEach((it, idx) => {
            const displayName = it.item_name || it.product_name || (it.product ? it.product.name : 'Product');
            const vName = it.variant_name || (it.variant ? it.variant.variant_name : null);
            const unitPrice = parseFloat(it.unit_price || it.price || 0);
            const returnableQty = parseFloat(it.remaining_qty ?? it.qty ?? 1);
            const prevReturned = parseFloat(it.already_returned_qty || 0);

            const vBadge = vName ? `<span class="badge fw-bold extra-small ms-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.68rem;">${vName}</span>` : '';
            const prevBadge = prevReturned > 0 ? `<div class="extra-small text-warning-emphasis font-mono"><i class="bi bi-clock-history me-1"></i>${prevReturned} prev returned</div>` : '';

            rowsHtml += `
                <tr class="qr-item-row" data-idx="${idx}">
                    <td class="ps-3 align-middle">
                        <input type="checkbox" class="qr-item-chk form-check-input mt-0" data-idx="${idx}" checked>
                    </td>
                    <td class="align-middle">
                        <div class="fw-bold text-dark lh-sm">${displayName}${vBadge}</div>
                        ${prevBadge}
                        <input type="hidden" class="qr-field-product-id" name="items[${idx}][product_id]" value="${it.product_id}">
                        ${it.variant_id ? `<input type="hidden" class="qr-field-variant-id" name="items[${idx}][variant_id]" value="${it.variant_id}">` : ''}
                    </td>
                    <td class="text-end font-mono align-middle fw-semibold text-dark">
                        ₱${unitPrice.toFixed(2)}
                    </td>
                    <td class="text-center font-mono align-middle">
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold font-mono px-2 py-0.5">${returnableQty}</span>
                    </td>
                    <td class="text-end align-middle">
                        <input type="number" 
                               step="0.01" 
                               min="0.01" 
                               max="${returnableQty}" 
                               name="items[${idx}][qty]" 
                               value="${returnableQty}" 
                               data-unit-price="${unitPrice}"
                               data-max="${returnableQty}"
                               class="form-control form-control-sm text-end font-mono fw-bold qr-item-qty" 
                               required>
                    </td>
                    <td class="text-end pe-3 font-mono fw-black text-danger align-middle qr-line-total">
                        ₱${(unitPrice * returnableQty).toFixed(2)}
                    </td>
                </tr>
            `;
        });

        itemsTbody.innerHTML = rowsHtml;
        formContainer.classList.remove('d-none');
        promptEl.classList.add('d-none');

        attachRowEvents();
        calculateRefundTotal();
    }

    function attachRowEvents() {
        document.querySelectorAll('.qr-item-chk').forEach(chk => {
            chk.addEventListener('change', () => {
                const row = chk.closest('tr');
                const qtyInput = row.querySelector('.qr-item-qty');
                if (qtyInput) {
                    qtyInput.disabled = !chk.checked;
                }
                calculateRefundTotal();
            });
        });

        document.querySelectorAll('.qr-item-qty').forEach(input => {
            input.addEventListener('input', () => {
                const val = parseFloat(input.value || 0);
                const max = parseFloat(input.dataset.max || 999999);
                const price = parseFloat(input.dataset.unitPrice || 0);
                const row = input.closest('tr');
                const lineTotalEl = row.querySelector('.qr-line-total');

                if (val > max) input.value = max;
                if (val < 0) input.value = 0;

                const finalQty = parseFloat(input.value || 0);
                if (lineTotalEl) {
                    lineTotalEl.textContent = `₱${(finalQty * price).toFixed(2)}`;
                }

                calculateRefundTotal();
            });
        });
    }

    function calculateRefundTotal() {
        let total = 0;
        document.querySelectorAll('.qr-item-row').forEach(row => {
            const chk = row.querySelector('.qr-item-chk');
            const qtyInput = row.querySelector('.qr-item-qty');
            if (chk && chk.checked && qtyInput && !qtyInput.disabled) {
                const qty = parseFloat(qtyInput.value || 0);
                const price = parseFloat(qtyInput.dataset.unitPrice || 0);
                total += (qty * price);
            }
        });

        estRefundEl.textContent = `₱${total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    // Select/Deselect All
    const btnSelectAll = document.getElementById('btnQrSelectAll');
    const btnDeselectAll = document.getElementById('btnQrDeselectAll');

    if (btnSelectAll) {
        btnSelectAll.addEventListener('click', () => {
            document.querySelectorAll('.qr-item-chk').forEach(chk => {
                chk.checked = true;
                const row = chk.closest('tr');
                const qtyInput = row.querySelector('.qr-item-qty');
                if (qtyInput) qtyInput.disabled = false;
            });
            if (chkMaster) chkMaster.checked = true;
            calculateRefundTotal();
        });
    }

    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', () => {
            document.querySelectorAll('.qr-item-chk').forEach(chk => {
                chk.checked = false;
                const row = chk.closest('tr');
                const qtyInput = row.querySelector('.qr-item-qty');
                if (qtyInput) qtyInput.disabled = true;
            });
            if (chkMaster) chkMaster.checked = false;
            calculateRefundTotal();
        });
    }

    if (chkMaster) {
        chkMaster.addEventListener('change', () => {
            const isChecked = chkMaster.checked;
            document.querySelectorAll('.qr-item-chk').forEach(chk => {
                chk.checked = isChecked;
                const row = chk.closest('tr');
                const qtyInput = row.querySelector('.qr-item-qty');
                if (qtyInput) qtyInput.disabled = !isChecked;
            });
            calculateRefundTotal();
        });
    }

    // Quick Refund Button on Current Sale (from bottom of cart)
    document.querySelectorAll('.btn-open-quick-refund-sale, #btnQuickRefundCurrentSale').forEach(btn => {
        btn.addEventListener('click', function () {
            const saleCode = this.dataset.saleCode || this.dataset.invoiceNo;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => {
                if (saleCode) {
                    performInvoiceSearch(saleCode);
                }
            }, 250);
        });
    });

    // Handle Form Submit
    if (formEl) {
        formEl.addEventListener('submit', function (e) {
            e.preventDefault();

            // Collect selected items
            const selectedItems = [];
            document.querySelectorAll('.qr-item-row').forEach(row => {
                const chk = row.querySelector('.qr-item-chk');
                const qtyInput = row.querySelector('.qr-item-qty');
                const prodInput = row.querySelector('.qr-field-product-id');
                const varInput = row.querySelector('.qr-field-variant-id');

                if (chk && chk.checked && qtyInput && !qtyInput.disabled) {
                    const qty = parseFloat(qtyInput.value || 0);
                    if (qty > 0) {
                        selectedItems.push({
                            product_id: prodInput?.value,
                            variant_id: varInput ? varInput.value : null,
                            qty: qty
                        });
                    }
                }
            });

            if (selectedItems.length === 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'No Items Selected', text: 'Please select at least 1 item with quantity to return.' });
                } else {
                    alert('Please select at least 1 item to return.');
                }
                return;
            }

            const invoiceNo = document.getElementById('qrHiddenInvoiceNo').value;
            const reason = document.getElementById('qrReasonInput').value;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Quick Refund?',
                    text: `Are you sure you want to process refund for ${selectedItems.length} item(s)? Items will be restocked and drawer payout recorded.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Process Refund',
                    cancelButtonText: 'Cancel'
                }).then(res => {
                    if (res.isConfirmed) {
                        sendRefundRequest(invoiceNo, selectedItems, reason);
                    }
                });
            } else {
                if (confirm('Process return & refund for selected items?')) {
                    sendRefundRequest(invoiceNo, selectedItems, reason);
                }
            }
        });
    }

    function sendRefundRequest(invoiceNo, items, reason) {
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing Refund...';
        }

        fetch('/returns/store-batch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                invoice_no: invoiceNo,
                items: items,
                reason: reason
            })
        })
        .then(r => r.json())
        .then(res => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle-fill fs-6"></i> <span>CONFIRM & PROCESS REFUND</span>';
            }

            if (res.success) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Refund Processed Successfully!',
                        text: res.message || 'Items have been returned, restocked to inventory, and cash drawer adjusted.',
                        confirmButtonColor: '#059669',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(res.message || 'Refund completed!');
                    window.location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Refund Failed', text: res.message || 'Could not complete the refund request.' });
                } else {
                    alert(res.message || 'Refund failed.');
                }
            }
        })
        .catch(err => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle-fill fs-6"></i> <span>CONFIRM & PROCESS REFUND</span>';
            }
            console.error('Refund submission error:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Submission Error', text: 'Network error occurred while submitting refund.' });
            } else {
                alert('Submission error.');
            }
        });
    }

    // Keyboard shortcut F7 for Quick Refund
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F7') {
            e.preventDefault();
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            setTimeout(() => {
                if (searchInput) searchInput.focus();
            }, 200);
        }
    });
});
</script>
