@extends('layouts.admin')

@section('content')
<div class="space-y-6 h-full flex flex-col" x-data="{ 
    filter: 'all', 
    logs: @json($logs),
    filteredLogs() {
        return this.logs.filter(l => this.filter === 'all' || l.type === this.filter);
    },
    expanded: null
}">
    <!-- Header -->
    <div class="flex items-center justify-between shrink-0">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sistem Logları & Debug</h2>
            <p class="text-sm text-slate-500 mt-1">Sistemdeki hata ve işlem kayıtlarını anlık izleyin.</p>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('admin.logs.clear') }}" method="POST" onsubmit="return confirm('Tüm log geçmişini silmek istediğinize emin misiniz?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2">
                    <i class="fas fa-trash-alt text-[10px]"></i> Temizle
                </button>
            </form>
            <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2">
                <i class="fas fa-sync-alt text-[10px]"></i> Yenile
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex items-center gap-4 shrink-0">
        <div class="flex gap-2">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'bg-corporate text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-widest">Hepsi</button>
            <button @click="filter = 'success'" :class="filter === 'success' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-widest">Başarılı</button>
            <button @click="filter = 'error'" :class="filter === 'error' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-widest">Hatalı</button>
            <button @click="filter = 'info'" :class="filter === 'info' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all uppercase tracking-widest">Bilgi</button>
        </div>
        <div class="flex-1"></div>
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            <input type="text" placeholder="Endpoint veya Payload Ara..." class="pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold focus:outline-none w-64 tracking-tight">
        </div>
    </div>

    <!-- Log Viewer -->
    <div class="flex-1 bg-corporate rounded-3xl overflow-hidden shadow-2xl flex flex-col min-h-[500px]">
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6 space-y-4">
            <template x-for="log in filteredLogs()" :key="log.id">
                <div class="space-y-2 group">
                    <div @click="expanded = (expanded === log.id ? null : log.id)" class="flex items-start gap-4 p-4 bg-slate-900 border border-slate-800 rounded-2xl cursor-pointer hover:border-brand-500 transition-all font-mono">
                        <span class="text-slate-600 text-[10px] font-bold mt-1" x-text="log.time"></span>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span :class="{
                                    'bg-emerald-500/10 text-emerald-500 border-emerald-500/50': log.type === 'success',
                                    'bg-red-500/10 text-red-500 border-red-500/50': log.type === 'error',
                                    'bg-blue-500/10 text-blue-400 border-blue-500/50': log.type === 'info'
                                }" class="text-[9px] px-2 py-0.5 rounded border-2 font-black uppercase tracking-widest" x-text="log.type"></span>
                                <span class="text-slate-200 font-black text-xs tracking-tight" x-text="log.endpoint"></span>
                                <span x-show="log.status !== 'N/A' && log.status !== '-'" :class="log.status >= 400 ? 'text-red-500' : 'text-emerald-500'" class="text-[10px] font-black" x-text="'[' + log.status + ']'"></span>
                            </div>
                            <div class="flex items-center gap-4 truncate">
                                <p class="text-[11px] text-slate-500 truncate" x-text="'Kayıt: ' + log.payload"></p>
                                <span class="text-slate-700">|</span>
                                <p class="text-[11px] text-slate-500 truncate" x-text="'Detay: ' + log.response"></p>
                            </div>
                        </div>

                        <i class="fas fa-chevron-down text-slate-600 mt-2 transition-transform duration-300" :class="expanded === log.id ? 'rotate-180' : ''"></i>
                    </div>

                    <!-- Expandable Details -->
                    <div x-show="expanded === log.id" x-collapse x-cloak class="mt-2 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-5 bg-slate-800 rounded-2xl border border-slate-700">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center justify-between">
                                    Log Mesajı / Payload
                                    <button @click.stop="notify('info', 'Kopyalandı')" class="hover:text-white transition-colors"><i class="far fa-copy"></i></button>
                                </p>
                                <pre x-text="log.payload" class="text-xs text-brand-400 whitespace-pre-wrap font-mono"></pre>
                            </div>
                            <div class="p-5 bg-slate-800 rounded-2xl border border-slate-700">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center justify-between">
                                    Ek Bilgiler / Response
                                    <button @click.stop="notify('info', 'Kopyalandı')" class="hover:text-white transition-colors"><i class="far fa-copy"></i></button>
                                </p>
                                <pre x-text="log.response" class="text-xs text-emerald-400 whitespace-pre-wrap font-mono"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="filteredLogs().length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-slate-500">
                    <i class="fas fa-terminal text-4xl mb-4 opacity-20"></i>
                    <p class="text-sm font-bold uppercase tracking-widest">Henüz bir kayıt bulunmuyor</p>
                    <p class="text-[10px] mt-1 opacity-60">Sistem işlemleri burada görüntülenecektir.</p>
                </div>
            </template>
        </div>
        <div class="p-4 bg-slate-900 border-t border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse-soft"></span>
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sistem İzleniyor...</span>
            </div>
            <p class="text-[10px] text-slate-600 font-bold tracking-tight">Version 2.0.4 - Developer Debug Engine On</p>
        </div>
    </div>
</div>
@endsection
