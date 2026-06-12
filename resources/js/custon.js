import $ from 'jquery'
window.$ = window.jQuery = $

$(document).ready(function () {

    function calculateAverageCost() {

        let currentStock =
            parseFloat(
                $('#current_stock').val()
            ) || 0;

        let quantity =
            parseFloat(
                $('#quantity').val()
            ) || 0;

        let currentCost =
            parseFloat(
                $('#current_cost').val()
            ) || 0;

        let purchaseCost =
            parseFloat(
                $('#unit_cost').val()
            ) || 0;

        if (
            quantity <= 0 ||
            purchaseCost <= 0
        ) {

            $('#average_cost').val('');

            return;
        }

        let totalExistingValue =
            currentStock * currentCost;

        let totalNewValue =
            quantity * purchaseCost;

        let totalStock =
            currentStock + quantity;

        let averageCost =
            (
                totalExistingValue +
                totalNewValue
            ) / totalStock;

        $('#average_cost').val(
            averageCost.toFixed(2)
        );

    }

    $('#quantity, #unit_cost').on(
        'input',
        calculateAverageCost
    );

    function calculateAdjustment() {

        const currentStock =
            parseFloat(
                $('#current_stock').val()
            ) || 0;

        const actualStock =
            parseFloat(
                $('#actual_stock').val()
            ) || 0;

        const adjustment =
            actualStock - currentStock;

        if (adjustment > 0) {

            $('#adjustment_qty')
                .val(`+${adjustment.toFixed(2)}`)
                .removeClass('text-danger')
                .addClass('text-success');

        } else if (adjustment < 0) {

            $('#adjustment_qty')
                .val(adjustment.toFixed(2))
                .removeClass('text-success')
                .addClass('text-danger');

        } else {

            $('#adjustment_qty')
                .val('0.00')
                .removeClass('text-success text-danger');

        }
    }

    $('#actual_stock').on(
        'input',
        calculateAdjustment
    );

    calculateAdjustment();

    $(document).on(
        'click',
        '.btn-view-sale',
        function () {

            const saleId =
                $(this).data('id');

            $.get(
                `/sales/${saleId}/details`,
                function (response) {

                    $('#detailInvoice').text(
                        response.invoice_no
                    );

                    $('#detailDate').text(
                        response.sale_date
                    );

                    $('#detailCustomer').text(
                        response.customer
                    );

                    $('#detailCashier').text(
                        response.cashier
                    );

                    const statusClass = {
                        active: 'bg-success',
                        completed: 'bg-success',
                        paid: 'bg-success',
                        pending: 'bg-warning',
                        cancelled: 'bg-danger',
                        refunded: 'bg-secondary'
                    };

                    $('#detailStatus')
                        .removeClass()
                        .addClass(
                            `badge fs-6 ${
                                statusClass[
                                    response.status
                                        ?.toLowerCase()
                                    ] || 'bg-primary'
                            }`
                        )
                        .text(
                            response.status
                        );

                    $('#detailSubtotal').text(
                        response.subtotal
                    );

                    $('#detailDiscount').text(
                        response.discount
                    );

                    $('#detailTotal').text(
                        response.total
                    );

                    $('#detailTendered').text(
                        response.tendered
                    );

                    $('#detailChange').text(
                        response.change
                    );

                    $('#detailProfit').text(
                        response.profit
                    );

                    $('#detailNotes').html(
                        response.notes ||
                        '<span class="text-muted">No notes available.</span>'
                    );

                    $('#paymentCount').text(
                        `${response.payments.length} Payment(s)`
                    );

                    $('#itemCount').text(
                        `${response.items.length} Item(s)`
                    );

                    let paymentHtml = '';

                    if (
                        response.payments.length
                    ) {

                        let totalPaid = 0;

                        response.payments.forEach(
                            payment => {

                                const amount =
                                    Number(
                                        String(
                                            payment.amount
                                        ).replace(
                                            /[₱,]/g,
                                            ''
                                        )
                                    );

                                totalPaid += amount;

                                paymentHtml += `
            <tr>
                <td>
                    ${payment.method}
                </td>

                <td>
                    ${payment.reference}
                </td>

                <td class="text-end">
                    ${payment.amount}
                </td>
            </tr>
        `;
                            }
                        );

                        paymentHtml += `
    <tr class="table-light fw-bold">

        <td colspan="2">
            Total Paid
        </td>

        <td class="text-end text-success">
            ₱${totalPaid.toLocaleString(
                            'en-PH',
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        )}
        </td>

    </tr>
`;

                        $('#paymentTable tbody')
                            .html(paymentHtml);

                    } else {

                        paymentHtml = `
                        <tr>
                            <td
                                colspan="6"
                                class="text-center text-muted py-4"
                            >
                                No payment records found
                            </td>
                        </tr>
                    `;
                    }

                    $('#paymentTable tbody')
                        .html(paymentHtml);

                    let itemsHtml = '';

                    if (
                        response.items.length
                    ) {

                        let totalQty = 0;
                        let totalAmount = 0;
                        let totalProfit = 0;

                        response.items.forEach(
                            item => {

                                totalQty += Number(
                                    item.quantity || 0
                                );

                                totalAmount += Number(
                                    String(item.total)
                                        .replace(/[₱,]/g, '')
                                );

                                totalProfit += Number(
                                    String(item.profit)
                                        .replace(/[₱,]/g, '')
                                );

                                itemsHtml += `
            <tr>
                <td>
                    ${item.barcode}
                </td>

                <td>
                    ${item.product}
                </td>

                <td class="text-center">
                    ${item.quantity}
                </td>

                <td class="text-end">
                    ${item.price}
                </td>

                <td class="text-end">
                    ${item.total}
                </td>

                <td class="text-end text-warning">
                    ${item.profit}
                </td>
            </tr>
        `;
                            }
                        );

                        itemsHtml += `
    <tr class="table-light fw-bold">
        <td colspan="2">
            TOTAL
        </td>

        <td class="text-center">
            ${totalQty}
        </td>

        <td></td>

        <td class="text-end">
            ₱${totalAmount.toLocaleString(
                            'en-PH',
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        )}
        </td>

        <td class="text-end text-warning">
            ₱${totalProfit.toLocaleString(
                            'en-PH',
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }
                        )}
        </td>
    </tr>
`;

                        $('#itemsTable tbody')
                            .html(itemsHtml);

                    } else {

                        itemsHtml = `
                        <tr>
                            <td
                                colspan="6"
                                class="text-center text-muted py-4"
                            >
                                No items found
                            </td>
                        </tr>
                    `;
                    }

                    $('#itemsTable tbody')
                        .html(itemsHtml);

                    bootstrap
                        .Modal
                        .getOrCreateInstance(
                            document.getElementById(
                                'saleDetailsModal'
                            )
                        )
                        .show();
                }
            )
                .fail(function () {

                    Swal.fire({
                        icon: 'error',
                        title: 'Unable to Load',
                        text: 'Failed to retrieve transaction details.'
                    });

                });

        }
    );

});

