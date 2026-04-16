@extends('layouts.user')

@section('title', 'Favorilerim')

@section('user_content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden min-h-[70vh]">
    {{-- Header --}}
    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/30 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-orange-500">
                <i class="fas fa-heart"></i>
            </div>
            <div>
                <h2 class="font-black italic text-slate-900 uppercase tracking-tighter">Favorilerim</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest"><span x-text="$store.fav.items.length"></span> Ürün Listeleniyor</p>
            </div>
        </div>
        <a href="{{ route('home') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-orange-500 transition-all flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> ALIŞVERİŞE DEVAM ET
        </a>
    </div>

    <div class="p-6">
        <!-- Empty State -->
        <div x-show="$store.fav.items.length === 0" x-cloak class="flex flex-col items-center justify-center py-20 text-gray-400 gap-6">
             <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center shadow-inner">
                 <i class="fas fa-heart text-5xl text-gray-200"></i>
             </div>
             <div class="text-center">
                 <p class="text-lg font-black italic text-slate-800 tracking-tighter uppercase mb-1">Favori listeniz şu an boş</p>
                 <p class="text-[11px] text-gray-400 font-bold uppercase tracking-wider">Henüz hiçbir ürünü favorilerinize eklemediniz.</p>
             </div>
             <a href="{{ route('home') }}" class="mt-4 px-8 py-4 bg-slate-900 text-white font-black rounded-xl shadow-lg hover:bg-orange-600 transition-all uppercase tracking-widest text-[10px]">
                 KEŞFETMEYE BAŞLA
             </a>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <template x-for="product in $store.fav.items" :key="product.id">
                <div class="group relative bg-white border border-gray-100 p-4 rounded-3xl transition-all hover:shadow-xl hover:border-orange-500 overflow-visible">
                    <button @click="$store.fav.toggle(product)" class="absolute -top-2 -right-2 bg-white border border-gray-100 text-gray-400 w-8 h-8 rounded-full flex items-center justify-center shadow-sm hover:bg-red-500 hover:text-white transition-all z-20">
                        <i class="fas fa-times text-[10px]"></i>
                    </button>
                    
                    <a :href="product.slug ? '/urun/' + product.slug : '/urun/' + product.id">
                        <div class="aspect-square bg-gray-50/50 relative overflow-hidden rounded-2xl mb-4 p-4 flex items-center justify-center">
                            <img :src="product.image" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        </div>
                    </a>

                    <div class="flex flex-col">
                        <div class="font-bold text-[9px] mb-1 uppercase tracking-widest text-orange-500" x-text="product.brand"></div>
                        <h3 class="text-[11px] font-bold text-slate-700 mb-4 h-8 overflow-hidden line-clamp-2 leading-tight" x-text="product.name"></h3>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="text-slate-900 font-black text-sm italic tracking-tighter" x-text="product.price + ' TL'"></div>
                        </div>
                        
                        <button @click="$store.cart.add(product)" class="w-full py-3 bg-slate-900 text-white text-[10px] font-black rounded-xl hover:bg-orange-500 transition-all uppercase tracking-widest shadow-sm">
                            SEPETE EKLE
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

