@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedOrder: null,
    statusMap: {
        'kargo_yapilmasi_bekleniyor': { label: 'Kargo Bekleniyor', color: 'bg-amber-100 text-amber-700' },
        'tamamlandi': { label: 'Tamamlandı', color: 'bg-emerald-100 text-emerald-700' },
        'iptal_edildi': { label: 'İptal Edildi', color: 'bg-red-100 text-red-700' },
        'hazirlaniyor': { label: 'Hazırlanıyor', color: 'bg-blue-100 text-blue-700' }
    },
    formatDate(dateStr) {
        if (!dateStr) return '-';
        try {
            return new Intl.DateTimeFormat('tr-TR', { 
                day: '2-digit', month: '2-digit', year: 'numeric', 
                hour: '2-digit', minute: '2-digit' 
            }).format(new Date(dateStr));
        } catch(e) { return dateStr; }
    },
    getStatus(status) {
        return this.statusMap[status] || { label: status, color: 'bg-slate-100 text-slate-600' };
    },
    calculateTotal(order) {
        if (!order || !order.siparisUrunler) return 0;
        return order.siparisUrunler.reduce((acc, item) => acc + (item.kdvDahilToplamTutar || 0), 0);
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="h-8 w-8 bg-amber-500 rounded-lg flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                    <i class="fas fa-envelope text-xs"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">PTT AVM Sipariş Yönetimi (Test)</h2>
            </div>
            <p class="text-sm text-slate-500">PTT AVM API'den gelen canlı sipariş verilerini buradan inceleyebilirsiniz.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left text-[10px]"></i> Tüm Siparişler
            </a>
            <button onclick="window.location.reload()" class="px-4 py-2 bg-amber-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-amber-500/20 hover:bg-amber-700 transition-colors flex items-center gap-2">
                <i class="fas fa-sync-alt text-[10px]"></i> Verileri Yenile
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Sipariş</p>
            <h3 class="text-2xl font-black text-slate-800">{{ count($orders) }}</h3>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sipariş No</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Müşteri</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Durum</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Tutar</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tarih</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <span class="text-xs font-black text-slate-800 tracking-tighter">#{{ $order['siparisNo'] ?? 'N/A' }}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-800">{{ ($order['musteriAdi'] ?? '') . ' ' . ($order['musteriSoyadi'] ?? '') }}</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $order['eposta'] ?? '' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php 
                            $status = $order['siparisUrunler'][0]['siparisDurumu'] ?? 'Unknown'; 
                        @endphp
                        <span :class="getStatus('{{ $status }}').color" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="getStatus('{{ $status }}').label"></span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        @php
                            $total = collect($order['siparisUrunler'] ?? [])->sum('kdvDahilToplamTutar');
                        @endphp
                        <span class="text-sm font-black text-slate-900 tabular-nums">{{ number_format($total, 2, ',', '.') }} ₺</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] font-bold text-slate-500" x-text="formatDate('{{ $order['islemTarihi'] ?? '' }}')"></span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button @click="selectedOrder = {{ json_encode($order) }}" class="p-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-amber-500 hover:text-amber-600 transition-all">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-box-open text-5xl text-slate-200 mb-4"></i>
                            <p class="text-sm font-bold text-slate-400">Herhangi bir sipariş verisi bulunamadı.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Details View -->
    <div x-show="selectedOrder" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.away="selectedOrder = null" class="bg-white rounded-[2.5rem] w-full max-w-5xl shadow-2xl relative flex flex-col max-h-[90vh]">
            <div class="p-8 pb-4 flex items-center justify-between border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 text-xl font-black">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800" x-text="'Sipariş Detayı: #' + (selectedOrder?.siparisNo || '')"></h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">PTT AVM</span>
                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                            <span :class="getStatus(selectedOrder?.siparisUrunler?.[0]?.siparisDurumu).color" class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest" x-text="getStatus(selectedOrder?.siparisUrunler?.[0]?.siparisDurumu).label"></span>
                        </div>
                    </div>
                </div>
                <button @click="selectedOrder = null" class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 pt-6 space-y-8">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Müşteri & Adres</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">AD SOYAD</p>
                                <p class="text-xs font-bold text-slate-800" x-text="(selectedOrder?.musteriAdi || '') + ' ' + (selectedOrder?.musteriSoyadi || '')"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">TESLİMAT ADRESİ</p>
                                <p class="text-xs font-bold text-slate-800 leading-relaxed" x-text="selectedOrder?.siparisAdresi"></p>
                                <p class="text-[10px] font-black text-brand-500 mt-1 uppercase" x-text="(selectedOrder?.siparisIlce || '') + ' / ' + (selectedOrder?.siparisIli || '')"></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Lojistik & Fatura</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">KARGO BARKOD</p>
                                <p class="text-xs font-black text-emerald-600 tracking-widest" x-text="selectedOrder?.kargoBarkod || 'Henüz Atanmadı'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">FATURA TİPİ</p>
                                <p class="text-xs font-bold text-slate-800 uppercase" x-text="selectedOrder?.faturaTip"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Lines -->
                <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-6 py-3">Ürün Adı / Barkod</th>
                                <th class="px-6 py-3 text-center">Adet</th>
                                <th class="px-6 py-3 text-right">Toplam (KDV Dahil)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-for="item in selectedOrder?.siparisUrunler" :key="item.lineItemId">
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-800" x-text="item.urun"></span>
                                            <span class="text-[10px] text-slate-400 font-bold" x-text="'Barkod: ' + (item.urunBarkod || '-')"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-slate-900" x-text="item.toplamIslemAdedi"></td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-700" x-text="item.kdvDahilToplamTutar.toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' ₺'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Raw Data View -->
                <div class="space-y-2">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Ham JSON Verisi</h4>
                    <pre class="p-6 bg-slate-900 text-amber-400 rounded-3xl text-[11px] font-mono overflow-x-auto custom-scrollbar shadow-inner" x-text="JSON.stringify(selectedOrder, null, 4)"></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
