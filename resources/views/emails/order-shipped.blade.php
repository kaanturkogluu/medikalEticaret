<x-mail::message>
# Merhaba {{ $order->customer_name }},

Siparişiniz kargoya verildi! Paketiniz yola çıktı ve en kısa sürede size ulaşacaktır.

**Kargo Detayları:**
- **Kargo Firması:** {{ $order->shippingCompany->name }}
- **Takip Numarası:** {{ $order->tracking_code }}

<x-mail::button :url="$order->shippingCompany->getTrackingLink($order->tracking_code)">
Kargomu Takip Et
</x-mail::button>

Sipariş detaylarınızı incelemek için web sitemizi ziyaret edebilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
