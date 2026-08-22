@extends('layouts.app')

@section('title', 'Sales History & Revenue Audit Log | LikhaPOS')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-3 p-2 bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                    <i class="bi bi-receipt-cutoff fs-5"></i>
                </div>
                <div>
                    <h4 class="fw-black text-dark mb-0 font-mono" style="letter-spacing:-0.5px;">Sales History & Revenue Audit Log</h4>
                </div>
            </div>
            <p class="text-muted small mb-0 ms-5 ps-1">
                Real-time ledger of completed checkout transactions, gross revenue, profit margins, and payment breakdown.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cashiering.cash-shifts.index') }}" class="btn btn-white border rounded-3 px-3 py-2 fw-bold text-dark extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-clock-history text-primary"></i>
                <span>Cash Shifts</span>
            </a>

            <a href="{{ route('terminal.index') }}" class="btn btn-success fw-bold px-3.5 py-2 rounded-3 shadow-xs d-flex align-items-center gap-2 hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.82rem;">
                <i class="bi bi-calculator-fill fs-6"></i>
                <span>Open POS Terminal</span>
            </a>
        </div>
    </div>

    {{-- Top 4 KPI Metrics Summary Cards --}}
    <div class="row g-3 mb-4">
        {{-- 1. Sales Today --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Today's Revenue</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#ecfdf5;color:#059669;"><i class="bi bi-cash-stack fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-success mb-0">₱{{ number_format($salesToday ?? 0, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Gross Completed Sales</div>
            </div>
        </div>

        {{-- 2. Transactions Count Today --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Orders Processed</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-bag-check-fill fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-primary mb-0">{{ number_format($transactionsToday ?? 0) }} <span class="fs-6 text-muted fw-normal">orders</span></div>
                <div class="text-muted extra-small mt-1 fw-semibold">Completed Receipts</div>
            </div>
        </div>

        {{-- 3. Average Sale --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Average Ticket Sale</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#fef3c7;color:#d97706;"><i class="bi bi-graph-up-arrow fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-warning-emphasis mb-0">₱{{ number_format($averageSale ?? 0, 2) }}</div>
                <div class="text-muted extra-small mt-1 fw-semibold">Avg Revenue per Order</div>
            </div>
        </div>

        {{-- 4. Status Indicator --}}
        <div class="col-6 col-md-3">
            <div class="modern-card h-100 p-3 bg-white border rounded-4 shadow-xs">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="kpi-label text-muted extra-small fw-extrabold text-uppercase">Channel & Terminal</span>
                    <div class="kpi-icon-box rounded-3 p-1.5" style="background:#f5f3ff;color:#7c3aed;"><i class="bi bi-display fs-6"></i></div>
                </div>
                <div class="kpi-value font-mono fs-4 fw-black text-purple mb-0" style="color:#7c3aed;">LikhaPOS</div>
                <div class="text-success extra-small mt-1 fw-bold"><i class="bi bi-check-circle-fill me-1"></i>System Active</div>
            </div>
        </div>
    </div>

    {{-- Main Sales History Table Card --}}
    <div class="card border rounded-4 shadow-xs bg-white overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-receipt text-success fs-5"></i>
                <h5 class="fw-bold text-dark mb-0 fs-6">Checkout Sales Audit Ledger</h5>
            </div>
        </div>

        <div class="card-body p-3">
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
                    ['data' => 'actions', 'orderable' => false, 'searchable' => false],
                    ['data' => 'invoice_number'],
                    ['data' => 'sale_date'],
                    ['data' => 'customer'],
                    ['data' => 'total_items'],
                    ['data' => 'subtotal'],
                    ['data' => 'discount'],
                    ['data' => 'total'],
                    ['data' => 'profit'],
                    ['data' => 'payment_method'],
                    ['data' => 'tendered'],
                    ['data' => 'change_amount'],
                    ['data' => 'status'],
                    ['data' => 'cashier']
                ]"
            />
        </div>
    </div>
</div>

{{-- Transaction Details Modal --}}
<div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-white border-bottom p-3.5">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0 fs-6">Transaction Details</h5>
                    <small class="text-muted font-mono" id="detailInvoice"></small>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted extra-small fw-extrabold text-uppercase">Customer</div>
                            <div class="fw-bold text-dark mt-1" id="detailCustomer"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted extra-small fw-extrabold text-uppercase">Cashier</div>
                            <div class="fw-bold text-dark mt-1" id="detailCashier"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted extra-small fw-extrabold text-uppercase">Payment Method</div>
                            <div class="fw-bold text-dark mt-1" id="detailPaymentMethod"></div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted extra-small fw-extrabold text-uppercase">Date & Time</div>
                            <div class="fw-bold text-dark font-mono mt-1" id="detailDate"></div>
                        </div>
                    </div>
                </div>

                {{-- Purchased Items Table --}}
                <h6 class="fw-bold text-dark mb-2 font-mono">Purchased Line Items</h6>
                <div class="table-responsive rounded-3 border mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item Name</th>
                                <th class="text-end">Price</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Line Total</th>
                            </tr>
                        </thead>
                        <tbody id="detailItemsBody"></tbody>
                    </table>
                </div>

                {{-- Summary Box --}}
                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="p-3.5 rounded-3 border bg-light">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Subtotal:</span>
                                <span id="detailSubtotal" class="font-mono fw-bold text-dark">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted small">Discount:</span>
                                <span id="detailDiscount" class="font-mono fw-bold text-danger">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between pt-2 border-top">
                                <span class="fw-bold text-dark">Total Amount Due:</span>
                                <span id="detailTotal" class="font-mono fw-black text-success fs-5">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    function initSalesDetails($) {
        $(document).on('click', '.btn-view-sale', function () {
            const saleId = $(this).data('id');
            if (!saleId) return;

            $.getJSON(`/sales/${saleId}/details`, function (data) {
                $('#detailInvoice').text(data.invoice_no);
                $('#detailCustomer').text(data.customer);
                $('#detailCashier').text(data.cashier);
                $('#detailPaymentMethod').text(data.payment_method);
                $('#detailDate').text(data.sale_date);
                $('#detailSubtotal').text('₱' + parseFloat(data.subtotal || 0).toFixed(2));
                $('#detailDiscount').text('₱' + parseFloat(data.discount || 0).toFixed(2));
                $('#detailTotal').text('₱' + parseFloat(data.total || 0).toFixed(2));

                let rows = '';
                (data.items || []).forEach(item => {
                    rows += `
                        <tr>
                            <td class="fw-bold text-dark">${item.name}</td>
                            <td class="text-end font-mono">₱${parseFloat(item.price).toFixed(2)}</td>
                            <td class="text-center font-mono">${item.qty}</td>
                            <td class="text-end font-mono fw-bold">₱${parseFloat(item.total).toFixed(2)}</td>
                        </tr>
                    `;
                });
                $('#detailItemsBody').html(rows);
                $('#saleDetailsModal').modal('show');
            });
        });
    }

    function checkJQuery() {
        if (window.$) {
            initSalesDetails(window.$);
        } else {
            setTimeout(checkJQuery, 50);
        }
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        checkJQuery();
    } else {
        document.addEventListener('DOMContentLoaded', checkJQuery);
    }
})();
</script>
@endpush
