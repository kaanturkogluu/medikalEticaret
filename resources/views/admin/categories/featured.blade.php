@extends('layouts.admin')

@section('title', 'Navbar Kategori Yönetimi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-indigo-500 decoration-4 underline-offset-8">Öne Çıkarılan Kategoriler</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium">Navbar (üst menü) üzerinde görünecek kategorileri buradan yönetebilir ve sıralayabilirsiniz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-sm font-black italic tracking-tighter uppercase rounded-xl shadow-sm text-slate-600 bg-white hover:bg-slate-50 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Geri Dön
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Search & Add Section -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white/50 backdrop-blur-md p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50">
                <h3 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                    <span class="w-2 h-2 bg-indigo-400 rounded-full"></span> Kategori Ara & Ekle
                </h3>
                
                <form action="{{ route('admin.categories.featured') }}" method="GET" class="space-y-4">
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Kategori adı..." 
                               class="block w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-medium placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    </div>
                    <button type="submit" class="w-full px-8 py-4 bg-slate-900 text-white rounded-2xl font-black italic uppercase tracking-tighter hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-95">Ara</button>
                </form>

                <div class="mt-8 space-y-3">
                    @forelse($searchResults as $result)
                        <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-100 group hover:border-indigo-500/50 transition-all">
                            <span class="text-xs font-black italic uppercase tracking-tighter text-slate-700">{{ $result->name }}</span>
                            <form action="{{ route('admin.categories.featured.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="add_id" value="{{ $result->id }}">
                                <button type="submit" class="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        @if(request('search'))
                            <p class="text-center py-8 text-xs font-bold text-slate-400 italic">Bulunamadı veya zaten öne çıkarılmış.</p>
                        @endif
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sorting Section -->
        <div class="lg:col-span-2">
            <div class="bg-white/50 backdrop-blur-md p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 min-h-[400px]">
                <h3 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                    <span class="w-2 h-2 bg-emerald-400 rounded-full"></span> Öne Çıkarılanlar & Sıralama
                </h3>

                <form action="{{ route('admin.categories.featured.update') }}" method="POST">
                    @csrf
                    <div class="space-y-3 mb-8">
                        @forelse($featuredCategories as $featured)
                            <div class="flex items-center gap-4 p-4 bg-white rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-md group">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 cursor-move">
                                    <i class="fas fa-grip-vertical"></i>
                                </div>
                                
                                <div class="flex-grow">
                                    <div class="text-sm font-black italic uppercase tracking-tighter text-slate-900">{{ $featured->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Sıra ID: #{{ $featured->id }}</div>
                                </div>

                                <div class="w-32">
                                    <input type="number" name="orders[{{ $featured->id }}]" value="{{ $featured->row_order }}" 
                                           class="w-full px-3 py-2 bg-slate-50 border border-slate-100 rounded-xl text-center text-xs font-black focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                                </div>

                                <button type="submit" name="remove_id" value="{{ $featured->id }}" class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @empty
                            <div class="py-20 text-center">
                                <i class="fas fa-info-circle text-2xl text-slate-200 mb-4"></i>
                                <p class="text-sm font-bold text-slate-400 italic">Henüz öne çıkarılmış bir kategori bulunmuyor.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($featuredCategories->count() > 0)
                        <div class="flex justify-end pt-6 border-t border-slate-100">
                            <button type="submit" class="px-10 py-5 bg-indigo-600 text-white font-black italic uppercase tracking-tighter rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                                <i class="fas fa-save"></i> Sıralamayı Kaydet
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
