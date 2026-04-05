@extends('layouts.admin')

@section('title', 'Sözleşmeler ve Politikalar')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center mb-10">
        <div class="sm:flex-auto">
            <h1 class="text-3xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-brand-500 decoration-4 underline-offset-8">Sözleşmeler ve Politikalar</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium italic">Sitedeki tüm yasal metinleri, sözleşmeleri ve politikaları buradan yönetebilirsiniz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center justify-center px-10 py-5 bg-slate-900 text-white font-black italic uppercase tracking-tighter rounded-2xl shadow-2xl shadow-slate-200/50 hover:bg-slate-800 transition-all transform hover:scale-105 active:scale-95 group">
                <i class="fas fa-plus mr-3 text-brand-400 group-hover:rotate-90 transition-transform"></i> Yeni Metin Ekle
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($pages as $page)
        <div class="bg-white/50 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 relative overflow-hidden group hover:shadow-brand-500/10 transition-all border-b-4 {{ $page->is_active ? 'border-b-emerald-500' : 'border-b-red-500' }}">
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                    <div class="p-4 bg-slate-900 rounded-2xl shadow-lg">
                        <i class="fas fa-file-contract text-brand-400 text-xl"></i>
                    </div>
                    <form action="{{ route('admin.pages.toggle', $page->slug) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-[10px] font-black italic uppercase tracking-widest px-4 py-2 rounded-full transition-all {{ $page->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $page->is_active ? 'Yayında' : 'Pasif' }}
                        </button>
                    </form>
                </div>

                <h3 class="text-xl font-black italic tracking-tighter text-slate-900 uppercase mb-2 group-hover:text-brand-600 transition-colors">{{ $page->title }}</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-6">/{{ $page->slug }}</p>

                <div class="flex gap-3">
                    <a href="{{ route('admin.pages.edit', $page->slug) }}" class="flex-grow inline-flex items-center justify-center px-6 py-4 bg-slate-100 text-slate-600 font-bold italic uppercase tracking-tighter text-xs rounded-xl hover:bg-brand-500 hover:text-white transition-all">
                        <i class="fas fa-edit mr-2"></i> Düzenle
                    </a>
                    <button type="button" @click="$dispatch('open-delete-modal', { url: '{{ route('admin.pages.destroy', $page->slug) }}' })" class="inline-flex items-center justify-center px-6 py-4 bg-red-50 text-red-400 font-bold italic uppercase tracking-tighter text-xs rounded-xl hover:bg-red-500 hover:text-white transition-all">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <!-- Background Decoration -->
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-slate-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700"></div>
        </div>
        @endforeach
    </div>

    @if($pages->isEmpty())
    <div class="mt-20 text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-slate-100 rounded-full mb-6">
            <i class="fas fa-file-invoice text-slate-300 text-4xl"></i>
        </div>
        <h2 class="text-2xl font-black italic tracking-tighter text-slate-400 uppercase">Henüz metin eklenmemiş</h2>
        <p class="mt-2 text-slate-400 font-medium italic underline decoration-slate-200 underline-offset-4 decoration-2">Yeni bir sayfa oluşturarak başlayın.</p>
    </div>
    @endif
</div>
@endsection
