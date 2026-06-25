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
            padding: 0;
            width: 100mm;
            color: #000;
            background: #fff;
        }
        .page-break {
            page-break-after: always;
            height: 100mm;
            width: 100mm;
            padding: 5mm;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .label-border {
            border: 3px solid #000;
            flex-grow: 1;
            padding: 3mm;
            display: flex;
            flex-direction: column;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            font-size: 10px;
            font-weight: bold;
        }
        .section-box {
            border-bottom: 1px dashed #000;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }
        .section-box:last-child {
            border-bottom: none;
        }
        .label-text {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
            margin-bottom: 1px;
            color: #333;
        }
        .value-text {
            font-size: 12px;
            font-weight: 900;
            line-height: 1.2;
            word-wrap: break-word;
        }
        .value-text.address {
            font-size: 10px;
            font-weight: bold;
        }
        .value-text.product {
            font-size: 11px;
        }
        .barcode-box {
            text-align: center;
            margin-top: auto;
            border-top: 2px solid #000;
            padding-top: 2mm;
        }
        .barcode-box img {
            height: 14mm;
            max-width: 80mm;
        }
        .barcode-val {
            font-size: 12px;
            font-weight: 900;
            font-family: monospace;
            margin-top: 1mm;
            letter-spacing: 2px;
        }
        .qty-badge {
            background: #000;
            color: #fff;
            font-size: 14px;
            font-weight: 900;
            padding: 2px 6px;
            border-radius: 4px;
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
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">YAZDIR (PRINT)</button>

    @php
        // Normal items
        $itemsToPrint = [];
        if ($order->items && $order->items->count() > 0) {
            foreach($order->items as $item) {
                $itemsToPrint[] = [
                    'product_name' => $item->product ? (($item->product->brand ? $item->product->brand->name . ' - ' : '') . $item->product->name) : 'Bilinmeyen Ürün',
                    'barcode' => ($item->product && $item->product->barcode) ? $item->product->barcode : ($item->product ? $item->product->sku : $order->external_order_id),
                    'qty' => $item->quantity
                ];
            }
        } 
        // Fallback for raw marketplace lines if order items not synced
        elseif (isset($order->raw_marketplace_data['lines'])) {
            foreach($order->raw_marketplace_data['lines'] as $line) {
                $itemsToPrint[] = [
                    'product_name' => $line['productName'] ?? $line['name'] ?? 'Bilinmeyen Ürün',
                    'barcode' => $line['barcode'] ?? $line['merchantSku'] ?? $line['sku'] ?? $order->external_order_id,
                    'qty' => $line['quantity'] ?? 1
                ];
            }
        }
        // Fallback for PTT AVM
        elseif (isset($order->raw_marketplace_data['siparisUrunler'])) {
            foreach($order->raw_marketplace_data['siparisUrunler'] as $line) {
                $itemsToPrint[] = [
                    'product_name' => $line['urun'] ?? 'Bilinmeyen Ürün',
                    'barcode' => $line['urunBarkod'] ?? $line['variantBarkod'] ?? $order->external_order_id,
                    'qty' => $line['toplamIslemAdedi'] ?? 1
                ];
            }
        }
        // Absolute fallback (just the order)
        else {
            $itemsToPrint[] = [
                'product_name' => 'Sipariş Paketi',
                'barcode' => $order->external_order_id ?? $order->id,
                'qty' => 1
            ];
        }
    @endphp

    @foreach($itemsToPrint as $idx => $item)
        <div class="page-break">
            <div class="label-border">
                <div class="header">
                    <h1>umutMed</h1>
                    <p>Sip: #{{ $order->external_order_id ?? $order->id }}</p>
                </div>

                <div class="section-box">
                    <span class="label-text">MÜŞTERİ:</span>
                    <div class="value-text">{{ $order->customer_name }}</div>
                </div>

                <div class="section-box" style="flex-grow: 1;">
                    <span class="label-text">ADRES:</span>
                    <div class="value-text address">
                        {{ $order->address_info['address'] ?? ($order->raw_marketplace_data['shipmentAddress']['fullAddress'] ?? '-') }}<br>
                        {{ $order->address_info['district'] ?? ($order->raw_marketplace_data['shipmentAddress']['district'] ?? '') }} / 
                        {{ $order->address_info['city'] ?? ($order->raw_marketplace_data['shipmentAddress']['city'] ?? '') }}
                    </div>
                </div>

                <div class="section-box">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <span class="label-text">ÜRÜN ({{ $idx + 1 }}/{{ count($itemsToPrint) }}):</span>
                            <div class="value-text product">{{ $item['product_name'] }}</div>
                        </div>
                        <div class="qty-badge">{{ $item['qty'] }} Adet</div>
                    </div>
                </div>

                <div class="barcode-box">
                    @php
                        // Clean barcode for URL
                        $cleanBarcode = urlencode(trim($item['barcode']));
                    @endphp
                    <!-- Using bwip-js API to generate a crisp barcode -->
                    <img src="https://bwipjs-api.metafloor.com/?bcid=code128&text={{ $cleanBarcode }}&scale=4&rotate=N" alt="Barcode">
                    <div class="barcode-val">{{ $item['barcode'] }}</div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        window.onload = () => {
            // setTimeout(() => window.print(), 500);
        };
    </script>
</body>
</html>
