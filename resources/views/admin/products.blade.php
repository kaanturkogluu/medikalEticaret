@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    searchTerm: '', 
    statusFilter: 'all', 
    marketplaceFilter: 'all',
    syncing: null,
    products: {{ $products->getCollection()->toJson() }},
    filteredProducts() {
        return this.products.filter(p => {
            const matchesSearch = p.name.toLowerCase().includes(this.searchTerm.toLowerCase()) || (p.sku && p.sku.toLowerCase().includes(this.searchTerm.toLowerCase()));
            const matchesStatus = this.statusFilter === 'all' || p.status === this.statusFilter;
            const matchesMarketplace = this.marketplaceFilter === 'all' || (p.marketplaces && p.marketplaces.some(m => m.includes(this.marketplaceFilter)));
            return matchesSearch && matchesStatus && matchesMarketplace;
        });
    },
    sync(id, type) {
        this.syncing = id + '-' + type;
        setTimeout(() => {
            this.syncing = null;
            notify('success', `${type.toUpperCase()} Senkronizasyonu Başarılı!`);
        }, 1500);
    },
    togglePopular(product) {
        fetch(`/admin/products/${product.id}/toggle-popular`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                product.is_popular = data.is_popular;
                notify('success', data.message);
            }
        });
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Ürün Yönetimi</h2>
            <p class="text-sm text-slate-500 mt-1">Stok, fiyat ve ürün bilgilerini tüm mecralarda yönetin.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="notify('info', 'İçe Aktarım Başlatıldı')" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2 shadow-lg shadow-brand-500/20">
                <i class="fas fa-file-import text-xs"></i> Excel/CSV İçe Aktar
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px] relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" x-model="searchTerm" placeholder="SKU veya Ürün Adı ile ara..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
        </div>
        <div class="flex items-center gap-2">
            <select x-model="statusFilter" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 focus:outline-none transition-all">
                <option value="all">Tüm Durumlar</option>
                <option value="synced">Senkronize</option>
                <option value="pending">Bekleyen</option>
                <option value="error">Hatalı</option>
            </select>
            <select x-model="marketplaceFilter" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-700 focus:outline-none transition-all">
                <option value="all">Tüm Pazaryerleri</option>
                <option value="Trendyol">Trendyol</option>
                <option value="Hepsiburada">Hepsiburada</option>
                <option value="N11">N11</option>
            </select>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Ürün Bilgisi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Fiyat & Stok</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Bağlı Kanallar</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Durum</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="p in filteredProducts()" :key="p.id">
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 group-hover:bg-white transition-colors overflow-hidden relative">
                                        <template x-if="p.image">
                                            <img :src="p.image" class="h-full w-full object-cover">
                                        </template>
                                        <template x-if="!p.image">
                                            <i class="fas fa-image text-xl"></i>
                                        </template>
                                        
                                        <!-- Popular Toggle Badge -->
                                        <button @click="togglePopular(p)" 
                                                class="absolute top-0 right-0 w-5 h-5 bg-white border-l border-b border-slate-100 flex items-center justify-center transition-all"
                                                :title="p.is_popular ? 'Popüler ürünlerden çıkar' : 'Popüler ürünlere ekle'">
                                            <i :class="p.is_popular ? 'fas fa-star text-amber-400' : 'far fa-star text-slate-300'" class="text-[10px]"></i>
                                        </button>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800 tracking-tight" x-text="p.name"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">SKU:</span>
                                            <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded tracking-tighter" x-text="p.sku || '-'"></span>
                                            <span class="text-[10px] text-slate-300">|</span>
                                            <span class="text-[10px] font-bold text-slate-400 uppercase">Barkod:</span>
                                            <span class="text-[11px] font-medium text-slate-500 tabular-nums" x-text="p.barcode || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-tag text-[10px] text-slate-400"></i>
                                        <span class="text-sm font-bold text-slate-900 tabular-nums" x-text="p.price ? p.price.toFixed(2) + ' ₺' : '0.00 ₺'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-boxes text-[10px] text-slate-400"></i>
                                        <span :class="p.stock > 0 ? 'text-slate-600' : 'text-red-500'" class="text-xs font-bold tabular-nums" x-text="p.stock + ' Adet'"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-[150px]">
                                    <template x-for="m in p.marketplaces">
                                        <span class="text-[9px] px-2 py-0.5 rounded-full border border-slate-200 bg-white font-bold text-slate-600 shadow-sm" x-text="m"></span>
                                    </template>
                                    <template x-if="!p.marketplaces || p.marketplaces.length === 0">
                                        <span class="text-[9px] text-slate-400 italic">Kanal Yok</span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span :class="{
                                        'bg-emerald-500': p.status === 'synced',
                                        'bg-amber-500': p.status === 'pending',
                                        'bg-red-500': p.status === 'error'
                                    }" class="h-2 w-2 rounded-full"></span>
                                    <span :class="{
                                        'text-emerald-600': p.status === 'synced',
                                        'text-amber-600': p.status === 'pending',
                                        'text-red-600': p.status === 'error'
                                    }" class="text-[10px] font-extrabold uppercase tracking-widest" x-text="p.status"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Sync Buttons -->
                                    <button @click="sync(p.id, 'all')" :disabled="syncing" class="p-2 bg-white border border-slate-200 rounded-lg hover:border-brand-500 hover:text-brand-600 transition-all shadow-sm" title="Tam Senkronize">
                                        <i :class="syncing === p.id + '-all' ? 'fa-spinner fa-spin' : 'fa-sync-alt'" class="fas text-sm"></i>
                                    </button>
                                    <button @click="sync(p.id, 'stock')" :disabled="syncing" class="p-2 bg-white border border-slate-200 rounded-lg hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm" title="Stok Güncelle">
                                        <i :class="syncing === p.id + '-stock' ? 'fa-spinner fa-spin' : 'fa-cubes'" class="fas text-sm"></i>
                                    </button>
                                    <button @click="sync(p.id, 'price')" :disabled="syncing" class="p-2 bg-white border border-slate-200 rounded-lg hover:border-amber-500 hover:text-amber-600 transition-all shadow-sm" title="Fiyat Güncelle">
                                        <i :class="syncing === p.id + '-price' ? 'fa-spinner fa-spin' : 'fa-tag'" class="fas text-sm"></i>
                                    </button>
                                    <div class="w-px h-6 bg-slate-200 mx-1"></div>
                                    <a :href="'/admin/products/' + p.id + '/edit'" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-brand-50 hover:border-brand-500 hover:text-brand-600 transition-all shadow-sm">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-all shadow-sm">
                                        <i class="fas fa-ellipsis-v text-slate-400 text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
            <p>Sistemde toplam <span class="text-slate-800">{{ $products->total() }}</span> ürün kayıtlı</p>
            <div class="pagination-container">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
