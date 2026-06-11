import ProductDB from '../db';

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

        await ProductDB.saveProducts(
            products
        );

        return products;

    },

    async getCached() {

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

    },

};

export default ProductService;
