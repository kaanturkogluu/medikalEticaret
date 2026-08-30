@extends('layouts.app')

@section('title', 'Teklif Talebiniz Alındı - #' . $quote->quote_no)

@section('content')
<div class="bg-slate-50/50 min-h-screen py-12">
    <div class="ty-container max-w-4xl">
        
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-xs border border-slate-200/80 space-y-8">
            
            <!-- Top Success Badge & Title -->
            <div class="text-center space-y-3">
                <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto shadow-sm border border-emerald-100 text-3xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 rounded-full text-xs font-black uppercase tracking-wider">
                    <span>Teklif Takip No: {{ $quote->quote_no }}</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Teklif Talebiniz Başarıyla Alındı!</h1>
                <p class="text-sm text-slate-500 max-w-lg mx-auto">
                    Talebiniz ekibimize ulaşmıştır. En kısa sürede ürünleriniz incelenerek <strong>{{ $quote->customer_phone }}</strong> numaranız üzerinden size özel indirimli fiyat teklifi iletilecektir.
                </p>
            </div>

            <!-- Custom Product / Direct Checkout Available Banner -->
            @if($quote->custom_payment_link)
            <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl space-y-4 border border-emerald-500/30">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-bold">
                            <i class="fas fa-sparkles"></i>
                            <span>Özel Fiyatınız Onaylandı</span>
                        </div>
                        <h3 class="text-xl font-black text-white">Teklifiniz İçin Özel Sipariş Linki Hazır!</h3>
                        <p class="text-xs text-slate-300">Anlaşılan özel indirimli fiyattan sepetinize ekleyip kartla veya havale ile siparişinizi tamamlayabilirsiniz.</p>
                    </div>

                    <div class="text-right sm:self-center">
                        <p class="text-xs text-emerald-300 font-medium">Özel Teklif Tutarı</p>
                        <p class="text-2xl font-black text-white">{{ number_format($quote->offered_total, 2, ',', '.') }} TL</p>
                    </div>
                </div>

                <div class="pt-2">
                    <a href="{{ $quote->custom_payment_link }}" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-sm rounded-2xl shadow-lg shadow-emerald-500/30 transition-all transform hover:scale-102 active:scale-98 w-full sm:w-auto">
                        <span>Hemen Özel Fiyatla Satın Al</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            @endif

            <!-- Request Details Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Details -->
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-circle text-emerald-600"></i>
                        <span>Talep Eden Bilgileri</span>
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-400 font-medium">Ad Soyad:</span>
                            <span class="font-bold text-slate-800">{{ $quote->customer_name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-400 font-medium">Telefon:</span>
                            <span class="font-bold text-slate-800">{{ $quote->customer_phone }}</span>
                        </div>
                        @if($quote->customer_email)
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-400 font-medium">E-Posta:</span>
                            <span class="font-bold text-slate-800">{{ $quote->customer_email }}</span>
                        </div>
                        @endif
                        @if($quote->organization_name)
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-400 font-medium">Kurum / Dernek:</span>
                            <span class="font-bold text-slate-800">{{ $quote->organization_name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-1">
                            <span class="text-slate-400 font-medium">Talep Türü:</span>
                            <span class="font-bold text-emerald-700">{{ $quote->type_label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status & Timing -->
                <div class="p-6 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                        <span>Talep Durumu &amp; Takip</span>
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-400 font-medium">Talep Tarihi:</span>
                            <span class="font-bold text-slate-800">{{ $quote->created_at->translatedFormat('d F Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-400 font-medium">Güncel Durum:</span>
                            @php $badge = $quote->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-black text-[11px] {{ $badge['bg'] }} {{ $badge['text'] }}">
                                <i class="fas {{ $badge['icon'] }} text-[9px]"></i>
                                <span>{{ $badge['label'] }}</span>
                            </span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-400 font-medium">Standart Liste Tutarı:</span>
                            <span class="font-bold text-slate-800">{{ number_format($quote->estimated_total, 2, ',', '.') }} TL</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itemized Products in Quote -->
            <div class="space-y-3">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Talep Edilen Ürünler</h3>
                <div class="bg-slate-50 rounded-2xl border border-slate-200/80 overflow-hidden">
                    <div class="divide-y divide-slate-200/60">
                        @foreach($quote->items as $item)
                        <div class="p-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @if($item->product_image)
                                <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 p-1 flex-shrink-0 flex items-center justify-center">
                                    <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="max-h-full max-w-full object-contain">
                                </div>
                                @endif
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $item->product_name }}</h4>
                                    <p class="text-[11px] text-slate-400">Birim: {{ number_format($item->unit_price, 2, ',', '.') }} TL</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="px-3 py-1 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-800">{{ $item->quantity }} Adet</span>
                                <p class="text-xs font-bold text-slate-700 mt-1">{{ number_format($item->total_price, 2, ',', '.') }} TL</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Direct Assistance Quick Buttons -->
            @php
                $waMsg = urlencode("Merhaba umutMed, #" . $quote->quote_no . " numaralı teklif talebim hakkında bilgi almak istiyorum.");
                $waPhone = preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('contact_whatsapp', '905469416996'));
            @endphp
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
                    <i class="fas fa-arrow-left text-[10px]"></i>
                    <span>Alışverişe Devam Et</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-3 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-2xl font-bold text-xs shadow-md transition-all">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>WhatsApp'tan Hemen Yazın</span>
                    </a>

                    <a href="tel:05469416996" class="inline-flex items-center gap-2 px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-xs shadow-md transition-all">
                        <i class="fas fa-phone-alt text-xs"></i>
                        <span>0546 941 69 96</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
