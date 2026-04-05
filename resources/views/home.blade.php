@extends('layouts.app')

@section('title', 'Market Ana Sayfası')

@section('sub_header')
    <!-- Sub Navbar Categories -->
    <nav class="category-nav hidden md:block">
        <div class="ty-container flex items-center justify-center">
            @if($categories->count() > 10)
                <div class="category-link cursor-pointer group" x-data="{ allCatSearch: '' }">
                    <span class="flex items-center gap-2 font-black italic">TÜM KATEGORİLER <i class="fas fa-chevron-down text-[10px]"></i></span>
                    <div class="absolute hidden group-hover:block top-full left-0 bg-white border border-gray-100 shadow-2xl p-0 w-[1000px] z-[1001] rounded-b-xl overflow-hidden">
                        <!-- Search Sidebar in Dropdown -->
                        <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                            <div class="relative">
                                <input type="text" x-model="allCatSearch" placeholder="Kategoriler arasında hızlıca ara..." class="w-full pl-10 p-3 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)]/20 focus:border-[var(--primary-color)] transition-all">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <!-- Categories Scrollable Grid -->
                        <div class="p-8 grid grid-cols-4 gap-x-8 max-h-[450px] overflow-y-auto custom-scrollbar">
                            @foreach($categories as $cat)
                                <a href="{{ route('home', ['category' => $cat->id]) }}" 
                                   x-show="allCatSearch === '' || '{{ str($cat->name)->lower() }}'.includes(allCatSearch.toLowerCase())"
                                   class="py-3 text-[13px] hover:text-[var(--primary-color)] hover:translate-x-1 transition-all font-medium border-b border-gray-50 last:border-0 flex items-center justify-between group/item">
                                    <span>{{ $cat->name }}</span>
                                    <i class="fas fa-chevron-right text-[10px] opacity-0 group-hover/item:opacity-100 transition-opacity"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @foreach($categories->take(9) as $cat)
                <a href="{{ route('home', ['category' => $cat->id]) }}" class="category-link {{ request('category') == $cat->id ? 'text-[var(--primary-color)] border-b-2 border-[var(--primary-color)]' : '' }}">
                    {{ str($cat->name)->upper() }}
                </a>
            @endforeach
        </div>
    </nav>
@endsection

@section('content')
    @if(\App\Models\Setting::getValue('banner_active', true) && $banners->count() > 0)
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

    <!-- Main Content -->
    <main class="ty-container py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Sidebar Filters -->
            <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">
                <h3 class="text-lg font-bold mb-4">Filtreler</h3>
                
                <div class="filter-section" x-data="{ catSearch: '' }">
                    <div class="filter-title">İlgili Kategoriler</div>
                    <div class="mb-3 relative">
                        <input type="text" x-model="catSearch" placeholder="Kategori ara..." class="w-full pl-8 p-2 text-xs border border-gray-200 rounded focus:outline-none focus:border-[var(--primary-color)] transition-colors">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                    </div>
                    <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        @foreach($categories as $cat)
                            <a href="{{ route('home', array_merge(request()->all(), ['category' => $cat->id])) }}" 
                               x-show="catSearch === '' || '{{ str($cat->name)->lower() }}'.includes(catSearch.toLowerCase())"
                               class="filter-item {{ request('category') == $cat->id ? 'text-[var(--primary-color)] font-bold' : '' }}">
                                <span class="flex-grow">{{ $cat->name }}</span>
                                <span class="text-[10px] text-gray-400">({{ $cat->products_count }})</span>
                            </a>
                        @endforeach
                        <div x-show="$el.querySelectorAll('a[style*=\'display: none\']').length === {{ count($categories) }}" class="text-xs text-gray-400 italic py-2">Sonuç bulunamadı</div>
                    </div>
                </div>

                <div class="filter-section" x-data="{ brandSearch: '' }">
                    <div class="filter-title">Markalar</div>
                    <div class="mb-3 relative">
                        <input type="text" x-model="brandSearch" @input="brandSearch = $event.target.value" placeholder="Marka ara..." class="w-full pl-8 p-2 text-xs border border-gray-200 rounded focus:outline-none focus:border-[var(--primary-color)] transition-colors">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[10px]"></i>
                    </div>
                    <div class="max-h-64 overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        @foreach($brands as $brand)
                            <a href="{{ route('home', array_merge(request()->all(), ['brand' => $brand->id])) }}" 
                               x-show="brandSearch === '' || '{{ str($brand->name)->lower() }}'.includes(brandSearch.toLowerCase())"
                               class="filter-item {{ request('brand') == $brand->id ? 'text-[var(--primary-color)] font-bold' : '' }}">
                                <span class="h-4 w-4 border border-gray-300 rounded flex items-center justify-center">
                                    @if(request('brand') == $brand->id) <i class="fas fa-check text-[8px] text-[var(--primary-color)]"></i> @endif
                                </span>
                                <span class="flex-grow">{{ $brand->name }}</span>
                                <span class="text-[10px] text-gray-400">({{ $brand->products_count }})</span>
                            </a>
                        @endforeach
                        <div x-show="$el.querySelectorAll('a[style*=\'display: none\']').length === {{ count($brands) }}" class="text-xs text-gray-400 italic py-2 text-center">Sonuç bulunamadı</div>
                    </div>
                </div>

                <div class="filter-section border-none">
                    <div class="filter-title">Fiyat Aralığı</div>
                    <form action="{{ route('home') }}" method="GET" class="flex gap-2">
                        @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        @if(request('brand')) <input type="hidden" name="brand" value="{{ request('brand') }}"> @endif
                        
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="En Az" class="w-1/2 p-2 text-xs border rounded focus:outline-none focus:border-[var(--primary-color)]">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="En Çok" class="w-1/2 p-2 text-xs border rounded focus:outline-none focus:border-[var(--primary-color)]">
                        <button type="submit" class="p-2 bg-[var(--primary-color)] text-white rounded"><i class="fas fa-chevron-right text-[10px]"></i></button>
                    </form>
                </div>
            </aside>

            <!-- Results Section -->
            <div class="flex-grow">
                <div class="bg-white p-4 rounded-lg border border-gray-200 mb-6 flex items-center justify-between shadow-sm">
                    <div class="text-sm">
                        @if(request('q'))
                            "<span class="font-bold">{{ request('q') }}</span>" araması için <span class="font-bold">{{ $products->total() }}</span> sonuç listeleniyor.
                        @else
                            Toplam <span class="font-bold">{{ $products->total() }}</span> ürün listeleniyor.
                        @endif
                    </div>
                    <div class="flex items-center gap-4">
                        <select onchange="let url = '{{ route('home', request()->except('sort', 'page')) }}'; let sep = url.includes('?') ? '&' : '?'; location.href = this.value ? url + sep + 'sort=' + this.value : url;" class="text-sm bg-white border border-gray-200 p-2 rounded focus:outline-none cursor-pointer">
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
                                <a href="{{ route('product.show', $product->id) }}" target="_blank">
                                    <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-2 group-hover:scale-105 transition-transform">
                                </a>
                                <button @click="$store.fav.toggle({id: '{{ $product->id }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="favorite-btn absolute top-2 right-2 w-8 h-8 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm" :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500' : 'text-gray-400'">
                                    <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart' : 'far fa-heart'"></i>
                                </button>
                                
                                <div class="absolute bottom-2 left-2 flex flex-col gap-1">
                                    @if($product->stock > 0)
                                        <div class="badge-free-shipping">HIZLI TESLİMAT</div>
                                    @endif
                                    <div class="bg-white text-[9px] font-bold px-2 py-0.5 rounded shadow-sm border border-gray-100 w-fit">KARGO BEDAVA</div>
                                </div>
                            </div>
                            
                            <div class="product-info p-3 flex flex-col flex-grow">
                                <div class="brand-name font-bold text-sm">{{ $product->brand->name ?? 'Markasız' }}</div>
                                <a href="{{ route('product.show', $product->id) }}" target="_blank">
                                    <h3 class="product-name text-xs text-gray-500 line-clamp-2 h-8 mb-2 leading-tight">{{ $product->name }}</h3>
                                </a>
                                
                                <div class="flex items-center gap-1 mt-auto">
                                    <div class="flex text-[10px] text-amber-400">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-bold">(152)</span>
                                </div>

                                <div class="mt-2">
                                    <div class="text-[var(--primary-color)] font-black text-base">{{ number_format($product->price, 2) }} TL</div>
                                </div>
                                
                                <button @click="$store.cart.add({id: '{{ $product->id }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, image: '{{ $img }}'})" class="w-full mt-3 py-2 bg-slate-900 text-white text-[11px] font-black rounded hover:bg-slate-800 transition-colors uppercase tracking-widest">
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
