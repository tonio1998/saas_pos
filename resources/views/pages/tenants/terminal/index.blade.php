@extends('layouts.app')

@section('title', 'Sales History')

@section('content')

    <x-page-header
        title="Sales History"
        subtitle="View and Manage Sales Transactions"
    >
        <x-slot:action>

            <a
                href="{{ route('sales.create') }}"
                class="btn btn-success btn-md"
            >
                <i class="bi bi-cart-check"></i>
                New Sale
            </a>

        </x-slot:action>
    </x-page-header>

    <div class="row g-3 mb-3">

        <div class="col-md-3">

            <x-card>

                <div class="text-muted">
                    Sales Today
                </div>

                <h3 class="mb-0 text-success">
                    ₱{{ number_format($salesToday ?? 0, 2) }}
                </h3>

            </x-card>

        </div>

        <div class="col-md-3">

            <x-card>

                <div class="text-muted">
                    Profit Today
                </div>

                <h3 class="mb-0 text-primary">
                    ₱{{ number_format($profitToday ?? 0, 2) }}
                </h3>

            </x-card>

        </div>

        <div class="col-md-3">

            <x-card>

                <div class="text-muted">
                    Transactions
                </div>

                <h3 class="mb-0">
                    {{ number_format($transactionsToday ?? 0) }}
                </h3>

            </x-card>

        </div>

        <div class="col-md-3">

            <x-card>

                <div class="text-muted">
                    Average Sale
                </div>

                <h3 class="mb-0 text-warning">
                    ₱{{ number_format($averageSale ?? 0, 2) }}
                </h3>

            </x-card>

        </div>

    </div>

    <x-card>

        <x-datatable
            id="salesTable"
            :columns="[
                'Actions',
                'Invoice No.',
                'Date & Time',
                'Customer',
                'Items',
                'Subtotal',
                'Discount',
                'Total Sales',
                'Profit',
                'Payment Method',
                'Tendered',
                'Change',
                'Status',
                'Cashier'
            ]"
            :ajax="route('sales.data')"
            :datatableColumns="[
                [
                    'data' => 'actions',
                    'orderable' => false,
                    'searchable' => false
                ],
                [
                    'data' => 'invoice_number'
                ],
                [
                    'data' => 'sale_date'
                ],
                [
                    'data' => 'customer'
                ],
                [
                    'data' => 'total_items'
                ],
                [
                    'data' => 'subtotal'
                ],
                [
                    'data' => 'discount'
                ],
                [
                    'data' => 'total'
                ],
                [
                    'data' => 'profit'
                ],
                [
                    'data' => 'payment_method'
                ],
                [
                    'data' => 'tendered'
                ],
                [
                    'data' => 'change_amount'
                ],
                [
                    'data' => 'status'
                ],
                [
                    'data' => 'cashier'
                ]
            ]"
        />

    </x-card>

    <div
        class="modal fade"
        id="saleDetailsModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div
            class="modal-dialog modal-xl modal-dialog-scrollable"
        >

            <div class="modal-content border-0 shadow">

                <div class="modal-header">

                    <div>

                        <h5 class="modal-title mb-1">
                            Transaction Details
                        </h5>

                        <small
                            class="text-muted"
                            id="detailInvoice"
                        ></small>

                    </div>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <div class="row g-3 mb-4">

                        <div class="col-md-3">

                            <div class="card h-100">

                                <div class="card-body">

                                    <div class="text-muted small">
                                        Customer
                                    </div>

                                    <div
                                        class="fw-semibold"
                                        id="detailCustomer"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card h-100">

                                <div class="card-body">

                                    <div class="text-muted small">
                                        Cashier
                                    </div>

                                    <div
                                        class="fw-semibold"
                                        id="detailCashier"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card h-100">

                                <div class="card-body">

                                    <div class="text-muted small">
                                        Date & Time
                                    </div>

                                    <div
                                        class="fw-semibold"
                                        id="detailDate"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card h-100">

                                <div class="card-body">

                                    <div class="text-muted small">
                                        Status
                                    </div>

                                    <span
                                        class="badge fs-6"
                                        id="detailStatus"
                                    ></span>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="row g-3 mb-4">

                        <div class="col-md-3">

                            <div class="card text-center h-100">

                                <div class="card-body">

                                    <div class="small text-muted">
                                        Subtotal
                                    </div>

                                    <div
                                        class="fw-bold fs-5"
                                        id="detailSubtotal"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card text-center h-100">

                                <div class="card-body">

                                    <div class="small text-muted">
                                        Discount
                                    </div>

                                    <div
                                        class="fw-bold fs-5 text-danger"
                                        id="detailDiscount"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card text-center h-100 border-primary">

                                <div class="card-body">

                                    <div class="small text-muted">
                                        Total Sale
                                    </div>

                                    <div
                                        class="fw-bold fs-3 text-primary"
                                        id="detailTotal"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="card text-center h-100 border-warning">

                                <div class="card-body">

                                    <div class="small text-muted">
                                        Profit
                                    </div>

                                    <div
                                        class="fw-bold fs-3 text-warning"
                                        id="detailProfit"
                                    ></div>

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="card mb-4">

                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >

                        <span class="fw-semibold">
                            Payment Details
                        </span>

                            <span
                                class="badge bg-primary"
                                id="paymentCount"
                            >
                            0 Payments
                        </span>

                        </div>

                        <div class="table-responsive">

                            <table
                                class="table table-hover align-middle mb-0"
                                id="paymentTable"
                            >

                                <thead class="table-light">

                                <tr>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th class="text-end">
                                        Amount
                                    </th>
                                </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                    <div class="card mb-4">

                        <div
                            class="card-header d-flex justify-content-between align-items-center"
                        >

                        <span class="fw-semibold">
                            Items Purchased
                        </span>

                            <span
                                class="badge bg-secondary"
                                id="itemCount"
                            >
                            0 Items
                        </span>

                        </div>

                        <div class="table-responsive">

                            <table
                                class="table table-striped table-hover align-middle mb-0"
                                id="itemsTable"
                            >

                                <thead class="table-light">

                                <tr>
                                    <th>Barcode</th>
                                    <th>Product</th>
                                    <th class="text-center">
                                        Qty
                                    </th>
                                    <th class="text-end">
                                        Price
                                    </th>
                                    <th class="text-end">
                                        Total
                                    </th>
                                    <th class="text-end">
                                        Profit
                                    </th>
                                </tr>

                                </thead>

                                <tbody></tbody>

                            </table>

                        </div>

                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="card h-100">

                                <div class="card-header fw-semibold">
                                    Notes
                                </div>

                                <div
                                    class="card-body"
                                    id="detailNotes"
                                >

                                <span class="text-muted">
                                    No notes available.
                                </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        id="btnReprint"
                    >
                        <i class="bi bi-printer me-1"></i>
                        Reprint Receipt
                    </button>

                </div>

            </div>

        </div>

    </div>

    <script>

    </script>
@endsection
