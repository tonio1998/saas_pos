import config from '../config';
import POSStore from '../core/POSStore';

const POSStorage = {

    load() {

        const data =
            localStorage.getItem(
                config.storageKey
            );

        if (!data) {
            return;
        }

        try {

            const cart =
                JSON.parse(data);

            POSStore.setCart(cart);

        } catch {

            POSStore.setCart([]);
        }
    },

    save() {

        localStorage.setItem(
            config.storageKey,
            JSON.stringify(
                POSStore.getState().cart
            )
        );
    },

    clear() {

        localStorage.removeItem(
            config.storageKey
        );
    }

};

export default POSStorage;
