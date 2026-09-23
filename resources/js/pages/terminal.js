import ProductDB from './db.js';
import ProductService from './services/product.service.js';
import { Modal } from 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

window.buildBIRThermalReceiptHTML = function (sale, storeConfig = null, state = null) {
    const store = storeConfig || window.POS_STORE_CONFIG || {
        business_name: 'MINIMART POS STORE',
        owner_name: '',
        phone: '',
        address: '',
        tin: '',
        header_text: '',
        footer_text: 'THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN',
        logo: null,
        currency_symbol: '₱'
    };

    const cartItems = (sale?.items && sale.items.length > 0) ? sale.items : (state?.cart || []);
    const payments = sale?.payments || [];

    // 1. Resolve Subtotal (Gross)
    let subtotalAmount = Number(sale?.subtotal ?? (state?.subtotal !== undefined ? state.subtotal : 0));
    if (subtotalAmount <= 0 && cartItems.length > 0) {
        subtotalAmount = cartItems.reduce((s, it) => s + Number(it.subtotal || it.line_total || ((it.qty || 1) * (it.price || it.unit_price || 0))), 0);
    }

    // 2. Resolve Discount
    let discountAmount = Number(sale?.discount_amount ?? sale?.discount ?? (state?.discount !== undefined ? state.discount : 0));

    // 3. Resolve Total Amount Due
    let totalAmount = Number(sale?.total_amount ?? sale?.total ?? (state?.total !== undefined ? state.total : 0));
    if (totalAmount <= 0 && subtotalAmount > 0) {
        totalAmount = Math.max(0, subtotalAmount - discountAmount);
    }

    // 4. Resolve Tendered / Payment Amount
    let tenderedAmount = Number(
        sale?.tendered_amount ?? 
        sale?.paid ?? 
        (payments.length > 0 ? payments.reduce((s, p) => s + Number(p.amount || 0), 0) : null) ?? 
        (state?.paid !== undefined && state.paid > 0 ? state.paid : null) ?? 
        (state?.tendered !== undefined && state.tendered > 0 ? state.tendered : null) ?? 
        0
    );

    // 5. Resolve Change
    let changeAmount = Number(
        sale?.change_amount ?? 
        sale?.change ?? 
        (state?.change !== undefined ? state.change : Math.max(0, tenderedAmount - totalAmount))
    );

    const vatableSales = (totalAmount / 1.12).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const vatAmount = (totalAmount - (totalAmount / 1.12)).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const invoiceNo = sale?.sale_code || sale?.invoice_no || 'SI-' + Math.floor(100000 + Math.random() * 900000);
    const cashierName = sale?.cashier_name || sale?.cashier?.name || sale?.user?.name || store.cashier_name || 'Cashier';
    const currSym = store.currency_symbol || '₱';
    const customerName = sale?.customer_name || sale?.customer?.CustomerName || sale?.customer?.name || (typeof sale?.customer === 'string' ? sale.customer : state?.customer_name || 'Walk-in Customer');

    return `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales Receipt - ${invoiceNo}</title>
<style>
@page {
    size: 80mm 200mm;
    margin: 0;
}
body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 11px;
    color: #000;
    width: 78mm;
    margin: 0 auto;
    padding: 4mm 2mm;
    background: #fff;
    box-sizing: border-box;
}
.text-center { text-align: center; }
.text-right { text-align: right; }
.text-left { text-align: left; }
.fw-bold { font-weight: bold; }
.divider {
    border-bottom: 1px dashed #000;
    margin: 4px 0;
}
.double-divider {
    border-bottom: 3px double #000;
    margin: 4px 0;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 2px 0;
    vertical-align: top;
}
.header-title {
    font-size: 13.5px;
    font-weight: bold;
    text-transform: uppercase;
}
.footer-note {
    font-size: 9px;
    margin-top: 8px;
}
@media print {
    .no-print { display: none; }
}
</style>
</head>
<body>

<div class="text-center">
    ${store.logo ? `<img src="${store.logo}" style="max-height: 48px; max-width: 120px; object-fit: contain; margin: 0 auto 4px auto; display: block;" alt="Logo">` : ''}
    <div class="header-title">${store.business_name || 'MINIMART STORE'}</div>
    ${store.owner_name ? `<div>Prop: ${store.owner_name}</div>` : ''}
    ${store.address ? `<div>${store.address}</div>` : ''}
    ${store.phone ? `<div>Tel: ${store.phone}</div>` : ''}
    ${store.tin ? `<div>TIN: ${store.tin}</div>` : ''}
    <div class="divider"></div>
    <div class="fw-bold">SALES RECEIPT / TRANSACTION SLIP</div>
    <div>OR / SI #: ${invoiceNo}</div>
    <div>Date: ${new Date().toLocaleString()}</div>
    <div>Cashier: ${cashierName}</div>
</div>

<div class="divider"></div>

<div>Customer: ${customerName}</div>

<div class="divider"></div>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="border-bottom: 1px dashed #000;">
            <th class="text-left" style="padding-bottom: 2px;">ITEM</th>
            <th class="text-right" style="padding-bottom: 2px;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        ${cartItems.map(item => {
        const qty = Number(item.qty || 1);
        const qtyStr = (Math.floor(qty) === qty) ? qty.toFixed(0) : qty.toString();
        const unitPrice = Number(item.price || item.unit_price || 0);
        const lineTotal = Number(item.subtotal || item.line_total || (qty * unitPrice));
        const name = item.name || item.product_name || 'Item';
        const unit = item.unit ? ` ${item.unit}` : '';
        return `
            <tr>
                <td class="text-left fw-bold" style="padding-top: 3px; font-size: 11px;">${name}</td>
                <td class="text-right fw-bold" style="padding-top: 3px; font-size: 11px; white-space: nowrap;">${currSym}${lineTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="padding-bottom: 3px; font-size: 10px; color: #333;">
                    &nbsp;&nbsp;${qtyStr}${unit} @ ${currSym}${unitPrice.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                </td>
            </tr>
            `;
    }).join('')}
    </tbody>
</table>

<div class="divider"></div>

<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td class="text-left">Subtotal (Gross):</td>
        <td class="text-right">${currSym}${subtotalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
    </tr>
    ${discountAmount > 0 ? `
    <tr>
        <td class="text-left" style="color: #dc2626;">Discount:</td>
        <td class="text-right" style="color: #dc2626;">-${currSym}${discountAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
    </tr>
    ` : ''}
    <tr class="fw-bold" style="font-size: 12px;">
        <td class="text-left" style="padding-top: 2px;">TOTAL AMOUNT DUE:</td>
        <td class="text-right" style="padding-top: 2px;">${currSym}${totalAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
    </tr>
    <tr>
        <td class="text-left">Payment:</td>
        <td class="text-right">${currSym}${tenderedAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
    </tr>
    <tr>
        <td class="text-left">Change:</td>
        <td class="text-right">${currSym}${changeAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
    </tr>
</table>

<div class="divider"></div>

<div class="fw-bold text-center">TAX BREAKDOWN (12% VAT)</div>
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td class="text-left">VATable Sales (12%):</td>
        <td class="text-right">${currSym}${vatableSales}</td>
    </tr>
    <tr>
        <td class="text-left">VAT Amount (12%):</td>
        <td class="text-right">${currSym}${vatAmount}</td>
    </tr>
    <tr>
        <td class="text-left">VAT Exempt Sales:</td>
        <td class="text-right">${currSym}0.00</td>
    </tr>
    <tr>
        <td class="text-left">Zero Rated Sales:</td>
        <td class="text-right">${currSym}0.00</td>
    </tr>
</table>

<div class="double-divider"></div>

<div class="text-center footer-note">
    <div style="margin-top: 4px;" class="fw-bold">${store.footer_text || 'THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN'}</div>
    <div style="font-size: 8px; margin-top: 4px;">THIS DOCUMENT SERVES AS AN OFFICIAL SALES RECORD</div>
    <div style="font-size: 7.5px; color: #555; margin-top: 2px;">POS Software: LikhaPOS Enterprise</div>
</div>

</body>
</html>`;
};
$(function () {
    document.getElementById('btnSelectCustomer').addEventListener('click', async function () {

        const customerId = document.getElementById('customer_id').value;
        const saleId = this.dataset.sale;
        this.disabled = true;

        try {
            const response = await fetch(
                `/sales/${saleId}/customer`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content,
                    },
                    body: JSON.stringify({
                        customer_id: customerId || null,
                        sale_id: saleId,
                    }),
                }
            );

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Unable to update customer.');
            }

            if (result.success) {
                const customerName = result.customer?.name || 'Walk-in Customer';
                const cartCustomerNameEl = document.getElementById('cartCustomerName');
                if (cartCustomerNameEl) {
                    cartCustomerNameEl.innerHTML = `<i class="bi bi-person me-1"></i>${customerName}`;
                }

                const cartCustomerAddressEl = document.getElementById('cartCustomerAddress');
                if (cartCustomerAddressEl) {
                    cartCustomerAddressEl.textContent = result.customer?.address || 'Walk-in Customer';
                }

                // Sync into POS state so payment modal inline display updates
                POS.state.customer_id = result.customer?.id || null;
                POS.state.customer_name = customerName;
                POS.syncCustomerDisplay();

                const fromCheckout = POS._customerModalFromCheckout;
                POS._customerModalFromCheckout = false;

                const modalEl = document.getElementById('customerModal');
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.hide();

                if (fromCheckout) {
                    setTimeout(() => {
                        POS.openCheckout();
                    }, 300);
                }
            } else {
                renderSaleStatus(result.type, result.success, result.sale_status, result.message);
            }

        } catch (error) {

            alert(error.message);

        } finally {

            this.disabled = false;

        }

    });

    $('#btnSaveCustomer').on('click', function () {
        const $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: '/sales/customers/quick-store',
            type: 'POST',
            data: $('#newCustomerForm').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success(response) {
                if (!response.status) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(response.message || 'Unable to create customer.');
                    } else {
                        alert(response.message || 'Unable to create customer.');
                    }
                    return;
                }

                // 1. Append option to customer_id select2 element
                const $customerSelect = $('#customer_id');
                if ($customerSelect.length && response.customer) {
                    const option = new Option(response.customer.text, response.customer.id, true, true);
                    $customerSelect.append(option).trigger('change');
                }

                // 2. Hide collapse & reset form safely
                const collapseEl = document.getElementById('newCustomerCollapse');
                if (collapseEl) {
                    try {
                        const bsCollapse = bootstrap.Collapse.getInstance(collapseEl) || new bootstrap.Collapse(collapseEl, { toggle: false });
                        bsCollapse.hide();
                    } catch (e) {
                        $(collapseEl).removeClass('show');
                    }
                }

                const formEl = document.getElementById('newCustomerForm');
                if (formEl) {
                    formEl.reset();
                }

                // 3. Update cart display & POS state
                const customerName = response.customer?.text || 'Walk-in Customer';
                const cartCustomerNameEl = document.getElementById('cartCustomerName');
                if (cartCustomerNameEl) {
                    cartCustomerNameEl.innerHTML = `<i class="bi bi-person me-1"></i>${customerName}`;
                }

                const cartCustomerAddressEl = document.getElementById('cartCustomerAddress');
                if (cartCustomerAddressEl) {
                    cartCustomerAddressEl.textContent = response.customer?.text || 'Walk-in Customer';
                }

                if (typeof POS !== 'undefined') {
                    POS.state.customer_id = response.customer?.id || null;
                    POS.state.customer_name = customerName;
                    POS.syncCustomerDisplay();
                }

                if (typeof toastr !== 'undefined') {
                    toastr.success(response.message || 'Customer created successfully.');
                }

                // 4. Hide modal
                const fromCheckout = typeof POS !== 'undefined' ? POS._customerModalFromCheckout : false;
                if (typeof POS !== 'undefined') {
                    POS._customerModalFromCheckout = false;
                }

                const modalEl = document.getElementById('customerModal');
                if (modalEl) {
                    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.hide();
                }

                if (fromCheckout && typeof POS !== 'undefined' && typeof POS.openCheckout === 'function') {
                    setTimeout(() => {
                        POS.openCheckout();
                    }, 300);
                }
            },

            error(xhr) {
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.values(errors).forEach(function (messages) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error(messages[0]);
                        } else {
                            alert(messages[0]);
                        }
                    });
                } else {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'An unexpected error occurred.';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            },

            complete() {
                $btn.prop('disabled', false).html('<i class="bi bi-person-plus me-1"></i>Create & Select Customer');
            }
        });
    });

});

const POS = {

    state: {
        cart: [],
        customer_id: null,
        customer_name: null,
        saleId: document.getElementById('saleId')?.value || 0,
        priceMode: 'retail',
        viewMode: localStorage.getItem('pos_view_mode') || 'table',
        subtotal: 0,
        discount: 0,
        total: 0,
        productMap: {},
        payments: [
            {
                id: Date.now(),
                method: 'cash',
                amount: 0,
                reference_number: ''
            }
        ],

        tendered: 0,
        change: 0,

    },

    _customerModalFromCheckout: false,
    _freshCheckout: true,

    async init() {

        if (
            !document.getElementById(
                'productContainer'
            )
        ) {
            return;
        }

        this.cache();

        await ProductDB.init();

        await this.loadActivePromos();

        await this.loadProducts();

        this.bindSearch();
        this.bindCategories();
        this.bindPricingMode();
        this.bindBarcodeScanner();
        this.bindKeyboardShortcuts();
        this.bindOrderTabsEvents();
        this.bindDiscount();
        this.bindCashButtons();
        this.bindCheckout();
        this.bindSplitPayments();
        this.bindCustomerModalTransitions();
        this.bindCustomItemModal();
        this.bindViewMode();
        this.bindForceRefresh();

        if (this._isSaleAlreadyPaid) {
            const pastSaleScript = document.getElementById('pastSaleDataPayload');
            if (pastSaleScript) {
                try {
                    const pastData = JSON.parse(pastSaleScript.textContent);
                    this.state.cart = pastData.items || [];
                    this.state.subtotal = pastData.subtotal || 0;
                    this.state.discount = pastData.discount || 0;
                    this.state.total = pastData.total || 0;
                    this.state.customer_name = pastData.customer_name || 'Walk-in Customer';
                    this.state.lastPaidSale = pastData;

                    // Update customer UI
                    const custEl = document.getElementById('cartCustomerName');
                    if (custEl) custEl.innerHTML = `<i class="bi bi-person me-1"></i>${this.state.customer_name}`;

                    this.calculateTotals();
                    this.renderCart();
                    this.renderSummary();
                    this.renderLiveReceiptPreview();

                    // Attach Print Receipt event
                    const btnPrintPast = document.getElementById('btnPrintPastSaleReceipt');
                    if (btnPrintPast && !btnPrintPast.dataset.bound) {
                        btnPrintPast.dataset.bound = 'true';
                        btnPrintPast.addEventListener('click', () => {
                            this.printReceipt(pastData);
                        });
                    }

                    return;
                } catch (e) {
                    console.error('Failed to parse past sale payload:', e);
                }
            }
        }

        this.initMultiOrdersSystem();

    },
    async loadActivePromos() {
        try {
            const res = await fetch('/promotions/active-promos', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) {
                const data = await res.json();
                this.state.autoPromotions = data.promotions || [];
            }
        } catch (e) {
            console.warn('Failed to load auto promotions:', e);
            this.state.autoPromotions = [];
        }
    },
    getMatchingPromosForItem(productId, variantId = null, categoryId = null) {
        if (!this.state.autoPromotions || this.state.autoPromotions.length === 0) return [];
        const matches = [];
        for (const promo of this.state.autoPromotions) {
            let matched = false;

            if (promo.applies_to === 'all') {
                matched = true;
            } else if (promo.items && Array.isArray(promo.items)) {
                // pos_promotion_items is the strict single source of truth
                if (variantId && promo.items.some(it => it.item_type === 'variant' && Number(it.item_id) === Number(variantId))) {
                    matched = true;
                } else if (productId && promo.items.some(it => it.item_type === 'product' && Number(it.item_id) === Number(productId))) {
                    matched = true;
                } else if (categoryId && promo.items.some(it => it.item_type === 'category' && Number(it.item_id) === Number(categoryId))) {
                    matched = true;
                }
            }

            if (matched) {
                matches.push(promo);
            }
        }
        return matches;
    },
    getItemPromo(productId, variantId = null, categoryId = null) {
        const matches = this.getMatchingPromosForItem(productId, variantId, categoryId);
        return matches.length > 0 ? matches[0] : null;
    },
    getPromoDiscountedPrice(regularPrice, promo) {
        if (!promo || regularPrice <= 0) return regularPrice;
        if (promo.promo_type === 'percentage') {
            const disc = regularPrice * (parseFloat(promo.discount_value || 0) / 100);
            return Math.max(0, regularPrice - disc);
        } else if (promo.promo_type === 'fixed_amount') {
            return Math.max(0, regularPrice - parseFloat(promo.discount_value || 0));
        } else if (promo.promo_type === 'bulk_tier') {
            return parseFloat(promo.discount_value || regularPrice);
        }
        return regularPrice;
    },
    formatCurrency(amount) {

        return `₱${Number(
            amount || 0
        ).toLocaleString(
            'en-PH',
            {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }
        )}`;

    },
    renderProducts(products) {
        const container = document.getElementById('productContainer');
        if (!container) return;

        if (!products || products.length === 0) {
            container.innerHTML = `
                <div class="empty-products text-center p-5 text-muted">
                    <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary"></i>
                    <h6 class="fw-bold">No products found</h6>
                    <small>Try searching with another keyword or barcode.</small>
                </div>
            `;
            return;
        }

        const viewMode = this.state.viewMode || 'table';

        // Update view mode toggle button UI states
        const btnViewTable = document.getElementById('btnViewTable');
        const btnViewGrid = document.getElementById('btnViewGrid');
        if (btnViewTable && btnViewGrid) {
            if (viewMode === 'table') {
                btnViewTable.classList.add('active', 'bg-white', 'text-dark');
                btnViewTable.classList.remove('text-muted');
                btnViewGrid.classList.remove('active', 'bg-white', 'text-dark');
                btnViewGrid.classList.add('text-muted');
            } else {
                btnViewGrid.classList.add('active', 'bg-white', 'text-dark');
                btnViewGrid.classList.remove('text-muted');
                btnViewTable.classList.remove('active', 'bg-white', 'text-dark');
                btnViewTable.classList.add('text-muted');
            }
        }

        if (viewMode === 'table') {
            const isWholesale = this.state.priceMode === 'wholesale';
            let tableRows = '';

            products.forEach(product => {
                const hasVariants = product.variants && Array.isArray(product.variants) && product.variants.length > 0;
                const imgUrl = product.image
                    ? `/storage/${product.image}?v=${new Date(product.updated_at).getTime()}`
                    : '/images/no_image.jpg';

                if (hasVariants) {
                    // ─── PARENT HEADER ROW (non-clickable, just a label) ────
                    const totalVariantStock = product.variants.reduce((s, v) => s + Number(v.stock_on_hand || 0), 0);
                    tableRows += `
                        <tr class="product-group-header" data-group-id="${product.id}" style="background:#f9f5ff;cursor:pointer;" title="Click to collapse/expand variants">
                            <td class="align-middle py-1.5 px-2.5" style="width:44px;">
                                <img src="${imgUrl}" onerror="this.onerror=null;this.src='/images/no_image.jpg';" alt="" class="rounded-2 border shadow-xs" style="width:36px;height:36px;object-fit:cover;">
                            </td>
                            <td class="align-middle py-1.5 px-2.5" colspan="3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-black text-dark" style="font-size:0.88rem;">${product.name}</span>
                                    <span class="badge fw-bold" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.68rem;">
                                        <i class="bi bi-layers-fill me-1"></i>${product.variants.length} Variants
                                    </span>
                                    <span class="badge bg-light text-muted border font-mono" style="font-size:0.65rem;">${totalVariantStock} total stock</span>
                                </div>
                            </td>
                            <td class="align-middle text-end pe-3" style="width:80px;">
                                <i class="bi bi-chevron-up variant-chevron text-muted" style="font-size:0.75rem;" data-group="${product.id}"></i>
                            </td>
                        </tr>`;

                    // ─── VARIANT CHILD ROWS ──────────────────────────────
                    product.variants.filter(v => Number(v.stock_on_hand || 0) > 0).forEach(v => {
                        const vRetail = Number(v.selling_price || 0);
                        const vWholesale = Number(v.wholesale_price || 0);
                        const vActive = (isWholesale && vWholesale > 0) ? vWholesale : vRetail;
                        const vStock = Number(v.stock_on_hand || 0);
                        const vUnit = v.unit?.name || product.unit?.name || '';

                        const vPromo = this.getItemPromo(product.id, v.id, product.category_id);
                        let vPromoBadge = '';
                        let vPromoPrice = 0;
                        if (vPromo) {
                            vPromoPrice = this.getPromoDiscountedPrice(vActive, vPromo);
                            const label = vPromo.promo_type === 'percentage' ? `${parseFloat(vPromo.discount_value)}% OFF` : 'PROMO';
                            vPromoBadge = `<span class="badge bg-danger text-white extra-small fw-bold px-1.5 py-0.5 rounded-pill"><i class="bi bi-tag-fill me-0.5"></i>${label}</span>`;
                        }

                        let vStockBadge = '';
                        if (vStock <= 0) vStockBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-x-circle me-1"></i>Out</span>`;
                        else if (vStock <= 10) vStockBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-exclamation me-1"></i>${vStock} Left</span>`;
                        else vStockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-check-circle me-1"></i>${vStock}</span>`;

                        let vPriceDisplay = '';
                        if (vPromo && vPromoPrice < vActive) {
                            vPriceDisplay = `<div class="text-end lh-sm"><div class="font-mono fw-black text-danger" style="font-size:0.88rem;">${this.formatCurrency(vPromoPrice)}</div><small class="text-muted extra-small font-mono text-decoration-line-through">${this.formatCurrency(vActive)}</small>${isWholesale ? '<span class="badge bg-primary-subtle text-primary border ms-1 extra-small" style="font-size:0.6rem;">WS</span>' : ''}</div>`;
                        } else if (isWholesale && vWholesale > 0) {
                            vPriceDisplay = `<div class="d-flex align-items-baseline justify-content-end gap-1"><span class="font-mono fw-black text-primary" style="font-size:0.88rem;">${this.formatCurrency(vWholesale)}</span><span class="badge bg-primary-subtle text-primary border extra-small" style="font-size:0.6rem;">WS</span></div>`;
                        } else if (vWholesale > 0) {
                            vPriceDisplay = `<div class="text-end lh-sm"><div class="font-mono fw-black text-emerald" style="font-size:0.88rem;">${this.formatCurrency(vRetail)}</div><small class="text-muted extra-small font-mono" style="font-size:0.65rem;">WS: ${this.formatCurrency(vWholesale)}</small></div>`;
                        } else {
                            vPriceDisplay = `<div class="font-mono fw-black text-emerald" style="font-size:0.88rem;">${this.formatCurrency(vRetail)}</div>`;
                        }

                        tableRows += `
                            <tr class="product-variant-row"
                                data-group="${product.id}"
                                data-id="${product.id}"
                                data-variant-id="${v.id}"
                                data-name="${product.name} (${v.variant_name})"
                                data-price="${vActive}"
                                data-retail-price="${vRetail}"
                                data-wholesale-price="${vWholesale}"
                                data-stock="${vStock}"
                                data-barcode="${v.barcode || ''}"
                                data-unit="${vUnit}"
                                style="background:#fdf8ff;cursor:pointer;"
                            >
                                <td class="align-middle py-1 px-2.5" style="width:44px;">
                                    <div style="width:4px;height:32px;background:#7c3aed;border-radius:2px;margin:auto;"></div>
                                </td>
                                <td class="align-middle py-1 px-2.5">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge fw-bold d-inline-flex align-items-center gap-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.72rem;">
                                            <i class="bi bi-tag-fill"></i>${v.variant_name}
                                        </span>
                                        ${vPromoBadge}
                                        ${v.barcode ? `<span class="badge bg-light text-muted border font-mono" style="font-size:0.65rem;">${v.barcode}</span>` : ''}
                                        ${v.qty_per_pack ? `<span class="badge bg-light text-muted border extra-small" style="font-size:0.62rem;">×${v.qty_per_pack}</span>` : ''}
                                    </div>
                                </td>
                                <td class="align-middle py-1 px-2.5" style="width:130px;">${vStockBadge}</td>
                                <td class="align-middle py-1 px-2.5 text-end" style="width:130px;">${vPriceDisplay}</td>
                                <td class="align-middle py-1 px-2.5 text-center" style="width:80px;">
                                    <button type="button" class="btn-add-variant btn btn-sm btn-success rounded-3 px-2.5 py-1 extra-small font-mono fw-extrabold shadow-xs" style="background:#059669;border:none;" ${vStock <= 0 ? 'disabled' : ''}>
                                        <i class="bi bi-plus-lg me-1"></i>Add
                                    </button>
                                </td>
                            </tr>`;
                    });

                } else {
                    // ─── REGULAR PRODUCT ROW (no variants) ───────────────
                    const retailPrice = Number(product.selling_price || 0);
                    const wholesalePrice = Number(product.wholesale_price || 0);
                    const activePrice = (isWholesale && wholesalePrice > 0) ? wholesalePrice : retailPrice;
                    const stock = Number(product.stock_on_hand || 0);

                    const pPromo = this.getItemPromo(product.id, null, product.category_id);
                    let pPromoBadge = '';
                    let pPromoPrice = 0;
                    if (pPromo) {
                        pPromoPrice = this.getPromoDiscountedPrice(activePrice, pPromo);
                        const label = pPromo.promo_type === 'percentage' ? `${parseFloat(pPromo.discount_value)}% OFF` : 'PROMO';
                        pPromoBadge = `<span class="badge bg-danger text-white extra-small fw-bold px-1.5 py-0.5 rounded-pill"><i class="bi bi-tag-fill me-0.5"></i>${label}</span>`;
                    }

                    let stockBadge = '';
                    if (stock <= 0) stockBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>`;
                    else if (stock <= 10) stockBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-exclamation-circle me-1"></i>${stock} Left</span>`;
                    else stockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-check-circle me-1"></i>${stock} Available</span>`;

                    let priceDisplay = '';
                    if (pPromo && pPromoPrice < activePrice) {
                        priceDisplay = `<div class="text-end lh-sm"><div class="font-mono fw-black text-danger" style="font-size:0.92rem;">${this.formatCurrency(pPromoPrice)}</div><small class="text-muted extra-small font-mono text-decoration-line-through">${this.formatCurrency(activePrice)}</small>${isWholesale ? '<span class="badge bg-primary-subtle text-primary border ms-1 extra-small" style="font-size:0.6rem;">WS</span>' : ''}</div>`;
                    } else if (isWholesale && wholesalePrice > 0) {
                        priceDisplay = `<div class="d-flex align-items-baseline justify-content-end gap-1"><span class="font-mono fw-black text-primary" style="font-size:0.92rem;">${this.formatCurrency(wholesalePrice)}</span><span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" style="font-size:0.65rem;">WS</span></div>`;
                    } else if (wholesalePrice > 0) {
                        priceDisplay = `<div class="text-end lh-sm"><div class="font-mono fw-black text-emerald" style="font-size:0.92rem;">${this.formatCurrency(retailPrice)}</div><small class="text-muted extra-small font-mono" style="font-size:0.68rem;">WS: ${this.formatCurrency(wholesalePrice)}</small></div>`;
                    } else {
                        priceDisplay = `<div class="font-mono fw-black text-emerald" style="font-size:0.92rem;">${this.formatCurrency(retailPrice)}</div>`;
                    }

                    tableRows += `
                        <tr class="product-row cursor-pointer"
                            data-id="${product.id}"
                            data-name="${product.name}"
                            data-price="${activePrice}"
                            data-retail-price="${retailPrice}"
                            data-wholesale-price="${wholesalePrice}"
                            data-stock="${stock}"
                            data-barcode="${product.barcode ?? ''}"
                            data-category="${product.category_id ?? ''}"
                        >
                            <td class="align-middle py-1.5 px-2.5" style="width:44px;">
                                <img src="${imgUrl}" onerror="this.onerror=null;this.src='/images/no_image.jpg';" alt="" class="rounded-2 border shadow-xs" style="width:36px;height:36px;object-fit:cover;">
                            </td>
                            <td class="align-middle py-1.5 px-2.5">
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="fw-bold text-dark text-truncate" style="max-width:300px;font-size:0.88rem;">${product.name}</span>
                                    ${pPromoBadge}
                                </div>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                    ${product.barcode ? `<span class="badge bg-light text-muted border font-mono extra-small" style="font-size:0.7rem;padding:2px 6px;">${product.barcode}</span>` : ''}
                                </div>
                            </td>
                            <td class="align-middle py-1.5 px-2.5" style="width:130px;">${stockBadge}</td>
                            <td class="align-middle py-1.5 px-2.5 text-end" style="width:130px;">${priceDisplay}</td>
                            <td class="align-middle py-1.5 px-2.5 text-center" style="width:80px;">
                                <button type="button" class="btn btn-sm btn-success rounded-3 px-2.5 py-1 extra-small font-mono fw-extrabold shadow-xs" style="background:#059669;border:none;">
                                    <i class="bi bi-plus-lg me-1"></i>Add
                                </button>
                            </td>
                        </tr>`;
                }
            });

            container.innerHTML = `
                <div class="table-responsive bg-white rounded-3 border shadow-xs overflow-hidden w-100">
                    <table class="table table-hover align-middle mb-0 pos-product-table">
                        <tbody class="border-top-0">${tableRows}</tbody>
                    </table>
                </div>`;
            container.className = 'products-table-wrapper w-100 flex-grow-1 overflow-y-auto border-radius-0';

        } else {
            // ─── GRID VIEW ────────────────────────────────────────────────
            const isWholesale = this.state.priceMode === 'wholesale';
            let cardsHtml = '';

            products.forEach(product => {
                const hasVariants = product.variants && Array.isArray(product.variants) && product.variants.length > 0;
                const imgUrl = product.image
                    ? `/storage/${product.image}?v=${new Date(product.updated_at).getTime()}`
                    : '/images/no_image.jpg';

                if (hasVariants) {
                    // ─── VARIANT GROUP CARD ──────────────────────────────
                    let variantChips = product.variants.filter(v => Number(v.stock_on_hand || 0) > 0).map(v => {
                        const vRetail = Number(v.selling_price || 0);
                        const vWholesale = Number(v.wholesale_price || 0);
                        const vActive = (isWholesale && vWholesale > 0) ? vWholesale : vRetail;
                        const vStock = Number(v.stock_on_hand || 0);
                        const vUnit = v.unit?.name || product.unit?.name || '';
                        const disabled = vStock <= 0;

                        return `
                            <div class="variant-chip d-flex align-items-center justify-content-between p-2 rounded-3 border mb-1 ${disabled ? 'opacity-50' : 'cursor-pointer'}"
                                 data-id="${product.id}"
                                 data-variant-id="${v.id}"
                                 data-name="${product.name} (${v.variant_name})"
                                 data-price="${vActive}"
                                 data-retail-price="${vRetail}"
                                 data-wholesale-price="${vWholesale}"
                                 data-stock="${vStock}"
                                 data-barcode="${v.barcode || ''}"
                                 data-unit="${vUnit}"
                                 style="background:${disabled ? '#f8f9fa' : '#fdf8ff'};border-color:${disabled ? '#dee2e6' : '#e9d5ff'}!important;">
                                <div>
                                    <span class="badge fw-bold d-inline-flex align-items-center gap-1" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.68rem;">
                                        <i class="bi bi-tag-fill"></i>${v.variant_name}
                                    </span>
                                    <div class="extra-small text-muted font-mono mt-0.5" style="font-size:0.65rem;">${disabled ? 'Out of stock' : vStock + ' avail'}</div>
                                </div>
                                <div class="text-end">
                                    <div class="font-mono fw-black ${isWholesale ? 'text-primary' : 'text-success'}" style="font-size:0.82rem;">${this.formatCurrency(vActive)}</div>
                                    ${!disabled ? `<i class="bi bi-plus-circle-fill text-success" style="font-size:0.95rem;"></i>` : ''}
                                </div>
                            </div>`;
                    }).join('');

                    cardsHtml += `
                        <div class="product-group-card" style="border:2px solid #e9d5ff;border-radius:12px;background:#fff;padding:10px;">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="${imgUrl}" onerror="this.onerror=null;this.src='/images/no_image.jpg';" alt="" class="rounded-2 border" style="width:32px;height:32px;object-fit:cover;">
                                <div>
                                    <div class="fw-black text-dark text-truncate" style="font-size:0.82rem;max-width:140px;">${product.name}</div>
                                    <span class="badge fw-bold" style="background:#f3e8ff;color:#7e22ce;border:1px solid #e9d5ff;font-size:0.62rem;">
                                        <i class="bi bi-layers-fill me-1"></i>${product.variants.length} Variants
                                    </span>
                                </div>
                            </div>
                            ${variantChips}
                        </div>`;

                } else {
                    // ─── REGULAR PRODUCT CARD ────────────────────────────
                    const retailPrice = Number(product.selling_price || 0);
                    const wholesalePrice = Number(product.wholesale_price || 0);
                    const activePrice = (isWholesale && wholesalePrice > 0) ? wholesalePrice : retailPrice;
                    const stock = Number(product.stock_on_hand || 0);

                    let stockBadge = '';
                    if (stock <= 0) stockBadge = `<span class="stock-badge out">Out of Stock</span>`;
                    else if (stock <= 10) stockBadge = `<span class="stock-badge low">${stock} Left</span>`;
                    else stockBadge = `<span class="stock-badge in">${stock} Available</span>`;

                    let priceDisplay = '';
                    if (isWholesale && wholesalePrice > 0) {
                        priceDisplay = `<div class="d-flex align-items-baseline gap-1"><span class="product-price text-primary fw-bold">${this.formatCurrency(wholesalePrice)}</span><span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" style="font-size:0.65rem;">WS</span></div>`;
                    } else if (wholesalePrice > 0) {
                        priceDisplay = `<div><div class="product-price">${this.formatCurrency(retailPrice)}</div><small class="text-muted extra-small" style="font-size:0.7rem;">WS: ${this.formatCurrency(wholesalePrice)}</small></div>`;
                    } else {
                        priceDisplay = `<div class="product-price">${this.formatCurrency(retailPrice)}</div>`;
                    }

                    cardsHtml += `
                        <div class="product-card cursor-pointer"
                            data-id="${product.id}"
                            data-name="${product.name}"
                            data-price="${activePrice}"
                            data-retail-price="${retailPrice}"
                            data-wholesale-price="${wholesalePrice}"
                            data-stock="${stock}"
                            data-barcode="${product.barcode ?? ''}"
                            data-category="${product.category_id ?? ''}"
                        >
                            <div class="product-image">
                                <img src="${imgUrl}" onerror="this.onerror=null;this.src='/images/no_image.jpg';" alt="${product.name}">
                            </div>
                            <div class="product-info">
                                <div class="product-name text-truncate">${product.name}</div>
                                <div class="product-stock"><span class="stock-label"> Stock:</span>${stockBadge}</div>
                                <div class="product-bottom d-flex align-items-center justify-content-between">${priceDisplay}</div>
                            </div>
                        </div>`;
                }
            });

            container.innerHTML = cardsHtml;
            container.className = 'products-grid flex-grow-1 overflow-y-auto p-3';
        }

        this.productCards = container.querySelectorAll('.product-card, .product-row');
        this.buildProductCache();
        this.bindProductEvents();
    },
    bindProductEvents() {
        const container = document.getElementById('productContainer');
        if (!container) return;

        // ── Regular product rows (no variants) — click anywhere to add ──
        container.querySelectorAll('.product-row:not(.product-group-header)').forEach(row => {
            row.addEventListener('click', () => {
                const productId = Number(row.dataset.id);
                const product = this.products?.find(p => p.id === productId);
                const retail = Number(row.dataset.retailPrice || row.dataset.price);
                const wholesale = Number(row.dataset.wholesalePrice || 0);
                const active = (this.state.priceMode === 'wholesale' && wholesale > 0) ? wholesale : retail;
                this.addToCart({
                    id: productId, variant_id: null,
                    barcode: row.dataset.barcode, name: row.dataset.name,
                    unit: product?.unit?.name || '',
                    allow_decimal_qty: product?.allow_decimal_qty ?? false,
                    retail_price: retail, wholesale_price: wholesale, price: active,
                    cost_price: Number(product?.cost_price || 0),
                    stock: Number(row.dataset.stock || 0),
                });
            });
        });

        // ── Variant child rows — click anywhere to add that variant ─────
        container.querySelectorAll('.product-variant-row').forEach(row => {
            row.addEventListener('click', (e) => {
                if (e.target.closest('.btn-add-variant')?.disabled) return;
                const productId = Number(row.dataset.id);
                const variantId = Number(row.dataset.variantId);
                const product = this.products?.find(p => p.id === productId);
                const variant = product?.variants?.find(v => v.id === variantId);
                const retail = Number(row.dataset.retailPrice || row.dataset.price);
                const wholesale = Number(row.dataset.wholesalePrice || 0);
                const active = (this.state.priceMode === 'wholesale' && wholesale > 0) ? wholesale : retail;
                this.addToCart({
                    id: productId, variant_id: variantId,
                    barcode: row.dataset.barcode, name: row.dataset.name,
                    unit: row.dataset.unit || product?.unit?.name || '',
                    allow_decimal_qty: product?.allow_decimal_qty ?? false,
                    retail_price: retail, wholesale_price: wholesale, price: active,
                    cost_price: Number(variant?.cost_price ?? product?.cost_price ?? 0),
                    stock: Number(row.dataset.stock || 0),
                });
            });
        });

        // ── Group header rows — toggle collapse/expand variant rows ─────
        container.querySelectorAll('.product-group-header').forEach(header => {
            header.addEventListener('click', () => {
                const groupId = header.dataset.groupId;
                const rows = container.querySelectorAll(`.product-variant-row[data-group="${groupId}"]`);
                const chevron = header.querySelector('.variant-chevron');
                const isHidden = rows.length && rows[0].style.display === 'none';
                rows.forEach(r => r.style.display = isHidden ? '' : 'none');
                if (chevron) chevron.className = `bi ${isHidden ? 'bi-chevron-up' : 'bi-chevron-down'} variant-chevron text-muted`;
            });
        });

        // ── Variant chips in GRID view ───────────────────────────────────
        container.querySelectorAll('.variant-chip:not([style*="opacity"])').forEach(chip => {
            chip.addEventListener('click', () => {
                const productId = Number(chip.dataset.id);
                const variantId = Number(chip.dataset.variantId);
                const product = this.products?.find(p => p.id === productId);
                const variant = product?.variants?.find(v => v.id === variantId);
                const retail = Number(chip.dataset.retailPrice || chip.dataset.price);
                const wholesale = Number(chip.dataset.wholesalePrice || 0);
                const active = (this.state.priceMode === 'wholesale' && wholesale > 0) ? wholesale : retail;
                this.addToCart({
                    id: productId, variant_id: variantId,
                    barcode: chip.dataset.barcode, name: chip.dataset.name,
                    unit: chip.dataset.unit || '',
                    allow_decimal_qty: product?.allow_decimal_qty ?? false,
                    retail_price: retail, wholesale_price: wholesale, price: active,
                    cost_price: Number(variant?.cost_price ?? product?.cost_price ?? 0),
                    stock: Number(chip.dataset.stock || 0),
                });
            });
        });

        // ── Regular product cards (grid, no variants) ────────────────────
        container.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', () => {
                const productId = Number(card.dataset.id);
                const product = this.products?.find(p => p.id === productId);
                const retail = Number(card.dataset.retailPrice || card.dataset.price);
                const wholesale = Number(card.dataset.wholesalePrice || 0);
                const active = (this.state.priceMode === 'wholesale' && wholesale > 0) ? wholesale : retail;
                this.addToCart({
                    id: productId, variant_id: null,
                    barcode: card.dataset.barcode, name: card.dataset.name,
                    unit: product?.unit?.name || '',
                    allow_decimal_qty: product?.allow_decimal_qty ?? false,
                    retail_price: retail, wholesale_price: wholesale, price: active,
                    cost_price: Number(product?.cost_price || 0),
                    stock: Number(card.dataset.stock || 0),
                });
            });
        });
    },

    showVariantModal(product) {
        const modalEl = document.getElementById('variantModal');
        if (!modalEl) return;

        const modalTitle = document.getElementById('variantModalTitle');
        const modalSubtitle = document.getElementById('variantModalSubtitle');
        const pricingBadge = document.getElementById('variantPricingModeBadge');
        const container = document.getElementById('variantListContainer');

        const isWholesale = this.state.priceMode === 'wholesale';

        if (modalTitle) modalTitle.textContent = product.name;
        if (modalSubtitle) modalSubtitle.textContent = `Select size/pack option (${product.variants.length} available)`;
        if (pricingBadge) {
            pricingBadge.className = isWholesale
                ? 'badge bg-primary-subtle text-primary border border-primary-subtle extra-small font-mono fw-bold px-2.5 py-1 rounded-pill'
                : 'badge bg-success-subtle text-success border border-success-subtle extra-small font-mono fw-bold px-2.5 py-1 rounded-pill';
            pricingBadge.innerHTML = isWholesale
                ? '<i class="bi bi-box-seam-fill me-1"></i>Wholesale Pricing'
                : '<i class="bi bi-tag-fill me-1"></i>Retail Pricing';
        }

        let variantsList = [];

        // Base unit option
        const baseRetail = Number(product.selling_price || 0);
        const baseWholesale = Number(product.wholesale_price || 0);
        const baseActive = (isWholesale && baseWholesale > 0) ? baseWholesale : baseRetail;
        const baseUnitName = product.unit?.name || 'unit';

        variantsList.push(`
            <div class="variant-select-item p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between gap-3 shadow-xs cursor-pointer"
                 data-product-id="${product.id}"
                 data-variant-id=""
                 data-name="${product.name} (Base)"
                 data-unit="${baseUnitName}"
                 data-retail-price="${baseRetail}"
                 data-wholesale-price="${baseWholesale}"
                 data-price="${baseActive}"
                 data-barcode="${product.barcode || ''}"
                 data-stock="${product.stock_on_hand || 0}"
                 style="cursor: pointer; transition: all 0.15s ease;">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-circle bg-light border p-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                        <i class="bi bi-box text-muted fs-6"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size:0.92rem;">Base Unit (1 ${baseUnitName})</div>
                        <div class="text-muted extra-small">Stock: ${product.stock_on_hand || 0} ${baseUnitName}</div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold font-mono ${isWholesale ? 'text-primary' : 'text-success'}" style="font-size:1.05rem;">
                        ${this.formatCurrency(baseActive)}
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-0.5 extra-small fw-bold mt-1">
                        Select
                    </button>
                </div>
            </div>
        `);

        // Variants
        product.variants.forEach(v => {
            const vRetail = Number(v.selling_price || 0);
            const vWholesale = Number(v.wholesale_price || 0);
            const vActive = (isWholesale && vWholesale > 0) ? vWholesale : vRetail;
            const vStock = Number(v.stock_on_hand || product.stock_on_hand || 0);
            const vUnit = v.unit?.name || product.unit?.name || '';

            variantsList.push(`
                <div class="variant-select-item p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between gap-3 shadow-xs cursor-pointer"
                     data-product-id="${product.id}"
                     data-variant-id="${v.id}"
                     data-name="${product.name} (${v.variant_name})"
                     data-unit="${vUnit}"
                     data-retail-price="${vRetail}"
                     data-wholesale-price="${vWholesale}"
                     data-price="${vActive}"
                     data-barcode="${v.barcode || ''}"
                     data-stock="${vStock}"
                     style="cursor: pointer; transition: all 0.15s ease;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-primary-subtle border border-primary-subtle p-2 d-flex align-items-center justify-content-center text-primary" style="width:38px;height:38px;">
                            <i class="bi bi-layers-fill fs-6"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:0.92rem;">${v.variant_name}</div>
                            <div class="text-muted extra-small">
                                ${v.qty_per_pack ? `Multiplier: <strong>${v.qty_per_pack}x</strong> • ` : ''}
                                Stock: ${vStock}
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold font-mono ${isWholesale ? 'text-primary' : 'text-success'}" style="font-size:1.05rem;">
                            ${this.formatCurrency(vActive)}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-0.5 extra-small fw-bold mt-1">
                            Select
                        </button>
                    </div>
                </div>
            `);
        });

        if (container) {
            container.innerHTML = variantsList.join('');
        }

        const modal = Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Bind clicks on variant items
        container.querySelectorAll('.variant-select-item').forEach(itemEl => {
            itemEl.addEventListener('click', () => {
                modal.hide();
                const variantId = itemEl.dataset.variantId ? Number(itemEl.dataset.variantId) : null;
                const variant = product?.variants?.find(v => v.id === variantId);
                this.addToCart({
                    id: Number(itemEl.dataset.productId),
                    variant_id: variantId,
                    name: itemEl.dataset.name,
                    unit: itemEl.dataset.unit || '',
                    barcode: itemEl.dataset.barcode,
                    allow_decimal_qty: product?.allow_decimal_qty ?? false,
                    retail_price: Number(itemEl.dataset.retailPrice),
                    wholesale_price: Number(itemEl.dataset.wholesalePrice),
                    price: Number(itemEl.dataset.price),
                    cost_price: Number(variant?.cost_price ?? product?.cost_price ?? 0),
                    stock: Number(itemEl.dataset.stock || 0),
                });
            });
        });
    },
    async loadProducts() {
        let products = await ProductService.sync().catch(() => ProductService.getCached());

        this.products = products;
        this.renderProducts(products);
    },
    async refreshProducts() {

        const products =
            await ProductService.sync();

        this.products =
            products;

        this.renderProducts(
            products
        );

    },
    cache() {
        this.saleId = document.getElementById('saleId');
        this.isSalePaidInput = document.getElementById('isSalePaid');
        this._isSaleAlreadyPaid = this.isSalePaidInput?.value === '1';
        this.saleCode = document.getElementById('saleCode')?.value || '';
        if (this._isSaleAlreadyPaid) {
            this.state.isPaid = true;
        }

        this.customer_id = document.getElementById('customer_id');
        this.cartItemsList =
            document.getElementById('cartItemsList');


        this.summarySubtotal =
            document.getElementById('summarySubtotal');

        this.summaryDiscount =
            document.getElementById('summaryDiscount');

        this.summaryTotal =
            document.getElementById('summaryTotal');

        this.productCards =
            document.querySelectorAll('.product-card');

        this.categoryChips =
            document.querySelectorAll('.category-chip');

        this.searchInput =
            document.getElementById('barcodeSearch');

        this.checkoutButton =
            document.getElementById('btnCheckout');

        this.paymentModal =
            document.getElementById(
                'paymentModal'
            );

        this.paymentMethod =
            document.getElementById(
                'paymentMethod'
            );

        this.amountTendered =
            document.getElementById(
                'amountTendered'
            );

        this.paymentTotal =
            document.getElementById(
                'paymentTotal'
            );

        this.paymentChange =
            document.getElementById(
                'paymentChange'
            );

        this.btnConfirmPayment =
            document.getElementById(
                'btnConfirmPayment'
            );

        this.posStatus =
            document.getElementById(
                'posStatus'
            );

        this.statusIndicator =
            document.getElementById(
                'statusIndicator'
            );

        this.activityList =
            document.getElementById(
                'activityList'
            );

        this.referenceSection =
            document.getElementById(
                'referenceSection'
            );

        this.referenceNumber =
            document.getElementById(
                'referenceNumber'
            );

        this.cashSection =
            document.getElementById(
                'cashSection'
            );

        this.changeRow =
            document.getElementById(
                'changeRow'
            );

        this.paymentNotes =
            document.getElementById(
                'paymentNotes'
            );

        this.discountType =
            document.getElementById(
                'discountType'
            );

        this.discountMode =
            document.getElementById(
                'discountMode'
            );

        this.discountValue =
            document.getElementById(
                'discountValue'
            );

        this.manualDiscountSection =
            document.getElementById(
                'manualDiscountSection'
            );

        this.discountInfoSection =
            document.getElementById(
                'discountInfoSection'
            );

        this.discountIdNoSection =
            document.getElementById(
                'discountIdNoSection'
            );

        this.discountHolder =
            document.getElementById(
                'discountHolder'
            );

        this.discountIdNo =
            document.getElementById(
                'discountIdNo'
            );

        this.paymentNotes =
            document.getElementById(
                'paymentNotes'
            );

        this.summarySubtotalModal =
            document.getElementById(
                'summarySubtotalModal'
            );

        this.summaryDiscountModal =
            document.getElementById(
                'summaryDiscountModal'
            );

        this.paymentLines =
            document.getElementById(
                'paymentLines'
            );

        this.paymentPaid =
            document.getElementById(
                'paymentPaid'
            );

        this.paymentBalance =
            document.getElementById(
                'paymentBalance'
            );

        this.paymentBalanceSummary =
            document.getElementById(
                'paymentBalanceSummary'
            );

        this.btnAddPayment =
            document.getElementById(
                'btnAddPayment'
            );
    },
    buildProductCache() {
        this.state.productMap = {};

        if (this.products && Array.isArray(this.products)) {
            this.products.forEach(product => {
                if (product.barcode) {
                    this.state.productMap[product.barcode.toLowerCase()] = {
                        type: 'product',
                        product: product
                    };
                }

                if (product.variants && Array.isArray(product.variants)) {
                    product.variants.forEach(variant => {
                        if (variant.barcode) {
                            this.state.productMap[variant.barcode.toLowerCase()] = {
                                type: 'variant',
                                product: product,
                                variant: variant
                            };
                        }
                    });
                }
            });
        }

        // Also index DOM cards for search filtering
        this.productCards?.forEach(card => {
            const barcode = card.dataset.barcode;
            if (barcode) {
                this.state.productMap[barcode.toLowerCase()] = this.state.productMap[barcode.toLowerCase()] || {
                    type: 'card',
                    card: card
                };
            }
        });
    },
    setStatus(
        message,
        type = 'ready'
    ) {

        if (
            !this.posStatus
        ) {
            return;
        }

        this.posStatus.textContent =
            message;

        this.statusIndicator.className =
            `status-indicator ${type}`;

        clearTimeout(
            this.statusTimer
        );

        this.statusTimer =
            setTimeout(() => {

                this.posStatus.textContent =
                    'Ready';

                this.statusIndicator.className =
                    'status-indicator ready';

            }, 4000);

    },
    events() {

        // this.productCards.forEach(card => {
        //
        //     card.addEventListener(
        //         'click',
        //         () => {
        //
        //             this.addToCart({
        //                 id: Number(card.dataset.id),
        //                 barcode: card.dataset.barcode,
        //                 name: card.dataset.name,
        //                 price: Number(card.dataset.price),
        //                 stock: Number(card.dataset.stock || 0),
        //             });
        //
        //         }
        //     );
        //
        // });

        this.bindSearch();

        this.bindCategories();

        this.bindBarcodeScanner();

        this.bindPricingMode();
        this.bindKeyboardShortcuts();
        this.bindCheckout();
        this.bindDiscount();
        this.bindCashButtons();
        this.bindSplitPayments();
        this.bindForceRefresh();
        this.bindCustomerModalTransitions();
        this.bindCustomItemModal();
        this.bindViewMode();
    },
    bindCustomItemModal() {
        const modalEl = document.getElementById('customItemModal');
        if (!modalEl) return;

        const nameInput = document.getElementById('customItemName');
        const priceInput = document.getElementById('customItemPrice');
        const qtyInput = document.getElementById('customItemQty');
        const totalPreview = document.getElementById('customItemTotalPreview');
        const btnConfirm = document.getElementById('btnConfirmCustomItem');

        const updatePreview = () => {
            const price = parseFloat(priceInput?.value || 0);
            const qty = parseFloat(qtyInput?.value || 1);
            const total = Math.max(0, price * qty);
            if (totalPreview) {
                totalPreview.textContent = this.formatCurrency(total);
            }
        };

        if (priceInput) priceInput.addEventListener('input', updatePreview);
        if (qtyInput) qtyInput.addEventListener('input', updatePreview);

        // Presets buttons
        modalEl.querySelectorAll('.btn-custom-preset').forEach(btn => {
            btn.addEventListener('click', () => {
                const name = btn.dataset.name;
                const price = btn.dataset.price;
                const unit = btn.dataset.unit || 'pc';

                if (nameInput) nameInput.value = name;
                if (priceInput) priceInput.value = price;
                if (qtyInput) qtyInput.value = '1';
                modalEl.dataset.customUnit = unit;

                updatePreview();
                if (priceInput) priceInput.focus();
            });
        });

        // Quick price addition
        modalEl.querySelectorAll('.btn-custom-price-add').forEach(btn => {
            btn.addEventListener('click', () => {
                const addVal = parseFloat(btn.dataset.add || 0);
                const curVal = parseFloat(priceInput?.value || 0);
                if (priceInput) {
                    priceInput.value = (curVal + addVal).toFixed(2);
                    updatePreview();
                }
            });
        });

        modalEl.querySelector('.btn-custom-price-clear')?.addEventListener('click', () => {
            if (priceInput) {
                priceInput.value = '';
                updatePreview();
            }
        });

        // Focus when modal is shown
        modalEl.addEventListener('shown.bs.modal', () => {
            updatePreview();
            if (nameInput && !nameInput.value) {
                nameInput.focus();
            } else if (priceInput) {
                priceInput.focus();
                priceInput.select();
            }
        });

        // Submit action
        if (btnConfirm) {
            btnConfirm.addEventListener('click', () => {
                const name = (nameInput?.value || '').trim();
                const price = parseFloat(priceInput?.value || 0);
                const qty = parseFloat(qtyInput?.value || 1);
                const unit = modalEl.dataset.customUnit || 'pc';

                if (!name) {
                    alert('Please enter a charge or service name.');
                    nameInput?.focus();
                    return;
                }

                if (isNaN(price) || price < 0) {
                    alert('Please enter a valid price / charge amount.');
                    priceInput?.focus();
                    return;
                }

                this.addCustomFeeItem({
                    name: name,
                    price: price,
                    qty: qty > 0 ? qty : 1,
                    unit: unit,
                    cost_price: 0,
                    category: 'Service / Fee'
                });

                // Clear input and close modal
                if (nameInput) nameInput.value = '';
                if (priceInput) priceInput.value = '';
                if (qtyInput) qtyInput.value = '1';
                delete modalEl.dataset.customUnit;

                try {
                    const bsModal = Modal.getOrCreateInstance(modalEl);
                    if (bsModal) bsModal.hide();
                } catch (err) {
                    if (typeof $ !== 'undefined') $(modalEl).modal('hide');
                }
            });
        }

        // Enter key submission in form
        [nameInput, priceInput, qtyInput].forEach(inp => {
            inp?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    btnConfirm?.click();
                }
            });
        });
    },

    addCustomFeeItem(customData) {
        const name = (customData.name || '').trim();
        if (!name) return;

        const price = Math.max(0, parseFloat(customData.price || 0));
        const qty = Math.max(0.001, parseFloat(customData.qty || 1));
        const cost = Math.max(0, parseFloat(customData.cost_price || 0));
        const unit = (customData.unit || 'pc').trim();
        const category = customData.category || 'Service / Fee';

        const customKey = 'custom_' + Date.now();

        this.state.cart.push({
            cartKey: customKey,
            id: null,
            product_id: null,
            variant_id: null,
            barcode: null,
            name: name,
            unit: unit,
            allow_decimal_qty: false,
            retail_price: price,
            wholesale_price: price,
            cost_price: cost,
            price: price,
            stock: 999999,
            qty: qty,
            subtotal: qty * price,
            is_custom: true,
            is_service: true,
            category: category
        });

        this.calculateTotals();
        this.renderCart();
        this.renderLiveReceiptPreview();
        this.setStatus(`Added: ${name} (₱${price.toFixed(2)})`, 'ready');

        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'success',
                title: `Added "${name}" (₱${price.toFixed(2)})`
            });
        }
    },
    bindViewMode() {
        const btnViewTable = document.getElementById('btnViewTable');
        const btnViewGrid = document.getElementById('btnViewGrid');
        if (btnViewTable && btnViewGrid) {
            btnViewTable.addEventListener('click', () => {
                this.state.viewMode = 'table';
                localStorage.setItem('pos_view_mode', 'table');
                this.renderProducts(this.getFilteredProducts());
            });

            btnViewGrid.addEventListener('click', () => {
                this.state.viewMode = 'grid';
                localStorage.setItem('pos_view_mode', 'grid');
                this.renderProducts(this.getFilteredProducts());
            });
        }
    },
    bindCustomerModalTransitions() {
        document.getElementById('btnModalSelectCustomer')?.addEventListener('click', (e) => {
            e.preventDefault();
            this._customerModalFromCheckout = true;

            const payModal = Modal.getInstance(this.paymentModal);
            if (payModal) payModal.hide();

            const custModalEl = document.getElementById('customerModal');
            if (custModalEl) {
                const custModal = Modal.getOrCreateInstance(custModalEl);
                custModal.show();
            }
        });

        document.getElementById('customerModal')?.addEventListener('hidden.bs.modal', () => {
            if (this._customerModalFromCheckout) {
                this._customerModalFromCheckout = false;
                setTimeout(() => {
                    this.openCheckout();
                }, 250);
            }
        });
    },
    bindPricingMode() {
        const retailRadio = document.getElementById('priceModeRetail');
        const wholesaleRadio = document.getElementById('priceModeWholesale');

        const handleModeChange = (mode) => {
            this.state.priceMode = mode;

            // Recalculate cart items with the active mode price
            this.state.cart.forEach(item => {
                const retail = Number(item.retail_price ?? item.price);
                const wholesale = Number(item.wholesale_price ?? 0);
                item.price = (mode === 'wholesale' && wholesale > 0) ? wholesale : retail;
                item.subtotal = item.qty * item.price;
            });

            // Re-render products to update prices on grid
            if (this.products) {
                this.renderProducts(this.getFilteredProducts());
            }

            this.calculateTotals();
            this.renderCart();
            this.setStatus(`Pricing Mode: ${mode.toUpperCase()}`, 'ready');
        };

        retailRadio?.addEventListener('change', () => handleModeChange('retail'));
        wholesaleRadio?.addEventListener('change', () => handleModeChange('wholesale'));
    },
    bindForceRefresh() {

        document.addEventListener(
            'keydown',
            async e => {

                if (
                    e.shiftKey &&
                    e.key === 'F5'
                ) {

                    e.preventDefault();

                    const products =
                        await ProductService.getCached(
                            true
                        );

                    this.products =
                        products;

                    this.renderProducts(
                        products
                    );

                    alert(
                        'Products refreshed.'
                    );

                }

            }
        );

    },
    bindSplitPayments() {
        const btn = document.getElementById('btnAddPayment') || this.btnAddPayment;
        if (btn) {
            btn.onclick = (e) => {
                e.preventDefault();
                e.stopPropagation();

                this.state.payments.push({
                    id: Date.now(),
                    method: 'cash',
                    amount: '',
                    reference_number: ''
                });

                this.renderPaymentLines();
                this.calculatePayments();

                setTimeout(() => {
                    const inputs = document.querySelectorAll('.payment-amount');
                    if (inputs.length) {
                        const last = inputs[inputs.length - 1];
                        last.focus();
                    }
                }, 50);
            };
        }
    },
    renderPaymentLines() {

        if (!this.paymentLines) {
            return;
        }

        const isMulti = this.state.payments.length > 1;

        this.paymentLines.innerHTML =
            this.state.payments
                .map((payment, index) => `
<div class="payment-row p-3.5 rounded-4 border bg-light bg-opacity-75 shadow-xs mb-3" data-index="${index}">
    <div class="row g-3 align-items-center">
        <div class="${isMulti ? 'col-4' : 'col-4'}">
            <label class="form-label extra-small fw-extrabold text-muted text-uppercase mb-1" style="font-size:0.7rem;letter-spacing:0.5px;">Payment Method</label>
            <select class="form-select payment-method fw-bold shadow-xs py-2 px-2.5" data-index="${index}" style="font-size:0.92rem;border-radius:10px;">
                <option value="cash" ${payment.method === 'cash' ? 'selected' : ''}>💵 Cash</option>
                <option value="gcash" ${payment.method === 'gcash' ? 'selected' : ''}>📱 GCash</option>
                <option value="bank_transfer" ${payment.method === 'bank_transfer' ? 'selected' : ''}>🏦 Bank Transfer</option>
                <option value="utang" ${payment.method === 'utang' ? 'selected' : ''}>📋 Utang (Credit)</option>
            </select>
        </div>

        <div class="${isMulti ? 'col-4' : 'col-4'}">
            <label class="form-label extra-small fw-extrabold text-muted text-uppercase mb-1" style="font-size:0.7rem;letter-spacing:0.5px;">Amount Tendered</label>
            <div class="input-group shadow-xs" style="border-radius:10px;overflow:hidden;">
                <span class="input-group-text font-mono fw-black bg-white text-muted px-2.5 fs-5">₱</span>
                <input
                    type="number"
                    step="0.01"
                    class="form-control font-mono fw-black payment-amount text-dark fs-4 py-2 px-2.5"
                    data-index="${index}"
                    value="${payment.amount || ''}"
                    placeholder="0.00"
                    style="letter-spacing:-0.5px;"
                >
            </div>
        </div>

        <div class="${isMulti ? 'col-3' : 'col-4'}">
            <label class="form-label extra-small fw-extrabold text-muted text-uppercase mb-1" style="font-size:0.7rem;letter-spacing:0.5px;">Reference / Note</label>
            <input
                type="text"
                class="form-control font-mono payment-reference shadow-xs py-2 px-2.5"
                data-index="${index}"
                value="${payment.reference_number || ''}"
                placeholder="Ref / Trace #"
                style="font-size:0.88rem;border-radius:10px;"
            >
        </div>

        ${isMulti ? `
        <div class="col-1 text-end pt-3">
            <button type="button" class="btn btn-sm text-danger remove-payment p-1.5 border-0 rounded-circle" data-index="${index}" title="Remove payment line">
                <i class="bi bi-trash3-fill fs-5"></i>
            </button>
        </div>
        ` : ''}
    </div>
</div>
`)
                .join('');

        this.attachPaymentEvents();

    },
    attachPaymentEvents() {

        document
            .querySelectorAll(
                '.payment-method'
            )
            .forEach(element => {

                element.addEventListener(
                    'change',
                    e => {

                        const index =
                            Number(
                                e.target.dataset.index
                            );

                        this.state.payments[index].method =
                            e.target.value;

                        // Refresh utang warning banner
                        this.syncCustomerDisplay();

                    }
                );

            });

        document
            .querySelectorAll(
                '.payment-amount'
            )
            .forEach(element => {

                element.addEventListener(
                    'input',
                    e => {

                        const index =
                            Number(
                                e.target.dataset.index
                            );

                        this.state.payments[index].amount =
                            Number(
                                e.target.value || 0
                            );

                        this.calculatePayments();

                    }
                );

            });

        document
            .querySelectorAll(
                '.payment-reference'
            )
            .forEach(element => {

                element.addEventListener(
                    'input',
                    e => {

                        const index =
                            Number(
                                e.target.dataset.index
                            );

                        this.state.payments[index].reference_number =
                            e.target.value;

                    }
                );

            });

        document
            .querySelectorAll(
                '.remove-payment'
            )
            .forEach(element => {

                element.addEventListener(
                    'click',
                    e => {

                        const index =
                            Number(
                                e.target.dataset.index
                            );

                        this.state.payments.splice(
                            index,
                            1
                        );

                        this.renderPaymentLines();

                        this.calculatePayments();

                    }
                );

            });

    },
    calculatePayments() {

        const paid =
            this.state.payments.reduce(
                (sum, payment) =>
                    sum +
                    Number(
                        payment.amount || 0
                    ),
                0
            );

        this.state.paid =
            paid;

        this.state.balance =
            Math.max(
                0,
                this.state.total - paid
            );

        this.state.change =
            Math.max(
                0,
                paid - this.state.total
            );

        this.paymentPaid.textContent =
            this.formatCurrency(
                paid
            );

        this.paymentBalance.textContent =
            this.formatCurrency(
                this.state.balance
            );

        if (this.paymentBalanceSummary) {
            this.paymentBalanceSummary.textContent =
                this.formatCurrency(
                    this.state.balance
                );
        }

        const tenderModalTotal = document.getElementById('tenderModalTotal');
        const tenderModalBalance = document.getElementById('tenderModalBalance');
        if (tenderModalTotal) {
            tenderModalTotal.textContent = this.formatCurrency(this.state.total);
        }
        if (tenderModalBalance) {
            tenderModalBalance.textContent = this.formatCurrency(this.state.balance);
        }

        this.paymentChange.textContent =
            this.formatCurrency(
                this.state.change
            );

        this.updateCheckoutReceiptPreview();
    },
    bindDiscount() {

        if (
            !this.discountType
        ) {
            return;
        }

        const refreshDiscount =
            () => {

                const type =
                    this.discountType.value;

                this.manualDiscountSection
                    ?.classList.add(
                        'd-none'
                    );

                this.discountInfoSection
                    ?.classList.add(
                        'd-none'
                    );

                this.discountIdNoSection
                    ?.classList.add(
                        'd-none'
                    );

                if (
                    type === 'manual'
                ) {

                    this.manualDiscountSection
                        ?.classList.remove(
                            'd-none'
                        );

                }

                if (
                    [
                        'senior',
                        'pwd',
                        'student',
                        'employee'
                    ].includes(
                        type
                    )
                ) {

                    this.discountInfoSection
                        ?.classList.remove(
                            'd-none'
                        );

                    this.discountIdNoSection
                        ?.classList.remove(
                            'd-none'
                        );

                    if (this.discountHolder && !this.discountHolder.value.trim() && this.state.customer_name && this.state.customer_name !== 'Walk-in Customer') {
                        this.discountHolder.value = this.state.customer_name;
                    }

                }

                if (type === 'promo' && this.state.appliedPromo) {
                    // Promo discount active
                }

                this.calculateTotals();

                this.calculatePayments();

                this.renderSummary();

            };

        this.discountType.addEventListener(
            'change',
            refreshDiscount
        );

        this.discountMode?.addEventListener(
            'change',
            refreshDiscount
        );

        this.discountValue?.addEventListener(
            'input',
            refreshDiscount
        );

        // Quick Preset Buttons (Senior, PWD, Student, Clear)
        document.querySelectorAll('.btn-quick-discount').forEach(btn => {
            btn.addEventListener('click', () => {
                const discountModeType = btn.dataset.type;
                if (discountModeType === 'clear') {
                    this.discountType.value = '';
                    this.state.appliedPromo = null;
                    const feedback = document.getElementById('promoVoucherFeedback');
                    if (feedback) {
                        feedback.style.display = 'none';
                        feedback.innerHTML = '';
                    }
                    const codeInp = document.getElementById('inpPromoVoucherCode');
                    if (codeInp) codeInp.value = '';
                } else {
                    this.discountType.value = discountModeType;
                }
                refreshDiscount();

                if (discountModeType === 'senior' || discountModeType === 'pwd') {
                    setTimeout(() => {
                        this.discountIdNo?.focus();
                    }, 150);
                }
            });
        });

        // Promo Voucher Validator
        const applyPromoBtn = document.getElementById('btnApplyPromoVoucher');
        const promoCodeInput = document.getElementById('inpPromoVoucherCode');
        const promoFeedback = document.getElementById('promoVoucherFeedback');

        const handleApplyPromo = async () => {
            const code = (promoCodeInput?.value || '').trim();
            if (!code) {
                if (promoFeedback) {
                    promoFeedback.className = 'extra-small text-danger mt-1.5 font-mono';
                    promoFeedback.innerHTML = '<i class="bi bi-exclamation-circle me-1"></i>Please enter a promo code.';
                    promoFeedback.style.display = 'block';
                }
                return;
            }

            if (applyPromoBtn) {
                applyPromoBtn.disabled = true;
                applyPromoBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            }

            try {
                const res = await fetch('/promotions/validate-promo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        code: code,
                        subtotal: this.state.subtotal,
                        items: this.state.cart
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.state.appliedPromo = data;
                    this.discountType.value = 'promo';
                    if (promoFeedback) {
                        promoFeedback.className = 'extra-small text-success mt-1.5 font-mono fw-bold';
                        promoFeedback.innerHTML = `<i class="bi bi-check-circle-fill me-1"></i>${data.message} (-₱${parseFloat(data.discount_amount).toFixed(2)})`;
                        promoFeedback.style.display = 'block';
                    }
                    refreshDiscount();
                } else {
                    this.state.appliedPromo = null;
                    if (promoFeedback) {
                        promoFeedback.className = 'extra-small text-danger mt-1.5 font-mono';
                        promoFeedback.innerHTML = `<i class="bi bi-x-circle-fill me-1"></i>${data.message || 'Invalid promo code.'}`;
                        promoFeedback.style.display = 'block';
                    }
                }
            } catch (err) {
                console.error('Promo validation error:', err);
                if (promoFeedback) {
                    promoFeedback.className = 'extra-small text-danger mt-1.5 font-mono';
                    promoFeedback.innerHTML = '<i class="bi bi-x-circle-fill me-1"></i>Failed to validate promo code.';
                    promoFeedback.style.display = 'block';
                }
            } finally {
                if (applyPromoBtn) {
                    applyPromoBtn.disabled = false;
                    applyPromoBtn.innerHTML = 'Apply';
                }
            }
        };

        applyPromoBtn?.addEventListener('click', handleApplyPromo);
        promoCodeInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleApplyPromo();
            }
        });

    },
    bindCashButtons() {

        document.querySelectorAll('.quick-tender-exact').forEach(btn => {
            btn.addEventListener('click', () => {
                if (!this.state.payments.length) {
                    this.state.payments.push({
                        id: Date.now(),
                        method: 'cash',
                        amount: 0,
                        reference_number: ''
                    });
                }
                this.state.payments[0].amount = this.state.total;
                this.renderPaymentLines();
                this.calculatePayments();
            });
        });

        document.querySelectorAll('.quick-tender-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const val = parseFloat(btn.dataset.val) || 0;
                if (!this.state.payments.length) {
                    this.state.payments.push({
                        id: Date.now(),
                        method: 'cash',
                        amount: 0,
                        reference_number: ''
                    });
                }
                this.state.payments[0].amount = val;
                this.renderPaymentLines();
                this.calculatePayments();
            });
        });

        document
            .querySelectorAll(
                '.cash-btn'
            )
            .forEach(
                button => {

                    button.addEventListener(
                        'click',
                        () => {

                            const amount =
                                Number(
                                    button.dataset.value
                                );

                            if (this.amountTendered) {
                                this.amountTendered.value =
                                    amount;

                                this.calculateChange();
                            }

                        }
                    );

                }
            );

    },
    bindCheckout() {

        if (
            this.checkoutButton
        ) {

            this.checkoutButton.addEventListener(
                'click',
                () => this.openCheckout()
            );

        }

        if (
            this.amountTendered
        ) {

            this.amountTendered.addEventListener(
                'input',
                () => this.calculateChange()
            );

        }

        if (
            this.btnConfirmPayment
        ) {

            this.btnConfirmPayment.addEventListener(
                'click',
                () => this.completeSale()
            );

        }

        if (
            this.paymentMethod
        ) {

            this.paymentMethod.addEventListener(
                'change',
                () => {

                    const isCash =
                        this.paymentMethod.value === 'cash';

                    this.cashSection?.classList.toggle(
                        'd-none',
                        !isCash
                    );

                    this.referenceSection?.classList.toggle(
                        'd-none',
                        isCash
                    );

                    this.changeRow?.classList.toggle(
                        'd-none',
                        !isCash
                    );

                    if (!isCash) {

                        this.state.tendered =
                            this.state.total;

                        this.state.change = 0;

                        this.paymentChange.textContent =
                            '₱0.00';

                    }

                }
            );

        }

        // Global POS Keyboard Shortcuts (F4: Complete Sale / Checkout, F2: Barcode Search, Enter: Submit Payment)
        document.addEventListener('keydown', (e) => {
            // F4 Key: Open Payment Modal or Complete Sale
            if (e.key === 'F4') {
                e.preventDefault();
                e.stopPropagation();

                const paymentModalEl = document.getElementById('paymentModal');
                const isPaymentModalOpen = paymentModalEl && paymentModalEl.classList.contains('show');

                if (isPaymentModalOpen) {
                    if (this.btnConfirmPayment && !this.btnConfirmPayment.disabled) {
                        this.btnConfirmPayment.click();
                    }
                } else {
                    if (this.btnCheckout && !this.btnCheckout.disabled) {
                        this.btnCheckout.click();
                    }
                }
                return;
            }

            // Enter Key: Submit payment when payment modal is active
            if (e.key === 'Enter') {
                const paymentModalEl = document.getElementById('paymentModal');
                const isPaymentModalOpen = paymentModalEl && paymentModalEl.classList.contains('show');

                if (isPaymentModalOpen) {
                    const tag = e.target ? e.target.tagName.toLowerCase() : '';
                    if (tag === 'textarea') {
                        return;
                    }
                    e.preventDefault();
                    if (this.btnConfirmPayment && !this.btnConfirmPayment.disabled) {
                        this.btnConfirmPayment.click();
                    }
                    return;
                }
            }

            // F2 Key: Focus Barcode / Product Search input
            if (e.key === 'F2') {
                e.preventDefault();
                const barcodeInput = document.getElementById('barcodeSearch');
                if (barcodeInput) {
                    barcodeInput.focus();
                    if (typeof barcodeInput.select === 'function') {
                        barcodeInput.select();
                    }
                }
                return;
            }
        });

        const paymentModalEl = document.getElementById('paymentModal');
        if (paymentModalEl) {
            paymentModalEl.addEventListener('shown.bs.modal', () => {
                this.updateCheckoutReceiptPreview();
                this.updateDiscountProfitPreview();
            });
        }

        const tenderSubModal = document.getElementById('checkoutTenderModal');
        if (tenderSubModal) {
            tenderSubModal.addEventListener('hidden.bs.modal', () => {
                const mainModalEl = document.getElementById('paymentModal');
                if (mainModalEl) {
                    const bsMainModal = bootstrap.Modal.getOrCreateInstance(mainModalEl);
                    bsMainModal.show();
                }
            });
        }

        const discountSubModal = document.getElementById('checkoutDiscountModal');
        if (discountSubModal) {
            discountSubModal.addEventListener('shown.bs.modal', () => {
                this.updateDiscountProfitPreview();
            });
            discountSubModal.addEventListener('hidden.bs.modal', () => {
                const mainModalEl = document.getElementById('paymentModal');
                if (mainModalEl) {
                    const bsMainModal = bootstrap.Modal.getOrCreateInstance(mainModalEl);
                    bsMainModal.show();
                }
            });
        }
    },
    async completeSale() {
        if (!this.validateCheckout()) {
            return;
        }

        try {
            this.btnConfirmPayment.disabled = true;
            const payload = this.buildPayload();
            const response = await fetch('/sales/complete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .content,
                },
                body: JSON.stringify(payload),
            });

            const result = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(result.message || `Request failed (${response.status}).`);
            }

            if (result.success) {
                const modal = bootstrap.Modal.getOrCreateInstance(this.paymentModal);
                modal.hide();
                await this.updateCachedStocks();

                const paidSaleData = {
                    sale_code: result.sale?.sale_code || result.sale_code || `#${this.saleCode || '260824-0001'}`,
                    invoice_no: result.sale?.invoice_no || result.invoice_no,
                    cashier_name: window.POS_STORE_CONFIG?.cashier_name || 'Cashier',
                    customer: this.state.customer,
                    items: [...this.state.cart],
                    subtotal: this.state.subtotal,
                    discount_amount: this.state.discount,
                    total_amount: this.state.total,
                    tendered_amount: this.state.paid || this.state.tendered || this.state.total,
                    change_amount: this.state.change || 0,
                    payments: this.state.payments
                };

                this.state.lastPaidSale = paidSaleData;
                this.state.isPaid = true;

                this.printReceipt(paidSaleData);
                this.reset();
                this.state.isPaid = true;
                this.state.lastPaidSale = paidSaleData;
                this.renderLiveReceiptPreview();

                // Automatically switch terminal state to the new transaction!
                if (result.next_sale_id) {
                    this.state.saleId = result.next_sale_id;
                    const saleIdInput = document.getElementById('saleId');
                    if (saleIdInput) {
                        saleIdInput.value = result.next_sale_id;
                    }
                    if (result.next_sale_url && window.history.replaceState) {
                        window.history.replaceState(null, '', result.next_sale_url);
                    }
                }
            } else {
                renderSaleStatus(result.type, result.success, result.sale_status, result.message);
            }


        } catch (error) {
            console.error(error);
            alert(error.message || 'Checkout failed.');

        } finally {
            this.btnConfirmPayment.disabled = false;
        }

    },
    async updateCachedStocks() {
        const products = await ProductDB.getProducts();

        const updatedProducts = products.map(product => {
            const soldItems = this.state.cart.filter(item => item.id === product.id);
            if (!soldItems || soldItems.length === 0) {
                return product;
            }

            let mainStock = Number(product.stock_on_hand || 0);

            const updatedVariants = product.variants ? product.variants.map(v => {
                const soldVar = soldItems.find(item => Number(item.variant_id) === Number(v.id));
                if (soldVar) {
                    const vStock = Number(v.stock_on_hand || 0);
                    return { ...v, stock_on_hand: Math.max(0, vStock - Number(soldVar.qty || 0)) };
                }
                return v;
            }) : product.variants;

            soldItems.forEach(item => {
                const factor = item.variant_id ? (product.variants?.find(v => Number(v.id) === Number(item.variant_id))?.qty_per_pack || 1) : 1;
                if (mainStock > 0) {
                    mainStock = Math.max(0, mainStock - (Number(item.qty || 0) * factor));
                }
            });

            return {
                ...product,
                stock_on_hand: mainStock,
                variants: updatedVariants
            };
        });

        await ProductDB.saveProducts(updatedProducts);
        this.products = updatedProducts;
        this.renderProducts(this.getFilteredProducts());
    },
    reset() {
        // Remove current completed order from active tabs
        if (this.state.orders && this.state.orders.length > 0) {
            this.state.orders = this.state.orders.filter(o => o.id !== this.state.activeOrderId);
        }

        if (!this.state.orders || this.state.orders.length === 0) {
            const newOrder = {
                id: 'ORDER_' + Date.now(),
                label: 'Sale 1',
                customer_name: 'Walk-in Customer',
                customer_id: null,
                customer: null,
                cart: [],
                priceMode: 'retail',
                subtotal: 0,
                discount: 0,
                total: 0
            };
            this.state.orders = [newOrder];
            this.state.activeOrderId = newOrder.id;
        } else {
            this.state.activeOrderId = this.state.orders[0].id;
        }

        this.loadOrderIntoCurrentState(this.state.activeOrderId);
        this.saveCurrentOrderToStorage();

        this.state.paid = 0;
        this.state.balance = 0;
        this.state.change = 0;
        this.state.payments = [];
        this._freshCheckout = true;

        if (this.paymentLines) this.paymentLines.innerHTML = '';
        if (this.discountType) this.discountType.value = '';
        if (this.discountMode) this.discountMode.value = 'percentage';
        if (this.discountValue) this.discountValue.value = '';
        if (this.discountHolder) this.discountHolder.value = '';
        if (this.discountIdNo) this.discountIdNo.value = '';
        if (this.paymentNotes) this.paymentNotes.value = '';

        this.searchInput.value = '';
        this.searchInput.focus();
    },

    printReceipt(sale) {
        const html = window.buildBIRThermalReceiptHTML(sale, window.POS_STORE_CONFIG, this.state);
        const receipt = window.open('', '_blank', 'width=380,height=900');
        if (receipt) {
            receipt.document.write(html + `<script>window.onload = () => { window.print(); setTimeout(() => window.close(), 600); };</script>`);
            receipt.document.close();
        }
    },
    calculateChange() {

        if (
            !this.amountTendered ||
            !this.paymentChange
        ) {
            return;
        }

        const tendered =
            parseFloat(
                this.amountTendered.value
            ) || 0;

        const total =
            Number(
                this.state.total || 0
            );

        const difference =
            Math.round(
                (
                    tendered -
                    total
                ) * 100
            ) / 100;

        this.state.tendered =
            tendered;

        this.state.change =
            difference > 0
                ? difference
                : 0;

        this.state.balance =
            difference < 0
                ? Math.abs(
                    difference
                )
                : 0;

        this.paymentChange.textContent =
            `₱${this.state.change.toLocaleString(
                'en-PH',
                {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }
            )}`;

        this.updateCheckoutReceiptPreview();
    },

    updateCheckoutReceiptPreview() {
        const frame = document.getElementById('checkoutReceiptPreviewFrame');
        const tenderModalTotal = document.getElementById('tenderModalTotal');
        const tenderModalBalance = document.getElementById('tenderModalBalance');

        const totalFormatted = `₱${Number(this.state.total || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        const balanceFormatted = `₱${Number(this.state.balance || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        if (tenderModalTotal) tenderModalTotal.textContent = totalFormatted;
        if (tenderModalBalance) tenderModalBalance.textContent = balanceFormatted;

        if (!frame) return;

        const saleData = {
            sale_code: `#${this.saleCode || '260823-0030'}`,
            cashier_name: window.POS_STORE_CONFIG?.cashier_name || 'Cashier',
            items: this.state.cart || [],
            subtotal: this.state.subtotal || 0,
            discount_amount: this.state.discount || 0,
            total_amount: this.state.total || 0,
            tendered_amount: this.state.paid || this.state.tendered || 0,
            change_amount: this.state.change || 0,
            payments: this.getPaymentLinesData ? this.getPaymentLinesData() : []
        };

        if (typeof window.buildBIRThermalReceiptHTML === 'function') {
            const html = window.buildBIRThermalReceiptHTML(saleData, window.POS_STORE_CONFIG, this.state);
            const doc = frame.contentWindow ? frame.contentWindow.document : (frame.contentDocument || frame.document);
            if (doc) {
                doc.open();
                doc.write(html);
                doc.close();
            }
        }
    },

    validateCheckout() {
        if (this.state.cart.length === 0) {
            if (typeof window.appAlert === 'function') {
                window.appAlert({ title: 'Cart Empty', text: 'Please add items to cart before proceeding.', type: 'warning' });
            } else {
                alert('Cart is empty.');
            }
            return false;
        }

        const discountType = this.discountType?.value;

        if (['senior', 'pwd', 'student', 'employee'].includes(discountType)) {
            if (!this.discountHolder.value.trim()) {
                if (typeof window.appAlert === 'function') {
                    window.appAlert({ title: 'ID Name Required', text: 'Please enter cardholder name as shown on ID.', type: 'warning' });
                } else {
                    alert('Customer name is required.');
                }
                return false;
            }

            if (!this.discountIdNo.value.trim()) {
                if (typeof window.appAlert === 'function') {
                    window.appAlert({ title: 'ID Number Required', text: 'Please enter ID number for special discount.', type: 'warning' });
                } else {
                    alert('ID number is required.');
                }
            }
        }
        if (!this.state.payments || this.state.payments.length === 0) {
            if (typeof window.appAlert === 'function') {
                window.appAlert({ title: 'No Payment Tendered', text: 'Please enter at least one payment method.', type: 'warning' });
            } else {
                alert('No payment entered.');
            }
            return false;
        }

        if (
            this.state.balance > 0
        ) {


            renderSaleStatus('error', 'WATCH OUT!', 'Insufficient payment.', 'The total amount paid is less than the total amount due.');
            return false;

        }

        for (
            const payment of this.state.payments
        ) {

            if (
                Number(
                    payment.amount || 0
                ) <= 0
            ) {

                alert(
                    'Payment amount is required.'
                );

                return false;

            }

            // Utang requires a customer, not a reference number
            if (payment.method === 'utang' && !this.state.customer_id) {

                alert(
                    'A customer must be selected for Utang (credit) payments. Please use the "Change" button to assign a customer.'
                );

                return false;

            }

            if (
                payment.method !== 'cash' &&
                payment.method !== 'utang' &&
                !String(
                    payment.reference_number || ''
                ).trim()
            ) {

                alert(
                    'Reference number is required.'
                );

                return false;

            }

        }

        return true;

    },
    buildPayload() {
        return {
            sale_id: this.state.saleId,
            customer_id: this.state.customer_id,
            subtotal: this.state.subtotal,
            discount: this.state.discount,
            total: this.state.total,
            discount_type: this.discountType?.value || null,
            discount_mode: this.discountMode?.value || null,
            discount_value: this.discountValue?.value || null,
            discount_holder: this.discountHolder?.value?.trim() || null,
            discount_id_no: this.discountIdNo?.value?.trim() || null,
            discount_reference: this.discountIdNo?.value?.trim() || (this.state.appliedPromo?.promo_code || null),
            promo_id: this.state.appliedPromo?.promo_id || null,
            notes: this.paymentNotes?.value?.trim() || null,
            payments: this.state.payments,
            paid: this.state.paid,
            change: this.state.change,
            items: this.state.cart.map(item => {
                const matchingPromos = typeof this.getMatchingPromosForItem === 'function' ? this.getMatchingPromosForItem(item.id, item.variant_id, item.category_id) : [];
                let activePromo = null;
                if (matchingPromos.length > 1) {
                    activePromo = (item.selectedPromoId && item.selectedPromoId !== 'none') ? this.state.autoPromotions.find(p => p.id === Number(item.selectedPromoId)) : null;
                } else if (matchingPromos.length === 1) {
                    activePromo = (item.selectedPromoId !== 'none') ? matchingPromos[0] : null;
                }

                let itemDiscount = 0;
                let effectiveUnitPrice = item.price;
                if (activePromo) {
                    const isMinSpendMet = !(activePromo.min_spend > 0 && this.state.subtotal < activePromo.min_spend);
                    const isMinQtyMet = item.qty >= (activePromo.min_quantity || 1);
                    if (isMinSpendMet && isMinQtyMet) {
                        effectiveUnitPrice = typeof this.getPromoDiscountedPrice === 'function' ? this.getPromoDiscountedPrice(item.price, activePromo) : item.price;
                        if (activePromo.promo_type === 'percentage') {
                            itemDiscount = (item.subtotal * (parseFloat(activePromo.discount_value || 0) / 100));
                        } else if (activePromo.promo_type === 'fixed_amount') {
                            itemDiscount = Math.min(parseFloat(activePromo.discount_value || 0), item.subtotal);
                        } else if (activePromo.promo_type === 'bulk_tier') {
                            const specialPrice = parseFloat(activePromo.discount_value || 0);
                            if (specialPrice < item.price) {
                                itemDiscount = (item.price - specialPrice) * item.qty;
                            }
                        }
                    }
                }

                const lineTotal = Math.max(0, (item.subtotal || (item.qty * item.price)) - itemDiscount);

                return {
                    product_id: item.id || null,
                    variant_id: item.variant_id || null,
                    name: item.name,
                    product_name: item.name,
                    is_custom: !!item.is_custom,
                    qty: item.qty,
                    price: item.price,
                    original_price: item.price,
                    effective_price: effectiveUnitPrice,
                    discount_amount: itemDiscount,
                    promo_id: activePromo ? activePromo.id : null,
                    subtotal: lineTotal
                };
            }),
        };
    },
    openCheckout() {

        if (!this.state.cart || this.state.cart.length === 0) {
            if (typeof window.appAlert === 'function') {
                window.appAlert({
                    title: 'Cart is Empty',
                    text: 'Please select items to add to cart before opening checkout.',
                    type: 'warning'
                });
            } else {
                alert('Cart is empty.');
            }
            return;
        }

        // Only reset discount and payment lines if this is a genuinely fresh checkout!
        if (this._freshCheckout) {

            if (this.discountType) this.discountType.value = '';
            if (this.discountMode) this.discountMode.value = 'percentage';
            if (this.discountValue) this.discountValue.value = '';
            if (this.discountHolder) this.discountHolder.value = '';
            if (this.discountIdNo) this.discountIdNo.value = '';
            if (this.paymentNotes) this.paymentNotes.value = '';

            this.manualDiscountSection?.classList.add('d-none');
            this.discountInfoSection?.classList.add('d-none');
            this.discountIdNoSection?.classList.add('d-none');

            this.state.payments = [
                {
                    id: Date.now(),
                    method: 'cash',
                    amount: 0,
                    reference_number: ''
                }
            ];

            this.state.paid = 0;
            this.state.balance = this.state.total;
            this.state.change = 0;

        } else {
            // Restore visibility of discount sections if a discount was already selected!
            const type = this.discountType?.value;
            if (type === 'manual') {
                this.manualDiscountSection?.classList.remove('d-none');
            } else if (['senior', 'pwd', 'student', 'employee'].includes(type)) {
                this.discountInfoSection?.classList.remove('d-none');
                this.discountIdNoSection?.classList.remove('d-none');
            }
        }

        this._freshCheckout = false;

        this.calculateTotals();
        this.renderPaymentLines();
        this.calculatePayments();
        this.syncCustomerDisplay();
        this.renderSummary();

        if (this.paymentTotal) {
            this.paymentTotal.textContent = this.formatCurrency(this.state.total);
        }

        const modal = Modal.getOrCreateInstance(
            this.paymentModal
        );

        modal.show();

        setTimeout(() => {

            document
                .querySelector(
                    '.payment-amount'
                )
                ?.focus();

        }, 200);

    },

    syncCustomerDisplay() {

        const nameEl = document.getElementById('modalCustomerName');
        const badgeEl = document.getElementById('modalCustomerBadge');
        const noCustomerEl = document.getElementById('modalNoCustomer');
        const utangWarningEl = document.getElementById('utangNoCustomerWarning');

        const hasCustomer = !!this.state.customer_id;
        const name = this.state.customer_name || 'Walk-in Customer';

        if (nameEl) nameEl.textContent = name;
        if (badgeEl) badgeEl.classList.toggle('d-none', !hasCustomer);
        if (noCustomerEl) noCustomerEl.classList.toggle('d-none', hasCustomer);
        if (utangWarningEl) utangWarningEl.classList.toggle('d-none', hasCustomer || !this.hasUtangPayment());

    },

    hasUtangPayment() {
        return this.state.payments.some(p => p.method === 'utang');
    },
    getFilteredProducts() {
        const keyword = (this.searchInput?.value || '').trim().toLowerCase();
        const activeCategory = document.querySelector('.category-chip.active')?.dataset.category;

        let filtered = this.products || [];

        if (activeCategory) {
            filtered = filtered.filter(p => String(p.category_id) === String(activeCategory));
        }

        if (keyword) {
            filtered = filtered.filter(p => {
                const name = (p.name || '').toLowerCase();
                const barcode = (p.barcode || '').toLowerCase();
                const sku = (p.sku || '').toLowerCase();
                const variantMatch = p.variants && Array.isArray(p.variants) && p.variants.some(v =>
                    (v.variant_name || '').toLowerCase().includes(keyword) ||
                    (v.barcode || '').toLowerCase().includes(keyword) ||
                    (v.sku || '').toLowerCase().includes(keyword)
                );
                return name.includes(keyword) || barcode.includes(keyword) || sku.includes(keyword) || variantMatch;
            });
        }

        // Helper to compute effective stock including variants
        const getEffectiveStock = (p) => {
            if (p.variants && Array.isArray(p.variants) && p.variants.length > 0) {
                const varSum = p.variants.reduce((sum, v) => sum + Number(v.stock_on_hand || 0), 0);
                if (varSum > 0 || Number(p.stock_on_hand || 0) <= 0) {
                    return varSum;
                }
            }
            return Number(p.stock_on_hand || p.stock || 0);
        };

        // Filter: Return ONLY in-stock products (stock > 0)
        filtered = filtered.filter(p => getEffectiveStock(p) > 0);

        // Sort by stock descending, then name ascending
        filtered.sort((a, b) => {
            const stockA = getEffectiveStock(a);
            const stockB = getEffectiveStock(b);
            if (stockA !== stockB) {
                return stockB - stockA; // Higher stock first
            }
            return (a.name || '').localeCompare(b.name || '');
        });

        return filtered;
    },

    bindSearch() {
        if (!this.searchInput) {
            return;
        }

        let searchTimeout = null;

        this.searchInput.addEventListener('input', e => {
            const keyword = e.target.value.trim().toLowerCase();

            // 1. Instant local search from memory cache (0ms delay)
            this.renderProducts(this.getFilteredProducts());

            // 2. Debounced Online Server-Side Search (300ms delay)
            clearTimeout(searchTimeout);
            if (keyword.length >= 2) {
                searchTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch(`/sales/products?q=${encodeURIComponent(keyword)}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        if (response.ok) {
                            const onlineProducts = await response.json();
                            if (onlineProducts && Array.isArray(onlineProducts) && onlineProducts.length > 0) {
                                const existingMap = new Map((this.products || []).map(p => [p.id, p]));
                                onlineProducts.forEach(op => existingMap.set(op.id, op));
                                this.products = Array.from(existingMap.values());

                                onlineProducts.forEach(p => {
                                    if (p.barcode) this.state.productMap[p.barcode.toLowerCase()] = p;
                                    if (p.variants && Array.isArray(p.variants)) {
                                        p.variants.forEach(v => {
                                            if (v.barcode) this.state.productMap[v.barcode.toLowerCase()] = { product: p, variant: v };
                                        });
                                    }
                                });

                                this.renderProducts(this.getFilteredProducts());
                            }
                        }
                    } catch (err) {
                        console.warn('Online product search failed:', err);
                    }
                }, 300);
            }
        });
    },
    bindCategories() {
        this.categoryChips = document.querySelectorAll('.category-chip');
        this.categoryChips.forEach(chip => {
            chip.addEventListener('click', () => {
                document.querySelectorAll('.category-chip').forEach(button => button.classList.remove('active'));
                chip.classList.add('active');
                const category = chip.dataset.category;
                this.filterCategory(category);
            });
        });
    },
    filterCategory(categoryId) {
        this.renderProducts(this.getFilteredProducts());
    },
    bindPricingMode() {
        const modeRadios = document.querySelectorAll('input[name="priceMode"]');
        modeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.state.priceMode = e.target.value;

                    // Update prices of existing cart items
                    const isWholesale = this.state.priceMode === 'wholesale';
                    this.state.cart.forEach(item => {
                        const prod = (this.products || []).find(p => p.id === item.id);
                        let ws = Number(item.wholesale_price || 0);
                        let ret = Number(item.retail_price || item.selling_price || item.price || 0);

                        if (item.variant_id && prod && prod.variants) {
                            const v = prod.variants.find(va => va.id === item.variant_id);
                            if (v) {
                                if (Number(v.wholesale_price || 0) > 0) ws = Number(v.wholesale_price);
                                if (Number(v.selling_price || 0) > 0) ret = Number(v.selling_price);
                            }
                        } else if (prod) {
                            if (Number(prod.wholesale_price || 0) > 0) ws = Number(prod.wholesale_price);
                            if (Number(prod.selling_price || 0) > 0) ret = Number(prod.selling_price);
                        }

                        item.wholesale_price = ws;
                        item.retail_price = ret;

                        if (isWholesale && ws > 0) {
                            item.price = ws;
                        } else if (ret > 0) {
                            item.price = ret;
                        }
                        item.subtotal = item.qty * item.price;
                    });

                    // Re-render product catalog table/grid with the active price mode
                    this.renderProducts(this.getFilteredProducts());

                    // Recalculate totals and update cart display
                    this.calculateTotals();
                    this.renderCart();
                    this.renderSummary();
                    this.renderLiveReceiptPreview();
                    this.saveCurrentOrderToStorage();
                }
            });
        });
    },
    bindBarcodeScanner() {

        if (!this.searchInput) {
            return;
        }

        this.searchInput.addEventListener(
            'keydown',
            e => {

                if (
                    e.key !== 'Enter'
                ) {
                    return;
                }

                const barcode =
                    e.target.value.trim().toLowerCase();

                if (!barcode) {
                    return;
                }

                const match =
                    this.state.productMap[
                    barcode
                    ];

                if (!match) {
                    // Try case-insensitive scan in products
                    const matchedProduct = this.products?.find(p => (p.barcode || '').toLowerCase() === barcode);
                    if (matchedProduct) {
                        if (matchedProduct.variants && matchedProduct.variants.length > 0) {
                            this.showVariantModal(matchedProduct);
                        } else {
                            const isWholesale = this.state.priceMode === 'wholesale';
                            const retail = Number(matchedProduct.selling_price || 0);
                            const wholesale = Number(matchedProduct.wholesale_price || 0);
                            const activePrice = (isWholesale && wholesale > 0) ? wholesale : retail;
                            this.addToCart({
                                id: matchedProduct.id,
                                variant_id: null,
                                barcode: matchedProduct.barcode,
                                name: matchedProduct.name,
                                unit: matchedProduct.unit?.name || '',
                                retail_price: retail,
                                wholesale_price: wholesale,
                                price: activePrice,
                                stock: Number(matchedProduct.stock_on_hand || 0),
                            });
                        }
                        e.target.value = '';
                        return;
                    }

                    e.target.select();
                    this.setStatus(`Barcode ${barcode} not found`, 'warning');
                    return;
                }

                if (match.type === 'variant') {
                    const { product, variant } = match;
                    const isWholesale = this.state.priceMode === 'wholesale';
                    const vRetail = Number(variant.selling_price || 0);
                    const vWholesale = Number(variant.wholesale_price || 0);
                    const activePrice = (isWholesale && vWholesale > 0) ? vWholesale : vRetail;
                    const vStock = Number(variant.stock_on_hand || product.stock_on_hand || 0);

                    this.addToCart({
                        id: product.id,
                        variant_id: variant.id,
                        name: `${product.name} (${variant.variant_name})`,
                        unit: variant.unit?.name || product.unit?.name || '',
                        barcode: variant.barcode,
                        retail_price: vRetail,
                        wholesale_price: vWholesale,
                        price: activePrice,
                        stock: vStock,
                    });
                } else if (match.type === 'product') {
                    const product = match.product;
                    if (product.variants && product.variants.length > 0) {
                        this.showVariantModal(product);
                    } else {
                        const isWholesale = this.state.priceMode === 'wholesale';
                        const retail = Number(product.selling_price || 0);
                        const wholesale = Number(product.wholesale_price || 0);
                        const activePrice = (isWholesale && wholesale > 0) ? wholesale : retail;
                        this.addToCart({
                            id: product.id,
                            variant_id: null,
                            barcode: product.barcode,
                            name: product.name,
                            unit: product.unit?.name || '',
                            retail_price: retail,
                            wholesale_price: wholesale,
                            price: activePrice,
                            stock: Number(product.stock_on_hand || 0),
                        });
                    }
                } else if (match.card) {
                    match.card.click();
                }

                e.target.value = '';

            }
        );

    },
    bindKeyboardShortcuts() {

        document.addEventListener(
            'keydown',
            e => {

                const tag =
                    document.activeElement.tagName;

                if (
                    tag === 'INPUT' ||
                    tag === 'TEXTAREA' ||
                    tag === 'SELECT'
                ) {
                    return;
                }

                switch (e.key) {

                    case 'F2':

                        e.preventDefault();

                        this.searchInput?.focus();

                        break;

                    case 'F4':

                        e.preventDefault();

                        this.checkoutButton?.click();

                        break;

                    case 'F6':

                        e.preventDefault();

                        this.holdCurrentTransaction();

                        break;

                    case 'Escape':

                        e.preventDefault();

                        this.searchInput?.blur();

                        break;

                }

            }
        );

    },
    parseFractionOrDecimal(input) {
        if (typeof input === 'number') return Math.max(0.0001, input);
        const str = String(input || '').trim();
        if (!str) return 0;

        // Mixed fraction like "1 1/2" or "2 1/4"
        if (str.includes(' ') && str.includes('/')) {
            const spaceParts = str.split(' ');
            if (spaceParts.length === 2) {
                const whole = parseFloat(spaceParts[0]);
                const frac = spaceParts[1].split('/');
                if (!isNaN(whole) && frac.length === 2) {
                    const n = parseFloat(frac[0]);
                    const d = parseFloat(frac[1]);
                    if (!isNaN(n) && !isNaN(d) && d !== 0) {
                        return whole + (n / d);
                    }
                }
            }
        }

        // Simple fraction like "1/4", "1/2", "3/4"
        if (str.includes('/')) {
            const parts = str.split('/');
            if (parts.length === 2) {
                const n = parseFloat(parts[0]);
                const d = parseFloat(parts[1]);
                if (!isNaN(n) && !isNaN(d) && d !== 0) {
                    return n / d;
                }
            }
        }

        const val = parseFloat(str);
        return isNaN(val) ? 0 : Math.max(0.0001, val);
    },

    async ensureActiveTransaction() {
        if (!this._isSaleAlreadyPaid && !this.state.isPaid) {
            return true;
        }

        try {
            this.setStatus('Starting new transaction...', 'info');
            const res = await fetch('/sales/new-transaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ current_sale_id: this.state.saleId })
            });
            const data = await res.json();
            if (data.success && data.sale_id) {
                this.state.saleId = data.sale_id;
                this.state.saleCode = data.sale_code;
                this._isSaleAlreadyPaid = false;
                this.state.isPaid = false;
                this.state.cart = [];
                this.state.subtotal = 0;
                this.state.discount = 0;
                this.state.total = 0;
                this.state.paid = 0;
                this.state.change = 0;

                const saleIdInput = document.getElementById('saleId');
                if (saleIdInput) saleIdInput.value = data.sale_id;

                const isSalePaidInput = document.getElementById('isSalePaid');
                if (isSalePaidInput) isSalePaidInput.value = '0';

                // Update order code badge on top bar and receipt preview
                const orderCodeEl = document.querySelector('.pos-top-header strong.font-mono');
                if (orderCodeEl) orderCodeEl.textContent = `#${data.sale_code}`;
                const receiptInvoiceEl = document.getElementById('receiptInvoiceNo');
                if (receiptInvoiceEl) receiptInvoiceEl.textContent = `#${data.sale_code}`;

                // Update browser URL silently without page reload
                if (data.sale_url && window.history.replaceState) {
                    window.history.replaceState(null, '', data.sale_url);
                }

                if (typeof window.appAlert === 'function') {
                    window.appAlert({
                        title: 'New Transaction Started',
                        text: `Previous transaction was already completed. Started new order #${data.sale_code}.`,
                        type: 'info'
                    });
                }

                this.renderCart();
                this.renderSummary();
                return true;
            }
        } catch (e) {
            console.error('Failed to auto-create new transaction:', e);
        }
        return true;
    },

    async addToCart(product) {
        if (this._isSaleAlreadyPaid || this.state.isPaid) {
            Swal.fire({
                icon: 'info',
                title: 'Past Completed Sale',
                text: 'This transaction has already been paid and finalized. Start a new sale to punch items.',
                confirmButtonColor: '#059669',
                confirmButtonText: 'Start New Sale',
                showCancelButton: true,
                cancelButtonText: 'Close'
            }).then(res => {
                if (res.isConfirmed) {
                    window.location.href = `/sales/terminal/${document.getElementById('saleId')?.value}/sale?q=new`;
                }
            });
            return;
        }

        if (
            product.stock <= 0
        ) {

            this.setStatus(
                `${product.name} is out of stock`,
                'warning'
            );

            this.logActivity(
                `${product.name} out of stock`
            );

            return;

        }

        const cartKey = product.id + '_' + (product.variant_id || 0);


        const existing =
            this.state.cart.find(
                item => (item.cartKey || (item.id + '_' + (item.variant_id || 0))) === cartKey
            );

        const retailPrice = Number(product.retail_price ?? product.price);
        const wholesalePrice = Number(product.wholesale_price ?? 0);
        const activePrice = (this.state.priceMode === 'wholesale' && wholesalePrice > 0) ? wholesalePrice : retailPrice;

        let costPrice = Number(product.cost_price || 0);
        if (!costPrice && this.products && Array.isArray(this.products)) {
            const prod = this.products.find(p => p.id === product.id);
            if (prod) {
                if (product.variant_id && prod.variants) {
                    const v = prod.variants.find(varItem => varItem.id === product.variant_id);
                    costPrice = Number(v?.cost_price ?? prod.cost_price ?? 0);
                } else {
                    costPrice = Number(prod.cost_price ?? 0);
                }
            }
        }

        if (existing) {

            if (
                existing.qty >= product.stock
            ) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Insufficient Stock',
                    text: `Only ${product.stock} available.`
                });

                return;

            }

            existing.qty = Math.round((existing.qty + 1) * 1000) / 1000;

            existing.price = activePrice;

            if (!existing.cost_price && costPrice) {
                existing.cost_price = costPrice;
            }

            existing.subtotal =
                existing.qty *
                existing.price;

        } else {

            this.state.cart.push({

                cartKey: cartKey,

                id: product.id,

                variant_id: product.variant_id || null,

                barcode: product.barcode,

                name: product.name,

                unit: product.unit || '',

                allow_decimal_qty: product.allow_decimal_qty ?? false,

                retail_price: retailPrice,

                wholesale_price: wholesalePrice,

                cost_price: costPrice,

                price: activePrice,

                stock: product.stock,

                qty: 1,

                subtotal: activePrice,

            });

        }

        this.calculateTotals();

        this.renderCart();

    },
    logActivity(
        message
    ) {

        if (
            !this.activityList
        ) {
            return;
        }

        const item =
            document.createElement(
                'div'
            );

        item.className =
            'activity-item';

        item.innerHTML = `

        <div
            class="activity-time"
        >
            ${new Date()
                .toLocaleTimeString()
            }
        </div>

        <div
            class="activity-text"
        >
            ${message}
        </div>

    `;

        this.activityList.prepend(
            item
        );

        while (
            this.activityList
                .children.length > 30
        ) {

            this.activityList
                .lastElementChild
                ?.remove();

        }

    },
    updateQuantity(
        cartKey,
        quantity
    ) {

        const item =
            this.state.cart.find(
                row => (row.cartKey || (row.id + '_' + (row.variant_id || 0))) === String(cartKey) || row.id === Number(cartKey)
            );

        if (!item) {
            return;
        }

        const parsedQty = this.parseFractionOrDecimal(quantity);

        if (parsedQty <= 0) {

            this.removeItem(
                cartKey
            );

            return;
        }

        if (
            parsedQty > item.stock
        ) {
            item.qty = item.stock;
            Swal.fire({
                icon: 'warning',
                title: 'Stock Limit Reached',
                text: `Only ${item.stock} available in stock.`
            });
        } else {
            item.qty = Math.round(parsedQty * 10000) / 10000;
        }

        item.subtotal =
            item.qty *
            item.price;

        this.calculateTotals();

        this.renderCart();

    },

    removeItem(
        cartKey
    ) {

        this.state.cart =
            this.state.cart.filter(
                item => (item.cartKey || (item.id + '_' + (item.variant_id || 0))) !== String(cartKey) && item.id !== Number(cartKey)
            );

        this.calculateTotals();

        this.renderCart();

    },

    getCartCostTotal() {
        if (!this.state.cart || !this.state.cart.length) return 0;
        return this.state.cart.reduce((totalCost, item) => {
            let unitCost = Number(item.cost_price || 0);
            if (!unitCost && this.products && Array.isArray(this.products)) {
                const prod = this.products.find(p => p.id === item.id);
                if (prod) {
                    if (item.variant_id && prod.variants) {
                        const v = prod.variants.find(varItem => varItem.id === item.variant_id);
                        unitCost = Number(v?.cost_price ?? prod.cost_price ?? 0);
                    } else {
                        unitCost = Number(prod.cost_price ?? 0);
                    }
                }
            }
            return totalCost + (unitCost * Number(item.qty || 0));
        }, 0);
    },

    calculateDiscountProfitPreview(previewDiscount = null) {
        const subtotal = Number(this.state.subtotal || 0);
        let discount = previewDiscount !== null ? Number(previewDiscount) : Number(this.state.discount || 0);
        discount = Math.min(discount, subtotal);
        const netTotal = Math.max(0, subtotal - discount);
        const totalCost = this.getCartCostTotal();
        const profit = netTotal - totalCost;
        const marginPercent = (netTotal > 0) ? (profit / netTotal) * 100 : (subtotal > 0 && totalCost > 0 ? ((subtotal - totalCost) / subtotal) * 100 : 0);

        return {
            subtotal,
            discount,
            netTotal,
            totalCost,
            profit,
            marginPercent,
            isLoss: (profit < -0.01 && this.state.cart.length > 0),
            isLowMargin: (profit >= -0.01 && marginPercent < 10 && this.state.cart.length > 0),
            isHealthy: (marginPercent >= 10 && this.state.cart.length > 0)
        };
    },

    updateDiscountProfitPreview() {
        const preview = this.calculateDiscountProfitPreview();

        const costEl = document.getElementById('previewCartCost');
        const dueEl = document.getElementById('previewDiscountedDue');
        const profitEl = document.getElementById('previewNetProfit');
        const marginBadge = document.getElementById('discountProfitMarginBadge');
        const alertEl = document.getElementById('discountProfitAlert');
        const alertIcon = document.getElementById('discountProfitAlertIcon');
        const alertText = document.getElementById('discountProfitAlertText');
        const cartProfitEl = document.getElementById('summaryCartProfit');
        const modalProfitEl = document.getElementById('summaryProfitModal');

        const formattedCost = this.formatCurrency(preview.totalCost);
        const formattedDue = this.formatCurrency(preview.netTotal);
        const formattedProfit = (preview.profit < 0 ? '-' : '+') + this.formatCurrency(Math.abs(preview.profit));
        const marginStr = (preview.marginPercent >= 0 ? '+' : '') + preview.marginPercent.toFixed(1) + '%';

        if (costEl) costEl.textContent = formattedCost;
        if (dueEl) dueEl.textContent = formattedDue;
        if (profitEl) {
            profitEl.textContent = formattedProfit;
            profitEl.className = `font-mono fw-black small ${preview.isLoss ? 'text-danger' : (preview.isLowMargin ? 'text-warning' : 'text-success')}`;
        }

        if (marginBadge) {
            marginBadge.textContent = `Margin: ${marginStr}`;
            if (preview.isLoss) {
                marginBadge.className = 'badge bg-danger text-white extra-small fw-bold px-2.5 py-1 rounded-pill animate-pulse';
            } else if (preview.isLowMargin) {
                marginBadge.className = 'badge bg-warning text-dark border border-warning extra-small fw-bold px-2.5 py-1 rounded-pill';
            } else {
                marginBadge.className = 'badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2.5 py-1 rounded-pill';
            }
        }

        if (alertEl && alertIcon && alertText) {
            if (!this.state.cart || this.state.cart.length === 0) {
                alertEl.style.background = '#f1f5f9';
                alertEl.style.color = '#475569';
                alertEl.style.borderColor = '#cbd5e1';
                alertIcon.className = 'bi bi-info-circle-fill fs-6 text-secondary';
                alertText.innerHTML = 'Walang laman ang cart. Magdagdag ng items upang makita ang profit analysis.';
            } else if (preview.isLoss) {
                alertEl.style.background = '#fef2f2';
                alertEl.style.color = '#991b1b';
                alertEl.style.borderColor = '#fecaca';
                alertIcon.className = 'bi bi-exclamation-triangle-fill fs-5 text-danger';
                alertText.innerHTML = `<strong>🚨 BABALA: MALULUGI ANG STORE!</strong> Lugi ng <strong>${this.formatCurrency(Math.abs(preview.profit))}</strong> (${marginStr}). Mas mababa ang singil kaysa sa puhunan (Cost: ${formattedCost})!`;
            } else if (preview.isLowMargin) {
                alertEl.style.background = '#fffbeb';
                alertEl.style.color = '#92400e';
                alertEl.style.borderColor = '#fde68a';
                alertIcon.className = 'bi bi-exclamation-circle-fill fs-6 text-warning';
                alertText.innerHTML = `<strong>⚠️ MABABANG MARGIN:</strong> Kumikita lamang ng <strong>${formattedProfit}</strong> (${marginStr}). Maliit ang tubo ng tindahan sa discount na ito.`;
            } else {
                alertEl.style.background = '#dcfce7';
                alertEl.style.color = '#166534';
                alertEl.style.borderColor = '#bbf7d0';
                alertIcon.className = 'bi bi-check-circle-fill fs-6 text-success';
                alertText.innerHTML = `<strong>✅ HEALTHY PROFIT MARGIN:</strong> Kikita ang store ng <strong>${formattedProfit}</strong> (${marginStr}) matapos ang discount.`;
            }
        }

        if (cartProfitEl) {
            cartProfitEl.textContent = `${formattedProfit} (${marginStr})`;
            cartProfitEl.className = `fw-black font-mono extra-small ${preview.isLoss ? 'text-danger' : (preview.isLowMargin ? 'text-warning' : 'text-success')}`;
        }

        if (modalProfitEl) {
            modalProfitEl.textContent = `${formattedProfit} (${marginStr})`;
            modalProfitEl.className = `font-mono fw-black small ${preview.isLoss ? 'text-danger' : (preview.isLowMargin ? 'text-warning' : 'text-success')}`;
        }
    },

    calculateTotals() {

        this.state.subtotal =
            Number(
                this.state.cart
                    .reduce(
                        (
                            total,
                            item
                        ) =>
                            total +
                            Number(
                                item.subtotal || 0
                            ),
                        0
                    )
                    .toFixed(2)
            );

        let discount = 0;

        const type =
            this.discountType?.value || '';

        if (
            type === 'manual'
        ) {

            const value =
                Number(
                    this.discountValue?.value || 0
                );

            const mode =
                this.discountMode?.value;

            if (
                mode === 'percentage'
            ) {

                discount =
                    this.state.subtotal *
                    (
                        value / 100
                    );

            } else {

                discount =
                    value;

            }

        } else if (
            type === 'senior'
        ) {

            discount =
                this.state.subtotal *
                0.20;

        } else if (
            type === 'pwd'
        ) {

            discount =
                this.state.subtotal *
                0.20;

        } else if (
            type === 'student'
        ) {

            discount =
                this.state.subtotal *
                0.05;

        } else if (
            type === 'employee'
        ) {

            discount =
                this.state.subtotal *
                0.10;

        } else if (
            type === 'promo' && this.state.appliedPromo
        ) {
            const promo = this.state.appliedPromo;
            if (promo.promo_type === 'percentage') {
                discount = this.state.subtotal * (parseFloat(promo.discount_value || 0) / 100);
            } else if (promo.promo_type === 'fixed_amount') {
                discount = parseFloat(promo.discount_value || 0);
            } else if (promo.discount_amount) {
                discount = parseFloat(promo.discount_amount || 0);
            }
        } else if (!type && this.state.autoPromotions && this.state.autoPromotions.length > 0) {
            // Automatic promotions: exactly 1 promo per item (NO stacking)
            for (const cartItem of this.state.cart) {
                if (cartItem.selectedPromoId === 'none') continue;

                let chosenPromo = null;
                if (cartItem.selectedPromoId) {
                    chosenPromo = this.state.autoPromotions.find(p => p.id === Number(cartItem.selectedPromoId));
                } else {
                    const matching = this.getMatchingPromosForItem(cartItem.id, cartItem.variant_id, cartItem.category_id);
                    if (matching.length > 0) {
                        chosenPromo = matching[0];
                        cartItem.selectedPromoId = chosenPromo.id;
                    }
                }

                if (chosenPromo) {
                    // Check min spend if configured
                    if (chosenPromo.min_spend > 0 && this.state.subtotal < chosenPromo.min_spend) {
                        continue;
                    }

                    const qty = parseFloat(cartItem.qty || 0);
                    if (qty >= (chosenPromo.min_quantity || 1)) {
                        if (chosenPromo.promo_type === 'bulk_tier') {
                            const specialPrice = parseFloat(chosenPromo.discount_value || 0);
                            if (specialPrice < cartItem.price) {
                                const diff = (cartItem.price - specialPrice) * qty;
                                discount += diff;
                                this.state.appliedPromo = chosenPromo;
                            }
                        } else if (chosenPromo.promo_type === 'percentage') {
                            discount += (cartItem.subtotal * (parseFloat(chosenPromo.discount_value || 0) / 100));
                            this.state.appliedPromo = chosenPromo;
                        } else if (chosenPromo.promo_type === 'fixed_amount') {
                            discount += Math.min(parseFloat(chosenPromo.discount_value || 0), cartItem.subtotal);
                            this.state.appliedPromo = chosenPromo;
                        }
                    }
                }
            }
        }

        discount =
            Number(
                Math.min(
                    discount,
                    this.state.subtotal
                ).toFixed(2)
            );

        const total =
            Number(
                (
                    this.state.subtotal -
                    discount
                ).toFixed(2)
            );

        this.state.discount =
            discount;

        this.state.total =
            total;

        if (
            this.state.payments?.length
        ) {

            this.calculatePayments();

        }

        this.updateDiscountProfitPreview();

    },

    renderCart() {

        if (
            this.state.cart.length === 0
        ) {

            this.cartItemsList.innerHTML = `
                <div class="empty-cart d-flex flex-column align-items-center justify-content-center py-5 text-muted">
                    <i class="bi bi-receipt fs-1 opacity-25 mb-2"></i>
                    <span class="fw-semibold small">Receipt is empty</span>
                    <small class="extra-small opacity-75">Click products to add to receipt</small>
                </div>
            `;

            this.renderSummary();

            return;

        }

        if (this._isSaleAlreadyPaid || this.state.isPaid) {
            const saleSubtotal = Number(this.state.subtotal || 0);
            const saleDiscount = Number(this.state.discount || 0);
            const overallDiscountRate = (saleSubtotal > 0 && saleDiscount > 0) ? (saleDiscount / saleSubtotal) : 0;
            const isSaleRefunded = Boolean(this.state.lastPaidSale?.is_refunded);
            const isSalePartial = Boolean(this.state.lastPaidSale?.is_partial);

            this.cartItemsList.innerHTML = this.state.cart.map(item => {
                const unitStr = typeof item.unit === 'object' && item.unit !== null ? (item.unit.name || '') : (item.unit || '');
                const origQty = Number(item.original_qty ?? item.qty ?? 1);
                const returnedQty = Number(item.returned_qty ?? (isSaleRefunded ? origQty : 0));
                const retainedQty = Number(item.retained_qty ?? (isSaleRefunded ? 0 : Math.max(0, origQty - returnedQty)));
                const isItemRefunded = Boolean(item.is_item_refunded || isSaleRefunded || (origQty > 0 && returnedQty >= origQty));
                const isItemPartial = Boolean(!isItemRefunded && (item.is_item_partial || returnedQty > 0));

                const originalUnitPrice = Number(item.original_price || item.price || 0);
                const grossLineTotal = origQty * originalUnitPrice;

                let lineDiscount = Number(item.discount || item.discount_amount || 0);
                if (lineDiscount === 0 && overallDiscountRate > 0) {
                    lineDiscount = grossLineTotal * overallDiscountRate;
                }

                const effectiveSubtotal = lineDiscount > 0 ? Math.max(0, grossLineTotal - lineDiscount) : (Number(item.subtotal) || grossLineTotal);
                const effectiveUnitPrice = origQty > 0 ? (effectiveSubtotal / origQty) : originalUnitPrice;
                const hasDiscount = lineDiscount > 0 || (effectiveUnitPrice < (originalUnitPrice - 0.001) && effectiveUnitPrice > 0);

                const retainedSubtotal = isItemRefunded ? 0 : (effectiveUnitPrice * retainedQty);

                if (isItemRefunded) {
                    return `
                        <div class="card border border-danger-subtle rounded-3 p-2.5 mb-2 shadow-xs" style="background: #fef2f2;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fw-bold text-dark font-mono small text-decoration-line-through text-muted">${item.name}</div>
                                <span class="badge bg-danger text-white font-mono extra-small fw-bold px-2 py-0.5 rounded-pill">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Refunded
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-muted extra-small font-mono">
                                <div>
                                    <span class="text-danger fw-bold">${origQty} ${unitStr ? unitStr : 'unit(s)'} (Returned)</span> × ₱${originalUnitPrice.toFixed(2)}
                                </div>
                                <div class="text-end font-mono">
                                    <div class="fw-black text-danger fs-6">₱0.00</div>
                                    <small class="text-muted extra-small text-decoration-line-through">₱${grossLineTotal.toFixed(2)}</small>
                                </div>
                            </div>
                            <div class="mt-1 pt-1 border-top border-danger-subtle d-flex align-items-center justify-content-between extra-small text-danger font-mono">
                                <span><i class="bi bi-check2-all me-1"></i>100% Restocked</span>
                                <span class="fw-bold">-₱${grossLineTotal.toFixed(2)} Payout</span>
                            </div>
                        </div>
                    `;
                }

                if (isItemPartial) {
                    const itemRefundedPayout = effectiveUnitPrice * returnedQty;
                    return `
                        <div class="card border border-warning-subtle rounded-3 p-2.5 mb-2 shadow-xs" style="background: #fffbeb;">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="fw-bold text-dark font-mono small">${item.name}</div>
                                <span class="badge bg-warning text-dark font-mono extra-small fw-bold px-2 py-0.5 rounded-pill">
                                    <i class="bi bi-arrow-return-left me-1"></i>${returnedQty} Returned
                                </span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between text-muted extra-small font-mono">
                                <div>
                                    <span class="text-success fw-bold">${retainedQty} retained</span> <span class="text-muted">(${origQty} orig)</span> × ₱${effectiveUnitPrice.toFixed(2)}
                                </div>
                                <div class="text-end font-mono">
                                    <div class="fw-black text-dark fs-6">₱${retainedSubtotal.toFixed(2)}</div>
                                    <small class="text-muted extra-small text-decoration-line-through">₱${grossLineTotal.toFixed(2)}</small>
                                </div>
                            </div>
                            <div class="mt-1 pt-1 border-top border-warning-subtle d-flex align-items-center justify-content-between extra-small text-warning-emphasis font-mono">
                                <span><i class="bi bi-arrow-return-left me-1"></i>${returnedQty} returned & restocked</span>
                                <span class="fw-bold text-danger">-₱${itemRefundedPayout.toFixed(2)} Refunded</span>
                            </div>
                        </div>
                    `;
                }

                return `
                    <div class="card border rounded-3 p-2.5 mb-2 bg-light shadow-xs">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <div class="fw-bold text-dark font-mono small">${item.name}</div>
                            <span class="badge bg-success text-white font-mono extra-small fw-bold px-2 py-0.5 rounded-pill">Sold</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between text-muted extra-small font-mono">
                            <div>
                                ${origQty} ${unitStr ? unitStr : 'unit(s)'} × 
                                ${hasDiscount ? `
                                    <span class="text-danger fw-bold font-mono">₱${effectiveUnitPrice.toFixed(2)}</span>
                                    <span class="text-decoration-line-through text-muted extra-small font-mono">₱${originalUnitPrice.toFixed(2)}</span>
                                ` : `
                                    <span>₱${originalUnitPrice.toFixed(2)}</span>
                                `}
                            </div>
                            <div class="text-end font-mono">
                                <div class="fw-black ${hasDiscount ? 'text-danger' : 'text-dark'} fs-6">₱${effectiveSubtotal.toFixed(2)}</div>
                                ${hasDiscount ? `<small class="text-muted extra-small text-decoration-line-through">₱${grossLineTotal.toFixed(2)}</small>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            this.renderSummary();
            return;
        }

        this.cartItemsList.innerHTML =
            this.state.cart
                .map(item => {

                    const isWholesaleItem = this.state.priceMode === 'wholesale' && item.wholesale_price > 0 && item.price === item.wholesale_price;
                    const badgeHtml = item.is_custom 
                        ? '<span class="badge border extra-small ms-1.5" style="background:#f5f3ff;color:#7c3aed;border-color:#ddd6fe;font-size:0.65rem;">Fee / Service</span>' 
                        : (isWholesaleItem ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small ms-1.5" style="font-size:0.65rem;">Wholesale</span>' : '');
                    const itemKey = item.cartKey || (item.id + '_' + (item.variant_id || 0));

                    // Promotions for this item
                    const matchingPromos = this.getMatchingPromosForItem(item.id, item.variant_id, item.category_id);
                    let promoHtml = '';
                    let activePromo = null;

                    if (matchingPromos.length > 1) {
                        activePromo = (item.selectedPromoId && item.selectedPromoId !== 'none') ? this.state.autoPromotions.find(p => p.id === Number(item.selectedPromoId)) : null;
                        const label = activePromo ? activePromo.title : 'No Promo';
                        promoHtml = `
                            <div class="mt-1">
                                <button type="button" class="btn btn-xs btn-primary-subtle text-primary border border-primary-subtle rounded-pill extra-small fw-bold px-2 py-0.5 open-promo-modal-btn d-inline-flex align-items-center gap-1" data-key="${itemKey}" title="Multiple promos available. Click to choose.">
                                    <i class="bi bi-tag-fill"></i>
                                    <span>${label}</span>
                                    <span class="badge bg-primary text-white rounded-pill ms-0.5" style="font-size:0.6rem;">${matchingPromos.length} promos ▾</span>
                                </button>
                            </div>
                        `;
                    } else if (matchingPromos.length === 1) {
                        activePromo = (item.selectedPromoId !== 'none') ? matchingPromos[0] : null;
                        if (activePromo) {
                            promoHtml = `
                                <div class="mt-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5 rounded-pill">
                                        <i class="bi bi-tag-fill me-1"></i>${activePromo.title}
                                    </span>
                                </div>
                            `;
                        }
                    }

                    // Calculate line discount for visual display
                    let itemDiscount = 0;
                    let effectiveUnitPrice = item.price;
                    if (activePromo) {
                        const isMinSpendMet = !(activePromo.min_spend > 0 && this.state.subtotal < activePromo.min_spend);
                        const isMinQtyMet = item.qty >= (activePromo.min_quantity || 1);

                        if (isMinSpendMet && isMinQtyMet) {
                            effectiveUnitPrice = this.getPromoDiscountedPrice(item.price, activePromo);
                            if (activePromo.promo_type === 'percentage') {
                                itemDiscount = (item.subtotal * (parseFloat(activePromo.discount_value || 0) / 100));
                            } else if (activePromo.promo_type === 'fixed_amount') {
                                itemDiscount = Math.min(parseFloat(activePromo.discount_value || 0), item.subtotal);
                            } else if (activePromo.promo_type === 'bulk_tier') {
                                const specialPrice = parseFloat(activePromo.discount_value || 0);
                                if (specialPrice < item.price) {
                                    itemDiscount = (item.price - specialPrice) * item.qty;
                                }
                            }
                        }
                    }

                    const effectiveSubtotal = Math.max(0, item.subtotal - itemDiscount);

                    let priceDisplay = '';
                    let subtotalDisplay = '';
                    if (itemDiscount > 0) {
                        priceDisplay = `<div class="receipt-item-calc font-mono" style="font-size: 0.82rem; font-weight: 600;"><span class="text-danger fw-black">₱${effectiveUnitPrice.toFixed(2)}</span> <span class="extra-small text-muted text-decoration-line-through">₱${item.price.toFixed(2)}</span> / ${item.unit || 'unit'}</div>`;
                        subtotalDisplay = `<div class="text-end font-mono"><div class="fw-extrabold text-danger font-mono" style="font-size: 1.05rem; font-weight: 900;">₱${effectiveSubtotal.toFixed(2)}</div><small class="text-muted extra-small text-decoration-line-through font-mono">₱${item.subtotal.toFixed(2)}</small></div>`;
                    } else {
                        priceDisplay = `<div class="receipt-item-calc text-muted font-mono" style="font-size: 0.82rem; font-weight: 600; color: #64748b;">₱${item.price.toFixed(2)} / ${item.unit || 'unit'}</div>`;
                        subtotalDisplay = `<div class="receipt-item-total fw-extrabold text-dark font-mono text-end" style="font-size: 1.05rem; font-weight: 900; color: #0f172a; white-space: nowrap; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">₱${item.subtotal.toFixed(2)}</div>`;
                    }

                    return `
<div class="receipt-item py-2.5 px-3 border-bottom bg-white" style="border-bottom: 1px dashed #cbd5e1 !important;" data-key="${itemKey}" data-id="${item.id}">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
        <div class="receipt-item-title fw-bold text-dark lh-sm flex-grow-1" style="font-size: 0.92rem; color: #0f172a;">
            ${item.name} ${badgeHtml}
            ${promoHtml}
        </div>
        ${subtotalDisplay}
    </div>

    <div class="d-flex justify-content-between align-items-center mt-1">
        ${priceDisplay}

        <div class="d-flex align-items-center gap-1.5">
            <!-- Stepper / Clickable Quantity Pill -->
            <div class="receipt-stepper d-flex align-items-center bg-light rounded-3 border p-0.5 shadow-xs">
                <button type="button" class="btn btn-sm btn-white qty-minus border-0 px-2 py-0 fw-bold text-dark lh-1 shadow-xs" style="width:26px;height:26px;font-size:0.9rem;" data-key="${itemKey}">−</button>
                
                <button type="button" class="btn btn-sm btn-light border-0 px-2 py-0 font-mono fw-extrabold text-dark open-qty-modal-btn" style="min-width: 46px; height: 26px; font-size: 0.88rem;" data-key="${itemKey}" title="Click to adjust quantity or weight">
                    ${item.qty} <small class="text-muted" style="font-size:0.75rem;">${item.unit || ''}</small>
                </button>
                
                <button type="button" class="btn btn-sm btn-white qty-plus border-0 px-2 py-0 fw-bold text-dark lh-1 shadow-xs" style="width:26px;height:26px;font-size:0.9rem;" data-key="${itemKey}">+</button>
            </div>

            <button type="button" class="btn btn-sm text-danger remove-item p-1 border-0 lh-1" data-key="${itemKey}" title="Remove item" style="width:26px;height:26px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-trash3-fill fs-6"></i>
            </button>
        </div>
    </div>
</div>
`;

                })
                .join('');

        this.attachCartEvents();

        this.renderSummary();

        this.saveCurrentOrderToStorage();

    },

    showPromoModal(cartKey) {
        const item = this.state.cart.find(row => (row.cartKey || (row.id + '_' + (row.variant_id || 0))) === String(cartKey));
        if (!item) return;

        const modalEl = document.getElementById('modalSelectPromo');
        if (!modalEl) return;

        const matching = this.getMatchingPromosForItem(item.id, item.variant_id, item.category_id);
        const listEl = document.getElementById('promoOptionsList');
        const keyInput = document.getElementById('inpPromoModalCartKey');
        const titleEl = document.getElementById('txtPromoModalTitle');
        const subEl = document.getElementById('txtPromoModalSubtitle');

        if (titleEl) titleEl.textContent = `Select Promo: ${item.name}`;
        if (subEl) subEl.textContent = `${matching.length} promotions available for this item (Choose 1)`;
        if (keyInput) keyInput.value = cartKey;

        const currentSelectedId = item.selectedPromoId !== undefined ? item.selectedPromoId : (matching[0]?.id || 'none');

        let optionsHtml = '';

        matching.forEach(p => {
            const isChecked = String(currentSelectedId) === String(p.id) ? 'checked' : '';
            const discPrice = this.getPromoDiscountedPrice(item.price, p);
            let promoBadge = '';
            if (p.promo_type === 'percentage') {
                promoBadge = `<span class="badge bg-primary text-white font-mono fw-bold">${parseFloat(p.discount_value)}% OFF</span>`;
            } else if (p.promo_type === 'fixed_amount') {
                promoBadge = `<span class="badge bg-success text-white font-mono fw-bold">₱${parseFloat(p.discount_value).toFixed(2)} OFF</span>`;
            } else if (p.promo_type === 'bulk_tier') {
                promoBadge = `<span class="badge bg-warning text-dark font-mono fw-bold">₱${parseFloat(p.discount_value).toFixed(2)} / pc (${p.min_quantity}+ pcs)</span>`;
            } else if (p.promo_type === 'buy_x_get_y') {
                promoBadge = `<span class="badge bg-purple text-white font-mono fw-bold" style="background:#6d28d9;">Buy ${p.min_quantity} Get ${p.get_quantity}</span>`;
            }

            optionsHtml += `
                <label class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-white cursor-pointer hover-bg-light position-relative mb-1">
                    <div class="d-flex align-items-center gap-2.5">
                        <input class="form-check-input radio-promo-option mt-0" type="radio" name="selectedPromoRadio" value="${p.id}" ${isChecked}>
                        <div>
                            <div class="fw-bold text-dark small mb-0.5">${p.title}</div>
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                ${promoBadge}
                                ${p.min_spend > 0 ? `<span class="extra-small text-muted font-mono">(Min spend: ₱${p.min_spend})</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="text-end font-mono">
                        <div class="fw-black text-success fs-6">${this.formatCurrency(discPrice)}</div>
                        <small class="text-muted extra-small text-decoration-line-through">${this.formatCurrency(item.price)}</small>
                    </div>
                </label>
            `;
        });

        // "No Promo / Regular Price" option
        const isNoneChecked = (currentSelectedId === 'none' || currentSelectedId === null) ? 'checked' : '';
        optionsHtml += `
            <label class="d-flex align-items-center justify-content-between p-3 rounded-3 border bg-light cursor-pointer position-relative">
                <div class="d-flex align-items-center gap-2.5">
                    <input class="form-check-input radio-promo-option mt-0" type="radio" name="selectedPromoRadio" value="none" ${isNoneChecked}>
                    <div>
                        <div class="fw-bold text-muted small">No Promotion (Regular Price)</div>
                        <small class="extra-small text-muted">Sell at standard SRP without promo discount</small>
                    </div>
                </div>
                <div class="text-end font-mono fw-bold text-dark">
                    ${this.formatCurrency(item.price)}
                </div>
            </label>
        `;

        if (listEl) listEl.innerHTML = optionsHtml;

        const modal = Modal.getOrCreateInstance(modalEl);
        modal.show();

        const confirmBtn = document.getElementById('btnConfirmPromoModal');
        if (confirmBtn) {
            confirmBtn.onclick = () => {
                const selectedRadio = modalEl.querySelector('input[name="selectedPromoRadio"]:checked');
                const chosenVal = selectedRadio ? selectedRadio.value : 'none';
                item.selectedPromoId = chosenVal === 'none' ? 'none' : Number(chosenVal);
                this.calculateTotals();
                this.renderCart();
                modal.hide();
            };
        }
    },

    showQuantityModal(cartKey) {
        const item = this.state.cart.find(row => (row.cartKey || (row.id + '_' + (row.variant_id || 0))) === String(cartKey));
        if (!item) return;

        const modalEl = document.getElementById('quantityModal');
        if (!modalEl) return;

        const modalName = document.getElementById('qtyModalProductName');
        const modalMeta = document.getElementById('qtyModalProductMeta');
        const modalKey = document.getElementById('qtyModalCartKey');
        const modalInput = document.getElementById('qtyModalInput');
        const modalUnit = document.getElementById('qtyModalUnitLabel');
        const modalSubtotal = document.getElementById('qtyModalSubtotal');
        const fractionSection = document.getElementById('qtyModalFractionSection');
        const wholeSection = document.getElementById('qtyModalWholeSection');
        const helpText = document.getElementById('qtyModalHelpText');

        if (modalName) modalName.textContent = item.name;
        if (modalMeta) modalMeta.textContent = `₱${item.price.toFixed(2)} per ${item.unit || 'unit'}`;
        if (modalKey) modalKey.value = cartKey;
        if (modalInput) modalInput.value = item.qty;
        if (modalUnit) modalUnit.textContent = item.unit || 'units';

        const allowsDecimal = item.allow_decimal_qty ?? false;

        if (fractionSection) fractionSection.classList.toggle('d-none', !allowsDecimal);
        if (wholeSection) wholeSection.classList.toggle('d-none', allowsDecimal);
        if (helpText) {
            helpText.innerHTML = allowsDecimal
                ? 'You can type fractions like <code>1/4</code>, <code>1/2</code>, <code>3/4</code> or decimals like <code>0.25</code>, <code>1.5</code>.'
                : 'Enter standard quantity for this item.';
        }

        const updatePreview = () => {
            const qty = this.parseFractionOrDecimal(modalInput.value);
            const sub = qty * item.price;
            if (modalSubtotal) modalSubtotal.textContent = this.formatCurrency(sub);
        };

        updatePreview();

        modalInput.oninput = updatePreview;

        modalEl.querySelectorAll('.qty-preset-btn').forEach(btn => {
            btn.onclick = () => {
                modalInput.value = btn.dataset.val;
                updatePreview();
            };
        });

        const modal = Modal.getOrCreateInstance(modalEl);
        modal.show();

        setTimeout(() => {
            modalInput.focus();
            modalInput.select();
        }, 200);

        const confirmBtn = document.getElementById('btnConfirmQtyModal');
        if (confirmBtn) {
            confirmBtn.onclick = () => {
                const finalQty = this.parseFractionOrDecimal(modalInput.value);
                this.updateQuantity(cartKey, finalQty);
                modal.hide();
            };
        }

        modalInput.onkeydown = (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmBtn?.click();
            }
        };
    },

    attachCartEvents() {

        // Open Quantity Modal when clicking quantity pill
        document.querySelectorAll('.open-qty-modal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.key;
                this.showQuantityModal(key);
            });
        });

        // Open Promotion Selector Modal
        document.querySelectorAll('.open-promo-modal-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.key;
                this.showPromoModal(key);
            });
        });

        // Quantity Minus
        document
            .querySelectorAll('.qty-minus')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        const key = button.dataset.key;

                        const item =
                            this.state.cart.find(
                                row => (row.cartKey || (row.id + '_' + (row.variant_id || 0))) === key
                            );

                        if (!item) {
                            return;
                        }

                        const step = (item.allow_decimal_qty && item.qty <= 1 && item.qty > 0.25) ? 0.25 : 1;
                        this.updateQuantity(
                            key,
                            Math.max(0.0001, Math.round((item.qty - step) * 1000) / 1000)
                        );

                    }
                );

            });

        // Quantity Plus
        document
            .querySelectorAll('.qty-plus')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        const key = button.dataset.key;

                        const item =
                            this.state.cart.find(
                                row => (row.cartKey || (row.id + '_' + (row.variant_id || 0))) === key
                            );

                        if (!item) {
                            return;
                        }

                        const step = (item.allow_decimal_qty && item.qty < 1) ? 0.25 : 1;
                        this.updateQuantity(
                            key,
                            Math.round((item.qty + step) * 1000) / 1000
                        );

                    }
                );

            });

        // Remove item
        document.querySelectorAll('.remove-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.key;
                this.removeItem(key);
            });
        });

    },

    renderSummary() {

        if (
            !this.summarySubtotal ||
            !this.summaryDiscount ||
            !this.summaryTotal
        ) {
            return;
        }

        const lastPaid = this.state.lastPaidSale;
        const isSaleRefunded = Boolean(lastPaid?.is_refunded);
        const isSalePartial = Boolean(lastPaid?.is_partial);
        const refundRow = document.getElementById('cartRefundRow');
        const refundPayoutEl = document.getElementById('summaryRefundPayout');

        if (isSaleRefunded) {
            this.summarySubtotal.innerHTML = `<span class="text-decoration-line-through text-muted extra-small me-1">${this.formatCurrency(this.state.subtotal)}</span> <span class="text-danger fw-black">₱0.00</span>`;
            this.summaryDiscount.textContent = '-₱0.00';
            this.summaryTotal.innerHTML = `<del class="extra-small text-secondary font-mono d-block" style="font-size:0.85rem;line-height:1.2;">${this.formatCurrency(this.state.total)}</del><span class="text-danger fw-extrabold" style="font-size:1.45rem;">₱0.00 <span class="badge bg-danger text-white extra-small" style="font-size:0.65rem;vertical-align:middle;">REFUNDED</span></span>`;
            if (refundRow && refundPayoutEl) {
                refundRow.classList.remove('d-none');
                refundPayoutEl.textContent = '-' + this.formatCurrency(this.state.total);
            }
            const profitEl = document.getElementById('summaryCartProfit');
            if (profitEl) {
                profitEl.innerHTML = `<span class="text-muted text-decoration-line-through me-1">₱0.00</span> <span class="text-danger fw-bold">₱0.00 (Refunded)</span>`;
            }
        } else if (isSalePartial) {
            const refundedAmount = Number(lastPaid.refunded_amount || 0);
            const retainedTotal = Number(lastPaid.retained_total || Math.max(0, this.state.total - refundedAmount));
            this.summarySubtotal.innerHTML = `<span class="text-decoration-line-through text-muted extra-small me-1">${this.formatCurrency(this.state.subtotal)}</span> <span class="text-dark fw-black">${this.formatCurrency(retainedTotal)}</span>`;
            this.summaryTotal.innerHTML = `<del class="extra-small text-secondary font-mono d-block" style="font-size:0.85rem;line-height:1.2;">${this.formatCurrency(this.state.total)}</del><span class="text-white fw-extrabold" style="font-size:1.45rem;">${this.formatCurrency(retainedTotal)} <span class="badge bg-warning text-dark extra-small" style="font-size:0.65rem;vertical-align:middle;">RETAINED</span></span>`;
            if (refundRow && refundPayoutEl) {
                refundRow.classList.remove('d-none');
                refundPayoutEl.textContent = '-' + this.formatCurrency(refundedAmount);
            }
        } else {
            if (refundRow) {
                refundRow.classList.add('d-none');
            }
            this.summarySubtotal.textContent = this.formatCurrency(this.state.subtotal);
            this.summaryDiscount.textContent = this.formatCurrency(this.state.discount);
            this.summaryTotal.textContent = this.formatCurrency(this.state.total);
        }

        if (
            this.summarySubtotalModal
        ) {

            this.summarySubtotalModal.textContent =
                this.formatCurrency(
                    this.state.subtotal
                );

        }

        if (
            this.summaryDiscountModal
        ) {

            this.summaryDiscountModal.textContent =
                this.formatCurrency(
                    this.state.discount
                );

        }

        const modalTotalEl = document.getElementById('summaryTotalModal');
        if (modalTotalEl) {
            modalTotalEl.textContent = this.formatCurrency(this.state.total);
        }

        if (
            this.paymentTotal
        ) {

            this.paymentTotal.textContent =
                this.formatCurrency(
                    this.state.total
                );

        }

        if (
            this.amountTendered &&
            this.amountTendered.value
        ) {

            this.calculateChange();

        }

        this.updateDiscountProfitPreview();

        this.renderLiveReceiptPreview();

    },

    renderLiveReceiptPreview() {
        const container = document.getElementById('receiptItemsContainer');
        const subtotalEl = document.getElementById('receiptSubtotal');
        const discountRow = document.getElementById('receiptDiscountRow');
        const discountEl = document.getElementById('receiptDiscount');
        const totalEl = document.getElementById('receiptTotal');
        const vatableEl = document.getElementById('receiptVatable');
        const vatAmountEl = document.getElementById('receiptVatAmount');
        const customerNameEl = document.getElementById('receiptCustomerName');
        const printBtn = document.getElementById('btnPrintLiveReceipt');
        const receiptBadge = document.getElementById('receiptLiveBadge');

        const lastPaid = this.state.lastPaidSale;
        const isRefunded = Boolean(lastPaid?.is_refunded);
        const isPartial = Boolean(lastPaid?.is_partial);
        const refundedAmount = Number(lastPaid?.refunded_amount || 0);

        if (receiptBadge) {
            if (isRefunded) {
                receiptBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold px-2 py-0.5';
                receiptBadge.innerHTML = '<i class="bi bi-arrow-counterclockwise me-1"></i>REFUNDED';
            } else if (isPartial) {
                receiptBadge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small fw-bold px-2 py-0.5';
                receiptBadge.innerHTML = '<i class="bi bi-arrow-return-left me-1"></i>PARTIAL RETURN';
            } else if (this.state.isPaid || this._isSaleAlreadyPaid) {
                receiptBadge.className = 'badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5';
                receiptBadge.innerHTML = '<i class="bi bi-check-circle me-1"></i>PAID';
            } else {
                receiptBadge.className = 'badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5';
                receiptBadge.innerHTML = '<span class="spinner-grow spinner-grow-sm me-1" style="width: 6px; height: 6px;" role="status"></span>Live';
            }
        }

        if (customerNameEl) {
            customerNameEl.textContent = (this.state.customer && this.state.customer.name) ? this.state.customer.name : (this.state.customer_name || 'Walk-in Customer');
        }

        const origTotal = Number(this.state.total || 0);
        const origSubtotal = Number(this.state.subtotal || origTotal);
        const discount = Number(this.state.discount || 0);

        const retainedTotal = isRefunded ? 0 : (isPartial ? Number(lastPaid?.retained_total || Math.max(0, origTotal - refundedAmount)) : origTotal);
        const displaySubtotal = isRefunded ? 0 : (isPartial ? retainedTotal : origSubtotal);

        const vatableSales = (retainedTotal / 1.12).toFixed(2);
        const vatAmount = (retainedTotal - Number(vatableSales)).toFixed(2);

        if (subtotalEl) {
            if (isRefunded) {
                subtotalEl.innerHTML = `<del class="text-muted extra-small me-1">${this.formatCurrency(origSubtotal)}</del><span class="text-danger fw-bold">₱0.00</span>`;
            } else if (isPartial) {
                subtotalEl.innerHTML = `<del class="text-muted extra-small me-1">${this.formatCurrency(origSubtotal)}</del><span>${this.formatCurrency(displaySubtotal)}</span>`;
            } else {
                subtotalEl.textContent = this.formatCurrency(origSubtotal);
            }
        }

        if (totalEl) {
            if (isRefunded) {
                totalEl.innerHTML = `<del class="text-muted extra-small me-1 font-normal">${this.formatCurrency(origTotal)}</del><span class="text-danger fw-bold">₱0.00 (REFUNDED)</span>`;
            } else if (isPartial) {
                totalEl.innerHTML = `<del class="text-muted extra-small me-1 font-normal">${this.formatCurrency(origTotal)}</del><span class="text-dark fw-bold">${this.formatCurrency(retainedTotal)} (RETAINED)</span>`;
            } else {
                totalEl.textContent = this.formatCurrency(origTotal);
            }
        }

        if (vatableEl) vatableEl.textContent = this.formatCurrency(Number(vatableSales));
        if (vatAmountEl) vatAmountEl.textContent = this.formatCurrency(Number(vatAmount));

        if (discountRow && discountEl) {
            if (discount > 0 && !isRefunded) {
                discountRow.classList.remove('d-none');
                discountEl.textContent = '-' + this.formatCurrency(discount);
            } else {
                discountRow.classList.add('d-none');
            }
        }

        if (container) {
            if (!this.state.cart || this.state.cart.length === 0) {
                container.innerHTML = `
                    <tr>
                        <td colspan="2" class="text-center py-4 text-muted" style="font-size: 10.5px;">
                            (Receipt is empty)<br>
                            <small>Scan or add items</small>
                        </td>
                    </tr>
                `;
            } else {
                let bannerHtml = '';
                if (isRefunded) {
                    bannerHtml = `
                        <tr>
                            <td colspan="2" class="text-center py-1" style="background:#fee2e2;border:1px dashed #ef4444;color:#b91c1c;font-weight:bold;font-size:11px;letter-spacing:0.5px;">
                                *** FULLY REFUNDED SALE ***
                            </td>
                        </tr>
                    `;
                } else if (isPartial) {
                    bannerHtml = `
                        <tr>
                            <td colspan="2" class="text-center py-1" style="background:#fef3c7;border:1px dashed #f59e0b;color:#92400e;font-weight:bold;font-size:11px;letter-spacing:0.5px;">
                                *** PARTIAL RETURN SALE ***
                            </td>
                        </tr>
                    `;
                }

                const itemsRows = this.state.cart.map(item => {
                    const origQty = Number(item.original_qty ?? item.qty ?? 1);
                    const returnedQty = Number(item.returned_qty ?? (isRefunded ? origQty : 0));
                    const retainedQty = Number(item.retained_qty ?? (isRefunded ? 0 : Math.max(0, origQty - returnedQty)));
                    const isItemRefunded = Boolean(item.is_item_refunded || isRefunded || (origQty > 0 && returnedQty >= origQty));
                    const isItemPartial = Boolean(!isItemRefunded && (item.is_item_partial || returnedQty > 0));

                    const qtyStr = (Math.floor(origQty) === origQty) ? origQty.toFixed(0) : origQty.toString();
                    const unitPrice = Number(item.price || item.unit_price || 0);
                    const lineTotal = Number(item.subtotal || item.line_total || (origQty * unitPrice));
                    const retainedSubtotal = isItemRefunded ? 0 : (unitPrice * retainedQty);
                    const name = item.name || item.product_name || 'Item';
                    const unit = item.unit ? ` ${item.unit}` : '';

                    if (isItemRefunded) {
                        return `
                        <tr>
                            <td class="text-start fw-bold pt-1 text-decoration-line-through text-muted" style="font-size: 11px;">
                                ${name} <span style="color:#dc2626;font-size:9.5px;text-decoration:none;display:inline-block;font-weight:bold;">[REFUNDED]</span>
                            </td>
                            <td class="text-end fw-bold pt-1 text-danger" style="font-size: 11px; white-space: nowrap;">₱0.00</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-start pb-1 text-muted" style="font-size: 10px;">
                                &nbsp;&nbsp;${qtyStr}${unit} (Returned & Restocked)
                            </td>
                        </tr>
                        `;
                    }

                    if (isItemPartial) {
                        return `
                        <tr>
                            <td class="text-start fw-bold pt-1" style="font-size: 11px;">
                                ${name} <span style="color:#d97706;font-size:9.5px;font-weight:bold;">[${returnedQty} Ret.]</span>
                            </td>
                            <td class="text-end fw-bold pt-1" style="font-size: 11px; white-space: nowrap;">₱${retainedSubtotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-start pb-1" style="font-size: 10px; color: #444;">
                                &nbsp;&nbsp;${retainedQty} retained (${qtyStr} orig) @ ₱${unitPrice.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                            </td>
                        </tr>
                        `;
                    }

                    return `
                    <tr>
                        <td class="text-start fw-bold pt-1" style="font-size: 11px;">${name}</td>
                        <td class="text-end fw-bold pt-1" style="font-size: 11px; white-space: nowrap;">₱${lineTotal.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-start pb-1" style="font-size: 10px; color: #444;">
                            &nbsp;&nbsp;${qtyStr}${unit} @ ₱${unitPrice.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </td>
                    </tr>
                    `;
                }).join('');

                container.innerHTML = bannerHtml + itemsRows;
            }
        }


        if (printBtn) {
            printBtn.classList.toggle('d-none', !this.state.isPaid && !this._isSaleAlreadyPaid);
        }

        if (printBtn && !printBtn.dataset.bound) {
            printBtn.dataset.bound = 'true';
            printBtn.addEventListener('click', () => {
                const saleToPrint = this.state.lastPaidSale || {
                    items: this.state.cart,
                    total_amount: this.state.total,
                    subtotal: this.state.subtotal,
                    discount_amount: this.state.discount,
                    customer: this.state.customer,
                };
                if (!saleToPrint.items || saleToPrint.items.length === 0) {
                    alert('No completed transaction to print.');
                    return;
                }
                const html = window.buildBIRThermalReceiptHTML(saleToPrint);
                const printWin = window.open('', '_blank', 'width=380,height=600');
                if (printWin) {
                    printWin.document.write(html);
                    printWin.document.close();
                    printWin.focus();
                    setTimeout(() => {
                        printWin.print();
                    }, 300);
                }
            });
        }
    },

    // ─── MULTI-SALE ACTIVE TABS SYSTEM (BASED ON SALE_CODE) ────────
    bindOrderTabsEvents() {
        const btnNew = document.getElementById('btnNewOrderTab');
        if (btnNew && !btnNew.dataset.bound) {
            btnNew.dataset.bound = 'true';
            btnNew.addEventListener('click', () => this.createNewOrderTab());
        }
    },

    initMultiOrdersSystem() {
        const currentCode = this.saleCode || document.getElementById('saleCode')?.value || 'current';

        try {
            const raw = localStorage.getItem('pos_active_multi_orders');
            if (raw) {
                const parsed = JSON.parse(raw);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    this.state.orders = parsed;

                    // Find if current sale_code is already in tabs
                    let current = this.state.orders.find(o => o.sale_code === currentCode || o.id === currentCode);
                    if (!current) {
                        current = {
                            id: currentCode,
                            sale_code: currentCode,
                            sale_id: this.state.saleId,
                            label: `#${currentCode}`,
                            customer_name: 'Walk-in Customer',
                            customer_id: null,
                            customer: null,
                            cart: [],
                            priceMode: 'retail',
                            subtotal: 0,
                            discount: 0,
                            total: 0
                        };
                        this.state.orders.push(current);
                    }

                    this.state.activeOrderId = current.id;
                    this.loadOrderIntoCurrentState(current.id);
                    return;
                }
            }
        } catch (e) {
            console.warn('Failed to parse active multi-orders:', e);
        }

        // Initialize with current sale_code order
        const initialOrder = {
            id: currentCode,
            sale_code: currentCode,
            sale_id: this.state.saleId,
            label: `#${currentCode}`,
            customer_name: 'Walk-in Customer',
            customer_id: null,
            customer: null,
            cart: [],
            priceMode: 'retail',
            subtotal: 0,
            discount: 0,
            total: 0
        };
        this.state.orders = [initialOrder];
        this.state.activeOrderId = initialOrder.id;
        this.loadOrderIntoCurrentState(initialOrder.id);
        this.saveCurrentOrderToStorage();
    },

    saveCurrentOrderToStorage() {
        if (!this.state.orders || this.state.orders.length === 0) return;

        const current = this.state.orders.find(o => o.id === this.state.activeOrderId || o.sale_code === this.state.activeOrderId);
        if (current) {
            current.cart = this.state.cart || [];
            current.customer = this.state.customer || null;
            current.customer_name = this.state.customer?.CustomerName || this.state.customer_name || 'Walk-in Customer';
            current.customer_id = this.state.customer_id || null;
            current.priceMode = this.state.priceMode || 'retail';
            current.subtotal = this.state.subtotal || 0;
            current.discount = this.state.discount || 0;
            current.total = this.state.total || 0;
        }

        try {
            localStorage.setItem('pos_active_multi_orders', JSON.stringify(this.state.orders));
            localStorage.setItem('pos_current_active_order_id', this.state.activeOrderId);
        } catch (e) {
            console.warn('Failed to save multi orders to localStorage:', e);
        }

        this.renderOrderTabs();
    },

    loadOrderIntoCurrentState(orderId) {
        const order = this.state.orders?.find(o => o.id === orderId || o.sale_code === orderId);
        if (!order) return;

        this.state.activeOrderId = order.id;
        this.saleCode = order.sale_code || order.id;
        if (order.sale_id) this.state.saleId = order.sale_id;

        this.state.cart = JSON.parse(JSON.stringify(order.cart || []));
        this.state.customer = order.customer || null;
        this.state.customer_name = order.customer_name || 'Walk-in Customer';
        this.state.customer_id = order.customer_id || null;
        this.state.priceMode = order.priceMode || 'retail';

        // Update customer display in UI
        const cartCustomerNameEl = document.getElementById('cartCustomerName');
        if (cartCustomerNameEl) {
            cartCustomerNameEl.innerHTML = `<i class="bi bi-person me-1"></i>${this.state.customer_name}`;
        }

        // Update price mode switch UI
        const wsRadio = document.getElementById('priceModeWholesale');
        const retailRadio = document.getElementById('priceModeRetail');
        if (this.state.priceMode === 'wholesale' && wsRadio) wsRadio.checked = true;
        else if (retailRadio) retailRadio.checked = true;

        this.calculateTotals();
        this.renderCart();
        this.renderSummary();
        this.renderOrderTabs();
    },

    renderOrderTabs() {
        const container = document.getElementById('orderTabsContainer');
        if (!container || !this.state.orders) return;

        container.innerHTML = this.state.orders.map((order, idx) => {
            const isActive = order.id === this.state.activeOrderId || order.sale_code === this.state.activeOrderId;
            const itemCount = (order.cart || []).reduce((sum, item) => sum + (Number(item.qty) || 1), 0);
            const codeDisplay = order.sale_code ? `#${order.sale_code}` : `Sale ${idx + 1}`;
            const customerDisplay = (order.customer_name && order.customer_name !== 'Walk-in Customer') ? ` (${order.customer_name})` : '';
            const displayLabel = `${codeDisplay}${customerDisplay}`;

            return `
                <div class="btn-group btn-group-sm shadow-xs me-1 flex-shrink-0" role="group">
                    <button
                        type="button"
                        class="btn btn-sm ${isActive ? 'btn-primary fw-bold text-white' : 'btn-light border text-dark font-mono'} px-2.5 py-1 extra-small rounded-pill-start btn-switch-order-tab d-flex align-items-center gap-1.5"
                        data-id="${order.id}"
                        style="${isActive ? 'background:#2563eb;border-color:#2563eb;' : ''}"
                        title="Switch to ${displayLabel}"
                    >
                        <i class="bi ${isActive ? 'bi-bag-check-fill' : 'bi-bag'}"></i>
                        <span>${displayLabel}</span>
                        ${itemCount > 0 ? `<span class="badge ${isActive ? 'bg-white text-primary' : 'bg-warning text-dark'} rounded-pill ms-1 font-mono" style="font-size:0.65rem;">${itemCount}</span>` : ''}
                    </button>
                    ${this.state.orders.length > 1 ? `
                        <button
                            type="button"
                            class="btn btn-sm ${isActive ? 'btn-primary text-white' : 'btn-light border text-danger'} px-1.5 py-1 extra-small rounded-pill-end btn-close-order-tab"
                            data-id="${order.id}"
                            style="${isActive ? 'background:#1d4ed8;border-color:#1d4ed8;' : ''}"
                            title="Close / Void this sale"
                        >
                            <i class="bi bi-x fs-6 lh-1"></i>
                        </button>
                    ` : ''}
                </div>
            `;
        }).join('');

        // Attach click events to switch tabs
        container.querySelectorAll('.btn-switch-order-tab').forEach(btn => {
            btn.onclick = () => {
                const orderId = btn.dataset.id;
                this.switchOrderTab(orderId);
            };
        });

        // Attach click events to close tabs
        container.querySelectorAll('.btn-close-order-tab').forEach(btn => {
            btn.onclick = (e) => {
                e.stopPropagation();
                const orderId = btn.dataset.id;
                this.closeOrderTab(orderId);
            };
        });
    },

    async createNewOrderTab() {
        const currentOrder = this.state.orders.find(o => o.id === this.state.activeOrderId || o.sale_code === this.state.activeOrderId);
        if (this.state.cart && this.state.cart.length > 0 && currentOrder) {
            const defaultPromptVal = this.state.customer?.CustomerName || currentOrder.customer_name || '';

            const { value: labelInput, isConfirmed } = await Swal.fire({
                title: '🏷️ Customer / Order Indicator',
                text: `Add a customer name for current transaction #${currentOrder.sale_code || 'Sale'}:`,
                input: 'text',
                inputValue: defaultPromptVal === 'Walk-in Customer' ? '' : defaultPromptVal,
                inputPlaceholder: 'e.g. Juan / Customer in Red Shirt',
                showCancelButton: true,
                confirmButtonText: 'Next Sale ➔',
                confirmButtonColor: '#059669',
                cancelButtonText: 'Cancel'
            });

            if (!isConfirmed) return;

            if (labelInput && labelInput.trim()) {
                currentOrder.customer_name = labelInput.trim();
                currentOrder.label = `#${currentOrder.sale_code} - ${labelInput.trim()}`;
            }
        }

        // Save active state before creating new
        this.saveCurrentOrderToStorage();

        // Create actual new sale record in backend
        try {
            const res = await fetch('/sales/new-transaction', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ current_sale_id: this.state.saleId })
            });

            const data = await res.json();
            if (data.success && data.sale_code) {
                const newOrder = {
                    id: data.sale_code,
                    sale_code: data.sale_code,
                    sale_id: data.sale_id,
                    sale_url: data.sale_url,
                    label: `#${data.sale_code}`,
                    customer_name: 'Walk-in Customer',
                    customer_id: null,
                    customer: null,
                    cart: [],
                    priceMode: 'retail',
                    subtotal: 0,
                    discount: 0,
                    total: 0
                };

                this.state.orders.push(newOrder);
                this.state.activeOrderId = newOrder.id;
                this.state.saleId = data.sale_id;
                this.saleCode = data.sale_code;

                const saleIdInput = document.getElementById('saleId');
                if (saleIdInput) saleIdInput.value = data.sale_id;
                const saleCodeInput = document.getElementById('saleCode');
                if (saleCodeInput) saleCodeInput.value = data.sale_code;

                if (data.sale_url && window.history.replaceState) {
                    window.history.replaceState(null, '', data.sale_url);
                }

                this.loadOrderIntoCurrentState(newOrder.id);
                this.saveCurrentOrderToStorage();
                this.searchInput?.focus();
                return;
            }
        } catch (e) {
            console.warn('Failed to create server sale transaction, falling back to local:', e);
        }

        // Fallback local code
        const fallbackCode = `Sale ${this.state.orders.length + 1}`;
        const newOrder = {
            id: fallbackCode,
            sale_code: fallbackCode,
            label: fallbackCode,
            customer_name: 'Walk-in Customer',
            customer_id: null,
            customer: null,
            cart: [],
            priceMode: 'retail',
            subtotal: 0,
            discount: 0,
            total: 0
        };

        this.state.orders.push(newOrder);
        this.state.activeOrderId = newOrder.id;
        this.loadOrderIntoCurrentState(newOrder.id);
        this.saveCurrentOrderToStorage();
        this.searchInput?.focus();
    },

    switchOrderTab(orderId) {
        if (orderId === this.state.activeOrderId) return;

        // Save current tab's items first
        this.saveCurrentOrderToStorage();

        // Load targeted tab - MATIC NA AGAD LALABAS ANG LAHAT NG ITEMS!
        this.loadOrderIntoCurrentState(orderId);

        const targetOrder = this.state.orders.find(o => o.id === orderId || o.sale_code === orderId);
        if (targetOrder && targetOrder.sale_url && window.history.replaceState) {
            window.history.replaceState(null, '', targetOrder.sale_url);
        }

        this.saveCurrentOrderToStorage();
    },

    async closeOrderTab(orderId) {
        const order = this.state.orders.find(o => o.id === orderId);
        if (!order) return;

        if (order.cart && order.cart.length > 0) {
            const { isConfirmed } = await Swal.fire({
                title: 'Void / Close this Sale?',
                text: `Order "${order.label}" has ${order.cart.length} punched items. Are you sure you want to void and remove it?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Void Sale',
                confirmButtonColor: '#dc2626',
                cancelButtonText: 'Cancel'
            });

            if (!isConfirmed) return;
        }

        this.state.orders = this.state.orders.filter(o => o.id !== orderId);

        if (this.state.orders.length === 0) {
            // Re-create Sale 1
            const initialOrder = {
                id: 'ORDER_' + Date.now(),
                label: 'Sale 1',
                customer_name: 'Walk-in Customer',
                customer_id: null,
                customer: null,
                cart: [],
                priceMode: 'retail',
                subtotal: 0,
                discount: 0,
                total: 0
            };
            this.state.orders = [initialOrder];
            this.state.activeOrderId = initialOrder.id;
        } else if (this.state.activeOrderId === orderId) {
            this.state.activeOrderId = this.state.orders[0].id;
        }

        this.loadOrderIntoCurrentState(this.state.activeOrderId);
        this.saveCurrentOrderToStorage();
    }

};

document.addEventListener(
    'DOMContentLoaded',
    async () => {

        const posPage =
            document.getElementById(
                'productContainer'
            );

        if (!posPage) {
            return;
        }

        try {

            await POS.init();


        } catch (error) {

            console.error(
                'POS initialization failed:',
                error
            );

        }

    }
);


