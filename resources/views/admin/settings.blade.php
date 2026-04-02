@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    saving: false,
    saveSettings() {
        this.saving = true;
        setTimeout(() => {
            this.saving = false;
            notify('success', 'Ayarlar başarıyla kaydedildi!');
        }, 1200);
    },
    settings: {
        queue_workers: 4,
        max_retries: 3,
        api_timeout: 30,
        sync_batch_size: 100,
        enable_notifications: true
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sistem Ayarları</h2>
            <p class="text-sm text-slate-500 mt-1">Kuyruk yapısı, hata toleransı ve entegrasyon sınırlarını belirleyin.</p>
        </div>
        <button @click="saveSettings()" :disabled="saving" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-colors disabled:opacity-50">
            <i :class="saving ? 'fa-spinner fa-spin' : 'fa-save'" class="fas"></i> 
            Ayarları Kaydet
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Settings Grid Left -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Queue Section -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-6">
                    <div class="h-10 w-10 bg-brand-50 rounded-xl flex items-center justify-center text-brand-600">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Kuyruk & İşlemci Ayarları</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Worker Sayısı</label>
                        <input type="number" x-model="settings.queue_workers" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <p class="text-[10px] text-slate-400 font-medium px-1 leading-relaxed">Aynı anda çalışan arka plan işlemcisi sayısıdır. Fazla olması sunucu kaynaklarını tüketebilir.</p>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Batch Boyutu</label>
                        <input type="number" x-model="settings.sync_batch_size" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        <p class="text-[10px] text-slate-400 font-medium px-1 leading-relaxed">Her bir toplu senkronizasyonda gruptaki ürün sayısı.</p>
                    </div>
                </div>
            </div>

            <!-- Error Section -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-6">
                    <div class="h-10 w-10 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 tracking-tight">Hata Toleransı & Retry</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Maksimum Deneme (Retries)</label>
                        <input type="number" x-model="settings.max_retries" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">API Zaman Aşımı (Timeout)</label>
                        <div class="relative">
                            <input type="number" x-model="settings.api_timeout" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all pr-12">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400">saniye</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Options -->
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 tracking-tight mb-6 flex items-center gap-2">
                    <i class="fas fa-bell text-brand-500"></i> Bildirim Ayarları
                </h3>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div>
                        <p class="text-xs font-bold text-slate-700">Sistem Bildirimleri</p>
                        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tight">Hata oluştuğunda bildir</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="settings.enable_notifications" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    </label>
                </div>
            </div>
            
            <div class="p-8 bg-brand-900 rounded-3xl shadow-xl shadow-brand-950/20 text-white relative overflow-hidden">
                <i class="fas fa-rocket text-brand-700 text-9xl absolute -right-4 -bottom-6 opacity-30 transform -rotate-12 translate-x-4 translate-y-4"></i>
                <div class="relative z-10">
                    <h3 class="text-lg font-extrabold mb-4 leading-tight">Gelişmiş Performans Modu</h3>
                    <p class="text-xs text-brand-200 leading-relaxed font-medium mb-6">Sunucu kaynaklarını maksimuma çıkararak daha hızlı senkronizasyon yapın.</p>
                    <button class="px-6 py-2.5 bg-brand-500 hover:bg-brand-400 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-brand-950/30">Hızlandır</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
