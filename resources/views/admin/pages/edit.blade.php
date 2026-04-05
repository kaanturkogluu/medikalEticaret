@extends('layouts.admin')

@section('title', 'Metni Düzenle: ' . $page->title)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center mb-8">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-brand-500 decoration-4 underline-offset-8">Metni Düzenle</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium italic"><span class="font-black text-slate-900 uppercase">{{ $page->title }}</span> içeriğini güncelliyorsunuz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.pages.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-sm font-black italic tracking-tighter uppercase rounded-xl bg-white shadow-xl shadow-slate-200/50 text-slate-600 hover:bg-slate-50 transition-all transform hover:scale-105 active:scale-95">
                <i class="fas fa-chevron-left mr-2"></i> Listeye Dön
            </a>
        </div>
    </div>

    <form action="{{ route('admin.pages.update', $page->slug) }}" method="POST" class="space-y-8 bg-white/50 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 overflow-hidden relative group">
        @csrf
        @method('PUT')
        
        <div class="relative space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="title" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">Sayfa Başlığı</label>
                    <input type="text" name="title" id="title" required value="{{ old('title', $page->title) }}"
                           class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all text-center">
                    @error('title') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                </div>
                
                <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100 h-full">
                    <div class="h-10 w-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-brand-500">
                        <i class="fas fa-power-off"></i>
                    </div>
                    <div class="flex-grow text-center">
                        <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase">Aktif Durum</h4>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $page->is_active ? 'checked' : '' }}>
                        <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                    </label>
                </div>
            </div>

            <div>
                <label for="content" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">İçerik (HTML desteklenir)</label>
                <textarea name="content" id="content" rows="15"
                          class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-3xl text-sm font-medium text-slate-700 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition-all min-h-[400px]">{{ old('content', $page->content) }}</textarea>
                @error('content') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                <p class="mt-3 text-xs text-slate-400 font-bold italic uppercase tracking-widest leading-relaxed">Not: HTML etiketlerini kullanarak içeriği biçimlendirebilirsiniz. Gelecek güncellemede buraya görsel bir editör eklenecektir.</p>
            </div>

            <div class="pt-6 border-t border-slate-100 flex gap-4 text-center">
                <button type="submit" class="flex-grow px-10 py-5 bg-slate-900 text-white font-black italic uppercase tracking-tighter rounded-2xl shadow-2xl shadow-slate-200/50 hover:bg-slate-800 transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                    <i class="fas fa-save text-brand-400"></i> İçeriği Kaydet
                </button>
                <a href="{{ route('admin.pages.index') }}" class="px-10 py-5 bg-gray-100 text-gray-500 font-black italic uppercase tracking-tighter rounded-2xl hover:bg-gray-200 transition-all">Vazgeç</a>
            </div>
        </div>
    </form>
</div>
@endsection
