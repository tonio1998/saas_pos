@extends('layouts.pos')

@section('title', 'POS Terminal')

@section('content')

    <!-- Full-Width Top Bar -->
    @include('pages.tenants.terminal.search_bar')

    <div class="pos-shell flex-grow-1 d-flex overflow-hidden mt-2">

        <div class="pos-left flex-grow-1 h-100 overflow-hidden d-flex flex-column">

            @include(
                'pages.tenants.terminal.products.product_grid'
            )

        </div>

        <div class="pos-right d-flex flex-column h-100 bg-white border-start">
            @php
                $status = strtolower($sale->sale_status ?? 'pending');
                $isRefunded = in_array($status, ['refunded', 'refund']);
                $isPartial = $status === 'partial_refund';
                $isSalePaid = in_array($status, ['completed', 'refunded', 'refund', 'partial_refund']);
                $refundSummary = $sale->getRefundSummary();
                $returnedMap = $refundSummary['returned_map'] ?? [];
                $refundedAmount = (float)($refundSummary['refunded_amount'] ?? 0);
                $refundedQty = (float)($refundSummary['refunded_qty'] ?? 0);
                $retainedTotal = max(0, (float)$sale->total_amount - $refundedAmount);
            @endphp
            <input type="hidden" id="saleId" value="{{ encryptId($sale->id) }}">
            <input type="hidden" id="isSalePaid" value="{{ $isSalePaid ? '1' : '0' }}">
            <input type="hidden" id="saleCode" value="{{ $sale->sale_code }}">

            @if($isSalePaid)
                <script id="pastSaleDataPayload" type="application/json">
                    {!! json_encode([
                        'id' => encryptId($sale->id),
                        'sale_code' => $sale->sale_code,
                        'invoice_no' => $sale->sale_code,
                        'sale_status' => $status,
                        'is_paid' => true,
                        'is_refunded' => $isRefunded,
                        'is_partial' => $isPartial,
                        'refunded_amount' => $refundedAmount,
                        'refunded_qty' => $refundedQty,
                        'retained_total' => $retainedTotal,
                        'customer_name' => $sale->customer?->CustomerName ?? 'Walk-in Customer',
                        'subtotal' => (float)$sale->subtotal,
                        'discount' => (float)$sale->discount_amount,
                        'discount_amount' => (float)$sale->discount_amount,
                        'total' => (float)$sale->total_amount,
                        'total_amount' => (float)$sale->total_amount,
                        'tendered_amount' => (float)($sale->tendered_amount > 0 ? $sale->tendered_amount : ($sale->payments->sum('amount') ?: $sale->total_amount)),
                        'change_amount' => (float)($sale->change_amount ?? 0),
                        'items' => $sale->items->map(function($it) use ($returnedMap, $isRefunded) {
                            $itemKey = $it->product_id . '_' . ($it->variant_id ?? 0);
                            $origQty = (float)$it->qty;
                            $returnedQty = $isRefunded ? $origQty : min($origQty, (float)($returnedMap[$itemKey] ?? 0));
                            $retainedQty = max(0, $origQty - $returnedQty);
                            $isItemRefunded = $isRefunded || ($origQty > 0 && $returnedQty >= $origQty);
                            $isItemPartial = !$isItemRefunded && ($returnedQty > 0);
                            $unitPrice = (float)$it->unit_price;
                            $origSubtotal = (float)$it->line_total;
                            $retainedSubtotal = $isItemRefunded ? 0 : ($origQty > 0 ? ($origSubtotal * ($retainedQty / $origQty)) : 0);

                            return [
                                'id' => $it->product_id,
                                'variant_id' => $it->variant_id,
                                'cartKey' => $itemKey,
                                'name' => $it->product_name,
                                'price' => $unitPrice,
                                'original_price' => $unitPrice,
                                'qty' => $origQty,
                                'original_qty' => $origQty,
                                'returned_qty' => $returnedQty,
                                'retained_qty' => $retainedQty,
                                'is_item_refunded' => $isItemRefunded,
                                'is_item_partial' => $isItemPartial,
                                'subtotal' => $origSubtotal,
                                'original_subtotal' => $origSubtotal,
                                'retained_subtotal' => $retainedSubtotal,
                                'discount' => (float)$it->discount_amount,
                                'unit' => $it->product?->unit?->name ?? (is_string($it->product?->unit) ? $it->product->unit : ''),
                                'allow_decimal_qty' => (bool)($it->product?->allow_decimal_qty ?? false),
                            ];
                        }),
                        'payments' => $sale->payments
                    ]) !!}
                </script>
            @endif

            <!-- Cart Customer & Header -->

            @include('pages.tenants.terminal.cart.cart_header')

            <!-- Scrollable Cart Items -->
            <div class="pos-cart-items-wrapper flex-grow-1 overflow-y-auto px-3 py-2">
                @include('pages.tenants.terminal.cart.cart_items')
            </div>

            <!-- Pinned Bottom Cart Summary & Action -->
            <div class="pos-cart-bottom-pinned border-top bg-white px-3 py-2.5 shadow-sm mt-auto">
                @include('pages.tenants.terminal.cart.cart_summary')
                @include('pages.tenants.terminal.cart.checkout_button')

                <div class="d-none">
                    <div class="status-indicator ready" id="statusIndicator"></div>
                    <div class="status-message" id="posStatus">Ready</div>
                    <div class="activity-list" id="activityList"></div>
                </div>
            </div>
        </div>

        <!-- Column 3: Live Thermal Receipt Preview (Zero-Waste Desktop Grid) -->
        <div class="pos-receipt-pane d-flex flex-column h-100 bg-slate-50 border-start" id="posReceiptPreviewPane">
            @include('pages.tenants.terminal.receipt_preview')
        </div>

    </div>


    <div
        class="modal fade"
        id="paymentModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-fullscreen m-0 p-0">
            <div class="modal-content border-0 shadow-lg rounded-0 h-100 d-flex flex-column">

                {{-- Fixed Top Header: Sales Title & Sticky Header KPIs --}}
                <div class="modal-header border-bottom py-2.5 px-4 bg-white d-flex align-items-center justify-content-between flex-wrap gap-3 sticky-top shadow-xs" style="z-index:1050;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-white shadow-xs" style="width:42px;height:42px;background:linear-gradient(135deg, #059669, #047857);">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-dark mb-0 font-mono">Complete Sale</h5>
                                <span class="badge bg-light text-dark border extra-small font-mono">#{{ $sale->sale_code }}</span>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem;">Fast tender, multi-payments, and checkout processing</small>
                        </div>
                    </div>

                    {{-- Fixed Top Header KPIs: Total Amount Due & Remaining Balance --}}
                    <div class="d-flex align-items-center gap-3 ms-auto me-3">
                        {{-- Total Amount Due KPI --}}
                        <div class="px-3 py-1.5 rounded-3 d-flex align-items-center gap-2.5" style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%);color:#fff;">
                            <div class="text-end">
                                <div class="text-secondary extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;font-size:0.65rem;">Total Amount Due</div>
                                <div id="paymentTotal" class="font-mono fw-black text-success lh-1 fs-4">₱0.00</div>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono fw-bold px-1.5 py-0.5">Payable</span>
                        </div>

                        {{-- Remaining Balance KPI --}}
                        <div class="px-3 py-1.5 rounded-3 border bg-white shadow-2xs d-flex align-items-center gap-2.5">
                            <div class="text-end">
                                <div class="text-muted extra-small fw-bold text-uppercase" style="letter-spacing:0.5px;font-size:0.65rem;">Remaining Balance</div>
                                <div id="paymentBalance" class="font-mono fw-black text-danger lh-1 fs-4">₱0.00</div>
                            </div>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small font-mono fw-bold px-1.5 py-0.5">Unpaid</span>
                        </div>
                    </div>

                    <button type="button" class="btn-close fs-5" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body: Zero-Scroll 3-Column Layout --}}
                <div class="modal-body p-3.5 bg-light bg-opacity-75 overflow-hidden flex-grow-1">
                    <div class="saleStatusContainer"></div>

                    <div class="row g-3 h-100 align-items-stretch">

                        {{-- COLUMN 1: Payment Controls & CRM (5 cols) --}}
                        <div class="col-lg-4 d-flex flex-column gap-3">

                            {{-- Quick Cash Tender Presets --}}
                            <div class="bg-white p-3 rounded-4 border shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="extra-small fw-extrabold text-uppercase text-muted" style="letter-spacing:0.6px;">
                                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Quick Cash Tender (Fast Pay)
                                    </span>
                                    <span class="badge bg-light text-muted border extra-small">Auto-fills</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2" id="quickTenderContainer">
                                    <button type="button" class="btn btn-outline-success fw-extrabold font-mono rounded-3 px-2.5 py-1 extra-small quick-tender-exact shadow-xs">
                                        <i class="bi bi-check2-all me-1"></i>Exact
                                    </button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-2.5 py-1 extra-small quick-tender-btn shadow-xs" data-val="20">₱20</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-2.5 py-1 extra-small quick-tender-btn shadow-xs" data-val="50">₱50</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1 extra-small quick-tender-btn shadow-xs" data-val="100">₱100</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1 extra-small quick-tender-btn shadow-xs" data-val="200">₱200</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1 extra-small quick-tender-btn shadow-xs" data-val="500">₱500</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1 extra-small quick-tender-btn shadow-xs" data-val="1000">₱1,000</button>
                                </div>
                            </div>

                            {{-- Complete CRM Customer Profile Card --}}
                            <div class="bg-white p-3 rounded-4 border shadow-sm" id="modalCustomerCard">
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div class="d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">
                                        <div class="rounded-3 bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0 text-success shadow-xs" style="width:38px;height:38px;">
                                            <i class="bi bi-person-badge-fill fs-5"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-1.5">
                                                <div class="fw-black text-dark small lh-sm mb-0 text-truncate" id="modalCustomerName">Walk-in Customer</div>
                                                <div id="modalCustomerBadge" class="d-none">
                                                    <span class="badge bg-success-subtle text-success extra-small fw-bold border border-success-subtle">Linked</span>
                                                </div>
                                            </div>
                                            <div id="modalNoCustomer" class="text-muted extra-small mt-0.5">
                                                No customer assigned
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        id="btnModalSelectCustomer"
                                        class="btn btn-light border rounded-pill px-3 py-1 text-dark fw-bold extra-small shadow-xs d-flex align-items-center gap-1"
                                        title="Select, Search, or Add Customer"
                                    >
                                        <i class="bi bi-people-fill text-primary"></i>
                                        <span>Change</span>
                                    </button>
                                </div>

                                {{-- Utang warning banner --}}
                                <div id="utangNoCustomerWarning" class="d-none mt-2 p-2 rounded-3 bg-warning bg-opacity-10 border border-warning-subtle d-flex align-items-center gap-2" style="font-size:0.75rem;">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-6"></i>
                                    <span class="text-warning-emphasis fw-semibold">
                                        <strong>Utang Notice:</strong> Select a customer to track credit balance.
                                    </span>
                                </div>
                            </div>

                            {{-- Modal Triggers for Tender & Discounts (Zero Scrolling!) --}}
                            <div class="bg-white p-3 rounded-4 border shadow-sm d-flex flex-column gap-2.5">
                                <button
                                    type="button"
                                    class="btn btn-light border border-primary-subtle w-100 py-2.5 px-3 text-start d-flex align-items-center justify-content-between rounded-3 shadow-2xs hover-lift"
                                    data-bs-toggle="modal"
                                    data-bs-target="#checkoutTenderModal"
                                >
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 bg-primary bg-opacity-10 p-1.5 text-primary">
                                            <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-black text-dark extra-small font-mono text-uppercase">Tender & Split Payments</div>
                                            <div class="text-muted extra-small">Cash, GCash, Card, Utang / Credit</div>
                                        </div>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted extra-small"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-light border border-success-subtle w-100 py-2.5 px-3 text-start d-flex align-items-center justify-content-between rounded-3 shadow-2xs hover-lift"
                                    data-bs-toggle="modal"
                                    data-bs-target="#checkoutDiscountModal"
                                >
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 bg-success bg-opacity-10 p-1.5 text-success">
                                            <i class="bi bi-percent fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-black text-dark extra-small font-mono text-uppercase">Special Discounts & Notes</div>
                                            <div class="text-muted extra-small">Senior Citizen, PWD, Student (20%)</div>
                                        </div>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted extra-small"></i>
                                </button>
                            </div>

                        </div>

                        {{-- COLUMN 2: Order Summary & Change Hero Card (4 cols) --}}
                        <div class="col-lg-4 d-flex flex-column justify-content-between gap-3">
                            <div class="bg-white rounded-4 border shadow-sm p-3.5 h-100 d-flex flex-column justify-content-between">

                                <div>
                                    <div class="d-flex align-items-center justify-content-between pb-2 mb-3 border-bottom">
                                        <span class="fw-black text-dark font-mono text-uppercase extra-small">Order Summary</span>
                                        <span class="badge bg-light text-dark border extra-small font-mono fw-bold">Live</span>
                                    </div>

                                    {{-- Line items breakdown with BOLD values --}}
                                    <div class="d-flex flex-column gap-2 mb-2">
                                        <div class="d-flex justify-content-between align-items-center py-0.5">
                                            <span class="text-muted extra-small">Cart Subtotal:</span>
                                            <strong id="summarySubtotalModal" class="font-mono text-dark fw-black small">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center py-0.5">
                                            <span class="text-muted extra-small">Discount Applied:</span>
                                            <strong id="summaryDiscountModal" class="font-mono text-warning fw-black small">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center py-0.5" id="paymentModalProfitRow">
                                            <span class="text-muted extra-small">Est. Net Profit (Margin):</span>
                                            <strong id="summaryProfitModal" class="font-mono text-success fw-black small">+₱0.00 (+0.0%)</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <span class="text-dark fw-extrabold small">Total Payable:</span>
                                            <strong class="font-mono text-success fw-black fs-5" id="summaryTotalModal">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                            <span class="text-muted extra-small fw-bold">Tendered / Paid:</span>
                                            <strong id="paymentPaid" class="font-mono text-primary fw-black fs-5">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center py-0.5">
                                            <span class="text-muted extra-small fw-bold">Remaining Balance:</span>
                                            <strong id="paymentBalanceSummary" class="font-mono text-danger fw-black fs-5">₱0.00</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hero Change Card --}}
                                <div class="my-auto py-2">
                                    <div class="p-3 rounded-4 text-center border shadow-xs" style="background:linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);border-color:#a7f3d0 !important;">
                                        <div class="extra-small text-uppercase fw-extrabold mb-1" style="letter-spacing:0.8px;color:#047857;">
                                            <i class="bi bi-cash-coin me-1"></i>Sukli / Change Due
                                        </div>
                                        <div id="paymentChange" class="font-mono fw-black lh-1 my-1" style="font-size:2.8rem;color:#059669;letter-spacing:-1px;">
                                            ₱0.00
                                        </div>
                                        <small class="text-muted extra-small d-block" style="color:#065f46 !important;">
                                            Exact or excess cash returned
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- COLUMN 3: Live 78mm Thermal Receipt Preview Panel (4 cols) --}}
                        <div class="col-lg-4">
                            <div class="bg-white rounded-4 border shadow-sm p-3 h-100 d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-receipt-cutoff text-primary fs-6"></i>
                                        <span class="fw-black text-dark font-mono extra-small text-uppercase">Live Receipt Preview</span>
                                    </div>
                                    <span class="badge bg-dark text-white font-mono extra-small">78mm Thermal</span>
                                </div>

                                <div class="flex-grow-1 bg-dark rounded-3 p-1.5 d-flex align-items-center justify-content-center overflow-hidden">
                                    <iframe id="checkoutReceiptPreviewFrame" style="width:100%;height:100%;min-height:360px;border:none;background:#fff;" class="rounded-2 shadow-xs"></iframe>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Fixed Bottom Modal Footer: Complete Sale Button --}}
                <div class="modal-footer border-top bg-white py-2.5 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3 sticky-bottom shadow-lg" style="z-index:1050;">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4 py-2 fw-bold font-mono extra-small" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> CANCEL / CLOSE (ESC)
                    </button>

                    <div class="d-flex align-items-center gap-3 flex-grow-1 justify-content-end" style="max-width:500px;">
                        <button
                            type="button"
                            class="btn btn-success w-100 py-3 rounded-3 fw-black font-mono shadow-sm d-flex align-items-center justify-content-center gap-2"
                            id="btnConfirmPayment"
                            style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:1.2rem;letter-spacing:0.5px;"
                        >
                            <i class="bi bi-check-circle-fill fs-4"></i>
                            <span>COMPLETE SALE</span>
                            <span class="badge bg-white bg-opacity-25 text-white extra-small ms-2 d-none d-sm-inline font-sans fw-bold">Press Enter / F4</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Sub-Modal 1: Tender & Split Payments Breakdown --}}
    <div class="modal fade" id="checkoutTenderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-2 text-primary">
                            <i class="bi bi-credit-card-2-front-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title font-mono fw-bold text-dark mb-0">Tender & Payment Breakdown</h6>
                            <small class="text-muted extra-small">Add split payments (Cash, GCash, Card, Utang)</small>
                        </div>
                    </div>

                    {{-- Live Reference KPI Badges for Cashier Reference --}}
                    <div class="d-flex align-items-center gap-2 ms-auto me-3">
                        <div class="px-3 py-1 rounded-3 bg-dark text-white text-end font-mono extra-small shadow-2xs">
                            <span class="text-white-50 extra-small" style="font-size:0.65rem;">TOTAL DUE:</span>
                            <strong id="tenderModalTotal" class="text-success fw-black ms-1 fs-6">₱0.00</strong>
                        </div>
                        <div class="px-3 py-1 rounded-3 border bg-white text-end font-mono extra-small shadow-2xs">
                            <span class="text-muted extra-small" style="font-size:0.65rem;">REMAINING:</span>
                            <strong id="tenderModalBalance" class="text-danger fw-black ms-1 fs-6">₱0.00</strong>
                        </div>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div id="paymentLines" class="d-flex flex-column gap-3 mb-3.5">
                        {{-- Rendered dynamically by terminal.js --}}
                    </div>
                    <button
                        type="button"
                        class="btn btn-light border border-dashed w-100 py-3 text-muted fw-bold rounded-3 shadow-xs hover-lift"
                        id="btnAddPayment"
                        style="border-style:dashed !important;"
                    >
                        <i class="bi bi-plus-circle-fill text-success me-1.5"></i> Add Another Payment Line
                    </button>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-white d-flex justify-content-end">
                    <button type="button" class="btn btn-primary rounded-3 px-4 py-2 extra-small fw-bold" data-bs-dismiss="modal">
                        <i class="bi bi-check-circle me-1"></i> Apply Payment Tender
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sub-Modal 2: Customer Special Discounts & Remarks --}}
    <div class="modal fade" id="checkoutDiscountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom py-3 px-4 bg-white">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2 text-success">
                            <i class="bi bi-percent fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title font-mono fw-bold text-dark mb-0">Special Discounts & Remarks</h6>
                            <small class="text-muted extra-small">Senior Citizen, PWD, Student, Employee, or Manual Rate</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        {{-- Quick Discount Badges --}}
                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1.5">Quick Presets</label>
                            <div class="d-flex flex-wrap gap-1.5">
                                <button type="button" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 py-1 btn-quick-discount" data-type="senior">
                                    👵 Senior (20%)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3 py-1 btn-quick-discount" data-type="pwd">
                                    ♿ PWD (20%)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3 py-1 btn-quick-discount" data-type="student">
                                    🎓 Student (5%)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary fw-bold rounded-pill px-3 py-1 btn-quick-discount" data-type="clear">
                                    ✖ Reset
                                </button>
                            </div>
                        </div>

                        {{-- Promo Code Voucher Input --}}
                        <div class="col-12">
                            <div class="p-3 bg-white rounded-3 border">
                                <label class="form-label extra-small fw-bold text-primary text-uppercase mb-1">
                                    <i class="bi bi-ticket-perforated-fill me-1"></i>Have a Promo Code / Voucher?
                                </label>
                                <div class="input-group">
                                    <input type="text" id="inpPromoVoucherCode" class="form-control font-mono text-uppercase fw-bold" placeholder="ENTER PROMO CODE...">
                                    <button type="button" id="btnApplyPromoVoucher" class="btn btn-dark fw-bold font-mono px-3">
                                        Apply
                                    </button>
                                </div>
                                <div id="promoVoucherFeedback" class="extra-small mt-1.5" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Discount Program</label>
                            <select id="discountType" class="form-select font-mono">
                                <option value="">No Discount (Standard Rate)</option>
                                <option value="senior">Senior Citizen (20%)</option>
                                <option value="pwd">PWD (20%)</option>
                                <option value="student">Student (5%)</option>
                                <option value="employee">Employee Discount</option>
                                <option value="promo">Promo Campaign / Voucher</option>
                                <option value="manual">Manual Custom Discount</option>
                            </select>
                        </div>

                        <div class="col-12 d-none" id="manualDiscountSection">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Custom Discount Value</label>
                            <div class="input-group">
                                <select id="discountMode" class="form-select" style="max-width:120px;">
                                    <option value="percentage">% Percent</option>
                                    <option value="fixed">₱ Fixed</option>
                                </select>
                                <input type="number" id="discountValue" class="form-control font-mono fw-bold" placeholder="0">
                            </div>
                        </div>

                        <div class="col-12 d-none" id="discountInfoSection">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">ID Cardholder Name</label>
                            <input type="text" id="discountHolder" class="form-control" placeholder="Full Name as shown on ID">
                        </div>

                        <div class="col-12 d-none" id="discountIdNoSection">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Senior / PWD / Student ID No.</label>
                            <input type="text" id="discountIdNo" class="form-control font-mono" placeholder="ID Number (e.g. OSCA-99824)">
                        </div>

                        {{-- Live Profit & Margin Impact Card --}}
                        <div class="col-12 mt-2" id="discountProfitCardSection">
                            <div class="card border rounded-3 p-3 shadow-xs" id="discountProfitCard" style="background: #f8fafc; border-color: #cbd5e1 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="extra-small fw-extrabold text-uppercase text-muted" style="letter-spacing:0.5px; font-size:0.72rem;">
                                        <i class="bi bi-graph-up-arrow me-1 text-primary"></i>Live Profit & Margin Preview
                                    </span>
                                    <span id="discountProfitMarginBadge" class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2.5 py-1 rounded-pill" style="font-size:0.75rem;">
                                        Margin: +0.0%
                                    </span>
                                </div>

                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div class="p-2 bg-white rounded-2 border">
                                            <small class="text-muted extra-small d-block text-truncate fw-semibold" style="font-size:0.68rem;">Total Cost (Puhunan)</small>
                                            <span id="previewCartCost" class="font-mono fw-bold text-dark small">₱0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-white rounded-2 border">
                                            <small class="text-muted extra-small d-block text-truncate fw-semibold" style="font-size:0.68rem;">Discounted Due</small>
                                            <span id="previewDiscountedDue" class="font-mono fw-bold text-primary small">₱0.00</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-white rounded-2 border">
                                            <small class="text-muted extra-small d-block text-truncate fw-semibold" style="font-size:0.68rem;">Est. Profit (Kita)</small>
                                            <span id="previewNetProfit" class="font-mono fw-black text-success small">₱0.00</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dynamic Profit / Loss Status Warning Banner --}}
                                <div id="discountProfitAlert" class="mt-2.5 p-2 rounded-2 extra-small fw-bold d-flex align-items-center gap-2" style="background:#dcfce7; color:#166534; border:1px solid #bbf7d0;">
                                    <i class="bi bi-check-circle-fill fs-6" id="discountProfitAlertIcon"></i>
                                    <span id="discountProfitAlertText" class="lh-sm">Profitable: Safe to apply this discount.</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Sale Notes / Customer Remarks</label>
                            <textarea id="paymentNotes" class="form-control" rows="2" placeholder="Add optional transaction notes or delivery instructions..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-2.5 px-4 bg-white d-flex justify-content-end">
                    <button type="button" class="btn btn-success rounded-3 px-4 py-2 extra-small fw-bold" data-bs-dismiss="modal">
                        <i class="bi bi-check-circle me-1"></i> Apply Discount
                    </button>
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
    </script>

    <!-- Variant Selection Modal -->
    <div class="modal fade" id="variantModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-3 p-2 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            <i class="bi bi-collection-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0 text-dark" id="variantModalTitle">Select Variant</h6>
                            <small class="text-muted" id="variantModalSubtitle">Choose product package / size</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="d-flex align-items-center justify-content-between mb-2.5 px-1">
                        <span class="extra-small text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Select Option to Add to Cart</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono fw-bold px-2 py-1 rounded-pill" id="variantPricingModeBadge">
                            <i class="bi bi-tag-fill me-1"></i>Retail Pricing
                        </span>
                    </div>
                    <div class="d-flex flex-column gap-2" id="variantListContainer">
                        <!-- Variant options rendered dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Insert Service / Custom Fee / Damage Charge Modal -->
    <div class="modal fade" id="customItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); color: #fff;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-3 p-2 bg-white bg-opacity-25 text-white d-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                            <i class="bi bi-plus-square-dotted fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0 text-white font-mono">Quick Charge / Service</h6>
                            <small class="text-white text-opacity-75" style="font-size:0.75rem;">Add non-inventory fee, tote bag, damaged item, or service</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Quick Preset Badges (1-Click Insertion) -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="extra-small text-uppercase fw-bold text-muted" style="letter-spacing: 0.5px;">Quick Presets (1-Tap Select)</span>
                            <small class="text-muted extra-small">Click to fill</small>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5" id="customItemPresetsList">
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Tote Bag (Canvas)" data-price="30" data-unit="pc">
                                🛍️ Tote Bag ₱30
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Eco Bag (Large)" data-price="10" data-unit="pc">
                                🛍️ Eco Bag L ₱10
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Eco Bag (Small)" data-price="5" data-unit="pc">
                                🛍️ Eco Bag S ₱5
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Box / Packaging Fee" data-price="20" data-unit="pc">
                                📦 Packaging Box ₱20
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Nabasag na Baso (Glass)" data-price="50" data-unit="pc" style="border-color:#fecaca !important;background:#fef2f2;color:#991b1b;">
                                🍷 Nabasag na Baso ₱50
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Nabasag na Plato/Mug" data-price="60" data-unit="pc" style="border-color:#fecaca !important;background:#fef2f2;color:#991b1b;">
                                🍽️ Nabasag na Plato ₱60
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Delivery / Hatid Fee" data-price="50" data-unit="service">
                                🚚 Delivery Fee ₱50
                            </button>
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 extra-small fw-bold btn-custom-preset hover-lift" data-name="Ice / Chilling Fee" data-price="15" data-unit="pack">
                                ❄️ Ice / Chilling ₱15
                            </button>
                        </div>
                    </div>

                    <!-- Service / Item Name Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Charge / Service Name <span class="text-danger">*</span></label>
                        <div class="input-group shadow-2xs">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-tag"></i></span>
                            <input
                                type="text"
                                id="customItemName"
                                class="form-control"
                                placeholder="e.g. Additional charge sa Tote Bag, Nabasag na Baso..."
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- Price & Qty Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label class="form-label small fw-bold text-dark mb-1">Amount / Price (₱) <span class="text-danger">*</span></label>
                            <div class="input-group shadow-2xs">
                                <span class="input-group-text bg-light fw-bold font-mono">₱</span>
                                <input
                                    type="number"
                                    step="0.01"
                                    id="customItemPrice"
                                    class="form-control font-mono fw-bold fs-6 text-dark"
                                    placeholder="0.00"
                                    autocomplete="off"
                                >
                            </div>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-bold text-dark mb-1">Quantity</label>
                            <input
                                type="number"
                                step="1"
                                min="1"
                                id="customItemQty"
                                class="form-control font-mono fw-bold text-center"
                                value="1"
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <!-- Quick Amount Preset Buttons -->
                    <div class="mb-3">
                        <div class="extra-small text-uppercase fw-bold text-muted mb-1.5" style="letter-spacing: 0.5px;">Quick Amount</div>
                        <div class="d-flex gap-1.5 flex-wrap">
                            <button type="button" class="btn btn-sm btn-light border rounded-2 px-2 py-0.5 extra-small font-mono fw-bold btn-custom-price-add" data-add="5">+₱5</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-2 px-2 py-0.5 extra-small font-mono fw-bold btn-custom-price-add" data-add="10">+₱10</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-2 px-2 py-0.5 extra-small font-mono fw-bold btn-custom-price-add" data-add="20">+₱20</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-2 px-2 py-0.5 extra-small font-mono fw-bold btn-custom-price-add" data-add="50">+₱50</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-2 px-2 py-0.5 extra-small font-mono fw-bold btn-custom-price-add" data-add="100">+₱100</button>
                            <button type="button" class="btn btn-sm btn-light border rounded-2 px-2 py-0.5 extra-small font-mono fw-bold btn-custom-price-clear text-danger">Clear</button>
                        </div>
                    </div>

                    <!-- Total Line Preview -->
                    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between mb-3">
                        <span class="small text-muted fw-bold">Estimated Charge Total:</span>
                        <strong class="font-mono fs-5 fw-black text-purple" id="customItemTotalPreview" style="color: #7c3aed;">₱0.00</strong>
                    </div>

                    <button type="button" id="btnConfirmCustomItem" class="btn w-100 py-2.5 fw-bold text-white rounded-3 shadow-xs d-flex align-items-center justify-content-center gap-1.5" style="background: linear-gradient(135deg, #7c3aed, #6d28d9); border: none;">
                        <i class="bi bi-cart-plus-fill fs-5"></i>
                        <span>Add Charge to Receipt</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quantity / Weighed Item Modal -->
    <div class="modal fade" id="quantityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom py-3 px-4 bg-light">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-3 p-2 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            <i class="bi bi-calculator fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold mb-0 text-dark" id="qtyModalProductName">Set Quantity</h6>
                            <small class="text-muted" id="qtyModalProductMeta">₱0.00 per unit</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="qtyModalCartKey" value="">

                    <!-- Quantity Input Field -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase mb-1" style="letter-spacing: 0.5px;">Quantity / Weight</label>
                        <div class="input-group input-group-lg shadow-xs">
                            <input
                                type="text"
                                id="qtyModalInput"
                                class="form-control font-mono fw-extrabold text-center text-dark fs-3"
                                placeholder="1"
                                autocomplete="off"
                            >
                            <span class="input-group-text font-mono fw-bold text-muted" id="qtyModalUnitLabel">pcs</span>
                        </div>
                        <small class="text-muted extra-small mt-1 d-block" id="qtyModalHelpText">
                            Type fractions like <code>1/4</code>, <code>1/2</code>, <code>3/4</code> or decimals like <code>0.25</code>, <code>1.5</code>.
                        </small>
                    </div>

                    <!-- Fractional Quick Buttons (Visible only if product allows decimal/fractional) -->
                    <div id="qtyModalFractionSection" class="mb-3">
                        <div class="extra-small text-uppercase fw-bold text-muted mb-1.5" style="letter-spacing: 0.5px;">Quick Fraction Presets</div>
                        <div class="gap-2" style="display:grid;grid-template-columns:repeat(4,1fr);">
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="0.25">¼</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="0.50">½</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="0.75">¾</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="1.00">1</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="1.50">1 ½</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="2.00">2</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="2.50">2 ½</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="5.00">5</button>
                        </div>
                    </div>

                    <!-- Whole Number Presets (For standard unit products) -->
                    <div id="qtyModalWholeSection" class="mb-3 d-none">
                        <div class="extra-small text-uppercase fw-bold text-muted mb-1.5" style="letter-spacing: 0.5px;">Quick Presets</div>
                        <div class="gap-2" style="display:grid;grid-template-columns:repeat(4,1fr);">
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="1">1</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="2">2</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="3">3</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="5">5</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="6">6</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="10">10</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="12">12</button>
                            <button type="button" class="btn btn-light border py-2 fw-bold text-dark qty-preset-btn" data-val="24">24</button>
                        </div>
                    </div>

                    <!-- Subtotal Preview Box -->
                    <div class="p-3 rounded-3 bg-light border d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold text-muted small">Computed Total:</span>
                        <strong class="font-mono fs-4 text-success" id="qtyModalSubtotal">₱0.00</strong>
                    </div>

                    <button type="button" id="btnConfirmQtyModal" class="btn btn-success w-100 py-2.5 fw-bold rounded-3 shadow-xs" style="background:#059669;border:none;">
                        <i class="bi bi-check2-circle me-1"></i> Apply Quantity
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Promotion Selector Modal (Single Promo Enforcement) --}}
    <div class="modal fade" id="modalSelectPromo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom py-3 px-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="bi bi-tags-fill fs-6"></i></span>
                        <div>
                            <h6 class="modal-title fw-black text-dark font-mono mb-0" id="txtPromoModalTitle">Select Promotion</h6>
                            <small class="text-muted extra-small" id="txtPromoModalSubtitle">Choose 1 promotion to apply for this item</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3.5">
                    <div class="p-2.5 bg-warning-subtle border border-warning-subtle rounded-3 text-warning-emphasis extra-small mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-6 text-warning"></i>
                        <span><strong>Policy:</strong> Only 1 promotion can be applied per item (no double-dipping / promo stacking).</span>
                    </div>

                    <input type="hidden" id="inpPromoModalCartKey" value="">

                    <div id="promoOptionsList" class="d-flex flex-column gap-2 mb-3">
                        <!-- Populated dynamically by JS -->
                    </div>

                    <button type="button" id="btnConfirmPromoModal" class="btn btn-primary w-100 py-2.5 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Apply Selected Promotion
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Refund & Return Modal --}}
    @include('pages.tenants.terminal.quick_refund_modal')

    {{-- Cashier Orders & Quick Switcher Modal --}}
    <div class="modal fade" id="cashierOrdersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
                <!-- Header -->
                <div class="modal-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center text-white shadow-xs" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); width: 44px; height: 44px;">
                            <i class="bi bi-receipt-cutoff fs-5"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-black text-dark font-mono mb-0" style="letter-spacing:-0.3px;">Cashier Order Switcher</h5>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-0.5 extra-small fw-bold">Live Register</span>
                            </div>
                            <small class="text-muted extra-small">Browse transactions and quickly switch or jump to any order.</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('sales.create', [encryptId(session('sale_id')), 'q=new']) }}" class="btn btn-sm btn-success fw-bold rounded-pill px-3 py-1.5 shadow-xs d-flex align-items-center gap-1.5" style="background:#059669;border:none;">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>Start New Sale</span>
                        </a>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>

                <!-- KPI Summary Pills & Search Controls -->
                <div class="bg-slate-50 border-bottom p-3.5">
                    <div class="row g-2.5 align-items-center justify-content-between">
                        <div class="col-lg-5 col-12">
                            <div class="position-relative">
                                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                <input type="text" id="inpSearchCashierOrders" class="form-control rounded-pill ps-5 pe-3 py-2 bg-white border" placeholder="Search order #, customer, item, or amount..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-7 col-12 d-flex align-items-center justify-content-lg-end gap-2 flex-wrap">
                            <div class="btn-group btn-group-sm rounded-pill p-0.5 bg-white border shadow-2xs" id="orderFilterTabs">
                                <button type="button" class="btn btn-sm btn-filter-order active rounded-pill px-3 py-1 fw-bold" data-status="all" style="background:#059669;color:#fff;border:none;">All</button>
                                <button type="button" class="btn btn-sm btn-filter-order rounded-pill px-3 py-1 fw-bold btn-light border-0 text-muted" data-status="completed">Paid / Completed</button>
                                <button type="button" class="btn btn-sm btn-filter-order rounded-pill px-3 py-1 fw-bold btn-light border-0 text-muted" data-status="pending">Open / In-Progress</button>
                            </div>
                            <span class="text-muted extra-small ms-1 font-mono d-none d-md-inline" id="txtOrderCountLabel">Loading orders...</span>
                        </div>
                    </div>
                </div>

                <!-- Modal Body: Table of Orders -->
                <div class="modal-body p-0" style="min-height: 380px;">
                    <div id="cashierOrdersLoader" class="text-center py-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted small mt-2 mb-0">Fetching cashier transactions...</p>
                    </div>

                    <div id="cashierOrdersContainer" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                                <thead class="bg-slate-100 text-muted extra-small text-uppercase font-mono border-bottom">
                                    <tr>
                                        <th class="ps-4 py-2.5">Order # / Time</th>
                                        <th class="py-2.5">Customer</th>
                                        <th class="py-2.5">Items Summary</th>
                                        <th class="py-2.5 text-end">Total Amount</th>
                                        <th class="py-2.5 text-center">Status</th>
                                        <th class="py-2.5 text-center">Payment</th>
                                        <th class="pe-4 py-2.5 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="cashierOrdersTableBody">
                                    <!-- Populated dynamically by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="cashierOrdersEmptyState" class="text-center py-5 d-none">
                        <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center p-3 mb-2 text-muted">
                            <i class="bi bi-inbox fs-3"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Transactions Found</h6>
                        <p class="text-muted small mb-0">No matching sales records for this cashier / filter.</p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light border-top py-2.5 px-4 d-flex align-items-center justify-content-between">
                    <small class="text-muted extra-small">
                        <i class="bi bi-info-circle me-1 text-primary"></i> Tip: Click on any order row or the <strong>Switch</strong> button to jump to that transaction immediately.
                    </small>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3 py-1.5 fw-bold" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Theme toggle
            const toggle = document.getElementById('themeToggle');
            if (toggle) {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
                toggle.checked = savedTheme === 'dark';
                toggle.addEventListener('change', function () {
                    const theme = this.checked ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', theme);
                    localStorage.setItem('theme', theme);
                });
            }

            // Cashier Orders Switcher Modal Handler
            const ordersModalEl = document.getElementById('cashierOrdersModal');
            if (!ordersModalEl) return;

            let cachedTransactions = [];
            let activeStatusFilter = 'all';

            const loader = document.getElementById('cashierOrdersLoader');
            const container = document.getElementById('cashierOrdersContainer');
            const emptyState = document.getElementById('cashierOrdersEmptyState');
            const tbody = document.getElementById('cashierOrdersTableBody');
            const searchInput = document.getElementById('inpSearchCashierOrders');
            const countLabel = document.getElementById('txtOrderCountLabel');
            const currentSaleId = document.getElementById('saleId')?.value || '';

            async function loadCashierTransactions() {
                loader.classList.remove('d-none');
                container.classList.add('d-none');
                emptyState.classList.add('d-none');

                try {
                    const res = await fetch(`/sales/cashier-transactions?current_sale_id=${encodeURIComponent(currentSaleId)}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!res.ok) throw new Error('Failed to load transactions');
                    const data = await res.json();
                    cachedTransactions = data.transactions || [];
                    renderTransactionList();
                } catch (e) {
                    console.error('Failed to load cashier orders:', e);
                    loader.classList.add('d-none');
                    emptyState.classList.remove('d-none');
                }
            }

            function renderTransactionList() {
                loader.classList.add('d-none');

                const keyword = (searchInput?.value || '').trim().toLowerCase();

                let filtered = cachedTransactions.filter(tx => {
                    if (activeStatusFilter !== 'all' && tx.status !== activeStatusFilter) {
                        return false;
                    }
                    if (keyword) {
                        const code = (tx.sale_code || '').toLowerCase();
                        const inv = (tx.invoice_no || '').toLowerCase();
                        const cust = (tx.customer_name || '').toLowerCase();
                        const items = (tx.items_preview || '').toLowerCase();
                        const amt = String(tx.total_amount || '');
                        const amtFmt = (tx.total_formatted || '').toLowerCase();

                        return code.includes(keyword) || inv.includes(keyword) || cust.includes(keyword) || items.includes(keyword) || amt.includes(keyword) || amtFmt.includes(keyword);
                    }
                    return true;
                });

                if (countLabel) {
                    countLabel.textContent = `Showing ${filtered.length} of ${cachedTransactions.length} orders`;
                }

                if (filtered.length === 0) {
                    container.classList.add('d-none');
                    emptyState.classList.remove('d-none');
                    return;
                }

                emptyState.classList.add('d-none');
                container.classList.remove('d-none');

                tbody.innerHTML = filtered.map(tx => {
                    const isCurrent = tx.is_current;
                    const isCompleted = tx.status === 'completed';
                    const rowClass = isCurrent ? 'bg-success bg-opacity-10 border-start border-3 border-success' : 'cursor-pointer';

                    return `
                        <tr class="${rowClass}" onclick="window.location.href='${tx.url}'" style="cursor: pointer; transition: background 0.15s ease;">
                            <td class="ps-4 py-2.5">
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="font-mono text-dark fs-6">${tx.sale_code}</strong>
                                    ${isCurrent ? `<span class="badge bg-success text-white rounded-pill px-2 py-0.5 extra-small fw-bold"><i class="bi bi-check-circle-fill me-0.5"></i> Active Now</span>` : ''}
                                </div>
                                <div class="extra-small text-muted font-mono mt-0.5">${tx.date_formatted} &bull; ${tx.time_formatted}</div>
                            </td>
                            <td class="py-2.5">
                                <div class="fw-bold text-dark small text-truncate" style="max-width:180px;">${tx.customer_name}</div>
                            </td>
                            <td class="py-2.5">
                                <div class="text-truncate text-muted small" style="max-width:220px;" title="${tx.items_preview}">
                                    ${tx.items_preview}
                                </div>
                                <div class="extra-small text-secondary font-mono">${tx.items_count} item${tx.items_count > 1 ? 's' : ''} (${tx.total_qty} pcs)</div>
                            </td>
                            <td class="py-2.5 text-end">
                                <span class="fw-black font-mono fs-6 text-dark">${tx.total_formatted}</span>
                            </td>
                            <td class="py-2.5 text-center">
                                ${tx.raw_status === 'refunded'
                                    ? `<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 extra-small fw-bold"><i class="bi bi-arrow-counterclockwise me-0.5"></i> Refunded</span>`
                                    : (tx.raw_status === 'partial_refund'
                                        ? `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold"><i class="bi bi-percent me-0.5"></i> Partial Return</span>`
                                        : (isCompleted
                                            ? `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 extra-small fw-bold"><i class="bi bi-check2 me-0.5"></i> Paid</span>`
                                            : `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold"><i class="bi bi-hourglass-split me-0.5"></i> Open</span>`
                                        )
                                    )
                                }
                            </td>
                            <td class="py-2.5 text-center">
                                <span class="badge bg-light border text-muted extra-small font-mono px-2 py-0.5">${tx.payment_method}</span>
                            </td>
                            <td class="pe-4 py-2.5 text-end" onclick="event.stopPropagation();">
                                ${isCurrent
                                    ? `<button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 extra-small fw-bold" disabled>Current</button>`
                                    : `<a href="${tx.url}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 extra-small fw-bold shadow-2xs hover-lift"><i class="bi bi-box-arrow-in-right me-1"></i> Switch</a>`
                                }
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            ordersModalEl.addEventListener('show.bs.modal', function () {
                loadCashierTransactions();
            });

            ordersModalEl.addEventListener('shown.bs.modal', function () {
                if (searchInput) {
                    searchInput.value = '';
                    searchInput.focus();
                }
            });

            if (searchInput) {
                searchInput.addEventListener('input', renderTransactionList);
            }

            document.querySelectorAll('.btn-filter-order').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.btn-filter-order').forEach(b => {
                        b.classList.remove('active');
                        b.classList.add('btn-light', 'text-muted');
                        b.style.background = '';
                        b.style.color = '';
                    });

                    this.classList.add('active');
                    this.classList.remove('btn-light', 'text-muted');
                    this.style.background = '#059669';
                    this.style.color = '#fff';

                    activeStatusFilter = this.dataset.status;
                    renderTransactionList();
                });
            });
        });
    </script>

@endsection
