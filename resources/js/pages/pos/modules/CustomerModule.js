import POSStore from '../core/POSStore';

const CustomerModule = {

    set(customer) {

        POSStore.setCustomer(
            customer
        );

        this.render();
    },

    clear() {

        POSStore.setCustomer(
            null
        );

        this.render();
    },

    get() {

        return POSStore
            .getState()
            .customer;
    },

    render() {

        const customer =
            this.get();

        const nameElement =
            document.getElementById(
                'selectedCustomerName'
            );

        const idElement =
            document.getElementById(
                'customer_id'
            );

        if (!nameElement) {
            return;
        }

        if (!customer) {

            nameElement.innerHTML =
                'Walk-in Customer';

            if (idElement) {

                idElement.value = '';
            }

            return;
        }

        nameElement.innerHTML =
            customer.name;

        if (idElement) {

            idElement.value =
                customer.id;
        }
    }

};

export default CustomerModule;
