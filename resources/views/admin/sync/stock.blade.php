@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ 
    syncing: false, 
    progress: 0, 
    processed: 0, 
    total: 1540,
    logs: [],
    startSync() {
        this.syncing = true;
        this.progress = 0;
        this.processed = 0;
        this.logs = [];
        this.addLog('Sync engine initialized...', 'info');
        
        let interval = setInterval(() => {
            if (this.progress < 100) {
                this.progress += Math.floor(Math.random() * 5) + 1;
                this.processed = Math.floor((this.progress / 100) * this.total);
                
                if (Math.random() > 0.8) {
                    const skus = ['TR-001', 'HB-99X', 'N11-A2', 'TR-L99', 'HB-002'];
                    const sku = skus[Math.floor(Math.random() * skus.length)];
                    this.addLog(`Updated SKU: ${sku} successfully.`, 'success');
                }
            } else {
                this.progress = 100;
                this.processed = this.total;
                this.syncing = false;
                this.addLog('Bulk stock sync completed successfully.', 'success');
                notify('success', 'Tüm Stoklar Senkronize Edildi!');
                clearInterval(interval);
            }
        }, 300);
    },
    addLog(msg, type) {
        const time = new Date().toLocaleTimeString();
        this.logs.unshift({ msg, type, time });
        if (this.logs.length > 50) this.logs.pop();
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Toplu Stok Senkronizasyonu</h2>
            <p class="text-sm text-slate-500 mt-1">Tüm veritabanındaki stok miktarlarını bağlı pazaryerlerine gönderin.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="startSync()" :disabled="syncing" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-colors disabled:opacity-50">
                <i :class="syncing ? 'fa-spinner fa-spin' : 'fa-play-circle'" class="fas"></i> 
                Toplu Senkronize Et
            </button>
        </div>
    </div>

    <!-- Main Sync View -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Progress & Queue Status -->
        <div class="lg:col-span-12 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mt-8 mb-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Genel İlerleme</p>
                        <p class="text-3xl font-black text-slate-800 tabular-nums" x-text="progress + '%'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">İşlenen Kayıt</p>
                        <p class="text-3xl font-black text-brand-600 tabular-nums" x-text="processed + ' / ' + total"></p>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden shadow-inner flex">
                    <div class="h-full bg-gradient-to-r from-brand-400 via-brand-600 to-brand-800 transition-all duration-300 relative shadow-[0_0_15px_rgba(14,165,233,0.5)]" :style="'width: ' + progress + '%'">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse w-full"></div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Aktif Kuyruklar</p>
                        <p class="text-lg font-black text-slate-800 tabular-nums">4 Workers</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tahmini Kalan Süre</p>
                        <p x-show="syncing" class="text-lg font-black text-slate-800 tabular-nums">2dk 15sn</p>
                        <p x-show="!syncing" class="text-lg font-black text-slate-400 uppercase">--</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Hata Oranı</p>
                        <p class="text-lg font-black text-red-500 tabular-nums">%0.00</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Per Item Logging Viewer (Simulated) -->
        <div class="lg:col-span-12">
            <div class="bg-corporate border border-slate-700 rounded-3xl p-6 shadow-2xl flex flex-col h-[500px]">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
                    <h3 class="text-base font-bold text-slate-300 flex items-center gap-2 tracking-tight">
                        <i class="fas fa-terminal text-brand-500"></i> Anlık İşlem Kayıtları
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Success: 841</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-red-500"></span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase">Errors: 0</span>
                        </div>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto space-y-2 custom-scrollbar font-mono text-[11px] leading-relaxed selection:bg-brand-500/30">
                    <template x-for="(log, index) in logs" :key="index">
                        <div class="flex items-start gap-4 p-2 rounded hover:bg-slate-800/50 transition-colors">
                            <span class="text-slate-600 font-bold shrink-0" x-text="log.time"></span>
                            <span :class="{
                                'text-emerald-400': log.type === 'success',
                                'text-blue-400': log.type === 'info',
                                'text-red-400': log.type === 'error'
                            }" class="font-bold flex items-center gap-2">
                                <span class="uppercase tracking-widest text-[9px] px-1.5 py-0.5 rounded border" :style="'border-color: currentColor'" x-text="log.type"></span>
                                <span class="text-slate-200" x-text="log.msg"></span>
                            </span>
                        </div>
                    </template>
                    <div x-show="logs.length === 0" class="h-full flex flex-col items-center justify-center text-slate-600 italic">
                        <i class="fas fa-ghost text-4xl mb-4 opacity-10"></i>
                        Henüz senkronizasyon kaydı bulunmuyor. Senkronizasyonu başlatın.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
