<x-mail::message>
# Sayın {{ $order->customer_name }},

Alışverişinizde bizi tercih ettiğiniz için teşekkür ederiz. 

**#{{ $order->external_order_id ?? $order->id }}** numaralı siparişiniz, aşağıda belirtilen neden dolayısıyla iptal edilmiştir.

@if($order->cancel_reason)
<div style="background-color: #f8efef; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 20px;">
    <strong>İptal Nedeni:</strong> {{ $order->cancel_reason }}
</div>
@endif

Siparişiniz sırasında kullanmış olduğunuz indirim kuponunuz ve/veya Med Puanlarınız hesabınıza iade edilmiştir. 
Eğer siparişinize ait ödeme işlemi gerçekleştirilmiş ise (Havale/EFT vb.), iade işleminiz muhasebe birimimiz tarafından en kısa sürede işleme alınacaktır. (Kredi kartı ile ödeme aşaması tamamlanmadan iptal edilen siparişler için iade işlemi söz konusu değildir.)

Anlayışınız için teşekkür eder, sağlıklı günler dileriz.

Saygılarımızla,<br>
**{{ config('app.name') }} Müşteri Hizmetleri**
</x-mail::message>
