@extends('layouts.admin')

@section('content')
<div class="space-y-8 pb-20">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase">Sıkça Sorulan Sorular</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Müşteri sorularını yönetin, sıralayın ve yayınlayın.</p>
        </div>
        <a href="{{ route('admin.faqs.create') }}" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black italic uppercase tracking-tighter hover:bg-brand-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-3">
            <i class="fas fa-plus-circle"></i> YENİ SORU EKLE
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 animate-bounce">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Sıra</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Soru</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Durum</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <span class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-[11px] font-black text-slate-500">#{{ $faq->order_index }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="max-w-md">
                                <h4 class="font-bold text-slate-900 text-sm leading-snug">{{ $faq->question }}</h4>
                                <p class="text-[10px] text-slate-400 line-clamp-1 mt-1">{{ strip_tags($faq->answer) }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <form action="{{ route('admin.faqs.toggle', $faq) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border transition-all {{ $faq->is_active ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $faq->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                                    {{ $faq->is_active ? 'Aktif' : 'Pasif' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-rose-500 hover:border-rose-500 transition-all shadow-sm">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-200">
                                    <i class="fas fa-question-circle text-2xl"></i>
                                </div>
                                <p class="text-xs font-black text-slate-300 uppercase italic tracking-widest">Henüz hiç soru eklenmedi.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
