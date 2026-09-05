<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thermal Receipt Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #ffffff;
            color: #000000;
            font-size: 12px;
            line-height: 1.35;
            padding: 12px 10px;
            max-width: 320px;
            margin: 0 auto;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .divider-double {
            border-top: 2px solid #000;
            margin: 6px 0;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 8px;
        }
        .store-logo {
            max-width: 60px;
            max-height: 60px;
            object-fit: contain;
            margin-bottom: 4px;
            filter: grayscale(100%) contrast(150%);
        }
        .store-title {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        .item-table th {
            text-align: left;
            border-bottom: 1px dashed #000;
            padding-bottom: 3px;
        }
        .item-table td {
            padding: 2px 0;
            vertical-align: top;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        .grand-total {
            font-size: 14px;
            font-weight: 900;
            margin: 4px 0;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 10px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        @if($tenant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo))
            <img src="{{ \Illuminate\Support\Facades\Storage::url($tenant->logo) }}" alt="Logo" class="store-logo"><br>
        @endif
        <div class="store-title">{{ $tenant->business_name ?? 'LIKHAPOS RETAIL' }}</div>
        <div>{{ $tenant->address ?? 'Main Branch, Surigao City' }}</div>
        <div>Tel: {{ $tenant->phone ?? '0912-345-6789' }}</div>
        <div>TIN: {{ $tenant->tin ?? '000-123-456-000' }}</div>
        @if($tenant->header_text)
            <div style="margin-top:3px;font-style:italic;">{{ $tenant->header_text }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <div style="display:flex;justify-content:space-between;font-size:11px;">
        <span>Invoice: #INV-20260831-01</span>
        <span>POS-01</span>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:11px;">
        <span>Date: {{ date('M d, Y h:i A') }}</span>
        <span>Cashier: Admin</span>
    </div>

    <div class="divider"></div>

    <table class="item-table">
        <thead>
            <tr>
                <th style="width:50%;">ITEM</th>
                <th class="text-center" style="width:20%;">QTY</th>
                <th class="text-right" style="width:30%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Kopiko Blanca 30g</td>
                <td class="text-center">2</td>
                <td class="text-right">₱28.00</td>
            </tr>
            <tr>
                <td>Lucky Me Pancit Canton</td>
                <td class="text-center">3</td>
                <td class="text-right">₱45.00</td>
            </tr>
            <tr>
                <td>Nature's Spring 500ml</td>
                <td class="text-center">1</td>
                <td class="text-right">₱15.00</td>
            </tr>
        </tbody>
    </table>

    <div class="divider"></div>

    <div class="total-row">
        <span>Subtotal:</span>
        <span>₱88.00</span>
    </div>
    <div class="total-row">
        <span>VATable Sales (12%):</span>
        <span>₱78.57</span>
    </div>
    <div class="total-row">
        <span>VAT Amount:</span>
        <span>₱9.43</span>
    </div>

    <div class="divider"></div>

    <div class="total-row grand-total">
        <span>TOTAL AMOUNT:</span>
        <span>₱88.00</span>
    </div>
    <div class="total-row">
        <span>CASH TENDERED:</span>
        <span>₱100.00</span>
    </div>
    <div class="total-row fw-bold">
        <span>CHANGE DUE:</span>
        <span>₱12.00</span>
    </div>

    <div class="divider-double"></div>

    <div class="receipt-footer">
        <div class="fw-bold">{{ $tenant->footer_text ?? 'THANK YOU FOR YOUR PURCHASE!' }}</div>
        <div style="font-size:9px;margin-top:4px;">LikhaPOS • Smart Cloud Retail</div>
    </div>
</body>
</html>
