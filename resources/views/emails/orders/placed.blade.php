<x-mail::message>
# Siparişiniz İçin Teşekkürler!

Merhaba {{ $order->customer_name }},

Siparişiniz başarıyla alındı ve hazırlık sürecine girmek üzere sıraya alındı. Sipariş detaylarınızı aşağıda bulabilirsiniz.

<x-mail::panel>
**Sipariş No:** #{{ $order->id }}  
**Tarih:** {{ $order->created_at->format('d.m.Y H:i') }}  
**Ödeme Yöntemi:** {{ strtoupper($order->payment_method) }}
</x-mail::panel>

@if($order->payment_method === 'eft')
## 🏦 Havale/EFT Bilgileri
Siparişinizin onaylanması için lütfen aşağıdaki hesaba ödemenizi gerçekleştiriniz.

**Banka:** {{ $bankDetails['bank_name'] }}  
**Hesap Sahibi:** {{ $bankDetails['bank_account_holder'] }}  
**IBAN:** `{{ $bankDetails['bank_iban'] }}`  
**Açıklama:** **Sipariş No: {{ $order->id }}**

*Lütfen açıklama kısmına sadece sipariş numaranızı yazınız.*
@endif

## 📦 Sipariş İçeriği
<x-mail::table>
| Ürün | Adet | Fiyat |
| :--- | :---: | :--- |
@foreach($order->items as $item)
| {{ $item->product->name ?? 'Ürün' }} | {{ $item->quantity }} | {{ number_format($item->price, 2, ',', '.') }} TL |
@endforeach
| **Toplam** | | **{{ number_format($order->total_price, 2, ',', '.') }} TL** |
</x-mail::table>

## 📍 Teslimat Adresi
{{ $order->address_info['city'] ?? '' }} / {{ $order->address_info['district'] ?? '' }}  
{{ $order->address_info['neighborhood'] ?? '' }}  
{{ $order->address_info['address'] ?? '' }}

<x-mail::button :url="route('user.orders')">
Siparişlerimi Görüntüle
</x-mail::button>

Herhangi bir sorunuz olursa bizimle iletişime geçmekten çekinmeyin.

Saygılarımızla,  
**{{ config('app.name') }}**
</x-mail::message>
