const CustomerApi = {

    async search(
        keyword
    ) {

        const response =
            await fetch(
                `/customers/search?q=${encodeURIComponent(
                    keyword
                )}`
            );

        if (
            !response.ok
        ) {

            throw new Error(
                'Failed to search customers'
            );
        }

        return response.json();
    },

    async get(
        customerId
    ) {

        const response =
            await fetch(
                `/customers/${customerId}`
            );

        if (
            !response.ok
        ) {

            throw new Error(
                'Customer not found'
            );
        }

        return response.json();
    }

};

export default CustomerApi;
