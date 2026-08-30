<x-mail::message>
# Teklif Talebiniz Başarıyla Alındı!

Merhaba **{{ $quote->customer_name }}**,

Medikal ürünler için oluşturduğunuz özel fiyat teklifi talebiniz tarafımıza ulaşmıştır. Uzman ekibimiz talebinizi inceleyip en kısa sürede telefon numaranız (**{{ $quote->customer_phone }}**) veya e-posta adresiniz üzerinden size özel indirimli fiyat teklifini iletecektir.

<x-mail::panel>
**Teklif Takip Numarası:** `{{ $quote->quote_no }}`  
**Talep Türü:** {{ $quote->type_label }}  
**Tarih:** {{ $quote->created_at->format('d.m.Y H:i') }}  
**Standart Liste Tutarı:** {{ number_format($quote->estimated_total, 2, ',', '.') }} TL
</x-mail::panel>

## 📋 Talep Edilen Ürünler
<x-mail::table>
| Ürün | Adet | Liste Fiyatı | Toplam |
| :--- | :---: | :--- | :--- |
@foreach($quote->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} Adet | {{ number_format($item->unit_price, 2, ',', '.') }} TL | {{ number_format($item->total_price, 2, ',', '.') }} TL |
@endforeach
</x-mail::table>

@if($quote->customer_note)
> **Talep Notunuz:**  
> {{ $quote->customer_note }}
@endif

<x-mail::button :url="route('quote.track', ['quote_no' => $quote->quote_no])">
Teklif Durumunu Canlı Takip Et
</x-mail::button>

Sorularınız veya acil talepleriniz için doğrudan WhatsApp hattımızdan bize ulaşabilirsiniz:  
📞 **0546 941 69 96**

Saygılarımızla,  
**{{ config('app.name') }} Ekibi**
</x-mail::message>
