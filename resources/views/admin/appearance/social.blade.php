@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-20">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appearance') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Sosyal Medya & Destek Ayarları</h1>
                <p class="text-sm text-slate-500 mt-1">Sosyal medya linklerinizi, WhatsApp desteği ve uygulama mağazalarını buradan yönetin.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.appearance.social.update') }}" method="POST" class="space-y-8">
        @csrf
        
        <!-- WhatsApp Support Section -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fab fa-whatsapp text-emerald-500 text-xl"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Hızlı Destek Hattı</h4>
                </div>
                <!-- Toggle -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="whatsapp_active" class="sr-only peer" {{ $settings['whatsapp_active'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Görünürlük</span>
                </label>
            </div>
            
            <div class="bg-emerald-50/50 p-6 rounded-3xl border border-emerald-100/50">
                <p class="text-xs text-emerald-800 font-medium leading-relaxed">
                    <i class="fas fa-info-circle mr-1"></i> WhatsApp numaranızı <strong>İletişim Bilgileri</strong> sayfasından güncelleyebilirsiniz. Burada sadece butonun görünürlüğünü yönetirsiniz.
                </p>
            </div>
        </div>

        <!-- Social Media Section -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-share-alt text-blue-500 text-xl"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Sosyal Medya Linkleri</h4>
                </div>
                <!-- Toggle -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="social_active" class="sr-only peer" {{ $settings['social_active'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                    <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Görünürlük</span>
                </label>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Instagram</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"><i class="fab fa-instagram"></i></span>
                        <input type="text" name="instagram" value="{{ $settings['instagram'] }}" placeholder="https://instagram.com/umutmedikal" class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Facebook</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"><i class="fab fa-facebook-f"></i></span>
                        <input type="text" name="facebook" value="{{ $settings['facebook'] }}" placeholder="https://facebook.com/umutmedikal" class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">X / Twitter</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"><i class="fa-brands fa-x-twitter"></i></span>
                        <input type="text" name="twitter" value="{{ $settings['twitter'] }}" placeholder="https://twitter.com/umutmedikal" class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">LinkedIn</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"><i class="fab fa-linkedin-in"></i></span>
                        <input type="text" name="linkedin" value="{{ $settings['linkedin'] }}" placeholder="https://linkedin.com/company/umutmedikal" class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- App Stores Section -->
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10 space-y-8">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-mobile-alt text-indigo-500 text-xl"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Mobil Uygulama Linkleri</h4>
                </div>
                <!-- Toggle -->
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="app_stores_active" class="sr-only peer" {{ $settings['app_stores_active'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                    <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest">Görünürlük</span>
                </label>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Google Play Store</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"><i class="fab fa-google-play"></i></span>
                        <input type="text" name="google_play" value="{{ $settings['google_play'] }}" placeholder="https://play.google.com/store/apps/details?id=..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Apple App Store</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"><i class="fab fa-apple text-xl leading-none"></i></span>
                        <input type="text" name="apple_store" value="{{ $settings['apple_store'] }}" placeholder="https://apps.apple.com/tr/app/..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl pl-12 pr-6 py-4 text-sm font-medium text-slate-600 outline-none focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all">
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-slate-900 text-white px-12 py-5 rounded-[24px] font-black italic shadow-2xl shadow-slate-200 hover:bg-brand-600 transition-all transform hover:-translate-y-1 flex items-center gap-4">
                <i class="fas fa-save opacity-50"></i>
                <span>AYARLARI KAYDET VE YAYINLA</span>
            </button>
        </div>
    </form>
</div>
@endsection
