@extends('layouts.pos')

@section('title', 'POS Terminal')

@section('content')

    <div class="pos-shell">

        <div class="pos-left">

            @include(
                'pages.tenants.terminal.search_bar'
            )

{{--            @include(--}}
{{--                'pages.tenants.terminal.category_tabs'--}}
{{--            )--}}

            @include(
                'pages.tenants.terminal.products.product_grid'
            )

        </div>

        <div class="pos-right">
            <div class="pos-status-panel">
                <input type="hidden" id="saleId" value="{{ encryptId($sale->id) }}">
                <div
                    class="pos-status-bar"
                >

                    <div
                        class="status-indicator ready"
                        id="statusIndicator"
                    ></div>

                    <div
                        class="status-message"
                        id="posStatus"
                    >
                        Ready
                    </div>

                </div>

                <div
                    class="activity-list"
                    id="activityList"
                ></div>

            </div>
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

        <div class="modal-dialog modal-xl modal-dialog-centered">

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

                <div class="modal-body py-3">
                    <div class="saleStatusContainer"></div>
                    <div class="row g-3">

                        <div class="col-lg-7">

                            <div class="row g-3 mb-3">

                                <div class="col-md-6">

                                    <div class="card bg-dark border-0">

                                        <div class="card-body text-center py-3">

                                            <div class="text-secondary text-uppercase fw-semibold mb-1">
                                                Total Due
                                            </div>

                                            <div
                                                id="paymentTotal"
                                                class="fw-bold"
                                                style="
                                                font-size:2rem;
                                                line-height:1;
                                                color:#22c55e;
                                            "
                                            >
                                                ₱0.00
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <div class="card bg-dark border-0">

                                        <div class="card-body text-center py-3">

                                            <div class="text-secondary text-uppercase fw-semibold mb-1">
                                                Balance
                                            </div>

                                            <div
                                                id="paymentBalance"
                                                class="fw-bold"
                                                style="
                                                font-size:2rem;
                                                line-height:1;
                                                color:#ef4444;
                                            "
                                            >
                                                ₱0.00
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div
                                id="paymentLines"
                                class="mb-2"
                            >

                                <div
                                    class="payment-row card border shadow-sm"
                                    data-index="0"
                                >

                                    <div class="card-body py-3">

                                        <div class="row g-2 align-items-end">

                                            <div class="col-md-4">

                                                <label class="form-label small mb-1">
                                                    Payment Method
                                                </label>

                                                <select
                                                    class="form-select payment-method"
                                                >

                                                    <option value="cash">
                                                        Cash
                                                    </option>

                                                    <option value="gcash">
                                                        GCash
                                                    </option>

                                                    <option value="bank_transfer">
                                                        Bank Transfer
                                                    </option>

                                                </select>

                                            </div>

                                            <div class="col-md-4">

                                                <label class="form-label small mb-1">
                                                    Amount
                                                </label>

                                                <input
                                                    type="number"
                                                    class="form-control payment-amount"
                                                    min="0"
                                                    step="0.01"
                                                    placeholder="0.00"
                                                >

                                            </div>

                                            <div class="col-md-4 payment-reference-container d-none">

                                                <label class="form-label small mb-1">
                                                    Reference Number
                                                </label>

                                                <input
                                                    type="text"
                                                    class="form-control payment-reference"
                                                    placeholder="Reference Number"
                                                >

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="d-grid mb-3">

                                <button
                                    type="button"
                                    class="btn btn-outline-success"
                                    id="btnAddPayment"
                                >

                                    <i class="bi bi-plus-circle"></i>

                                    Add Payment Method

                                </button>

                            </div>

                            <button
                                class="btn btn-outline-secondary w-100"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#discountPanel"
                            >

                                Discount & More Options

                            </button>

                            <div
                                id="discountPanel"
                                class="collapse mt-3"
                            >

                                <div class="card">

                                    <div class="card-body">

                                        <div class="row g-3">

                                            <div class="col-md-6">

                                                <label class="form-label">
                                                    Discount Type
                                                </label>

                                                <select
                                                    id="discountType"
                                                    class="form-select"
                                                >

                                                    <option value="">
                                                        No Discount
                                                    </option>

                                                    <option value="senior">
                                                        Senior Citizen
                                                    </option>

                                                    <option value="pwd">
                                                        PWD
                                                    </option>

                                                    <option value="student">
                                                        Student
                                                    </option>

                                                    <option value="employee">
                                                        Employee
                                                    </option>

                                                    <option value="manual">
                                                        Manual Discount
                                                    </option>

                                                </select>

                                            </div>

                                            <div
                                                class="col-md-6 d-none"
                                                id="manualDiscountSection"
                                            >

                                                <div class="input-group">

                                                    <select
                                                        id="discountMode"
                                                        class="form-select"
                                                    >

                                                        <option value="percentage">
                                                            Percentage
                                                        </option>

                                                        <option value="fixed">
                                                            Fixed Amount
                                                        </option>

                                                    </select>

                                                    <input
                                                        type="number"
                                                        id="discountValue"
                                                        class="form-control"
                                                        placeholder="Value"
                                                    >

                                                </div>

                                            </div>

                                        </div>

                                        <div
                                            class="row g-3 mt-2 d-none"
                                            id="discountInfoSection"
                                        >

                                            <div class="col-md-6">

                                                <input
                                                    type="text"
                                                    id="discountHolder"
                                                    class="form-control"
                                                    placeholder="Customer Name"
                                                >

                                            </div>

                                            <div class="col-md-6">

                                                <input
                                                    type="text"
                                                    id="discountIdNo"
                                                    class="form-control"
                                                    placeholder="ID Number"
                                                >

                                            </div>

                                        </div>

                                        <div class="mt-3">

                                        <textarea
                                            id="paymentNotes"
                                            class="form-control"
                                            rows="2"
                                            placeholder="Notes (Optional)"
                                        ></textarea>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="col-lg-5">

                            <div class="card border-success h-100">

                                <div class="card-body d-flex flex-column">

                                    <div class="d-flex justify-content-between mb-2">

                                    <span class="form-label">
                                        Subtotal
                                    </span>

                                        <strong id="summarySubtotalModal"  class="text-warning fs-6">
                                            ₱0.00
                                        </strong>

                                    </div>

                                    <div class="d-flex justify-content-between mb-2">

                                    <span class="form-label">Discount</span>

                                        <strong id="summaryDiscountModal" class="text-warning fs-6">
                                            ₱0.00
                                        </strong>

                                    </div>

                                    <div class="d-flex justify-content-between mb-2">

                                    <span class="form-label">
                                        Paid
                                    </span>

                                        <strong
                                            id="paymentPaid"
                                            class="text-primary fs-6"
                                        >
                                            ₱0.00
                                        </strong>

                                    </div>

                                    <div class="d-flex justify-content-between">

                                    <span class="form-label">
                                        Balance
                                    </span>

                                        <strong
                                            id="paymentBalanceSummary"
                                            class="text-danger  fs-6"
                                        >
                                            ₱0.00
                                        </strong>

                                    </div>

                                    <hr>

                                    <div class="text-center my-auto">

                                        <div class="text-uppercase fw-semibold mb-2 form-label">
                                            Change
                                        </div>

                                        <div
                                            id="paymentChange"
                                            class="fw-bold text-success"
                                            style="
                                            font-size:4rem;
                                            line-height:1;
                                        "
                                        >
                                            ₱0.00
                                        </div>

                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn-success btn-lg w-100 mt-4"
                                        id="btnConfirmPayment"
                                    >

                                        COMPLETE SALE

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div
        class="modal fade"
        id="itemDiscountModal"
        tabindex="-1"
    >

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Item Discount
                    </h5>

                </div>

                <div class="modal-body">

                    <input
                        type="number"
                        id="itemDiscountValue"
                        class="form-control"
                        min="0"
                        step="0.01"
                    >

                </div>

                <div class="modal-footer">

                    <button
                        class="btn btn-primary"
                        id="btnApplyItemDiscount"
                    >
                        Apply
                    </button>

                </div>

            </div>

        </div>

    </div>

    <script>
        function renderSaleStatus(type, success, status, message = '') {
            const statuses = {
                pending: {
                    icon: 'bi-cart-fill',
                    title: 'ACTIVE SALE'
                },
                completed: {
                    icon: 'bi-check-circle-fill',
                    title: 'SALE COMPLETED'
                },
                cancelled: {
                    icon: 'bi-x-circle-fill',
                    title: 'SALE CANCELLED'
                },
                on_hold: {
                    icon: 'bi-pause-circle-fill',
                    title: 'SALE ON HOLD'
                }
            };

            const s = statuses[status] || statuses.pending;

            const html = `
                <div class="sale-status sale-status-${type}">
                    <i class="bi ${s.icon}"></i>
                    <div class="sale-status-content">
                        <strong>${s.title}</strong>
                        ${message ? `<small>${message}</small>` : ''}
                    </div>
                </div>
            `;

            document.querySelectorAll('.saleStatusContainer').forEach(el => {
                el.innerHTML = html;
            });
        }





        document.addEventListener('DOMContentLoaded', () => {

            const toggle = document.getElementById('themeToggle');

            if (!toggle) return;

            // Restore saved theme
            const savedTheme =
                localStorage.getItem('theme') || 'light';

            document.documentElement.setAttribute(
                'data-theme',
                savedTheme
            );

            toggle.checked = savedTheme === 'dark';

            // Switch theme
            toggle.addEventListener('change', function () {

                const theme =
                    this.checked
                        ? 'dark'
                        : 'light';

                document.documentElement.setAttribute(
                    'data-theme',
                    theme
                );

                localStorage.setItem(
                    'theme',
                    theme
                );

            });

        });

    </script>

@endsection
