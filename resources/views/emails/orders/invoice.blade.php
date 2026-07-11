<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sipariş Faturanız</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; color: #333; line-height: 1.6;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="background-color: #f97316; padding: 20px; text-align: center;">
            <h2 style="color: #ffffff; margin: 0;">Faturanız Hazır</h2>
        </div>
        
        <div style="padding: 30px;">
            <p>Sayın <strong>{{ $order->customer_name }}</strong>,</p>
            <p><strong>#{{ $order->external_order_id ?? $order->id }}</strong> numaralı siparişinize ait e-fatura belgeniz oluşturulmuş olup bu e-postanın ekinde tarafınıza sunulmuştur.</p>
            
            <div style="background-color: #fff7ed; border-left: 4px solid #f97316; padding: 15px; margin: 25px 0;">
                <p style="margin: 0;"><strong>Sipariş No:</strong> #{{ $order->external_order_id ?? $order->id }}</p>
                <p style="margin: 5px 0 0 0;"><strong>Sipariş Tarihi:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            </div>
            
            <p>Faturanızı görüntülemek için ekteki PDF dosyasını indirebilirsiniz.</p>
            <p>Bizi tercih ettiğiniz için teşekkür ederiz.</p>
        </div>
        
        <div style="background-color: #f1f5f9; padding: 20px; text-align: center; font-size: 12px; color: #64748b;">
            <p style="margin: 0;">Bu otomatik bir bilgilendirme mesajıdır, lütfen cevaplamayınız.</p>
        </div>
    </div>
</body>
</html>
