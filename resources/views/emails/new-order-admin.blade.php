<x-mail::message>
# Yeni Sipariş Alındı!

Web siteniz üzerinden yeni bir sipariş verildi. Detaylar aşağıdadır:

**Sipariş Bilgileri:**
- **Sipariş No:** #{{ $order->id }}
- **Müşteri:** {{ $order->customer_name }}
- **E-Posta:** {{ $order->customer_email }}
- **Telefon:** {{ $order->customer_phone }}
- **Toplam Tutar:** {{ number_format($order->total_price, 2, ',', '.') }} ₺
- **Ödeme Yöntemi:** {{ strtoupper($order->payment_method) }}

**Teslimat Adresi:**
{{ $order->address_info['address'] ?? '-' }}
{{ $order->address_info['district'] ?? '' }} / {{ $order->address_info['city'] ?? '' }}

<x-mail::button :url="route('admin.orders')">
Siparişi Görüntüle
</x-mail::button>

İyi çalışmalar,<br>
{{ config('app.name') }}
</x-mail::message>
