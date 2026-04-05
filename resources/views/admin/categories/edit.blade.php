@extends('layouts.admin')

@section('title', 'Kategori Düzenle: ' . $category->name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center mb-8">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-[var(--primary-color)] decoration-4 underline-offset-8">Kategori Düzenle</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium"><span class="font-black text-slate-900 uppercase italic">{{ $category->name }}</span> kategorisini güncelliyorsunuz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-sm font-black italic tracking-tighter uppercase rounded-xl bg-white shadow-xl shadow-slate-200/50 text-slate-600 hover:bg-slate-50 transition-all transform hover:scale-105 active:scale-95">
                <i class="fas fa-chevron-left mr-2"></i> Listeye Dön
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-8 bg-white/50 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 overflow-hidden relative group">
                @csrf
                @method('PUT')
                
                <!-- Decorative Element -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[var(--primary-color)]/5 rounded-full blur-[100px] transition-all group-hover:bg-[var(--primary-color)]/10"></div>
                
                <div class="relative space-y-6">
                    <div>
                        <label for="name" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">Kategori Adı</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $category->name) }}"
                               class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10 focus:border-[var(--primary-color)] transition-all text-center">
                        @error('name') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="parent_id" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">Üst Kategori</label>
                        <select name="parent_id" id="parent_id" 
                                class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10 focus:border-[var(--primary-color)] transition-all appearance-none cursor-pointer">
                            <option value="">— Anakategori (Yok)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->parent_id ? '└ ' . $parent->name : $parent->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] text-slate-400 font-bold italic">Eğer bu bir alt kategori ise, ait olduğu üst kategoriyi seçin.</p>
                        @error('parent_id') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center">
                        <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                            <div class="h-10 w-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-[var(--primary-color)]">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div class="flex-grow text-left">
                                <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase">Durum</h4>
                                <p class="text-[10px] text-slate-400 font-bold">Aktif / Pasif</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="active" value="1" class="sr-only peer" {{ $category->active ? 'checked' : '' }}>
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                            <div class="h-10 w-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-500">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="flex-grow text-left">
                                <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase">Menüde Göster</h4>
                                <p class="text-[10px] text-slate-400 font-bold">Navbar'da Sabitle</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_navbar" value="1" class="sr-only peer" {{ $category->is_navbar ? 'checked' : '' }}>
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-500 shadow-inner"></div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex gap-4 text-center">
                        <button type="submit" class="flex-grow px-10 py-5 bg-slate-900 text-white font-black italic uppercase tracking-tighter rounded-2xl shadow-2xl shadow-slate-200/50 hover:bg-slate-800 transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                            <i class="fas fa-save text-orange-400"></i> Güncellemeleri Kaydet
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="px-10 py-5 bg-gray-100 text-gray-500 font-black italic uppercase tracking-tighter rounded-2xl hover:bg-gray-200 transition-all">Vazgeç</a>
                    </div>
                </div>
            </form>
        </div>

        <div>
            <!-- Info Sidebar -->
            <div class="space-y-6">
                <!-- Statistics Card -->
                <div class="bg-white p-8 rounded-[40px] shadow-xl shadow-slate-200/50 border border-slate-50 relative overflow-hidden group">
                    <div class="absolute right-0 bottom-0 p-8 opacity-10 scale-150 rotate-12 transition-transform group-hover:rotate-0">
                        <i class="fas fa-chart-pie text-slate-900 text-6xl"></i>
                    </div>
                    <h3 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                         <span class="w-2 h-2 bg-emerald-400 rounded-full"></span> Kategori Özeti
                    </h3>
                    <div class="flex flex-col gap-6 relative z-10 text-center">
                        <div>
                            <div class="text-4xl font-black italic tracking-tighter text-slate-900">{{ $category->products()->count() }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Tanımlı Ürün</div>
                        </div>
                        <div class="pt-6 border-t border-slate-50">
                            <div class="text-xs font-bold text-slate-500 italic uppercase tracking-tighter">Oluşturulma: <span class="text-slate-900">{{ $category->created_at->format('d/m/Y') }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[40px] shadow-xl shadow-slate-200/50 border border-slate-50">
                    <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                        <span class="w-2 h-2 bg-orange-400 rounded-full"></span> Güvenlik Notu
                    </h4>
                    <p class="text-[11px] text-slate-600 font-medium leading-relaxed italic">İçinde ürün olan veya alt kategorisi bulunan kategoriler silinemez. Önce içindeki ürünleri başka kategoriye taşımalı veya silmelisiniz.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
