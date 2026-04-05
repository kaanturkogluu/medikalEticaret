@extends('layouts.admin')

@section('title', 'Yeni Kategori Ekle')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center mb-8">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-[var(--primary-color)] decoration-4 underline-offset-8">Yeni Kategori</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium">Sisteme yeni bir kategori eklemek için formu eksiksiz doldurunuz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-sm font-black italic tracking-tighter uppercase rounded-xl bg-white shadow-xl shadow-slate-200/50 text-slate-600 hover:bg-slate-50 transition-all transform hover:scale-105 active:scale-95">
                <i class="fas fa-chevron-left mr-2"></i> Listeye Dön
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-8 bg-white/50 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 overflow-hidden relative group">
                @csrf
                
                <!-- Decorative Element -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[var(--primary-color)]/5 rounded-full blur-[100px] transition-all group-hover:bg-[var(--primary-color)]/10"></div>
                
                <div class="relative space-y-6">
                    <div>
                        <label for="name" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3 text-center">Kategori Adı</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                               class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10 focus:border-[var(--primary-color)] transition-all">
                        @error('name') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="parent_id" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">Üst Kategori</label>
                        <select name="parent_id" id="parent_id" 
                                class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10 focus:border-[var(--primary-color)] transition-all appearance-none cursor-pointer">
                            <option value="">Anakategori (Yok)</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-[10px] text-slate-400 font-bold italic">Eğer bu bir alt kategori ise, ait olduğu üst kategoriyi seçin.</p>
                        @error('parent_id') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                            <div class="h-10 w-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-[var(--primary-color)]">
                                <i class="fas fa-power-off"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase">Durum</h4>
                                <p class="text-[10px] text-slate-400 font-bold">Aktif / Pasif</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="active" value="1" class="sr-only peer" checked>
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500 shadow-inner"></div>
                            </label>
                        </div>

                        <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                            <div class="h-10 w-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-500">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase">Menüde Göster</h4>
                                <p class="text-[10px] text-slate-400 font-bold">Navbar'da Sabitle</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_navbar" value="1" class="sr-only peer">
                                <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-indigo-500 shadow-inner"></div>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex gap-4 text-center">
                        <button type="submit" class="flex-grow px-10 py-5 bg-[var(--primary-color)] text-white font-black italic uppercase tracking-tighter rounded-2xl shadow-2xl shadow-orange-100 hover:bg-[var(--primary-hover)] transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                            <i class="fas fa-save"></i> Kategoriyi Kaydet
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="px-10 py-5 bg-gray-100 text-gray-500 font-black italic uppercase tracking-tighter rounded-2xl hover:bg-gray-200 transition-all">Vazgeç</a>
                    </div>
                </div>
            </form>
        </div>

        <div>
            <!-- Info Sidebar -->
            <div class="space-y-6">
                <div class="bg-slate-900 p-8 rounded-[40px] text-white shadow-2xl relative overflow-hidden group">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 group-hover:scale-110 transition-transform"></div>
                     <i class="fas fa-tags text-orange-400 text-2xl mb-4"></i>
                     <h3 class="text-lg font-black italic tracking-tighter uppercase mb-2">Hiyerarşik Yapı</h3>
                     <p class="text-xs text-slate-400 font-bold leading-loose">Kategorileri üst/alt ilişkisi kurarak organize edebilirsiniz. Bu sayede müşterileriniz için daha kolay bir gezinme deneyimi sağlarsınız.</p>
                </div>

                <div class="bg-white p-8 rounded-[40px] shadow-xl shadow-slate-200/50 border border-slate-50">
                    <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                        <span class="w-2 h-2 bg-orange-400 rounded-full"></span> İpuçları
                    </h4>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="h-6 w-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-1">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                            <p class="text-[11px] text-slate-600 font-medium">Kategori ismi net ve anlaşılır olmalıdır.</p>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="h-6 w-6 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-1">
                                <i class="fas fa-check text-[10px]"></i>
                            </div>
                            <p class="text-[11px] text-slate-600 font-medium">Kategoriyi pasife alırsanız içindeki ürünler de pasif görünecektir.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
