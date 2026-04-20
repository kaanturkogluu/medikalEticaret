@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ 
    saving: false,
    channel: {
        name: '{{ old('name', $channel->name) }}',
        slug: '{{ old('slug', $channel->slug) }}',
        active: {{ old('active', $channel->active ? 'true' : 'false') }},
        color: '{{ old('color', $channel->color ?? '#f8fafc') }}',
        api_key: '{{ old('api_key', $channel->credential?->api_key) }}',
        api_secret: '{{ old('api_secret', $channel->credential?->api_secret) }}',
        supplier_id: '{{ old('supplier_id', $channel->credential?->supplier_id) }}'
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight" x-text="channel.name + ' Entegrasyon Ayarları'"></h2>
            <p class="text-sm text-slate-500 mt-1">API anahtarlarını, tedarikçi bilgilerini ve çalışma durumunu bu panelden güncelleyebilirsiniz.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.marketplaces') }}" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left text-[10px]"></i> Vazgeç
            </a>
            <button @click="$refs.form.submit(); saving = true" :disabled="saving" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-colors disabled:opacity-50">
                <i :class="saving ? 'fa-spinner fa-spin' : 'fa-save'" class="fas"></i> 
                Değişiklikleri Kaydet
            </button>
        </div>
    </div>

    <!-- Edit Form Grid -->
    <form x-ref="form" action="{{ route('admin.marketplaces.update', $channel->id) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @csrf
        @method('PUT')
        
        <!-- Left: Core Info -->
        <div class="lg:col-span-12 space-y-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-6">
                    <div class="h-10 w-10 bg-brand-50 rounded-xl flex items-center justify-center text-brand-600">
                        <i class="fas fa-plug text-sm"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Temel Bağlantı Bilgileri</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Marketplace Name -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Pazaryeri Adı</label>
                        <input type="text" name="name" x-model="channel.name" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all p-4">
                        @error('name')<p class="text-[10px] text-red-500 font-bold px-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Marketplace Slug / Identifier -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Slug / Tanımlayıcı</label>
                        <input type="text" name="slug" x-model="channel.slug" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all p-4">
                        @error('slug')<p class="text-[10px] text-red-500 font-bold px-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Marketplace Color -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Tablo Satır Rengi</label>
                        <div class="flex items-center gap-3 p-2 bg-slate-50 border border-slate-200 rounded-xl">
                            <input type="color" name="color" x-model="channel.color" class="h-10 w-12 border-0 bg-transparent cursor-pointer rounded-lg p-0">
                            <input type="text" x-model="channel.color" class="flex-1 bg-transparent border-0 text-sm font-black text-slate-700 focus:ring-0 uppercase p-0" placeholder="#FFFFFF">
                        </div>
                        @error('color')<p class="text-[10px] text-red-500 font-bold px-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Active Toggle -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Entegrasyon Durumu</label>
                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <span class="text-xs font-bold text-slate-600 ml-2" x-text="channel.active ? 'Aktif' : 'Pasif'"></span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="active" value="1" x-model="channel.active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600 shadow-inner"></div>
                            </label>
                        </div>
                        <input type="hidden" name="active" x-bind:value="channel.active ? 1 : 0">
                    </div>
                </div>
            </div>

            <!-- Credentials Section -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-6">
                    <div class="h-10 w-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                        <i class="fas fa-key text-sm"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Marketplace API Anahtarı ve Kimlikler</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Supplier ID -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Supplier ID (Tedarikçi No)</label>
                        <div class="relative">
                            <i class="fas fa-building absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="text" name="supplier_id" x-model="channel.supplier_id" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all p-4" placeholder="Örn: 142921">
                        </div>
                    </div>

                    <!-- API Key -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">API Key</label>
                        <div class="relative group">
                            <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input x-ref="apiKey" type="password" name="api_key" x-model="channel.api_key" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all p-4">
                            <button type="button" @click="$refs.apiKey.type = ($refs.apiKey.type === 'password' ? 'text' : 'password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- API Secret -->
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">API Secret</label>
                        <div class="relative group">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <input type="password" name="api_secret" x-model="channel.api_secret" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all p-4">
                        </div>
                    </div>
                </div>

                <div class="mt-10 p-5 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
                    <i class="fas fa-info-circle text-amber-500 mt-1"></i>
                    <div>
                        <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider mb-1">Güvenlik Uyarısı</p>
                        <p class="text-xs text-amber-600 font-medium leading-relaxed">Pazaryeri API anahtarları sistem üzerinden şifreli bir şekilde iletilir. Bağlantı doğrulamasını tamamlamak için gerekli tüm izinlere sahip olduklarından emin olun.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
