@extends('layouts.app')

@section('title', 'Market Ana Sayfası')

@section('sub_header')
    <!-- Sub Navbar Categories -->
    <nav class="category-nav hidden md:block">
        <div class="ty-container flex items-center justify-center">
            @foreach($categories->take(10) as $cat)
                <a href="{{ route('home', ['category' => $cat->id]) }}" class="category-link {{ request('category') == $cat->id ? 'text-[var(--primary-color)]' : '' }}">
                    {{ str($cat->name)->upper() }}
                </a>
            @endforeach
            @if($categories->count() > 10)
                <div class="category-link cursor-pointer group">
                    TÜM KATEGORİLER
                    <div class="absolute hidden group-hover:flex top-full left-0 bg-white border border-gray-200 shadow-xl p-6 grid grid-cols-4 w-[800px] z-[1001]">
                        @foreach($categories->slice(10) as $cat)
                            <a href="{{ route('home', ['category' => $cat->id]) }}" class="py-2 text-xs hover:text-[var(--primary-color)]">{{ $cat->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </nav>
@endsection

@section('content')
    <!-- Main Content -->
    <main class="ty-container py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Sidebar Filters -->
            <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">
                <h3 class="text-lg font-bold mb-4">Filtreler</h3>
                
                <div class="filter-section">
                    <div class="filter-title">İlgili Kategoriler</div>
                    <div class="max-h-48 overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        @foreach($categories as $cat)
                            <a href="{{ route('home', array_merge(request()->all(), ['category' => $cat->id])) }}" 
                               class="filter-item {{ request('category') == $cat->id ? 'text-[var(--primary-color)] font-bold' : '' }}">
                                <span class="flex-grow">{{ $cat->name }}</span>
                                <span class="text-[10px] text-gray-400">({{ $cat->products_count }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-title">Markalar</div>
                    <div class="max-h-64 overflow-y-auto pr-2 custom-scrollbar space-y-1">
                        @foreach($brands as $brand)
                            <a href="{{ route('home', array_merge(request()->all(), ['brand' => $brand->id])) }}" 
                               class="filter-item {{ request('brand') == $brand->id ? 'text-[var(--primary-color)] font-bold' : '' }}">
                                <span class="h-4 w-4 border border-gray-300 rounded flex items-center justify-center">
                                    @if(request('brand') == $brand->id) <i class="fas fa-check text-[8px] text-[var(--primary-color)]"></i> @endif
                                </span>
                                <span class="flex-grow">{{ $brand->name }}</span>
                                <span class="text-[10px] text-gray-400">({{ $brand->products_count }})</span>
                            </a>
                        @endforeach
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
                        <select class="text-sm bg-white border border-gray-200 p-2 rounded focus:outline-none">
                            <option>Önerilen Sıralama</option>
                            <option>En Düşük Fiyat</option>
                            <option>En Yüksek Fiyat</option>
                            <option>En Yeniler</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($products as $product)
                        <div class="product-card">
                            <div class="product-image-container relative aspect-[2/3] bg-gray-50 overflow-hidden">
                                @php $img = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x600?text=Resim+Yok'; @endphp
                                <a href="{{ route('product.show', $product->id) }}">
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
                                <a href="{{ route('product.show', $product->id) }}">
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
