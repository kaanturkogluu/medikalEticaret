@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.faqs.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tighter italic uppercase">SORUYU DÜZENLE</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Soru ve cevabı güncelleyin.</p>
        </div>
    </div>

    <form action="{{ route('admin.faqs.update', $faq) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-100 p-10 space-y-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Soru Metni</label>
                <input type="text" name="question" required value="{{ old('question', $faq->question) }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Cevap Metni</label>
                <textarea name="answer" required rows="6" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">{{ old('answer', $faq->answer) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Görüntüleme Sırası (Opsiyonel)</label>
                    <input type="number" name="order_index" value="{{ old('order_index', $faq->order_index) }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Yayın Durumu</label>
                    <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">Aktif Yayınla</span>
                        <input type="checkbox" name="is_active" value="1" {{ $faq->is_active ? 'checked' : '' }} class="w-5 h-5 accent-emerald-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-brand-600 text-white px-12 py-5 rounded-[24px] font-black italic shadow-2xl shadow-brand-100 hover:bg-slate-900 transition-all transform hover:-translate-y-1 flex items-center gap-4">
                <i class="fas fa-check-double opacity-50"></i>
                <span>GÜNCELLEMELERİ KAYDET</span>
            </button>
        </div>
    </form>
</div>
@endsection
