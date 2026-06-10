const POS = {

    vatRate: 0.12,

    products: [],

    cart: [],

    init() {

        this.loadCart();

        this.bindEvents();

        this.updateDateTime();

        this.renderCart();

        setInterval(() => {

            this.updateDateTime();

        }, 1000);
    },

    bindEvents() {

        const searchInput =
            document.getElementById(
                'barcodeSearch'
            );

        if (searchInput) {

            searchInput.addEventListener(
                'keydown',
                e => {

                    if (
                        e.key !== 'Enter'
                    ) {
                        return;
                    }

                    e.preventDefault();

                    const value =
                        e.target.value.trim();

                    if (!value) {
                        return;
                    }

                    this.scanBarcode(
                        value
                    );

                    e.target.value = '';
                }
            );
        }

        document.addEventListener(
            'click',
            e => {

                const product =
                    e.target.closest(
                        '.product-card'
                    );

                if (product) {

                    this.addToCart({
                        id:
                        product.dataset.id,
                        barcode:
                        product.dataset.barcode,
                        name:
                        product.dataset.name,
                        price:
                            parseFloat(
                                product.dataset.price
                            ),
                        image:
                            product.dataset.image || ''
                    });

                    return;
                }

                const qtyPlus =
                    e.target.closest(
                        '.qty-plus'
                    );

                if (qtyPlus) {

                    this.changeQty(
                        qtyPlus.dataset.id,
                        1
                    );

                    return;
                }

                const qtyMinus =
                    e.target.closest(
                        '.qty-minus'
                    );

                if (qtyMinus) {

                    this.changeQty(
                        qtyMinus.dataset.id,
                        -1
                    );

                    return;
                }

                const removeBtn =
                    e.target.closest(
                        '.remove-item'
                    );

                if (removeBtn) {

                    this.removeItem(
                        removeBtn.dataset.id
                    );

                    return;
                }
            }
        );

        document.addEventListener(
            'keydown',
            e => {

                switch (
                    e.key
                    ) {

                    case 'F2':
                        e.preventDefault();
                        this.productInquiry();
                        break;

                    case 'F4':
                        e.preventDefault();
                        this.discount();
                        break;

                    case 'F5':
                        e.preventDefault();
                        this.holdSale();
                        break;

                    case 'F6':
                        e.preventDefault();
                        this.recallSale();
                        break;

                    case 'F7':
                        e.preventDefault();
                        this.customerLookup();
                        break;

                    case 'F8':
                        e.preventDefault();
                        this.refund();
                        break;
                }

                if (
                    e.ctrlKey &&
                    e.key.toLowerCase() ===
                    'd'
                ) {

                    e.preventDefault();

                    this.openDrawer();
                }
            }
        );
    },

    async scanBarcode(
        barcode
    ) {

        try {

            const response =
                await fetch(
                    `/products/barcode/${barcode}`
                );

            if (
                !response.ok
            ) {
                return;
            }

            const product =
                await response.json();

            this.addToCart({
                id: product.id,
                barcode:
                product.barcode,
                name:
                product.name,
                price:
                    parseFloat(
                        product.selling_price
                    ),
                image:
                product.image
            });

        } catch (
            error
            ) {

            console.error(
                error
            );
        }
    },

    addToCart(product) {

        const existing =
            this.cart.find(
                item =>
                    item.id ==
                    product.id
            );

        if (existing) {

            existing.qty++;

        } else {

            this.cart.push({
                ...product,
                qty: 1,
                discount: 0
            });
        }

        this.persistCart();

        this.renderCart();
    },

    changeQty(
        productId,
        amount
    ) {

        const item =
            this.cart.find(
                x =>
                    x.id ==
                    productId
            );

        if (!item) {
            return;
        }

        item.qty += amount;

        if (
            item.qty <= 0
        ) {

            this.cart =
                this.cart.filter(
                    x =>
                        x.id !=
                        productId
                );
        }

        this.persistCart();

        this.renderCart();
    },

    removeItem(
        productId
    ) {

        this.cart =
            this.cart.filter(
                item =>
                    item.id !=
                    productId
            );

        this.persistCart();

        this.renderCart();
    },

    clearCart() {

        this.cart = [];

        this.persistCart();

        this.renderCart();
    },

    renderCart() {

        const container =
            document.querySelector(
                '.cart-items'
            );

        if (!container) {
            return;
        }

        if (
            !this.cart.length
        ) {

            container.innerHTML = `
                <div
                    class="text-center text-muted py-5"
                >
                    No items added
                </div>
            `;

            this.computeTotals();

            return;
        }

        container.innerHTML =
            this.cart
                .map(
                    item => {

                        const lineTotal =
                            (
                                item.price *
                                item.qty
                            ) -
                            item.discount;

                        return `
                            <div class="cart-item">

                                <div class="cart-item-top">

                                    <div>

                                        <div class="cart-name">
                                            ${item.name}
                                        </div>

                                        <div class="cart-meta">
                                            ${item.barcode}
                                        </div>

                                    </div>

                                    <button
                                        class="btn btn-sm btn-danger remove-item"
                                        data-id="${item.id}"
                                    >
                                        ×
                                    </button>

                                </div>

                                <div class="cart-item-bottom">

                                    <div class="qty-box">

                                        <button
                                            class="qty-minus"
                                            data-id="${item.id}"
                                        >
                                            -
                                        </button>

                                        <span>
                                            ${item.qty}
                                        </span>

                                        <button
                                            class="qty-plus"
                                            data-id="${item.id}"
                                        >
                                            +
                                        </button>

                                    </div>

                                    <div
                                        class="cart-total"
                                    >
                                        ${this.currency(
                            lineTotal
                        )}
                                    </div>

                                </div>

                            </div>
                        `;
                    }
                )
                .join('');

        this.computeTotals();
    },

    computeTotals() {

        let subtotal = 0;

        let discount = 0;

        this.cart.forEach(
            item => {

                subtotal +=
                    item.price *
                    item.qty;

                discount +=
                    item.discount;
            }
        );

        const vat =
            subtotal *
            this.vatRate;

        const total =
            subtotal -
            discount;

        const subtotalEl =
            document.getElementById(
                'subtotalAmount'
            );

        const discountEl =
            document.getElementById(
                'discountAmount'
            );

        const vatEl =
            document.getElementById(
                'vatAmount'
            );

        const totalEl =
            document.getElementById(
                'grandTotal'
            );

        if (subtotalEl) {

            subtotalEl.innerHTML =
                this.currency(
                    subtotal
                );
        }

        if (discountEl) {

            discountEl.innerHTML =
                this.currency(
                    discount
                );
        }

        if (vatEl) {

            vatEl.innerHTML =
                this.currency(
                    vat
                );
        }

        if (totalEl) {

            totalEl.innerHTML =
                this.currency(
                    total
                );
        }
    },

    persistCart() {

        localStorage.setItem(
            'pos_cart',
            JSON.stringify(
                this.cart
            )
        );
    },

    loadCart() {

        const data =
            localStorage.getItem(
                'pos_cart'
            );

        if (!data) {
            return;
        }

        try {

            this.cart =
                JSON.parse(
                    data
                );

        } catch {

            this.cart = [];
        }
    },

    currency(value) {

        return new Intl.NumberFormat(
            'en-PH',
            {
                style: 'currency',
                currency: 'PHP'
            }
        ).format(
            value
        );
    },

    updateDateTime() {

        const element =
            document.getElementById(
                'posDateTime'
            );

        if (!element) {
            return;
        }

        element.innerHTML =
            new Date()
                .toLocaleString(
                    'en-PH'
                );
    },

    productInquiry() {

        console.log(
            'Product Inquiry'
        );
    },

    discount() {

        console.log(
            'Discount'
        );
    },

    holdSale() {

        console.log(
            'Hold Sale'
        );
    },

    recallSale() {

        console.log(
            'Recall Sale'
        );
    },

    customerLookup() {

        console.log(
            'Customer Lookup'
        );
    },

    refund() {

        console.log(
            'Refund'
        );
    },

    openDrawer() {

        console.log(
            'Open Drawer'
        );
    }
};

document.addEventListener(
    'DOMContentLoaded',
    () => {

        POS.init();
    }
);

window.POS = POS;
