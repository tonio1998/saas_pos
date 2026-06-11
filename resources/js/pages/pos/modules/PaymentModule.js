import POSStore from '../core/POSStore';

const PaymentModule = {

    getTotals() {

        const cart =
            POSStore
                .getState()
                .cart;

        const subtotal =
            cart.reduce(
                (
                    total,
                    item
                ) =>
                    total +
                    (
                        item.price *
                        item.qty
                    ),
                0
            );

        const discount =
            cart.reduce(
                (
                    total,
                    item
                ) =>
                    total +
                    (
                        item.discount || 0
                    ),
                0
            );

        const vat =
            subtotal *
            0.12;

        const total =
            subtotal -
            discount;

        return {

            subtotal,

            discount,

            vat,

            total

        };
    }

};

export default PaymentModule;
