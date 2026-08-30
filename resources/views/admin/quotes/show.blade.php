@extends('layouts.admin')

@section('title', 'Teklif Detayı - #' . $quote->quote_no)

@section('content')
<div class="space-y-6" x-data="{
    copySuccess: false,
    copyLink(text) {
        navigator.clipboard.writeText(text);
        this.copySuccess = true;
        setTimeout(() => this.copySuccess = false, 3000);
        window.notify('success', 'Ödeme linki panoya kopyalandı!');
    }
}">

    <!-- Top Breadcrumb & Actions Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <nav class="flex text-xs font-semibold text-slate-400 gap-2 mb-1">
                <a href="{{ route('admin.quotes.index') }}" class="hover:text-slate-800 transition-colors">Teklif Talepleri</a>
                <span>/</span>
                <span class="text-slate-700">#{{ $quote->quote_no }}</span>
            </nav>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span>Teklif Talebi #{{ $quote->quote_no }}</span>
                @php $badge = $quote->status_badge; @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-black text-xs {{ $badge['bg'] }} {{ $badge['text'] }}">
                    <i class="fas {{ $badge['icon'] }} text-[10px]"></i>
                    <span>{{ $badge['label'] }}</span>
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.quotes.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-colors inline-flex items-center gap-1.5">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Tüm Talepler</span>
            </a>
            
            <form action="{{ route('admin.quotes.destroy', $quote) }}" method="POST" onsubmit="return confirm('Bu teklif talebini silmek istediğinize emin misiniz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-xl text-xs transition-colors" title="Teklifi Sil">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Active Created Product & Payment Link Highlight (If Converted) -->
    @if($quote->custom_payment_link)
    <div class="bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 text-white rounded-3xl p-6 md:p-8 shadow-xl border border-emerald-500/30 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/20 text-emerald-300 rounded-full text-xs font-bold">
                    <i class="fas fa-check-circle"></i>
                    <span>Özel Sipariş Ürünü &amp; Ödeme Linki Aktif</span>
                </div>
                <h3 class="text-xl font-black text-white">Müşteri İçin Özel Satın Alma Linki Hazır</h3>
                <p class="text-xs text-slate-300">Bu linki kopyalayarak veya WhatsApp ile müşterinize gönderin. Müşteri linke girip kredi kartı ile doğrudan sipariş verebilir.</p>
            </div>

            <div class="text-right shrink-0">
                <p class="text-xs text-emerald-300 font-bold">Anlaşılan Fiyat</p>
                <p class="text-2xl font-black text-white">{{ number_format($quote->offered_total, 2, ',', '.') }} TL</p>
            </div>
        </div>

        <div class="p-3 bg-white/10 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-3 backdrop-blur-xs border border-white/10">
            <input type="text" readonly value="{{ $quote->custom_payment_link }}" class="w-full bg-transparent text-xs font-mono text-emerald-200 border-0 outline-none select-all">
            
            <div class="flex items-center gap-2 w-full sm:w-auto shrink-0">
                <button type="button" @click="copyLink('{{ $quote->custom_payment_link }}')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm w-full sm:w-auto">
                    <i class="fas fa-copy text-xs"></i>
                    <span x-text="copySuccess ? 'Kopyalandı!' : 'Linki Kopyala'"></span>
                </button>

                @php
                    $phoneClean = preg_replace('/[^0-9]/', '', $quote->customer_phone);
                    $waSendMsg = urlencode("Merhaba " . $quote->customer_name . " Bey/Hanım,\n#" . $quote->quote_no . " numaralı teklif talebiniz için anlaşılan özel fiyatlı sipariş ve ödeme linkiniz oluşturulmuştur:\n\n" . $quote->custom_payment_link . "\n\nLinke tıklayarak doğrudan kredi kartı veya havale ile siparişinizi tamamlayabilirsiniz.");
                @endphp
                <a href="https://wa.me/{{ $phoneClean }}?text={{ $waSendMsg }}" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm w-full sm:w-auto">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>WhatsApp'tan Gönder</span>
                </a>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Side: Requested Items & Offer Generation -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Items Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-boxes text-emerald-600"></i>
                        <span>Talep Edilen Ürünler ({{ $quote->items->count() }} Çeşit - {{ $quote->items->sum('quantity') }} Adet)</span>
                    </h2>
                    <span class="text-xs font-bold text-slate-500">Standart Liste Tutarı: <strong>{{ number_format($quote->estimated_total, 2, ',', '.') }} TL</strong></span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($quote->items as $item)
                    <div class="py-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            @if($item->product_image)
                            <div class="w-14 h-14 rounded-xl bg-slate-50 border border-slate-200 p-1 flex-shrink-0 flex items-center justify-center">
                                <img src="{{ $item->product_image }}" alt="{{ $item->product_name }}" class="max-h-full max-w-full object-contain">
                            </div>
                            @endif
                            <div class="space-y-1">
                                <h3 class="text-xs font-black text-slate-900">{{ $item->product_name }}</h3>
                                <p class="text-[11px] text-slate-400">
                                    Birim Liste Fiyatı: <span class="font-bold text-slate-600">{{ number_format($item->unit_price, 2, ',', '.') }} TL</span>
                                    @if($item->product_sku) • Kod: {{ $item->product_sku }} @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="px-3 py-1 bg-amber-50 text-amber-800 border border-amber-200/80 rounded-xl text-xs font-black">{{ $item->quantity }} Adet</span>
                            <p class="text-xs font-black text-slate-800 mt-1">{{ number_format($item->total_price, 2, ',', '.') }} TL</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Total Box -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500">Toplam Standart Katalog Fiyatı:</span>
                    <span class="text-lg font-black text-slate-900">{{ number_format($quote->estimated_total, 2, ',', '.') }} TL</span>
                </div>
            </div>

            <!-- Generate Custom Product & Payment Link Form -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-magic text-emerald-600"></i>
                            <span>Hızlı Özel Sipariş Ürünü &amp; Ödeme Linki Oluştur</span>
                        </h2>
                        <p class="text-[11px] text-slate-400 mt-0.5">Müşteri ile anlaştığınız özel indirimli fiyatı girip tek tıkla gizli bir satın alma linki oluşturun.</p>
                    </div>
                </div>

                <form action="{{ route('admin.quotes.generate-product', $quote) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Agreed Price -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">
                                Müşteriyle Anlaşılan Özel Fiyat (TL) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="number" step="0.01" min="1" name="price" required value="{{ old('price', $quote->offered_total ?? $quote->estimated_total) }}" placeholder="Örn: 8500.00" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-4 pr-12 text-sm font-black text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-xs text-slate-400">TL</span>
                            </div>
                        </div>

                        <!-- Custom Package Name -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">
                                Özel Ürün Başlığı (İsteğe Bağlı)
                            </label>
                            <input type="text" name="custom_name" value="{{ old('custom_name', 'Özel Teklif Siparişi - ' . $quote->customer_name . ' (#' . $quote->quote_no . ')') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-xs font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-bolt text-xs"></i>
                        <span>{{ $quote->custom_payment_link ? 'Özel Sipariş Ürününü ve Fiyatını Güncelle' : 'Özel Sipariş Ürünü & Satın Alma Linki Oluştur' }}</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Right Side: Customer Info & Status Management -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Customer Contact Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-address-card text-emerald-600"></i>
                    <span>Müşteri İletişim Kartı</span>
                </h3>

                <div class="space-y-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Müşteri Adı</p>
                        <p class="font-black text-slate-900 text-sm">{{ $quote->customer_name }}</p>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Telefon Numarası</p>
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 text-sm">{{ $quote->customer_phone }}</span>
                            <div class="flex gap-1.5">
                                <a href="tel:{{ $quote->customer_phone }}" class="px-2.5 py-1 bg-slate-200 hover:bg-slate-300 text-slate-800 rounded-lg text-xs font-bold transition-colors" title="Ara">
                                    <i class="fas fa-phone-alt"></i>
                                </a>
                                @php $cleanP = preg_replace('/[^0-9]/', '', $quote->customer_phone); @endphp
                                <a href="https://wa.me/{{ $cleanP }}" target="_blank" rel="noopener noreferrer" class="px-2.5 py-1 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-lg text-xs font-bold transition-colors" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    @if($quote->customer_email)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">E-Posta</p>
                        <a href="mailto:{{ $quote->customer_email }}" class="font-bold text-slate-800 hover:underline">{{ $quote->customer_email }}</a>
                    </div>
                    @endif

                    @if($quote->organization_name)
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Kurum / Dernek / Firma</p>
                        <p class="font-bold text-slate-800">{{ $quote->organization_name }}</p>
                    </div>
                    @endif

                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Talep Türü</p>
                        <p class="font-bold text-emerald-800">{{ $quote->type_label }}</p>
                    </div>

                    @if($quote->customer_note)
                    <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200/80 space-y-1">
                        <p class="text-[10px] font-bold text-amber-800 uppercase">Müşteri Notu</p>
                        <p class="text-xs text-amber-950 font-medium leading-relaxed">{{ $quote->customer_note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status & Internal Notes Form -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-tasks text-emerald-600"></i>
                    <span>Talep Durumu &amp; Yönetim</span>
                </h3>

                <form action="{{ route('admin.quotes.update-status', $quote) }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Durum</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                            <option value="pending" {{ $quote->status === 'pending' ? 'selected' : '' }}>⏳ Beklemede</option>
                            <option value="reviewed" {{ $quote->status === 'reviewed' ? 'selected' : '' }}>👁️ İncelendi</option>
                            <option value="offered" {{ $quote->status === 'offered' ? 'selected' : '' }}>🏷️ Fiyat Verildi</option>
                            <option value="converted" {{ $quote->status === 'converted' ? 'selected' : '' }}>⚡ Özel Ürün Hazırlandı</option>
                            <option value="completed" {{ $quote->status === 'completed' ? 'selected' : '' }}>✅ Tamamlandı / Satın Alındı</option>
                            <option value="rejected" {{ $quote->status === 'rejected' ? 'selected' : '' }}>❌ İptal Edildi</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Yönetici İç Notları</label>
                        <textarea name="admin_notes" rows="3" placeholder="Örn: Müşteri ile WhatsApp'tan görüşüldü, 20 koli için anlaşıldı." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs font-medium text-slate-800 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">{{ old('admin_notes', $quote->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs transition-colors">
                        Durumu &amp; Notları Kaydet
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
