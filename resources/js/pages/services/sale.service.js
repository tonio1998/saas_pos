const SaleService = {

    async checkout(payload) {
        console.log(payload);
        const response =
            await fetch(
                '/sales/store',
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
