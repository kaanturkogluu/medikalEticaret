<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Ummet Medikal') }} - Trendyol Market</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            /* Theme Colors - Modify these to change look and feel */
            --primary-color: #f27a1a;
            --primary-hover: #e67216;
            --background-color: #f5f5f5;
            --card-bg: #ffffff;
            --text-main: #333333;
            --text-muted: #666666;
            --border-color: #e6e6e6;
            --price-color: #f27a1a;
            --accent-green: #0bc15c;
            --accent-blue: #3399ff;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            color: var(--text-main);
        }

        .ty-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Navbar & Header */
        header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .search-bar {
            background-color: #f3f3f3;
            border-radius: 6px;
            padding: 8px 40px 8px 16px;
            width: 100%;
            border: 2px solid transparent;
            transition: all 0.2s;
        }

        .search-bar:focus {
            background-color: white;
            border-color: var(--primary-color);
            outline: none;
        }

        .category-nav {
            background: white;
            border-bottom: 1px solid var(--border-color);
        }

        .category-link {
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-main);
            transition: color 0.2s;
            position: relative;
        }

        .category-link:hover {
            color: var(--primary-color);
        }

        .category-link:hover::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
        }

        /* Product Card */
        .product-card {
            background: var(--card-bg);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .product-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .product-image-container {
            position: relative;
            aspect-ratio: 2/3;
            background: #f9f9f9;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .favorite-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 32px;
            height: 32px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #999;
            transition: all 0.2s;
        }

        .favorite-btn:hover {
            color: #ff4757;
            transform: scale(1.1);
        }

        .product-info {
            padding: 10px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .brand-name {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .product-name {
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 36px;
        }

        .price-container {
            margin-top: 8px;
        }

        .product-price {
            color: var(--price-color);
            font-weight: 800;
            font-size: 16px;
        }

        /* Filter Sidebar */
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

        .filter-item:hover {
            color: var(--primary-color);
        }

        /* Pagination */
        .pagination-link {
            padding: 8px 14px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            background: white;
        }

        .pagination-link.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Badges */
        .badge-free-shipping {
            background-color: var(--accent-green);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 700;
            width: fit-content;
            margin-top: 4px;
        }
    </style>
</head>
<body x-data="{ mobileFilters: false }">

    <!-- Top Info Bar -->
    <div class="bg-gray-100 hidden md:block">
        <div class="ty-container h-8 flex items-center justify-end gap-6 text-[11px] text-gray-500 font-medium">
            <a href="#" class="hover:text-amber-600 transition-colors">İndirim Kuponlarım</a>
            <a href="#" class="hover:text-amber-600 transition-colors">Trendyol'da Satış Yap</a>
            <a href="#" class="hover:text-amber-600 transition-colors">Yardım & Destek</a>
        </div>
    </div>

    <!-- Header / Navbar -->
    <header class="py-4 shadow-sm">
        <div class="ty-container">
            <div class="flex items-center gap-8">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <h1 class="text-3xl font-black italic tracking-tighter text-slate-900">
                        TREND<span class="text-[var(--primary-color)]">YOL</span>
                    </h1>
                </a>

                <!-- Search -->
                <div class="flex-grow max-w-2xl relative group">
                    <form action="{{ route('home') }}" method="GET">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Aradığınız ürün, kategori veya markayı yazınız" class="search-bar">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--primary-color)] font-bold">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- User Actions -->
                <div class="flex items-center gap-6 text-sm font-bold text-gray-700">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin') }}" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                                <i class="far fa-user text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                <span class="hidden lg:inline">Panelim</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                                <i class="far fa-user text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                <span class="hidden lg:inline">Giriş Yap</span>
                            </a>
                        @endauth
                    @else
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                            <i class="fas fa-cog text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                            <span class="hidden lg:inline">Yönetim Paneli</span>
                        </a>
                    @endif
                    <a href="#" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                        <i class="far fa-heart text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                        <span class="hidden lg:inline">Favorilerim</span>
                    </a>
                    <a href="#" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                        <div class="relative">
                            <i class="fas fa-shopping-cart text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                            <span class="absolute -top-2 -right-2 bg-[var(--primary-color)] text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white">0</span>
                        </div>
                        <span class="hidden lg:inline">Sepetim</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

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

    <!-- Main Content -->
    <main class="ty-container py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Sidebar Filters (Hidden on Mobile) -->
            <aside class="w-full lg:w-64 flex-shrink-0 hidden lg:block">
                <h3 class="text-lg font-bold mb-4">Filtreler</h3>
                
                <!-- Category Filter -->
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

                <!-- Brand Filter -->
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

                <!-- Price range filter -->
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
                <!-- Sorting & Info Bar -->
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
                            <div class="product-image-container">
                                @php $img = $product->productImages->first()?->url ?? 'https://via.placeholder.com/400x600?text=Resim+Yok'; @endphp
                                <a href="{{ route('product.show', $product->id) }}">
                                    <img src="{{ $img }}" alt="{{ $product->name }}" class="product-image">
                                </a>
                                <button class="favorite-btn">
                                    <i class="far fa-heart"></i>
                                </button>
                                
                                <div class="absolute bottom-2 left-2 flex flex-col gap-1">
                                    @if($product->stock > 0)
                                        <div class="badge-free-shipping">HIZLI TESLİMAT</div>
                                    @endif
                                    <div class="bg-white text-[9px] font-bold px-2 py-0.5 rounded shadow-sm border border-gray-100 w-fit">KARGO BEDAVA</div>
                                </div>
                            </div>
                            
                            <div class="product-info">
                                <div class="brand-name">{{ $product->brand->name ?? 'Markasız' }}</div>
                                <a href="{{ route('product.show', $product->id) }}">
                                    <h3 class="product-name">{{ $product->name }}</h3>
                                </a>
                                
                                <!-- Ratings -->
                                <div class="flex items-center gap-1 mt-1">
                                    <div class="flex text-[10px] text-amber-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star text-slate-200"></i>
                                        <i class="fas fa-star text-slate-200"></i>
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-bold">(152)</span>
                                </div>

                                <div class="price-container">
                                    <div class="product-price">{{ number_format($product->price, 2) }} TL</div>
                                </div>
                                
                                <button class="w-full mt-3 py-2 bg-slate-900 text-white text-[11px] font-black rounded hover:bg-slate-800 transition-colors uppercase tracking-widest">
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

                <!-- Pagination -->
                <div class="mt-12 flex justify-center">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-16 mt-20">
        <div class="ty-container grid grid-cols-1 md:grid-cols-4 gap-12">
            <div>
                <h4 class="text-lg font-bold mb-6">Trendyol</h4>
                <ul class="space-y-3 text-sm text-gray-400 font-medium">
                    <li><a href="#" class="hover:text-white transition-colors">Hakkımızda</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Kariyer</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">İletişim</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Sürdürülebilirlik</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-6">Kampanyalar</h4>
                <ul class="space-y-3 text-sm text-gray-400 font-medium">
                    <li><a href="#" class="hover:text-white transition-colors">Aktif Kampanyalar</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Elite Üyelik</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Hediye Fikirleri</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Trendyol Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-6">Yardım</h4>
                <ul class="space-y-3 text-sm text-gray-400 font-medium">
                    <li><a href="#" class="hover:text-white transition-colors">Sıkça Sorulan Sorular</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">İade Politikası</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Ödeme Seçenekleri</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Kullanım Koşulları</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-bold mb-6">Güvenli Alışveriş</h4>
                <div class="flex flex-wrap gap-4 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all cursor-pointer">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" class="h-6" alt="Paypal">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-8" alt="Mastercard">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" class="h-4" alt="Visa">
                </div>
                <div class="mt-8">
                    <h5 class="text-sm font-bold mb-4">Bizi Takip Edin</h5>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[var(--primary-color)] transition-colors"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[var(--primary-color)] transition-colors"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-[var(--primary-color)] transition-colors"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="ty-container border-t border-slate-800 mt-16 pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-widest">
            <p>&copy; 2026 {{ config('app.name') }} | Tüm Hakları Saklıdır.</p>
            <p>Developer by Antigravity AI Engine</p>
        </div>
    </footer>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
    </style>

</body>
</html>
