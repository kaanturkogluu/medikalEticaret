@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Mail Geçmişi</h1>
        <p class="text-sm text-slate-500 mt-1">Sistemden gönderilen e-postaların kayıtları</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-4 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row gap-4 justify-between items-center">
        <form action="{{ route('admin.email-logs.index') }}" method="GET" class="flex flex-wrap gap-3 w-full">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="E-posta veya Konu ara..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm">
                </div>
            </div>
            <select name="type" class="border border-slate-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                <option value="">Tüm Türler</option>
                <option value="Hoşgeldin" {{ request('type') == 'Hoşgeldin' ? 'selected' : '' }}>Hoşgeldin</option>
                <option value="Sipariş" {{ request('type') == 'Sipariş' ? 'selected' : '' }}>Sipariş</option>
                <option value="Kargo" {{ request('type') == 'Kargo' ? 'selected' : '' }}>Kargo</option>
                <option value="Sipariş İptal" {{ request('type') == 'Sipariş İptal' ? 'selected' : '' }}>Sipariş İptal</option>
                <option value="Diğer" {{ request('type') == 'Diğer' ? 'selected' : '' }}>Diğer</option>
            </select>
            <select name="status" class="border border-slate-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                <option value="">Tüm Durumlar</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Başarılı</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Başarısız</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-700 text-sm font-medium transition-colors">
                Filtrele
            </button>
            @if(request()->anyFilled(['search', 'type', 'status']))
                <a href="{{ route('admin.email-logs.index') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 text-sm font-medium transition-colors">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-medium">Alıcı</th>
                    <th class="px-6 py-4 font-medium">Tür</th>
                    <th class="px-6 py-4 font-medium">Konu</th>
                    <th class="px-6 py-4 font-medium">Durum</th>
                    <th class="px-6 py-4 font-medium">Tarih</th>
                    <th class="px-6 py-4 font-medium text-right">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-800">
                        {{ $log->to_email }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $log->type ?? 'Bilinmiyor' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-600 truncate max-w-xs">
                        {{ $log->subject }}
                    </td>
                    <td class="px-6 py-4">
                        @if($log->status == 'sent')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-check-circle mr-1.5"></i> Başarılı
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700 border border-rose-200">
                                <i class="fas fa-times-circle mr-1.5"></i> Başarısız
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-slate-500 whitespace-nowrap">
                        {{ $log->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="showMailDetails({{ $log->id }})" class="text-brand-600 hover:text-brand-800 text-sm font-medium">
                            <i class="fas fa-eye"></i> Görüntüle
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-4">
                            <i class="fas fa-envelope-open text-2xl text-slate-400"></i>
                        </div>
                        <p class="font-medium text-slate-600">Henüz mail kaydı bulunmuyor.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $logs->links() }}
        </div>
    @endif
</div>

<!-- Modal for Mail Details -->
<div id="mailModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4" x-cloak>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden" @click.away="closeMailModal()">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-slate-50 shrink-0">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-envelope text-brand-500"></i> Mail Detayı
            </h3>
            <button onclick="closeMailModal()" class="text-slate-400 hover:text-slate-600 p-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar" id="mailModalContent">
            <!-- Content loaded via AJAX -->
            <div class="flex justify-center py-10">
                <i class="fas fa-spinner fa-spin text-3xl text-brand-500"></i>
            </div>
        </div>
    </div>
</div>

<script>
    function showMailDetails(id) {
        document.getElementById('mailModal').classList.remove('hidden');
        document.getElementById('mailModalContent').innerHTML = `
            <div class="flex justify-center py-10">
                <i class="fas fa-spinner fa-spin text-3xl text-brand-500"></i>
            </div>
        `;
        
        fetch(`/admin/email-logs/${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('mailModalContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('mailModalContent').innerHTML = `
                    <div class="p-4 bg-rose-50 text-rose-600 rounded-lg">
                        Mail detayı yüklenirken bir hata oluştu.
                    </div>
                `;
            });
    }

    function closeMailModal() {
        document.getElementById('mailModal').classList.add('hidden');
    }
</script>
@endsection
