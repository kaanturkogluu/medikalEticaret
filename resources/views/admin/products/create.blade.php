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
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Yeni Ürün Ekle</h2>
                <p class="text-sm text-slate-500 mt-1">Sisteme yeni bir ürün girişi yapın.</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button @click="$refs.createForm.submit(); saving = true" :disabled="saving" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-all flex items-center gap-2 shadow-lg shadow-slate-900/20 disabled:opacity-50">
                <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-plus'"></i>
                <span x-text="saving ? 'Kaydediliyor...' : 'Ürünü Kaydet'"></span>
            </button>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <form action="{{ route('admin.products.store') }}" method="POST" x-ref="createForm">
                @csrf
                
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
            </form>
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
                        <input type="checkbox" name="active" value="1" checked class="h-5 w-5 rounded-md border-emerald-300 text-emerald-600 focus:ring-emerald-500">
                    </div>
                </div>

                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100">
                    <p class="text-[10px] text-amber-700 font-bold uppercase tracking-widest mb-1">Not</p>
                    <p class="text-[10px] text-amber-600 leading-relaxed font-medium">Yeni eklenen ürünler otomatik olarak yayına alınır. Görsel eklemek için ürünü önce kaydediniz.</p>
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
