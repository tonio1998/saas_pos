import ProductDB from './db.js';
import ProductService from './services/product.service.js';

const POS = {

    state: {

        cart: [],

        customer: null,

        subtotal: 0,

        discount: 0,

        total: 0,
        productMap: {},
        paymentMethod: 'cash',

        tendered: 0,

        change: 0,

    },

    async init() {

        this.cache();

        await ProductDB.init();

        await this.loadProducts();

        this.events();

        this.renderCart();

        this.searchInput?.focus();

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
            products.map(product => `

            <div
                class="product-card"
                data-id="${product.id}"
                data-name="${product.name}"
                data-price="${product.selling_price}"
                data-stock="${product.stock_on_hand ?? 0}"
                data-barcode="${product.barcode ?? ''}"
                data-category="${product.category_id ?? ''}"
            >

                <div class="product-image">

                    <img
                        src="${
                product.image
                    ? product.image
                    : '/images/no_image.jpg'
            }"
                        alt="${product.name}"
                    >

                </div>

                <div class="product-info">

                    <div class="product-name">
                        ${product.name}
                    </div>

                    <div class="product-price">
                        ₱${Number(
                product.selling_price
            ).toFixed(2)}
                    </div>

                </div>

            </div>

        `).join('');

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

    },
    async completeSale() {

        if (
            !this.validateCheckout()
        ) {
            return;
        }

        try {

            this.btnConfirmPayment.disabled =
                true;

            const payload =
                this.buildPayload();

            const response =
                await fetch(
                    '/pos/sales',
                    {
                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .content,

                        },

                        body:
                            JSON.stringify(
                                payload
                            ),

                    }
                );

            const result =
                await response.json();

            if (
                !response.ok
            ) {

                throw new Error(
                    result.message ||
                    'Checkout failed.'
                );

            }

            bootstrap.Modal
                .getInstance(
                    this.paymentModal
                )
                .hide();

            this.printReceipt(
                result
            );

            this.reset();

        } catch (error) {

            alert(
                error.message
            );

        } finally {

            this.btnConfirmPayment.disabled =
                false;

        }

    },
    reset() {

        this.state.cart = [];

        this.state.subtotal = 0;

        this.state.discount = 0;

        this.state.total = 0;

        this.state.tendered = 0;

        this.state.change = 0;

        this.renderCart();

        this.amountTendered.value = '';

        this.searchInput.value = '';

        this.searchInput.focus();

    },
    printReceipt(
        sale
    ) {

        const receipt =
            window.open(
                '',
                '_blank',
                'width=400,height=700'
            );

        receipt.document.write(`

        <html>

        <head>

            <title>

                Receipt

            </title>

        </head>

        <body>

            <h3>

                CatchuPOS

            </h3>

            <hr>

            ${this.state.cart.map(item => `

                <div>

                    ${item.name}

                    x ${item.qty}

                    = ₱${item.subtotal.toFixed(2)}

                </div>

            `).join('')}

            <hr>

            <strong>

                Total:

                ₱${this.state.total.toFixed(2)}

            </strong>

            <br>

            Tendered:

            ₱${this.state.tendered.toFixed(2)}

            <br>

            Change:

            ₱${this.state.change.toFixed(2)}

        </body>

        </html>

    `);

        receipt.document.close();

        receipt.focus();

        receipt.print();

    },
    calculateChange() {

        const tendered =
            Number(
                this.amountTendered.value || 0
            );

        const change =
            tendered -
            this.state.total;

        this.state.tendered =
            tendered;

        this.state.change =
            change > 0
                ? change
                : 0;

        this.paymentChange.textContent =
            `₱${this.state.change.toFixed(2)}`;

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

        const paymentMethod =
            this.paymentMethod.value;

        if (
            paymentMethod === 'cash'
        ) {

            if (
                this.state.tendered <
                this.state.total
            ) {

                alert(
                    'Insufficient payment.'
                );

                return false;

            }

        }

        return true;

    },
    buildPayload() {

        return {

            customer_id:
            this.state.customer,

            payment_method:
            this.paymentMethod.value,

            subtotal:
            this.state.subtotal,

            discount:
            this.state.discount,

            total:
            this.state.total,

            tendered:
            this.state.tendered,

            change:
            this.state.change,

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

        this.paymentTotal.textContent =
            `₱${this.state.total.toFixed(2)}`;

        this.amountTendered.value = '';

        this.paymentChange.textContent =
            '₱0.00';

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                this.paymentModal
            );

        modal.show();

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

        const existing =
            this.state.cart.find(
                item => item.id === product.id
            );

        if (existing) {

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
            this.state.cart.reduce(
                (
                    total,
                    item
                ) =>
                    total +
                    item.subtotal,
                0
            );

        this.state.discount = 0;

        this.state.total =
            this.state.subtotal -
            this.state.discount;

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

        this.summarySubtotal.textContent =
            `₱${this.state.subtotal.toFixed(2)}`;

        this.summaryDiscount.textContent =
            `₱${this.state.discount.toFixed(2)}`;

        this.summaryTotal.textContent =
            `₱${this.state.total.toFixed(2)}`;

    },

};

document.addEventListener(
    'DOMContentLoaded',
    () => POS.init()
);
