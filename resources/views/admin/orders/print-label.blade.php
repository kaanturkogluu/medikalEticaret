<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zebra Label - #{{ $order->id }}</title>
    <style>
        /* Zebra GK420d / ZD220 Thermal Printer Optimization */
        @page {
            size: 100mm 100mm;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            font-family: 'Arial', sans-serif; /* Clean font for thermal printers */
            margin: 0;
            padding: 5mm;
            width: 100mm;
            height: 100mm;
            color: #000;
            background: #fff;
        }
        .label-border {
            border: 3px solid #000;
            height: 90mm; /* 100mm - padding */
            padding: 3mm;
            display: flex;
            flex-direction: column;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            font-weight: bold;
        }
        .info-grid {
            display: grid;
            grid-template-cols: 1fr;
            gap: 2mm;
        }
        .section-box {
            border: 1px solid #000;
            padding: 2mm;
        }
        .label-text {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 1mm;
            display: block;
        }
        .value-text {
            font-size: 13px;
            font-weight: 900;
            line-height: 1.2;
        }
        .items-list {
            flex-grow: 1;
            margin: 3mm 0;
            border: 2px solid #000;
            padding: 2mm;
            overflow: hidden;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th {
            font-size: 10px;
            text-align: left;
            border-bottom: 2px solid #000;
            padding-bottom: 1mm;
        }
        .items-table td {
            font-size: 11px;
            font-weight: 900;
            padding: 1.5mm 0;
            border-bottom: 1px solid #000;
        }
        .footer-area {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
        }
        .barcode-box {
            text-align: center;
        }
        .barcode-box img {
            height: 12mm;
            max-width: 50mm;
        }
        .barcode-val {
            font-size: 10px;
            font-weight: bold;
            font-family: monospace;
            margin-top: 1mm;
        }
        .packer-box {
            text-align: right;
            font-size: 10px;
            font-weight: 900;
            border-left: 2px solid #000;
            padding-left: 3mm;
        }
        .print-btn {
            position: fixed;
            top: 5px;
            right: 5px;
            background: #000;
            color: #fff;
            padding: 10px;
            font-weight: bold;
            border: none;
            cursor: pointer;
            z-index: 100;
        }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; margin: 0; }
            .label-border { height: 100mm; width: 100mm; border: none; } /* Printer uses physical labels */
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">YAZDIR (PRINT)</button>

    <div class="label-border">
        <div class="header">
            <h1>umutMed</h1>
            <p>SEVKİYAT ETİKETİ (WEB)</p>
        </div>

        <div class="info-grid">
            <div class="section-box">
                <span class="label-text">MÜŞTERİ / ADRES:</span>
                <div class="value-text">{{ $order->customer_name }}</div>
                <div class="value-text" style="font-size: 11px; margin-top: 1mm;">
                    {{ $order->address_info['address'] ?? '-' }}<br>
                    {{ $order->address_info['district'] ?? '' }} / {{ $order->address_info['city'] ?? '' }}
                </div>
            </div>
        </div>

        <div class="items-list">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>ÜRÜN ADI</th>
                        <th style="text-align: right;">ADET</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ str($item->product->name)->limit(40) }}</td>
                            <td style="text-align: right;">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="footer-area">
            <div class="barcode-box">
                @php
                    $barcodeData = $order->external_order_id ?? $order->id;
                @endphp
                <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $barcodeData }}&scale=4&rotate=N" alt="Barcode">
                <div class="barcode-val">{{ $barcodeData }}</div>
            </div>
            
            <div class="packer-box">
                <span class="label-text" style="margin-bottom: 0;">PAKETLEYİCİ:</span>
                <div class="value-text" style="font-size: 12px;">{{ $packer }}</div>
            </div>
        </div>
    </div>

    <script>
        // Zebra printers often benefit from a slight delay before printing starts
        window.onload = () => {
            // setTimeout(() => window.print(), 500);
        };
    </script>
</body>
</html>
