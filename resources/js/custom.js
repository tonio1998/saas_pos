import $ from 'jquery'

window.$ = window.jQuery = $

$(document).ready(function () {

    document.addEventListener('click', async function (e) {
        const link = e.target.closest('.view-sale');

        if (!link) {
            return;
        }

        e.preventDefault();

        const modal = new bootstrap.Modal(
            document.getElementById('saleDetailsModal')
        );

        modal.show();

        const content = document.getElementById('saleDetailsContent');

        content.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary"></div>
        </div>
    `;

        try {

            const response = await fetch(`/sales/${link.dataset.id}/sales_details`);

            const result = await response.json();

            const sale = result.sale;

            console.log('sale', sale);

            let rows = '';

            let itemRows = '';
            let paymentRows = '';

            sale.items.forEach(item => {
                itemRows += `
                    <tr>
                        <td>
                            <div class="fw-semibold">${item.product.name}</div>
                            <small class="text-muted">${item.barcode}</small>
                        </td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">${item.unit_price}</td>
                        <td class="text-end">${item.line_total}</td>
                    </tr>
                `;
            });

            sale.payments.forEach(payment => {
                paymentRows += `
                    <tr>
                        <td>${payment.payment_date}</td>
                        <td>${payment.payment_method}</td>
                        <td>${payment.reference_number}</td>
                        <td class="text-end">${payment.amount}</td>
                    </tr>
                `;
            });

            content.innerHTML = `
                <div class="container-fluid">

                    <div class="row g-3 mb-4">

                        <div class="col-md-8">

                            <div class="card border-0 bg-light">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-start">

                                        <div>

                                            <h5 class="fw-bold mb-1">
                                                Sale Details
                                            </h5>

                                            <div class="text-muted">
                                                ${sale.invoice_no ?? 'No Invoice'}
                                            </div>

                                        </div>

                                        <span class="badge bg-success">
                                            ${sale.sale_status}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="card border-0 bg-success text-white">

                                <div class="card-body text-center">

                                    <small class="text-dark">Total Sale</small>

                                    <div class="display-6 fw-bold text-dark">
                                        ${sale.total_amount}
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <table class="table table-sm mb-0">

                                <tr>
                                    <th width="140">Customer</th>
                                    <td>${sale.customer.CustomerName}</td>
                                </tr>

                                <tr>
                                    <th>Cashier</th>
                                    <td>${sale.cashier.name}</td>
                                </tr>

                                <tr>
                                    <th>Sale Date</th>
                                    <td>${sale.sale_date}</td>
                                </tr>

                            </table>

                        </div>

                        <div class="col-md-6">

                            <table class="table table-sm mb-0">

                                <tr>
                                    <th width="140">Subtotal</th>
                                    <td class="text-end">${sale.subtotal}</td>
                                </tr>

                                <tr>
                                    <th>Discount</th>
                                    <td class="text-end">${sale.discount_amount}</td>
                                </tr>

                                <tr class="fw-bold">

                                    <th>Total</th>

                                    <td class="text-end text-success">
                                        ${sale.total_amount}
                                    </td>

                                </tr>

                            </table>

                        </div>

                    </div>

                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white">

                            <strong>
                                Purchased Items
                            </strong>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>Product</th>

                                        <th width="100" class="text-center">
                                            Qty
                                        </th>

                                        <th width="120" class="text-end">
                                            Price
                                        </th>

                                        <th width="120" class="text-end">
                                            Total
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    ${itemRows}

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="card border-0 shadow-sm mb-3">

                        <div class="card-header bg-white">

                            <strong>
                                Payment History
                            </strong>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-sm align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>Date</th>

                                        <th>Method</th>

                                        <th>Reference</th>

                                        <th class="text-end">
                                            Amount
                                        </th>

                                    </tr>

                                </thead>

                                <tbody>

                                    ${paymentRows}

                                </tbody>

                            </table>

                        </div>

                    </div>

                    ${sale.notes
                                ? `
                        <div class="alert alert-light border mb-0">

                            <strong>Notes</strong>

                            <div class="mt-2">
                                ${sale.notes}
                            </div>

                        </div>
                        `
                                : ''
                            }

                </div>
                `;

        }  catch (error) {

        console.error(error);

        content.innerHTML = `
        <div class="alert alert-danger">
            ${error.message}
        </div>
    `;

    }

    });

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

