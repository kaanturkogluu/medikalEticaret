<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Alıcı (Kime)</span>
            <div class="font-medium text-slate-800">{{ $log->to_email }}</div>
        </div>
        
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Gönderim Tarihi</span>
            <div class="font-medium text-slate-800">{{ $log->created_at->format('d.m.Y H:i:s') }}</div>
        </div>

        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Mail Türü</span>
            <div class="font-medium text-slate-800">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ $log->type ?? 'Bilinmiyor' }}
                </span>
            </div>
        </div>

        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block mb-1">Durum</span>
            <div class="font-medium text-slate-800">
                @if($log->status == 'sent')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                        <i class="fas fa-check-circle mr-1.5"></i> Başarılı
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700 border border-rose-200">
                        <i class="fas fa-times-circle mr-1.5"></i> Başarısız
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if($log->status == 'failed' && $log->error_message)
        <div class="bg-rose-50 p-4 rounded-xl border border-rose-100">
            <span class="text-xs font-bold text-rose-400 uppercase tracking-widest block mb-1">Hata Mesajı</span>
            <div class="font-mono text-sm text-rose-700 break-words whitespace-pre-wrap">{{ $log->error_message }}</div>
        </div>
    @endif

    <div class="border border-slate-200 rounded-xl overflow-hidden">
        <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
            <span class="text-sm font-bold text-slate-700">Konu: {{ $log->subject }}</span>
        </div>
        <div class="p-4 bg-white">
            <div class="border border-slate-100 rounded-lg p-4 bg-slate-50/50 min-h-[200px] overflow-hidden">
                <!-- Render HTML safely inside iframe to avoid CSS conflicts -->
                <iframe srcdoc="{{ htmlspecialchars($log->body) }}" class="w-full min-h-[400px] border-0" sandbox="allow-same-origin"></iframe>
            </div>
        </div>
    </div>
</div>
