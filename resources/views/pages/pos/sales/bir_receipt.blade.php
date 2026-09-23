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
    @php
        $receiptLogo = !empty($tenant->logo_square) ? $tenant->logo_square : (!empty($tenant->logo) ? $tenant->logo : null);
    @endphp
    @if($receiptLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($receiptLogo))
        <img src="{{ \Illuminate\Support\Facades\Storage::url($receiptLogo) }}" alt="Logo" style="max-height: 52px; max-width: 140px; object-fit: contain; margin: 0 auto 5px auto; display: block;">
    @endif
    <div class="header-title">{{ $tenant->business_name ?? 'MINIMART STORE' }}</div>
    @if(!empty($tenant->owner_name))
        <div>Prop: {{ $tenant->owner_name }}</div>
    @endif
    @if(!empty($tenant->address))
        <div>{{ $tenant->address }}</div>
    @endif
    @if(!empty($tenant->phone))
        <div>Tel: {{ $tenant->phone }}</div>
    @endif
    @if(!empty($tenant->tin))
        <div>TIN: {{ $tenant->tin }}</div>
    @endif
    <div class="divider"></div>
    <div class="fw-bold">SALES RECEIPT / TRANSACTION SLIP</div>
    <div>OR / SI #: <strong>{{ $sale->invoice_no }}</strong></div>
    <div>Date: {{ $sale->sale_date ? $sale->sale_date->format('Y-m-d H:i:s') : date('Y-m-d H:i:s') }}</div>
    <div>Cashier: {{ $sale->cashier?->name ?? 'Cashier' }}</div>
</div>

<div class="divider"></div>

<div>Customer: {{ $sale->customer ? ($sale->customer->CustomerName ?? $sale->customer->name) : 'Walk-in Customer' }}</div>

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

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="border-bottom: 1px dashed #000;">
            <th class="text-left" style="padding-bottom: 2px;">ITEM</th>
            <th class="text-right" style="padding-bottom: 2px;">TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sale->items as $item)
            @php
                $itemQty = (float)$item->qty;
                $qtyStr = (floor($itemQty) == $itemQty) ? number_format($itemQty, 0) : rtrim(rtrim(number_format($itemQty, 4), '0'), '.');
                $unitPrice = (float)$item->unit_price;
                $grossLineTotal = (float)($item->line_total ?: ($itemQty * $unitPrice));
                $unitStr = $item->product?->unit?->name ?? (is_string($item->product?->unit) ? $item->product->unit : '');
            @endphp
            <tr>
                <td class="text-left fw-bold" style="padding-top: 3px; font-size: 11px;">{{ $item->product_name }}</td>
                <td class="text-right fw-bold" style="padding-top: 3px; font-size: 11px; white-space: nowrap;">₱{{ number_format($grossLineTotal, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-left" style="padding-bottom: 3px; font-size: 10px; color: #333;">
                    &nbsp;&nbsp;{{ $qtyStr }}{{ $unitStr ? ' ' . $unitStr : '' }} @ ₱{{ number_format($unitPrice, 2) }}
                </td>
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
