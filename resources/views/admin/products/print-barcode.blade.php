<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barkod Yazdır - {{ $product->name }}</title>
    <style>
        /* Precision Print Settings for Zebra GK420d / ZD220 */
        @page {
            size: 58mm 40mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            width: 58mm;
            height: 40mm;
            overflow: hidden;
            background: #fff;
            color: #000;
        }

        .label-container {
            width: 58mm;
            height: 40mm;
            padding: 2mm 3mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .product-name {
            width: 100%;
            font-size: 8.5pt;
            font-weight: bold;
            line-height: 1.1;
            text-align: center;
            margin-bottom: 1mm;
            word-wrap: break-word;
        }

        .price-tag {
            font-size: 13pt;
            font-weight: 900;
            text-align: center;
            margin-bottom: 1mm;
        }

        .barcode-wrapper {
            width: 100%;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .barcode-img {
            max-width: 100%;
            height: 18mm; /* Optimized for Zebra thermal head */
            display: block;
        }

        .barcode-text {
            font-size: 8.5pt;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1.5px;
            margin-top: 0.5mm;
        }

        /* Print Button (Visible only on screen) */
        .no-print {
            position: fixed;
            top: 5px;
            right: 5px;
            background: #000;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            z-index: 9999;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <button class="no-print" onclick="window.print()">YAZDIR</button>

    <div class="label-container">
        <!-- 1. Product Name (Max 2 lines) -->
        <h1 class="product-name">
            {{ $product->name }}
        </h1>

        <!-- 2. Price (Prominent) -->
        <div class="price-tag">
            {{ number_format($product->price, 2, ',', '.') }} TL
        </div>

        <!-- 3. Barcode (Code 128) -->
        <div class="barcode-wrapper">
            @php
                $barcodeValue = $product->barcode ?? $product->sku ?? '0000000000000';
            @endphp
            <img class="barcode-img" 
                 src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ urlencode($barcodeValue) }}&scale=3&rotate=N" 
                 alt="Barcode">
            
            <!-- 4. Barcode Text (Human Readable) -->
            <div class="barcode-text">
                {{ $barcodeValue }}
            </div>
        </div>
    </div>
</body>
</html>
