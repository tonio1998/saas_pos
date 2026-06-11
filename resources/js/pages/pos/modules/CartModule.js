import POSStore from '../core/POSStore';
import POSStorage from '../services/POSStorage';

const CartModule = {

    add(product) {

        const cart =
            POSStore
                .getState()
                .cart;

        const existing =
            cart.find(
                item =>
                    item.id ==
                    product.id
            );

        if (existing) {

            existing.qty++;

        } else {

            cart.push({

                ...product,

                qty: 1,

                discount: 0

            });
        }

        POSStorage.save();
    },

    remove(productId) {

        const cart =
            POSStore
                .getState()
                .cart
                .filter(
                    item =>
                        item.id !=
                        productId
                );

        POSStore.setCart(
            cart
        );

        POSStorage.save();
    },

    changeQty(
        productId,
        amount
    ) {

        const cart =
            POSStore
                .getState()
                .cart;

        const item =
            cart.find(
                item =>
                    item.id ==
                    productId
            );

        if (!item) {
            return;
        }

        item.qty += amount;

        if (
            item.qty <= 0
        ) {

            this.remove(
                productId
            );

            return;
        }

        POSStorage.save();
    },

    clear() {

        POSStore.setCart([]);

        POSStorage.save();
    }

};

export default CartModule;
