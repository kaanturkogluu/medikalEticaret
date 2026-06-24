@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Geçmiş SMS'ler</h2>
                <p class="text-sm text-slate-500 mt-1">Sistemden müşterilere ve yöneticilere gönderilen tüm SMS logları.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.netgsm.index') }}" class="py-2 px-4 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Test Paneline Dön
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Tarih</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Kategori</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Müşteri</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Telefon</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest w-1/3">Mesaj İçeriği</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap">Görev ID</th>
                            <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest whitespace-nowrap text-right">Durum</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $log->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="p-4 text-xs font-bold text-slate-600 whitespace-nowrap">
                                    <span class="px-2 py-1 bg-slate-100 rounded-lg border border-slate-200">{{ $log->type }}</span>
                                </td>
                                <td class="p-4 text-sm font-medium text-slate-800 whitespace-nowrap">
                                    {{ $log->customer_name ?? '-' }}
                                </td>
                                <td class="p-4 text-sm font-mono text-slate-600 whitespace-nowrap">
                                    {{ $log->phone }}
                                </td>
                                <td class="p-4 text-xs text-slate-500 max-w-[300px] truncate" title="{{ $log->message }}">
                                    {{ $log->message }}
                                </td>
                                <td class="p-4 text-xs font-mono text-slate-400 whitespace-nowrap">
                                    @if($log->job_id)
                                        <div class="flex items-center gap-2">
                                            {{ $log->job_id }}
                                            <button onclick="copyToClipboard('{{ $log->job_id }}')" class="text-brand-500 hover:text-brand-600" title="Kopyala">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-4 text-right whitespace-nowrap">
                                    @if($log->status_code === '00' || $log->status_message === 'Başarılı')
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 text-xs font-bold">
                                            <i class="fas fa-check-circle text-[10px]"></i> Başarılı
                                        </div>
                                    @else
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-600 border border-rose-100 text-xs font-bold" title="{{ $log->status_message }}">
                                            <i class="fas fa-exclamation-circle text-[10px]"></i> Hata ({{ $log->status_code }})
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500 text-sm">
                                    <div class="flex flex-col items-center justify-center gap-3">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-2xl text-slate-300">
                                            <i class="fas fa-history"></i>
                                        </div>
                                        <p>Henüz hiçbir SMS logu bulunmuyor.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $logs->links('pagination::tailwind') }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Optional: show a small toast or alert
        }, function(err) {
            console.error('Kopyalama başarısız: ', err);
        });
    }
</script>
@endpush
