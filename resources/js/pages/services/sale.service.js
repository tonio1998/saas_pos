const SaleService = {

    async checkout(payload) {

        const response =
            await fetch(
                '/api/pos/sales',
                {
                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                        document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            .content,

                    },

                    body:
                        JSON.stringify(
                            payload
                        )

                }
            );

        return await response.json();

    }

};

export default SaleService;
