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
            <input type="hidden" id="saleId" value="{{ encryptId($sale->id) }}">

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

    </div>

    <div
        class="modal fade"
        id="paymentModal"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                {{-- Modal Header --}}
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4 p-md-4.5 bg-light bg-opacity-75">
                    <div class="saleStatusContainer"></div>

                    <div class="row g-4">

                        {{-- LEFT COLUMN: Payment Inputs & CRM Controls --}}
                        <div class="col-lg-7 d-flex flex-column gap-4">

                            {{-- Top KPI Header: Total Due & Balance Due --}}
                            <div class="row g-3 g-md-4">
                                <div class="col-md-6">
                                    <div class="p-4 rounded-4 border-0 shadow-sm h-100 d-flex flex-column justify-content-between" style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 100%);color:#fff;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="text-secondary extra-small fw-bold text-uppercase" style="letter-spacing:0.8px;">Total Amount Due</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small font-mono fw-bold px-2 py-1">Payable</span>
                                        </div>
                                        <div class="mt-1">
                                            <div id="paymentTotal" class="font-mono fw-black text-success lh-1" style="font-size:2.5rem;letter-spacing:-0.5px;">
                                                ₱0.00
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-4 rounded-4 border bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="text-muted extra-small fw-bold text-uppercase" style="letter-spacing:0.8px;">Remaining Balance</span>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle extra-small font-mono fw-bold px-2 py-1">Unpaid</span>
                                        </div>
                                        <div class="mt-1">
                                            <div id="paymentBalance" class="font-mono fw-black text-danger lh-1" style="font-size:2.5rem;letter-spacing:-0.5px;">
                                                ₱0.00
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quick Cash Tender Presets --}}
                            <div class="bg-white p-4 rounded-4 border shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="extra-small fw-extrabold text-uppercase text-muted" style="letter-spacing:0.6px;">
                                        <i class="bi bi-lightning-charge-fill text-warning me-1"></i>Quick Cash Tender (Fast Pay)
                                    </span>
                                    <span class="badge bg-light text-muted border extra-small">Auto-fills amount</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2.5" id="quickTenderContainer">
                                    <button type="button" class="btn btn-outline-success fw-extrabold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-exact shadow-xs">
                                        <i class="bi bi-check2-all me-1"></i>Exact Amount
                                    </button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-btn shadow-xs" data-val="20">₱20</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-btn shadow-xs" data-val="50">₱50</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-btn shadow-xs" data-val="100">₱100</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-btn shadow-xs" data-val="200">₱200</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-btn shadow-xs" data-val="500">₱500</button>
                                    <button type="button" class="btn btn-light border fw-bold font-mono rounded-3 px-3 py-1.5 extra-small quick-tender-btn shadow-xs" data-val="1000">₱1,000</button>
                                </div>
                            </div>

                            {{-- Complete CRM Customer Profile Card --}}
                            <div class="bg-white p-4 rounded-4 border shadow-sm" id="modalCustomerCard">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                                        <div class="rounded-4 bg-success bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0 text-success shadow-xs" style="width:44px;height:44px;">
                                            <i class="bi bi-person-badge-fill fs-4"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="fw-black text-dark fs-6 lh-sm mb-0" id="modalCustomerName">Walk-in Customer</div>
                                                <div id="modalCustomerBadge" class="d-none">
                                                    <span class="badge bg-success-subtle text-success extra-small fw-bold border border-success-subtle">CRM Linked</span>
                                                </div>
                                            </div>
                                            <div id="modalNoCustomer" class="text-muted extra-small mt-0.5">
                                                <i class="bi bi-info-circle me-1"></i>No customer profile assigned to this sale
                                            </div>
                                        </div>
                                    </div>

                                    <button
                                        type="button"
                                        id="btnModalSelectCustomer"
                                        class="btn btn-light border rounded-pill px-3.5 py-1.5 text-dark fw-bold extra-small shadow-xs d-flex align-items-center gap-1.5"
                                        title="Select, Search, or Add Customer"
                                    >
                                        <i class="bi bi-people-fill text-primary"></i>
                                        <span>Change / Select Customer</span>
                                    </button>
                                </div>

                                {{-- Utang warning banner --}}
                                <div id="utangNoCustomerWarning" class="d-none mt-3 p-2.5 rounded-3 bg-warning bg-opacity-10 border border-warning-subtle d-flex align-items-center gap-2" style="font-size:0.78rem;">
                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                    <span class="text-warning-emphasis fw-semibold">
                                        <strong>Utang (Credit) Notice:</strong> Utang transaction requires an assigned customer to track debtor balance. Please select or register a customer.
                                    </span>
                                </div>
                            </div>

                            {{-- Payment Lines Breakdown Container --}}
                            <div class="bg-white p-4 rounded-4 border shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-3.5 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="extra-small fw-extrabold text-uppercase text-muted" style="letter-spacing:0.6px;">
                                            <i class="bi bi-credit-card-2-front-fill text-primary me-1"></i>Tender & Payment Breakdown
                                        </span>
                                    </div>
                                    <small class="text-muted extra-small font-mono">Supports Split / Multi-Payments</small>
                                </div>

                                <div id="paymentLines" class="d-flex flex-column gap-3 mb-3.5">
                                    {{-- Rendered dynamically with comfortable padding by terminal.js --}}
                                </div>

                                <button
                                    type="button"
                                    class="btn btn-light border border-dashed w-100 py-3 text-muted fw-bold rounded-3 shadow-xs hover-lift"
                                    id="btnAddPayment"
                                    style="border-style:dashed !important;"
                                >
                                    <i class="bi bi-plus-circle-fill text-success me-1.5"></i> Add Another Payment Method (Split Tender)
                                </button>
                            </div>

                            {{-- Discounts & Notes Accordion --}}
                            <div class="bg-white rounded-4 border shadow-sm overflow-hidden">
                                <button
                                    class="btn btn-white w-100 py-3.5 px-4 text-start d-flex align-items-center justify-content-between text-dark fw-bold small border-0"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#discountPanel"
                                    aria-expanded="false"
                                >
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="bi bi-percent text-success fs-5"></i>
                                        <span>Customer Discounts & Special Program (Senior / PWD / Student)</span>
                                    </span>
                                    <i class="bi bi-chevron-down extra-small text-muted"></i>
                                </button>

                                <div id="discountPanel" class="collapse border-top p-4 bg-light bg-opacity-50">
                                    <div class="row g-3.5">
                                        <div class="col-md-6">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Discount Program</label>
                                            <select id="discountType" class="form-select">
                                                <option value="">No Discount (Standard Rate)</option>
                                                <option value="senior">Senior Citizen (20%)</option>
                                                <option value="pwd">PWD (20%)</option>
                                                <option value="student">Student (5%)</option>
                                                <option value="employee">Employee Discount</option>
                                                <option value="manual">Manual Custom Discount</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 d-none" id="manualDiscountSection">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Custom Discount Value</label>
                                            <div class="input-group">
                                                <select id="discountMode" class="form-select" style="max-width:110px;">
                                                    <option value="percentage">% Percent</option>
                                                    <option value="fixed">₱ Fixed</option>
                                                </select>
                                                <input type="number" id="discountValue" class="form-control font-mono fw-bold" placeholder="0">
                                            </div>
                                        </div>

                                        <div class="col-md-6 d-none" id="discountInfoSection">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">ID Cardholder Name</label>
                                            <input type="text" id="discountHolder" class="form-control" placeholder="Full Name as shown on ID">
                                        </div>

                                        <div class="col-md-6 d-none" id="discountIdNoSection">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Senior / PWD / Student ID No.</label>
                                            <input type="text" id="discountIdNo" class="form-control font-mono" placeholder="ID Number">
                                        </div>

                                        <div class="col-12 mt-2">
                                            <label class="form-label extra-small fw-bold text-muted text-uppercase mb-1">Sale Notes / Customer Remarks</label>
                                            <textarea id="paymentNotes" class="form-control" rows="2" placeholder="Add optional transaction notes or delivery instructions..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- RIGHT COLUMN: Receipt Summary & Change Banner --}}
                        <div class="col-lg-5">
                            <div class="bg-white rounded-4 border shadow-sm p-4 p-md-4.5 h-100 d-flex flex-column justify-content-between gap-4">

                                <div>
                                    <div class="d-flex align-items-center justify-content-between pb-3 mb-3.5 border-bottom">
                                        <div>
                                            <span class="fw-black text-dark font-mono text-uppercase fs-6">Order Summary</span>
                                            <div class="text-muted extra-small">Live calculated receipt totals</div>
                                        </div>
                                        <span class="badge bg-light text-dark border extra-small font-mono fw-bold">Live Breakdown</span>
                                    </div>

                                    {{-- Line items breakdown with BOLD values --}}
                                    <div class="d-flex flex-column gap-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-center py-0.5">
                                            <span class="text-muted small">Cart Subtotal:</span>
                                            <strong id="summarySubtotalModal" class="font-mono text-dark fw-black fs-5">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center py-0.5">
                                            <span class="text-muted small">Discount Applied:</span>
                                            <strong id="summaryDiscountModal" class="font-mono text-warning fw-black fs-5">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <span class="text-dark fw-extrabold small">Total Payable:</span>
                                            <strong class="font-mono text-success fw-black fs-4" id="summaryTotalModal">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <span class="text-muted small fw-bold">Total Tendered / Paid:</span>
                                            <strong id="paymentPaid" class="font-mono text-primary fw-black fs-4">₱0.00</strong>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center py-0.5">
                                            <span class="text-muted small fw-bold">Remaining Balance:</span>
                                            <strong id="paymentBalanceSummary" class="font-mono text-danger fw-black fs-4">₱0.00</strong>
                                        </div>
                                    </div>
                                </div>

                                {{-- Hero Change Card with Generous Padding & Ultra-Bold Change --}}
                                <div class="my-auto py-3">
                                    <div class="p-4 rounded-4 text-center border shadow-xs" style="background:linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);border-color:#a7f3d0 !important;">
                                        <div class="extra-small text-uppercase fw-extrabold mb-1" style="letter-spacing:1px;color:#047857;">
                                            <i class="bi bi-cash-coin me-1 fs-6"></i>Sukli / Change Due
                                        </div>
                                        <div id="paymentChange" class="font-mono fw-black lh-1 my-2" style="font-size:3.6rem;color:#059669;letter-spacing:-1px;">
                                            ₱0.00
                                        </div>
                                        <small class="text-muted extra-small d-block" style="color:#065f46 !important;">
                                            Exact or excess cash returned to buyer
                                        </small>
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <div class="pt-3">
                                    <button
                                        type="button"
                                        class="btn btn-success w-100 py-3.5 rounded-3 fw-black font-mono shadow-sm d-flex align-items-center justify-content-center gap-2"
                                        id="btnConfirmPayment"
                                        style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:1.15rem;letter-spacing:0.5px;"
                                    >
                                        <i class="bi bi-check-circle-fill fs-4"></i>
                                        <span>COMPLETE SALE</span>
                                        <span class="badge bg-white bg-opacity-25 text-white extra-small ms-1.5 d-none d-sm-inline font-sans fw-bold">F4 / Enter</span>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('themeToggle');

            if (!toggle) return;

            // Restore saved theme
            const savedTheme = localStorage.getItem('theme') || 'light';

            document.documentElement.setAttribute('data-theme', savedTheme);

            toggle.checked = savedTheme === 'dark';

            // Switch theme
            toggle.addEventListener('change', function () {
                const theme = this.checked ? 'dark' : 'light';

                document.documentElement.setAttribute('data-theme', theme);

                localStorage.setItem('theme', theme);
            });
        });
    </script>

@endsection
