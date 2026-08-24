<div class="pos-top-header px-3 py-2 border-bottom bg-white d-flex align-items-center justify-content-between gap-2 shadow-xs" style="min-height:56px;flex-shrink:0;">
    <!-- Left: Brand / Store Info & Navigation -->
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-light border rounded-3 text-dark fw-bold d-flex align-items-center gap-1.5 px-2.5 py-1.5 shadow-xs" title="Back to Dashboard">
            <i class="bi bi-arrow-left"></i>
            <span class="d-none d-lg-inline">Dashboard</span>
        </a>

        <div class="d-flex align-items-center gap-2 ps-1">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-xs" style="width:32px;height:32px;background:#059669;">
                <i class="bi bi-calculator-fill fs-6"></i>
            </div>
            <div>
                <span class="fw-extrabold text-dark d-block lh-1 font-mono" style="font-size: 0.92rem;">LikhaPOS</span>
                <small class="text-muted extra-small d-none d-xl-inline">{{ session('tenant_name', 'Store') }}</small>
            </div>
        </div>

        @if($isStarter ?? true)
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-bold d-none d-xxl-inline ms-1 extra-small">
                <i class="bi bi-lightning-charge-fill me-1"></i> Starter
            </span>
        @elseif($isGrowth ?? false)
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1 fw-bold d-none d-xxl-inline ms-1 extra-small">
                <i class="bi bi-rocket-takeoff-fill me-1"></i> Growth
            </span>
        @else
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1 fw-bold d-none d-xxl-inline ms-1 extra-small">
                <i class="bi bi-shield-check me-1"></i> Pro
            </span>
        @endif
    </div>

    <!-- Center: Product Search / Barcode Scanner Input -->
    <div class="flex-grow-1 mx-2" style="max-width: 480px;">
        <div class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6"></i>
            <input
                id="barcodeSearch"
                class="form-control rounded-pill ps-5 pe-3 py-1.5 border-slate"
                style="border-color: #cbd5e1; font-size: 0.92rem; background: #f8fafc;"
                placeholder="Scan barcode [F2] or search product..."
                autocomplete="off"
                autofocus
            >
        </div>
    </div>

    <!-- Right: Order Switcher, Shift Info & Actions -->
    <div class="d-flex align-items-center gap-2">
        <!-- Order Switcher -->
        <div class="d-flex align-items-center bg-light border rounded-3 p-1 gap-1 shadow-2xs">
            @if($previousSale)
                <a href="{{ route('sales.create', encryptId($previousSale->id)) }}" class="btn btn-sm btn-white border-0 text-dark px-2 py-0.5 rounded-2 hover-lift" title="Previous Sale">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @else
                <button class="btn btn-sm btn-light border-0 text-muted px-2 py-0.5 rounded-2" disabled>
                    <i class="bi bi-chevron-left"></i>
                </button>
            @endif

            <button type="button" 
                class="btn btn-sm btn-white border border-slate-200 rounded-2 px-2.5 py-0.5 text-center d-flex align-items-center gap-1.5 shadow-2xs hover-lift" 
                id="btnOpenCashierOrdersModal" 
                data-bs-toggle="modal" 
                data-bs-target="#cashierOrdersModal" 
                title="Click to view & switch between cashier transactions">
                <div class="text-start">
                    <small class="text-muted text-uppercase d-block fw-bold" style="font-size:0.58rem;letter-spacing:0.5px;line-height:1;">Order</small>
                    <strong class="text-dark font-mono d-flex align-items-center gap-1" style="font-size:0.84rem;">
                        #{{ $sale->sale_code }}
                        <i class="bi bi-chevron-expand extra-small text-muted ms-0.5" style="font-size:0.65rem;"></i>
                    </strong>
                </div>
            </button>

            <a href="{{ route('sales.create', [encryptId(session('sale_id')), 'q=new']) }}" class="btn btn-sm btn-success text-white px-2 py-0.5 rounded-2 shadow-xs hover-lift" style="background:#059669;border:none;" title="Start New Sale Order">
                <i class="bi bi-plus-lg"></i>
            </a>

            @if($nextSale)
                <a href="{{ route('sales.create', encryptId($nextSale->id)) }}" class="btn btn-sm btn-white border-0 text-dark px-2 py-0.5 rounded-2 hover-lift" title="Next Sale">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @endif
        </div>

        <!-- Shift & Device Info Pill -->
        @if($sale->cashShift)
            <div class="dropdown">
                <button class="btn btn-sm btn-light border rounded-3 d-flex align-items-center gap-2 px-2.5 py-1.5 text-dark fw-semibold shadow-xs" type="button" data-bs-toggle="dropdown">
                    <span class="badge bg-success rounded-circle p-1"></span>
                    <span class="d-none d-md-inline small font-mono">{{ $sale->terminal?->terminal_name ?? 'Device 1' }}</span>
                    <i class="bi bi-chevron-down extra-small text-muted"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" style="min-width: 220px;">
                    <li class="px-3 py-1 text-muted extra-small text-uppercase fw-bold">Active Shift Info</li>
                    <li class="px-3 py-1">
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Drawer:</span>
                            <span class="fw-bold">{{ $sale->drawer?->drawer_name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Cashier:</span>
                            <span class="fw-bold">{{ $sale->cashShift?->cashier?->name ?? auth()->user()->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Shift Code:</span>
                            <span class="fw-bold font-mono">{{ $sale->cashShift?->shift_code ?? '-' }}</span>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item rounded-3 small py-1.5" href="{{ route('cashiering.cash-shifts.show', encryptId($sale->cashShift->id)) }}">
                            <i class="bi bi-eye text-primary me-1.5"></i> Shift Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 small py-1.5 text-danger fw-bold" href="{{ route('cashiering.cash-shifts.close', encryptId($sale->cashShift->id)) }}">
                            <i class="bi bi-door-closed me-1.5"></i> Close Shift
                        </a>
                    </li>
                </ul>
            </div>

            @if($sale->cashShift->status == 'open')
                <a href="{{ route('cashiering.cash-shifts.close', encryptId($sale->cashShift->id)) }}" class="btn btn-sm btn-outline-danger rounded-3 fw-bold d-none d-sm-inline-flex align-items-center gap-1 px-2.5 py-1" title="Close Shift">
                    <i class="bi bi-door-closed"></i>
                    <span>Close Shift</span>
                </a>
            @endif
        @endif
    </div>
</div>

<div class="saleStatusContainer"></div>
