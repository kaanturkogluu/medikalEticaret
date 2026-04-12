@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight italic uppercase">İade Şablonları</h2>
            <p class="text-sm text-slate-500 mt-1">Ürün tiplerine göre özelleştirilmiş iade kuralları oluşturun.</p>
        </div>
        <a href="{{ route('admin.return-templates.create') }}" class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase italic tracking-widest hover:bg-brand-600 transition-all shadow-xl flex items-center gap-3">
            <i class="fas fa-plus text-[10px]"></i> YENİ ŞABLON EKLE
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                        <th class="px-8 py-6">Şablon Adı</th>
                        <th class="px-8 py-6">Kural Sayısı</th>
                        <th class="px-8 py-6">Son Güncelleme</th>
                        <th class="px-8 py-6 text-right">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($templates as $template)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-6">
                                <span class="text-sm font-black text-slate-900 uppercase italic tracking-tighter">{{ $template->name }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-lg text-[10px] font-black italic uppercase">
                                    {{ count(explode("\n", trim($template->content))) }} Madde
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-widest">{{ $template->updated_at->format('d.m.Y H:i') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.return-templates.edit', $template) }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 hover:border-brand-500 transition-all shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.return-templates.destroy', $template) }}" method="POST" onsubmit="return confirm('Bu şablonu silmek istediğinize emin misiniz?');">
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
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-slate-200">
                                        <i class="fas fa-file-contract text-4xl"></i>
                                    </div>
                                    <p class="text-slate-400 text-sm font-bold uppercase tracking-widest">Henüz iade şablonu oluşturulmadı.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
