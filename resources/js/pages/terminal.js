import ProductDB from './db.js';
import ProductService from './services/product.service.js';
import { Modal } from 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
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

        await this.loadProducts();

        this.events();

        this.renderCart();

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
            const tableRows = products.map(product => {
                const stock = Number(product.stock_on_hand ?? 0);
                let stockBadge = '';
                if (stock <= 0) {
                    stockBadge = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-x-circle me-1"></i>Out of Stock</span>`;
                } else if (stock <= 10) {
                    stockBadge = `<span class="badge bg-warning-subtle text-warning border border-warning-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-exclamation-circle me-1"></i>${stock} Left</span>`;
                } else {
                    stockBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5 rounded-pill"><i class="bi bi-check-circle me-1"></i>${stock} Available</span>`;
                }

                const isWholesale = this.state.priceMode === 'wholesale';
                const retailPrice = Number(product.selling_price || 0);
                const wholesalePrice = Number(product.wholesale_price || 0);
                const activePrice = (isWholesale && wholesalePrice > 0) ? wholesalePrice : retailPrice;
                const hasVariants = product.variants && Array.isArray(product.variants) && product.variants.length > 0;

                let priceDisplay = '';
                if (isWholesale && wholesalePrice > 0) {
                    priceDisplay = `
                        <div class="d-flex align-items-baseline justify-content-end gap-1">
                            <span class="font-mono fw-black text-primary" style="font-size:0.92rem;">${this.formatCurrency(wholesalePrice)}</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" style="font-size:0.6rem;">WS</span>
                        </div>
                    `;
                } else if (wholesalePrice > 0) {
                    priceDisplay = `
                        <div class="text-end lh-sm">
                            <div class="font-mono fw-black text-emerald" style="font-size:0.92rem;">${this.formatCurrency(retailPrice)}</div>
                            <small class="text-muted extra-small font-mono" style="font-size:0.68rem;">WS: ${this.formatCurrency(wholesalePrice)}</small>
                        </div>
                    `;
                } else {
                    priceDisplay = `<div class="font-mono fw-black text-emerald" style="font-size:0.92rem;">${this.formatCurrency(retailPrice)}</div>`;
                }

                let variantBadge = '';
                if (hasVariants) {
                    variantBadge = `
                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle extra-small" style="background:#f3e8ff;color:#7e22ce;border-color:#e9d5ff;font-size:0.68rem;">
                            <i class="bi bi-layers-fill me-1"></i>${product.variants.length} Options
                        </span>
                    `;
                }

                const imgUrl = product.image
                    ? `/storage/${product.image}?v=${new Date(product.updated_at).getTime()}`
                    : '/images/no_image.jpg';

                return `
                    <tr
                        class="product-row ${hasVariants ? 'has-variants' : ''} cursor-pointer"
                        data-id="${product.id}"
                        data-name="${product.name}"
                        data-price="${activePrice}"
                        data-retail-price="${retailPrice}"
                        data-wholesale-price="${wholesalePrice}"
                        data-stock="${stock}"
                        data-barcode="${product.barcode ?? ''}"
                        data-category="${product.category_id ?? ''}"
                        data-has-variants="${hasVariants ? '1' : '0'}"
                    >
                        <td class="align-middle py-1.5 px-2.5" style="width: 44px;">
                            <img src="${imgUrl}" alt="" class="rounded-2 border shadow-xs" style="width:36px;height:36px;object-fit:cover;background:#f8fafc;flex-shrink:0;">
                        </td>
                        <td class="align-middle py-1.5 px-2.5">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 360px; font-size: 0.88rem;">${product.name}</div>
                            <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                ${product.barcode ? `<span class="badge bg-light text-muted border font-mono extra-small" style="font-size:0.7rem;padding:2px 6px;">${product.barcode}</span>` : ''}
                                ${variantBadge}
                            </div>
                        </td>
                        <td class="align-middle py-1.5 px-2.5" style="width: 130px;">
                            ${stockBadge}
                        </td>
                        <td class="align-middle py-1.5 px-2.5 text-end" style="width: 130px;">
                            ${priceDisplay}
                        </td>
                        <td class="align-middle py-1.5 px-2.5 text-center" style="width: 80px;">
                            <button type="button" class="btn btn-sm btn-success rounded-3 px-2.5 py-1 extra-small font-mono fw-extrabold shadow-xs" style="background:#059669;border:none;">
                                <i class="bi bi-plus-lg me-1"></i>Add
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');

            container.innerHTML = `
                <div class="table-responsive bg-white rounded-3 border shadow-xs overflow-hidden w-100">
                    <table class="table table-hover align-middle mb-0 pos-product-table">
                        <tbody class="border-top-0">
                            ${tableRows}
                        </tbody>
                    </table>
                </div>
            `;
            container.className = 'products-table-wrapper w-100 flex-grow-1 overflow-y-auto border-radius-0';
        } else {
            // Card Grid View
            const cardsHtml = products.map(product => {
                const stock = Number(product.stock_on_hand ?? 0);
                let stockBadge = '';
                if (stock <= 0) {
                    stockBadge = `<span class="stock-badge out">Out of Stock</span>`;
                } else if (stock <= 10) {
                    stockBadge = `<span class="stock-badge low">${stock} Left</span>`;
                } else {
                    stockBadge = `<span class="stock-badge in">${stock} Available</span>`;
                }

                const isWholesale = this.state.priceMode === 'wholesale';
                const retailPrice = Number(product.selling_price || 0);
                const wholesalePrice = Number(product.wholesale_price || 0);
                const activePrice = (isWholesale && wholesalePrice > 0) ? wholesalePrice : retailPrice;
                const hasVariants = product.variants && Array.isArray(product.variants) && product.variants.length > 0;

                let priceDisplay = '';
                if (isWholesale && wholesalePrice > 0) {
                    priceDisplay = `
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="product-price text-primary fw-bold">${this.formatCurrency(wholesalePrice)}</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" style="font-size:0.65rem;">Wholesale</span>
                        </div>
                    `;
                } else if (wholesalePrice > 0) {
                    priceDisplay = `
                        <div>
                            <div class="product-price">${this.formatCurrency(retailPrice)}</div>
                            <small class="text-muted extra-small" style="font-size:0.7rem;">WS: ${this.formatCurrency(wholesalePrice)}</small>
                        </div>
                    `;
                } else {
                    priceDisplay = `<div class="product-price">${this.formatCurrency(retailPrice)}</div>`;
                }

                let variantBadge = '';
                if (hasVariants) {
                    variantBadge = `
                        <span class="badge bg-purple-subtle text-purple border border-purple-subtle extra-small mt-1" style="background:#f3e8ff;color:#7e22ce;border-color:#e9d5ff;font-size:0.68rem;">
                            <i class="bi bi-layers-fill me-1"></i>${product.variants.length} Options
                        </span>
                    `;
                }

                return `
                    <div
                        class="product-card ${hasVariants ? 'has-variants' : ''}"
                        data-id="${product.id}"
                        data-name="${product.name}"
                        data-price="${activePrice}"
                        data-retail-price="${retailPrice}"
                        data-wholesale-price="${wholesalePrice}"
                        data-stock="${stock}"
                        data-barcode="${product.barcode ?? ''}"
                        data-category="${product.category_id ?? ''}"
                        data-has-variants="${hasVariants ? '1' : '0'}"
                    >
                        <div class="product-image">
                            <img src="${product.image ? `/storage/${product.image}?v=${new Date(product.updated_at).getTime()}` : '/images/no_image.jpg'}" alt="${product.name}">
                        </div>
                        <div class="product-info">
                            <div class="product-name text-truncate">${product.name}</div>
                            <div class="product-stock">
                                <span class="stock-label"> Stock:</span>
                                ${stockBadge}
                            </div>
                            <div class="product-bottom d-flex align-items-center justify-content-between">
                                ${priceDisplay}
                                ${variantBadge}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = cardsHtml;
            container.className = 'products-grid flex-grow-1 overflow-y-auto p-3';
        }

        this.productCards = container.querySelectorAll('.product-card, .product-row');
        this.buildProductCache();
        this.bindProductEvents();
    },
    bindProductEvents() {

        this.productCards.forEach(card => {

            card.addEventListener(
                'click',
                () => {

                    const productId = Number(card.dataset.id);
                    const product = this.products?.find(p => p.id === productId);

                    if (product && product.variants && Array.isArray(product.variants) && product.variants.length > 0) {
                        this.showVariantModal(product);
                        return;
                    }

                    const retail = Number(card.dataset.retailPrice || card.dataset.price);
                    const wholesale = Number(card.dataset.wholesalePrice || 0);
                    const activePrice = (this.state.priceMode === 'wholesale' && wholesale > 0) ? wholesale : retail;

                    this.addToCart({
                        id: productId,
                        variant_id: null,
                        barcode: card.dataset.barcode,
                        name: card.dataset.name,
                        unit: product?.unit?.name || '',
                        allow_decimal_qty: product?.allow_decimal_qty ?? false,
                        retail_price: retail,
                        wholesale_price: wholesale,
                        price: activePrice,
                        stock: Number(card.dataset.stock || 0),
                    });

                }
            );

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
                this.addToCart({
                    id: Number(itemEl.dataset.productId),
                    variant_id: itemEl.dataset.variantId ? Number(itemEl.dataset.variantId) : null,
                    name: itemEl.dataset.name,
                    unit: itemEl.dataset.unit || '',
                    barcode: itemEl.dataset.barcode,
                    allow_decimal_qty: product?.allow_decimal_qty ?? false,
                    retail_price: Number(itemEl.dataset.retailPrice),
                    wholesale_price: Number(itemEl.dataset.wholesalePrice),
                    price: Number(itemEl.dataset.price),
                    stock: Number(itemEl.dataset.stock || 0),
                });
            });
        });
    },
    async loadProducts() {

        let products =
            await ProductService.getCached();

        if (
            !products ||
            products.length === 0
        ) {

            products =
                await ProductService.sync();

        }

        this.products =
            products;

        this.renderProducts(
            products
        );

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
        this.bindViewMode();
    },
    bindViewMode() {
        const btnViewTable = document.getElementById('btnViewTable');
        const btnViewGrid = document.getElementById('btnViewGrid');
        if (btnViewTable && btnViewGrid) {
            btnViewTable.addEventListener('click', () => {
                this.state.viewMode = 'table';
                localStorage.setItem('pos_view_mode', 'table');
                this.renderProducts(this.products);
            });

            btnViewGrid.addEventListener('click', () => {
                this.state.viewMode = 'grid';
                localStorage.setItem('pos_view_mode', 'grid');
                this.renderProducts(this.products);
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
                this.renderProducts(this.products);
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

        this.btnAddPayment?.addEventListener(
            'click',
            () => {

                this.state.payments.push({

                    id: Date.now(),

                    method: 'cash',

                    amount: 0,

                    reference_number: ''

                });

                this.renderPaymentLines();

            }
        );

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

        this.paymentChange.textContent =
            this.formatCurrency(
                this.state.change
            );

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
                this.printReceipt(result);
                this.reset();
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

        const products =
            await ProductDB.getProducts();

        const updatedProducts =
            products.map(product => {

                const soldItem =
                    this.state.cart.find(
                        item =>
                            item.id === product.id
                    );

                if (!soldItem) {
                    return product;
                }

                return {
                    ...product,
                    stock_on_hand: Math.max(
                        0,
                        Number(product.stock_on_hand || 0) -
                        Number(soldItem.qty || 0)
                    )
                };

            });

        await ProductDB.saveProducts(
            updatedProducts
        );

        this.products =
            updatedProducts;

        this.renderProducts(
            updatedProducts
        );

    },
    reset() {

        this.state.cart = [];

        this.state.subtotal = 0;

        this.state.discount = 0;

        this.state.total = 0;

        this.state.paid = 0;

        this.state.balance = 0;

        this.state.change = 0;

        this.state.payments = [];

        this.state.customer_id = null;

        this.state.customer_name = null;

        // Allow fresh payment init on next checkout
        this._freshCheckout = true;

        this.renderCart();

        this.renderSummary();

        this.searchInput.value = '';

        this.searchInput.focus();

        this.discountType.value = '';

        this.discountMode.value =
            'percentage';

        this.discountValue.value = '';

        this.discountHolder.value = '';

        this.discountIdNo.value = '';

        this.paymentNotes.value = '';

        if (
            this.paymentLines
        ) {

            this.paymentLines.innerHTML = '';

        }

        // Reset cart customer display
        const cartCustomerName = document.getElementById('cartCustomerName');
        if (cartCustomerName) {
            cartCustomerName.innerHTML = '<i class="bi bi-person me-1"></i>Walk-in Customer';
        }

        // Reset pricing mode to Retail
        this.state.priceMode = 'retail';
        const retailRadio = document.getElementById('priceModeRetail');
        if (retailRadio) retailRadio.checked = true;

    },
    printReceipt(sale) {

        const payments =
            sale.payments || [];

        const totalPaid =
            payments.reduce(
                (sum, payment) =>
                    sum +
                    Number(
                        payment.amount || 0
                    ),
                0
            );

        const receipt =
            window.open(
                '',
                '_blank',
                'width=300,height=900'
            );

        receipt.document.write(`

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>
Receipt
</title>

<style>
@page {
    size: 70mm auto;
    margin: 0;
}

html,
body {
    width: 70mm;
    margin: 0;
    font-family: monospace;
    font-size: 16px;
    box-sizing: border-box;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


.center {
    text-align: center;
}

.right {
    text-align: right;
}

.line {

    border-top:
        1px dashed #000;

    margin:
        6px 0;

}

.row {

    display: flex;

    justify-content:
        space-between;

    gap: 10px;

}

.item {

    margin-bottom:
        6px;

}

.item-name {

    font-weight:
        bold;

}

.footer {

    text-align:
        center;

    margin-top:
        10px;

}

.total {

    font-size:
        14px;

    font-weight:
        bold;

}

</style>

</head>

<body>

<div class="center">
    <div style="font-size:16px;font-weight:bold;">
        BaryaPOS
    </div>

    <div>
        Your Store Name
    </div>

    <div>
        Invoice #${sale.invoice_no ?? '-'}
    </div>
</div>

<div class="line"></div>

<div>

    Date:
    ${new Date().toLocaleString()}

</div>

<div>

    Cashier:
    ${sale.cashier_name ?? ''}

</div>

${this.discountHolder?.value
                ? `
<div>
    Customer:
    ${this.discountHolder.value}
</div>
`
                : ''
            }

<div class="line"></div>

${this.state.cart.map(item => `

<div class="item">

    <div class="item-name">
        ${item.name}
    </div>

    <div class="row">

        <span>

            ${item.qty}
            ×
            ${this.formatCurrency(
                item.price
            )}

        </span>

        <span>

            ${this.formatCurrency(
                item.subtotal
            )}

        </span>

    </div>

</div>

`).join('')}

<div class="line"></div>

<div class="row">

    <span>
        Subtotal
    </span>

    <span>
        ${this.formatCurrency(
                this.state.subtotal
            )}
    </span>

</div>

<div class="row">

    <span>
        Discount
    </span>

    <span>
        ${this.formatCurrency(
                this.state.discount
            )}
    </span>

</div>

${this.discountType?.value
                ? `
<div>
    Discount Type:
    ${this.discountType.value.toUpperCase()}
</div>
`
                : ''
            }

${this.discountIdNo?.value
                ? `
<div>
    ID No:
    ${this.discountIdNo.value}
</div>
`
                : ''
            }

<div class="row total">

    <span>
        TOTAL
    </span>

    <span>
        ${this.formatCurrency(
                this.state.total
            )}
    </span>

</div>

<div class="line"></div>

<div>

    <strong>
        PAYMENTS
    </strong>

</div>

${payments.map(payment => `

<div class="row">

    <span>

        ${payment.payment_method
                    ?.replace(
                        '_',
                        ' '
                    )
                    .toUpperCase()
                }

    </span>

    <span>

        ${this.formatCurrency(
                    payment.amount
                )}

    </span>

</div>

${payment.reference_number
                    ? `
<div>
    Ref:
    ${payment.reference_number}
</div>
`
                    : ''
                }

`).join('')}

<div class="line"></div>

<div class="row">

    <strong>
        Paid
    </strong>

    <strong>
        ${this.formatCurrency(
                    totalPaid
                )}
    </strong>

</div>

<div class="row">

    <strong>
        Change
    </strong>

    <strong>
        ${this.formatCurrency(
                    this.state.change
                )}
    </strong>

</div>

<div class="line"></div>

${this.paymentNotes?.value?.trim()
                ? `
<div>
    Notes:
</div>

<div>
    ${this.paymentNotes.value}
</div>

<div class="line"></div>
`
                : ''
            }

<div class="footer">

    Thank You!

    <br>

    Please Come Again

</div>

<script>

window.onload = () => {

    window.print();

    setTimeout(
        () => window.close(),
        500
    );

};

</script>

</body>

</html>

`);

        receipt.document.close();

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

    },
    validateCheckout() {

        if (
            this.state.cart.length === 0
        ) {

            alert(
                'Cart is empty.'
            );

            return false;

        }

        const discountType =
            this.discountType?.value;

        if (
            [
                'senior',
                'pwd',
                'student',
                'employee'
            ].includes(
                discountType
            )
        ) {

            if (
                !this.discountHolder.value.trim()
            ) {

                alert(
                    'Customer name is required.'
                );

                return false;

            }

            if (
                !this.discountIdNo.value.trim()
            ) {

                alert(
                    'ID number is required.'
                );

                return false;

            }

        }

        if (
            !this.state.payments ||
            this.state.payments.length === 0
        ) {

            alert(
                'No payment entered.'
            );

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
            notes: this.paymentNotes?.value?.trim() || null,
            payments: this.state.payments,
            paid: this.state.paid,
            change: this.state.change,
            items:
                this.state.cart.map(
                    item => ({

                        product_id:
                            item.id,

                        qty:
                            item.qty,

                        price:
                            item.price,

                    })
                ),

        };

    },
    openCheckout() {

        if (
            !this.state.cart ||
            this.state.cart.length === 0
        ) {

            alert(
                'Cart is empty.'
            );

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
    bindSearch() {

        if (!this.searchInput) {
            return;
        }

        this.searchInput.addEventListener(
            'input',
            e => {

                const keyword =
                    e.target.value
                        .trim()
                        .toLowerCase();

                this.productCards.forEach(card => {

                    const name =
                        (
                            card.dataset.name || ''
                        ).toLowerCase();

                    const barcode =
                        (
                            card.dataset.barcode || ''
                        ).toLowerCase();

                    const visible =
                        name.includes(keyword) ||
                        barcode.includes(keyword);

                    card.style.display =
                        visible
                            ? ''
                            : 'none';

                });

            }
        );

    },
    bindCategories() {

        this.categoryChips.forEach(chip => {

            chip.addEventListener(
                'click',
                () => {

                    this.categoryChips.forEach(
                        button =>
                            button.classList.remove(
                                'active'
                            )
                    );

                    chip.classList.add(
                        'active'
                    );

                    const category =
                        chip.dataset.category;

                    this.filterCategory(
                        category
                    );

                }
            );

        });

    },
    filterCategory(
        categoryId
    ) {

        this.productCards.forEach(card => {

            if (
                !categoryId
            ) {

                card.style.display = '';

                return;

            }

            const cardCategory =
                card.dataset.category;

            card.style.display =
                cardCategory === categoryId
                    ? ''
                    : 'none';

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

    addToCart(product) {

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

        this.cartItemsList.innerHTML =
            this.state.cart
                .map(item => {

                    const isWholesaleItem = this.state.priceMode === 'wholesale' && item.wholesale_price > 0 && item.price === item.wholesale_price;
                    const badgeHtml = isWholesaleItem ? '<span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small ms-1.5" style="font-size:0.65rem;">Wholesale</span>' : '';
                    const itemKey = item.cartKey || (item.id + '_' + (item.variant_id || 0));

                    return `
<div class="receipt-item py-2.5 px-3 border-bottom bg-white" style="border-bottom: 1px dashed #cbd5e1 !important;" data-key="${itemKey}" data-id="${item.id}">
    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
        <div class="receipt-item-title fw-bold text-dark lh-sm flex-grow-1" style="font-size: 0.92rem; color: #0f172a;">
            ${item.name} ${badgeHtml}
        </div>
        <div class="receipt-item-total fw-extrabold text-dark font-mono text-end" style="font-size: 1.05rem; font-weight: 900; color: #0f172a; white-space: nowrap; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;">
            ₱${item.subtotal.toFixed(2)}
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-1">
        <div class="receipt-item-calc text-muted font-mono" style="font-size: 0.82rem; font-weight: 600; color: #64748b;">
            ₱${item.price.toFixed(2)} / ${item.unit || 'unit'}
        </div>

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

        this.summarySubtotal.textContent =
            this.formatCurrency(
                this.state.subtotal
            );

        this.summaryDiscount.textContent =
            this.formatCurrency(
                this.state.discount
            );

        this.summaryTotal.textContent =
            this.formatCurrency(
                this.state.total
            );

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

    },

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


