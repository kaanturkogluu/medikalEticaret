@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    saving: false,
    tab: 'general'
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products') }}" class="h-10 w-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-brand-600 hover:border-brand-500 transition-all shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Ürün Düzenle</h2>
                <p class="text-sm text-slate-500 mt-1">{{ $product->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="$refs.editForm.submit(); saving = true" :disabled="saving" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold hover:bg-brand-700 transition-all flex items-center gap-2 shadow-lg shadow-brand-500/20 disabled:opacity-50">
                <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                <span x-text="saving ? 'Kaydediliyor...' : 'Değişiklikleri Kaydet'"></span>
            </button>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Tabs and Main Info -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Navigation Tabs -->
            <div class="flex items-center gap-1 bg-white p-1.5 rounded-2xl border border-slate-100 shadow-sm overflow-x-auto no-scrollbar">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shrink-0">
                    <i class="fas fa-info-circle text-xs"></i> Genel Bilgiler
                </button>
                <button @click="tab = 'variants'" :class="tab === 'variants' ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shrink-0">
                    <i class="fas fa-layer-group text-xs"></i> Varyantlar & Özellikler
                </button>
                <button @click="tab = 'marketplaces'" :class="tab === 'marketplaces' ? 'bg-brand-50 text-brand-600' : 'text-slate-500 hover:bg-slate-50'" class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all flex items-center gap-2 shrink-0">
                    <i class="fas fa-store text-xs"></i> Pazaryeri Verileri
                </button>
            </div>

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" x-ref="editForm">
                @csrf
                @method('PUT')
                
                <!-- General Tab Content -->
                <div x-show="tab === 'general'" class="space-y-6 transition-all" x-transition:enter="duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-2">
                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Ürün Adı (Sistem)</label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                                <p class="text-[10px] text-slate-400 px-1 mt-1 font-medium">* Bu isim sadece bu sistemde ve web sitesinde kullanılır.</p>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Marka</label>
                                <select name="brand_id" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                                    <option value="">Marka Seçin</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Stok Kodu (SKU)</label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all uppercase">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Barkod</label>
                                <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 tabular-nums font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Fiyat (₺)</label>
                                <div class="relative">
                                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" class="w-full pl-5 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold tabular-nums focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                                    <span class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₺</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Stok Miktarı</label>
                                <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold tabular-nums focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Açıklama</label>
                            <textarea name="description" rows="6" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-600 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Variants Tab Content -->
                <div x-show="tab === 'variants'" class="space-y-6" x-cloak x-transition:enter="duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-2">
                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Ürün Özellikleri</h3>
                                <p class="text-xs text-slate-500 mt-1">Trendyol ve diğer mecralardan gelen teknik detaylar</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($product->productAttributes as $attr)
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $attr->attribute_name }}</p>
                                <p class="text-sm font-bold text-slate-700">{{ $attr->attribute_value }}</p>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($product->variant_key)
                        <div class="mt-8 pt-8 border-t border-slate-100">
                            <h3 class="text-lg font-bold text-slate-800 mb-6">Varyant Grup: <span class="text-brand-600">{{ $product->variant_key }}</span></h3>
                            <div class="space-y-3">
                                @foreach($product->variants as $variant)
                                <div class="flex items-center justify-between p-4 {{ $variant->id == $product->id ? 'bg-brand-50 border-brand-100' : 'bg-white border-slate-100' }} border rounded-2xl group transition-all">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden">
                                            @if($variant->productImages->first())
                                                <img src="{{ $variant->productImages->first()->url }}" class="h-full w-full object-cover">
                                            @else
                                                <i class="fas fa-image text-slate-300"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold {{ $variant->id == $product->id ? 'text-brand-700' : 'text-slate-700' }}">{{ $variant->sku }}</p>
                                            <p class="text-[10px] text-slate-400 uppercase tracking-tighter">{{ $variant->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-6">
                                        <div class="text-right">
                                            <p class="text-xs font-bold text-slate-700 tabular-nums">{{ number_format($variant->price, 2) }} ₺</p>
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $variant->stock }} Adet</p>
                                        </div>
                                        @if($variant->id != $product->id)
                                        <a href="{{ route('admin.products.edit', $variant->id) }}" class="px-4 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-500 hover:border-brand-500 hover:text-brand-600 transition-all">DÜZENLE</a>
                                        @else 
                                        <span class="px-4 py-1.5 bg-brand-500 text-white rounded-lg text-[10px] font-bold uppercase">ŞU ANKİ</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Marketplaces Tab Content -->
                <div x-show="tab === 'marketplaces'" class="space-y-6" x-cloak x-transition:enter="duration-300 ease-out" x-transition:enter-start="opacity-0 translate-y-2">
                    <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Pazaryeri Bağlantıları</h3>
                                <p class="text-xs text-slate-500 mt-1">Her bir kanal için özel fiyat ve stok durumları</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse($product->channelProducts as $cp)
                            <div class="p-6 bg-white border border-slate-200 rounded-3xl hover:border-brand-500/50 transition-all group">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center">
                                            @if($cp->channel->name == 'Trendyol')
                                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b3/Trendyol_logo.svg" class="h-8 w-8 object-contain opacity-70 group-hover:opacity-100 transition-all">
                                            @else
                                                <i class="fas fa-store text-slate-300 text-xl group-hover:text-brand-500 transition-all"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-800 tracking-tight">{{ $cp->channel->name }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID:</span>
                                                <span class="text-[10px] font-bold text-brand-600 bg-brand-50 px-2 py-0.5 rounded tracking-tighter">{{ $cp->external_id }}</span>
                                                <span class="text-[10px] text-slate-300">|</span>
                                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">DURUM:</span>
                                                <span class="text-[10px] font-bold {{ $cp->sync_status == 'synced' ? 'text-emerald-500' : 'text-amber-500' }} uppercase">{{ $cp->sync_status }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-12">
                                        <div class="text-right">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kanal Fiyatı</p>
                                            <p class="text-sm font-bold text-slate-800 tabular-nums">{{ number_format($cp->price, 2) }} ₺</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kanal Stoğu</p>
                                            <p class="text-sm font-bold text-slate-800 tabular-nums">{{ $cp->stock }} Adet</p>
                                        </div>
                                        <button type="button" class="h-10 w-10 flex items-center justify-center bg-slate-50 rounded-xl text-slate-400 hover:text-brand-600 hover:bg-brand-50 transition-all">
                                            <i class="fas fa-sync-alt text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-12 px-6 border-2 border-dashed border-slate-100 rounded-3xl">
                                <i class="fas fa-link-slash text-4xl text-slate-200 mb-4"></i>
                                <p class="text-sm font-bold text-slate-500">Bu ürün henüz hiçbir pazaryeri ile eşleşmedi.</p>
                                <button type="button" class="mt-4 text-brand-600 text-xs font-bold uppercase tracking-widest hover:text-brand-700">Bağlantı Ekle +</button>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            
            <!-- Product Images -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Ürün Görselleri</h3>
                    <button class="text-brand-600 text-[10px] font-bold uppercase tracking-widest hover:text-brand-700">Yönet</button>
                </div>
                
                <div class="grid grid-cols-3 gap-2">
                    @foreach($product->productImages as $image)
                    <div class="aspect-square rounded-2xl bg-slate-50 border border-slate-100 relative group overflow-hidden">
                        <img src="{{ $image->url }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                            <button class="h-8 w-8 rounded-lg bg-white/20 backdrop-blur-md text-white text-xs hover:bg-white/40 transition-colors">
                                <i class="fas fa-expand"></i>
                            </button>
                            <button class="h-8 w-8 rounded-lg bg-white/20 backdrop-blur-md text-white text-xs hover:bg-white/40 transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                    <button class="aspect-square rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center gap-1 text-slate-400 hover:border-brand-400 hover:text-brand-500 hover:bg-brand-50 transition-all">
                        <i class="fas fa-plus text-xs"></i>
                        <span class="text-[9px] font-bold uppercase">YÜKLE</span>
                    </button>
                </div>
            </div>

            <!-- Basic Stats & Meta -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Ürün Durumu</p>
                    <div class="flex items-center justify-between p-4 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100">
                        <div class="flex items-center gap-3">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <p class="text-xs font-bold uppercase tracking-widest">SATIŞA AÇIK</p>
                        </div>
                        <input type="checkbox" name="active" value="1" {{ $product->active ? 'checked' : '' }} class="h-5 w-5 rounded-md border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-400 uppercase tracking-widest">KATEGORİ</span>
                        <span class="font-bold text-slate-800 text-right">{{ $product->category->name ?? 'Belirtilmedi' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-400 uppercase tracking-widest">MARKA</span>
                        <span class="font-bold text-slate-800 text-right">{{ $product->brand_name ?? 'Belirtilmedi' }}</span>
                    </div>
                    <div class="h-px bg-slate-50 my-2"></div>
                    <div class="flex justify-between items-center text-xs text-slate-400">
                        <span>Oluşturulma</span>
                        <span class="font-medium">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-slate-400">
                        <span>Son Güncelleme</span>
                        <span class="font-medium tabular-nums">{{ $product->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-corporate p-6 rounded-3xl text-white shadow-xl shadow-corporate/20 relative overflow-hidden">
                <div class="absolute -right-8 -bottom-8 h-32 w-32 bg-brand-500 rounded-full blur-[60px] opacity-20"></div>
                
                <h3 class="text-sm font-black uppercase tracking-widest mb-6 relative z-10">Hızli İşlemler</h3>
                
                <div class="grid grid-cols-1 gap-2 relative z-10">
                    <button @click="notify('info', 'Fiyat tüm kanallarda güncelleniyor...')" class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-2xl flex items-center gap-3 px-4 transition-all text-xs font-bold">
                        <i class="fas fa-tag text-brand-400"></i> Tüm Kanallarda Fiyatı Eşitle
                    </button>
                    <button @click="notify('info', 'Stok tüm kanallarda güncelleniyor...')" class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-2xl flex items-center gap-3 px-4 transition-all text-xs font-bold">
                        <i class="fas fa-cubes text-emerald-400"></i> Tüm Kanallarda Stoğu Eşitle
                    </button>
                    <button @click="notify('warning', 'Ürün pazaryeri silme henüz aktif değil')" class="w-full py-3 bg-rose-500/20 hover:bg-rose-500/30 text-rose-400 rounded-2xl flex items-center gap-3 px-4 transition-all text-xs font-bold">
                        <i class="fas fa-trash-alt"></i> Ürünü Tüm Kanallardan Sil
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
