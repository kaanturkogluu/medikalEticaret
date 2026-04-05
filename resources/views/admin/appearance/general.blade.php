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
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Genel Görünüm & Footer Ayarları</h1>
                <p class="text-sm text-slate-500 mt-1">Site ana renkleri, başlık ve alt bilgi (footer) linklerini yönetin.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.appearance.general.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- Style Section -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <i class="fas fa-palette text-brand-500"></i>
                <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Site Stili & Temel Bilgiler</h4>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Site Başlığı (Title)</label>
                    <input type="text" name="site_title" value="{{ $settings['site_title'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Ana Marka Rengi (Primary Color)</label>
                    <div class="flex gap-4">
                        <input type="color" name="primary_color" value="{{ $settings['primary_color'] }}" class="w-20 h-14 rounded-2xl border border-slate-200 p-1 cursor-pointer bg-white">
                        <input type="text" readonly value="{{ $settings['primary_color'] }}" class="flex-grow bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-mono text-slate-500">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-slate-50">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Footer QR Kod Linki (ETBIS vb.)</label>
                    <div class="flex gap-4">
                        <input type="text" name="footer_qr" value="{{ $settings['footer_qr'] }}" class="flex-grow bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-xs font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all" placeholder="QR Kod görsel linkini buraya yapıştırın">
                        <div class="w-14 h-14 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($settings['footer_qr'])
                                <img src="{{ $settings['footer_qr'] }}" class="w-full h-full object-contain p-1">
                            @else
                                <i class="fas fa-qrcode text-slate-300"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Links Section -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8"
             x-data="{ 
                columns: {{ json_encode($settings['footer_columns']) }},
                addColumn() { 
                    if(this.columns.length < 4) {
                        this.columns.push({ title: 'YENİ SÜTUN', links: [{ text: 'Örnek Link', url: '#' }] }) 
                    }
                },
                removeColumn(i) { this.columns.splice(i, 1) },
                addLink(colIdx) { this.columns[colIdx].links.push({ text: 'Yeni Link', url: '#' }) },
                removeLink(colIdx, linkIdx) { this.columns[colIdx].links.splice(linkIdx, 1) }
             }">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-list-ul text-brand-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Footer Link Sütunları</h4>
                </div>
                <button type="button" @click="addColumn()" x-show="columns.length < 4" class="px-5 py-2 bg-slate-900 text-white rounded-full text-[10px] font-black uppercase italic tracking-tighter hover:bg-brand-600 transition-all shadow-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i> SÜTUN EKLE
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <template x-for="(col, i) in columns" :key="i">
                    <div class="bg-slate-50 border border-slate-100 rounded-[32px] p-8 space-y-6 relative group">
                        <button type="button" @click="removeColumn(i)" class="absolute -right-2 -top-2 w-8 h-8 bg-rose-500 text-white rounded-xl shadow-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="fas fa-times text-xs"></i>
                        </button>

                        <div class="space-y-2">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-2">Sütun Başlığı</label>
                            <input type="text" :name="'footer_columns['+i+'][title]'" x-model="col.title" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-black italic tracking-tighter outline-none focus:border-brand-500 transition-all uppercase">
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest pl-2">Linkler</label>
                                <button type="button" @click="addLink(i)" class="text-[9px] font-black text-brand-500 uppercase hover:underline">Link Ekle</button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(link, j) in col.links" :key="j">
                                    <div class="flex gap-2 items-center">
                                        <input type="text" :name="'footer_columns['+i+'][links]['+j+'][text]'" x-model="link.text" placeholder="Görünecek Metin" class="w-1/2 bg-white border border-slate-100 rounded-lg px-3 py-2 text-[10px] font-bold">
                                        <input type="text" :name="'footer_columns['+i+'][links]['+j+'][url]'" x-model="link.url" placeholder="URL" class="w-1/2 bg-white border border-slate-100 rounded-lg px-3 py-2 text-[10px] font-medium text-slate-400">
                                        <button type="button" @click="removeLink(i, j)" class="text-rose-400 hover:text-rose-600 px-1"><i class="fas fa-trash-alt text-[10px]"></i></button>
                                    </div>
                                </template>
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
                <span>TÜM ARAYÜZ AYARLARINI KAYDET</span>
            </button>
        </div>
    </form>
</div>
@endsection
