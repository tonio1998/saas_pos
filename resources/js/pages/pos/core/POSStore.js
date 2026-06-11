class POSStore {

    constructor() {

        this.state = {

            cart: [],

            customer: null,

            paymentMethod: 'cash',

            discount: 0

        };
    }

    getState() {

        return this.state;
    }

    setCart(cart) {

        this.state.cart = cart;
    }

    setCustomer(customer) {

        this.state.customer = customer;
    }

    setPaymentMethod(method) {

        this.state.paymentMethod = method;
    }

    setDiscount(discount) {

        this.state.discount = discount;
    }

}

export default new POSStore();
