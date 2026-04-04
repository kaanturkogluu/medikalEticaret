@extends('layouts.app')

@section('title', 'İletişim')

@section('content')
<div class="bg-gray-50/50 py-16">
    <div class="ty-container">
        <!-- Page Header -->
        <div class="text-center mb-16 px-4">
            <div class="inline-block bg-[var(--primary-color)]/10 text-[var(--primary-color)] px-4 py-1.5 rounded-full text-xs font-black uppercase italic tracking-widest mb-4">
                Bize Ulaşın
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 italic tracking-tighter mb-4">İLETİŞİM BİLGİLERİ</h1>
            <p class="text-slate-500 font-medium max-w-2xl mx-auto leading-relaxed">
                Her türlü soru, öneri veya iş birliği talebiniz için bizimle iletişime geçebilirsiniz. 
                Uzman ekibimiz size yardımcı olmaktan mutluluk duyacaktır.
            </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Contact Info Cards -->
            <div class="w-full lg:w-1/3 flex flex-col gap-6">
                <!-- Phone Card -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                    <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform group-hover:bg-blue-500 group-hover:text-white">
                        <i class="fas fa-phone-alt text-xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 italic tracking-tighter mb-2">TELEFON</h3>
                    <p class="text-slate-500 text-sm font-medium mb-4">Hafta içi 09:00 - 18:00 saatleri arasında bize ulaşabilirsiniz.</p>
                    <a href="tel:+905555555555" class="text-xl font-black text-blue-600 hover:underline">+90 555 555 55 55</a>
                </div>

                <!-- WhatsApp Card -->
                <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                    <div class="w-14 h-14 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform group-hover:bg-green-500 group-hover:text-white">
                        <i class="fab fa-whatsapp text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 italic tracking-tighter mb-2">WHATSAPP</h3>
                    <p class="text-slate-500 text-sm font-medium mb-4">7/24 mesaj yoluyla destek alabilirsiniz.</p>
                    <a href="https://wa.me/905555555555" target="_blank" class="bg-[#25D366] text-white px-6 py-3 rounded-xl font-black italic shadow-lg shadow-green-100 inline-flex items-center gap-3 hover:bg-[#128C7E] transition-all">
                        <span>MESAJ ATIN</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <!-- Email & Address -->
                <div class="bg-slate-900 p-10 rounded-3xl text-white relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 text-white/5 text-[150px]">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-black italic tracking-tighter mb-6 text-[var(--primary-color)]">ADRES BİLGİSİ</h3>
                        <p class="text-white/70 text-sm font-medium leading-relaxed mb-8">
                            Umut Medikal Ürünler San. ve Tic. Ltd. Şti.<br>
                            Merkez Mah. Sağlık Cad. No:123/A<br>
                            İskenderun / HATAY
                        </p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-widest">
                                <i class="far fa-envelope text-[var(--primary-color)] text-lg"></i>
                                <span>info@umutmed.com</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map Area -->
            <div class="flex-grow">
                <div class="bg-white p-4 rounded-[40px] border border-gray-100 shadow-2xl h-full min-h-[500px] relative">
                    <div class="w-full h-full rounded-[30px] overflow-hidden grayscale-[0.2] hover:grayscale-0 transition-all duration-700 ring-4 ring-gray-50">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2258.231631379967!2d36.20961572777762!3d36.82758169743837!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x152f3f7dbcdb71e9%3A0x9173dfcc5adb2a23!2sumut%20medikal!5e0!3m2!1str!2str!4v1775334764993!5m2!1str!2str" 
                                class="w-full h-full" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                    
                    <!-- Floating Map Badge -->
                    <a href="https://www.google.com/maps/dir//umut+medikal,+Sava%C5%9F,+Sa%C4%9Fl%C4%B1k+Cd.+No:41,+31200+%C4%B0skenderun%2FHatay/@36.5862214,36.1666872,17z/data=!4m18!1m8!3m7!1s0x152f3f7dbcdb71e9:0x9173dfcc5adb2a23!2sumut+medikal!8m2!3d36.5862214!4d36.1666872!10e5!16s%2Fg%2F11b6_v2q7x!4m8!1m0!1m5!1m1!1s0x152f3f7dbcdb71e9:0x9173dfcc5adb2a23!2m2!1d36.1666872!2d36.5862214!3e3?entry=ttu" target="_blank" class="absolute bottom-10 left-10 bg-white/80 backdrop-blur-md px-6 py-4 rounded-2xl shadow-2xl border border-white/50 flex items-center gap-4 group hover:bg-slate-900 transition-all duration-500">
                        <div class="w-12 h-12 bg-[var(--primary-color)] text-white rounded-xl flex items-center justify-center shadow-lg group-hover:rotate-12 transition-transform">
                            <i class="fas fa-location-arrow"></i>
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-white/50">Mağaza Konumu</div>
                            <div class="text-sm font-black text-slate-900 italic tracking-tighter group-hover:text-white">Yol Tarifi Alın <i class="fas fa-chevron-right text-[8px] ml-1"></i></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
