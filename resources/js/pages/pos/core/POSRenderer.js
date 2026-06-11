import POSStore from './POSStore';
import POSTemplates from './POSTemplates';
import PaymentModule from '../modules/PaymentModule';
import POSHelpers from './POSHelpers';

const POSRenderer = {

    renderCart() {

        const container =
            document.querySelector(
                '.cart-items'
            );

        if (!container) {
            return;
        }

        const cart =
            POSStore
                .getState()
                .cart;

        if (!cart.length) {

            container.innerHTML =
                `
                <div class="empty-cart">
                    No items added
                </div>
                `;

            return;
        }

        container.innerHTML =
            cart
                .map(
                    item =>
                        POSTemplates.cartItem(
                            item
                        )
                )
                .join('');

        this.renderSummary();
    },

    renderSummary() {

        const totals =
            PaymentModule
                .getTotals();

        $('#subtotalAmount').html(
            POSHelpers.currency(
                totals.subtotal
            )
        );

        $('#discountAmount').html(
            POSHelpers.currency(
                totals.discount
            )
        );

        $('#vatAmount').html(
            POSHelpers.currency(
                totals.vat
            )
        );

        $('#grandTotal').html(
            POSHelpers.currency(
                totals.total
            )
        );
    }

};

export default POSRenderer;
