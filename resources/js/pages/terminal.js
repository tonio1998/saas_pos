import ProductDB from './db.js';
import ProductService from './services/product.service.js';
import { Modal } from 'bootstrap';

$(function () {

    $('#btnSaveCustomer').on('click', function () {

        const $btn = $(this);

        $btn
            .prop('disabled', true)
            .html(
                '<span class="spinner-border spinner-border-sm me-2"></span>Saving...'
            );

        $.ajax({

            url: '/sales/customers/quick-store',

            type: 'POST',

            data: $('#newCustomerForm').serialize(),

            headers: {

                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content')

            },

            success(response) {

                if (!response.status) {

                    toastr.error(
                        response.message ??
                        'Unable to create customer.'
                    );

                    return;
                }

                const option = new Option(

                    response.customer.text,

                    response.customer.id,

                    true,

                    true

                );

                $('#Customer')
                    .append(option)
                    .trigger('change');

                $('#newCustomerCollapse')
                    .collapse('hide');

                $('#newCustomerForm')[0]
                    .reset();

                bootstrap.Modal
                    .getInstance(
                        document.getElementById('customerModal')
                    )
                    .hide();

                toastr.success(
                    response.message
                );

            },

            error(xhr) {

                if (xhr.status === 422) {

                    const errors =
                        xhr.responseJSON.errors;

                    Object.values(errors).forEach(function (messages) {

                        toastr.error(
                            messages[0]
                        );

                    });

                } else {

                    toastr.error(
                        'An unexpected error occurred.'
                    );

                }

            },

            complete() {

                $btn
                    .prop('disabled', false)
                    .html(
                        '<i class="bi bi-person-plus-fill me-1"></i>Create & Select Customer'
                    );

            }

        });

    });

});

const POS = {

    state: {
        cart: [],
        customer_id: null,
        saleId: document.getElementById('saleId')?.value || 0,
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

        const container =
            document.getElementById(
                'productContainer'
            );

        if (!container) {
            return;
        }

        if (
            !products ||
            products.length === 0
        ) {

            container.innerHTML = `
            <div class="empty-products">
                No products found
            </div>
        `;

            return;
        }

        container.innerHTML =
            products.map(product => {

                const stock =
                    Number(
                        product.stock_on_hand ?? 0
                    );

                let stockBadge = '';

                if (stock <= 0) {

                    stockBadge = `
                <span class="stock-badge out">
                    Out of Stock
                </span>
            `;

                } else if (stock <= 10) {

                    stockBadge = `
                <span class="stock-badge low">
                    ${stock} Left
                </span>
            `;

                } else {

                    stockBadge = `
                <span class="stock-badge in">
                    ${stock} Available
                </span>
            `;

                }

                console.log(product);

                return `

            <div
                class="product-card"
                data-id="${product.id}"
                data-name="${product.name}"
                data-price="${product.selling_price}"
                data-stock="${stock}"
                data-barcode="${product.barcode ?? ''}"
                data-category="${product.category_id ?? ''}"
            >

                <div class="product-image">

                    <img
                        src="${
                                        product.image
                                            ? `/storage/${product.image}?v=${new Date(product.updated_at).getTime()}`
                                            : '/images/no_image.jpg'
                                    }"
                        alt="${product.name}"
                    >

                </div>

                <div class="product-info">

                    <div class="product-name">

                        ${product.name}

                    </div>

                    <div class="product-stock">

                        <span class="stock-label">

                            Stock:

                        </span>

                        ${stockBadge}

                    </div>

                    <div class="product-bottom">

                        <div class="product-price">

                            ${this.formatCurrency(
                                product.selling_price
                            )}

                        </div>

                    </div>

                </div>

            </div>

        `;

            }).join('');

        this.productCards =
            container.querySelectorAll(
                '.product-card'
            );

        this.buildProductCache();

        this.bindProductEvents();

    },
    bindProductEvents() {

        this.productCards.forEach(card => {

            card.addEventListener(
                'click',
                () => {

                    this.addToCart({

                        id:
                            Number(
                                card.dataset.id
                            ),

                        barcode:
                        card.dataset.barcode,

                        name:
                        card.dataset.name,

                        price:
                            Number(
                                card.dataset.price
                            ),

                        stock:
                            Number(
                                card.dataset.stock || 0
                            ),

                    });

                }
            );

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

        this.btnAddPayment =
            document.getElementById(
                'btnAddPayment'
            );
    },
    buildProductCache() {

        this.productCards.forEach(card => {

            const barcode =
                card.dataset.barcode;

            if (!barcode) {
                return;
            }

            this.state.productMap[
                barcode
                ] = card;

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

        this.bindKeyboardShortcuts();
        this.bindCheckout();
        this.bindDiscount();
        this.bindCashButtons();
        this.bindSplitPayments();
        this.bindForceRefresh();
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

        this.paymentLines.innerHTML =
            this.state.payments
                .map((payment, index) => `

<div
    class="payment-row card shadow-sm mb-2"
>

    <div class="card-body">

        <div class="row g-2">

            <div class="col-md-4">

                <select
                    class="form-select payment-method"
                    data-index="${index}"
                >

                    <option
                        value="cash"
                        ${payment.method === 'cash' ? 'selected' : ''}
                    >
                        Cash
                    </option>

                    <option
                        value="gcash"
                        ${payment.method === 'gcash' ? 'selected' : ''}
                    >
                        GCash
                    </option>

                    <option
                        value="bank_transfer"
                        ${payment.method === 'bank_transfer' ? 'selected' : ''}
                    >
                        Bank Transfer
                    </option>

                </select>

            </div>

            <div class="col-md-4">

                <input
                    type="number"
                    class="form-control payment-amount"
                    data-index="${index}"
                    value="${payment.amount || ''}"
                    placeholder="Amount"
                >

            </div>

            <div class="col-md-3">

                <input
                    type="text"
                    class="form-control payment-reference"
                    data-index="${index}"
                    value="${payment.reference_number || ''}"
                    placeholder="Reference"
                >

            </div>

            <div class="col-md-1">

                <button
                    class="btn btn-danger remove-payment"
                    data-index="${index}"
                >
                    ×
                </button>

            </div>

        </div>

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

                            this.amountTendered.value =
                                amount;

                            this.calculateChange();

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

    },
    async completeSale() {

        if (
            !this.validateCheckout()
        ) {
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

            Modal.getInstance(this.paymentModal)?.hide();

            await this.updateCachedStocks();

            this.printReceipt(result);

            this.reset();

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

${
            this.discountHolder?.value
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

${
            this.discountType?.value
                ? `
<div>
    Discount Type:
    ${this.discountType.value.toUpperCase()}
</div>
`
                : ''
        }

${
            this.discountIdNo?.value
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

        ${
            payment.payment_method
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

${
            payment.reference_number
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

${
            this.paymentNotes?.value?.trim()
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

            alert(
                'Insufficient payment.'
            );

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

            if (
                payment.method !== 'cash' &&
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
            this.state.cart.length === 0
        ) {

            alert(
                'Cart is empty.'
            );

            return;

        }

        this.discountType.value = '';

        this.discountMode.value =
            'percentage';

        this.discountValue.value = '';

        this.discountHolder.value = '';

        this.discountIdNo.value = '';

        this.paymentNotes.value = '';

        this.manualDiscountSection
            ?.classList.add(
            'd-none'
        );

        this.discountInfoSection
            ?.classList.add(
            'd-none'
        );

        this.calculateTotals();

        this.state.payments = [

            {
                id: Date.now(),
                method: 'cash',
                amount: 0,
                reference_number: ''
            }

        ];

        this.state.paid = 0;

        this.state.balance =
            this.state.total;

        this.state.change = 0;

        this.renderPaymentLines();

        this.calculatePayments();

        this.renderSummary();

        this.paymentTotal.textContent =
            this.formatCurrency(
                this.state.total
            );

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
                    e.target.value.trim();

                if (!barcode) {
                    return;
                }

                const card =
                    this.state.productMap[
                        barcode
                        ];

                if (!card) {

                    e.target.select();

                    return;
                }

                card.click();

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

        const existing =
            this.state.cart.find(
                item => item.id === product.id
            );

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

            existing.qty++;

            existing.subtotal =
                existing.qty *
                existing.price;

        } else {

            this.state.cart.push({

                id: product.id,

                barcode: product.barcode,

                name: product.name,

                price: product.price,

                stock: product.stock,

                qty: 1,

                subtotal: product.price,

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
            ${
            new Date()
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
        productId,
        quantity
    ) {

        const item =
            this.state.cart.find(
                row => row.id === productId
            );

        if (!item) {
            return;
        }

        quantity =
            Number(quantity);

        if (quantity <= 0) {

            this.removeItem(
                productId
            );

            return;
        }

        if (
            quantity > item.stock
        ) {
            quantity =
                item.stock;
        }

        item.qty =
            quantity;

        item.subtotal =
            item.qty *
            item.price;

        this.calculateTotals();

        this.renderCart();

    },

    removeItem(
        productId
    ) {

        this.state.cart =
            this.state.cart.filter(
                item =>
                    item.id !== productId
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
                <div class="empty-cart">
                    <i class="bi bi-cart"></i>
                    <span>No Items</span>
                </div>
            `;

            this.renderSummary();

            return;

        }

        this.cartItemsList.innerHTML =
            this.state.cart
                .map(item => {

                    return `
<div
    class="cart-item"
    data-id="${item.id}"
>

    <div class="cart-item-header">

        <div class="cart-item-name">
            ${item.name}
        </div>

        <button
            class="remove-item"
            data-id="${item.id}"
        >
            <i class="bi bi-trash"></i>
        </button>

    </div>

    <div class="cart-item-summary">

        ₱${item.price.toFixed(2)}
        ×
        ${item.qty}
        =
        ₱${item.subtotal.toFixed(2)}

    </div>

    <div class="cart-item-actions">

        <button
            class="qty-minus"
            data-id="${item.id}"
        >
            -
        </button>

        <input
            type="number"
            class="qty-input"
            data-id="${item.id}"
            value="${item.qty}"
            min="1"
        >

        <button
            class="qty-plus"
            data-id="${item.id}"
        >
            +
        </button>

    </div>

</div>
`;

                })
                .join('');

        this.attachCartEvents();

        this.renderSummary();

    },

    attachCartEvents() {

        document
            .querySelectorAll('.qty-minus')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        const id =
                            Number(
                                button.dataset.id
                            );

                        const item =
                            this.state.cart.find(
                                row =>
                                    row.id === id
                            );

                        if (!item) {
                            return;
                        }

                        this.updateQuantity(
                            id,
                            item.qty - 1
                        );

                    }
                );

            });

        document
            .querySelectorAll('.qty-plus')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        const id =
                            Number(
                                button.dataset.id
                            );

                        const item =
                            this.state.cart.find(
                                row =>
                                    row.id === id
                            );

                        if (!item) {
                            return;
                        }

                        this.updateQuantity(
                            id,
                            item.qty + 1
                        );

                    }
                );

            });

        document
            .querySelectorAll('.qty-input')
            .forEach(input => {

                input.addEventListener(
                    'change',
                    () => {

                        this.updateQuantity(
                            Number(
                                input.dataset.id
                            ),
                            input.value
                        );

                    }
                );

            });

        document
            .querySelectorAll('.remove-item')
            .forEach(button => {

                button.addEventListener(
                    'click',
                    () => {

                        this.removeItem(
                            Number(
                                button.dataset.id
                            )
                        );

                    }
                );

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


