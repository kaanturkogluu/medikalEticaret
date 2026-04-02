@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedOrder: null,
    orders: [
        { id: '1292831', marketplace: 'Trendyol', customer: 'Ahmet Yılmaz', total: 1245.90, items: [ { name: 'Deri Ceket Lüks', price: 1245.90, quantity: 1 } ], address: 'Beşiktaş/İstanbul', status: 'completed', createdAt: '2026-04-02 10:15:02', timeline: [{action: 'Sipariş Oluşturuldu', date: '2026-04-02 10:15:02'}, {action: 'Sipariş Onaylandı', date: '2026-04-02 10:20:00'}] },
        { id: 'HB-99120', marketplace: 'Hepsiburada', customer: 'Mehmet Ali Son', total: 320.50, items: [ { name: 'Mavi Kot Pantolon', price: 160.25, quantity: 2 } ], address: 'Nilüfer/Bursa', status: 'pending', createdAt: '2026-04-02 15:42:01', timeline: [{action: 'Sipariş Bekliyor', date: '2026-04-02 15:42:01'}] },
        { id: 'N11-3042', marketplace: 'N11', customer: 'Caner Koç', total: 450.00, items: [ { name: 'Beyaz Sneaker', price: 450.00, quantity: 1 } ], address: 'Merkez/Ankara', status: 'created', createdAt: '2026-04-01 22:10:00', timeline: [{action: 'Sipariş Alındı', date: '2026-04-01 22:10:00'}] }
    ],
    viewDetail(order) {
        this.selectedOrder = order;
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sipariş Yönetimi</h2>
            <p class="text-sm text-slate-500 mt-1">Pazaryerlerinden gelen tüm siparişleri tek merkezden takip edin.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="notify('success', 'Siparişler Güncellendi')" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fas fa-sync text-indigo-500 text-[10px]"></i> Siparişleri Çek
            </button>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Sipariş No</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Pazaryeri</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Müşteri</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Toplam</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Durum</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Tarih</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="o in orders" :key="o.id">
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-bold text-slate-900 tracking-tighter" x-text="'#' + o.id"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold uppercase" x-text="o.marketplace"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-sm text-slate-600" x-text="o.customer"></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-slate-900 tabular-nums tracking-tighter" x-text="o.total.toFixed(2) + ' ₺'"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="{
                                    'bg-emerald-100 text-emerald-600': o.status === 'completed',
                                    'bg-amber-100 text-amber-600': o.status === 'pending',
                                    'bg-blue-100 text-blue-600': o.status === 'created'
                                }" class="px-3 py-1 rounded-full text-[10px] font-bold uppercase" x-text="o.status"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400 font-medium tracking-tight" x-text="o.createdAt"></td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="viewDetail(o)" class="p-2 bg-white border border-slate-200 rounded-lg hover:border-brand-500 hover:text-brand-600 transition-all shadow-sm">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <button class="p-2 bg-white border border-slate-200 rounded-lg hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                                        <i class="fas fa-sync text-sm"></i>
                                    </button>
                                    <button class="p-2 bg-white border border-slate-200 rounded-lg hover:border-blue-500 hover:text-blue-600 transition-all shadow-sm">
                                        <i class="fas fa-check text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Order Detail Modal -->
    <div x-show="selectedOrder" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-all duration-300">
        <div @click.away="selectedOrder = null" class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
            <div class="h-1 bg-gradient-to-r from-brand-500 to-brand-700"></div>
            <div class="p-6 md:p-8 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-xl font-bold text-slate-800" x-text="'Sipariş Detayı #' + selectedOrder?.id"></h3>
                        <p class="text-sm text-slate-500 mt-1" x-text="selectedOrder?.marketplace + ' üzerinden gelen sipariş akışı'"></p>
                    </div>
                    <button @click="selectedOrder = null" class="p-2 hover:bg-slate-50 rounded-lg transition-colors text-slate-400">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto space-y-8 custom-scrollbar pr-2">
                    <!-- Items Section -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="fas fa-shopping-basket"></i> Sipariş İçeriği
                        </h4>
                        <div class="space-y-2">
                            <template x-for="item in selectedOrder?.items">
                                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 bg-white rounded border border-slate-200 flex items-center justify-center text-slate-300">
                                            <i class="fas fa-box text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800" x-text="item.name"></p>
                                            <p class="text-[10px] text-slate-400">Adet: <span class="text-slate-600 font-bold" x-text="item.quantity"></span></p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-900 tabular-nums" x-text="item.price.toFixed(2) + ' ₺'"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Details Section -->
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt"></i> Teslimat Adresi
                            </h4>
                            <p class="text-sm font-semibold text-slate-700 leading-relaxed" x-text="selectedOrder?.address"></p>
                            <p class="text-xs text-slate-500 mt-2 font-medium" x-text="selectedOrder?.customer"></p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <i class="fas fa-clock"></i> Zaman Akışı
                            </h4>
                            <div class="space-y-4">
                                <template x-for="t in selectedOrder?.timeline">
                                    <div class="flex items-start gap-3 relative">
                                        <div class="h-2 w-2 rounded-full bg-brand-500 mt-1.5 z-10"></div>
                                        <div class="absolute left-[3.5px] top-4 w-[1px] h-full bg-slate-200"></div>
                                        <div>
                                            <p class="text-xs font-extrabold text-slate-800" x-text="t.action"></p>
                                            <p class="text-[10px] text-slate-500 mt-0.5" x-text="t.date"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase">Toplam Tutar</p>
                        <p class="text-2xl font-black text-brand-600 tabular-nums" x-text="selectedOrder?.total.toFixed(2) + ' ₺'"></p>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition-colors">Yazdır</button>
                        <button class="px-6 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-colors shadow-lg shadow-brand-500/20">Fatura Oluştur</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
