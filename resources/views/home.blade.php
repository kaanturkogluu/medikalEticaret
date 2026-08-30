@extends('layouts.app')

@section('title', 'Tıbbi Malzeme & Medikal Sağlık Ürünleri')

@section('content')
    @php 
        $isFiltered = request()->hasAny(['category', 'brand', 'q', 'min_price', 'max_price']); 
    @endphp

    <!-- Top Banner Slider (Visible when not filtering) -->
    @if(!$isFiltered && \App\Models\Setting::getValue('banner_active', true) && $banners->count() > 0)
    <div class="ty-container pt-6" 
         x-data="{ 
            activeBanner: 0, 
            count: {{ $banners->count() }}, 
            timer: null,
            init() { this.startTimer() },
            startTimer() {
                this.timer = setInterval(() => {
                    this.activeBanner = (this.activeBanner + 1) % this.count;
                }, 6000);
            },
            resetTimer() {
                clearInterval(this.timer);
                this.startTimer();
            },
            goTo(index) {
                this.activeBanner = index;
                this.resetTimer();
            },
            next() {
                this.activeBanner = (this.activeBanner + 1) % this.count;
                this.resetTimer();
            },
            prev() {
                this.activeBanner = (this.activeBanner - 1 + this.count) % this.count;
                this.resetTimer();
            }
         }">
        <div class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-sm border border-slate-200/80 bg-slate-100 h-[260px] sm:h-[360px] lg:h-[420px] group">
            @foreach($banners as $index => $banner)
            <div x-show="activeBanner === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-500" 
                 x-transition:enter-start="opacity-0 scale-[1.02]" 
                 x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0"
                 style="display: none;"
                 x-cloak>
                <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-full object-cover" alt="{{ $banner->title ?? 'Banner' }}">
                
                <!-- Elegant Backdrop Overlay (Only when banner has text or buttons) -->
                @php
                    $hasOverlayContent = !empty($banner->title) || !empty($banner->subtitle) || (!empty($banner->buttons) && count($banner->buttons) > 0);
                @endphp
                @if($hasOverlayContent)
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-900/40 to-transparent flex items-center px-6 sm:px-12 md:px-16">
                    <div class="max-w-xl space-y-3 sm:space-y-4">
                        @if($banner->subtitle)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur-xs border border-white/20"
                             style="{{ $banner->subtitle_color ? 'color: ' . $banner->subtitle_color . ';' : '' }}">
                            <i class="fas fa-certificate text-[10px]"></i>
                            <span>{{ $banner->subtitle }}</span>
                        </div>
                        @endif

                        @if($banner->title)
                        <h2 class="text-xl sm:text-3xl lg:text-4xl font-extrabold text-white leading-tight tracking-tight drop-shadow-md"
                            style="{{ $banner->title_color ? 'color: ' . $banner->title_color . ';' : '' }}">
                            {!! nl2br(e($banner->title)) !!}
                        </h2>
                        @endif

                        @if($banner->buttons && count($banner->buttons) > 0)
                        <div class="flex flex-wrap gap-3 pt-2">
                            @foreach($banner->buttons as $button)
                            <a href="{{ $button['link'] ?? '#' }}" class="px-5 py-2.5 sm:px-6 sm:py-3 rounded-xl font-bold text-xs sm:text-sm shadow-md flex items-center gap-2 transition-all hover:scale-105"
                               style="background-color: {{ $button['bg'] ?? 'var(--primary-color)' }}; color: {{ $button['color'] ?? '#FFFFFF' }};">
                                <span>{{ $button['text'] }}</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endforeach

            <!-- Slider Controls -->
            @if($banners->count() > 1)
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                @foreach($banners as $index => $banner)
                <button type="button" @click="goTo({{ $index }})" 
                        class="h-2 rounded-full transition-all duration-300" 
                        :class="activeBanner === {{ $index }} ? 'w-8 bg-emerald-500' : 'w-2 bg-white/60 hover:bg-white'"></button>
                @endforeach
            </div>

            <!-- Arrows -->
            <button type="button" @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/60 backdrop-blur-xs rounded-full text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            <button type="button" @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-black/30 hover:bg-black/60 backdrop-blur-xs rounded-full text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                <i class="fas fa-chevron-right text-sm"></i>
            </button>
            @endif
        </div>
    </div>
    @endif

    <!-- Medical Trust Matrix (4 Pillars) -->
    @if(!$isFiltered)
    <div class="ty-container pt-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
            
            <div class="bg-white border border-slate-200/90 rounded-xl p-4 flex items-center gap-3.5 shadow-2xs hover:border-emerald-400 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-lg">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 leading-tight">%100 Orijinal Ürün</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">T.C. Sağlık Bakanlığı ÜTS kayıtlı</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200/90 rounded-xl p-4 flex items-center gap-3.5 shadow-2xs hover:border-emerald-400 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 text-lg">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 leading-tight">Aynı Gün Kargo</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">16:00'a kadar sevk garantisi</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200/90 rounded-xl p-4 flex items-center gap-3.5 shadow-2xs hover:border-emerald-400 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-lg">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 leading-tight">SGK Uyumlu Fatura</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Hasta bezi & medikal geri ödeme</p>
                </div>
            </div>

            <div class="bg-white border border-slate-200/90 rounded-xl p-4 flex items-center gap-3.5 shadow-2xs hover:border-emerald-400 transition-colors">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 text-lg">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-slate-900 leading-tight">Medikal Uzman Desteği</h4>
                    <p class="text-[11px] text-slate-500 mt-0.5">Beden ve ürün danışma hattı</p>
                </div>
            </div>

        </div>
    </div>
    @endif

    <!-- Featured Brands Section -->
    @if(!$isFiltered && $featuredBrands->count() > 0)
    <div class="ty-container pt-10 pb-2">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-1.5 h-5 bg-emerald-600 rounded-full"></div>
                <h3 class="text-base font-bold text-slate-900">Yetkili & Öne Çıkan Medikal Markalar</h3>
            </div>
            <span class="text-xs text-slate-400">Resmi Distribütör Güvencesi</span>
        </div>

        <div class="flex items-center gap-3 overflow-x-auto pb-2 no-scrollbar">
            @foreach($featuredBrands as $brand)
            <a href="{{ route('home', ['brand' => $brand->slug]) }}" 
               class="bg-white border border-slate-200 hover:border-emerald-500 rounded-xl px-4 py-2.5 shrink-0 flex items-center gap-3 shadow-2xs hover:shadow-sm transition-all group min-w-[130px] sm:min-w-[160px]">
                <div class="w-9 h-9 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center p-1 overflow-hidden shrink-0">
                    @if($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="w-full h-full object-contain">
                    @else
                        <span class="text-xs font-bold text-emerald-700 uppercase">{{ substr($brand->name, 0, 2) }}</span>
                    @endif
                </div>
                <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-700 transition-colors truncate">{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Popular Products Slider Section -->
    @php
        $popularActive = \App\Models\Setting::getValue('popular_section_active', true);
        $popularTitle = \App\Models\Setting::getValue('popular_section_title', 'Popüler Ürünler');
        $popularSubtitle = \App\Models\Setting::getValue('popular_section_subtitle', 'En Çok Tercih Edilen Medikal Ürünler');
    @endphp

    @if(!$isFiltered && $popularActive && $popularProducts->count() > 0)
    <section class="ty-container pt-8 pb-4">
        <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-1.5 h-5 bg-emerald-600 rounded-full"></div>
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">
                        {{ $popularTitle }}
                    </h2>
                    @if($popularSubtitle)
                        <p class="text-[11px] text-slate-500">{{ $popularSubtitle }}</p>
                    @endif
                </div>
            </div>
            <a href="{{ route('home', ['sort' => 'newest']) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition-colors flex items-center gap-1">
                <span>Tümünü Gör</span>
                <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="flex overflow-x-auto pb-4 gap-4 no-scrollbar scroll-smooth">
            @foreach($popularProducts as $product)
                @php 
                    $pImg = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x400?text=Ürün';
                    $pComments = $product->approvedComments ?? collect();
                    $pCommentCount = $pComments->count();
                    $pAvgRating = $pCommentCount > 0 ? round($pComments->avg('rating'), 1) : null;
                @endphp
                <div class="flex-shrink-0 w-[200px] sm:w-[220px] bg-white border border-slate-200 hover:border-emerald-500 rounded-xl p-3 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between group">
                    
                    <div>
                        <!-- Image & Badges -->
                        <div class="relative aspect-square bg-slate-50 rounded-lg overflow-hidden p-2.5 mb-2.5">
                            <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                                <img src="{{ $pImg }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                            </a>

                            <!-- Badges -->
                            <div class="absolute top-2 left-2 flex flex-col gap-1 pointer-events-none">
                                @if($product->free_shipping || $product->price >= 700)
                                    <span class="bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">Ücretsiz Kargo</span>
                                @endif
                                @if($product->earned_points > 0)
                                    <span class="bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">+{{ $product->earned_points }} Puan</span>
                                @endif
                            </div>

                            <!-- Favorite button -->
                            <button type="button" 
                                    @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $pImg }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}})" 
                                    class="absolute top-2 right-2 w-7 h-7 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-xs text-xs transition-colors"
                                    :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500' : 'text-slate-400 hover:text-red-500'">
                                <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart' : 'far fa-heart'"></i>
                            </button>
                        </div>

                        <!-- Brand & Title -->
                        <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wide mb-1">
                            {{ $product->brand->name ?? 'Medikal' }}
                        </div>
                        <a href="{{ route('product.show', $product->slug) }}" class="block text-xs font-semibold text-slate-800 hover:text-emerald-700 line-clamp-2 leading-snug transition-colors" title="{{ $product->name }}">
                            {{ $product->name }}
                        </a>
                    </div>

                    <!-- Price & Cart Button -->
                    <div class="pt-3 mt-2 border-t border-slate-100 space-y-2">
                        <div class="flex items-baseline justify-between">
                            <div class="text-sm font-extrabold text-slate-900">
                                {{ number_format($product->price, 2, ',', '.') }} TL
                            </div>
                            @if($product->eft_discount)
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">EFT -%5</span>
                            @endif
                        </div>

                        @if($product->stock > 0)
                            <button type="button" 
                                    @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $pImg }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}, eft_discount: {{ $product->eft_discount ? 'true' : 'false' }}})" 
                                    class="w-full py-1.5 bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
                                <i class="fas fa-cart-plus text-[11px]"></i>
                                <span>Sepete Ekle</span>
                            </button>
                        @else
                            <button disabled class="w-full py-1.5 bg-slate-200 text-slate-500 text-xs font-bold rounded-lg cursor-not-allowed">
                                Stokta Yok
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Breadcrumb & Active Filters Bar (When Filtering) -->
    @if($isFiltered)
    <div class="ty-container pt-6">
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-3">
            <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors font-medium">Ana Sayfa</a>
            <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
            @if(request('category'))
                @php $cat = \App\Models\Category::where('slug', request('category'))->orWhere('id', request('category'))->first(); @endphp
                <span class="text-slate-800 font-semibold">{{ $cat->name ?? request('category') }}</span>
            @elseif(request('brand'))
                <span class="text-slate-800 font-semibold">Marka: {{ request('brand') }}</span>
            @elseif(request('q'))
                <span class="text-slate-800 font-semibold">"{{ request('q') }}" Arama Sonuçları</span>
            @else
                <span class="text-slate-800 font-semibold">Filtrelenmiş Ürünler</span>
            @endif
        </nav>
        
        <div class="flex flex-wrap items-center gap-2 pb-2">
            <span class="text-xs text-slate-400 font-medium">Aktif Filtreler:</span>
            @if(request('category'))
                <a href="{{ route('home', request()->except('category', 'page')) }}" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-2.5 py-1 rounded-lg text-xs font-medium hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-colors flex items-center gap-1.5">
                    <span>Kategori: {{ $cat->name ?? request('category') }}</span>
                    <i class="fas fa-times text-[10px]"></i>
                </a>
            @endif
            @if(request('brand'))
                <a href="{{ route('home', request()->except('brand', 'page')) }}" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-2.5 py-1 rounded-lg text-xs font-medium hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-colors flex items-center gap-1.5">
                    <span>Marka: {{ request('brand') }}</span>
                    <i class="fas fa-times text-[10px]"></i>
                </a>
            @endif
            @if(request('min_price') || request('max_price'))
                <a href="{{ route('home', request()->except('min_price', 'max_price', 'page')) }}" class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-2.5 py-1 rounded-lg text-xs font-medium hover:bg-red-50 hover:text-red-700 hover:border-red-200 transition-colors flex items-center gap-1.5">
                    <span>Fiyat: {{ request('min_price') ?? '0' }} - {{ request('max_price') ?? '∞' }} TL</span>
                    <i class="fas fa-times text-[10px]"></i>
                </a>
            @endif
            <a href="{{ route('home') }}" class="text-xs text-red-600 hover:underline font-bold ml-1">Filtreleri Temizle</a>
        </div>
    </div>
    @endif

    <!-- Main Content Grid with Perfectly Aligned Sidebar & Products -->
    <div class="ty-container py-6" x-data="{ mobileFilterOpen: false }">
        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
            
            <!-- Left Sidebar Filters (Desktop) -->
            <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block sticky top-28 self-start">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs space-y-6">
                    
                    <!-- Filter Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fas fa-sliders text-emerald-600"></i>
                            <span>Filtreler</span>
                        </h3>
                        @if($isFiltered)
                            <a href="{{ route('home') }}" class="text-[11px] text-red-600 hover:underline font-semibold">Temizle</a>
                        @endif
                    </div>

                    <!-- Category Filter List -->
                    <div class="space-y-2.5">
                        <div class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center justify-between">
                            <span>Kategoriler</span>
                        </div>
                        <div class="max-h-56 overflow-y-auto pr-1 custom-scrollbar space-y-1 text-xs">
                            @foreach($categories->take(12) as $categoryItem)
                                @php 
                                    $isCatActive = request('category') == $categoryItem->id || request('category') == $categoryItem->slug;
                                @endphp
                                <a href="{{ route('home', array_merge(request()->except('page'), ['category' => $categoryItem->slug ?? $categoryItem->id])) }}" 
                                   class="flex items-center justify-between px-2.5 py-1.5 rounded-lg transition-colors {{ $isCatActive ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <span class="truncate">{{ $categoryItem->name }}</span>
                                    <span class="text-[10px] text-slate-400 ml-1">({{ $categoryItem->products_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Brands Filter -->
                    <div class="space-y-2.5 pt-4 border-t border-slate-100" x-data="{ brandSearch: '' }">
                        <div class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center justify-between">
                            <span>Markalar</span>
                        </div>
                        
                        <div class="relative">
                            <input type="text" x-model="brandSearch" placeholder="Marka ara..." 
                                   class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:bg-white transition-colors">
                            <i class="fas fa-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                        </div>

                        <div class="max-h-60 overflow-y-auto pr-1 custom-scrollbar space-y-1 text-xs">
                            @foreach($brands as $brand)
                                @php $isBrandActive = (request('brand') == $brand->id || request('brand') == $brand->slug); @endphp
                                <a href="{{ route('home', array_merge(request()->except('page'), ['brand' => $brand->slug])) }}" 
                                   x-show="brandSearch === '' || '{{ str($brand->name)->lower() }}'.includes(brandSearch.toLowerCase())"
                                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg transition-colors {{ $isBrandActive ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <div class="w-3.5 h-3.5 rounded border flex items-center justify-center transition-colors {{ $isBrandActive ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-slate-300 bg-white' }}">
                                        @if($isBrandActive)
                                            <i class="fas fa-check text-[8px]"></i>
                                        @endif
                                    </div>
                                    <span class="flex-grow truncate">{{ $brand->name }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">({{ $brand->products_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range Filter -->
                    <div class="space-y-2.5 pt-4 border-t border-slate-100">
                        <div class="text-xs font-bold text-slate-800 uppercase tracking-wide">
                            <span>Fiyat Aralığı</span>
                        </div>
                        <form action="{{ route('home') }}" method="GET" class="space-y-2">
                            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                            
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min TL" 
                                       class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:bg-white transition-colors">
                                <span class="text-slate-400">-</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max TL" 
                                       class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-500 focus:bg-white transition-colors">
                            </div>
                            <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs">
                                Fiyata Göre Filtrele
                            </button>
                        </form>
                    </div>

                </div>
            </aside>

            <!-- Results Section (Right Side) -->
            <div class="flex-grow w-full">
                
                <!-- Perfectly Aligned Top Toolbar -->
                <div class="bg-white border border-slate-200 rounded-2xl p-3.5 sm:p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    
                    <div class="flex items-center gap-3">
                        <!-- Mobile Filter Button Trigger -->
                        <button type="button" @click="mobileFilterOpen = true" 
                                class="lg:hidden inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold px-3.5 py-2 rounded-xl text-xs transition-colors">
                            <i class="fas fa-sliders text-emerald-600"></i>
                            <span>Filtrele</span>
                            @if($isFiltered)
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            @endif
                        </button>

                        <div class="text-xs text-slate-600">
                            @if(request('q'))
                                "<strong class="text-slate-900">{{ request('q') }}</strong>" için <strong class="text-slate-900">{{ $products->total() }}</strong> ürün bulundu
                            @else
                                Toplam <strong class="text-slate-900">{{ $products->total() }}</strong> medikal ürün listeleniyor
                            @endif
                        </div>
                    </div>

                    <!-- Sort Select Dropdown -->
                    <div class="flex items-center gap-2">
                        <label for="sort-select" class="text-xs text-slate-500 font-medium hidden sm:inline">Sıralama:</label>
                        <select id="sort-select" onchange="let url = '{{ route('home', request()->except('sort', 'page')) }}'; let sep = url.includes('?') ? '&' : '?'; location.href = this.value ? url + sep + 'sort=' + this.value : url;" 
                                class="text-xs font-semibold text-slate-700 bg-slate-50 border border-slate-200 px-3 py-2 rounded-xl focus:outline-none focus:border-emerald-500 cursor-pointer hover:bg-slate-100 transition-colors">
                            <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Önerilen Sıralama</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>En Düşük Fiyat</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>En Yüksek Fiyat</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>En Yeni Eklenenler</option>
                        </select>
                    </div>

                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                    @forelse($products as $product)
                        @php 
                            $pImg = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x400?text=Ürün';
                            $pComments = $product->approvedComments ?? collect();
                            $pCommentCount = $pComments->count();
                            $pAvgRating = $pCommentCount > 0 ? round($pComments->avg('rating'), 1) : null;
                        @endphp
                        <div class="bg-white border border-slate-200 hover:border-emerald-500 rounded-xl p-3 sm:p-3.5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between group">
                            
                            <div>
                                <!-- Image Container -->
                                <div class="relative aspect-square bg-slate-50/70 rounded-lg overflow-hidden p-3 mb-3">
                                    <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                                        <img src="{{ $pImg }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                                    </a>

                                    <!-- Badges -->
                                    <div class="absolute top-2 left-2 flex flex-col gap-1 pointer-events-none">
                                        @if($product->free_shipping || $product->price >= 700)
                                            <span class="bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">Ücretsiz Kargo</span>
                                        @endif
                                        @if($product->earned_points > 0)
                                            <span class="bg-blue-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-2xs">+{{ $product->earned_points }} Puan</span>
                                        @endif
                                    </div>

                                    <!-- Favorite Button -->
                                    <button type="button" 
                                            @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $pImg }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}})" 
                                            class="absolute top-2 right-2 w-7 h-7 bg-white/90 hover:bg-white rounded-full flex items-center justify-center shadow-xs text-xs transition-colors"
                                            :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500' : 'text-slate-400 hover:text-red-500'"
                                            title="Favorilere Ekle">
                                        <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart' : 'far fa-heart'"></i>
                                    </button>
                                </div>

                                <!-- Brand & Title -->
                                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wide mb-1">
                                    {{ $product->brand->name ?? 'Medikal' }}
                                </div>
                                <a href="{{ route('product.show', $product->slug) }}" class="block text-xs font-semibold text-slate-800 hover:text-emerald-700 line-clamp-2 leading-snug transition-colors" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </a>

                                <!-- Rating & Views indicator -->
                                <div class="flex items-center gap-1.5 mt-1.5 text-[11px] text-slate-400">
                                    @if($pCommentCount > 0)
                                        <div class="flex text-amber-400 text-[10px]">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="{{ $i <= round($pAvgRating) ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <span class="font-semibold text-slate-600">({{ $pCommentCount }})</span>
                                    @else
                                        <span class="text-[10px] text-slate-400"><i class="far fa-eye mr-0.5"></i> {{ number_format($product->views) }}</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Price & Add to Cart -->
                            <div class="pt-3 mt-2 border-t border-slate-100 space-y-2">
                                <div class="flex items-baseline justify-between">
                                    <div class="text-sm sm:text-base font-extrabold text-slate-900">
                                        {{ number_format($product->price, 2, ',', '.') }} TL
                                    </div>
                                    @if($product->eft_discount)
                                        <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">EFT -%5</span>
                                    @endif
                                </div>

                                @if($product->stock > 0)
                                    <button type="button" 
                                            @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $pImg }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}, eft_discount: {{ $product->eft_discount ? 'true' : 'false' }}})" 
                                            class="w-full py-2 bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition-colors flex items-center justify-center gap-1.5 shadow-2xs active:scale-[0.98]">
                                        <i class="fas fa-cart-plus text-xs"></i>
                                        <span>Sepete Ekle</span>
                                    </button>
                                @else
                                    <button disabled class="w-full py-2 bg-slate-200 text-slate-500 text-xs font-bold rounded-lg cursor-not-allowed">
                                        Tükendi
                                    </button>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full py-16 bg-white border border-slate-200 rounded-2xl text-center space-y-3 p-8">
                            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                                <i class="fas fa-search-minus"></i>
                            </div>
                            <h4 class="text-base font-bold text-slate-800">Aradığınız Kriterlere Uygun Ürün Bulunamadı</h4>
                            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                                Farklı bir arama terimi deneyebilir veya aktif filtreleri temizleyerek tüm medikal ürünlerimizi görüntüleyebilirsiniz.
                            </p>
                            <a href="{{ route('home') }}" class="inline-block px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                                Tüm Ürünleri Göster
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Custom Clean Pagination -->
                @if($products->hasPages())
                    <div class="mt-8">
                        {{ $products->links('partials.pagination') }}
                    </div>
                @endif

            </div>
        </div>

        <!-- Mobile Filter Offcanvas Drawer -->
        <div x-show="mobileFilterOpen" 
             x-cloak 
             x-effect="document.body.style.overflow = mobileFilterOpen ? 'hidden' : ''"
             class="fixed inset-0 z-[9999] lg:hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="mobileFilterOpen = false"></div>

            <!-- Drawer Container -->
            <div class="fixed inset-y-0 left-0 max-w-xs w-full bg-white shadow-2xl z-10 flex flex-col"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full">
                
                <!-- Drawer Header -->
                <div class="p-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fas fa-sliders text-emerald-600"></i>
                        <span>Filtreler</span>
                    </h3>
                    <button type="button" @click="mobileFilterOpen = false" class="w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-slate-900 flex items-center justify-center">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <!-- Drawer Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-4 space-y-6">
                    <!-- Categories -->
                    <div class="space-y-2">
                        <div class="text-xs font-bold text-slate-900 uppercase">Kategoriler</div>
                        <div class="space-y-1 text-xs">
                            @foreach($categories as $categoryItem)
                                @php $isCatActive = request('category') == $categoryItem->id || request('category') == $categoryItem->slug; @endphp
                                <a href="{{ route('home', array_merge(request()->except('page'), ['category' => $categoryItem->slug ?? $categoryItem->id])) }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg {{ $isCatActive ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-700 bg-slate-50' }}">
                                    <span>{{ $categoryItem->name }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $categoryItem->products_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <div class="text-xs font-bold text-slate-900 uppercase">Markalar</div>
                        <div class="space-y-1 max-h-48 overflow-y-auto pr-1 custom-scrollbar text-xs">
                            @foreach($brands as $brand)
                                @php $isBrandActive = (request('brand') == $brand->id || request('brand') == $brand->slug); @endphp
                                <a href="{{ route('home', array_merge(request()->except('page'), ['brand' => $brand->slug])) }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-lg {{ $isBrandActive ? 'bg-emerald-50 text-emerald-800 font-bold border border-emerald-200' : 'text-slate-700 bg-slate-50' }}">
                                    <span>{{ $brand->name }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $brand->products_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="space-y-2 pt-4 border-t border-slate-100">
                        <div class="text-xs font-bold text-slate-900 uppercase">Fiyat Aralığı</div>
                        <form action="{{ route('home') }}" method="GET" class="space-y-2">
                            @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                            @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                            <div class="flex items-center gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full p-2 text-xs border border-slate-200 rounded-lg">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full p-2 text-xs border border-slate-200 rounded-lg">
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-lg">Filtrele</button>
                        </form>
                    </div>
                </div>

                <!-- Drawer Footer -->
                @if($isFiltered)
                    <div class="p-4 border-t border-slate-200 bg-slate-50">
                        <a href="{{ route('home') }}" class="block w-full py-2.5 text-center bg-red-50 text-red-700 border border-red-200 font-bold text-xs rounded-xl">
                            Filtreleri Temizle
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Recently Viewed Section -->
    @if(!$isFiltered && $recentlyViewedProducts->count() > 0)
    <section class="ty-container py-10 mt-6 border-t border-slate-200">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-1.5 h-5 bg-slate-400 rounded-full"></div>
                <h2 class="text-base font-bold text-slate-900">Son İncelediğiniz Medikal Ürünler</h2>
            </div>
            <button type="button" @click="document.cookie = 'recently_viewed=; Max-Age=0; path=/'; location.reload();" 
                    class="text-xs text-slate-400 hover:text-red-500 transition-colors flex items-center gap-1">
                <i class="fas fa-trash-alt text-[10px]"></i>
                <span>Geçmişi Temizle</span>
            </button>
        </div>

        <div class="flex overflow-x-auto pb-4 gap-4 no-scrollbar scroll-smooth">
            @foreach($recentlyViewedProducts as $product)
                @php $rImg = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x400?text=Ürün'; @endphp
                <div class="flex-shrink-0 w-[180px] bg-white border border-slate-200 hover:border-emerald-500 rounded-xl p-3 shadow-2xs hover:shadow-sm transition-all flex flex-col justify-between group">
                    <div>
                        <div class="relative aspect-square bg-slate-50 rounded-lg overflow-hidden p-2 mb-2">
                            <a href="{{ route('product.show', $product->slug) }}" class="block w-full h-full">
                                <img src="{{ $rImg }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                            </a>
                        </div>
                        <div class="text-[10px] font-bold text-emerald-700 uppercase truncate mb-0.5">{{ $product->brand->name ?? 'Medikal' }}</div>
                        <a href="{{ route('product.show', $product->slug) }}" class="block text-xs font-semibold text-slate-800 hover:text-emerald-700 line-clamp-1 leading-snug">
                            {{ $product->name }}
                        </a>
                    </div>
                    <div class="pt-2 mt-2 border-t border-slate-100 flex items-center justify-between">
                        <div class="text-xs font-bold text-slate-900">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                        @if($product->stock > 0)
                            <button type="button" 
                                    @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $rImg }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}, eft_discount: {{ $product->eft_discount ? 'true' : 'false' }}})" 
                                    class="w-7 h-7 rounded-lg bg-slate-900 hover:bg-emerald-600 text-white flex items-center justify-center text-xs transition-colors">
                                <i class="fas fa-cart-plus text-[10px]"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
@endsection

@section('styles')
<style>
    /* Sleek no-scrollbar cross-browser */
    .no-scrollbar::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
    }
    .no-scrollbar {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }
</style>
@endsection
