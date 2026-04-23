<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şifre Sıfırlama</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; }
        .wrapper { max-width: 600px; margin: 20px auto; }
        .header { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 40px; text-align: center; border-radius: 24px 24px 0 0; }
        .header h1 { color: white; font-size: 24px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase; }
        .header p { color: rgba(255,255,255,0.6); font-size: 13px; margin-top: 6px; }
        .body { background: white; padding: 40px; border-radius: 0 0 24px 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .greeting { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
        .text { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 32px; }
        .btn-container { text-align: center; margin: 32px 0; }
        .btn { background: #f97316; color: white !important; text-decoration: none; padding: 16px 32px; border-radius: 12px; font-weight: 700; font-size: 14px; display: inline-block; text-transform: uppercase; letter-spacing: 1px; }
        .expire { color: #94a3b8; font-size: 12px; margin-top: 24px; text-align: center; }
        .footer { padding: 24px 40px; text-align: center; }
        .footer p { font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
            <p>Şifre Sıfırlama Talebi</p>
        </div>
        <div class="body">
            <p class="greeting">Merhaba {{ $user->name }},</p>
            <p class="text">
                Hesabınız için şifre sıfırlama talebinde bulundunuz. Aşağıdaki butona tıklayarak yeni şifrenizi oluşturabilirsiniz.
            </p>
            <div class="btn-container">
                <a href="{{ route('password.reset', ['token' => $token, 'email' => $user->email]) }}" class="btn">ŞİFREYİ SIFIRLA</a>
            </div>
            <p class="text">
                Bu bağlantı <strong>60 dakika</strong> süresince geçerlidir. Eğer bu talebi siz yapmadıysanız, herhangi bir işlem yapmanıza gerek yoktur.
            </p>
            <p class="expire">Bu e-posta otomatik olarak gönderilmiştir.</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }} — Tüm Hakları Saklıdır</p>
        </div>
    </div>
</body>
</html>
