import POSHelpers from './POSHelpers';

const POSTemplates = {

    cartItem(
        item
    ) {

        const total =
            (
                item.price *
                item.qty
            ) -
            (
                item.discount || 0
            );

        return `
            <div class="cart-item">

                <div class="cart-item-top">

                    <div>

                        <div class="cart-name">
                            ${item.name}
                        </div>

                        <div class="cart-meta">
                            ${item.barcode || ''}
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

                    <div class="cart-total">

                        ${POSHelpers.currency(total)}

                    </div>

                </div>

            </div>
        `;
    }

};

customer(
    customer
) {

    return `
        <div
            class="customer-result"
            data-id="${customer.id}"
            data-name="${customer.name}"
        >

            <div>

                <div
                    class="customer-result-name"
                >
                    ${customer.name}
                </div>

                <div
                    class="customer-result-meta"
                >
                    ${
        customer.mobile_number ??
        ''
    }
                </div>

            </div>

        </div>
    `;
}

export default POSTemplates;
