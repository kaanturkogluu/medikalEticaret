@extends('layouts.app')

@section('title', 'Güvenli Ödeme')

@section('content')
<div class="min-h-screen py-12 bg-slate-50">
    <div class="ty-container max-w-4xl">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <!-- Payment Section -->
            <div class="flex-grow w-full lg:w-2/3">
                <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 overflow-hidden border border-slate-100">
                    <div class="bg-slate-900 p-8 text-white relative overflow-hidden">
                        <!-- Decorative Background -->
                        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-64 h-64 bg-orange-500/10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl"></div>
                        
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <a href="{{ url()->previous() }}" class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors group">
                                    <i class="fas fa-arrow-left text-sm text-white group-hover:-translate-x-1 transition-transform"></i>
                                </a>
                                <div>
                                    <h1 class="text-2xl font-black italic tracking-tighter uppercase">Güvenli Ödeme</h1>
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1 italic">Iyzico Altyapısı ile Korunmaktadır</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md">
                                    <i class="fas fa-shield-alt text-xl text-green-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="mb-8 p-6 bg-blue-50 rounded-2xl border border-blue-100 flex items-start gap-4 italic">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm shrink-0">
                                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-blue-900">Sayın {{ $order->customer_name }},</p>
                                <p class="text-xs text-blue-700 mt-1 leading-relaxed font-medium">Toplam <span class="text-blue-900 font-black">{{ number_format($order->total_price, 2) }} {{ $order->currency }}</span> tutarındaki ödemenizi aşağıdan kart bilgilerinizle güvenle tamamlayabilirsiniz.</p>
                            </div>
                        </div>

                        <!-- Iyzico Form Container -->
                        <div id="iyzipay-checkout-form" class="responsive min-h-[400px]">
                            {!! $paymentContent !!}
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex flex-col items-center justify-center gap-4">
                        <div class="flex items-center gap-6 opacity-50 grayscale hover:grayscale-0 transition-all">
                            <img src="https://www.iyzico.com/assets/images/iyzico-logo.svg" alt="iyzico" class="h-6">
                            <div class="h-4 w-px bg-slate-300"></div>
                            <div class="flex items-center gap-2">
                                <i class="fab fa-cc-visa text-xl"></i>
                                <i class="fab fa-cc-mastercard text-xl"></i>
                                <i class="fab fa-cc-amex text-xl"></i>
                            </div>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 text-center italic">Tüm kredi kartları ile 12 aya varan taksit seçenekleri</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="w-full lg:w-1/3 shrink-0">
                <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/40 p-6 border border-slate-100 sticky top-24">
                    <h3 class="text-sm font-black uppercase italic tracking-tighter text-slate-900 mb-6 border-b border-slate-100 pb-4">Sipariş Özeti</h3>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-500 italic">
                            <span>Sipariş No</span>
                            <span class="text-slate-900">#{{ $order->id }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-slate-500 italic">
                            <span>Tarih</span>
                            <span class="text-slate-900">{{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4 max-h-64 overflow-y-auto custom-scrollbar pr-2 mb-6">
                        @foreach($order->items as $item)
                        <div class="flex gap-3 items-center py-2 border-b border-slate-50 last:border-0">
                            <div class="w-12 h-12 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($item->product && $item->product->productImages->count() > 0)
                                    <img src="{{ asset($item->product->productImages->first()->url) }}" class="w-full h-full object-contain p-1">
                                @else
                                    <i class="fas fa-box text-slate-200"></i>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-[10px] font-black uppercase italic text-slate-900 truncate">{{ $item->product->name ?? 'Ürün' }}</p>
                                <p class="text-[9px] font-bold text-slate-400 mt-0.5 italic">{{ $item->quantity }} Adet x {{ number_format($item->price, 2) }} {{ $order->currency }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <div class="flex justify-between items-center text-xs font-bold text-slate-500 italic">
                            <span>Ara Toplam</span>
                            <span class="text-slate-900">{{ number_format($order->total_price - $order->shipping_price, 2) }} {{ $order->currency }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs font-bold text-slate-500 italic">
                            <span>Kargo</span>
                            <span class="{{ $order->shipping_price == 0 ? 'text-green-600' : 'text-slate-900' }}">
                                {{ $order->shipping_price == 0 ? 'Ücretsiz' : number_format($order->shipping_price, 2) . ' ' . $order->currency }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-3 mt-3 border-t-2 border-slate-900">
                            <span class="text-sm font-black uppercase italic tracking-tighter">Toplam</span>
                            <span class="text-xl font-black italic tracking-tighter text-slate-900">{{ number_format($order->total_price, 2) }} {{ $order->currency }}</span>
                        </div>
                    </div>
                </div>

                <!-- Trust Badge -->
                <div class="mt-6 p-4 bg-green-50 rounded-2xl border border-green-100 flex items-center gap-3 italic">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm shrink-0">
                        <i class="fas fa-lock text-green-500 text-sm"></i>
                    </div>
                    <p class="text-[9px] font-bold text-green-800 leading-tight">Kart bilgileriniz sistemimizde saklanmaz ve 256-bit SSL ile şifrelenerek iyzico'ya iletilir.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Iyzico Form overrides to ensure it fits our premium theme */
    #iyzipay-checkout-form {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 auto !important;
    }
    
    /* Force iyzico's internal container to be full width */
    #iyzipay-checkout-form > div {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Custom scrollbar for the sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endsection
