<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode Labels &mdash; {{ $storeName }}</title>
    <!-- JsBarcode for crisp, laser-sharp printable vector barcodes -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #0f172a;
            color: #000;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Screen Controls Toolbar (Hidden on Print) */
        .print-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1e293b;
            color: #fff;
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,0.35);
            z-index: 9999;
            border-bottom: 1px solid #334155;
        }

        .btn-print {
            background: #059669;
            color: #fff;
            border: none;
            padding: 9px 24px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
            transition: all 0.15s ease;
        }
        .btn-print:hover { 
            background: #047857;
            transform: translateY(-1px);
        }

        .btn-close-window {
            background: #334155;
            color: #fff;
            border: none;
            padding: 9px 20px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13.5px;
            cursor: pointer;
            margin-left: 8px;
        }
        .btn-close-window:hover { background: #475569; }

        .labels-page-container {
            margin-top: 75px;
            margin-bottom: 40px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        /* ── Page Sheet Container ── */
        .print-page-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            page-break-after: always;
            break-after: page;
            box-sizing: border-box;
        }
        .print-page-sheet:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        /* ============================================================
           FORMAT 1: COMPACT SHELF PRICE TAGS (Compact 36mm Card + Large Barcode)
        ============================================================ */
        .sheet-shelf_tag {
            padding: 8mm 6mm;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(7, 36mm);
            grid-gap: 3.5mm;
            align-content: start;
        }

        .shelf-tag-card {
            border: 1.5px solid #000000;
            border-radius: 4px;
            background: #ffffff;
            height: 36mm; /* Compact Height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 0 0 1px #000000;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            box-sizing: border-box;
        }

        /* Shelf Top Header Bar */
        .shelf-header-bar {
            background: #000000;
            color: #ffffff;
            padding: 1.5px 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 5.5mm;
        }
        .shelf-header-store {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .shelf-header-badge {
            background: #ffffff;
            color: #000000;
            font-size: 7px;
            font-weight: 800;
            padding: 0.5px 4px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .shelf-header-sku {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            font-weight: 700;
            color: #e2e8f0;
            letter-spacing: 0.4px;
        }

        /* Compact Shelf Body: Left Product Info + Large Barcode | Right SRP Price */
        .shelf-main-content {
            display: flex;
            align-items: stretch;
            height: 30.5mm;
            background: #ffffff;
        }

        .shelf-product-left {
            flex: 1;
            padding: 2px 6px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1.5px solid #000000;
            overflow: hidden;
        }
        .shelf-product-title {
            font-size: 12.5px;
            font-weight: 900;
            line-height: 1.1;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: -0.1px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
        .shelf-barcode-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            margin: 0;
            overflow: hidden;
        }
        .shelf-barcode-svg {
            width: 100% !important;
            height: 50px !important;
            display: block;
        }
        .shelf-product-meta {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            font-weight: 800;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            white-space: nowrap;
            line-height: 1;
        }

        .shelf-price-right {
            width: 38%;
            background: #f8fafc;
            padding: 2px 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .shelf-price-tag-label {
            font-size: 7.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #000000;
            line-height: 1;
            margin-bottom: 1px;
        }
        .shelf-price-amount {
            font-family: 'JetBrains Mono', 'Inter', sans-serif;
            font-size: 21px;
            font-weight: 900;
            color: #000000;
            line-height: 0.95;
            letter-spacing: -0.5px;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .shelf-price-amount .currency-symbol {
            font-size: 13px;
            font-weight: 900;
            margin-right: 1px;
            margin-top: 1px;
        }
        .shelf-price-footer-tax {
            font-size: 6.5px;
            font-weight: 800;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-top: 1px;
        }

        /* ============================================================
           FORMAT 2: A4 3-COLUMN STICKERS (Compact Packaging Labels)
        ============================================================ */
        .sheet-a4_3col {
            padding: 10mm 8mm;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-gap: 4mm;
            align-content: start;
        }

        .sticker-card-a4 {
            border: 1px dashed #94a3b8;
            border-radius: 4px;
            padding: 5px 6px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            height: 38mm;
            overflow: hidden;
            background: #ffffff;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }
        .sticker-a4-store {
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .sticker-a4-name {
            font-size: 11px;
            font-weight: 800;
            line-height: 1.15;
            color: #000000;
            margin: 2px 0;
            max-height: 24px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .sticker-a4-price {
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
            font-weight: 900;
            color: #000000;
            line-height: 1;
        }

        /* ============================================================
           FORMAT 3: THERMAL ROLL 50x30mm
        ============================================================ */
        .sheet-thermal_50x30 {
            width: 50mm;
            min-height: auto;
            background: transparent;
            margin: 0 auto;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 4mm;
            box-shadow: none;
        }
        .sheet-thermal_50x30 .sticker-card-thermal {
            width: 50mm;
            height: 30mm;
            border: 1.5px solid #000000;
            border-radius: 4px;
            padding: 2mm 3mm;
            page-break-after: always;
            break-after: page;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            box-sizing: border-box;
        }

        /* ============================================================
           PRINT MEDIA OPTIMIZATIONS
        ============================================================ */
        @media print {
            .print-toolbar { display: none !important; }
            body { 
                background: #ffffff !important; 
                margin: 0 !important;
                padding: 0 !important;
            }
            .labels-page-container { 
                margin: 0 !important; 
                padding: 0 !important; 
                gap: 0 !important;
                display: block !important;
            }
            .print-page-sheet {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 10mm 7mm !important;
                width: 210mm !important;
                height: 297mm !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            .print-page-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            .shelf-tag-card {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .sticker-card-a4 {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
                border: 1px dashed #cbd5e1 !important;
            }
            .sheet-thermal_50x30 {
                gap: 0 !important;
            }
            .sheet-thermal_50x30 .sticker-card-thermal {
                box-shadow: none !important;
                border: 1px solid #000000 !important;
                border-radius: 3px !important;
            }
            @page {
                size: auto;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    {{-- Top Floating Toolbar --}}
    <div class="print-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <div style="background: rgba(255,255,255,0.12); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; letter-spacing: 0.5px;">
                {{ strtoupper($storeName) }}
            </div>
            <strong style="font-size: 14px; font-weight: 800;">Barcode &amp; Shelf Price Tags</strong>
            <span style="color: #94a3b8; font-size: 13px;">&bull; Total <strong>{{ count($labels) }}</strong> Label(s)</span>
        </div>
        <div>
            <button type="button" class="btn-print" onclick="window.print();">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
                </svg>
                Print Labels Now
            </button>
            <button type="button" class="btn-close-window" onclick="window.close();">Close</button>
        </div>
    </div>

    <div class="labels-page-container">
        @if($paperSize === 'shelf_tag')
            {{-- ── Shelf Price Tags: Chunked by 14 tags per A4 Page Sheet (2 cols x 7 rows @ 36mm) ── --}}
            @foreach(array_chunk($labels, 14) as $pageIndex => $pageLabels)
                <div class="print-page-sheet sheet-shelf_tag">
                    @foreach($pageLabels as $label)
                        <div class="shelf-tag-card">
                            {{-- Header Ribbon --}}
                            <div class="shelf-header-bar">
                                <div class="shelf-header-store">
                                    @if($showStoreName)
                                        <span>{{ $storeName }}</span>
                                    @endif
                                    <span class="shelf-header-badge">RETAIL</span>
                                </div>
                                <div class="shelf-header-sku">
                                    SKU: {{ $label['sku'] ?: $label['barcode'] }}
                                </div>
                            </div>

                            {{-- Compact Main Content: Left Product Info + Barcode | Right SRP Price --}}
                            <div class="shelf-main-content">
                                <div class="shelf-product-left">
                                    <div class="shelf-product-title">{{ $label['name'] }}</div>
                                    <div class="shelf-barcode-wrapper">
                                        <svg class="barcode-item shelf-barcode-svg" data-code="{{ $label['barcode'] }}"></svg>
                                    </div>
                                    <div class="shelf-product-meta">
                                        <span>RETAIL SHELF ITEM</span>
                                        <span>TAX INCL.</span>
                                    </div>
                                </div>

                                @if($showPrice)
                                    <div class="shelf-price-right">
                                        <div class="shelf-price-tag-label">SRP PRICE</div>
                                        <div class="shelf-price-amount">
                                            <span class="currency-symbol">₱</span>{{ number_format($label['price'], 2) }}
                                        </div>
                                        <div class="shelf-price-footer-tax">VAT INCLUDED</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

        @elseif($paperSize === 'thermal_50x30')
            {{-- ── Thermal Roll 50x30mm ── --}}
            <div class="sheet-thermal_50x30">
                @foreach($labels as $label)
                    <div class="sticker-card-thermal">
                        @if($showStoreName)
                            <div class="sticker-a4-store">{{ $storeName }}</div>
                        @endif
                        <div class="sticker-a4-name">{{ $label['name'] }}</div>
                        <svg class="barcode-item" data-code="{{ $label['barcode'] }}" style="height: 24px; max-width: 100%;"></svg>
                        @if($showPrice)
                            <div class="sticker-a4-price">₱{{ number_format($label['price'], 2) }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

        @else
            {{-- ── A4 3-Column Stickers: Chunked by 21 stickers per A4 Page Sheet ── --}}
            @foreach(array_chunk($labels, 21) as $pageIndex => $pageLabels)
                <div class="print-page-sheet sheet-a4_3col">
                    @foreach($pageLabels as $label)
                        <div class="sticker-card-a4">
                            @if($showStoreName)
                                <div class="sticker-a4-store">{{ $storeName }}</div>
                            @endif
                            <div class="sticker-a4-name">{{ $label['name'] }}</div>
                            <svg class="barcode-item" data-code="{{ $label['barcode'] }}" style="height: 26px; max-width: 100%;"></svg>
                            @if($showPrice)
                                <div class="sticker-a4-price">₱{{ number_format($label['price'], 2) }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Render all SVG barcodes with JsBarcode Code128
            document.querySelectorAll('.barcode-item').forEach(function (svg) {
                const code = svg.getAttribute('data-code') || '00000000';
                const isShelfTag = svg.classList.contains('shelf-barcode-svg');

                try {
                    JsBarcode(svg, code, {
                        format: "CODE128",
                        displayValue: true, // Display barcode number directly below barcode
                        fontSize: isShelfTag ? 10 : 9,
                        textMargin: 1,
                        margin: 0,
                        height: isShelfTag ? 24 : 20,
                        width: isShelfTag ? 1.7 : 1.2,
                        font: "monospace"
                    });

                    // Ensure shelf tag barcodes expand full width and height
                    if (isShelfTag) {
                        svg.removeAttribute("width");
                        svg.removeAttribute("height");
                        svg.setAttribute("preserveAspectRatio", "none");
                        svg.style.width = "100%";
                        svg.style.height = "26px";
                        svg.style.display = "block";
                    }
                } catch (e) {
                    console.warn('Barcode render fallback for:', code);
                }
            });
        });
    </script>
</body>
</html>
