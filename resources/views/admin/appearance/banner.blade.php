@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appearance') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Banner Yönetimi</h1>
                <p class="text-sm text-slate-500 mt-1">Ana sayfa banner alanını aktif veya pasif hale getirin.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 animate-pulse-soft">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm leading-none">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl overflow-hidden">
        <form action="{{ route('admin.appearance.banner.update') }}" method="POST" class="p-10 space-y-10">
            @csrf
            
            <div class="flex items-center justify-between p-8 bg-slate-50 rounded-3xl border border-slate-100">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-slate-400 shadow-sm">
                        <i class="fas fa-toggle-on text-2xl" :class="$el.nextElementSibling.checked ? 'text-brand-500' : 'text-slate-300'"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-1">Banner Durumu</h3>
                        <p class="text-xs text-slate-500 font-medium leading-relaxed">Kapalı konuma getirildiğinde banner ana sayfada gizlenecektir.</p>
                    </div>
                </div>
                
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="banner_active" value="1" {{ $bannerActive ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-16 h-8 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand-500 shadow-inner"></div>
                </label>
            </div>

            <!-- Banner Preview -->
            <div class="space-y-4">
                <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest pl-2">Mevcut Banner Görünümü</h4>
                <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-50 transition-opacity duration-300" :class="document.querySelector('input[name=banner_active]').checked ? 'opacity-100' : 'opacity-30 grayscale'">
                    <img src="{{ asset('images/banners/main_banner.png') }}" class="w-full h-48 object-cover" alt="Banner Preview">
                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                        <span class="bg-white/10 backdrop-blur-md text-white border border-white/20 px-6 py-2 rounded-xl text-xs font-black italic tracking-tighter uppercase">Önizleme</span>
                    </div>
                </div>
                <p x-show="!document.querySelector('input[name=banner_active]').checked" class="text-[10px] text-rose-500 font-bold italic text-center">Banner şu an pasif durumda ve gizli.</p>
            </div>

            <div class="flex justify-end pt-6 border-t border-slate-50">
                <button type="submit" class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-black italic shadow-2xl hover:bg-brand-600 transition-all transform hover:-translate-y-1 flex items-center gap-3">
                    <i class="fas fa-save text-sm opacity-50"></i>
                    <span>AYARLARI KAYDET</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Instructions Card -->
    <div class="bg-indigo-900 rounded-[32px] p-8 text-white relative overflow-hidden shadow-xl">
        <div class="absolute -right-20 -top-20 text-white/5 text-[180px] pointer-events-none rotate-12">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="relative z-10">
            <h4 class="text-lg font-bold italic tracking-tighter uppercase mb-4">Nasıl Çalışır?</h4>
            <ul class="space-y-3 text-sm text-indigo-100/80 font-medium">
                <li class="flex gap-3"><i class="fas fa-check-circle text-indigo-400 mt-1"></i> Ayarları kaydettiğiniz anda değişiklikler yayına alınır.</li>
                <li class="flex gap-3"><i class="fas fa-check-circle text-indigo-400 mt-1"></i> Banner kapalıyken header ile ürün listesi arasındaki boşluk otomatik optimize edilir.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
