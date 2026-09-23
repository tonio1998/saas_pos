<div class="checkout-section mt-2">
    @if($isSalePaid ?? false)
        <div class="d-flex flex-column gap-2">
            @if(!($isRefunded ?? false))
                <button
                    type="button"
                    class="btn btn-danger w-100 py-2.5 rounded-3 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2"
                    id="btnQuickRefundCurrentSale"
                    data-sale-code="{{ $sale->sale_code }}"
                    data-invoice-no="{{ $sale->invoice_no ?: $sale->sale_code }}"
                    style="background: linear-gradient(135deg, #dc2626, #b91c1c); border: none; font-size: 0.95rem;"
                >
                    <i class="bi bi-arrow-return-left fs-5 text-white"></i>
                    <span>QUICK REFUND / RETURN</span>
                </button>
            @endif

            <button
                type="button"
                class="btn btn-primary w-100 py-2.5 rounded-3 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2"
                id="btnPrintPastSaleReceipt"
                style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none; font-size: 0.95rem;"
            >
                <i class="bi bi-printer-fill fs-5 text-white"></i>
                <span>PRINT RECEIPT (#{{ $sale->sale_code }})</span>
            </button>

            <a
                href="{{ route('sales.create', [encryptId(session('sale_id')), 'q=new']) }}"
                class="btn btn-success w-100 py-2.5 rounded-3 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2"
                style="background: #059669; border: none; font-size: 0.95rem;"
            >
                <i class="bi bi-plus-circle-fill fs-5"></i>
                <span>START NEW SALE</span>
            </a>
        </div>
    @else
        <button
            type="button"
            class="btn btn-success w-100 py-3 rounded-3 fw-extrabold shadow d-flex align-items-center justify-content-between px-4 btn-checkout"
            id="btnCheckout"
            style="background: linear-gradient(135deg, #059669, #047857); border: none; font-size: 1.1rem; color: #ffffff;"
        >
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-lightning-charge-fill text-warning fs-5"></i>
                <span>PROCEED TO PAY</span>
            </div>
            <div class="d-flex align-items-center gap-2 font-mono">
                <span class="fs-5">CHECKOUT</span>
                <i class="bi bi-arrow-right-circle-fill fs-5"></i>
            </div>
        </button>
    @endif
</div>
