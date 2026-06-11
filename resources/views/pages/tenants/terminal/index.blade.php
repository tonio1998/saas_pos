@extends('layouts.pos')

@section('title', 'POS Terminal')

@section('content')

    <div class="pos-shell">

        <div class="pos-left">

            @include(
                'pages.tenants.terminal.search_bar'
            )

            @include(
                'pages.tenants.terminal.category_tabs'
            )

            @include(
                'pages.tenants.terminal.products.product_grid'
            )

        </div>

        <div class="pos-right">

            @include(
                'pages.tenants.terminal.cart.cart_header'
            )

            @include(
                'pages.tenants.terminal.cart.cart_items'
            )

            @include(
                'pages.tenants.terminal.cart.cart_summary'
            )

            @include(
                'pages.tenants.terminal.cart.checkout_button'
            )

        </div>

    </div>

    <div
        class="modal fade"
        id="paymentModal"
        tabindex="-1"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Complete Sale
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select
                            id="paymentMethod"
                            class="form-select"
                        >
                            <option value="cash">
                                Cash
                            </option>

                            <option value="gcash">
                                GCash
                            </option>

                            <option value="card">
                                Card
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Amount Tendered
                        </label>

                        <input
                            type="number"
                            id="amountTendered"
                            class="form-control"
                            min="0"
                            step="0.01"
                        >

                    </div>

                    <div class="payment-summary">

                        <div class="d-flex justify-content-between">

                            <span>Total</span>

                            <strong id="paymentTotal">

                                ₱0.00

                            </strong>

                        </div>

                        <div class="d-flex justify-content-between mt-2">

                            <span>Change</span>

                            <strong id="paymentChange">

                                ₱0.00

                            </strong>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        class="btn btn-success"
                        id="btnConfirmPayment"
                    >
                        Complete Sale
                    </button>

                </div>

            </div>

        </div>

    </div>

@endsection
