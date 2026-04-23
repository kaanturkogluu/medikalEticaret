@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedOrder: null,
    statusMap: {
        'Created': { label: 'Yeni Sipariş', color: 'bg-emerald-100 text-emerald-700' },
        'Picking': { label: 'Hazırlanıyor', color: 'bg-amber-100 text-amber-700' },
        'Shipped': { label: 'Kargoya Verildi', color: 'bg-blue-100 text-blue-700' },
        'Delivered': { label: 'Teslim Edildi', color: 'bg-slate-100 text-slate-700' },
        'Cancelled': { label: 'İptal Edildi', color: 'bg-red-100 text-red-700' },
        'UnPaid': { label: 'Ödeme Bekliyor', color: 'bg-orange-100 text-orange-700' }
    },
    formatDate(ms) {
        if (!ms) return '-';
        return new Intl.DateTimeFormat('tr-TR', { 
            day: '2-digit', month: '2-digit', year: 'numeric', 
            hour: '2-digit', minute: '2-digit' 
        }).format(new Date(ms));
    },
    getStatus(status) {
        return this.statusMap[status] || { label: status, color: 'bg-slate-100 text-slate-600' };
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <div class="h-8 w-8 bg-[#F27A1A] rounded-lg flex items-center justify-center text-white shadow-lg shadow-orange-500/20">
                    <i class="fas fa-shopping-bag text-xs"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Trendyol Sipariş Yönetimi (Ham Veri)</h2>
            </div>
            <p class="text-sm text-slate-500">Trendyol API'den gelen canlı sipariş verilerini buradan inceleyebilirsiniz.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.orders') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left text-[10px]"></i> Tüm Siparişler
            </a>
            <button onclick="window.location.reload()" class="px-4 py-2 bg-[#F27A1A] text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-500/20 hover:bg-orange-700 transition-colors flex items-center gap-2">
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
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Yeni (Created)</p>
            <h3 class="text-2xl font-black text-emerald-600">{{ count($orders->where('status', 'Created')) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Hazırlanıyor (Picking)</p>
            <h3 class="text-2xl font-black text-amber-500">{{ count($orders->where('status', 'Picking')) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Kargolananlar</p>
            <h3 class="text-2xl font-black text-blue-600">{{ count($orders->where('status', 'Shipped')) }}</h3>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-100 rounded-[2.5rem] shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sipariş / Müşteri</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sipariş No</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Durum</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Tutar</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sipariş Tarihi</th>
                    <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-slate-800 tracking-tighter">{{ ($order['customerFirstName'] ?? '') . ' ' . ($order['customerLastName'] ?? '') ?: 'Müşteri Bilirtilmemiş' }}</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ $order['customerEmail'] ?? '' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-xs font-bold text-slate-600 tabular-nums">#{{ $order['orderNumber'] ?? 'N/A' }}</span>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php $status = $order['status'] ?? 'Unknown'; @endphp
                        <span :class="getStatus('{{ $status }}').color" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="getStatus('{{ $status }}').label"></span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <span class="text-sm font-black text-slate-900 tabular-nums">{{ number_format($order['totalPrice'] ?? 0, 2, ',', '.') }} ₺</span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="text-[10px] font-bold text-slate-500">
                            {{ isset($order['orderDate']) ? date('d.m.Y H:i', $order['orderDate'] / 1000) : 'N/A' }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button @click="selectedOrder = {{ json_encode($order) }}" class="p-2 bg-white border border-slate-200 rounded-xl shadow-sm hover:border-orange-500 hover:text-orange-600 transition-all">
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

    <!-- Details View (Modal Style or Raw) -->
    <div x-show="selectedOrder" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div @click.away="selectedOrder = null" class="bg-white rounded-[2.5rem] w-full max-w-4xl shadow-2xl relative flex flex-col max-h-[90vh]">
            <div class="p-8 pb-4 flex items-center justify-between border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 text-xl font-black">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800" x-text="'Sipariş Detayı: #' + (selectedOrder?.orderNumber || '')"></h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">TRENDYOL PAZARYERİ</span>
                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                            <span :class="getStatus(selectedOrder?.status).color" class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest" x-text="getStatus(selectedOrder?.status).label"></span>
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
                                <p class="text-xs font-bold text-slate-800" x-text="(selectedOrder?.customerFirstName || '') + ' ' + (selectedOrder?.customerLastName || '')"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">TESLİMAT ADRESİ</p>
                                <p class="text-xs font-bold text-slate-800 leading-relaxed" x-text="selectedOrder?.shippingAddress?.address1 + ' ' + (selectedOrder?.shippingAddress?.address2 || '')"></p>
                                <p class="text-[10px] font-black text-orange-500 mt-1 uppercase" x-text="selectedOrder?.shippingAddress?.district + ' / ' + selectedOrder?.shippingAddress?.city"></p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Lojistik Bilgileri</h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">KARGO FİRMASI</p>
                                <p class="text-xs font-bold text-slate-800" x-text="selectedOrder?.cargoProviderName || 'Belirtilmemiş'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400">TAKİP NUMARASI</p>
                                <p class="text-xs font-black text-orange-600 tracking-widest" x-text="selectedOrder?.cargoTrackingNumber || 'Henüz Atanmadı'"></p>
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
                                <th class="px-6 py-3 text-right">Birim Fiyat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <template x-for="item in selectedOrder?.lines" :key="item.id">
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-800" x-text="item.productName"></span>
                                            <span class="text-[10px] text-slate-400 font-bold" x-text="'Barkod: ' + (item.barcode || '-')"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-black text-slate-900" x-text="item.quantity"></td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-700" x-text="item.price.toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' ₺'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Raw Data View -->
                <div class="space-y-2">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-1">Ham JSON Verisi</h4>
                    <pre class="p-6 bg-slate-900 text-orange-400 rounded-3xl text-[11px] font-mono overflow-x-auto custom-scrollbar shadow-inner" x-text="JSON.stringify(selectedOrder, null, 4)"></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
