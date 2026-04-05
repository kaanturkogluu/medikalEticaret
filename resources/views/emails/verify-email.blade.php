<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-posta Doğrulama</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; }
        .wrapper { max-width: 600px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 40px 40px 50px; text-align: center; }
        .header h1 { color: white; font-size: 24px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .header p { color: rgba(255,255,255,0.6); font-size: 13px; margin-top: 6px; }
        .body { background: white; padding: 40px; margin-top: -16px; border-radius: 0 0 24px 24px; }
        .greeting { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
        .text { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 32px; }
        .code-box { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); border-radius: 16px; padding: 32px; text-align: center; margin: 32px 0; }
        .code-label { color: rgba(255,255,255,0.8); font-size: 11px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 12px; }
        .code { color: white; font-size: 48px; font-weight: 900; letter-spacing: 12px; font-family: 'Courier New', monospace; }
        .expire { color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 12px; }
        .warning { background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-top: 24px; }
        .warning p { font-size: 12px; color: #92400e; font-weight: 600; }
        .footer { padding: 24px 40px; background: #f8fafc; border-radius: 0 0 24px 24px; text-align: center; }
        .footer p { font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>E-posta Doğrulama</p>
        </div>
        <div class="body">
            <p class="greeting">Merhaba {{ $user->name }},</p>
            <p class="text">
                Hesabınızı aktive etmek için aşağıdaki 6 haneli doğrulama kodunu kullanın.
                Bu kod <strong>30 dakika</strong> süresince geçerlidir.
            </p>
            <div class="code-box">
                <div class="code-label">Doğrulama Kodunuz</div>
                <div class="code">{{ $code }}</div>
                <div class="expire">30 dakika içinde kullanın</div>
            </div>
            <p class="text">
                Bu işlemi siz başlatmadıysanız, bu e-postayı dikkate almayınız.
                Hesabınız herhangi bir değişikliğe uğramayacaktır.
            </p>
            <div class="warning">
                <p>⚠️ Bu kodu kimseyle paylaşmayın. Müşteri hizmetlerimiz sizden asla doğrulama kodu istemez.</p>
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }} — Tüm Hakları Saklıdır</p>
        </div>
    </div>
</body>
</html>
