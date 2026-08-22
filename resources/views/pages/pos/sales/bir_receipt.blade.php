<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BIR Official Receipt - {{ $sale->invoice_no }}</title>
    <style>
        @page {
            size: 80mm 200mm;
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            width: 78mm;
            margin: 0 auto;
            padding: 4mm 2mm;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 4px 0;
        }
        .double-divider {
            border-bottom: 3px double #000;
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 2px 0;
            vertical-align: top;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
        }
        .footer-note {
            font-size: 9px;
            margin-top: 8px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="margin-bottom: 10px; text-align: center;">
    <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Print BIR Receipt</button>
</div>

<div class="text-center">
    <div class="header-title">{{ $tenant->business_name ?? 'MINIMART STORE' }}</div>
    <div>Prop: {{ $tenant->owner_name ?? 'Store Proprietor' }}</div>
    <div>{{ $tenant->address ?? 'Philippines' }}</div>
    <div>TIN: {{ $tenant->tin ?? '000-000-000-00000' }} (VAT Reg)</div>
    <div>MIN: {{ $tenant->bir_min ?? 'MIN-2026-89127' }}</div>
    <div>SN: {{ $tenant->bir_sn ?? 'SN-891273918' }}</div>
    <div>BIR Acc: {{ $tenant->bir_acc_no ?? 'BIR-ACC-2026-001' }}</div>
    <div class="divider"></div>
    <div class="fw-bold">SALES INVOICE / OFFICIAL RECEIPT</div>
    <div>OR/SI #: {{ $sale->invoice_no }}</div>
    <div>Date: {{ $sale->sale_date ? $sale->sale_date->format('Y-m-d H:i:s') : date('Y-m-d H:i:s') }}</div>
    <div>Cashier: {{ $sale->cashier?->name ?? 'Cashier' }}</div>
</div>

<div class="divider"></div>

@if($sale->customer)
    <div>Customer: {{ $sale->customer->name }}</div>
    <div>Address: {{ $sale->customer->address ?? 'N/A' }}</div>
    <div>TIN: {{ $sale->customer->tin ?? 'N/A' }}</div>
    <div class="divider"></div>
@endif

<table>
    <thead>
        <tr>
            <th class="text-left">Qty Item</th>
            <th class="text-right">Price</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
            <tr>
                <td class="text-left">
                    {{ number_format($item->qty, 0) }} x {{ $item->product_name }}
                    <span style="font-size:9px;">({{ $sale->vat_exempt_sales > 0 ? 'E' : 'V' }})</span>
                </td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="divider"></div>

<table>
    <tr>
        <td>Subtotal (Gross):</td>
        <td class="text-right">₱{{ number_format($sale->subtotal, 2) }}</td>
    </tr>
    @if($sale->discount_amount > 0)
        <tr>
            <td>Discount {{ $sale->discount_id_no ? '(SC/PWD)' : '' }}:</td>
            <td class="text-right">-₱{{ number_format($sale->discount_amount, 2) }}</td>
        </tr>
    @endif
    <tr class="fw-bold">
        <td>TOTAL AMOUNT DUE:</td>
        <td class="text-right">₱{{ number_format($sale->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td>Payment ({{ strtoupper($sale->payment_method) }}):</td>
        <td class="text-right">₱{{ number_format($sale->tendered_amount > 0 ? $sale->tendered_amount : $sale->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td>Change:</td>
        <td class="text-right">₱{{ number_format($sale->change_amount, 2) }}</td>
    </tr>
</table>

<div class="divider"></div>

<div class="fw-bold text-center">BIR TAX BREAKDOWN</div>
<table>
    <tr>
        <td>VATable Sales (12%):</td>
        <td class="text-right">₱{{ number_format($sale->vatable_sales, 2) }}</td>
    </tr>
    <tr>
        <td>VAT Amount (12%):</td>
        <td class="text-right">₱{{ number_format($sale->tax_amount, 2) }}</td>
    </tr>
    <tr>
        <td>VAT Exempt Sales:</td>
        <td class="text-right">₱{{ number_format($sale->vat_exempt_sales, 2) }}</td>
    </tr>
    <tr>
        <td>Zero Rated Sales:</td>
        <td class="text-right">₱{{ number_format($sale->zero_rated_sales, 2) }}</td>
    </tr>
</table>

@if($sale->discount_id_no)
    <div class="divider"></div>
    <div>SC/PWD ID: {{ $sale->discount_id_no }}</div>
    <div>Holder: {{ $sale->discount_holder ?? 'Senior Citizen / PWD' }}</div>
@endif

<div class="double-divider"></div>

<div class="text-center footer-note">
    <div>POS Provider: RetailPOS Inc.</div>
    <div>TIN: 999-888-777-00000</div>
    <div>Accreditation No: BIR-ACC-2026-999</div>
    <div style="margin-top: 6px;" class="fw-bold">THANK YOU FOR YOUR PURCHASE!</div>
    <div style="font-size: 8px; margin-top: 4px;">THIS SERVES AS YOUR OFFICIAL RECEIPT</div>
</div>

</body>
</html>
