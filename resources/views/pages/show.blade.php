@extends('layouts.app')

@section('title', $page->title)

@section('content')
<div class="bg-white dark:bg-slate-900 min-h-screen">
    <!-- Hero Section -->
    <div class="relative py-20 bg-slate-50 border-b border-slate-100 overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -left-20 -top-20 w-80 h-80 bg-brand-500 rounded-full blur-[100px]"></div>
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-brand-500 rounded-full blur-[100px]"></div>
        </div>
        
        <div class="container mx-auto px-6 relative">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-black italic tracking-tighter text-slate-900 uppercase leading-none mb-6">
                    {{ $page->title }}
                </h1>
                <p class="text-sm text-slate-400 font-bold uppercase tracking-widest flex items-center gap-3">
                    <span class="w-10 h-[2px] bg-brand-500"></span>
                    Yasal Bilgilendirme & Sözleşme
                </p>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="container mx-auto px-6 py-20">
        <div class="flex flex-col lg:flex-row gap-16">
            <!-- Sidebar Navigation -->
            <div class="lg:w-1/4">
                <div class="sticky top-24 space-y-4">
                    <h3 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-4 px-6 flex items-center gap-2">
                        <i class="fas fa-list-ul text-brand-500"></i> Diğer Sayfalar
                    </h3>
                    <div class="space-y-2">
                        @foreach(\App\Models\Page::where('is_active', true)->orderBy('title')->get() as $p)
                        <a href="{{ route('page.show', $p->slug) }}" 
                           class="block px-6 py-4 rounded-2xl border transition-all flex items-center justify-between group {{ $page->id == $p->id ? 'bg-slate-900 border-slate-900 text-white shadow-xl shadow-slate-200' : 'bg-white border-slate-100 text-slate-600 hover:border-brand-500 hover:text-slate-900 shadow-sm' }}">
                            <span class="text-sm font-bold {{ $page->id == $p->id ? 'italic' : '' }}">{{ $p->title }}</span>
                            <i class="fas fa-chevron-right text-xs {{ $page->id == $p->id ? 'text-brand-400' : 'text-slate-200 group-hover:text-brand-500' }}"></i>
                        </a>
                        @endforeach
                    </div>

                    <div class="mt-10 p-8 bg-brand-50 rounded-3xl border border-brand-100">
                        <i class="fas fa-headset text-2xl text-brand-500 mb-4"></i>
                        <h4 class="text-sm font-black italic tracking-tight text-slate-900 uppercase mb-2">Desteğe mi ihtiyacınız var?</h4>
                        <p class="text-xs text-slate-600 font-medium leading-relaxed italic mb-4">Sözleşmeler hakkında sorularınız için bizimle iletişime geçebilirsiniz.</p>
                        <a href="{{ route('contact') }}" class="text-xs font-black italic text-brand-600 uppercase tracking-widest hover:underline">İletişime Geç →</a>
                    </div>
                </div>
            </div>

            <!-- Text Content -->
            <div class="lg:w-3/4">
                <article class="bg-white p-10 md:p-16 rounded-[40px] shadow-2xl shadow-slate-200/50 border border-slate-50">
                    <div class="prose prose-slate max-w-none prose-headings:italic prose-headings:font-black prose-headings:tracking-tighter prose-headings:uppercase prose-p:text-slate-600 prose-p:leading-relaxed prose-strong:text-slate-900">
                        {!! nl2br($page->content) !!}
                    </div>

                    <div class="mt-16 pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
                        <p class="text-xs text-slate-400 font-bold italic uppercase tracking-widest">
                            Son Güncelleme: {{ $page->updated_at->format('d/m/Y') }}
                        </p>
                        <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-black italic uppercase tracking-tighter hover:bg-slate-200 transition-all">
                            <i class="fas fa-print"></i> Yazdır
                        </button>
                    </div>
                </article>
            </div>
        </div>
    </div>
</div>
@endsection
