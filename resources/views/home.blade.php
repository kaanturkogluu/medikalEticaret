@extends('layouts.app')

@section('title', 'Market Ana Sayfası')

@section('content')
    @php 
        $isFiltered = request()->hasAny(['category', 'brand', 'q', 'min_price', 'max_price']); 
    @endphp

    @if(!$isFiltered && \App\Models\Setting::getValue('banner_active', true) && $banners->count() > 0)
    <!-- Banner Section -->
    <div class="ty-container pt-8" 
         x-data="{ 
            activeBanner: 0, 
            count: {{ $banners->count() }}, 
            timer: null,
            init() { this.startTimer() },
            startTimer() {
                this.timer = setInterval(() => {
                    this.activeBanner = (this.activeBanner + 1) % this.count;
                }, 5000);
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
        <div class="relative rounded-[32px] overflow-hidden shadow-2xl group group-hover:shadow-3xl transition-all duration-500 bg-slate-100 h-[450px]">
            @foreach($banners as $index => $banner)
            <div x-show="activeBanner === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-700" 
                 x-transition:enter-start="opacity-0 scale-105" 
                 x-transition:enter-end="opacity-100 scale-100" 
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0"
                 style="display: none;"
                 x-cloak>
                <img src="{{ asset('storage/' . $banner->image_path) }}" class="w-full h-full object-cover" alt="Banner">
                
                <!-- Overlay Content -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/40 to-transparent flex items-center px-12 md:px-20">
                    <div class="max-w-2xl space-y-6">
                        @if($banner->subtitle)
                        <div class="inline-block bg-[var(--primary-color)] text-white px-4 py-1.5 rounded-full font-black uppercase tracking-widest italic shadow-xl"
                             style="color: {{ $banner->subtitle_color }}; font-size: {{ $banner->subtitle_size }}px;">
                            {{ $banner->subtitle }}
                        </div>
                        @endif
                        <h2 class="font-black leading-[1.1] italic tracking-tighter drop-shadow-2xl"
                            style="color: {{ $banner->title_color }}; font-size: {{ $banner->title_size }}px;">
                            {!! nl2br(e($banner->title)) !!}
                        </h2>
                        @if($banner->buttons && count($banner->buttons) > 0)
                        <div class="flex flex-wrap gap-4 pt-4">
                            @foreach($banner->buttons as $button)
                            <a href="{{ $button['link'] ?? '#' }}" class="px-8 py-4 rounded-xl font-black italic shadow-2xl flex items-center gap-3 transition-all transform hover:scale-105 group/btn border border-white/10"
                               style="background-color: {{ $button['bg'] ?? 'var(--primary-color)' }}; color: {{ $button['color'] ?? '#FFFFFF' }};">
                                {{ $button['text'] }} 
                                <i class="fas fa-chevron-right text-[10px] group-hover/btn:translate-x-1 transition-transform"></i>
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Decorative elements per banner can be added here if needed -->
            </div>
            @endforeach

            <!-- Slider Controls -->
            @if($banners->count() > 1)
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-3 z-10">
                @foreach($banners as $index => $banner)
                <button @click="goTo({{ $index }})" class="h-1.5 rounded-full transition-all duration-500" :class="activeBanner === {{ $index }} ? 'w-10 bg-[var(--primary-color)]' : 'w-3 bg-white/50 hover:bg-white'"></button>
                @endforeach
            </div>

            <!-- Arrows -->
            <button @click="prev()" class="absolute left-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                <i class="fas fa-chevron-left text-lg"></i>
            </button>
            <button @click="next()" class="absolute right-6 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full text-white flex items-center justify-center transition-all opacity-0 group-hover:opacity-100">
                <i class="fas fa-chevron-right text-lg"></i>
            </button>
            @endif
        </div>
    </div>
    @endif

    <!-- Featured Brands Section -->
    @if(!$isFiltered && $featuredBrands->count() > 0)
    <div class="ty-container pt-12 pb-6">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-1 h-6 bg-slate-900 rounded-full"></div>
            <h3 class="text-lg font-black italic tracking-tighter text-slate-900 uppercase">Öne Çıkan <span class="text-[var(--primary-color)]">Markalar</span></h3>
        </div>
        <div class="flex items-center justify-between gap-4 overflow-x-auto pb-4 custom-scrollbar">
            @foreach($featuredBrands as $brand)
            <a href="{{ route('home', ['brand' => $brand->slug]) }}" class="flex flex-col items-center gap-3 shrink-0 group">
                <div class="w-20 h-20 md:w-28 md:h-28 rounded-full bg-white border border-slate-100 shadow-sm group-hover:shadow-xl group-hover:border-[var(--primary-color)] transition-all duration-500 p-4 flex items-center justify-center overflow-hidden relative">
                    <div class="absolute inset-0 bg-[var(--primary-color)] opacity-0 group-hover:opacity-[0.03] transition-opacity"></div>
                    @if($brand->logo)
                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="w-full h-full object-contain filter grayscale group-hover:grayscale-0 transition-all duration-500 scale-90 group-hover:scale-100">
                    @else
                        <span class="text-xs font-black text-slate-300 italic uppercase">{{ substr($brand->name, 0, 2) }}</span>
                    @endif
                </div>
                <span class="text-[10px] md:text-xs font-black italic tracking-tighter text-slate-500 group-hover:text-[var(--primary-color)] uppercase transition-colors">{{ $brand->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Popular Products Section -->
    @php
        $popularActive = \App\Models\Setting::getValue('popular_section_active', true);
        $popularTitle = \App\Models\Setting::getValue('popular_section_title', 'Popüler Ürünler');
        $popularSubtitle = \App\Models\Setting::getValue('popular_section_subtitle', 'En Çok Tercih Edilenler');
    @endphp

    @if(!$isFiltered && $popularActive && $popularProducts->count() > 0)
    <section class="ty-container py-12">
        <div class="flex flex-col mb-8 gap-1">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-1.5 h-8 bg-[var(--primary-color)] rounded-full"></div>
                    <h2 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase">
                        @php
                            $titleParts = explode(' ', $popularTitle);
                            $lastWord = array_pop($titleParts);
                            $firstPart = implode(' ', $titleParts);
                        @endphp
                        {{ $firstPart }} <span class="text-[var(--primary-color)]">{{ $lastWord }}</span>
                    </h2>
                </div>
                <a href="{{ route('home', ['sort' => 'newest']) }}" class="text-xs font-black uppercase italic tracking-tighter text-slate-400 hover:text-[var(--primary-color)] transition-all underline decoration-2 underline-offset-4">Tümünü İncele <i class="fas fa-chevron-right ml-1"></i></a>
            </div>
            @if($popularSubtitle)
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] ml-5">{{ $popularSubtitle }}</p>
            @endif
        </div>

        <div class="flex overflow-x-auto pb-6 gap-6 custom-scrollbar scroll-smooth">
            @foreach($popularProducts as $product)
                <div class="flex-shrink-0 w-[240px] product-card">
                    <div class="product-image-container relative aspect-[2/3] bg-gray-50 overflow-hidden">
                        @php $img = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x600?text=Resim+Yok'; @endphp
                        <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform">
                        </a>
                        <button @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="favorite-btn absolute top-2 right-2 w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm" :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500' : 'text-gray-400'">
                            <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                        
                        <div class="absolute bottom-2 left-2 flex flex-col gap-1">
                            @if($product->is_popular)
                                <div class="bg-amber-400 text-white text-[9px] font-bold px-2 py-0.5 rounded shadow-sm border border-amber-500 w-fit">EDİTÖRÜN SEÇİMİ</div>
                            @endif
                            <div class="badge-free-shipping uppercase">Popüler Ürün</div>
                        </div>
                    </div>
                    
                    <div class="product-info p-3 flex flex-col flex-grow">
                        <div class="brand-name font-bold text-sm">{{ $product->brand->name ?? 'Markasız' }}</div>
                        <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                            <h3 class="product-name text-xs text-gray-500 line-clamp-2 h-8 mb-2 leading-tight">{{ $product->name }}</h3>
                        </a>
                        
                        <div class="flex items-center gap-1 mt-auto">
                            <div class="flex text-[10px] text-amber-400">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <span class="text-[10px] text-gray-400 font-bold">({{ $product->views }})</span>
                        </div>

                        <div class="mt-2">
                            <div class="text-[var(--primary-color)] font-black text-base">{{ number_format($product->price, 2) }} TL</div>
                        </div>
                        
                        <button @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="w-full mt-3 py-2 bg-slate-900 text-white text-[11px] font-black rounded hover:bg-slate-800 transition-colors uppercase tracking-widest">
                            Sepete Ekle
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Breadcrumb & Title for Filtered View -->
    @if($isFiltered)
    <div class="ty-container pt-8">
        <nav class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">
            <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors">Ana Sayfa</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            @if(request('category'))
                @php $cat = \App\Models\Category::where('slug', request('category'))->orWhere('id', request('category'))->first(); @endphp
                <span class="text-slate-900">{{ $cat->name ?? request('category') }}</span>
            @elseif(request('brand'))
                <span class="text-slate-900">Marka: {{ request('brand') }}</span>
            @elseif(request('q'))
                <span class="text-slate-900">Arama: {{ request('q') }}</span>
            @endif
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-gray-100 pb-6">
            <div>
                <h1 class="text-3xl font-black italic tracking-tighter text-slate-900 uppercase">
                    @if(request('category'))
                        {{ $cat->name ?? 'Kategori' }}
                    @elseif(request('brand'))
                        {{ strtoupper(request('brand')) }} ÜRÜNLERİ
                    @elseif(request('q'))
                        "{{ request('q') }}" ARAMA SONUÇLARI
                    @else
                        TÜM ÜRÜNLER
                    @endif
                </h1>
                <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest">Toplam {{ $products->total() }} ürün bulundu</p>
            </div>
            
            <!-- Active Filter Tags -->
            <div class="flex flex-wrap gap-2">
                @if(request('category'))
                    <a href="{{ route('home', request()->except('category', 'page')) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-gray-600 hover:bg-red-50 hover:text-red-500 transition-all flex items-center gap-2 group">
                        Kategori: {{ $cat->name ?? request('category') }} <i class="fas fa-times text-[8px] opacity-40 group-hover:opacity-100"></i>
                    </a>
                @endif
                @if(request('brand'))
                    <a href="{{ route('home', request()->except('brand', 'page')) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-gray-600 hover:bg-red-50 hover:text-red-500 transition-all flex items-center gap-2 group">
                        Marka: {{ request('brand') }} <i class="fas fa-times text-[8px] opacity-40 group-hover:opacity-100"></i>
                    </a>
                @endif
                @if(request('min_price') || request('max_price'))
                    <a href="{{ route('home', request()->except('min_price', 'max_price', 'page')) }}" class="bg-gray-100 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase text-gray-600 hover:bg-red-50 hover:text-red-500 transition-all flex items-center gap-2 group">
                        Fiyat: {{ request('min_price') ?? '0' }} - {{ request('max_price') ?? '∞' }} TL <i class="fas fa-times text-[8px] opacity-40 group-hover:opacity-100"></i>
                    </a>
                @endif
                @if($isFiltered)
                    <a href="{{ route('home') }}" class="text-[10px] font-black uppercase text-red-500 hover:underline flex items-center gap-1 ml-2">TÜMÜNÜ TEMİZLE</a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="ty-container py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Sidebar Filters -->
            <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block sticky top-40 h-fit self-start">
                
                <div class="filter-section border-t-0 pt-0" x-data="{ brandSearch: '' }">
                    <div class="filter-title">Markalar</div>
                    <div class="mb-3 relative">
                        <input type="text" x-model="brandSearch" @input="brandSearch = $event.target.value" placeholder="Marka ara..." class="w-full pl-8 p-2 text-xs border border-gray-200 rounded focus:outline-none focus:border-[var(--primary-color)] transition-colors">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                    </div>
                    <div class="max-h-64 overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        @foreach($brands as $brand)
                            @php $isActive = (request('brand') == $brand->id || request('brand') == $brand->slug); @endphp
                            <a href="{{ route('home', array_merge(request()->except('page'), ['brand' => $brand->slug])) }}" 
                               x-show="brandSearch === '' || '{{ str($brand->name)->lower() }}'.includes(brandSearch.toLowerCase())"
                               class="filter-item {{ $isActive ? 'text-slate-900 font-black bg-slate-50 rounded-xl' : '' }} px-2 py-2 group/item transition-all">
                                <span class="h-4 w-4 border-2 rounded-md flex items-center justify-center transition-all" :class="'{{ $isActive }}' ? 'border-slate-900 bg-slate-900' : 'border-gray-200 group-hover/item:border-gray-400'">
                                    @if($isActive) <i class="fas fa-check text-[8px] text-white"></i> @endif
                                </span>
                                <span class="flex-grow">{{ $brand->name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold">({{ $brand->products_count }})</span>
                            </a>
                        @endforeach
                        <div x-show="$el.querySelectorAll('a[style*=\'display: none\']').length === {{ count($brands) }}" class="text-xs text-gray-400 italic py-2 text-center">Sonuç bulunamadı</div>
                    </div>
                </div>

                <div class="filter-section border-none">
                    <div class="filter-title">Fiyat Aralığı</div>
                    <form action="{{ route('home') }}" method="GET" class="flex gap-2">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                        
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="En Az" class="w-1/2 p-2 text-xs border rounded focus:outline-none focus:border-[var(--primary-color)]">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="En Çok" class="w-1/2 p-2 text-xs border rounded focus:outline-none focus:border-[var(--primary-color)]">
                        <button type="submit" class="p-2 bg-[var(--primary-color)] text-white rounded"><i class="fas fa-chevron-right text-[10px]"></i></button>
                    </form>
                </div>
            </aside>

            <!-- Results Section -->
            <div class="flex-grow">
                <div class="bg-white p-4 rounded-3xl border border-gray-100 mb-6 flex items-center justify-between shadow-sm">
                    <div class="text-xs font-bold text-gray-500 uppercase tracking-widest italic">
                        @if(request('q'))
                            "<span class="text-slate-900 font-black">{{ request('q') }}</span>" sonuçları
                        @else
                            {{ $products->total() }} Ürün Sergileniyor
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <select onchange="let url = '{{ route('home', request()->except('sort', 'page')) }}'; let sep = url.includes('?') ? '&' : '?'; location.href = this.value ? url + sep + 'sort=' + this.value : url;" class="text-[11px] font-black uppercase italic bg-gray-50 border border-gray-100 px-4 py-2.5 rounded-xl focus:outline-none cursor-pointer hover:bg-gray-100 transition-all outline-none">
                            <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Önerilen Sıralama</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>En Düşük Fiyat</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>En Yüksek Fiyat</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>En Yeniler</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($products as $product)
                        <div class="product-card">
                            <div class="product-image-container relative aspect-[2/3] bg-gray-50 overflow-hidden">
                                @php $img = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x600?text=Resim+Yok'; @endphp
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                                    <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform">
                                </a>
                                <button @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="favorite-btn absolute top-2 right-2 w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm" :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500' : 'text-gray-400'">
                                    <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart' : 'far fa-heart'"></i>
                                </button>
                                
                                <div class="absolute bottom-2 left-2 flex flex-col gap-1">
                                    @if($product->stock > 0)
                                        <div class="badge-free-shipping">HIZLI TESLİMAT</div>
                                    @endif
                                    @if($product->price >= 700)
                                        <div class="bg-white text-[9px] font-bold px-2 py-0.5 rounded shadow-sm border border-gray-100 w-fit">KARGO BEDAVA</div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="product-info p-3 flex flex-col flex-grow">
                                <div class="brand-name font-bold text-sm">{{ $product->brand->name ?? 'Markasız' }}</div>
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                                    <h3 class="product-name text-xs text-gray-500 line-clamp-2 h-8 mb-2 leading-tight">{{ $product->name }}</h3>
                                </a>
                                
                                <div class="flex items-center gap-1 mt-auto">
                                    <div class="flex text-[10px] text-amber-400">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-bold">(152)</span>
                                </div>

                                <div class="mt-2">
                                    <div class="text-[10px] text-gray-400 line-through font-bold opacity-60">{{ number_format($product->price * 1.2, 2) }} TL</div>
                                    <div class="flex items-center justify-between">
                                        <div class="text-[var(--primary-color)] font-black text-base">{{ number_format($product->price, 2) }} TL</div>
                                        <div class="text-[9px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded tracking-tighter uppercase italic">EFT -%5</div>
                                    </div>
                                </div>
                                
                                <button @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="w-full mt-3 py-2 bg-slate-900 text-white text-[11px] font-black rounded hover:bg-slate-800 transition-colors uppercase tracking-widest">
                                    Sepete Ekle
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 flex flex-col items-center justify-center text-gray-400 gap-4">
                            <i class="fas fa-search-minus text-6xl"></i>
                            <p class="text-lg font-bold italic">Aradığınız kriterlere uygun ürün bulunamadı.</p>
                            <a href="{{ route('home') }}" class="text-[var(--primary-color)] font-bold underline">Hepsini Gör</a>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination (Custom Trendyol Style) -->
                {{ $products->links('partials.pagination') }}
            </div>
        </div>
    </main>

    <!-- Recently Viewed Section -->
    @if($recentlyViewedProducts->count() > 0)
    <section class="ty-container py-12 border-t border-slate-100 mt-12 bg-gray-50/50 rounded-[40px] px-8">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-1.5 h-8 bg-slate-400 rounded-full"></div>
                <h2 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase">Daha Önce <span class="text-slate-400">Görüntülenenler</span></h2>
            </div>
            <button @click="document.cookie = 'recently_viewed=; Max-Age=0; path=/'; location.reload();" class="text-[10px] font-black uppercase text-slate-400 hover:text-red-500 transition-colors italic">Geçmişi Temizle <i class="fas fa-trash-alt ml-1"></i></button>
        </div>

        <div class="flex overflow-x-auto pb-6 gap-6 custom-scrollbar scroll-smooth">
            @foreach($recentlyViewedProducts as $product)
                <div class="flex-shrink-0 w-[200px] product-card bg-white shadow-xl">
                    <div class="product-image-container relative aspect-[2/3] bg-gray-50 overflow-hidden">
                        @php $img = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x600?text=Resim+Yok'; @endphp
                        <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                            <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform">
                        </a>
                        <button @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="favorite-btn absolute top-2 right-2 w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm" :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500' : 'text-gray-400'">
                            <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart' : 'far fa-heart'"></i>
                        </button>
                    </div>
                    
                    <div class="product-info p-3 flex flex-col flex-grow">
                        <div class="brand-name font-bold text-[11px] truncate uppercase tracking-tighter text-gray-400">{{ $product->brand->name ?? 'Markasız' }}</div>
                        <a href="{{ route('product.show', $product->slug) }}" target="_blank">
                            <h3 class="product-name text-[11px] text-gray-500 line-clamp-1 h-4 mb-2 leading-tight font-bold">{{ $product->name }}</h3>
                        </a>
                        
                        <div class="mt-2">
                            <div class="text-[var(--primary-color)] font-black text-sm">{{ number_format($product->price, 2) }} TL</div>
                        </div>
                        
                        <button @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="w-full mt-3 py-1.5 bg-slate-900 text-white text-[10px] font-black rounded hover:bg-slate-800 transition-colors uppercase tracking-widest">
                            Sepete Ekle
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
@endsection

@section('styles')
    .product-card {
        background: var(--card-bg);
        border-radius: 8px;
        border: 1px solid var(--border-color);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .product-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-color: var(--primary-color);
        transform: translateY(-2px);
    }
    .filter-section {
        border-bottom: 1px solid var(--border-color);
        padding: 15px 0;
    }
    .filter-title {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .filter-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 0;
        font-size: 13px;
        color: var(--text-muted);
        cursor: pointer;
    }
    .filter-item:hover { color: var(--primary-color); }
    .badge-free-shipping {
        background-color: var(--accent-green);
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 700;
        width: fit-content;
    }
@endsection
