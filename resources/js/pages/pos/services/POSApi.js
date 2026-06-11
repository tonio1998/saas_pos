const POSApi = {

    async getProductByBarcode(
        barcode
    ) {

        const response =
            await fetch(
                `/products/barcode/${barcode}`
            );

        if (!response.ok) {

            throw new Error(
                'Product not found'
            );
        }

        return response.json();
    },

    async searchProducts(
        keyword
    ) {

        const response =
            await fetch(
                `/products/search?q=${encodeURIComponent(keyword)}`
            );

        return response.json();
    }

};

export default POSApi;
