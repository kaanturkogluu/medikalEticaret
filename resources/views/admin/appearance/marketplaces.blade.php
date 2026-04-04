@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-20">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appearance') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Pazaryeri & Üst Bar Ayarları</h1>
                <p class="text-sm text-slate-500 mt-1">Mağaza linklerinizi ve kayan yazı alanını buradan yönetin.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.appearance.marketplaces.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Marquee Text Section -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <i class="fas fa-bullhorn text-brand-500"></i>
                <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Kayan Yazı (Marquee)</h4>
            </div>
            
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Görünecek Metin</label>
                <textarea name="marquee_text" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-3xl px-8 py-6 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-8 focus:ring-brand-50 focus:border-brand-500 transition-all resize-none leading-relaxed">{{ $marqueeText }}</textarea>
                <p class="text-[10px] text-slate-400 italic font-medium px-2">Not: Haberler veya kampanyalar arasında nokta (•) veya tire (-) kullanarak ayırmanızı öneririz.</p>
            </div>
        </div>

        <!-- Marketplaces Repeater -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8" 
             x-data="{ 
                marketplaces: {{ json_encode($marketplaces) }},
                add() { this.marketplaces.push({ name: 'YENİ MAĞAZA', url: '#', logo: '', color: '#000000' }) },
                remove(i) { this.marketplaces.splice(i, 1) }
             }">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-store-alt text-brand-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Pazaryeri Linkleri</h4>
                </div>
                <button type="button" @click="add()" class="px-5 py-2 bg-slate-900 text-white rounded-full text-[10px] font-black uppercase italic tracking-tighter hover:bg-brand-600 transition-all shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i> YENİ EKLE
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="(mp, i) in marketplaces" :key="i">
                    <div class="bg-slate-50/50 border border-slate-100 rounded-3xl p-6 relative group transition-all hover:bg-white hover:shadow-xl">
                        <button type="button" @click="remove(i)" class="absolute -right-3 -top-3 w-8 h-8 bg-rose-500 text-white rounded-xl shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-1">Mağaza Adı</label>
                                    <input type="text" :name="'marketplaces['+i+'][name]'" x-model="mp.name" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-black italic tracking-tighter outline-none focus:border-brand-500 transition-all uppercase">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-1">Marka Rengi</label>
                                    <input type="color" :name="'marketplaces['+i+'][color]'" x-model="mp.color" class="w-full h-10 rounded-xl border border-slate-200 p-1 cursor-pointer bg-white">
                                </div>
                            </div>
                            
                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-1">Mağaza Linki (URL)</label>
                                <input type="text" :name="'marketplaces['+i+'][url]'" x-model="mp.url" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-medium outline-none focus:border-brand-500 transition-all">
                            </div>

                            <div class="space-y-1">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-1">Favicon / Logo Linki</label>
                                <div class="flex gap-3">
                                    <input type="text" :name="'marketplaces['+i+'][logo]'" x-model="mp.logo" class="flex-grow bg-white border border-slate-200 rounded-xl px-4 py-3 text-[10px] lowercase outline-none focus:border-brand-500 transition-all">
                                    <div class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <img :src="mp.logo" x-show="mp.logo" class="w-5 h-5 object-contain">
                                        <i class="fas fa-image text-slate-200" x-show="!mp.logo"></i>
                                    </div>
                                </div>
                                <p class="text-[8px] text-slate-400 italic mt-1 font-medium pl-1">Örn: https://www.google.com/s2/favicons?domain=trendyol.com&sz=128</p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end p-4">
            <button type="submit" class="bg-indigo-600 text-white px-12 py-5 rounded-[24px] font-black italic shadow-2xl shadow-indigo-100 hover:bg-slate-900 transition-all transform hover:-translate-y-1 flex items-center gap-4">
                <i class="fas fa-check-double opacity-50"></i>
                <span>TÜM GÜNCELLEMELERİ YAYINLA</span>
            </button>
        </div>
    </form>
</div>
@endsection
