const ProductDB = {

    db: null,

    async init() {

        return new Promise(
            (
                resolve,
                reject
            ) => {

                const request =
                    indexedDB.open(
                        'catchupos',
                        1
                    );

                request.onupgradeneeded =
                    event => {

                        const db =
                            event.target.result;

                        if (
                            !db.objectStoreNames.contains(
                                'products'
                            )
                        ) {

                            const store =
                                db.createObjectStore(
                                    'products',
                                    {
                                        keyPath: 'id'
                                    }
                                );

                            store.createIndex(
                                'name',
                                'name',
                                {
                                    unique: false
                                }
                            );

                            store.createIndex(
                                'barcode',
                                'barcode',
                                {
                                    unique: false
                                }
                            );

                        }

                    };

                request.onsuccess =
                    event => {

                        this.db =
                            event.target.result;

                        resolve();

                    };

                request.onerror =
                    event =>
                        reject(
                            event.target.error
                        );

            }
        );

    },

    async saveProducts(
        products
    ) {

        return new Promise(
            (
                resolve,
                reject
            ) => {

                const tx =
                    this.db.transaction(
                        'products',
                        'readwrite'
                    );

                const store =
                    tx.objectStore(
                        'products'
                    );

                products.forEach(
                    product =>
                        store.put(
                            product
                        )
                );

                tx.oncomplete =
                    () =>
                        resolve(
                            true
                        );

                tx.onerror =
                    event =>
                        reject(
                            event.target.error
                        );

            }
        );

    },

    async clearProducts() {

        return new Promise(
            (
                resolve,
                reject
            ) => {

                const tx =
                    this.db.transaction(
                        'products',
                        'readwrite'
                    );

                const store =
                    tx.objectStore(
                        'products'
                    );

                const request =
                    store.clear();

                request.onsuccess =
                    () =>
                        resolve(
                            true
                        );

                request.onerror =
                    event =>
                        reject(
                            event.target.error
                        );

            }
        );

    },

    async getProducts() {

        return new Promise(
            (
                resolve,
                reject
            ) => {

                const tx =
                    this.db.transaction(
                        'products',
                        'readonly'
                    );

                const store =
                    tx.objectStore(
                        'products'
                    );

                const request =
                    store.getAll();

                request.onsuccess =
                    () =>
                        resolve(
                            request.result
                        );

                request.onerror =
                    event =>
                        reject(
                            event.target.error
                        );

            }
        );

    },

    async search(
        keyword
    ) {

        return new Promise(
            (
                resolve,
                reject
            ) => {

                const tx =
                    this.db.transaction(
                        'products',
                        'readonly'
                    );

                const store =
                    tx.objectStore(
                        'products'
                    );

                const request =
                    store.getAll();

                request.onsuccess =
                    () => {

                        const term =
                            keyword
                                .trim()
                                .toLowerCase();

                        const rows =
                            request.result
                                .filter(
                                    product => {

                                        const name =
                                            (
                                                product.name ||
                                                ''
                                            )
                                                .toLowerCase();

                                        const barcode =
                                            (
                                                product.barcode ||
                                                ''
                                            )
                                                .toLowerCase();

                                        return (
                                            name.includes(
                                                term
                                            ) ||
                                            barcode.includes(
                                                term
                                            )
                                        );

                                    }
                                )
                                .slice(
                                    0,
                                    50
                                );

                        resolve(
                            rows
                        );

                    };

                request.onerror =
                    event =>
                        reject(
                            event.target.error
                        );

            }
        );

    },

    async findBarcode(
        barcode
    ) {

        return new Promise(
            (
                resolve,
                reject
            ) => {

                const tx =
                    this.db.transaction(
                        'products',
                        'readonly'
                    );

                const store =
                    tx.objectStore(
                        'products'
                    );

                const request =
                    store.getAll();

                request.onsuccess =
                    () => {

                        const product =
                            request.result.find(
                                item =>
                                    item.barcode ===
                                    barcode
                            );

                        resolve(
                            product ||
                            null
                        );

                    };

                request.onerror =
                    event =>
                        reject(
                            event.target.error
                        );

            }
        );

    },

};

export default ProductDB;
