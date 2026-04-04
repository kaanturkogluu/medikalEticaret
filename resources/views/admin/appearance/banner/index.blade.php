@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appearance') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Banner Yönetimi</h1>
                <p class="text-sm text-slate-500 mt-1">Ana sayfada gösterilen banner reklamlarını yönetin.</p>
            </div>
        </div>
        <a href="{{ route('admin.appearance.banner.create') }}" class="px-6 py-3 bg-brand-500 text-white rounded-2xl font-black italic shadow-lg shadow-brand-100 hover:bg-brand-600 transition-all transform hover:-translate-y-1 flex items-center gap-2">
            <i class="fas fa-plus"></i> YENİ BANNER EKLE
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-6 py-4 rounded-2xl flex items-center gap-3 animate-pulse-soft">
            <i class="fas fa-check-circle"></i>
            <span class="font-bold text-sm leading-none">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($banners as $banner)
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl overflow-hidden group">
                <div class="relative h-48 overflow-hidden">
                    <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Banner">
                    <div class="absolute inset-x-0 top-0 p-4 flex justify-between items-start">
                        <div class="bg-black/50 backdrop-blur-md text-white border border-white/20 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">
                            SIRA: {{ $banner->order }}
                        </div>
                        <form action="{{ route('admin.appearance.banner.toggle', $banner) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center transition-colors {{ $banner->is_active ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                <i class="fas {{ $banner->is_active ? 'fa-eye' : 'fa-eye-slash' }} text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="p-6">
                    <h3 class="text-md font-black text-slate-800 italic tracking-tighter truncate uppercase mb-1">{{ $banner->title ?? 'BAŞLIKSIZ' }}</h3>
                    <p class="text-xs text-slate-400 font-medium truncate mb-6">{{ $banner->subtitle ?? 'Alt başlık belirtilmemiş' }}</p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.appearance.banner.edit', $banner) }}" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-brand-50 hover:text-brand-500 transition-colors">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.appearance.banner.destroy', $banner) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-rose-50 hover:text-rose-500 transition-colors">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                        <span class="text-[10px] font-black {{ $banner->is_active ? 'text-emerald-500' : 'text-rose-500' }} uppercase italic tracking-widest">
                            {{ $banner->is_active ? 'YAYINDA' : 'PASİF' }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center text-slate-300 gap-4 bg-white rounded-[40px] border border-dashed border-slate-200">
                <i class="fas fa-images text-6xl opacity-20"></i>
                <p class="font-bold italic">Henüz hiç banner eklenmemiş.</p>
                <a href="{{ route('admin.appearance.banner.create') }}" class="text-brand-500 font-black border-b-2 border-brand-100">Hemen Bir Tane Oluşturun</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
