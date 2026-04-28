@extends('layouts.app')

@section('title', 'Ödeme Başarılı')

@section('content')
<div class="bg-gray-50/50 min-h-screen py-20">
    <div class="ty-container max-w-4xl">
        <div class="bg-white rounded-[50px] p-12 md:p-20 shadow-2xl border border-gray-100 text-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-green-50 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10">
                <div class="w-24 h-24 bg-green-100 text-green-600 rounded-[35px] flex items-center justify-center mx-auto mb-10 shadow-lg shadow-green-100/50 transform hover:scale-110 transition-transform">
                    <i class="fas fa-check text-4xl"></i>
                </div>

                <h2 class="text-4xl font-black text-slate-900 uppercase italic tracking-tighter mb-4">Ödeme Başarılı!</h2>
                <p class="text-gray-500 font-bold mb-12">Siparişiniz başarıyla alındı ve ödemeniz onaylandı.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16 px-4">
                    <div class="bg-gray-50 p-8 rounded-[35px] border border-gray-100 group hover:bg-slate-900 hover:text-white transition-all duration-500">
                        <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 group-hover:text-white/50 mb-2">Sipariş Numarası</p>
                        <p class="text-3xl font-black italic tracking-tighter">#{{ $order->id }}</p>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-[35px] border border-gray-100 group hover:bg-slate-900 hover:text-white transition-all duration-500">
                        <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 group-hover:text-white/50 mb-2">Toplam Tutar</p>
                        <p class="text-3xl font-black italic tracking-tighter">{{ number_format($order->total_price, 2) }} TL</p>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-[40px] p-10 mb-12 flex items-start gap-6 text-left">
                    <div class="w-12 h-12 bg-blue-500 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/20">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-blue-900 uppercase italic tracking-tighter mb-1">Siparişiniz Hazırlanıyor</h4>
                        <p class="text-sm text-blue-700 font-bold leading-relaxed">Ödemeniz onaylandı. Kargo takip bilgileri en kısa sürede tarafınıza e-posta ile iletilecektir.</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                    <a href="{{ route('user.orders') }}" class="w-full md:w-auto px-12 py-5 bg-slate-900 text-white rounded-[25px] font-black italic shadow-xl shadow-slate-900/20 hover:bg-orange-600 transition-all active:scale-95 uppercase tracking-tighter">Siparişlerimi Görüntüle</a>
                    <a href="{{ route('home') }}" class="w-full md:w-auto px-12 py-5 bg-gray-100 text-slate-900 rounded-[25px] font-black italic hover:bg-gray-200 transition-all active:scale-95 uppercase tracking-tighter">Alışverişe Devam Et</a>
                </div>

                <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-center gap-12 grayscale opacity-40">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt text-2xl"></i>
                        <span class="text-[10px] font-black uppercase tracking-tighter">GÜVENLİ ÖDEME SİSTEMİ</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-truck text-2xl"></i>
                        <span class="text-[10px] font-black uppercase tracking-tighter">HIZLI KARGO</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
