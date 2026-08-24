@php
    $store = $tenant ?? \App\Models\POS\POSTenant::first();
    $cashierName = auth()->user()?->name ?? auth()->user()?->username ?? 'Cashier';
    $invoiceNo = $sale->sale_code ?? $sale->invoice_no ?? 'SI-' . str_pad($sale->id ?? 1, 6, '0', STR_PAD_LEFT);
@endphp

<div class="pos-receipt-container d-flex flex-column h-100 bg-slate-100">
    <!-- Receipt Header Bar -->
    <div class="pos-receipt-header p-2.5 px-3 bg-white border-bottom d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-2 bg-emerald-50 text-emerald-600 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-receipt fs-6"></i>
            </div>
            <div>
                <div class="fw-bold text-dark extra-small lh-1 text-uppercase" style="letter-spacing: 0.5px;">Live Receipt</div>
                <div class="text-muted" style="font-size: 0.68rem;">Thermal 80mm Print Preview</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-1.5">
            <span class="badge bg-success-subtle text-success border border-success-subtle extra-small fw-bold px-2 py-0.5" id="receiptLiveBadge">
                <span class="spinner-grow spinner-grow-sm me-1" style="width: 6px; height: 6px;" role="status"></span>Live
            </span>
            <button type="button" id="btnPrintLiveReceipt" class="btn btn-sm btn-dark px-2.5 py-1 extra-small rounded-2 shadow-xs d-none" title="Print this receipt">
                <i class="bi bi-printer-fill me-1 text-warning"></i>Print Receipt
            </button>
        </div>

    </div>

    <!-- Thermal Paper Container (1:1 Exact Print Styling) -->
    <div class="pos-receipt-paper-wrapper flex-grow-1 overflow-y-auto p-3 d-flex justify-content-center">
        <div class="pos-thermal-paper shadow rounded-1 p-3.5 bg-white text-dark" id="liveReceiptContent" style="font-family: 'Courier New', Courier, monospace; font-size: 11.5px; color: #000000; line-height: 1.35; width: 100%; max-width: 290px;">
            
            <!-- Store Header -->
            <div class="text-center">
                @if(!empty($store->logo))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($store->logo) }}" alt="Logo" class="mb-1" style="max-height: 48px; max-width: 120px; object-fit: contain; display: block; margin: 0 auto;">
                @endif
                <div class="fw-bold text-uppercase" style="font-size: 13.5px; letter-spacing: 0.5px;">
                    {{ $store->business_name ?? 'MINIMART STORE' }}
                </div>
                @if(!empty($store->owner_name))
                    <div style="font-size: 10.5px;">Prop: {{ $store->owner_name }}</div>
                @endif
                @if(!empty($store->address))
                    <div style="font-size: 10.5px;">{{ $store->address }}</div>
                @endif
                @if(!empty($store->phone))
                    <div style="font-size: 10.5px;">Tel: {{ $store->phone }}</div>
                @endif
                @if(!empty($store->tin))
                    <div style="font-size: 10.5px;">TIN: {{ $store->tin }}</div>
                @endif

                <div class="pos-receipt-divider my-1.5"></div>
                <div class="fw-bold" style="font-size: 11.5px;">SALES RECEIPT / TRANSACTION SLIP</div>
                <div style="font-size: 11px;">OR / SI #: <strong id="receiptInvoiceNo">{{ $invoiceNo }}</strong></div>
                <div style="font-size: 10.5px;">Date: <span id="receiptDate">{{ date('Y-m-d H:i:s') }}</span></div>
                <div style="font-size: 10.5px;">Cashier: <span id="receiptCashier">{{ $cashierName }}</span></div>
            </div>

            <!-- Customer Meta -->
            <div class="pos-receipt-divider my-1.5"></div>
            <div id="receiptCustomerSection" style="font-size: 10.5px;">
                <div>Customer: <span id="receiptCustomerName" class="fw-bold">Walk-in Customer</span></div>
            </div>

            <div class="pos-receipt-divider my-1.5"></div>

            <!-- Items Table (1:1 with print) -->
            <table class="w-100 mb-1" style="font-size: 11px; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px dashed #000;">
                        <th class="text-start pb-1">Qty Item</th>
                        <th class="text-end pb-1">Price</th>
                        <th class="text-end pb-1">Total</th>
                    </tr>
                </thead>
                <tbody id="receiptItemsContainer">
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted" style="font-size: 10.5px;">
                            (Receipt is empty)<br>
                            <small>Scan or add items</small>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="pos-receipt-divider my-1.5"></div>

            <!-- Summary Table (1:1 with print) -->
            <table class="w-100" style="font-size: 11px; border-collapse: collapse;">
                <tr>
                    <td class="text-start">Subtotal (Gross):</td>
                    <td class="text-end" id="receiptSubtotal">₱0.00</td>
                </tr>
                <tr id="receiptDiscountRow" class="d-none">
                    <td class="text-start text-danger">Discount:</td>
                    <td class="text-end text-danger" id="receiptDiscount">-₱0.00</td>
                </tr>
                <tr style="font-weight: bold; font-size: 12.5px;">
                    <td class="text-start pt-1">TOTAL AMOUNT DUE:</td>
                    <td class="text-end pt-1" id="receiptTotal">₱0.00</td>
                </tr>
                <tr id="receiptTenderedRow">
                    <td class="text-start">Payment:</td>
                    <td class="text-end" id="receiptTendered">₱0.00</td>
                </tr>
                <tr id="receiptChangeRow">
                    <td class="text-start">Change:</td>
                    <td class="text-end" id="receiptChange">₱0.00</td>
                </tr>
            </table>

            <div class="pos-receipt-divider my-2"></div>

            <!-- TAX BREAKDOWN (12% VAT) -->
            <div class="fw-bold text-center mb-1" style="font-size: 10.5px;">TAX BREAKDOWN (12% VAT)</div>
            <table class="w-100 text-muted" style="font-size: 10px; border-collapse: collapse; color: #333 !important;">
                <tr>
                    <td class="text-start">VATable Sales (12%):</td>
                    <td class="text-end" id="receiptVatable">₱0.00</td>
                </tr>
                <tr>
                    <td class="text-start">VAT Amount (12%):</td>
                    <td class="text-end" id="receiptVatAmount">₱0.00</td>
                </tr>
                <tr>
                    <td class="text-start">VAT Exempt Sales:</td>
                    <td class="text-end">₱0.00</td>
                </tr>
                <tr>
                    <td class="text-start">Zero Rated Sales:</td>
                    <td class="text-end">₱0.00</td>
                </tr>
            </table>

            <div class="pos-receipt-divider-double my-2"></div>

            <!-- Footer -->
            <div class="text-center" style="font-size: 9px; line-height: 1.3;">
                <div class="fw-bold mt-1" style="font-size: 10.5px;">
                    {{ $store->footer_text ?? "THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN" }}
                </div>
                <div style="font-size: 8px; margin-top: 3px; font-weight: bold;">
                    THIS DOCUMENT SERVES AS AN OFFICIAL SALES RECORD
                </div>
                <div style="font-size: 7.5px; color: #555; margin-top: 2px;">
                    POS Software: LikhaPOS Enterprise
                </div>
            </div>

        </div>
    </div>
</div>
