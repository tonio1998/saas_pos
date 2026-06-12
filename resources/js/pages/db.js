const ProductDB = {

    db: null,

    async init() {

        if (this.db) {
            return;
        }

        return new Promise((resolve, reject) => {

            const request = indexedDB.open('catchupos', 2);

            request.onupgradeneeded = event => {

                const db = event.target.result;

                if (!db.objectStoreNames.contains('products')) {

                    const store = db.createObjectStore(
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

                if (!db.objectStoreNames.contains('settings')) {

                    db.createObjectStore(
                        'settings',
                        {
                            keyPath: 'key'
                        }
                    );

                }

            };

            request.onsuccess = event => {

                this.db = event.target.result;

                resolve();

            };

            request.onerror = event =>
                reject(event.target.error);

        });

    },

    async saveProducts(products) {

        return new Promise((resolve, reject) => {

            const tx = this.db.transaction(
                ['products', 'settings'],
                'readwrite'
            );

            const productStore =
                tx.objectStore('products');

            const settingsStore =
                tx.objectStore('settings');

            productStore.clear();

            products.forEach(product => {

                productStore.put(product);

            });

            settingsStore.put({
                key: 'products_last_sync',
                value: Date.now()
            });

            tx.oncomplete = () =>
                resolve(true);

            tx.onerror = event =>
                reject(event.target.error);

        });

    },

    async clearProducts() {

        return new Promise((resolve, reject) => {

            const tx = this.db.transaction(
                'products',
                'readwrite'
            );

            const store =
                tx.objectStore('products');

            const request =
                store.clear();

            request.onsuccess = () =>
                resolve(true);

            request.onerror = event =>
                reject(event.target.error);

        });

    },

    async getProducts() {

        return new Promise((resolve, reject) => {

            const tx = this.db.transaction(
                'products',
                'readonly'
            );

            const store =
                tx.objectStore('products');

            const request =
                store.getAll();

            request.onsuccess = () =>
                resolve(request.result || []);

            request.onerror = event =>
                reject(event.target.error);

        });

    },

    async getLastSync() {

        return new Promise((resolve, reject) => {

            const tx = this.db.transaction(
                'settings',
                'readonly'
            );

            const store =
                tx.objectStore('settings');

            const request =
                store.get('products_last_sync');

            request.onsuccess = () =>
                resolve(
                    request.result?.value || null
                );

            request.onerror = event =>
                reject(event.target.error);

        });

    },

    async search(keyword) {

        const term =
            keyword
                .trim()
                .toLowerCase();

        const products =
            await this.getProducts();

        return products
            .filter(product => {

                const name =
                    (product.name || '')
                        .toLowerCase();

                const barcode =
                    (product.barcode || '')
                        .toLowerCase();

                return (
                    name.includes(term) ||
                    barcode.includes(term)
                );

            })
            .slice(0, 50);

    },

    async findBarcode(barcode) {

        const products =
            await this.getProducts();

        return (
            products.find(
                product =>
                    product.barcode === barcode
            ) || null
        );

    }

};

export default ProductDB;
