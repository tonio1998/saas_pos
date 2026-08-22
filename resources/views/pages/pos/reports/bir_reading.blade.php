<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BIR Audit Report - {{ $type }} ({{ $date->format('Y-m-d') }})</title>
    <style>
        @page {
            size: 80mm 250mm;
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
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="margin-bottom: 10px; text-align: center;">
    <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Print BIR Report</button>
</div>

<div class="text-center">
    <div class="fw-bold" style="font-size: 14px;">{{ $tenant->business_name ?? 'MINIMART STORE' }}</div>
    <div>Prop: {{ $tenant->owner_name ?? 'Store Proprietor' }}</div>
    <div>{{ $tenant->address ?? 'Philippines' }}</div>
    <div>TIN: {{ $tenant->tin ?? '000-000-000-00000' }}</div>
    <div>MIN: {{ $tenant->bir_min ?? 'MIN-2026-89127' }}</div>
    <div>SN: {{ $tenant->bir_sn ?? 'SN-891273918' }}</div>
    <div class="divider"></div>
    <div class="fw-bold">{{ $type }}</div>
    <div>Report Date: {{ $date->format('Y-m-d') }}</div>
    <div>Printed: {{ date('Y-m-d H:i:s') }}</div>
</div>

<div class="divider"></div>

<table>
    <tr>
        <td>First OR #:</td>
        <td class="text-right">{{ $summary['first_invoice'] }}</td>
    </tr>
    <tr>
        <td>Last OR #:</td>
        <td class="text-right">{{ $summary['last_invoice'] }}</td>
    </tr>
    <tr>
        <td>Transaction Count:</td>
        <td class="text-right">{{ $summary['total_transactions'] }}</td>
    </tr>
</table>

<div class="divider"></div>

<table>
    <tr class="fw-bold">
        <td>GROSS SALES:</td>
        <td class="text-right">₱{{ number_format($summary['gross_sales'], 2) }}</td>
    </tr>
    <tr>
        <td>Less: Regular Discounts:</td>
        <td class="text-right">-₱{{ number_format($summary['regular_discounts'], 2) }}</td>
    </tr>
    <tr>
        <td>Less: SC/PWD Discounts:</td>
        <td class="text-right">-₱{{ number_format($summary['sc_pwd_discounts'], 2) }}</td>
    </tr>
    <tr class="fw-bold">
        <td>NET SALES:</td>
        <td class="text-right">₱{{ number_format($summary['net_sales'], 2) }}</td>
    </tr>
</table>

<div class="divider"></div>

<div class="fw-bold text-center">TAX SUMMARY BREAKDOWN</div>
<table>
    <tr>
        <td>VATable Sales (12%):</td>
        <td class="text-right">₱{{ number_format($summary['vatable_sales'], 2) }}</td>
    </tr>
    <tr>
        <td>VAT Amount (12%):</td>
        <td class="text-right">₱{{ number_format($summary['vat_amount'], 2) }}</td>
    </tr>
    <tr>
        <td>VAT Exempt Sales:</td>
        <td class="text-right">₱{{ number_format($summary['vat_exempt_sales'], 2) }}</td>
    </tr>
    <tr>
        <td>Zero Rated Sales:</td>
        <td class="text-right">₱{{ number_format($summary['zero_rated_sales'], 2) }}</td>
    </tr>
</table>

@if(isset($summary['grand_total']))
    <div class="double-divider"></div>
    <table>
        <tr class="fw-bold" style="font-size: 12px;">
            <td>ACCUMULATED GRAND TOTAL:</td>
            <td class="text-right">₱{{ number_format($summary['grand_total'], 2) }}</td>
        </tr>
    </table>
@endif

<div class="double-divider"></div>

<div class="text-center" style="font-size: 9px; margin-top: 8px;">
    <div>END OF REPORT</div>
    <div>BIR ACC: {{ $tenant->bir_acc_no ?? 'BIR-ACC-2026-001' }}</div>
</div>

</body>
</html>
