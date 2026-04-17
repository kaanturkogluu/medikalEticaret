@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    saving: false,
    tab: 'general',
    attributes: [],
    addAttribute() {
        this.attributes.push({ name: '', value: '' });
    },
    removeAttribute(index) {
        this.attributes.splice(index, 1);
    },
    newImages: [],
    isReadingFiles: false,
    handleImageSelect(e) {
        if (!this.$refs.imageInput._rawFiles) this.$refs.imageInput._rawFiles = [];
        
        const files = Array.from(e.target.files);
        if (files.length === 0) return;
        
        this.isReadingFiles = true;
        let loadedCount = 0;
        
        files.forEach(file => {
            file._ui_id = Date.now() + Math.random();
            this.$refs.imageInput._rawFiles.push(file);
            const reader = new FileReader();
            reader.onload = (e) => {
                this.newImages.push({
                    id: file._ui_id,
                    url: e.target.result,
                    name: file.name
                });
                loadedCount++;
                if (loadedCount === files.length) {
                    this.isReadingFiles = false;
                    this.updateFileInput();
                }
            };
            reader.readAsDataURL(file);
        });
    },
    removeNewImage(id) {
        const index = this.newImages.findIndex(i => i.id === id);
        if (index > -1) this.newImages.splice(index, 1);
        
        if (this.$refs.imageInput._rawFiles) {
            const rIndex = this.$refs.imageInput._rawFiles.findIndex(f => f._ui_id === id);
            if (rIndex > -1) this.$refs.imageInput._rawFiles.splice(rIndex, 1);
        }
        this.updateFileInput();
    },
    updateFileInput() {
        if (!this.$refs.imageInput._rawFiles) return;
        const dt = new DataTransfer();
        this.$refs.imageInput._rawFiles.forEach(f => {
            dt.items.add(f);
        });
        this.$refs.imageInput.files = dt.files;
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products') }}" class="h-10 w-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-brand-600 hover:border-brand-500 transition-all shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Yeni Ürün Ekle</h2>
                <p class="text-sm text-slate-500 mt-1">Sisteme yeni bir ürün girişi yapın.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="$refs.createForm.submit();" :disabled="saving || isReadingFiles" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all flex items-center gap-2 shadow-lg shadow-slate-900/20 disabled:opacity-50">
                <i class="fas" :class="saving || isReadingFiles ? 'fa-spinner fa-spin' : 'fa-plus'"></i>
                <span x-text="saving ? 'Kaydediliyor...' : (isReadingFiles ? 'Okunuyor...' : 'Ürünü Kaydet')"></span>
            </button>
        </div>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" x-ref="createForm" enctype="multipart/form-data" @submit="if(isReadingFiles) { $event.preventDefault(); return false; } saving = true;">
        @csrf
        
        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Ürün Adı</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Kategori</label>
                            <select name="category_id" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                                <option value="">Kategori Seçin</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Marka</label>
                            <select name="brand_id" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                                <option value="">Marka Seçin</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Stok Kodu (SKU)</label>
                            <input type="text" name="sku" value="{{ old('sku') }}" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all uppercase">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Barkod</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 tabular-nums font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Fiyat (₺)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="price" value="{{ old('price') }}" required class="w-full pl-5 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold tabular-nums focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                                <span class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₺</span>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Stok Miktarı</label>
                            <input type="number" name="stock" value="{{ old('stock', 0) }}" required class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-bold tabular-nums focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Özel İade Şablonu (Opsiyonel)</label>
                        <select name="return_template_id" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-800 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all appearance-none cursor-pointer">
                            <option value="">Varsayılan İade Koşulları</option>
                            @foreach($returnTemplates as $tmpl)
                                <option value="{{ $tmpl->id }}" {{ old('return_template_id') == $tmpl->id ? 'selected' : '' }}>
                                    {{ $tmpl->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-400 uppercase tracking-widest px-1">Açıklama</label>
                        <textarea name="description" rows="6" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-600 font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">{{ old('description') }}</textarea>
                    </div>

                    <div class="h-px bg-slate-50 my-6"></div>

                    <!-- Ürün Özellikleri (Dynamic) -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">Ürün Özellikleri <span class="text-[10px] text-brand-500 ml-1">(Pazaryeri Eşleşmeleri İçin)</span></h4>
                            <button type="button" @click="addAttribute()" class="text-[10px] font-bold text-brand-600 hover:text-brand-700 uppercase tracking-widest bg-brand-50 px-3 py-1.5 rounded-lg transition-colors">
                                + Özellik Ekle
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <template x-for="(attr, index) in attributes" :key="index">
                                <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                    <div class="flex-1 space-y-1">
                                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest px-1">Özellik Adı (Örn: Renk, Beden, Malzeme)</label>
                                        <input type="text" name="attribute_names[]" x-model="attr.name" required placeholder="Renk" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <label class="text-[9px] font-bold text-slate-400 uppercase tracking-widest px-1">Değeri (Örn: Siyah, XL, Pamuk)</label>
                                        <input type="text" name="attribute_values[]" x-model="attr.value" required placeholder="Siyah" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition-all">
                                    </div>
                                    <button type="button" @click="removeAttribute(index)" class="mt-4 h-9 w-9 shrink-0 flex items-center justify-center bg-white border border-slate-200 text-rose-500 rounded-lg hover:border-rose-200 hover:bg-rose-50 transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </template>
                            <template x-if="attributes.length === 0">
                                <div class="text-center py-6 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                                    <p class="text-xs font-medium text-slate-500">Henüz özellik eklenmedi.</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="h-px bg-slate-50 my-6"></div>

                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-4">Pazaryeri Yönlendirmeleri (Opsiyonel)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @php
                                $siteMarketplaces = json_decode(\App\Models\Setting::getValue('marketplaces', '[]'), true);
                            @endphp
                            @foreach($siteMarketplaces as $sm)
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">{{ $sm['name'] }} Linki</label>
                                    <input type="url" name="marketplace_urls[{{ $sm['name'] }}]" value="{{ old('marketplace_urls.'.$sm['name']) }}" placeholder="https://..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Ürün Durumu</p>
                        <div class="flex items-center justify-between p-4 bg-emerald-50 text-emerald-600 rounded-2xl border border-emerald-100">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                <p class="text-xs font-bold uppercase tracking-widest">AKTİF</p>
                            </div>
                            <input type="checkbox" name="active" value="1" checked class="h-5 w-5 rounded-md border-emerald-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                        </div>
                    </div>

                    <!-- Product Images -->
                    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm mt-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Ürün Görselleri</h3>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2">
                            <!-- Local Image Previews -->
                            <template x-for="image in newImages" :key="image.id">
                                <div class="aspect-square rounded-2xl bg-white border border-brand-200 relative group overflow-hidden">
                                    <img :src="image.url" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <button type="button" @click.stop="removeNewImage(image.id)" class="h-8 w-8 rounded-lg bg-red-500/80 backdrop-blur-md text-white text-xs hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="absolute top-2 left-2 px-2 py-0.5 bg-brand-500 text-white text-[9px] font-bold rounded-lg uppercase tracking-widest shadow-sm">
                                        YENİ
                                    </div>
                                </div>
                            </template>

                            <input type="file" name="images[]" multiple class="hidden" x-ref="imageInput" @change="handleImageSelect" accept="image/*">
                            
                            <button type="button" @click="$refs.imageInput.click()" class="aspect-square rounded-2xl border-2 border-dashed border-slate-100 flex flex-col items-center justify-center gap-1 text-slate-400 hover:border-brand-400 hover:text-brand-500 hover:bg-brand-50 transition-all relative overflow-hidden">
                                <div x-show="isReadingFiles" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/90 z-10 backdrop-blur-sm">
                                    <i class="fas fa-spinner fa-spin text-brand-500 text-lg mb-1"></i>
                                    <span class="text-[9px] font-bold uppercase text-brand-600">Okunuyor...</span>
                                </div>
                                <i class="fas fa-plus text-xs"></i>
                                <span class="text-[9px] font-bold uppercase">YÜKLE</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
