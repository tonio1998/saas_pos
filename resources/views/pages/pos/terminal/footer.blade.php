<footer class="footers border-top bg-white shadow-sm sticky-bottom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center flex-wrap gap-4">
                <div class="status-item">
                    <div class="status-icon bg-primary-subtle text-primary">
                        <i class="bi bi-pc-display-horizontal"></i>
                    </div>

                    <div>
                        <div class="status-label">Terminal</div>
                        <div class="status-value">{{ $sale->terminal?->terminal_name ?? '-' }}</div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-icon bg-warning-subtle text-warning"><i class="bi bi-safe2"></i></div>
                    <div>
                        <div class="status-label">Drawer</div>
                        <div class="status-value">
                            {{ $sale->drawer?->drawer_name ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="status-item">
                    <div class="status-icon bg-success-subtle text-success"><i class="bi bi-person-badge"></i></div>
                    <div>
                        <div class="status-label">Cashier</div>
                        <div class="status-value">
                            {{ $sale->cashShift?->cashier?->name ?? auth()->user()->name }}
                        </div>
                    </div>
                </div>

                <div class="status-item">

                    <div class="status-icon bg-info-subtle text-info">
                        <i class="bi bi-clock-history"></i>
                    </div>

                    <div>

                        <div class="status-label">
                            Shift
                        </div>

                        <div class="status-value">
                            {{ $sale->cashShift?->shift_code ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

            <div class="d-flex align-items-center flex-wrap gap-4">
                <div class="metric">
                    <small class="text-muted d-block">Sales</small>
                    <div class="fw-bold text-primary fs-8">
                        <h2>₱{{ number_format($sale->cashShift->sales_total ?? 0,2) }}</h2>
                    </div>
                </div>
                <div class="metric">
                    <small class="text-muted d-block">Started</small>
                    <div class="fw-semibold">
                        {{ optional($sale->cashShift?->opened_at)->format('h:i A') ?? '-' }}
                    </div>
                </div>

                @if($sale->cashShift)
                    <a href="{{ route('cashiering.cash-shifts.show', encryptId($sale->cashShift->id)) }}" class="btn btn-light border">
                        <i class="bi bi-eye me-1"></i>
                        Details
                    </a>

                    @if($sale->cashShift->status == 'open')
                        <a href="{{ route('cashiering.cash-shifts.close', encryptId($sale->cashShift->id)) }}" class="btn btn-danger">
                            <i class="bi bi-door-closed me-1"></i>
                            Close Shift
                        </a>
                    @else

                        <button
                            class="btn btn-success"
                            disabled
                        >

                            <i class="bi bi-check-circle me-1"></i>

                            Shift Closed

                        </button>

                    @endif

                @endif

            </div>

        </div>

    </div>

</footer>

<style>

    .footer{
        z-index:1025;
    }

    .status-item{
        display:flex;
        align-items:center;
        gap:.75rem;
    }

    .status-icon{
        width:42px;
        height:42px;
        border-radius:12px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1.1rem;
        flex-shrink:0;
    }

    .status-label{
        font-size:.72rem;
        color:#6c757d;
        line-height:1;
        margin-bottom:2px;
    }

    .status-value{
        font-size:.92rem;
        font-weight:600;
        color:#212529;
        white-space:nowrap;
    }

    .metric{
        text-align:right;
        min-width:95px;
    }

    .metric small{
        font-size:.72rem;
    }

    .metric .fw-bold,
    .metric .fw-semibold{
        font-size:.95rem;
    }

    .btn{
        white-space:nowrap;
    }

</style>
