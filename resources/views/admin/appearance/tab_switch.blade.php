@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appearance') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Sekme Başlık Ayarları</h1>
                <p class="text-sm text-slate-500 mt-1">Kullanıcı başka sekmeye geçtiğinde tarayıcı başlığının nasıl değişeceğini ayarlayın.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.appearance.tab_switch.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-window-restore text-brand-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Başlık Değiştirme Özelliği</h4>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="active" value="1" {{ $settings['active'] ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                    <span class="ml-3 text-xs font-bold text-slate-500 uppercase tracking-widest">Aktif / Pasif</span>
                </label>
            </div>
            
            <div class="grid grid-cols-1 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Gidince Görünecek Yazı (Away Title)</label>
                    <input type="text" name="away_title" value="{{ $settings['away_title'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all" placeholder="Örn: Bizi Unutma! 😢">
                    <p class="text-[10px] text-slate-400 px-2 mt-1">Kullanıcı başka bir sekmeye geçtiğinde tarayıcı sekmesinde bu yazı görünecektir.</p>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Dönünce Görünecek Yazı (Back Title)</label>
                    <input type="text" name="back_title" value="{{ $settings['back_title'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all" placeholder="Örn: Hoş Geldin! 😍">
                    <p class="text-[10px] text-slate-400 px-2 mt-1">Kullanıcı sekmeye geri döndüğünde kısa süreliğine bu yazı görünür ve ardından normal başlığa döner.</p>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="mt-10 p-8 bg-slate-50 rounded-[32px] border border-slate-100">
                <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-eye"></i> ÖNİZLEME (NASIL ÇALIŞIR?)
                </h5>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="w-8 h-8 bg-brand-500 rounded-lg flex items-center justify-center text-white text-xs">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div class="flex-1">
                            <div class="h-2 w-24 bg-slate-100 rounded-full mb-2"></div>
                            <div class="h-2 w-40 bg-slate-50 rounded-full"></div>
                        </div>
                        <span class="text-[10px] font-bold text-brand-600 bg-brand-50 px-3 py-1 rounded-full uppercase tracking-tighter">Aktif Sekme</span>
                    </div>
                    <div class="flex items-center gap-4 bg-slate-200/50 p-4 rounded-2xl border border-dashed border-slate-300 opacity-60">
                        <div class="w-8 h-8 bg-slate-300 rounded-lg flex items-center justify-center text-white text-xs">
                            <i class="fas fa-ghost"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-bold text-slate-600 italic">"{{ $settings['away_title'] }}"</p>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 px-3 py-1 rounded-full uppercase tracking-tighter">Başka Sekme</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end p-4">
            <button type="submit" class="bg-indigo-600 text-white px-12 py-5 rounded-[24px] font-black italic shadow-2xl shadow-indigo-100 hover:bg-slate-900 transition-all transform hover:-translate-y-1 flex items-center gap-4">
                <i class="fas fa-check-double opacity-50"></i>
                <span> AYARLARI KAYDET</span>
            </button>
        </div>
    </form>
</div>
@endsection
