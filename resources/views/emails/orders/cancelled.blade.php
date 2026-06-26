<x-mail::message>
# Merhaba {{ $order->customer_name }},

**#{{ $order->external_order_id ?? $order->id }}** numaralı siparişiniz iptal edilmiştir.

@if($order->cancel_reason)
**İptal Nedeni:** {{ $order->cancel_reason }}
@endif

Varsa kullandığınız indirim kuponunuz yeniden aktif edilmiş ve harcadığınız Med Puanlarınız hesabınıza iade edilmiştir.
Eğer siparişiniz için önceden bir ödeme yaptıysanız, iadeniz en kısa sürede işleme alınacaktır. (Kredi kartı ile ödeme yapılmadan önce iptal edildiyse dikkate almayınız.)

Bizi tercih ettiğiniz için teşekkür ederiz, sağlıklı günler dileriz.

Saygılarımızla,<br>
{{ config('app.name') }}
</x-mail::message>
