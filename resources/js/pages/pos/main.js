import POSStorage from './services/POSStorage';
import POSApi from './services/POSApi';

import CartModule from './modules/CartModule';

import POSRenderer from './core/POSRenderer';

document.addEventListener(
    'DOMContentLoaded',
    () => {

        POSStorage.load();

        POSRenderer.renderCart();

        document.addEventListener(
            'click',
            async e => {

                const card =
                    e.target.closest(
                        '.product-card'
                    );

                if (card) {

                    CartModule.add({

                        id:
                        card.dataset.id,

                        name:
                        card.dataset.name,

                        barcode:
                        card.dataset.barcode,

                        price:
                            parseFloat(
                                card.dataset.price
                            )

                    });

                    POSRenderer.renderCart();

                    return;
                }

                const plus =
                    e.target.closest(
                        '.qty-plus'
                    );

                if (plus) {

                    CartModule.changeQty(
                        plus.dataset.id,
                        1
                    );

                    POSRenderer.renderCart();

                    return;
                }

                const minus =
                    e.target.closest(
                        '.qty-minus'
                    );

                if (minus) {

                    CartModule.changeQty(
                        minus.dataset.id,
                        -1
                    );

                    POSRenderer.renderCart();

                    return;
                }

                const remove =
                    e.target.closest(
                        '.remove-item'
                    );

                if (remove) {

                    CartModule.remove(
                        remove.dataset.id
                    );

                    POSRenderer.renderCart();
                }
            }
        );

        document
            .getElementById(
                'barcodeSearch'
            )
            ?.addEventListener(
                'keydown',
                async e => {

                    if (
                        e.key !==
                        'Enter'
                    ) {
                        return;
                    }

                    const barcode =
                        e.target.value.trim();

                    if (!barcode) {
                        return;
                    }

                    try {

                        const product =
                            await POSApi.getProductByBarcode(
                                barcode
                            );

                        CartModule.add({

                            id:
                            product.id,

                            name:
                            product.name,

                            barcode:
                            product.barcode,

                            price:
                                parseFloat(
                                    product.selling_price
                                )
                        });

                        POSRenderer.renderCart();

                    } catch (
                        error
                        ) {

                        console.error(
                            error
                        );
                    }

                    e.target.value = '';
                }
            );
    }
);
