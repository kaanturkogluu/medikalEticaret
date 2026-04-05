@extends('layouts.admin')

@section('title', 'Kategori Yönetimi')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-[var(--primary-color)] decoration-4 underline-offset-8">Kategoriler</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium">Sistemdeki tüm kategorileri buradan yönetebilirsiniz. Alt kategoriler oluşturabilir veya mevcut olanları düzenleyebilirsiniz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-black italic tracking-tighter uppercase rounded-xl shadow-xl shadow-orange-100 text-white bg-[var(--primary-color)] hover:bg-[var(--primary-hover)] transition-all transform hover:scale-105 active:scale-95">
                <i class="fas fa-plus mr-2"></i> Yeni Kategori Ekle
            </a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="mt-8 bg-white/50 backdrop-blur-md p-6 rounded-3xl border border-white shadow-2xl shadow-slate-200/50">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-4">
            <div class="relative flex-grow group">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Kategori adı ile ara..." 
                       class="block w-full pl-12 pr-4 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-medium placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10 focus:border-[var(--primary-color)] transition-all">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-[var(--primary-color)] transition-colors"></i>
            </div>
            <button type="submit" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black italic uppercase tracking-tighter hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 active:scale-95">Filtrele</button>
            @if(request('q'))
                <a href="{{ route('admin.categories.index') }}" class="px-8 py-4 bg-gray-100 text-gray-600 rounded-2xl font-black italic uppercase tracking-tighter hover:bg-gray-200 transition-all flex items-center">Temizle</a>
            @endif
        </form>
    </div>

    <!-- Categories Table -->
    <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow-2xl shadow-slate-200/50 rounded-[32px] border border-white bg-white/50 backdrop-blur-sm">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="py-5 pl-8 pr-3 text-left text-xs font-black uppercase tracking-widest text-slate-400 italic">Kategori Adı</th>
                                <th scope="col" class="px-3 py-5 text-left text-xs font-black uppercase tracking-widest text-slate-400 italic">Üst Kategori</th>
                                <th scope="col" class="px-3 py-5 text-left text-xs font-black uppercase tracking-widest text-slate-400 italic text-center">Ürün Sayısı</th>
                                <th scope="col" class="px-3 py-5 text-left text-xs font-black uppercase tracking-widest text-slate-400 italic text-center">Durum</th>
                                <th scope="col" class="px-3 py-5 text-left text-xs font-black uppercase tracking-widest text-slate-400 italic text-center">Menü</th>
                                <th scope="col" class="relative py-5 pl-3 pr-8 text-right text-xs font-black uppercase tracking-widest text-slate-400 italic">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($categories as $category)
                            <tr class="hover:bg-white/80 transition-colors group">
                                <td class="whitespace-nowrap py-5 pl-8 pr-3">
                                    <div class="text-sm font-black text-slate-900 italic tracking-tighter uppercase">{{ $category->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">SLUG: {{ $category->slug }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-slate-500 font-bold italic uppercase tracking-tighter">
                                    @if($category->parent)
                                        <span class="inline-flex items-center gap-2">
                                            <i class="fas fa-level-up-alt rotate-90 text-[10px] text-slate-300"></i>
                                            {{ $category->parent->name }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-slate-500 font-bold text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-100 text-slate-600">
                                        {{ $category->products_count }} Ürün
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-center">
                                    <form action="{{ route('admin.categories.toggle-active', $category->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black italic tracking-widest uppercase transition-all {{ $category->active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $category->active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                            {{ $category->active ? 'Aktif' : 'Pasif' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-center">
                                    <form action="{{ route('admin.categories.toggle-navbar', $category->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center p-2 rounded-xl text-xs transition-all {{ $category->is_navbar ? 'bg-indigo-50 text-indigo-600 shadow-inner' : 'bg-slate-50 text-slate-300 hover:text-slate-400' }}" title="Navbar'da Göster">
                                            <i class="fas fa-globe"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="relative whitespace-nowrap py-5 pl-3 pr-8 text-right text-sm font-medium">
                                    <div class="flex justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                        <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-2.5 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-3 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-4 ring-8 ring-slate-50/50">
                                            <i class="fas fa-tags text-4xl text-slate-200"></i>
                                        </div>
                                        <p class="text-lg font-black italic tracking-tighter text-slate-400 uppercase">Herhangi bir kategori bulunamadı.</p>
                                        <a href="{{ route('admin.categories.create') }}" class="mt-6 text-sm font-black italic tracking-tighter text-[var(--primary-color)] hover:underline underline-offset-4 uppercase">Hemen İlk Kategoriyi Ekle</a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-8">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
