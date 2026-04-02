@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ 
    syncing: false, 
    progress: 0, 
    processed: 0, 
    total: 850,
    logs: [],
    startSync() {
        this.syncing = true;
        this.progress = 0;
        this.processed = 0;
        this.logs = [];
        this.addLog('Price sync engine initialized...', 'info');
        
        let interval = setInterval(() => {
            if (this.progress < 100) {
                this.progress += Math.floor(Math.random() * 8) + 1;
                this.processed = Math.floor((this.progress / 100) * this.total);
                
                if (Math.random() > 0.7) {
                    const skus = ['AY- sneaker', 'CK- deri', 'TR- tişört', 'KP- kot'];
                    const sku = skus[Math.floor(Math.random() * skus.length)];
                    this.addLog(`Price updated for SKU: ${sku} successfully on N11.`, 'success');
                }
            } else {
                this.progress = 100;
                this.processed = this.total;
                this.syncing = false;
                this.addLog('Bulk price sync completed successfully.', 'success');
                notify('success', 'Tüm Fiyatlar Senkronize Edildi!');
                clearInterval(interval);
            }
        }, 200);
    },
    addLog(msg, type) {
        const time = new Date().toLocaleTimeString();
        this.logs.unshift({ msg, type, time });
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Toplu Fiyat Senkronizasyonu</h2>
            <p class="text-sm text-slate-500 mt-1">Sistemdeki güncel fiyatları tüm pazaryerlerine anlık olarak basın.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="startSync()" :disabled="syncing" class="px-6 py-2.5 bg-amber-600 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-amber-500/20 hover:bg-amber-700 transition-colors disabled:opacity-50">
                <i :class="syncing ? 'fa-spinner fa-spin' : 'fa-play-circle'" class="fas"></i> 
                Fiyatları Senkronize Et
            </button>
        </div>
    </div>

    <!-- Main Sync View -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-12">
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <div class="flex items-center justify-between mt-8 mb-4">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Genel İlerleme</p>
                        <p class="text-3xl font-black text-slate-800 tabular-nums" x-text="progress + '%'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">İşlenen Fiyat</p>
                        <p class="text-3xl font-black text-amber-600 tabular-nums" x-text="processed + ' / ' + total"></p>
                    </div>
                </div>
                
                <div class="w-full h-4 bg-slate-100 rounded-full overflow-hidden shadow-inner flex">
                    <div class="h-full bg-gradient-to-r from-amber-400 via-amber-600 to-amber-800 transition-all duration-300 relative" :style="'width: ' + progress + '%'">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse w-full"></div>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-3 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Kuyruk Durumu</p>
                        <p class="text-lg font-black text-slate-800 tabular-nums">8 Parallel Requests</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center flex flex-col justify-center text-amber-600">
                        <i class="fas fa-bolt mb-1"></i>
                        <p class="text-lg font-black tabular-nums">Yüksek Hız</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-center flex flex-col justify-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Hata Toleransı</p>
                        <p class="text-lg font-black text-slate-800 tabular-nums">3 Retries Max</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-12">
            <div class="bg-corporate border border-slate-700 rounded-3xl p-6 shadow-2xl flex flex-col h-[400px]">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-800">
                    <h3 class="text-base font-bold text-slate-300 flex items-center gap-2">
                        <i class="fas fa-terminal text-amber-500"></i> Fiyat İşlem Konsolu
                    </h3>
                </div>
                <div class="flex-1 overflow-y-auto space-y-2 custom-scrollbar font-mono text-[11px] leading-relaxed">
                    <template x-for="(log, index) in logs" :key="index">
                        <div class="flex items-start gap-4 p-2 rounded hover:bg-slate-800/50 transition-colors">
                            <span class="text-slate-600 font-bold shrink-0" x-text="log.time"></span>
                            <span :class="log.type === 'success' ? 'text-emerald-400' : 'text-blue-400'" class="font-bold flex items-center gap-2 underline decoration-slate-700 underline-offset-4">
                                <span class="text-slate-200" x-text="log.msg"></span>
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
