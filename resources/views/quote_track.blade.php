@extends('layouts.app')

@section('title', 'Teklif Sorgulama & Durum Takibi - ' . config('app.name'))

@section('content')
<div class="bg-slate-50/50 min-h-screen py-10">
    <div class="ty-container max-w-5xl space-y-8">
        
        <!-- Breadcrumb & Header -->
        <div>
            <nav class="flex text-xs font-semibold text-slate-400 gap-2 mb-3">
                <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Ana Sayfa</a>
                <span>/</span>
                <span class="text-slate-700">Teklif Sorgulama</span>
            </nav>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs border border-amber-200/60">
                            <i class="fas fa-search-dollar"></i>
                        </div>
                        <span>Teklif Sorgulama &amp; Durum Takibi</span>
                    </h1>
                    <p class="text-xs md:text-sm text-slate-500 mt-1">Toplu alım veya bağış için oluşturduğunuz teklif talebinizin güncel durumunu ve özel ödeme linkini sorgulayın.</p>
                </div>

                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-800 text-xs font-bold px-4 py-2 rounded-xl border border-emerald-200/60 self-start md:self-auto">
                    <i class="fas fa-headset text-emerald-600"></i>
                    <span>Teklif Destek: <a href="tel:05469416996" class="underline hover:text-emerald-950">0546 941 69 96</a></span>
                </div>
            </div>
        </div>

        <!-- Search Bar Card -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-xs border border-slate-200/80 space-y-4">
            <form action="{{ route('quote.track') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" 
                           name="quote_no" 
                           required 
                           value="{{ old('quote_no', $searchedNo) }}" 
                           placeholder="Teklif Takip Numaranızı Giriniz (Örn: TK-2026-XXXX)" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-sm font-black text-slate-900 focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 outline-none transition-all uppercase tracking-wider">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                </div>
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-2xl font-black text-sm uppercase tracking-wider shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-search text-xs"></i>
                    <span>Teklifimi Sorgula</span>
                </button>
            </form>

            @if($errorMessage)
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs font-bold flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-base text-rose-500 shrink-0"></i>
                <span>{{ $errorMessage }}</span>
            </div>
            @endif
        </div>

        <!-- Results View -->
        @if($quote)
        <div class="space-y-6">
            
            <!-- Status Timeline Stepper -->
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-xs border border-slate-200/80">
                <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Teklif Durum Süreci</h2>
                
                @php
                    $statuses = [
                        'pending'   => ['label' => 'Talep Alındı', 'desc' => 'Talebiniz ekibimize ulaştı.'],
                        'reviewed'  => ['label' => 'İnceleniyor', 'desc' => 'Stok ve indirim oranları hesaplanıyor.'],
                        'offered'   => ['label' => 'Fiyat Belirlendi', 'desc' => 'Size özel indirimli fiyat hazırlandı.'],
                        'converted' => ['label' => 'Ödeme Linki Aktif', 'desc' => 'Özel satın alma linkiniz oluşturuldu.'],
                        'completed' => ['label' => 'Tamamlandı', 'desc' => 'Siparişiniz başarıyla alındı.'],
                    ];
                    $currentIdx = array_search($quote->status, array_keys($statuses));
                    if ($currentIdx === false) $currentIdx = 0;
                    if ($quote->status === 'rejected') $currentIdx = -1;
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-5 gap-4 relative">
                    @foreach($statuses as $key => $st)
                    @php 
                        $idx = array_search($key, array_keys($statuses));
                        $isPast = $currentIdx >= $idx;
                        $isCurrent = $currentIdx === $idx;
                    @endphp
                    <div class="flex flex-col items-center text-center space-y-2 p-3 rounded-2xl {{ $isCurrent ? 'bg-emerald-50/70 border border-emerald-200' : '' }}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-black transition-all {{ $isPast ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-100 text-slate-400' }}">
                            @if($isPast)
                                <i class="fas fa-check"></i>
                            @else
                                <span>{{ $idx + 1 }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black {{ $isPast ? 'text-slate-900' : 'text-slate-400' }}">{{ $st['label'] }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 leading-tight">{{ $st['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Converted / Direct Payment Callout -->
            @if($quote->custom_payment_link)
            <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl space-y-4 border border-emerald-500/30">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-bold">
                            <i class="fas fa-sparkles"></i>
                            <span>Özel Fiyatınız Onaylandı</span>
                        </div>
                        <h3 class="text-xl font-black text-white">Teklifiniz İçin Özel Sipariş Linki Hazır!</h3>
                        <p class="text-xs text-slate-300">Anlaşılan özel indirimli fiyattan kredi kartı ile güvenle siparişinizi verebilirsiniz.</p>
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

            <!-- Quote Summary Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Details -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-user-circle text-emerald-600"></i>
                        <span>Talep Eden Bilgileri</span>
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Takip No:</span>
                            <span class="font-black text-slate-900">{{ $quote->quote_no }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Ad Soyad:</span>
                            <span class="font-bold text-slate-800">{{ $quote->customer_name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Telefon:</span>
                            <span class="font-bold text-slate-800">{{ $quote->customer_phone }}</span>
                        </div>
                        @if($quote->organization_name)
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Kurum / Firma:</span>
                            <span class="font-bold text-slate-800">{{ $quote->organization_name }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-1">
                            <span class="text-slate-400 font-medium">Talep Türü:</span>
                            <span class="font-bold text-emerald-700">{{ $quote->type_label }}</span>
                        </div>
                    </div>
                </div>

                <!-- Status & Pricing Info -->
                <div class="p-6 bg-white rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                        <span>Talep Durumu &amp; Fiyat</span>
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Talep Tarihi:</span>
                            <span class="font-bold text-slate-800">{{ $quote->created_at->translatedFormat('d F Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Güncel Durum:</span>
                            @php $badge = $quote->status_badge; @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-black text-[11px] {{ $badge['bg'] }} {{ $badge['text'] }}">
                                <i class="fas {{ $badge['icon'] }} text-[9px]"></i>
                                <span>{{ $badge['label'] }}</span>
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-400 font-medium">Liste Fiyat Toplamı:</span>
                            <span class="font-bold text-slate-800">{{ number_format($quote->estimated_total, 2, ',', '.') }} TL</span>
                        </div>
                        @if($quote->offered_total)
                        <div class="flex justify-between py-1">
                            <span class="text-emerald-700 font-black">Özel İndirimli Fiyat:</span>
                            <span class="font-black text-emerald-700 text-sm">{{ number_format($quote->offered_total, 2, ',', '.') }} TL</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-3">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Talep Edilen Ürünler</h3>
                <div class="divide-y divide-slate-100">
                    @foreach($quote->items as $item)
                    <div class="py-3.5 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            @if($item->product_image)
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 p-1 flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="max-h-full max-w-full object-contain">
                            </div>
                            @endif
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $item->product_name }}</h4>
                                <p class="text-[11px] text-slate-400">Birim Liste: {{ number_format($item->unit_price, 2, ',', '.') }} TL</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-3 py-1 bg-slate-100 rounded-xl text-xs font-black text-slate-800">{{ $item->quantity }} Adet</span>
                            <p class="text-xs font-bold text-slate-700 mt-1">{{ number_format($item->total_price, 2, ',', '.') }} TL</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Direct Support Help Buttons -->
            @php
                $waMsg = urlencode("Merhaba umutMed, #" . $quote->quote_no . " numaralı teklif talebim hakkında bilgi almak istiyorum.");
                $waPhone = preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('contact_whatsapp', '905469416996'));
            @endphp
            <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="space-y-0.5 text-center sm:text-left">
                    <h4 class="text-sm font-black text-emerald-950">Fiyat Teklifinizle İlgili Danışmak İster Misiniz?</h4>
                    <p class="text-xs text-emerald-800">Teklif danışmanımız ile WhatsApp üzerinden anında görüşebilirsiniz.</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-5 py-3 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-2xl font-bold text-xs shadow-md transition-all">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>WhatsApp Teklif Hattı</span>
                    </a>
                </div>
            </div>

        </div>
        @elseif(!$errorMessage)
        <!-- Initial Guidance Card -->
        <div class="bg-white rounded-3xl p-8 md:p-12 text-center shadow-xs border border-slate-200/80 max-w-xl mx-auto space-y-4">
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h3 class="text-base font-black text-slate-900">Teklif Takip Numaranızı Giriniz</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                Talebinizi oluşturduğunuzda size iletilen <strong>TK-2026-XXXX</strong> formatındaki takip numarasını yukarıdaki alana girerek teklifinizin durumunu öğrenebilirsiniz.
            </p>
        </div>
        @endif

    </div>
</div>
@endsection
