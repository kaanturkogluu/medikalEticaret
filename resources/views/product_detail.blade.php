@extends('layouts.app')

@php
    $primaryColor = \App\Models\Setting::getValue('site_primary_color', '#f27a1a');
@endphp

@section('title', $product->name)

@section('content')
    <main class="ty-container pb-20">
        <!-- Breadcrumbs -->
        <nav class="breadcrumb flex gap-4 font-bold items-center py-6">
            <a href="{{ route('home') }}" class="text-[11px] text-gray-400 hover:text-slate-900 transition-colors uppercase tracking-widest">Ana Sayfa</a>
            <i class="fas fa-chevron-right text-[8px] text-gray-300"></i>
            @if($product->category)
                <a href="{{ route('home', ['category' => $product->category->slug ?? $product->category_id]) }}" class="text-[11px] text-gray-400 hover:text-slate-900 transition-colors uppercase tracking-widest">{{ $product->category->name }}</a>
                <i class="fas fa-chevron-right text-[8px] text-gray-300"></i>
            @endif
            <span class="text-[11px] text-gray-300 uppercase tracking-widest truncate max-w-[200px]">{{ $product->name }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-16" x-data="{ activeImage: '{{ $product->productImages->first()?->url ?? 'https://via.placeholder.com/600x900' }}' }">
            
            <!-- Left: Image Gallery (Sticky) -->
            <div class="w-full lg:w-[55%]">
                <div class="lg:sticky lg:top-40 flex gap-6">
                    <!-- Thumbnails -->
                    <div class="flex flex-col gap-3 shrink-0 max-h-[600px] overflow-y-auto no-scrollbar">
                        @foreach($product->productImages as $image)
                            <div @click="activeImage = '{{ $image->url }}'" 
                                 :class="activeImage == '{{ $image->url }}' ? 'border-orange-500 ring-2 ring-orange-50' : 'border-gray-100'"
                                 class="w-20 h-24 bg-white border-2 rounded-2xl cursor-pointer transition-all p-2 flex items-center justify-center overflow-hidden hover:border-orange-200">
                                <img src="{{ $image->url }}" class="w-full h-full object-contain" alt="thumbnail">
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Main Image with Zoom -->
                    <div class="flex-grow aspect-[4/5] bg-white rounded-[40px] border border-gray-100 shadow-sm overflow-hidden flex items-center justify-center p-12 relative group cursor-zoom-in"
                         @mousemove="zoom = true; handleZoom($event)" @mouseleave="zoom = false"
                         x-data="{ 
                            zoom: false, zoomX: 0, zoomY: 0,
                            handleZoom(e) {
                                const rect = e.currentTarget.getBoundingClientRect();
                                this.zoomX = ((e.clientX - rect.left) / rect.width) * 100;
                                this.zoomY = ((e.clientY - rect.top) / rect.height) * 100;
                            }
                         }">
                        <div class="absolute inset-0 bg-gradient-to-tr from-gray-50/50 to-transparent pointer-events-none z-10"></div>
                        <img :src="activeImage" 
                             :style="zoom ? `transform: scale(2.5); transform-origin: ${zoomX}% ${zoomY}%;` : ''"
                             class="w-full h-full object-contain transition-transform duration-150" 
                             alt="{{ $product->name }}">
                        
                        <!-- Zoom Hint -->
                        <div x-show="!zoom" class="absolute bottom-6 right-6 w-12 h-12 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-slate-400 shadow-xl opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100 z-20">
                            <i class="fas fa-search-plus"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="w-full lg:w-[45%] flex flex-col">
                <!-- Header -->
                <div class="space-y-4 mb-8">
                    <div class="flex items-center justify-between">
                        <a href="#" class="text-3xl font-black italic tracking-tighter text-slate-900 uppercase hover:text-[var(--primary-color)] transition-all">
                            {{ $product->brand->name ?? 'Markasız' }}
                        </a>
                        <div class="flex items-center gap-2">
                             <div class="flex text-amber-400 text-sm">
                                @php $avgRating = $product->approvedComments->avg('rating') ?: 5; @endphp
                                @for($i=1; $i<=5; $i++)
                                    <i class="{{ $i <= $avgRating ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                             </div>
                             <span class="text-xs font-black text-slate-400">({{ $product->approvedComments->count() }})</span>
                        </div>
                    </div>
                    <h1 class="text-3xl text-slate-800 font-bold leading-tight tracking-tight uppercase">{{ $product->name }}</h1>
                    
                    <div class="flex items-center gap-6 pt-2">
                        <div class="flex items-center gap-2 text-xs font-black text-green-600 bg-green-50 px-3 py-1.5 rounded-full uppercase tracking-tighter">
                            <i class="fas fa-certificate"></i>
                            YETKİLİ SATICI
                        </div>
                        <div class="flex items-center gap-2 text-xs font-black text-slate-400 uppercase tracking-tighter">
                            <i class="far fa-eye"></i>
                            {{ number_format($product->views) }} Görüntülenme
                        </div>
                    </div>
                </div>

                <!-- Price Box -->
                <div class="relative bg-slate-900 rounded-[40px] p-8 mb-10 overflow-hidden shadow-2xl shadow-slate-200 group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full -mr-16 -mt-16 blur-3xl group-hover:bg-orange-500/20 transition-all duration-1000"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="text-gray-400 text-sm font-bold line-through mb-1 opacity-60 italic">{{ number_format($product->price * 1.2, 2) }} TL</div>
                            <div class="flex items-center gap-4">
                                <span class="text-5xl font-black text-white tracking-tighter">{{ number_format($product->price, 2) }} <span class="text-2xl font-light opacity-80">TL</span></span>
                                <div class="flex flex-col gap-1">
                                    <div class="bg-orange-500 text-white text-[9px] font-black px-3 py-1 rounded-lg animate-pulse uppercase">-%20 İNDİRİM</div>
                                    <div class="bg-green-500 text-white text-[9px] font-black px-3 py-1 rounded-lg uppercase whitespace-nowrap">EFT İLE %5 EK İNDİRİM</div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="text-[10px] font-black text-orange-400 uppercase tracking-[0.2em]">Ödeme Seçenekleri</div>
                            <div class="text-xs text-gray-300 font-bold italic">Vade farksız 3 taksit imkanı</div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Widget -->
                <div class="mb-10 p-6 bg-white border-2 border-slate-50 rounded-[30px] flex items-center gap-6 group hover:border-green-100 transition-all cursor-default">
                    <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 grow-0 shrink-0 shadow-sm relative group-hover:rotate-6 transition-transform">
                        <i class="fas fa-truck-fast text-2xl"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase italic tracking-tighter flex items-center gap-2">
                            AYNI GÜN KARGO
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-ping"></span>
                        </h4>
                        <p class="text-xs text-slate-400 font-medium leading-relaxed mt-1">Saat 16:00'a kadar olan siparişlerinizde paketiniz bugün yola çıksın!</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 mb-12 relative">
                    @if($product->price >= 700)
                        <div class="absolute -top-8 left-0 bg-green-500 text-white text-[10px] font-black px-4 py-1.5 rounded-full shadow-xl shadow-green-100 flex items-center gap-2 animate-bounce">
                            <i class="fas fa-truck-fast"></i>
                            BU ÜRÜNDE KARGO BEDAVA!
                        </div>
                    @endif
                    @php $imgArr = $product->productImages->first()?->url ?? 'https://via.placeholder.com/600x900'; @endphp
                    <button @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $imgArr }}'})" 
                            class="flex-grow h-20 bg-slate-900 text-white text-xl font-black rounded-3xl shadow-xl shadow-slate-100 hover:bg-orange-600 hover:shadow-orange-100 transition-all flex items-center justify-center gap-4 group active:scale-95">
                        <i class="fas fa-shopping-basket group-hover:rotate-12 transition-transform"></i>
                        <span>SEPETE EKLE</span>
                    </button>
                    <button @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $imgArr }}'})" 
                            class="w-20 h-20 border-2 border-gray-100 rounded-3xl flex items-center justify-center transition-all bg-white hover:border-red-100 active:scale-90" 
                            :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500 bg-red-50 border-red-50' : 'text-gray-300'">
                        <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart text-3xl' : 'far fa-heart text-3xl'"></i>
                    </button>
                </div>

                <!-- Merchant & Marketplaces -->
                <div class="flex flex-col gap-6 mb-12">
                    <!-- Global Store Hub -->
                    @if(!empty($marketplaces))
                        <div class="bg-slate-50/50 p-8 rounded-[40px] border-2 border-slate-100/50 group hover:border-orange-100 transition-all">
                            <div class="flex items-center justify-between mb-6">
                                <h5 class="text-xs font-black text-slate-800 uppercase tracking-widest italic flex items-center gap-3">
                                    <span class="w-8 h-px bg-orange-500"></span>
                                    DİĞER MAĞAZALARIMIZDA ÜRÜNÜ ZİYARET EDİN
                                </h5>
                            </div>
                            
                            <div class="flex flex-wrap gap-4">
                                @foreach($marketplaces as $mt)
                                    @php
                                        $customUrls = $product->raw_marketplace_data['custom_urls'] ?? [];
                                        $targetUrl = !empty($customUrls[$mt['name']]) ? $customUrls[$mt['name']] : $mt['url'];
                                    @endphp
                                    <a href="{{ $targetUrl }}" target="_blank" class="flex-grow md:flex-grow-0 flex items-center justify-center gap-3 bg-white px-6 py-4 rounded-2xl border-2 border-white shadow-sm hover:shadow-xl hover:border-slate-900 transition-all group/mt relative overflow-hidden">
                                        <div class="absolute inset-0 bg-slate-900 translate-y-full group-hover/mt:translate-y-0 transition-transform duration-300"></div>
                                        <div class="w-8 h-8 flex items-center justify-center overflow-hidden rounded-lg shrink-0 relative z-10 transition-transform group-hover/mt:scale-110">
                                            <img src="{{ $mt['logo'] }}" alt="{{ $mt['name'] }}" class="w-full h-full object-contain">
                                        </div>
                                        <span class="text-[11px] font-black text-slate-800 uppercase tracking-tighter relative z-10 group-hover/mt:text-white transition-colors">{{ $mt['name'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                <!-- Trust Badges -->
                <div class="grid grid-cols-3 gap-4 py-8 border-y border-gray-100">
                    <div class="flex flex-col items-center gap-3 text-center group">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                            <i class="fas fa-shield-alt text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter leading-tight">Orijinal Ürün Garantisi</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 text-center group">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-slate-400 group-hover:bg-green-50 group-hover:text-green-500 transition-colors">
                            <i class="fas fa-undo-alt text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter leading-tight">14 Gün Kolay İade</span>
                    </div>
                    <div class="flex flex-col items-center gap-3 text-center group">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-slate-400 group-hover:bg-amber-50 group-hover:text-amber-500 transition-colors">
                            <i class="fas fa-lock text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-slate-800 uppercase tracking-tighter leading-tight">Güvenli Ödeme SSL</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description & Details Tabs -->
        <div class="mt-24" x-data="{ tab: 'features' }">
            <div class="flex border-b border-gray-100 items-center justify-center gap-12 md:gap-16 sticky top-40 bg-white z-[100] py-6 bg-opacity-90 backdrop-blur-xl rounded-t-[40px] flex-wrap md:flex-nowrap">
                <button @click="tab = 'features'" :class="tab == 'features' ? 'text-slate-900 border-b-4 border-orange-500' : 'text-gray-400 grayscale'" class="pb-2 text-sm font-black italic uppercase tracking-widest transition-all">Ürün Özellikleri</button>
                <button @click="tab = 'returns'" :class="tab == 'returns' ? 'text-slate-900 border-b-4 border-orange-500' : 'text-gray-400 grayscale'" class="pb-2 text-sm font-black italic uppercase tracking-widest transition-all">İade Koşulları</button>
                <button @click="tab = 'comments'" :class="tab == 'comments' ? 'text-slate-900 border-b-4 border-orange-500' : 'text-gray-400 grayscale'" class="pb-2 text-sm font-black italic uppercase tracking-widest transition-all">Değerlendirmeler ({{ $product->approvedComments->count() }})</button>
            </div>
            
            <div class="py-16 max-w-5xl mx-auto min-h-[400px]">
                
                <div x-show="tab == 'features'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="text-slate-600 leading-relaxed text-sm space-y-8 bg-gray-50/50 p-12 rounded-[50px] border border-gray-100 italic mb-10">
                        {!! $product->description !!}
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-2">
                        @foreach($product->productAttributes as $attr)
                            <div class="flex items-center justify-between py-5 border-b border-gray-50 group hover:bg-slate-50 px-8 rounded-2xl transition-all">
                                <div class="text-xs font-black text-slate-400 uppercase tracking-widest group-hover:text-[var(--primary-color)] transition-colors">{{ $attr->name }}</div>
                                <div class="text-sm text-slate-800 font-bold italic">{!! $attr->value !!}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="tab == 'returns'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-indigo-50/50 p-12 rounded-[50px] border border-indigo-100/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <div class="space-y-6">
                            <h4 class="text-xl font-black italic tracking-tighter text-indigo-900 uppercase">
                                {{ $product->returnTemplate ? $product->returnTemplate->name . ' İade Koşulları' : 'Kolay İade Süreçleri' }}
                            </h4>
                            
                            @if($product->returnTemplate)
                                <div class="space-y-4">
                                    @foreach(explode("\n", $product->returnTemplate->content) as $line)
                                        @if(trim($line))
                                            <div class="flex items-start gap-4 bg-white/50 p-4 rounded-2xl border border-white">
                                                <i class="fas fa-check-circle mt-1 text-indigo-400"></i>
                                                <span class="text-xs text-indigo-800 font-bold leading-relaxed">{{ trim($line) }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-indigo-700 leading-relaxed font-medium">Satın aldığınız ürünleri, teslim aldığınız tarihten itibaren <strong>14 gün içerisinde</strong> herhangi bir gerekçe göstermeksizin iade edebilir veya değiştirebilirsiniz.</p>
                                <ul class="space-y-4 text-xs text-indigo-800 font-bold">
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-check-circle mt-1 text-indigo-400"></i>
                                        <span>İade edilecek ürünün ambalajı hasar görmemiş, kullanılmamış ve yeniden satılabilir durumda olmalıdır.</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <i class="fas fa-check-circle mt-1 text-indigo-400"></i>
                                        <span>Sağlık ve hijyen açısından uygun olmayan ürünlerin (iç çamaşırı, maske, steril ürünler vb.) ambalajı açıldıktan sonra iadesi kabul edilememektedir.</span>
                                    </li>
                                </ul>
                            @endif
                        </div>
                        <div class="bg-white p-8 rounded-[32px] shadow-xl shadow-indigo-100 border border-indigo-50">
                            <h5 class="text-[10px] font-black text-indigo-400 uppercase tracking-[0.2em] mb-4">Nasıl İade Ederim?</h5>
                            <ol class="space-y-6">
                                <li class="flex gap-4">
                                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-[10px] font-black shrink-0">1</span>
                                    <p class="text-[11px] text-indigo-900 font-bold">Hesabım > Siparişlerim sayfasından iade talebi oluşturun.</p>
                                </li>
                                <li class="flex gap-4">
                                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-[10px] font-black shrink-0">2</span>
                                    <p class="text-[11px] text-indigo-900 font-bold">Size verilen kargo kodu ile ürünü en yakın şubeden ücretsiz gönderin.</p>
                                </li>
                                <li class="flex gap-4">
                                    <span class="w-6 h-6 bg-indigo-600 text-white rounded-lg flex items-center justify-center text-[10px] font-black shrink-0">3</span>
                                    <p class="text-[11px] text-indigo-900 font-bold">Ürün tarafımıza ulaştıktan sonra 3 iş günü içinde ücret iadeniz yapılır.</p>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div x-show="tab == 'comments'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="py-12 px-4 md:px-8">
                    @php
                        $comments = $product->approvedComments;
                        $total = $comments->count();
                        $avg = $total > 0 ? round($comments->avg('rating'), 1) : 0;
                        $distribution = [];
                        for($i=5; $i>=1; $i--) {
                            $distribution[$i] = $total > 0 ? ($comments->where('rating', $i)->count() / $total) * 100 : 0;
                        }
                    @endphp
                    
                    <div class="grid lg:grid-cols-12 gap-12 text-left">
                        <!-- Rating Summary & Form Column -->
                        <div class="lg:col-span-4 space-y-8">
                            <!-- Summary Card -->
                            <div class="bg-gray-50/50 p-8 rounded-[40px] border border-gray-100">
                                <div class="flex items-center gap-6 mb-8">
                                    <div class="text-6xl font-black italic tracking-tighter text-slate-900">{{ $avg ?: '0.0' }}</div>
                                    <div>
                                        <div class="flex text-amber-400 text-sm mb-1">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="{{ $i <= round($avg) ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $total }} DEĞERLENDİRME</div>
                                    </div>
                                </div>
                                
                                <div class="space-y-3">
                                    @foreach($distribution as $star => $percent)
                                        <div class="flex items-center gap-4">
                                            <span class="text-[10px] font-black text-slate-400 w-4">{{ $star }}</span>
                                            <div class="flex-grow h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-slate-900" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <span class="text-[10px] font-black text-slate-900 w-8 text-right">{{ round($percent) }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Form Sticky -->
                            <div class="lg:sticky lg:top-52">
                                @auth
                                    <div class="bg-slate-900 p-8 rounded-[40px] shadow-2xl shadow-slate-200 relative overflow-hidden group">
                                         <div class="absolute -top-10 -right-10 w-32 h-32 bg-orange-500/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                                         <h4 class="text-xl font-black italic text-white uppercase tracking-tighter mb-6 relative z-10">Deneyiminizi Paylaşın</h4>
                                         <form action="{{ route('comment.store', $product) }}" method="POST" class="space-y-5 relative z-10" x-data="{ loading: false }" @submit="loading = true">
                                             @csrf
                                             <div>
                                                 <div class="flex gap-2" x-data="{ r: 5 }">
                                                     <input type="hidden" name="rating" :value="r">
                                                     <template x-for="i in 5">
                                                         <button type="button" @click="r = i" class="text-2xl transition-all hover:scale-110" :class="i <= r ? 'text-amber-400' : 'text-slate-700'" :disabled="loading">
                                                             <i :class="i <= r ? 'fas fa-star' : 'far fa-star'"></i>
                                                         </button>
                                                     </template>
                                                 </div>
                                             </div>
                                             <textarea name="content" rows="3" class="w-full bg-slate-800/50 border-0 rounded-2xl p-4 text-white text-xs focus:ring-2 focus:ring-orange-500 transition-all placeholder:text-slate-500" placeholder="Ürün nasıl? Başkalarına yardımcı olun..." required :readonly="loading"></textarea>
                                             <button type="submit" class="w-full py-4 bg-white text-slate-900 text-[10px] font-black rounded-2xl hover:bg-orange-500 hover:text-white transition-all uppercase tracking-widest disabled:opacity-50 disabled:pointer-events-none" :disabled="loading">
                                                 <span x-show="!loading">Yorumu Gönder</span>
                                                 <span x-show="loading"><i class="fas fa-spinner animate-spin mr-2"></i> Gönderiliyor...</span>
                                             </button>
                                         </form>
                                    </div>
                                @else
                                    <div class="bg-white p-8 rounded-[40px] border-2 border-dashed border-gray-100 flex flex-col items-center text-center gap-4">
                                         <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-slate-300">
                                             <i class="fas fa-lock"></i>
                                         </div>
                                         <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">Yorum yapmak için giriş yapmalısınız.</p>
                                         <a href="{{ route('login') }}" class="w-full py-4 bg-slate-900 text-white text-[10px] font-black rounded-2xl hover:bg-orange-500 transition-all uppercase tracking-widest">Giriş Yap</a>
                                    </div>
                                @endauth
                            </div>
                        </div>

                        <!-- Comment List Column -->
                        <div class="lg:col-span-8">
                            <div class="space-y-6">
                                @forelse($comments as $comment)
                                    <div class="bg-white p-8 rounded-[40px] border border-gray-100 hover:border-slate-900 transition-all group">
                                        <div class="flex items-start justify-between mb-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center font-black text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-xs font-black text-slate-900 uppercase tracking-tighter">{{ $comment->user->name }}</div>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <div class="flex text-amber-400 text-[8px]">
                                                            @for($i=1; $i<=5; $i++)
                                                                <i class="{{ $i <= $comment->rating ? 'fas' : 'far' }} fa-star"></i>
                                                            @endfor
                                                        </div>
                                                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ $comment->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1.5 bg-green-50 px-2 py-1 rounded-lg">
                                                <i class="fas fa-check-circle text-green-500 text-[10px]"></i>
                                                <span class="text-[9px] font-black text-green-700 uppercase tracking-tighter">Onaylı Alışveriş</span>
                                            </div>
                                        </div>
                                        <p class="text-slate-600 text-sm leading-relaxed font-medium pl-1">"{{ $comment->content }}"</p>
                                        
                                        @if($comment->admin_reply)
                                            <div class="mt-8 ml-4 md:ml-8 p-6 bg-slate-900 rounded-[32px] relative group/reply border-b-4 border-orange-500 shadow-2xl shadow-slate-200">
                                                <div class="absolute -top-4 -left-4 w-8 h-8 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg">
                                                    <i class="fas fa-reply-all text-[10px]"></i>
                                                </div>
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center text-orange-400 font-black text-[10px] italic">STORE</div>
                                                        <span class="text-[10px] font-black text-white uppercase tracking-widest italic">umutMed Market</span>
                                                    </div>
                                                    <span class="text-[9px] text-white/40 font-bold uppercase tracking-widest">{{ $comment->replied_at?->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-white/80 text-xs leading-relaxed font-medium italic">"{{ $comment->admin_reply }}"</p>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="py-20 text-center flex flex-col items-center gap-6">
                                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center text-gray-200">
                                            <i class="fas fa-comments text-4xl"></i>
                                        </div>
                                        <p class="text-sm font-black text-slate-300 uppercase tracking-[0.3em] italic">Bu ürün için henüz yorum yok.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        <div class="mt-32">
            <div class="flex items-baseline justify-between mb-12 border-b-2 border-gray-100 pb-8">
                <h3 class="text-4xl font-black italic tracking-tighter text-slate-900 uppercase">
                    Benzer <span class="text-[var(--primary-color)]">Ürünler</span>
                </h3>
                <a href="{{ route('home', ['category' => $product->category->slug ?? $product->category_id]) }}" class="text-xs font-black text-slate-400 hover:text-slate-900 transition-colors uppercase tracking-widest border-b-2 border-orange-100">Hepsini Gör</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-10">
                @foreach($relatedProducts as $rp)
                    <div class="group relative bg-white border border-gray-100 p-5 rounded-[40px] hover:shadow-2xl transition-all duration-500 overflow-hidden"
                         x-data="{ 
                            activeImage: 0, 
                            images: {{ $rp->productImages->pluck('url')->toJson() }} 
                         }">
                        <div class="aspect-[3/4] bg-gray-50 rounded-[30px] overflow-hidden mb-6 relative flex items-center justify-center"
                             @mouseleave="activeImage = 0">
                            
                            <!-- Images & Link -->
                            <a href="{{ route('product.show', $rp) }}" target="_blank" class="block w-full h-full relative z-10">
                                <template x-for="(image, index) in images.slice(0, 5)" :key="index">
                                    <img :src="image" 
                                         x-show="activeImage === index"
                                         x-transition:enter="transition opacity duration-300"
                                         x-transition:enter-start="opacity-0"
                                         x-transition:enter-end="opacity-100"
                                         class="absolute inset-0 w-full h-full object-contain p-6"
                                         :alt="'{{ addslashes($rp->name) }}'"
                                         style="display: none;">
                                </template>
                                <img x-show="images.length === 0" src="{{ $rp->productImages->first()?->url ?? 'https://via.placeholder.com/400x600' }}" alt="" class="w-full h-full object-contain p-6">

                                <!-- Hover Segments inside link to maintain clickability -->
                                <div class="absolute inset-0 flex">
                                    <template x-for="(image, index) in images.slice(0, 5)" :key="index">
                                        <div class="flex-1 h-full" @mouseenter="activeImage = index"></div>
                                    </template>
                                </div>
                            </a>

                            <!-- Dots -->
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-1.5 z-20 pointer-events-none opacity-0 group-hover:opacity-100 transition-opacity"
                                 x-show="images.length > 1">
                                <template x-for="(image, index) in images.slice(0, 5)" :key="index">
                                    <div class="h-1 rounded-full transition-all duration-300 bg-white shadow-sm"
                                         :class="activeImage === index ? 'w-4 bg-slate-900 border border-white' : 'w-1 bg-slate-400/50'"></div>
                                </template>
                            </div>
                            
                            <!-- Brand Overlays -->
                            <div class="absolute top-4 left-4 bg-white/80 backdrop-blur px-3 py-1 rounded-full border border-white shadow-sm opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0 z-30">
                                <span class="text-[9px] font-black text-slate-900 uppercase italic tracking-tighter">{{ $rp->brand->name ?? 'Marka' }}</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('product.show', $rp) }}" target="_blank">
                            <h4 class="text-xs text-gray-500 h-10 overflow-hidden line-clamp-2 leading-tight px-2 font-medium group-hover:text-slate-900 transition-colors">{{ $rp->name }}</h4>
                            <div class="mt-6 flex items-center justify-between px-2">
                                <div class="text-xl font-black text-slate-900 tracking-tighter group-hover:text-[var(--primary-color)] transition-colors">{{ number_format($rp->price, 2) }} TL</div>
                                <div class="text-[9px] font-black text-green-600 bg-green-50 px-2 py-0.5 rounded uppercase tracking-tighter">Ücretsiz Kargo</div>
                            </div>
                        </a>
                        
                        <!-- Quick Add -->
                        <button @click="$store.cart.add({id: '{{ $rp->id }}', slug: '{{ $rp->slug }}', name: '{{ addslashes($rp->name) }}', brand: '{{ addslashes($rp->brand->name ?? '') }}', price: {{ $rp->price }}, image: '{{ $rp->productImages->first()?->url ?? '' }}'})" 
                                class="absolute top-4 right-4 bg-slate-900 text-white w-12 h-12 rounded-2xl flex items-center justify-center opacity-0 translate-x-4 group-hover:opacity-100 group-hover:translate-x-0 transition-all shadow-xl hover:bg-orange-500 hover:scale-110 active:scale-95 z-30">
                            <i class="fas fa-cart-plus text-lg"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            Swal.fire({
                title: 'TEŞEKKÜRLER!',
                text: "{{ session('success') }}",
                icon: 'success',
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '{{ $primaryColor }}',
                confirmButtonText: 'TAMAM',
                customClass: {
                    popup: 'rounded-[40px] border-none shadow-2xl',
                    title: 'font-black italic tracking-tighter uppercase',
                    confirmButton: 'px-10 py-4 font-black rounded-2xl'
                }
            });
            @endif

            @if(session('error') || $errors->any())
            Swal.fire({
                title: 'BİR SORUN OLUŞTU!',
                text: "{{ session('error') ?? $errors->first() }}",
                icon: 'error',
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'TAMAM',
                customClass: {
                    popup: 'rounded-[40px] border-none shadow-2xl',
                    title: 'font-black italic tracking-tighter uppercase',
                    confirmButton: 'px-10 py-4 font-black rounded-2xl'
                }
            });
            @endif
        });
    </script>
@endsection
