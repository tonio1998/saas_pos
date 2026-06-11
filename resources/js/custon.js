import $ from 'jquery'
window.$ = window.jQuery = $

$(document).ready(function () {

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
