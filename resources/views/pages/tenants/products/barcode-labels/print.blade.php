<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode Labels - {{ $storeName }}</title>
    <!-- JsBarcode for crisp, laser-sharp printable vector barcodes -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f1f5f9;
            color: #000;
        }

        /* Screen Controls Toolbar (Hidden on Print) */
        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #0f172a;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
        }

        .btn-print {
            background: #059669;
            color: #fff;
            border: none;
            padding: 8px 24px;
            border-radius: 999px;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-print:hover { background: #047857; }

        .btn-close-window {
            background: #334155;
            color: #fff;
            border: none;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 14px;
            cursor: pointer;
            margin-left: 8px;
        }

        .labels-page-container {
            margin-top: 70px;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        /* ── Format 1: A4 3-Column Sheet ───────────────────────── */
        .sheet-a4_3col {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            padding: 10mm 8mm;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 4mm;
            align-content: start;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .sticker-card {
            border: 1px dashed #cbd5e1;
            padding: 6px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 38mm;
            overflow: hidden;
            background: #fff;
        }

        /* ── Format 2: Shelf Price Tags (Large SRP) ────────────── */
        .sheet-shelf_tag {
            width: 210mm;
            background: #fff;
            padding: 10mm;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-gap: 6mm;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .sheet-shelf_tag .sticker-card {
            height: 52mm;
            border: 2px solid #000;
            padding: 8px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .shelf-header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1.5px solid #000;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        .shelf-store { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .shelf-name { font-size: 14px; font-weight: 900; line-height: 1.2; text-align: left; margin-bottom: 4px; width: 100%; }
        .shelf-body {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .shelf-price-box { text-align: right; }
        .shelf-price-label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #475569; }
        .shelf-price-val { font-size: 26px; font-weight: 900; font-family: monospace; line-height: 1; }

        /* ── Format 3: Thermal Roll 50x30mm ────────────────────── */
        .sheet-thermal_50x30 {
            width: 50mm;
            background: #fff;
            margin: 0 auto;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 2mm;
        }
        .sheet-thermal_50x30 .sticker-card {
            width: 50mm;
            height: 30mm;
            border: none;
            padding: 3mm;
            page-break-after: always;
        }

        /* Generic Sticker Elements */
        .sticker-store {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .sticker-name {
            font-size: 11px;
            font-weight: 800;
            line-height: 1.15;
            margin: 2px 0;
            max-height: 24px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .sticker-barcode-svg {
            max-width: 100%;
            height: 28px !important;
            margin: 2px 0;
        }

        .sticker-price {
            font-size: 14px;
            font-weight: 900;
            font-family: monospace;
            color: #000;
            line-height: 1;
        }

        /* Print Media Settings */
        @media print {
            .print-toolbar { display: none !important; }
            body { background: #fff; }
            .labels-page-container { margin-top: 0; padding: 0; }
            .sticker-card { border: none !important; }
            .sheet-shelf_tag .sticker-card { border: 1.5px solid #000 !important; }
            .sheet-a4_3col { box-shadow: none; padding: 5mm 5mm; }
            @page {
                margin: 0;
                size: auto;
            }
        }
    </style>
</head>
<body>

    <div class="print-toolbar">
        <div>
            <strong>Barcode & Shelf Tags</strong> — Total {{ count($labels) }} Label(s) ready to print
        </div>
        <div>
            <button type="button" class="btn-print" onclick="window.print();">🖨️ Print Labels Now</button>
            <button type="button" class="btn-close-window" onclick="window.close();">Close</button>
        </div>
    </div>

    <div class="labels-page-container">
        <div class="sheet-{{ $paperSize }}">
            @foreach($labels as $idx => $label)
                @if($paperSize === 'shelf_tag')
                    {{-- Large Shelf Price Tag --}}
                    <div class="sticker-card">
                        <div class="shelf-header">
                            @if($showStoreName)
                                <div class="shelf-store">{{ $storeName }}</div>
                            @endif
                            <div class="shelf-store font-mono">{{ $label['sku'] ?: $label['barcode'] }}</div>
                        </div>
                        <div class="shelf-name">{{ $label['name'] }}</div>
                        <div class="shelf-body">
                            <svg class="barcode-item" data-code="{{ $label['barcode'] }}" style="height: 32px; max-width: 55%;"></svg>
                            @if($showPrice)
                                <div class="shelf-price-box">
                                    <div class="shelf-price-label">SRP Price</div>
                                    <div class="shelf-price-val">₱{{ number_format($label['price'], 2) }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Standard Barcode Sticker (A4 or Thermal) --}}
                    <div class="sticker-card">
                        @if($showStoreName)
                            <div class="sticker-store">{{ $storeName }}</div>
                        @endif
                        <div class="sticker-name">{{ $label['name'] }}</div>
                        <svg class="barcode-item sticker-barcode-svg" data-code="{{ $label['barcode'] }}"></svg>
                        @if($showPrice)
                            <div class="sticker-price">₱{{ number_format($label['price'], 2) }}</div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Render all SVG barcodes with JsBarcode Code128
            document.querySelectorAll('.barcode-item').forEach(function (svg) {
                const code = svg.getAttribute('data-code') || '00000000';
                try {
                    JsBarcode(svg, code, {
                        format: "CODE128",
                        displayValue: true,
                        fontSize: 10,
                        margin: 0,
                        height: 28,
                        width: 1.3
                    });
                } catch (e) {
                    console.warn('Barcode render fallback for:', code);
                }
            });
        });
    </script>
</body>
</html>
