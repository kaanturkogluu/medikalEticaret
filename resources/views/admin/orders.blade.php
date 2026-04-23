@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedOrder: null,
    statusMap: {
        'awaiting': { label: 'Onay Bekliyor', color: 'bg-indigo-100 text-indigo-700' },
        'created': { label: 'Hazırlanıyor', color: 'bg-blue-100 text-blue-700' },
        'pending_payment': { label: 'Ödeme Bekleniyor', color: 'bg-amber-100 text-amber-700' },
        'pending': { label: 'Beklemede', color: 'bg-slate-100 text-slate-700' },
        'picking': { label: 'Toplanıyor', color: 'bg-amber-100 text-amber-700' },
        'invoiced': { label: 'Faturalandı', color: 'bg-cyan-100 text-cyan-700' },
        'shipped': { label: 'Kargoya Verildi', color: 'bg-orange-100 text-orange-700' },
        'atcollectionpoint': { label: 'Teslimat Noktasında', color: 'bg-purple-100 text-purple-700' },
        'cancelled': { label: 'İptal Edildi', color: 'bg-red-100 text-red-700' },
        'unpacked': { label: 'Paket Bölündü', color: 'bg-slate-100 text-slate-700' },
        'delivered': { label: 'Teslim Edildi', color: 'bg-emerald-100 text-emerald-700' },
        'undelivered': { label: 'Teslim Edilemedi', color: 'bg-rose-100 text-rose-700' },
        'returned': { label: 'İade Edildi', color: 'bg-gray-100 text-gray-700' },
        'undeliveredandreturned': { label: 'İade Edildi', color: 'bg-gray-100 text-gray-700' },
        'readytoship': { label: 'Kargoya Hazır', color: 'bg-indigo-100 text-indigo-700' }
    },
    formatDate(ms) {
        if (!ms) return '-';
        return new Intl.DateTimeFormat('tr-TR', { 
            day: '2-digit', month: '2-digit', year: 'numeric', 
            hour: '2-digit', minute: '2-digit' 
        }).format(new Date(ms));
    },
    getStatus(status) {
        const s = (status || '').toLowerCase();
        return this.statusMap[s] || { label: status, color: 'bg-slate-100 text-slate-600' };
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sipariş Yönetimi (Tüm Siparişler)</h2>
            <p class="text-sm text-slate-500 mt-1">Trendyol, Hepsiburada, N11 ve Web Sitenizden gelen tüm siparişleri buradan yönetebilirsiniz.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.orders') }}" method="GET" class="flex items-center gap-2">
                <select name="channel_id" onchange="this.form.submit()" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all shadow-sm">
                    <option value="">Tüm Pazaryerleri</option>
                    <option value="web" {{ request('channel_id') === 'web' ? 'selected' : '' }}>Web Siparişleri</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id }}" {{ request('channel_id') == $channel->id ? 'selected' : '' }}>
                            {{ $channel->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-sync text-brand-500 text-[10px]"></i> Filtrele
                </button>
            </form>
            <form action="{{ route('admin.orders.sync') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2 shadow-lg shadow-brand-500/20">
                    <i class="fas fa-sync-alt text-[10px]"></i> Pazaryeri Siparişlerini Çek
                </button>
            </form>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sipariş / Paket No</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Müşteri</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Tutar</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Durum</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Oluşturma</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                @php
                    $bgColor = $o->channel?->color ?? '#f8fafc';
                @endphp
                <tr :class="getStatus('{{ $o->order_status }}').color.split(' ')[0]" 
                    style="border-left: 6px solid {{ $bgColor }};" 
                    class="hover:brightness-95 transition-all group border-b border-white">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-slate-800 tracking-tighter">#{{ $o->external_order_id ?? $o->id }}</span>
                            <span class="text-[9px] font-bold text-brand-600 uppercase tracking-tighter opacity-70">
                                {{ $o->raw_marketplace_data['shipmentNumber'] ?? ( $o->channel_id ? 'Paket No Yok' : 'WEB SİPARİŞİ' ) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">{{ $o->customer_name }}</span>
                            <span class="text-[10px] text-slate-400 lowercase">{{ $o->customer_email }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-black text-slate-900 tabular-nums">{{ number_format($o->total_price, 2, ',', '.') }} ₺</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span :class="getStatus('{{ $o->order_status }}').color" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="getStatus('{{ $o->order_status }}').label"></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-bold text-slate-500">
                            {{ $o->order_date ? $o->order_date->format('d.m.Y H:i') : $o->created_at->format('d.m.Y H:i') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @php $o->load(['items.product', 'channel']); @endphp
                        <button @click="selectedOrder = {{ json_encode($o) }}" class="p-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-brand-500 hover:text-brand-600 transition-all opacity-0 group-hover:opacity-100">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Dynamic Order Details Modal -->
    <div x-show="selectedOrder" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all overflow-y-auto">
        <div @click.away="selectedOrder = null" class="bg-white rounded-[2.5rem] w-full max-w-4xl shadow-2xl relative animate-in fade-in zoom-in duration-200 flex flex-col max-h-[90vh]">
            <!-- Modal Header -->
            <div class="p-8 pb-4 flex items-center justify-between border-b border-slate-50">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-xl font-black">
                        <i class="fas fa-box"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800" x-text="'Sipariş Detayı: #' + (selectedOrder?.external_order_id || selectedOrder?.id)"></h3>
                        <div class="flex items-center gap-3 mt-1">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest" x-text="selectedOrder?.channel?.name || 'WEB SİTE'"></span>
                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                            <span :class="getStatus(selectedOrder?.order_status).color" class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest" x-text="getStatus(selectedOrder?.order_status).label"></span>
                        </div>
                    </div>
                </div>
                <button @click="selectedOrder = null" class="h-10 w-10 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-8 pt-6 space-y-8 custom-scrollbar">
                
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- General Info -->
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-info-circle text-brand-500"></i> Genel Bilgiler
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Sipariş Tarihi</p>
                                <p class="text-xs font-bold text-slate-800" x-text="selectedOrder?.raw_marketplace_data?.orderDate ? formatDate(selectedOrder.raw_marketplace_data.orderDate) : formatDate(new Date(selectedOrder?.created_at).getTime())"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Ödeme Yöntemi</p>
                                <p class="text-xs font-black text-brand-600 uppercase tracking-tighter" x-text="selectedOrder?.payment_method || (selectedOrder?.channel_id ? 'Pazaryeri' : '-')"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Müşteri E-Posta</p>
                                <p class="text-xs font-bold text-slate-800" x-text="selectedOrder?.customer_email || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Cargo Info -->
                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-truck text-brand-500"></i> Kargo Bilgileri
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Kargo Şirketi</p>
                                <p class="text-xs font-bold text-slate-800" x-text="selectedOrder?.raw_marketplace_data?.cargoProviderName || '-'"></p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Takip Numarası</p>
                                <div class="flex items-center gap-2">
                                    <p class="text-xs font-black text-slate-900 tracking-tighter tabular-nums" x-text="selectedOrder?.raw_marketplace_data?.cargoTrackingNumber || '-'"></p>
                                    <template x-if="selectedOrder?.raw_marketplace_data?.cargoTrackingNumber">
                                        <button class="text-[10px] text-brand-500 font-bold hover:underline">Sorgula</button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">Paket Durumu</p>
                                <p class="text-xs font-extrabold text-brand-600 uppercase tracking-tighter" x-text="selectedOrder?.raw_marketplace_data?.shipmentPackageStatus || '-'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 space-y-4">
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-brand-500"></i> Teslimat Adresi
                        </h4>
                        <div class="space-y-3">
                            <p class="text-xs font-bold text-slate-800 leading-relaxed" x-text="selectedOrder?.address_info?.address || selectedOrder?.raw_marketplace_data?.shipmentAddress?.fullAddress || selectedOrder?.raw_marketplace_data?.shippingAddress?.address"></p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter" x-text="(selectedOrder?.address_info?.district || selectedOrder?.raw_marketplace_data?.shipmentAddress?.district || selectedOrder?.raw_marketplace_data?.shippingAddress?.district || '-') + ' / ' + (selectedOrder?.address_info?.city || selectedOrder?.raw_marketplace_data?.shipmentAddress?.city || selectedOrder?.raw_marketplace_data?.shippingAddress?.city || '-')"></p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                        <h4 class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Sipariş İçeriği (Lines)</h4>
                        <span class="text-[10px] font-extrabold text-brand-600 bg-brand-50 px-2 py-0.5 rounded-lg" x-text="(selectedOrder?.raw_marketplace_data?.lines?.length || selectedOrder?.items?.length || 0) + ' Kalem Ürün'"></span>
                    </div>
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                                <th class="px-6 py-3">Ürün / SKU</th>
                                <th class="px-6 py-3 text-center">Adet</th>
                                <th class="px-6 py-3 text-right">Birim Fiyat</th>
                                <th class="px-6 py-3 text-right">İndirim</th>
                                <th class="px-6 py-3 text-right">KDV (%)</th>
                                <th class="px-6 py-3 text-right">Toplam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <!-- Marketplace Lines (Generic/Trendyol) -->
                            <template x-if="selectedOrder?.raw_marketplace_data?.lines">
                                <template x-for="item in selectedOrder?.raw_marketplace_data?.lines" :key="item.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-800 tracking-tight" x-text="item.productName || item.name"></span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-black tracking-tighter" x-text="item.sku || item.stockCode || item.sellerStockCode || '-'"></span>
                                                    <span class="text-[9px] text-slate-400" x-text="item.barcode ? 'Barkod: ' + item.barcode : ''"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums" x-text="item.quantity"></td>
                                        <td class="px-6 py-4 text-right text-xs font-bold text-slate-700 tabular-nums" x-text="item.price.toFixed(2) + ' ₺'"></td>
                                        <td class="px-6 py-4 text-right text-xs font-bold text-red-500 tabular-nums" x-text="(item.discount || 0).toFixed(2) + ' ₺'"></td>
                                        <td class="px-6 py-4 text-right text-[10px] font-bold text-slate-400" x-text="'%' + (item.vatRate || 0)"></td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-slate-900 tabular-nums" x-text="((item.price * item.quantity) - (item.discount || 0)).toFixed(2) + ' ₺'"></td>
                                    </tr>
                                </template>
                            </template>

                            <!-- PTT AVM Lines -->
                            <template x-if="selectedOrder?.raw_marketplace_data?.siparisUrunler">
                                <template x-for="item in selectedOrder?.raw_marketplace_data?.siparisUrunler" :key="item.lineItemId || item.urunId">
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-800 tracking-tight" x-text="item.urun"></span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-black tracking-tighter" x-text="item.urunBarkod || item.variantBarkod || '-'"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums" x-text="item.toplamIslemAdedi"></td>
                                        <td class="px-6 py-4 text-right text-xs font-bold text-slate-700 tabular-nums" x-text="(item.kdvDahilToplamTutar / item.toplamIslemAdedi).toFixed(2) + ' ₺'"></td>
                                        <td class="px-6 py-4 text-right text-xs font-bold text-red-500 tabular-nums" x-text="(item.indirimToplam || 0).toFixed(2) + ' ₺'"></td>
                                        <td class="px-6 py-4 text-right text-[10px] font-bold text-slate-400" x-text="'%' + (item.kdvOrani || 0)"></td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-slate-900 tabular-nums" x-text="item.kdvDahilToplamTutar.toFixed(2) + ' ₺'"></td>
                                    </tr>
                                </template>
                            </template>

                            <!-- Website Lines -->
                            <template x-if="!selectedOrder?.raw_marketplace_data?.lines && !selectedOrder?.raw_marketplace_data?.siparisUrunler && selectedOrder?.items">
                                <template x-for="item in selectedOrder?.items" :key="item.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-bold text-slate-800 tracking-tight" x-text="item.product?.name || 'Ürün'"></span>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-black tracking-tighter" x-text="item.product?.sku || '-'"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-black text-slate-900 tabular-nums" x-text="item.quantity"></td>
                                        <td class="px-6 py-4 text-right text-xs font-bold text-slate-700 tabular-nums" x-text="(item.price * 1).toFixed(2) + ' ₺'"></td>
                                        <td class="px-6 py-4 text-right text-xs font-bold text-red-500 tabular-nums" x-text="selectedOrder?.payment_method === 'eft' ? (item.price * item.quantity * 0.05).toFixed(2) + ' ₺' : '0.00 ₺'"></td>
                                        <td class="px-6 py-4 text-right text-[10px] font-bold text-slate-400">-</td>
                                        <td class="px-6 py-4 text-right text-sm font-black text-slate-900 tabular-nums" x-text="(selectedOrder?.payment_method === 'eft' ? (item.price * item.quantity * 0.95) : (item.price * item.quantity)).toFixed(2) + ' ₺'"></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- History Timeline -->
                <div class="space-y-4">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 px-1">
                        <i class="fas fa-history text-brand-500"></i> Paket Geçmişi (Package History)
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <template x-for="history in selectedOrder?.raw_marketplace_data?.packageHistories" :key="history.createdDate">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-white border border-slate-200 flex items-center justify-center text-brand-500">
                                        <i class="fas fa-clock text-xs"></i>
                                    </div>
                                    <span :class="getStatus(history.status).color" class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest" x-text="getStatus(history.status).label"></span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400" x-text="formatDate(history.createdDate)"></span>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-8 bg-slate-50 rounded-b-[2.5rem] border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam Brüt</p>
                        <p class="text-xl font-black text-slate-800 tabular-nums" x-text="((selectedOrder?.raw_marketplace_data?.grossAmount * 1 || (selectedOrder?.total_price * 1 + selectedOrder?.discount_amount * 1) || 0)).toFixed(2) + ' ₺'"></p>
                    </div>
                    <div class="h-10 w-px bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Toplam İndirim</p>
                        <p class="text-xl font-black text-red-500 tabular-nums" x-text="'- ' + (selectedOrder?.raw_marketplace_data?.totalDiscount || selectedOrder?.discount_amount || 0).toFixed(2) + ' ₺'"></p>
                    </div>
                    <div class="h-10 w-px bg-slate-200"></div>
                    <div>
                        <p class="text-[10px] font-black text-brand-600 uppercase tracking-widest mb-1">Genel Toplam (NET)</p>
                        <p class="text-2xl font-black text-brand-600 tabular-nums" x-text="(selectedOrder?.total_price * 1 || 0).toLocaleString('tr-TR', { minimumFractionDigits: 2 }) + ' ₺'"></p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <template x-if="selectedOrder?.order_status === 'Awaiting' || selectedOrder?.order_status === 'pending_payment'">
                        <form :action="'{{ route('admin.orders.approve', ':id') }}'.replace(':id', selectedOrder.id)" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-3 bg-emerald-600 text-white rounded-2xl text-xs font-black hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/30 uppercase tracking-widest">
                                Siparişi Onayla
                            </button>
                        </form>
                    </template>
                    <button class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-black text-slate-600 hover:bg-slate-100 transition-all uppercase tracking-widest">
                        Yazdır
                    </button>
                    <button class="px-6 py-3 bg-brand-600 text-white rounded-2xl text-xs font-black hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/30 uppercase tracking-widest">
                        Etiket Çıkar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
