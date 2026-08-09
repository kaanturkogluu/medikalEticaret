@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    searchTerm: '', 
    statusFilter: 'all', 
    marketplaceFilter: 'all',
    syncing: null,
    products: {{ $products->getCollection()->toJson() }},
    priceModalOpen: false,
    stockModalOpen: false,
    editingProduct: null,
    tempPrice: 0,
    tempStock: 0,
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
    },
    openPriceModal(product) {
        this.editingProduct = product;
        this.tempPrice = product.price;
        this.priceModalOpen = true;
    },
    savePrice() {
        if (!this.editingProduct) return;
        fetch(`/admin/products/${this.editingProduct.id}/update-price`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ price: this.tempPrice })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.editingProduct.price = data.price;
                this.priceModalOpen = false;
                notify('success', data.message);
            } else if (data.message) {
                notify('error', data.message);
            }
        })
        .catch(err => notify('error', 'Fiyat güncellenirken bir hata oluştu.'));
    },
    openStockModal(product) {
        this.editingProduct = product;
        this.tempStock = product.stock;
        this.stockModalOpen = true;
    },
    saveStock() {
        if (!this.editingProduct) return;
        fetch(`/admin/products/${this.editingProduct.id}/update-stock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ stock: this.tempStock })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.editingProduct.stock = data.stock;
                this.editingProduct.active = data.active;
                this.stockModalOpen = false;
                notify('success', data.message);
            } else if (data.message) {
                notify('error', data.message);
            }
        })
        .catch(err => notify('error', 'Stok güncellenirken bir hata oluştu.'));
    },
    toggleActive(product) {
        fetch(`/admin/products/${product.id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                product.active = data.active;
                notify('success', data.message);
            } else if (data.message) {
                notify('error', data.message);
            }
        })
        .catch(err => notify('error', 'Durum güncellenirken bir hata oluştu.'));
    },
    deleteProduct(id) {
        if (!confirm('Bu ürünü silmek istediğinize emin misiniz?')) return;
        
        fetch(`/admin/products/${id}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.products = this.products.filter(p => p.id !== id);
                notify('success', data.message);
            }
        });
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Ürün Yönetimi</h2>
            <p class="text-sm text-slate-500 mt-1">Stok, fiyat ve ürün bilgilerini tüm mecralarda yönetin.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button @click="$dispatch('open-import-modal')" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-file-import text-xs"></i> İçe Aktar (CSV)
            </button>
            <a href="{{ asset('templates/urun_ekleme_sablonu.csv') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-file-excel text-emerald-600"></i> Excel Şablonu İndir
            </a>
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-semibold hover:bg-slate-800 transition-colors flex items-center gap-2 shadow-lg shadow-slate-900/20">
                <i class="fas fa-plus text-xs"></i> Yeni Ürün Ekle
            </a>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <form action="{{ route('admin.products') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4" id="filterForm">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="SKU veya Ürün Adı ile ara..." class="w-full pl-10 pr-20 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 bg-slate-900 text-white text-[10px] font-black italic rounded-md hover:bg-brand-600 transition-all uppercase">ARA</button>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <select name="stock_status" onchange="this.form.submit()" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all cursor-pointer w-full sm:w-auto">
                    <option value="all">Tüm Stok Durumları</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>Stokta Var</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Stokta Yok</option>
                </select>

                <select x-model="marketplaceFilter" class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm font-bold text-slate-700 focus:outline-none transition-all cursor-pointer w-full sm:w-auto">
                    <option value="all">Tüm Pazaryerleri</option>
                    <option value="Trendyol">Trendyol</option>
                    <option value="Hepsiburada">Hepsiburada</option>
                    <option value="N11">N11</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto overflow-y-auto max-h-[600px] custom-scrollbar">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Ürün Bilgisi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Fiyat & Stok</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Satış Durumu</th>
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
                                        <a :href="'/urun/' + p.slug" target="_blank" class="text-sm font-bold text-slate-800 tracking-tight hover:text-brand-600 hover:underline transition-colors" x-text="p.name" title="Sitede Gör"></a>
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
                                <div class="flex flex-col gap-1.5">
                                    <button type="button" @click="openPriceModal(p)" class="inline-flex items-center gap-2 px-2.5 py-1 bg-slate-50 hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 rounded-lg transition-all text-left w-fit cursor-pointer group/btn" title="Fiyatı Değiştir">
                                        <i class="fas fa-tag text-[10px] text-slate-400 group-hover/btn:text-emerald-600 transition-colors"></i>
                                        <span class="text-xs font-bold text-slate-900 group-hover/btn:text-emerald-700 tabular-nums" x-text="p.price ? p.price.toFixed(2) + ' ₺' : '0.00 ₺'"></span>
                                        <i class="fas fa-pencil-alt text-[9px] text-slate-300 group-hover/btn:text-emerald-500 ml-1"></i>
                                    </button>
                                    <button type="button" @click="openStockModal(p)" class="inline-flex items-center gap-2 px-2.5 py-1 bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-300 rounded-lg transition-all text-left w-fit cursor-pointer group/btn" title="Stoku Değiştir">
                                        <i class="fas fa-boxes text-[10px] text-slate-400 group-hover/btn:text-brand-600 transition-colors"></i>
                                        <span :class="p.stock > 0 ? 'text-slate-600 group-hover/btn:text-brand-700' : 'text-red-500 font-extrabold'" class="text-xs font-bold tabular-nums" x-text="p.stock + ' Adet'"></span>
                                        <i class="fas fa-pencil-alt text-[9px] text-slate-300 group-hover/btn:text-brand-500 ml-1"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <button type="button" @click="toggleActive(p)"
                                        :class="p.active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-bold transition-all shadow-sm cursor-pointer"
                                        :title="p.active ? 'Satışa kapatmak için tıklayın' : 'Satışa açmak için tıklayın'">
                                    <span :class="p.active ? 'bg-emerald-500 animate-pulse' : 'bg-red-500'" class="w-2 h-2 rounded-full"></span>
                                    <span x-text="p.active ? 'Satışa Açık' : 'Satışa Kapalı'"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5 lg:opacity-0 lg:group-hover:opacity-100 opacity-100 transition-opacity">
                                     <a :href="'/admin/products/' + p.id + '/edit'" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-brand-50 hover:border-brand-500 hover:text-brand-600 transition-all shadow-sm">
                                         <i class="fas fa-edit text-sm"></i>
                                     </a>
                                     <button @click="deleteProduct(p.id)" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-red-50 hover:border-red-500 hover:text-red-600 transition-all shadow-sm" title="Sil">
                                         <i class="fas fa-trash text-sm"></i>
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

    <!-- Price Update Modal -->
    <div x-show="priceModalOpen" 
         x-cloak
         class="fixed inset-0 z-[100] overflow-y-auto" 
         style="display: none;" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="priceModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" 
                 @click="priceModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="priceModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative">
                 
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shadow-sm">
                            <i class="fas fa-tag text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Fiyat Güncelle</h3>
                            <p class="text-xs text-slate-400 truncate max-w-[240px]" x-text="editingProduct ? editingProduct.name : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="priceModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="savePrice()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Yeni Fiyat (₺)</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" x-model="tempPrice" required autofocus class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">₺</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="priceModalOpen = false" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">İptal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                            <i class="fas fa-check text-[10px]"></i> Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Stock Update Modal -->
    <div x-show="stockModalOpen" 
         x-cloak
         class="fixed inset-0 z-[100] overflow-y-auto" 
         style="display: none;" 
         role="dialog" 
         aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="stockModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" 
                 @click="stockModalOpen = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="stockModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative">
                 
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-50 border border-brand-100 flex items-center justify-center text-brand-600 shadow-sm">
                            <i class="fas fa-boxes text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Stok Güncelle</h3>
                            <p class="text-xs text-slate-400 truncate max-w-[240px]" x-text="editingProduct ? editingProduct.name : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="stockModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="saveStock()" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Yeni Stok Miktarı</label>
                        <div class="relative">
                            <input type="number" step="1" min="0" x-model="tempStock" required autofocus class="w-full pl-4 pr-14 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 uppercase">Adet</span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="stockModalOpen = false" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">İptal</button>
                        <button type="submit" class="px-5 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 shadow-md shadow-slate-900/20 transition-all flex items-center gap-1.5">
                            <i class="fas fa-check text-[10px]"></i> Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div x-data="productImport()" 
     @open-import-modal.window="openModal()" 
     x-show="isOpen" 
     class="fixed inset-0 z-[100] overflow-y-auto" 
     style="display: none;" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm" 
             @click="closeModal()" 
             aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="isOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             class="inline-block w-full max-w-5xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl relative">
             
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-xl font-black text-slate-800" id="modal-title">Ürünleri İçe Aktar (CSV)</h3>
                    <p class="text-sm text-slate-500 mt-1">Excel'den dışa aktarılmış CSV dosyanızı yükleyin</p>
                </div>
                <button @click="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div x-show="step === 1">
                <form @submit.prevent="submitFile" class="space-y-4">
                    <div class="border-2 border-dashed border-brand-200 rounded-xl p-12 text-center hover:bg-brand-50 hover:border-brand-300 transition-all cursor-pointer group" @click="$refs.fileInput.click()">
                        <div class="w-16 h-16 bg-white shadow-sm border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-csv text-2xl text-brand-500"></i>
                        </div>
                        <h4 class="text-base font-bold text-slate-700 mb-1">CSV dosyanızı seçmek için tıklayın</h4>
                        <p class="text-sm text-slate-500">veya sürükleyip bırakın</p>
                        <input type="file" x-ref="fileInput" @change="handleFileChange" accept=".csv, .txt" class="hidden">
                    </div>
                    
                    <div x-show="fileName" class="text-sm font-bold text-brand-700 bg-brand-50 p-4 rounded-xl border border-brand-100 flex justify-between items-center shadow-inner">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-alt text-brand-400 text-lg"></i>
                            <span x-text="fileName"></span>
                        </div>
                        <i class="fas fa-check-circle text-brand-500 text-lg"></i>
                    </div>
                    
                    <div class="flex justify-end gap-3 mt-8 pt-4 border-t border-slate-100">
                        <button type="button" @click="closeModal()" class="px-5 py-2.5 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors">İptal</button>
                        <button type="submit" :disabled="!file || loading" class="px-6 py-2.5 bg-brand-600 text-white rounded-lg text-sm font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none flex items-center gap-2">
                            <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> İşleniyor...</span>
                            <span x-show="!loading">Ön İzleme Oluştur <i class="fas fa-arrow-right ml-1 text-xs"></i></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
        height: 38px;
        display: flex;
        align-items: center;
        background-color: #f8fafc;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #0ea5e9;
        outline: none;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-dropdown {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productImport', () => ({
            isOpen: false,
            step: 1,
            file: null,
            fileName: '',
            loading: false,
            
            openModal() {
                this.isOpen = true;
                this.step = 1;
                this.file = null;
                this.fileName = '';
                if(this.$refs.fileInput) this.$refs.fileInput.value = '';
            },
            closeModal() {
                this.isOpen = false;
            },
            handleFileChange(e) {
                const files = e.target.files;
                if (files.length > 0) {
                    this.file = files[0];
                    this.fileName = this.file.name;
                }
            },
            submitFile() {
                if (!this.file) return;
                
                this.loading = true;
                let formData = new FormData();
                formData.append('file', this.file);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("admin.products.import.preview") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        window.location.href = '{{ route("admin.products.import.preview-page") }}?key=' + data.import_key;
                    } else {
                        notify('error', data.message || 'Bir hata oluştu.');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    notify('error', 'Sunucu hatası oluştu. JSON yanıt alınamadı.');
                });
            }
        }));
    });
</script>

@endsection
