<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Receipt - {{ $sale->invoice_no }}</title>
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
            font-size: 13.5px;
            font-weight: bold;
            text-transform: uppercase;
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
    <button onclick="window.print()" style="padding: 8px 16px; background: #059669; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Print Receipt</button>
</div>

<div class="text-center">
    <div class="header-title">{{ $tenant->business_name ?? 'LIKHAPOS RETAIL STORE' }}</div>
    @if(!empty($tenant->header_text))
        <div style="font-size: 10px; font-style: italic; margin-bottom: 2px;">{{ $tenant->header_text }}</div>
    @endif
    @if(!empty($tenant->owner_name))
        <div>PROP: {{ strtoupper($tenant->owner_name) }}</div>
    @endif
    @if(!empty($tenant->address))
        <div>{{ $tenant->address }}</div>
    @endif
    @if(!empty($tenant->phone))
        <div>TEL / MOBILE: {{ $tenant->phone }}</div>
    @endif
    @if(!empty($tenant->email))
        <div>EMAIL: {{ $tenant->email }}</div>
    @endif
    @if(!empty($tenant->tin))
        <div>VAT REG TIN: {{ $tenant->tin }}</div>
    @endif
    @if(!empty($tenant->branch_code))
        <div>BRANCH CODE: {{ $tenant->branch_code }}</div>
    @endif
    @if(!empty($tenant->bir_min))
        <div>MIN: {{ $tenant->bir_min }}</div>
    @endif
    @if(!empty($tenant->bir_sn))
        <div>SERIAL NO: {{ $tenant->bir_sn }}</div>
    @endif
    @if(!empty($tenant->bir_acc_no))
        <div>BIR ACCR NO: {{ $tenant->bir_acc_no }}</div>
    @endif
    <div class="divider"></div>
    <div class="fw-bold">SALES INVOICE / OFFICIAL RECEIPT</div>
</div>

<div style="font-size: 10px; line-height: 14px; margin: 3px 0;">
    <div>SI/OR NO  : <strong>{{ $sale->invoice_no }}</strong></div>
    <div>DATE/TIME : {{ $sale->sale_date ? $sale->sale_date->format('Y-m-d H:i:s') : date('Y-m-d H:i:s') }}</div>
    <div>CASHIER   : {{ $sale->cashier?->name ?? 'Cashier' }}</div>
    <div>CUSTOMER  : {{ $sale->customer ? ($sale->customer->CustomerName ?? $sale->customer->name) : 'Walk-in Customer' }}</div>
</div>

<div class="divider"></div>

@php
    $rawGross = $sale->items->sum(function($it) {
        return (float)$it->qty * (float)$it->unit_price;
    });
    $itemDiscounts = $sale->items->sum(function($it) {
        return (float)($it->discount_amount ?? 0);
    });
    $totalSavings = max($itemDiscounts, (float)$sale->discount_amount, ($rawGross > (float)$sale->total_amount ? $rawGross - (float)$sale->total_amount : 0));
    $savingsPct = $rawGross > 0 && $totalSavings > 0 ? round(($totalSavings / $rawGross) * 100, 1) : 0;
@endphp

<table>
    <thead>
        <tr>
            <th class="text-left" style="width: 50%;">QTY  DESCRIPTION</th>
            <th class="text-right" style="width: 25%;">PRICE</th>
            <th class="text-right" style="width: 25%;">AMOUNT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
            @php
                $itemQty = (float)$item->qty;
                $unitPrice = (float)$item->unit_price;
                $grossLineTotal = $itemQty * $unitPrice;
            @endphp
            <tr>
                <td class="text-left">
                    {{ number_format($itemQty, 0) }}x  {{ $item->product_name }}
                    <span style="font-size:9px;">({{ $sale->vat_exempt_sales > 0 ? 'E' : 'V' }})</span>
                </td>
                <td class="text-right">₱{{ number_format($unitPrice, 2) }}</td>
                <td class="text-right">₱{{ number_format($grossLineTotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="divider"></div>

<div style="display: flex; justify-content: space-between; font-size: 10px; font-weight: bold; margin: 2px 0;">
    <span>TOTAL ITEMS: {{ count($sale->items) }}</span>
    <span>TOTAL QTY: {{ number_format($sale->items->sum('qty'), 0) }}</span>
</div>

<div class="divider"></div>

<table>
    <tr>
        <td>SUBTOTAL (GROSS):</td>
        <td class="text-right">₱{{ number_format($rawGross > 0 ? $rawGross : $sale->subtotal, 2) }}</td>
    </tr>
    @if($totalSavings > 0)
        <tr>
            <td style="color: #dc2626; font-weight: bold;">LESS: PROMO DISCOUNT:</td>
            <td class="text-right" style="color: #dc2626; font-weight: bold;">-₱{{ number_format($totalSavings, 2) }}</td>
        </tr>
    @endif
    <tr class="fw-bold">
        <td>TOTAL AMOUNT DUE:</td>
        <td class="text-right">₱{{ number_format($sale->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td>TENDERED ({{ strtoupper($sale->payment_method ?? 'CASH') }}):</td>
        <td class="text-right">₱{{ number_format($sale->tendered_amount > 0 ? $sale->tendered_amount : $sale->total_amount, 2) }}</td>
    </tr>
    <tr>
        <td>CHANGE:</td>
        <td class="text-right">₱{{ number_format($sale->change_amount, 2) }}</td>
    </tr>
</table>

<div class="divider"></div>

<div class="fw-bold text-center">TAX BREAKDOWN (12% VAT)</div>
<table>
    <tr>
        <td>VATABLE SALES (12%):</td>
        <td class="text-right">₱{{ number_format($sale->vatable_sales, 2) }}</td>
    </tr>
    <tr>
        <td>VAT AMOUNT (12%):</td>
        <td class="text-right">₱{{ number_format($sale->tax_amount, 2) }}</td>
    </tr>
    <tr>
        <td>VAT-EXEMPT SALES:</td>
        <td class="text-right">₱{{ number_format($sale->vat_exempt_sales, 2) }}</td>
    </tr>
    <tr>
        <td>ZERO-RATED SALES:</td>
        <td class="text-right">₱{{ number_format($sale->zero_rated_sales, 2) }}</td>
    </tr>
</table>

@if($sale->discount_id_no)
    <div class="divider"></div>
    <div>SC/PWD ID: {{ $sale->discount_id_no }}</div>
    <div>HOLDER   : {{ $sale->discount_holder ?? 'Senior Citizen / PWD' }}</div>
@endif

@if($totalSavings > 0)
    <div class="divider"></div>
    <div style="border: 1px dashed #d97706; background: #fffbeb; padding: 6px; margin: 6px 0; text-align: center; border-radius: 4px;">
        <div style="font-weight: bold; font-size: 10px; color: #b45309;">*** TOTAL PROMO SAVINGS ***</div>
        <div style="font-weight: 900; font-size: 11.5px; color: #dc2626;">YOU SAVED ₱{{ number_format($totalSavings, 2) }} ({{ $savingsPct }}% OFF)</div>
        <div style="font-size: 8px; color: #78350f;">Thank you for shopping and saving with us!</div>
    </div>
@endif

<div class="double-divider"></div>

<div class="text-center footer-note">
    <div style="margin-top: 4px; white-space: pre-line;" class="fw-bold">{{ $tenant->footer_text ?? "THANK YOU FOR YOUR PURCHASE!\nPLEASE COME AGAIN" }}</div>
    <div style="font-size: 8px; margin-top: 4px;">THIS DOCUMENT SERVES AS AN OFFICIAL SALES INVOICE</div>
    <div style="font-size: 7.5px; color: #555; margin-top: 2px;">POS System: LikhaPOS Enterprise</div>
</div>

</body>
</html>
