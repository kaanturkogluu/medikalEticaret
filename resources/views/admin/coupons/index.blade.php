@extends('layouts.admin')

@section('content')
<div x-data="{ selected: [] }">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Kupon Yönetimi</h1>
            <p class="text-sm text-slate-500">Müşterileriniz için indirim kuponları oluşturun ve yönetin.</p>
        </div>
        <div class="flex items-center gap-3">
            <button 
                x-show="selected.length > 0"
                @click="if(selected.length === 3) window.open('{{ route('admin.coupons.print') }}?ids=' + selected.join(','), '_blank'); else alert('Lütfen tam olarak 3 adet kupon seçin.');"
                :class="selected.length === 3 ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-slate-400 cursor-not-allowed'"
                class="flex items-center gap-2 px-4 py-2 text-white rounded-lg font-bold transition-all shadow-lg">
                <i class="fas fa-print"></i> Seçilenleri Yazdır (<span x-text="selected.length"></span>/3)
            </button>
            <button @click="$dispatch('open-modal', 'create-coupon')" class="flex items-center gap-2 px-4 py-2 bg-brand-500 text-white rounded-lg font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-500/20">
                <i class="fas fa-plus"></i> Yeni Kupon Oluştur
            </button>
        </div>
    </div>

    <!-- Coupons Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 w-10"></th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kupon Kodu</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">İndirim Tipi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Değer</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Durum</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Kullanım Tarihi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-slate-50 transition-colors" :class="selected.includes('{{ $coupon->id }}') ? 'bg-indigo-50/50' : ''">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $coupon->id }}" x-model="selected" 
                                   :disabled="selected.length >= 3 && !selected.includes('{{ $coupon->id }}')"
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">{{ $coupon->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($coupon->type === 'percent')
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">Yüzdelik (%)</span>
                            @else
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">Sabit (TL)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-700">
                            {{ $coupon->type === 'percent' ? '%' . number_format($coupon->value, 0) : '₺' . number_format($coupon->value, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($coupon->is_used)
                            <span class="flex items-center gap-1.5 text-xs font-bold text-rose-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-600"></span> Kullanıldı
                            </span>
                            @else
                            <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-600"></span> Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $coupon->used_at ? $coupon->used_at->format('d.m.Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Bu kuponu silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Sil" {{ $coupon->is_used ? 'disabled opacity-50' : '' }}>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <i class="fas fa-ticket-alt text-4xl mb-3 block opacity-20"></i>
                            Henüz hiç kupon oluşturulmamış.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
            {{ $coupons->links() }}
        </div>
        @endif
    </div>
</div>
    @if($coupons->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
        {{ $coupons->links() }}
    </div>
    @endif
</div>

<!-- Create Coupon Modal -->
<div x-data="{ open: false }" 
     @open-modal.window="if($event.detail === 'create-coupon') open = true"
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    
    <div @click.away="open = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-800">Yeni Kupon Oluştur</h3>
            <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('admin.coupons.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">İndirim Tipi</label>
                <select name="type" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none">
                    <option value="fixed">Sabit TL Tutarı</option>
                    <option value="percent">Yüzdelik (%) İndirim</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">İndirim Değeri</label>
                <div class="relative">
                    <input type="number" name="value" step="0.01" required placeholder="Örn: 100 veya 15" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1.5">Oluşturulacak Adet</label>
                <input type="number" name="count" value="1" min="1" max="50" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all outline-none">
                <p class="text-[10px] text-slate-400 mt-1">Tek seferde maksimum 50 adet oluşturabilirsiniz.</p>
            </div>
            
            <div class="pt-4 flex gap-3">
                <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-bold hover:bg-slate-50 transition-all">Vazgeç</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-brand-500 text-white rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-500/20">Oluştur</button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<script>
    setTimeout(() => {
        notify('success', '{{ session('success') }}');
    }, 500);
</script>
@endif

@if(session('error'))
<script>
    setTimeout(() => {
        notify('error', '{{ session('error') }}');
    }, 500);
</script>
@endif
@endsection
