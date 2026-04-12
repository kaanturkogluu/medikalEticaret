@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.return-templates.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
            <i class="fas fa-chevron-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Yeni İade Şablonu</h1>
            <p class="text-sm text-slate-500 mt-1">Belirli ürün grupları için iade kurallarını belirleyin.</p>
        </div>
    </div>

    <form action="{{ route('admin.return-templates.store') }}" method="POST">
        @csrf
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-10">
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Şablon İsmi</label>
                <input type="text" name="name" placeholder="Örn: Mobilya / Sandalye Grubu" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-8 py-5 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all outline-none" required>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">İade Koşulları (Her satır bir madde)</label>
                    <span class="text-[9px] text-amber-500 font-bold italic uppercase tracking-tighter">İpucu: Her kuralı yeni bir satıra yazın.</span>
                </div>
                <textarea name="content" rows="12" class="w-full bg-slate-50 border border-slate-200 rounded-[32px] px-8 py-8 text-sm font-medium text-slate-600 focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all outline-none leading-relaxed" placeholder="Ürün kırılmamış olmalıdır.&#10;Orijinal kutusuyla iade edilmelidir.&#10;Kurulum yapıldıktan sonra iade kabul edilememektedir." required></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-slate-900 text-white px-12 py-5 rounded-[24px] font-black italic shadow-2xl shadow-indigo-100 hover:bg-brand-600 transition-all transform hover:-translate-y-1 flex items-center gap-4">
                    <i class="fas fa-save opacity-50"></i>
                    <span>ŞABLONU OLUŞTUR</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
