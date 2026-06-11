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

});
