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
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Popüler Ürünler Ayarları</h1>
                <p class="text-sm text-slate-500 mt-1">Ana sayfada yer alan popüler ürünler (vitrin) bölümünün başlık ve görünüm ayarlarını yapın.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.appearance.popular.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-fire text-orange-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Popüler Ürünler Bölümü</h4>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="active" value="1" {{ $settings['active'] ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                    <span class="ml-3 text-xs font-bold text-slate-500 uppercase tracking-widest">Aktif / Pasif</span>
                </label>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Bölüm Başlığı</label>
                    <input type="text" name="title" value="{{ $settings['title'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all" placeholder="Örn: Popüler Ürünler">
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Bölüm Alt Başlığı</label>
                    <input type="text" name="subtitle" value="{{ $settings['subtitle'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all" placeholder="Örn: En Çok Tercih Edilenler">
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Maksimum Ürün Sayısı</label>
                    <input type="number" name="max_items" value="{{ $settings['max_items'] }}" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all" placeholder="10">
                    <p class="text-[10px] text-slate-400 px-2 mt-1">Vitrin alanında yan yana kaydırılabilir kaç ürün listeleneceğini belirler.</p>
                </div>
            </div>

            <!-- Manual Selection Reminder -->
            <div class="mt-10 p-8 bg-orange-50 rounded-[32px] border border-orange-100 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 text-orange-200/20 text-[120px] pointer-events-none rotate-12">
                    <i class="fas fa-star"></i>
                </div>
                <div class="relative z-10 flex items-start gap-4">
                    <div class="w-12 h-12 bg-white rounded-2xl shadow-sm flex items-center justify-center text-orange-500 shrink-0">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-black text-orange-900 italic uppercase tracking-tighter mb-1">Popüler Ürünleri Nasıl Seçerim?</h5>
                        <p class="text-xs text-orange-700 font-medium leading-relaxed">
                            Ürünleri tek tek "Popüler" olarak işaretlemek için 
                            <a href="{{ route('admin.products') }}" class="font-bold underline hover:text-orange-900 transition-colors">Ürün Yönetimi</a> 
                            sayfasına gidin. Ürün görselinin üzerindeki <strong>yıldız ikonuna</strong> tıklayarak dilediğiniz ürünü anında vitrine ekleyebilirsiniz. 
                            Hiç ürün seçilmezse sistem en çok görüntülenenleri otomatik getirir.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end p-4">
            <button type="submit" class="bg-orange-600 text-white px-12 py-5 rounded-[24px] font-black italic shadow-2xl shadow-orange-100 hover:bg-slate-900 transition-all transform hover:-translate-y-1 flex items-center gap-4">
                <i class="fas fa-check-double opacity-50"></i>
                <span> AYARLARI KAYDET</span>
            </button>
        </div>
    </form>
</div>
@endsection
