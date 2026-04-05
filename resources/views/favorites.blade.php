@extends('layouts.app')

@section('title', 'Favorilerim')

@section('content')
    <main class="ty-container py-12 min-h-[70vh]">
        <div class="flex items-center justify-between mb-12 pb-6 border-b-2 border-gray-100">
            <div class="flex flex-col gap-1">
                <h2 class="text-3xl font-black italic tracking-tighter text-slate-900 border-l-8 border-orange-500 pl-6 uppercase">Favorilerim</h2>
                <span class="text-sm font-bold text-gray-400 pl-6 uppercase tracking-widest"><span x-text="$store.fav.items.length"></span> Ürün Listeleniyor</span>
            </div>
            <a href="{{ route('home') }}" class="text-sm font-black text-white bg-slate-900 px-8 py-4 rounded-xl shadow-lg hover:shadow-slate-200 transition-all uppercase tracking-tighter group flex items-center gap-3">
                <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                Alışverişe Devam Et
            </a>
        </div>

        <!-- Empty State -->
        <div x-show="$store.fav.items.length === 0" x-cloak class="flex flex-col items-center justify-center py-32 text-gray-400 gap-6 bg-white rounded-3xl border-2 border-dashed border-gray-100 shadow-2xl">
             <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center relative shadow-inner ring-4 ring-gray-100">
                 <i class="fas fa-heart text-7xl text-gray-100 drop-shadow-sm"></i>
             </div>
             <div class="text-center">
                 <p class="text-2xl font-black italic text-slate-800 tracking-tighter uppercase mb-2">Favori listeniz şu an boş</p>
                 <p class="text-sm text-gray-400 font-medium">Sevdiğiniz ürünleri daha sonra kolayca bulmak için favorilerinize ekleyin.</p>
             </div>
             <a href="{{ route('home') }}" class="mt-4 px-12 py-5 bg-[var(--primary-color)] text-white font-black rounded-2xl shadow-xl shadow-orange-100 hover:bg-[var(--primary-hover)] transition-all uppercase tracking-widest text-sm transform hover:scale-105">
                 ŞİMDİ ALIŞVERİŞE BAŞLA
             </a>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-8">
            <template x-for="product in $store.fav.items" :key="product.id">
                <div class="product-card group relative bg-white border border-gray-100 p-4 rounded-3xl transition-all hover:shadow-2xl hover:shadow-gray-100 hover:border-orange-500 hover:-translate-y-2 overflow-visible">
                    <button @click="$store.fav.toggle(product)" class="absolute -top-4 -right-4 bg-slate-900 text-white w-10 h-10 rounded-2xl flex items-center justify-center shadow-xl shadow-slate-200 hover:bg-red-500 transition-colors z-20 transform hover:scale-110">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                    
                    <a :href="product.slug ? '/urun/' + product.slug : '/urun/' + product.id" target="_blank">
                        <div class="aspect-[2/3] bg-gray-50 relative overflow-hidden rounded-2xl mb-4 p-4 flex items-center justify-center ring-1 ring-gray-50">
                            <img :src="product.image" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700">
                        </div>
                    </a>

                    <div class="p-2 flex flex-col flex-grow">
                        <div class="font-black text-xs mb-2 uppercase tracking-tighter text-slate-900 border-b-2 border-orange-100 w-fit" x-text="product.brand"></div>
                        <h3 class="text-xs text-gray-500 mb-6 h-8 overflow-hidden line-clamp-2 leading-tight pr-4 font-medium" x-text="product.name"></h3>
                        <div class="mt-auto">
                             <div class="flex items-center justify-between mb-4">
                                <div class="text-[var(--primary-color)] font-black text-xl tracking-tighter" x-text="product.price + ' TL'"></div>
                             </div>
                             <button @click="$store.cart.add(product)" class="w-full py-4 bg-slate-900 text-white text-[11px] font-black rounded-2xl hover:bg-orange-500 transition-all shadow-xl hover:shadow-orange-100 uppercase tracking-widest">
                                 SEPETE EKLE
                             </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>
@endsection
