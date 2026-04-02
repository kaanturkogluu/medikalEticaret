<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - {{ config('app.name') }}</title>
    
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
            background-color: #ffffff;
            color: var(--text-main);
        }

        .ty-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

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

        .breadcrumb {
            display: flex;
            gap: 10px;
            font-size: 12px;
            color: #999;
            padding: 20px 0;
        }

        .breadcrumb a {
            color: #666;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
            color: var(--primary-color);
        }

        /* Gallery */
        .gallery-main {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
        }

        .thumbnail {
            width: 60px;
            height: 80px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            cursor: pointer;
            object-fit: cover;
            transition: border-color 0.2s;
        }

        .thumbnail.active {
            border-color: var(--primary-color);
            border-width: 2px;
        }

        /* Product Details */
        .product-brand {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 4px;
        }

        .product-title {
            font-size: 20px;
            color: #444;
            font-weight: 400;
            line-height: 1.3;
        }

        .rating-stars {
            color: #ffc000;
            font-size: 14px;
        }

        .price-box {
            background-color: #fafafa;
            border: 1px solid #f2f2f2;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .current-price {
            font-size: 32px;
            font-weight: 900;
            color: var(--primary-color);
        }

        .add-to-basket {
            background-color: var(--primary-color);
            color: white;
            width: 100%;
            padding: 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            transition: background 0.2s;
        }

        .add-to-basket:hover {
            background-color: var(--primary-hover);
        }

        /* Tabs */
        .tab-btn {
            padding: 15px 30px;
            font-weight: 700;
            font-size: 14px;
            border-bottom: 3px solid transparent;
            cursor: pointer;
        }

        .tab-btn.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* Related Products */
        .related-card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px;
            transition: box-shadow 0.2s;
        }

        .related-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body x-data="{ activeImage: '{{ $product->productImages->first()?->url ?? 'https://via.placeholder.com/600x900' }}' }">

    <!-- Header (Same as Home) -->
    <header class="py-4 shadow-sm">
        <div class="ty-container">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <h1 class="text-3xl font-black italic tracking-tighter text-slate-900">
                        TREND<span class="text-[var(--primary-color)]">YOL</span>
                    </h1>
                </a>
                <div class="flex-grow max-w-2xl relative group">
                    <form action="{{ route('home') }}" method="GET">
                        <input type="text" name="q" placeholder="Aradığınız ürün, kategori veya markayı yazınız" class="search-bar">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--primary-color)] font-bold">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="flex items-center gap-6 text-sm font-bold text-gray-700">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 hover:text-[var(--primary-color)]">
                        <i class="fas fa-cog text-lg text-gray-400"></i>
                    </a>
                    <a href="#" class="flex items-center gap-2 hover:text-[var(--primary-color)]">
                        <i class="fas fa-shopping-cart text-lg text-gray-400"></i>
                        <span class="bg-[var(--primary-color)] text-white text-[9px] px-1.5 py-0.5 rounded-full">0</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="ty-container pb-20">
        <!-- Breadcrumbs -->
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Ana Sayfa</a>
            <i class="fas fa-chevron-right text-[8px] self-center"></i>
            <a href="{{ route('home', ['category' => $product->category_id]) }}">{{ $product->category->name ?? 'Kategori' }}</a>
            <i class="fas fa-chevron-right text-[8px] self-center"></i>
            <span class="text-gray-400">{{ str($product->name)->limit(40) }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-12 mt-4">
            
            <!-- Left: Images -->
            <div class="w-full lg:w-1/2 flex gap-4">
                <div class="flex flex-col gap-2 shrink-0">
                    @foreach($product->productImages as $image)
                        <img src="{{ $image->url }}" 
                             @click="activeImage = '{{ $image->url }}'"
                             :class="activeImage == '{{ $image->url }}' ? 'active' : ''"
                             class="thumbnail" alt="thumbnail">
                    @endforeach
                </div>
                <div class="flex-grow gallery-main aspect-[2/3]">
                    <img :src="activeImage" class="w-full h-full object-contain p-4" alt="{{ $product->name }}">
                </div>
            </div>

            <!-- Right: Info -->
            <div class="w-full lg:w-1/2">
                <div class="mb-4">
                    <h2 class="product-brand uppercase">{{ $product->brand->name ?? 'Markasız' }}</h2>
                    <h1 class="product-title">{{ $product->name }}</h1>
                </div>

                <!-- Ratings -->
                <div class="flex items-center gap-4 py-2 border-b border-gray-100">
                    <div class="flex items-center gap-1">
                        <span class="font-bold text-sm">4.8</span>
                        <div class="rating-stars flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <span class="text-gray-400 text-sm">|</span>
                    <span class="text-[var(--accent-blue)] text-sm font-bold cursor-pointer">152 Değerlendirme</span>
                    <span class="text-gray-400 text-sm">|</span>
                    <span class="text-[var(--accent-blue)] text-sm font-bold cursor-pointer">43 Soru & Cevap</span>
                </div>

                <!-- Price Section -->
                <div class="price-box">
                    <div class="text-xs text-gray-500 line-through">{{ number_format($product->price * 1.2, 2) }} TL</div>
                    <div class="flex items-end gap-2">
                        <span class="current-price">{{ number_format($product->price, 2) }} TL</span>
                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded mb-2">-%20</span>
                    </div>
                    <div class="text-[11px] text-green-600 font-bold mt-2">
                        <i class="fas fa-ticket text-xs mr-1"></i> Sepette %10 İndirim (250 TL ve üzeri)
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4 mb-8">
                    <button class="add-to-basket flex-grow flex items-center justify-center gap-3">
                        <span>Sepete Ekle</span>
                    </button>
                    <button class="w-14 h-14 border border-gray-200 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors">
                        <i class="far fa-heart text-xl"></i>
                    </button>
                </div>

                <!-- Merchant -->
                <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <div class="text-sm font-bold">UMMET MEDİKAL</div>
                            <div class="text-xs text-green-600 font-bold">9.8 Satıcı Puanı</div>
                        </div>
                    </div>
                    <a href="#" class="text-xs font-bold text-[var(--accent-blue)]">Mağazayı Gör</a>
                </div>

                <!-- Highlights -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-gray-700">Öne Çıkan Özellikler:</h3>
                    <ul class="grid grid-cols-2 gap-y-2">
                        @foreach($product->productAttributes->take(6) as $attr)
                            <li class="text-xs text-gray-600 flex items-center gap-2">
                                <span class="w-1.5 h-1.5 bg-gray-300 rounded-full"></span>
                                <span class="font-bold">{{ $attr->name }}:</span> {{ $attr->value }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Description & Details Tabs -->
        <div class="mt-20 border-t border-gray-200" x-data="{ tab: 'description' }">
            <div class="flex border-b border-gray-100 justify-center">
                <button @click="tab = 'description'" :class="tab == 'description' ? 'active' : ''" class="tab-btn">Ürün Açıklaması</button>
                <button @click="tab = 'features'" :class="tab == 'features' ? 'active' : ''" class="tab-btn">Ürün Özellikleri</button>
                <button @click="tab = 'comments'" :class="tab == 'comments' ? 'active' : ''" class="tab-btn">Yorumlar (152)</button>
            </div>
            
            <div class="py-10 max-w-4xl mx-auto">
                <div x-show="tab == 'description'" class="text-gray-600 leading-relaxed space-y-4">
                    {!! nl2br(e($product->description)) !!}
                </div>
                
                <div x-show="tab == 'features'" x-cloak>
                    <table class="w-full border-collapse">
                        @foreach($product->productAttributes as $attr)
                            <tr class="border-b border-gray-50">
                                <td class="py-3 text-sm font-bold text-gray-500 w-1/3">{{ $attr->name }}</td>
                                <td class="py-3 text-sm text-gray-800">{{ $attr->value }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <div x-show="tab == 'comments'" x-cloak class="text-center py-10">
                    <p class="text-gray-400 italic">Henüz yorum yapılmamış.</p>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-20">
            <h3 class="text-xl font-black mb-8 italic">Benzer Ürünler</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                @foreach($relatedProducts as $rp)
                    <a href="{{ route('product.show', $rp->id) }}" class="related-card group">
                        <div class="aspect-[2/3] bg-gray-50 rounded-lg overflow-hidden mb-3">
                            <img src="{{ $rp->productImages->first()?->url ?? 'https://via.placeholder.com/400x600' }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        </div>
                        <div class="text-xs font-bold mb-1">{{ $rp->brand->name ?? 'Markasız' }}</div>
                        <div class="text-[11px] text-gray-500 h-8 overflow-hidden">{{ $rp->name }}</div>
                        <div class="text-sm font-black text-[var(--primary-color)] mt-2">{{ number_format($rp->price, 2) }} TL</div>
                    </a>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Footer (Same as Home) -->
    <footer class="bg-slate-900 text-white py-16 mt-20">
        <div class="ty-container text-center text-xs text-slate-500 font-bold uppercase tracking-widest">
            <p>&copy; 2026 {{ config('app.name') }} | Trendyol Entegrasyon Sistemi</p>
        </div>
    </footer>

</body>
</html>
