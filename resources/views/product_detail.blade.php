@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <main class="ty-container pb-20">
        <!-- Breadcrumbs -->
        <nav class="breadcrumb flex gap-10 font-bold items-center py-6">
            <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:underline">Ana Sayfa</a>
            <i class="fas fa-chevron-right text-[8px] text-gray-400"></i>
            <a href="{{ route('home', ['category' => $product->category->slug ?? $product->category_id]) }}" class="text-xs text-gray-500 hover:underline">{{ $product->category->name ?? 'Kategori' }}</a>
            <i class="fas fa-chevron-right text-[8px] text-gray-400"></i>
            <span class="text-xs text-gray-300">{{ str($product->name)->limit(40) }}</span>
        </nav>

        <div x-data="{ activeImage: '{{ $product->productImages->first()?->url ?? 'https://via.placeholder.com/600x900' }}' }" class="flex flex-col lg:flex-row gap-12 mt-4">
            <!-- Left: Images -->
            <div class="w-full lg:w-1/2 flex gap-4">
                <div class="flex flex-col gap-2 shrink-0">
                    @foreach($product->productImages as $image)
                        <img src="{{ $image->url }}" 
                             @click="activeImage = '{{ $image->url }}'"
                             :class="activeImage == '{{ $image->url }}' ? 'border-[var(--primary-color)] ring-2 ring-orange-50' : 'border-gray-200'"
                             class="w-16 h-20 object-contain border rounded cursor-pointer transition-all p-1" alt="thumbnail">
                    @endforeach
                </div>
                <div class="flex-grow aspect-[2/3] border border-gray-100 rounded-xl overflow-hidden bg-white flex items-center justify-center p-8">
                    <img :src="activeImage" class="w-full h-full object-contain" alt="{{ $product->name }}">
                </div>
            </div>

            <!-- Right: Info -->
            <div class="w-full lg:w-1/2">
                <div class="mb-4">
                    <h2 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-[var(--primary-color)] decoration-4 underline-offset-8 mb-4">{{ $product->brand->name ?? 'Markasız' }}</h2>
                    <h1 class="text-2xl text-gray-700 font-medium leading-tight">{{ $product->name }}</h1>
                </div>

                <!-- Ratings -->
                <div class="flex items-center gap-4 py-4 border-b border-gray-50">
                    <div class="flex items-center gap-1 bg-amber-50 px-2 py-1 rounded">
                        <span class="font-black text-amber-600 text-sm">4.8</span>
                        <div class="flex text-amber-400 text-xs">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                    </div>
                    <span class="text-gray-300 text-xs">|</span>
                    <span class="text-[var(--accent-blue)] text-xs font-black cursor-pointer hover:underline">152 Değerlendirme</span>
                    <span class="text-gray-300 text-xs">|</span>
                    <span class="text-[var(--accent-blue)] text-xs font-black cursor-pointer hover:underline">43 Soru & Cevap</span>
                    <span class="text-gray-300 text-xs">|</span>
                    <div class="flex items-center gap-1.5 text-slate-400">
                        <i class="far fa-eye text-xs"></i>
                        <span class="text-xs font-bold">{{ number_format($product->views) }} Görüntülenme</span>
                    </div>
                </div>

                <!-- Price Section -->
                <div class="bg-gray-50 p-6 rounded-2xl my-8 border border-white shadow-sm ring-1 ring-gray-100">
                    <div class="text-sm text-gray-400 line-through mb-1">{{ number_format($product->price * 1.2, 2) }} TL</div>
                    <div class="flex items-end gap-3">
                        <span class="text-4xl font-black text-[var(--primary-color)] tracking-tighter">{{ number_format($product->price, 2) }} TL</span>
                        <span class="bg-red-500 text-white text-xs font-black px-2 py-1 rounded-lg mb-2">-%20</span>
                    </div>
                </div>

                <!-- Shipping Highlight Badge -->
                <div class="mb-8 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-100 rounded-2xl flex items-center gap-4 relative overflow-hidden group">
                    <div class="absolute -right-2 -top-2 text-green-100/50 text-5xl rotate-12 transition-transform group-hover:rotate-0">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-green-600 shadow-sm shrink-0 ring-4 ring-green-100/50">
                        <i class="fas fa-shipping-fast text-xl animate-bounce"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="text-xs font-black text-green-900 uppercase italic tracking-tighter flex items-center gap-2">
                           <span class="w-2 h-2 bg-green-500 rounded-full animate-ping"></span>
                           AYNI GÜN KARGO FIRSATI
                        </div>
                        <div class="text-[11px] text-green-700 font-bold leading-tight mt-1">
                            Bugün saat <span class="bg-green-600 text-white px-1.5 py-0.5 rounded-md font-black italic">16:00</span>'a kadar vereceğiniz siparişlerde kargonuz <span class="text-green-900 underline decoration-green-300 decoration-2 font-black italic">BUGÜN</span> yola çıksın!
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 mb-10">
                    @php $imgArr = $product->productImages->first()?->url ?? 'https://via.placeholder.com/600x900'; @endphp
                    <button @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $imgArr }}'})" class="flex-grow h-16 bg-[var(--primary-color)] text-white text-lg font-black rounded-xl shadow-lg shadow-orange-100 hover:bg-[var(--primary-hover)] transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-shopping-basket"></i>
                        <span>SEPETE EKLE</span>
                    </button>
                    <button @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $imgArr }}'})" class="w-16 h-16 border-2 border-gray-100 rounded-xl flex items-center justify-center transition-all bg-white hover:border-red-100" :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500 bg-red-50' : 'text-gray-300'">
                        <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart text-2xl' : 'far fa-heart text-2xl'"></i>
                    </button>
                </div>

                <!-- Merchant -->
                <div class="flex items-center justify-between p-6 border border-gray-100 rounded-2xl mb-10 bg-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-slate-900 rounded-full flex items-center justify-center text-white ring-4 ring-slate-100">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <div class="text-sm font-black text-slate-900 tracking-tight">UMMET MEDİKAL</div>
                            <div class="text-xs text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-full w-fit mt-1">9.8 Satıcı Puanı</div>
                        </div>
                    </div>
                    <a href="#" class="text-xs font-black text-[var(--accent-blue)] border-b-2 border-blue-50">Mağazayı Gör</a>
                </div>

                <!-- Highlights -->
                <div class="space-y-4">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest border-l-4 border-orange-400 pl-3">Öne Çıkan Özellikler</h3>
                    <ul class="grid grid-cols-2 gap-4">
                        @foreach($product->productAttributes->take(6) as $attr)
                            <li class="p-3 rounded-lg border border-gray-50 flex flex-col gap-1 bg-white hover:shadow-sm transition-shadow">
                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $attr->name }}</span>
                                <span class="text-xs text-slate-800 font-medium">{{ $attr->value }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Description & Details Tabs -->
        <div class="mt-24" x-data="{ tab: 'description' }">
            <div class="flex border-b border-gray-200 items-center justify-center gap-12 sticky top-20 bg-white z-[100] py-4 bg-opacity-90 backdrop-blur">
                <button @click="tab = 'description'" :class="tab == 'description' ? 'text-[var(--primary-color)] border-b-2 border-orange-500' : 'text-gray-400'" class="pb-2 font-black italic uppercase tracking-tighter transition-all">Ürün Açıklaması</button>
                <button @click="tab = 'features'" :class="tab == 'features' ? 'text-[var(--primary-color)] border-b-2 border-orange-500' : 'text-gray-400'" class="pb-2 font-black italic uppercase tracking-tighter transition-all">Tüm Özellikler</button>
                <button @click="tab = 'comments'" :class="tab == 'comments' ? 'text-[var(--primary-color)] border-b-2 border-orange-500' : 'text-gray-400'" class="pb-2 font-black italic uppercase tracking-tighter transition-all">Değerlendirmeler (152)</button>
            </div>
            
            <div class="py-12 max-w-4xl mx-auto min-h-[400px]">
                <div x-show="tab == 'description'" class="text-slate-600 leading-relaxed text-sm space-y-6">
                    {!! nl2br(e($product->description)) !!}
                </div>
                
                <div x-show="tab == 'features'" x-cloak>
                    <div class="grid grid-cols-1 gap-1">
                        @foreach($product->productAttributes as $attr)
                            <div class="flex items-center py-4 border-b border-gray-50 group hover:bg-gray-50 px-4 rounded-lg transition-colors">
                                <div class="w-1/3 text-sm font-bold text-gray-400 group-hover:text-slate-500">{{ $attr->name }}</div>
                                <div class="w-2/3 text-sm text-slate-800 font-medium">{{ $attr->value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="tab == 'comments'" x-cloak class="text-center py-24 flex flex-col items-center gap-4">
                    <i class="far fa-comment-alt text-6xl text-gray-100"></i>
                    <p class="text-gray-400 italic font-bold">Henüz hiç yorum yapılmamış.</p>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-20">
            <h3 class="text-2xl font-black mb-10 italic tracking-tighter decoration-slate-900 underline underline-offset-8">Benzer Ürünler</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
                @foreach($relatedProducts as $rp)
                    <div class="group relative bg-white border border-gray-100 p-3 rounded-2xl hover:shadow-xl hover:shadow-gray-100 transition-all">
                        <a href="{{ route('product.show', $rp) }}" target="_blank">
                             <div class="aspect-[2/3] bg-gray-50 rounded-xl overflow-hidden mb-4 p-4">
                                <img src="{{ $rp->productImages->first()?->url ?? 'https://via.placeholder.com/400x600' }}" alt="" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="text-[10px] font-black text-slate-900 border-b-2 border-orange-100 w-fit mb-1 uppercase tracking-widest">{{ $rp->brand->name ?? 'Markasız' }}</div>
                            <div class="text-xs text-gray-500 h-10 overflow-hidden line-clamp-2 leading-tight pr-4">{{ $rp->name }}</div>
                            <div class="text-lg font-black text-[var(--primary-color)] mt-3 tracking-tighter">{{ number_format($rp->price, 2) }} TL</div>
                        </a>
                        <button @click="$store.cart.add({id: '{{ $rp->id }}', slug: '{{ $rp->slug }}', name: '{{ addslashes($rp->name) }}', brand: '{{ addslashes($rp->brand->name ?? '') }}', price: {{ $rp->price }}, image: '{{ $rp->productImages->first()?->url ?? '' }}'})" class="absolute bottom-4 right-4 bg-slate-900 text-white w-10 h-10 rounded-xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-xl hover:bg-orange-500">
                            <i class="fas fa-cart-plus text-sm"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection
