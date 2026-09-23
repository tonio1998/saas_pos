<div class="cart-header border-bottom px-3 py-2 bg-light" style="flex-shrink: 0;">
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
        <div>
            <div class="d-flex align-items-center gap-1.5">
                <i class="bi bi-receipt text-success"></i>
                <span class="fw-extrabold text-dark text-uppercase small font-mono" style="letter-spacing: 0.5px;">Current Receipt</span>
            </div>
            <div class="text-muted extra-small" id="cartCustomerName">
                <i class="bi bi-person me-1"></i>{{ $sale->customer?->CustomerName ?? 'Walk-in Customer' }}
            </div>
        </div>

        @if(!($isSalePaid ?? false))
            <div class="d-flex align-items-center gap-1.5">
                <!-- Retail / Wholesale Pricing Switcher -->
                <div class="btn-group btn-group-sm bg-white border rounded-pill p-0.5 shadow-xs" role="group" id="pricingModeGroup">
                    <input type="radio" class="btn-check" name="priceMode" id="priceModeRetail" value="retail" checked autocomplete="off">
                    <label class="btn btn-sm btn-outline-success border-0 rounded-pill px-2.5 py-0.5 extra-small fw-bold" for="priceModeRetail" title="Standard retail selling price">
                        <i class="bi bi-tag-fill me-1"></i>Retail
                    </label>

                    <input type="radio" class="btn-check" name="priceMode" id="priceModeWholesale" value="wholesale" autocomplete="off">
                    <label class="btn btn-sm btn-outline-primary border-0 rounded-pill px-2.5 py-0.5 extra-small fw-bold" for="priceModeWholesale" title="Bulk wholesale price">
                        <i class="bi bi-box-seam-fill me-1"></i>Wholesale
                    </label>
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-white border rounded-pill px-2.5 py-1 text-purple fw-bold shadow-xs extra-small d-flex align-items-center gap-1 hover-lift"
                    style="color: #7c3aed; border-color: #ddd6fe !important; background: #faf5ff;"
                    data-bs-toggle="modal"
                    data-bs-target="#customItemModal"
                    id="btnOpenCustomItemModal"
                    title="Quick insert non-inventory fee, Tote Bag charge, broken item charge, delivery fee, or custom service"
                >
                    <i class="bi bi-plus-circle-fill text-purple" style="color: #7c3aed;"></i>
                    <span>+ Fee / Service</span>
                </button>

                <button
                    type="button"
                    class="btn btn-sm btn-white border rounded-pill px-2.5 py-1 text-dark fw-semibold shadow-xs extra-small"
                    data-bs-toggle="modal"
                    data-bs-target="#customerModal"
                >
                    <i class="bi bi-person-plus me-1 text-success"></i>
                    Customer
                </button>
            </div>
        @else
            <div class="d-flex align-items-center gap-1.5">
                <span class="badge bg-white border text-muted extra-small font-mono fw-bold px-2.5 py-1 rounded-pill shadow-xs">
                    <i class="bi bi-lock-fill me-1 text-secondary"></i>Finalized
                </span>
            </div>
        @endif
    </div>
</div>

@if($isSalePaid ?? false)
    @if($isRefunded ?? false)
        <div class="px-3 py-1.5 border-bottom d-flex align-items-center justify-content-between gap-1.5" style="background: #fef2f2; border-color: #fecaca; flex-shrink: 0;">
            <span class="badge bg-danger text-white font-mono fw-bold px-2.5 py-1 extra-small">
                <i class="bi bi-arrow-counterclockwise me-1"></i>REFUNDED TRANSACTION (100%)
            </span>
            <small class="text-danger fw-bold extra-small font-mono">Restocked & Payout Returned</small>
        </div>
    @elseif($isPartial ?? false)
        <div class="px-3 py-1.5 border-bottom d-flex align-items-center justify-content-between gap-1.5" style="background: #fffbeb; border-color: #fde68a; flex-shrink: 0;">
            <span class="badge bg-warning text-dark font-mono fw-bold px-2.5 py-1 extra-small">
                <i class="bi bi-arrow-return-left me-1"></i>PARTIAL RETURN TRANSACTION
            </span>
            <small class="text-warning-emphasis fw-bold extra-small font-mono">Refund Payout: -₱{{ number_format($refundedAmount ?? 0, 2) }}</small>
        </div>
    @else
        <div class="px-3 py-1.5 border-bottom d-flex align-items-center justify-content-between gap-1.5" style="background: #ecfdf5; border-color: #a7f3d0; flex-shrink: 0;">
            <span class="badge bg-success text-white font-mono fw-bold px-2.5 py-1 extra-small">
                <i class="bi bi-check-circle-fill me-1"></i>PAID TRANSACTION
            </span>
            <small class="text-success fw-bold extra-small font-mono">Archived Receipt</small>
        </div>
    @endif
@else
    <!-- Multi-Sale / Multi-Transaction Tabs Bar -->
    <div class="pos-order-tabs-bar px-3 py-1.5 bg-white border-bottom d-flex align-items-center justify-content-between gap-1.5" id="posOrderTabsBar" style="flex-shrink: 0; background: #f8fafc;">
        <div id="orderTabsContainer" class="d-flex align-items-center gap-1.5 flex-grow-1 overflow-x-auto py-0.5" style="scrollbar-width: thin;">
            <!-- Populated dynamically by JS -->
        </div>
        <button type="button" class="btn btn-sm btn-success rounded-pill px-2.5 py-1 extra-small fw-bold shadow-xs d-flex align-items-center gap-1 flex-shrink-0" id="btnNewOrderTab" style="background:#059669;border:none;" title="Start a new transaction / Next Customer">
            <i class="bi bi-plus-lg"></i>
            <span>Next Sale</span>
        </button>
    </div>
@endif

<div
    class="modal fade"
    id="customerModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content border-0 shadow rounded-4">

            <div class="modal-header border-0">

                <div>

                    <h5 class="modal-title fw-bold mb-1">
                        <i class="bi bi-people-fill text-primary me-2"></i>
                        Select Customer
                    </h5>

                    <small class="text-muted">
                        Search an existing customer or create a new one.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>

            <div class="modal-body">
                <div class="mb-4">
                    <x-form.group
                        name="Customer"
                        label="Customer"
                        class="col-12"
                    >

                        <x-form.select
                            name="customer_id"
                            id="customer_id"
                            ajax="{{ route('select2.customers') }}"
                            value="{{ old('customer_id','') }}"
                            placeholder="Search Customer"
                            dropdownParent="#customerModal"
                        />

                    </x-form.group>

                    <small class="text-muted">
                        Search by customer code, name, or mobile number.
                    </small>

                </div>

                <div class="text-center">

                    <span class="text-muted">
                        Can't find the customer?
                    </span>

                    <button
                        type="button"
                        class="btn btn-link text-decoration-none"
                        data-bs-toggle="collapse"
                        data-bs-target="#newCustomerCollapse"
                    >
                        <i class="bi bi-person-plus"></i>
                        Add New Customer
                    </button>

                </div>

                <div class="collapse mt-4" id="newCustomerCollapse">
                    <form id="newCustomerForm">
                        <div class="card border-0 bg-light rounded-4">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <h6 class="fw-bold mb-1">
                                    <i class="bi bi-person-plus me-2 text-primary"></i>
                                    New Customer
                                </h6>
                                <small class="text-muted">
                                    Enter the basic customer information.
                                </small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <x-form.group name="customer_name" label="Customer Name" required>
                                            <x-form.input
                                                name="customer_name"
                                                placeholder="Enter customer name"
                                                autocomplete="off"
                                            />
                                        </x-form.group>
                                    </div>
                                    <div class="col-12">
                                        <x-form.group name="customer_address" label="Customer Address">
                                            <x-form.input
                                                name="customer_address"
                                                rows="2"
                                                placeholder="Enter customer address"
                                            />
                                        </x-form.group>

                                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-transparent border-0 text-end">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#newCustomerCollapse"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-link text-decoration-none"
                                    id="btnSaveCustomer"
                                >
                                    <i class="bi bi-person-plus"></i>
                                    Create & Select Customer
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

            <div class="modal-footer border-0">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="btnSelectCustomer"
                    data-sale="{{ $sale->id }}"
                >
                    Select Customer
                </button>

            </div>

        </div>

    </div>
</div>
