<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon Yazdır - {{ $coupons->first()->code ?? 'Kuponlar' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { 
            size: A4 portrait; 
            margin: 10mm; 
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }
        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 5mm;
            padding: 5mm;
        }
        .coupon-container {
            width: 100%;
            height: 90mm;
            background: white;
            background-image: radial-gradient(#f1f5f9 1px, transparent 1px);
            background-size: 20px 20px;
            position: relative;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 2px dashed #cbd5e1;
            display: flex;
            flex-direction: column;
            padding: 15px 40px;
            box-sizing: border-box;
        }
        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .logo-text {
            color: #1e3a8a;
            line-height: 1;
            text-align: center;
        }
        .logo-text .top {
            font-weight: 800;
            font-size: 22px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .logo-text .bottom {
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .logo-text .bottom::before, .logo-text .bottom::after {
            content: '';
            flex: 1;
            height: 2px;
            background: #1e3a8a;
        }
        .tagline {
            text-align: center;
            color: #64748b;
            font-style: italic;
            font-size: 12px;
            margin: 8px 0;
            font-weight: 500;
        }
        .separator {
            border-top: 1px solid #94a3b8;
            margin: 0;
        }
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .coupon-title {
            color: #1e3a8a;
            font-weight: 800;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .discount-value {
            color: #10b981;
            font-weight: 800;
            font-size: 52px;
            line-height: 1;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .discount-value .symbol {
            font-size: 30px;
            margin-bottom: 6px;
        }
        .discount-value .label {
            color: #1e3a8a;
            font-size: 28px;
            margin-left: 10px;
        }
        .code-box {
            border: 1px solid #94a3b8;
            padding: 5px 30px;
            font-size: 16px;
            font-weight: 600;
            color: #334155;
            margin-top: 10px;
        }
        .code-box b {
            color: #1e3a8a;
            font-weight: 800;
            margin-left: 8px;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 5px;
        }
        .footer-left .url {
            color: #1e3a8a;
            font-weight: 800;
            font-size: 14px;
            border-bottom: 2px solid #94a3b8;
            display: inline-block;
            padding-bottom: 1px;
            margin-bottom: 2px;
        }
        .footer-left .hint {
            color: #64748b;
            font-style: italic;
            font-size: 11px;
            font-weight: 500;
        }
        .qr-code {
            width: 70px;
            height: 70px;
            background: white;
            padding: 3px;
            border: 1px solid #e2e8f0;
        }
        
        @media print {
            body { background: white; }
            .page-container { margin: 0; padding: 0; }
            .coupon-container { box-shadow: none; border: 2px dashed #cbd5e1; page-break-inside: avoid; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="page-container">
        @foreach($coupons as $coupon)
        <div class="coupon-container">
            <!-- Header -->
            <div class="logo-section">
                <div class="logo-text">
                    <div class="top">UMUT MEDİKAL</div>
                    <div class="bottom">MARKET</div>
                </div>
            </div>

            <div class="tagline">Sağlık Ürünlerinde Güvenilir Alışverişin Adresi.</div>
            
            <hr class="separator">

            <!-- Body -->
            <div class="main-content">
                <div class="coupon-title">ÖZEL İNDİRİM KUPONU</div>
                <div class="discount-value">
                    @if($coupon->type === 'percent')
                        %{{ number_format($coupon->value, 0) }}
                    @else
                        <span class="symbol">₺</span>{{ number_format($coupon->value, 0) }}
                    @endif
                    <span class="label">İNDİRİM</span>
                </div>
                <div class="code-box">
                    KUPON KODU: <b>{{ $coupon->code }}</b>
                </div>
            </div>

            <hr class="separator" style="margin-bottom: 10px;">

            <!-- Footer -->
            <div class="footer">
                <div class="footer-left">
                    <div class="url">www.umutmedikalmarket.com</div>
                    <div class="hint">QR Kodu Tara & Hemen Ziyaret Et!</div>
                </div>
                <div class="qr-code">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=https://umutmedikalmarket.com" alt="QR Code" class="w-full h-full">
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="fixed bottom-8 no-print">
        <button onclick="window.print()" class="px-10 py-4 bg-slate-900 text-white rounded-full font-bold shadow-2xl hover:scale-105 transition-all flex items-center gap-3">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            KUPONU YAZDIR
        </button>
    </div>
</body>
</html>
