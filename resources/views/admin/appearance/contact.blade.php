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
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">İletişim Bilgileri</h1>
                <p class="text-sm text-slate-500 mt-1">Sitedeki tüm iletişim kanallarını ve harita konumunu buradan güncelleyin.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl overflow-hidden">
        <form action="{{ route('admin.appearance.contact.update') }}" method="POST" class="p-10 space-y-10">
            @csrf
            
            <!-- Basic Contact Info -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <i class="fas fa-address-card text-brand-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Genel Bilgiler</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Telefon Numarası</label>
                        <input type="text" name="phone" value="{{ $settings['phone'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">WhatsApp Numarası (Örn: 90530...)</label>
                        <input type="text" name="whatsapp" value="{{ $settings['whatsapp'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">E-Posta Adresi</label>
                        <input type="email" name="email" value="{{ $settings['email'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="space-y-6">
                <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                    <i class="fas fa-map-marked-alt text-brand-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Adres ve Konum</h4>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Mağaza Adresi</label>
                    <textarea name="address" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-900 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all resize-none">{{ $settings['address'] }}</textarea>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Google Maps Paylaşım Linki (iframe 'src' değeri)</label>
                    <input type="text" name="maps" value="{{ $settings['maps'] }}" placeholder="https://www.google.com/maps/embed?..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-xs font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    <p class="text-[9px] text-slate-400 italic mt-1">Google Haritalar'dan "Paylaş" -> "Harita Yerleştir" kısmındaki iframe'in 'src' tırnakları arasındaki linki buraya yapıştırın.</p>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-8 border-t border-slate-50">
                <button type="submit" class="bg-slate-900 text-white px-10 py-5 rounded-[20px] font-black italic shadow-2xl shadow-slate-200 hover:bg-brand-600 transition-all transform hover:-translate-y-1 flex items-center gap-3">
                    <i class="fas fa-save text-lg opacity-50"></i>
                    <span>TÜM DEĞİŞİKLİKLERİ KAYDET</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
