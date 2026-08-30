<x-mail::message>
# Teklif Talebinizin Durumu Güncellendi

Merhaba **{{ $quote->customer_name }}**,

**#{{ $quote->quote_no }}** numaralı teklif talebiniz hakkında yeni bir güncelleme bulunmaktadır.

<x-mail::panel>
**Teklif Takip Numarası:** `{{ $quote->quote_no }}`  
**Güncel Durum:** {{ $quote->status_badge['label'] }}  
@if($quote->offered_total)
**Özel Teklif Tutarı:** **{{ number_format($quote->offered_total, 2, ',', '.') }} TL** (KDV Dahil)  
@endif
</x-mail::panel>

@if($quote->custom_payment_link)
## 🎉 Özel Sipariş Ürününüz & Ödeme Linkiniz Hazır!

Talebiniz için anlaşılan indirimli fiyat tanımlanmıştır. Aşağıdaki bağlantıya tıklayarak doğrudan kredi kartı veya havale ile siparişinizi tamamlayabilirsiniz:

<x-mail::button :url="$quote->custom_payment_link" color="success">
Özel Fiyatla Hemen Satın Al
</x-mail::button>
@else
<x-mail::button :url="route('quote.track', ['quote_no' => $quote->quote_no])">
Teklif Detayını İncele
</x-mail::button>
@endif

@if($quote->admin_notes)
> **Yönetici Notu:**  
> {{ $quote->admin_notes }}
@endif

Sorularınız veya destek için doğrudan bizimle iletişime geçebilirsiniz:  
📞 **0546 941 69 96**

Saygılarımızla,  
**{{ config('app.name') }} Ekibi**
</x-mail::message>
