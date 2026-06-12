import ProductDB from '../db';

const CACHE_DURATION = 5 * 60 * 1000;

const ProductService = {

    async sync() {

        const response =
            await fetch(
                '/sales/products'
            );

        if (!response.ok) {
            throw new Error(
                'Unable to load products'
            );
        }

        const products =
            await response.json();

        await ProductDB.clearProducts();

        await ProductDB.saveProducts(
            products
        );

        localStorage.setItem(
            'products_last_sync',
            Date.now()
        );

        return products;

    },

    async getCached(force = false) {

        if (force) {

            return await this.sync();

        }

        const lastSync =
            Number(
                localStorage.getItem(
                    'products_last_sync'
                )
            );

        const isExpired =
            !lastSync ||
            (
                Date.now() - lastSync
            ) >
            CACHE_DURATION;

        if (isExpired) {

            try {

                return await this.sync();

            } catch {

                return await ProductDB.getProducts();

            }

        }

        return await ProductDB.getProducts();

    },

    async search(keyword) {

        return await ProductDB.search(
            keyword
        );

    },

    async barcode(barcode) {

        return await ProductDB.findBarcode(
            barcode
        );

    }

};

export default ProductService;
