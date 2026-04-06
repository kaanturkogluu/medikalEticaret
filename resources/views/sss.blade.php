@extends('layouts.app')

@section('title', 'Sıkça Sorulan Sorular')

@section('content')
<div class="bg-slate-50 min-h-screen py-16 px-4">
    <div class="max-w-4xl mx-auto space-y-12">
        <!-- Header -->
        <div class="text-center space-y-4">
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-sm border border-slate-100 mb-4 animate-bounce">
                <i class="fas fa-question-circle text-orange-500"></i>
                <span class="text-[10px] font-black text-slate-900 uppercase tracking-widest italic">Yardım Merkezi</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tighter italic uppercase underline decoration-orange-500 decoration-8 underline-offset-[12px]">Sıkça Sorulan Sorular</h1>
            <p class="text-slate-500 text-sm font-medium italic max-w-xl mx-auto leading-relaxed pt-4">Size daha hızlı yardımcı olabilmek için en çok sorulan soruları bir araya getirdik. Merak ettiğiniz diğer konular için bizimle iletişime geçebilirsiniz.</p>
        </div>

        <!-- FAQ Accordion -->
        <div class="space-y-4" x-data="{ active: null }">
            @forelse($faqs as $faq)
                <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden transition-all duration-500"
                     :class="active === {{ $faq->id }} ? 'ring-2 ring-orange-500/20' : ''">
                    <button @click="active = (active === {{ $faq->id }} ? null : {{ $faq->id }})"
                            class="w-full text-left px-8 py-6 flex items-center justify-between gap-6 group">
                        <div class="flex items-center gap-4">
                           <span class="w-10 h-10 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center font-black italic text-xs group-hover:bg-orange-500 group-hover:text-white transition-all duration-300">
                               {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                           </span>
                           <h3 class="font-black italic text-slate-900 text-sm md:text-base uppercase tracking-tighter group-hover:text-orange-500 transition-colors">{{ $faq->question }}</h3>
                        </div>
                        <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:text-orange-500 transition-all duration-300"
                             :class="active === {{ $faq->id }} ? 'rotate-180 bg-orange-50 text-orange-500' : ''">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </button>
                    
                    <div x-show="active === {{ $faq->id }}" 
                         x-collapse
                         x-cloak>
                        <div class="px-8 pb-8 pt-2">
                            <div class="w-full h-px bg-slate-50 mb-6"></div>
                            <div class="prose prose-slate max-w-none text-slate-600 text-sm md:text-base leading-loose font-medium italic pl-4 border-l-4 border-orange-500/30">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center space-y-6">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto text-slate-200 shadow-xl border border-slate-50">
                        <i class="fas fa-comment-slash text-4xl"></i>
                    </div>
                    <p class="text-sm font-black text-slate-400 uppercase tracking-[0.2em] italic">Şu an gösterilecek bir soru bulunmuyor.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer Hub -->
        <div class="bg-slate-900 rounded-[40px] p-10 md:p-16 relative overflow-hidden shadow-2xl shadow-slate-900/20">
            <i class="fas fa-headset absolute -right-8 -bottom-8 text-9xl text-white/5 transform -rotate-12"></i>
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-12 text-center md:text-left">
                <div class="space-y-4">
                    <h2 class="text-2xl md:text-3xl font-black text-white italic uppercase tracking-tighter">Hâlâ sorunuz mu var?</h2>
                    <p class="text-slate-400 text-sm font-medium italic">Profesyonel destek ekibimiz size yardımcı olmak için burada.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <a href="{{ route('contact') }}" class="px-10 py-5 bg-orange-500 text-white rounded-2xl font-black italic uppercase tracking-tighter hover:bg-white hover:text-orange-500 transition-all shadow-xl shadow-orange-500/20 text-center">İletişime Geç</a>
                    <a href="https://wa.me/{{ \App\Models\Setting::getValue('contact_whatsapp', '') }}" target="_blank" class="px-10 py-5 bg-white/10 text-white border border-white/20 rounded-2xl font-black italic uppercase tracking-tighter hover:bg-white/20 transition-all text-center flex items-center justify-center gap-3">
                        <i class="fab fa-whatsapp text-lg text-green-400"></i> WhatsApp Destek
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
