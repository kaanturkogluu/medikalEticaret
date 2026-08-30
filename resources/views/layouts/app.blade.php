<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteTitle = \App\Models\Setting::getValue('site_title', 'umutMed Market');
        $siteFavicon = \App\Models\Setting::getValue('site_favicon', '/favicon.svg');
        $faviconUrl = $siteFavicon;
        
        // Dynamic Favicon Type
        $faviconType = 'image/x-icon';
        if (str_ends_with($siteFavicon, '.svg')) {
            $faviconType = 'image/svg+xml';
        } elseif (str_ends_with($siteFavicon, '.png')) {
            $faviconType = 'image/png';
        }

        // Cache busting using filemtime if local
        if (file_exists(public_path($siteFavicon))) {
            $faviconUrl .= '?v=' . filemtime(public_path($siteFavicon));
        }
        
        $primaryColor = \App\Models\Setting::getValue('site_primary_color', '#059669');
        $footerQr = \App\Models\Setting::getValue('site_footer_qr', '');
        $defaultFooter = [
            ["title" => "umutMed", "links" => [["text" => "Hakkımızda", "url" => "#"], ["text" => "Kariyer", "url" => "#"], ["text" => "İletişim", "url" => "/iletisim"], ["text" => "Sürdürülebilirlik", "url" => "#"]]],
            ["title" => "Kampanyalar", "links" => [["text" => "Aktif Kampanyalar", "url" => "#"], ["text" => "Elite Üyelik", "url" => "#"], ["text" => "Hediye Fikirleri", "url" => "#"], ["text" => "umutMed Blog", "url" => "#"]]],
            ["title" => "Yardım", "links" => [["text" => "Sıkça Sorulan Sorular", "url" => route('sss')], ["text" => "İade Politikası", "url" => route('page.show', 'iade-iptal-politikasi')], ["text" => "Mesafeli Satış Sözleşmesi", "url" => route('page.show', 'mesafeli-satis-sozlesmesi')], ["text" => "Ödeme Seçenekleri", "url" => route('page.show', 'odeme-politikasi')], ["text" => "Kullanım Koşulları", "url" => route('page.show', 'kullanim-kosullari')]]]
        ];
        $footerCols = json_decode(\App\Models\Setting::getValue('site_footer_columns', json_encode($defaultFooter)), true);
    @endphp

    <title>@yield('title', config('app.name')) - {{ $siteTitle }}</title>

    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --primary-color:
                {{ $primaryColor }}
            ;
            --primary-hover:
                {{ $primaryColor }}
                ee;
            --background-color: #f5f5f5;
            --card-bg: #ffffff;
            --text-main: #333333;
            --text-muted: #666666;
            --border-color: #e6e6e6;
            --price-color:
                {{ $primaryColor }}
            ;
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

        [x-cloak] {
            display: none !important;
        }

        /* Header */
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
            display: inline-block;
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

        /* Scrollbar */
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

        /* Marquee Animation */
        .marquee-wrapper {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
        }

        .marquee-content {
            display: inline-block;
            animation: marquee 25s linear infinite;
            padding-left: 100%;
        }

        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }
    </style>
    @yield('styles')

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-9R61M1L3PK"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-9R61M1L3PK');
    </script>
</head>

<body x-data>

    @php
        $defaultMarketplaces = [
            ['name' => 'TRENDYOL', 'url' => 'https://trendyol.com', 'logo' => 'https://www.google.com/s2/favicons?domain=trendyol.com&sz=128', 'color' => '#059669'],
            ['name' => 'N11', 'url' => 'https://n11.com', 'logo' => 'https://www.google.com/s2/favicons?domain=n11.com&sz=128', 'color' => '#e11e24'],
            ['name' => 'HEPSİBURADA', 'url' => 'https://hepsiburada.com', 'logo' => 'https://www.google.com/s2/favicons?domain=hepsiburada.com&sz=128', 'color' => '#ff6000'],
            ['name' => 'AMAZON', 'url' => 'https://amazon.com.tr', 'logo' => 'https://www.google.com/s2/favicons?domain=amazon.com.tr&sz=128', 'color' => '#000000'],
        ];
        $marketplaces = json_decode(\App\Models\Setting::getValue('marketplaces', json_encode($defaultMarketplaces)), true);
        $marqueeText = \App\Models\Setting::getValue('marquee_text', "Açılışa Özel Tüm Ürünlerde %20'ye Varan İndirimler! • Saat 16:00'a Kadar Verilen Siparişlerde Aynı Gün Kargo! • Ücretsiz Kargo Fırsatını Kaçırmayın!");
    @endphp

    <!-- Top Info Bar -->
    <div class="bg-gray-100 hidden md:block border-b border-gray-200">
        <div class="ty-container h-8 flex items-center justify-between text-[11px] text-gray-500 font-medium">
            <!-- Left Side: Marketplace Icons -->
            <div class="flex items-center gap-8">
                <span
                    class="text-[9px] uppercase font-black text-gray-400 whitespace-nowrap leading-none border-r border-gray-200 pr-4 py-1.5">Bizi
                    Takip Edin</span>
                <div class="flex items-center gap-6">
                    @foreach($marketplaces as $mp)
                        <a href="{{ $mp['url'] }}" target="_blank" title="{{ $mp['name'] }}"
                            class="hover:opacity-75 transition-opacity flex items-center gap-2">
                            @if($mp['logo'])
                                <img src="{{ $mp['logo'] }}" class="h-4 w-4 rounded-sm shadow-sm" alt="{{ $mp['name'] }}">
                            @endif
                            <span style="color: {{ $mp['color'] }};"
                                class="font-black text-[10px] tracking-tight uppercase">{{ $mp['name'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Middle: Scrolling Text -->
            <div class="flex-grow mx-20 marquee-wrapper">
                <div class="marquee-content text-[var(--primary-color)] font-black text-[12px] tracking-wide uppercase">
                    {{ $marqueeText }}
                </div>
            </div>

            <!-- Right Side: Links -->
            <div class="flex items-center gap-6 shrink-0">
                <a href="{{ route('quote.track') }}"
                    class="hover:text-amber-300 text-amber-300 transition-colors uppercase font-black italic tracking-tighter text-[12px] flex items-center gap-1.5">
                    <i class="fas fa-search-dollar text-xs"></i>
                    <span>Teklif Sorgula</span>
                </a>
                <a href="{{ route('contact') }}"
                    class="hover:text-emerald-400 transition-colors uppercase font-black italic tracking-tighter text-[12px]">İletişim
                    & Konum</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="shadow-sm sticky top-0 z-[1000]" x-data="{ mobileCatOpen: false }">
        <div class="py-4 bg-white border-b border-gray-100">
            <div class="ty-container">
                <!-- Desktop Header (Hidden on Mobile) -->
                <div class="hidden md:flex items-center gap-8">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex-shrink-0">
                        <h1 class="text-3xl font-black italic tracking-tighter text-slate-900">
                            umut<span class="text-[var(--primary-color)]">Med</span>
                        </h1>
                    </a>

                    <!-- Search -->
                    <div class="flex-grow max-w-2xl relative group">
                        <form action="{{ route('home') }}" method="GET">
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Aradığınız ürün, kategori veya markayı yazınız" class="search-bar">
                            <button type="submit"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-[var(--primary-color)] font-bold">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- User Actions -->
                    <div class="flex items-center gap-6 text-sm font-bold text-gray-700">
                        @if (Route::has('login'))
                            @auth
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                                        <i class="fas fa-cog text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                        <span class="hidden lg:inline uppercase tracking-tighter italic">Yönetim</span>
                                    </a>
                                @else
                                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                        <a href="javascript:void(0)"
                                            class="flex items-center gap-2 hover:text-[var(--primary-color)] transition-colors py-4">
                                            <i class="far fa-user text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                            <span class="hidden lg:inline uppercase tracking-tighter italic">Hesabım</span>
                                            <i class="fas fa-chevron-down text-[8px] opacity-30 group-hover:rotate-180 transition-transform"></i>
                                        </a>
                                        
                                        <!-- Dropdown Menu -->
                                        <div x-show="open" 
                                             x-transition:enter="transition ease-out duration-200"
                                             x-transition:enter-start="opacity-0 translate-y-4"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             x-cloak 
                                             class="absolute top-full right-0 w-60 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.15)] rounded-2xl py-3 transform z-[100] border border-slate-50">
                                            
                                            <!-- Invisible Bridge -->
                                            <div class="absolute w-full h-6 -top-6 bg-transparent"></div>
                                            
                                            <div class="px-4 py-2 mb-2 border-b border-slate-50">
                                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest italic">Hoş Geldiniz</p>
                                                <p class="text-xs font-black text-slate-900 truncate uppercase mt-0.5">{{ auth()->user()->name }}</p>
                                            </div>

                                            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-[11px] font-black uppercase italic tracking-tighter transition-colors group/item">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover/item:bg-white transition-colors">
                                                    <i class="fas fa-th-large text-gray-400 group-hover/item:text-slate-900"></i>
                                                </div>
                                                Hesap Özeti
                                            </a>
                                            <a href="{{ route('user.orders') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-[11px] font-black uppercase italic tracking-tighter transition-colors group/item">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover/item:bg-white transition-colors">
                                                    <i class="fas fa-box text-gray-400 group-hover/item:text-slate-900"></i>
                                                </div>
                                                Siparişlerim
                                            </a>
                                            <a href="{{ route('user.quotes') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-[11px] font-black uppercase italic tracking-tighter transition-colors group/item">
                                                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center group-hover/item:bg-white transition-colors">
                                                    <i class="fas fa-file-invoice-dollar text-amber-500 group-hover/item:text-amber-600"></i>
                                                </div>
                                                Teklif Taleplerim
                                            </a>
                                            <a href="{{ route('user.addresses') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-[11px] font-black uppercase italic tracking-tighter transition-colors group/item">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover/item:bg-white transition-colors">
                                                    <i class="fas fa-map-marker-alt text-gray-400 group-hover/item:text-slate-900"></i>
                                                </div>
                                                Adreslerim
                                            </a>
                                            <a href="{{ route('user.comments') }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 text-[11px] font-black uppercase italic tracking-tighter transition-colors group/item">
                                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover/item:bg-white transition-colors">
                                                    <i class="fas fa-comment text-gray-400 group-hover/item:text-slate-900"></i>
                                                </div>
                                                Yorumlarım
                                            </a>
                                            
                                            <div class="my-2 border-t border-slate-50"></div>
                                            
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-red-50 text-[11px] font-black uppercase italic tracking-tighter text-red-500 transition-colors group/item">
                                                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover/item:bg-white transition-colors text-red-400">
                                                        <i class="fas fa-sign-out-alt"></i>
                                                    </div>
                                                    Güvenli Çıkış
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                                    <a href="javascript:void(0)" class="flex items-center gap-2 hover:text-[var(--primary-color)] transition-colors py-4">
                                        <i class="far fa-user text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                        <span class="hidden lg:inline uppercase tracking-tighter italic">Giriş Yap</span>
                                    </a>

                                    <!-- Guest Dropdown -->
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 translate-y-4"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         x-cloak 
                                         class="absolute top-full right-0 w-48 bg-white shadow-[0_20px_50px_rgba(0,0,0,0.15)] rounded-2xl p-4 transform z-[100] border border-slate-50">
                                        
                                        <div class="absolute w-full h-6 -top-6 bg-transparent"></div>

                                        <a href="{{ route('login') }}" class="block w-full text-center py-2.5 bg-emerald-600 text-white rounded-xl text-[11px] font-black uppercase italic tracking-tighter hover:bg-emerald-700 transition-all mb-2 shadow-sm">Giriş Yap</a>
                                        <a href="{{ route('register') }}" class="block w-full text-center py-2.5 bg-slate-50 text-slate-900 rounded-xl text-[11px] font-black uppercase italic tracking-tighter border border-slate-100 hover:bg-slate-100 transition-all">Üye Ol</a>
                                    </div>
                                </div>
                            @endauth
                        @else
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                                <i class="fas fa-cog text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                <span class="hidden lg:inline">Yönetim Paneli</span>
                            </a>
                        @endif

                        <a href="{{ route('favorites') }}"
                            class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                            <div class="relative">
                                <i class="far fa-heart text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                <span x-show="$store.fav.items.length" x-text="$store.fav.items.length"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white"></span>
                            </div>
                            <span class="hidden lg:inline">Favorilerim</span>
                        </a>

                        <!-- Teklif Sepeti Trigger (Desktop) -->
                        <a href="{{ route('quote.cart') }}" @click.prevent="$store.quote.open = true"
                            class="flex items-center gap-2 hover:text-amber-600 transition-colors group"
                            title="Toplu & Bağış Alımları İçin Teklif Sepeti">
                            <div class="relative">
                                <i class="fas fa-file-invoice-dollar text-lg text-gray-400 group-hover:text-amber-500 transition-colors"></i>
                                <span x-show="$store.quote.items.length" x-text="$store.quote.items.length"
                                    class="absolute -top-2 -right-2 bg-amber-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white font-black"></span>
                            </div>
                            <span class="hidden lg:inline text-amber-700 font-black">Teklif Sepeti</span>
                        </a>

                        <a href="#" @click.prevent="$store.cart.open = true"
                            class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                            <div class="relative">
                                <i
                                    class="fas fa-shopping-cart text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                <span x-show="$store.cart.items.length" x-text="$store.cart.items.length"
                                    class="absolute -top-2 -right-2 bg-[var(--primary-color)] text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white"></span>
                            </div>
                            <span class="hidden lg:inline">Sepetim</span>
                        </a>
                    </div>
                </div>
                
                <!-- Mobile Header (Visible only on Mobile) -->
                <div class="flex md:hidden flex-col gap-4">
                    <!-- Top Row: Logo, Search, Account -->
                    <div class="flex items-center justify-between gap-3">
                        <!-- Logo -->
                        <a href="{{ route('home') }}" class="flex-shrink-0">
                            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 leading-none">
                                umut<span class="text-[var(--primary-color)]">Med</span>
                            </h1>
                        </a>
                        
                        <!-- Search -->
                        <div class="flex-grow relative">
                            <form action="{{ route('home') }}" method="GET">
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="Ara..." class="w-full bg-gray-50 border border-gray-200 rounded-full py-2 pl-4 pr-10 text-xs focus:outline-none focus:border-[var(--primary-color)]">
                                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--primary-color)]">
                                    <i class="fas fa-search text-sm"></i>
                                </button>
                            </form>
                        </div>
                        
                        <!-- Account -->
                        <div class="flex-shrink-0">
                            @if (Route::has('login'))
                                @auth
                                    @if(auth()->user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="text-xl text-gray-400">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    @else
                                        <div class="relative" x-data="{ openMob: false }" @click.outside="openMob = false">
                                            <button @click="openMob = !openMob" class="text-xl text-gray-400">
                                                <i class="far fa-user"></i>
                                            </button>
                                            
                                            <!-- Mobile User Dropdown -->
                                            <div x-show="openMob" x-cloak class="absolute top-full right-0 mt-2 w-48 bg-white shadow-xl rounded-xl py-2 z-[1001] border border-gray-100">
                                                <div class="px-4 py-2 mb-2 border-b border-gray-50">
                                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest italic">Hoş Geldiniz</p>
                                                    <p class="text-xs font-black text-slate-900 truncate uppercase mt-0.5">{{ auth()->user()->name }}</p>
                                                </div>
                                                <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Hesabım</a>
                                                <a href="{{ route('user.orders') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Siparişlerim</a>
                                                <a href="{{ route('user.quotes') }}" class="block px-4 py-2 text-xs font-bold text-amber-700 hover:bg-amber-50">Teklif Taleplerim</a>
                                                <a href="{{ route('quote.track') }}" class="block px-4 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-50">Teklif Sorgula</a>
                                                <a href="{{ route('user.addresses') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Adreslerim</a>
                                                <form action="{{ route('logout') }}" method="POST" class="mt-2 pt-2 border-t border-gray-50">
                                                    @csrf
                                                    <button type="submit" class="block w-full text-left px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50">Çıkış Yap</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="relative" x-data="{ openMob: false }" @click.outside="openMob = false">
                                        <button @click="openMob = !openMob" class="text-xl text-gray-400">
                                            <i class="far fa-user"></i>
                                        </button>
                                        <!-- Guest Dropdown -->
                                        <div x-show="openMob" x-cloak class="absolute top-full right-0 mt-2 w-40 bg-white shadow-xl rounded-xl py-2 z-[1001] border border-gray-100">
                                            <a href="{{ route('login') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Giriş Yap</a>
                                            <a href="{{ route('register') }}" class="block px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Üye Ol</a>
                                        </div>
                                    </div>
                                @endauth
                            @endif
                        </div>
                    </div>
                    
                    <!-- Bottom Row: Categories Hamburger, Fav, Quote, Cart -->
                    <div class="flex items-center justify-between mt-1">
                        <!-- Categories Hamburger Trigger -->
                        <button @click="mobileCatOpen = true" class="flex items-center gap-2 text-[var(--primary-color)] font-black text-[13px] tracking-tight">
                            <i class="fas fa-bars text-lg"></i> KATEGORİLER
                        </button>
                        
                        <!-- Favorites, Quote & Cart -->
                        <div class="flex items-center gap-4">
                            <a href="{{ route('favorites') }}" class="relative text-xl text-gray-400">
                                <i class="far fa-heart"></i>
                                <span x-show="$store.fav.items.length" x-text="$store.fav.items.length" class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border border-white"></span>
                            </a>
                            <button @click="$store.quote.open = true" class="relative text-xl text-amber-500" title="Teklif Sepeti">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span x-show="$store.quote.items.length" x-text="$store.quote.items.length" class="absolute -top-1.5 -right-2 bg-amber-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border border-white font-bold"></span>
                            </button>
                            <button @click="$store.cart.open = true" class="relative text-xl text-gray-400">
                                <i class="fas fa-shopping-cart"></i>
                                <span x-show="$store.cart.items.length" x-text="$store.cart.items.length" class="absolute -top-1.5 -right-2 bg-[var(--primary-color)] text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border border-white"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Navbar Categories (Desktop Only) -->
        <nav class="category-nav hidden md:block bg-white shadow-sm border-b border-gray-200">
            <div class="ty-container flex items-center justify-center w-full relative" x-data="{ openAll: false }">
                @if($categories->count() > 10)
                    <div class="category-link cursor-pointer group" @mouseenter="openAll = true" @mouseleave="openAll = false">
                        <span class="flex items-center gap-2 font-black italic">TÜM KATEGORİLER <i class="fas fa-chevron-down text-[10px]"></i></span>
                        <div x-show="openAll" x-cloak class="absolute top-full left-0 bg-white border border-gray-100 shadow-2xl p-0 w-[1000px] z-[1001] rounded-b-xl overflow-hidden" x-data="{ allCatSearch: '' }">
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
                                    @php $searchText = strtolower(($cat->parent ? $cat->parent->name . ' ' : '') . $cat->name); @endphp
                                    <a href="{{ route('home', ['category' => $cat->slug ?? $cat->id]) }}" 
                                       x-show="allCatSearch === '' || '{{ $searchText }}'.includes(allCatSearch.toLowerCase())"
                                       class="py-3 text-[13px] hover:text-[var(--primary-color)] hover:translate-x-1 transition-all font-medium border-b border-gray-50 last:border-0 flex items-center justify-between group/item">
                                        <span class="flex flex-col leading-tight">
                                            @if($cat->parent)
                                                <span class="text-[10px] text-gray-400 font-normal">{{ $cat->parent->name }}</span>
                                            @endif
                                            <span>{{ $cat->name }}</span>
                                        </span>
                                        <i class="fas fa-chevron-right text-[10px] opacity-0 group-hover/item:opacity-100 transition-opacity"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @php 
                    $displayCats = $navbarCategories->count() > 0 ? $navbarCategories : $categories->take(9);
                @endphp

                @foreach($displayCats as $cat)
                    @php 
                        $isActive = (request('category') == $cat->id || request('category') == $cat->slug); 
                        $categoryBrands = \App\Models\Brand::where('active', true)->whereHas('products', function($q) use ($cat) {
                            $q->where('category_id', $cat->id);
                        })->take(12)->get();
                    @endphp
                    <div class="relative group/brandmenu h-full flex items-center">
                        <a href="{{ route('home', ['category' => $cat->slug]) }}" class="category-link {{ $isActive ? 'text-[var(--primary-color)] border-b-2 border-[var(--primary-color)]' : '' }}">
                            {{ str($cat->name)->upper() }}
                        </a>
                        
                        @if($categoryBrands->count() > 0)
                            <!-- Hover Brands Menu -->
                            <div class="absolute top-full left-1/2 -translate-x-1/2 w-[450px] bg-white border border-gray-100 shadow-2xl rounded-2xl z-[1000] p-6 transition-all duration-300 transform opacity-0 invisible group-hover/brandmenu:opacity-100 group-hover/brandmenu:visible translate-y-4 group-hover/brandmenu:translate-y-1 text-center cursor-default">
                                <h4 class="text-[11px] font-black uppercase text-slate-800 italic tracking-widest mb-4 border-b border-gray-100 pb-3">{{ $cat->name }} KATEGORİSİNDEKİ MARKALAR</h4>
                                <div class="grid grid-cols-4 gap-4">
                                    @foreach($categoryBrands as $b)
                                        <a href="{{ route('home', ['category' => $cat->slug, 'brand' => $b->slug]) }}" class="flex flex-col items-center gap-2 text-xs font-bold text-slate-600 hover:text-[var(--primary-color)] transition-all group/brandlink">
                                            <div class="h-14 w-14 rounded-2xl bg-white border border-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0 group-hover/brandlink:shadow-lg group-hover/brandlink:border-[var(--primary-color)] transition-all p-2 relative">
                                                <div class="absolute inset-0 bg-[var(--primary-color)] opacity-0 group-hover/brandlink:opacity-5 transition-opacity"></div>
                                                @if($b->logo)
                                                    <img src="{{ asset('storage/' . $b->logo) }}" class="h-full w-full object-contain filter grayscale group-hover/brandlink:grayscale-0 group-hover/brandlink:scale-110 transition-all duration-500">
                                                @else
                                                    <span class="text-[12px] text-slate-300 font-black uppercase inline-block group-hover/brandlink:scale-110 group-hover/brandlink:text-[var(--primary-color)] transition-all">{{ substr($b->name, 0, 2) }}</span>
                                                @endif
                                            </div>
                                            <span class="text-[9px] font-black uppercase italic tracking-tighter truncate w-full text-center group-hover/brandlink:text-slate-900">{{ $b->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="mt-5 pt-4 border-t border-gray-50">
                                    <a href="{{ route('home', ['category' => $cat->slug]) }}" class="inline-flex items-center justify-center gap-2 text-[10px] font-black uppercase text-[var(--primary-color)] bg-[var(--primary-color)]/10 hover:bg-[var(--primary-color)] hover:text-white px-6 py-2.5 rounded-xl transition-colors w-full">Tüm {{ $cat->name }} Ürünlerini Gör <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="absolute top-full left-1/2 -translate-x-1/2 w-[350px] bg-white border border-gray-100 shadow-2xl rounded-2xl z-[1000] p-6 transition-all duration-300 transform opacity-0 invisible group-hover/brandmenu:opacity-100 group-hover/brandmenu:visible translate-y-4 group-hover/brandmenu:translate-y-1 text-center cursor-default">
                                <div class="py-4 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-box-open text-xl opacity-50"></i>
                                    </div>
                                    <p class="text-[10px] font-black uppercase tracking-widest italic text-center">Bu kategoride henüz markalı ürün bulunmuyor.</p>
                                    <a href="{{ route('home', ['category' => $cat->slug]) }}" class="mt-4 block text-[10px] font-black uppercase text-[var(--primary-color)] hover:underline">Yine de Ürünlere Göz At</a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </nav>

    <!-- Mobile Categories Offcanvas Drawer -->
    <div x-show="mobileCatOpen" x-cloak class="md:hidden fixed inset-0 z-[2000] flex">
        <!-- Backdrop -->
        <div x-show="mobileCatOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="mobileCatOpen = false"></div>
        
        <!-- Drawer Content -->
        <div x-show="mobileCatOpen" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative w-4/5 max-w-[320px] h-full bg-white shadow-2xl flex flex-col" x-data="{ mobileCatSearch: '' }">
            
            <!-- Header -->
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/80">
                <span class="font-black italic text-slate-800 tracking-tight flex items-center gap-2">
                    <i class="fas fa-bars text-[var(--primary-color)]"></i> KATEGORİLER
                </span>
                <button @click="mobileCatOpen = false" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg shadow-sm text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Search -->
            <div class="p-4 border-b border-gray-100 bg-white">
                <div class="relative">
                    <input type="text" x-model="mobileCatSearch" placeholder="Kategorilerde ara..." class="w-full bg-gray-50 border border-gray-200 rounded-xl py-3 pl-11 pr-4 text-sm focus:outline-none focus:border-[var(--primary-color)] focus:bg-white transition-colors">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            
            <!-- Category List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar p-2 bg-slate-50/50">
                @foreach($categories as $cat)
                    @php $searchText = strtolower(($cat->parent ? $cat->parent->name . ' ' : '') . $cat->name); @endphp
                    <a href="{{ route('home', ['category' => $cat->slug ?? $cat->id]) }}" 
                       x-show="mobileCatSearch === '' || '{{ $searchText }}'.includes(mobileCatSearch.toLowerCase())"
                       class="flex items-center justify-between px-4 py-3 mb-1 text-sm font-bold text-gray-700 bg-white border border-gray-100 rounded-xl hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-all group/mobcat">
                        <span class="flex flex-col">
                            @if($cat->parent)
                                <span class="text-[10px] text-gray-400 font-normal leading-tight">{{ $cat->parent->name }}</span>
                            @endif
                            <span class="leading-snug">{{ $cat->name }}</span>
                        </span>
                        <i class="fas fa-chevron-right text-[10px] text-gray-300 group-hover/mobcat:text-[var(--primary-color)] transition-colors"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    </header>

    @yield('sub_header')
    
    <!-- Flash Messages -->
    <div class="ty-container mt-6">
        @if(session('success'))
        <div class="mb-6 p-5 bg-green-50 border border-green-100 rounded-3xl text-sm font-bold text-green-700 flex items-center gap-4 shadow-sm italic">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                <i class="fas fa-check text-green-500"></i>
            </div>
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="mb-6 p-5 bg-red-50 border border-red-100 rounded-3xl text-sm font-bold text-red-700 flex items-center gap-4 shadow-sm italic">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                <i class="fas fa-times text-red-500"></i>
            </div>
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any() && !Request::is('login') && !Request::is('register') && !Request::is('forgot-password') && !Request::is('reset-password*'))
        <div class="mb-6 p-5 bg-red-50 border border-red-100 rounded-3xl text-sm font-bold text-red-700 shadow-sm italic">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 pt-16 pb-12 border-t border-slate-800">
        <div class="ty-container">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-8 pb-10 border-b border-slate-800">
                
                <!-- Col 1: Brand Info & Social Media (2 cols in desktop) -->
                <div class="lg:col-span-2 space-y-5">
                    <a href="{{ route('home') }}" class="inline-block">
                        <span class="text-2xl font-black text-white tracking-tight">
                            umut<span class="text-emerald-500">Med</span>
                        </span>
                    </a>
                    
                    <p class="text-xs text-slate-400 leading-relaxed max-w-md">
                        T.C. Sağlık Bakanlığı ÜTS kayıtlı medikal hijyen ve hasta bakım sarf malzemeleri tedarikçiniz. Adınıza resmi fatura ve aynı gün kargo güvencesiyle hizmetinizdeyiz.
                    </p>

                    <!-- Contact Details Snippet -->
                    <div class="space-y-2 text-xs text-slate-300">
                        @php
                            $contactPhoneVal = \App\Models\Setting::getValue('contact_phone', '0546 941 69 96');
                            $contactAddressVal = \App\Models\Setting::getValue('contact_address', 'Numune Evler, Ezgi Sk., 31600 Dörtyol/Hatay');
                            $facebook = \App\Models\Setting::getValue('social_facebook', '#');
                            $instagram = \App\Models\Setting::getValue('social_instagram', '#');
                            $twitter = \App\Models\Setting::getValue('social_twitter', '#');
                            $linkedin = \App\Models\Setting::getValue('social_linkedin', '#');
                        @endphp

                        @if($contactPhoneVal)
                            <div class="flex items-center gap-2.5">
                                <i class="fas fa-phone text-emerald-500 w-4 text-center"></i>
                                <a href="tel:{{ $contactPhoneVal }}" class="hover:text-white transition-colors font-medium">{{ $contactPhoneVal }}</a>
                            </div>
                        @endif
                        @if($contactAddressVal)
                            <div class="flex items-start gap-2.5">
                                <i class="fas fa-location-dot text-emerald-500 w-4 text-center mt-0.5"></i>
                                <span>{{ $contactAddressVal }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Social Media Links In Footer -->
                    <div class="pt-2">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3">Sosyal Medyada Bizi Takip Edin</div>
                        <div class="flex items-center gap-2.5">
                            @if($facebook && $facebook !== '#')
                                <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-[#1877F2] text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-xs" title="Facebook">
                                    <i class="fab fa-facebook-f text-sm"></i>
                                </a>
                            @endif
                            @if($instagram && $instagram !== '#')
                                <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-[#E4405F] text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-xs" title="Instagram">
                                    <i class="fab fa-instagram text-sm"></i>
                                </a>
                            @endif
                            @if($twitter && $twitter !== '#')
                                <a href="{{ $twitter }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-black text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-xs" title="X (Twitter)">
                                    <i class="fa-brands fa-x-twitter text-sm"></i>
                                </a>
                            @endif
                            @if($linkedin && $linkedin !== '#')
                                <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" 
                                   class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-[#0A66C2] text-slate-300 hover:text-white flex items-center justify-center transition-all shadow-xs" title="LinkedIn">
                                    <i class="fab fa-linkedin-in text-sm"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer Dynamic Columns (Only rendered if they contain valid links!) -->
                @foreach($footerCols as $col)
                    @php
                        $validLinks = collect($col['links'] ?? [])->filter(function($l) {
                            return !empty($l['text']) && trim($l['text']) !== '';
                        });
                    @endphp
                    @if($validLinks->isNotEmpty())
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ $col['title'] }}</h4>
                            <ul class="space-y-2.5 text-xs text-slate-400">
                                @foreach($validLinks as $link)
                                    <li>
                                        <a href="{{ $link['url'] ?? '#' }}" class="hover:text-emerald-400 transition-colors inline-block">
                                            {{ $link['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach

            </div>

            <!-- Horizontal Single-Row Güvenli Alışveriş Bar -->
            <div class="py-6 border-b border-slate-800 my-2">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-emerald-400 shrink-0">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white leading-tight">256-Bit SSL & iyzico</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Uçtan uca şifreli güvenli ödeme</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-blue-400 shrink-0">
                            <i class="fas fa-shield-check text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white leading-tight">3D Secure Koruma</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Tüm banka kartlarına taksit imkanı</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-amber-400 shrink-0">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white leading-tight">Resmi E-Fatura</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">SGK geri ödeme mevzuatına uygun</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center text-purple-400 shrink-0">
                                <i class="fas fa-certificate text-sm"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-white leading-tight">%100 Orijinal Medikal</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Sağlık Bakanlığı ÜTS kayıtlı</div>
                            </div>
                        </div>

                        @if($footerQr)
                            <a href="{{ \App\Models\Setting::getValue('etbis_url', '#') }}" target="_blank" rel="noopener noreferrer"
                               class="bg-white p-1 rounded-lg shadow-xs hover:scale-105 transition-transform flex flex-col items-center cursor-pointer shrink-0 border border-white/20" title="ETBİS Doğrulama">
                                <img src="{{ $footerQr }}" class="w-9 h-9 max-w-[36px] max-h-[36px] object-contain rounded" alt="ETBİS QR Kod">
                                <span class="text-[6px] text-slate-900 font-black uppercase tracking-tighter leading-none mt-0.5">ETBİS</span>
                            </a>
                        @endif
                    </div>

                </div>
            </div>

            <!-- iyzico & Payment Methods Bar -->
            <div class="py-4 border-b border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-[11px] font-semibold text-slate-400">Güvenli Ödeme Altyapısı:</span>
                    <div class="inline-flex items-center gap-2 bg-white px-3 py-1 rounded-lg shadow-xs h-7">
                        <svg class="h-4 w-auto" viewBox="0 0 70 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5.5 3.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm-1.2 5H1.8V17h2.5V8.5zM14.8 8.5l-2.7 6.6-2.6-6.6H7l3.9 9.3-2.3 5.2h2.6l6.3-14.5h-2.7zM23.5 14.8h-5.6l5.6-4.7v-1.6H15.4v2.1h5.3l-5.6 4.7v1.7h8.4v-2.2zM30.8 3.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zm-1.2 5H27.1V17h2.5V8.5zM38.5 8.3c-2.6 0-4.4 2-4.4 4.5 0 2.6 1.8 4.5 4.4 4.5 1.7 0 3.1-.8 3.8-2.1l-2-.9c-.4.7-1.1 1.1-1.8 1.1-1.2 0-2-.9-2.1-2.1h6.1c.1-.2.1-.6.1-.8 0-2.4-1.7-4.2-4.1-4.2zm-2 3.7c.2-1.1.8-1.7 1.9-1.7 1 0 1.7.6 1.8 1.7h-3.7zM50.2 8.3c-2.7 0-4.7 2-4.7 4.5 0 2.5 2 4.5 4.7 4.5 2.7 0 4.7-2 4.7-4.5 0-2.5-2-4.5-4.7-4.5zm0 6.9c-1.5 0-2.5-1.1-2.5-2.4 0-1.3 1-2.4 2.5-2.4 1.5 0 2.5 1.1 2.5 2.4 0 1.3-1 2.4-2.5 2.4z" fill="#1E64FF"/>
                        </svg>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[11px] text-emerald-400 bg-emerald-950/60 border border-emerald-800/60 px-2.5 py-0.5 rounded-md font-medium">
                        <i class="fas fa-shield-halved text-[10px]"></i>
                        <span>iyzico Korumalı Alışveriş</span>
                    </span>
                </div>

                <!-- Card provider logos (Sharp Inline SVGs - Never Break) -->
                <div class="flex items-center gap-2">
                    <!-- Visa -->
                    <div class="bg-white px-2.5 py-1 rounded-lg h-7 flex items-center justify-center shadow-xs" title="Visa">
                        <svg class="h-3.5 w-auto" viewBox="0 0 100 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M38.868 1.168L25.568 30.832H16.888L10.364 7.332C9.97 5.798 9.638 5.25 8.458 4.604C6.534 3.552 3.346 2.576 0.592 1.97L0.774 1.168H14.436C16.186 1.168 17.754 2.336 18.14 4.328L21.592 22.704L30.128 1.168H38.868ZM72.472 20.914C72.506 12.942 61.418 12.498 61.496 8.928C61.522 7.846 62.564 6.684 64.834 6.38C65.958 6.23 69.09 6.118 72.568 7.728L73.938 1.41C72.07 0.738 69.686 0.1 66.688 0.1C58.56 0.1 52.834 4.422 52.786 10.618C52.736 15.19 56.87 17.742 59.976 19.264C63.168 20.826 64.24 21.824 64.224 23.21C64.198 25.336 61.67 26.268 59.328 26.302C55.192 26.362 52.786 25.186 50.876 24.296L49.458 30.944C51.376 31.826 54.928 32.584 58.606 32.628C67.242 32.628 72.432 28.362 72.472 20.914ZM93.76 30.832H101.338L94.724 1.168H87.728C86.152 1.168 84.826 2.084 84.238 3.496L71.95 30.832H80.64L82.37 26.046H92.982L93.76 30.832ZM84.772 19.458L89.14 7.476L91.668 19.458H84.772ZM50.148 1.168L43.312 30.832H35.028L41.864 1.168H50.148Z" fill="#1A1F71"/>
                        </svg>
                    </div>
                    <!-- Mastercard -->
                    <div class="bg-white px-2 py-1 rounded-lg h-7 flex items-center justify-center shadow-xs" title="Mastercard">
                        <svg class="h-4 w-auto" viewBox="0 0 36 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="13" cy="12" r="7.5" fill="#EB001B"/>
                            <circle cx="23" cy="12" r="7.5" fill="#F79E1B"/>
                            <path d="M18 6.4A7.47 7.47 0 0 0 15 12c0 2.24 1 4.25 2.56 5.6A7.47 7.47 0 0 0 21 12a7.47 7.47 0 0 0-3-5.6Z" fill="#FF5F00"/>
                        </svg>
                    </div>
                    <!-- TROY -->
                    <div class="bg-white px-2 py-1 rounded-lg h-7 flex items-center justify-center shadow-xs" title="TROY">
                        <svg class="h-4 w-auto" viewBox="0 0 54 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 3C0 1.34315 1.34315 0 3 0H51C52.6569 0 54 1.34315 54 3V17C54 18.6569 52.6569 20 51 20H3C1.34315 20 0 18.6569 0 17V3Z" fill="#0079C1"/>
                            <path d="M8 5H17V8H14V15H11V8H8V5ZM18.5 5H24.5C26.5 5 28 6.2 28 8.2C28 9.6 27.2 10.7 25.8 11.1L28.5 15H25.2L22.8 11.5H21.5V15H18.5V5ZM21.5 7.5V9.5H24.2C24.8 9.5 25.2 9.1 25.2 8.5C25.2 7.9 24.8 7.5 24.2 7.5H21.5ZM29.5 10C29.5 7.2 31.8 5 34.8 5C37.8 5 40.1 7.2 40.1 10C40.1 12.8 37.8 15 34.8 15C31.8 15 29.5 12.8 29.5 10ZM32.5 10C32.5 11.5 33.5 12.6 34.8 12.6C36.1 12.6 37.1 11.5 37.1 10C37.1 8.5 36.1 7.4 34.8 7.4C33.5 7.4 32.5 8.5 32.5 10ZM41.5 5H44.8L47 9.2L49.2 5H52.5L48.6 11.2V15H45.4V11.2L41.5 5Z" fill="#FFFFFF"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Compliance Bar -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $siteTitle }}. Tüm hakları saklıdır.</p>
                <div class="flex items-center gap-4 text-slate-400">
                    <span class="flex items-center gap-1.5"><i class="fas fa-shield-halved text-emerald-500"></i> T.C. Sağlık Bakanlığı ÜTS Kayıtlı</span>
                    <span>•</span>
                    <span>T.C. Ticaret Bakanlığı ETBİS Kayıtlı</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Fixed Floating WhatsApp Button (Bottom-Left) -->
    @php
        $waRaw = preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('contact_whatsapp', '905469416996'));
        $waDefaultMsg = urlencode("Merhaba " . $siteTitle . ", ürünler hakkında bilgi almak istiyorum.");
    @endphp
    <div class="fixed bottom-6 left-6 z-50 group flex items-center">
        <a href="https://wa.me/{{ $waRaw }}?text={{ $waDefaultMsg }}" 
           target="_blank" 
           rel="noopener noreferrer"
           class="relative w-14 h-14 bg-[#25D366] hover:bg-[#20ba5a] text-white rounded-full flex items-center justify-center shadow-2xl hover:shadow-emerald-500/50 transition-all duration-300 transform hover:scale-110 active:scale-95"
           title="WhatsApp Destek Hattı"
           aria-label="WhatsApp Destek Hattı">
            
            <!-- Animated subtle pulse ring -->
            <span class="absolute inset-0 rounded-full bg-[#25D366] opacity-40 animate-ping pointer-events-none"></span>
            
            <i class="fab fa-whatsapp text-3xl relative z-10"></i>
        </a>

        <!-- Hover Tooltip Message -->
        <a href="https://wa.me/{{ $waRaw }}?text={{ $waDefaultMsg }}" 
           target="_blank" 
           rel="noopener noreferrer"
           class="hidden sm:inline-flex items-center gap-2 ml-3 bg-slate-900 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xl opacity-0 group-hover:opacity-100 translate-x-[-10px] group-hover:translate-x-0 transition-all duration-200 pointer-events-none group-hover:pointer-events-auto border border-slate-700">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>WhatsApp Destek Hattı</span>
        </a>
    </div>

    <!-- Cart Drawer -->
    <div x-show="$store.cart.open" x-cloak class="fixed inset-0 z-[2000]" aria-labelledby="slide-over-title"
        role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$store.cart.open = false"></div>
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div class="w-screen max-w-md"
                x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-black text-gray-900" id="slide-over-title">Sepetim (<span
                                    x-text="$store.cart.items.length"></span>)</h2>
                            <button @click="$store.cart.open = false" type="button"
                                class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="mt-8 px-2">
                            <div class="flow-root">
                                <ul role="list" class="-my-6 divide-y divide-gray-200">
                                    <template x-for="item in $store.cart.items" :key="item.id">
                                        <li class="flex py-6">
                                            <div
                                                class="h-24 w-20 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 bg-gray-50">
                                                <img :src="item.image" :alt="item.name"
                                                    class="h-full w-full object-contain p-2">
                                            </div>
                                            <div class="ml-4 flex flex-1 flex-col">
                                                <div>
                                                    <div
                                                        class="flex justify-between text-sm font-bold text-gray-900 leading-tight">
                                                        <h3 x-text="item.brand" class="uppercase"></h3>
                                                        <p class="ml-1 whitespace-nowrap" x-text="item.price + ' TL'">
                                                        </p>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500 line-clamp-2"
                                                        x-text="item.name"></p>
                                                </div>
                                                <div class="flex flex-1 items-end justify-between text-xs">
                                                    <div class="flex items-center gap-3 border rounded px-2">
                                                        <button @click="$store.cart.decrement(item.id)">-</button>
                                                        <span x-text="item.qty"></span>
                                                        <button @click="$store.cart.increment(item.id)">+</button>
                                                    </div>
                                                    <button @click="$store.cart.remove(item.id)"
                                                        class="font-bold text-[var(--primary-color)]">Kaldır</button>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                                <div x-show="$store.cart.items.length === 0" class="text-center py-24 text-gray-400">
                                    <i class="fas fa-shopping-basket text-5xl mb-4 opacity-20"></i>
                                    <p class="font-bold italic">Sepetiniz şu an boş.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div x-show="$store.cart.items.length > 0" class="border-t border-gray-200 px-4 py-6 sm:px-6 bg-gray-50/50">
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-sm font-medium text-gray-600">
                                <p>Ara Toplam</p>
                                <p x-text="$store.cart.subtotal().toFixed(2) + ' TL'"></p>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-gray-600">
                                <p>Kargo Ücreti</p>
                                <p x-text="$store.cart.shipping() === 0 ? 'Ücretsiz' : $store.cart.shipping().toFixed(2) + ' TL'" :class="$store.cart.shipping() === 0 ? 'text-green-600' : ''"></p>
                            </div>
                            <div x-show="$store.cart.shipping() > 0" class="bg-emerald-50 p-2.5 rounded-xl text-[11px] text-emerald-800 font-bold border border-emerald-200/60 flex items-center gap-2">
                                <i class="fas fa-truck-fast text-emerald-600"></i>
                                <span><span x-text="(700 - $store.cart.subtotal()).toFixed(2) + ' TL'"></span> daha ürün ekleyin, kargo bedavaya gelsin!</span>
                            </div>
                        </div>
                        <div class="flex justify-between text-xl font-black text-gray-900 border-t border-gray-100 pt-4">
                            <p>Toplam</p>
                            <p x-text="$store.cart.total() + ' TL'"></p>
                        </div>
                        <div class="mt-8">
                            @auth
                                <a href="{{ route('checkout') }}"
                                    class="flex items-center justify-center rounded-2xl border border-transparent bg-emerald-600 hover:bg-emerald-700 px-6 py-4 text-base font-black text-white shadow-xl shadow-emerald-600/20 transition-all active:scale-95">Ödemeye
                                    Geç</a>
                            @else
                                <button @click="$dispatch('open-checkout-choice')"
                                    class="w-full flex items-center justify-center rounded-2xl border border-transparent bg-emerald-600 hover:bg-emerald-700 px-6 py-4 text-base font-black text-white shadow-xl shadow-emerald-600/20 transition-all active:scale-95">Ödemeye
                                    Geç</button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quote Drawer (Teklif Sepeti Çekmecesi) -->
    <div x-show="$store.quote.open" x-cloak class="fixed inset-0 z-[2000]" aria-labelledby="slide-over-quote-title"
        role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-xs transition-opacity" @click="$store.quote.open = false"></div>
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div class="w-screen max-w-md"
                x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl">
                    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                        <div class="flex items-start justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2" id="slide-over-quote-title">
                                    <i class="fas fa-file-invoice-dollar text-amber-500"></i>
                                    <span>Teklif Sepetim (<span x-text="$store.quote.items.length"></span>)</span>
                                </h2>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Toplu alım veya bağış için özel indirimli fiyat teklifi talep edin.</p>
                            </div>
                            <button @click="$store.quote.open = false" type="button"
                                class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="mt-6 px-1">
                            <div class="flow-root">
                                <ul role="list" class="-my-4 divide-y divide-slate-100">
                                    <template x-for="item in $store.quote.items" :key="item.id">
                                        <li class="flex py-4 gap-3 group">
                                            <div class="h-20 w-16 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center p-1">
                                                <img :src="item.image" :alt="item.name" class="h-full w-full object-contain">
                                            </div>
                                            <div class="flex flex-1 flex-col justify-between">
                                                <div>
                                                    <div class="flex justify-between items-start text-xs font-black text-slate-900 gap-1">
                                                        <p class="line-clamp-2 leading-tight" x-text="item.name"></p>
                                                        <p class="whitespace-nowrap text-emerald-700 font-black" x-text="(item.price * item.qty).toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' TL'"></p>
                                                    </div>
                                                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="'Birim Liste Fiyatı: ' + item.price.toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' TL'"></p>
                                                </div>
                                                
                                                <div class="flex items-center justify-between mt-2 pt-1 border-t border-slate-50">
                                                    <!-- Quantity input & quick bulk buttons -->
                                                    <div class="flex items-center gap-1.5">
                                                        <div class="flex items-center border border-slate-200 rounded-lg bg-slate-50 overflow-hidden">
                                                            <button @click="$store.quote.decrement(item.id)" class="px-2 py-0.5 text-slate-600 hover:bg-slate-200 text-xs font-black">-</button>
                                                            <input type="number" :value="item.qty" @change="$store.quote.updateQty(item.id, $event.target.value)" class="w-12 text-center text-xs font-bold bg-transparent border-0 py-0.5 focus:ring-0">
                                                            <button @click="$store.quote.increment(item.id)" class="px-2 py-0.5 text-slate-600 hover:bg-slate-200 text-xs font-black">+</button>
                                                        </div>
                                                        <div class="flex gap-1">
                                                            <button @click="$store.quote.updateQty(item.id, 20)" class="px-1.5 py-0.5 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded text-[10px] font-black" title="20 Adet">+20</button>
                                                            <button @click="$store.quote.updateQty(item.id, 50)" class="px-1.5 py-0.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded text-[10px] font-black" title="50 Adet">+50</button>
                                                        </div>
                                                    </div>
                                                    
                                                    <button @click="$store.quote.remove(item.id)" class="text-xs font-bold text-rose-500 hover:text-rose-700">
                                                        <i class="fas fa-trash-alt text-[10px] mr-0.5"></i> Sil
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                                
                                <div x-show="$store.quote.items.length === 0" class="text-center py-20 text-slate-400 space-y-3">
                                    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-3xl flex items-center justify-center mx-auto shadow-inner">
                                        <i class="fas fa-file-invoice-dollar text-2xl"></i>
                                    </div>
                                    <p class="font-bold text-slate-700 text-sm">Teklif sepetiniz şu an boş.</p>
                                    <p class="text-xs text-slate-400 max-w-xs mx-auto">Ürün detay sayfalarından "Teklif Sepetine Ekle" butonuna basarak toplu alım veya bağış teklifi isteyebilirsiniz.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div x-show="$store.quote.items.length > 0" class="border-t border-slate-200 px-4 py-5 sm:px-6 bg-slate-50">
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-xs font-bold text-slate-500">
                                <p>Tahmini Standart Tutar</p>
                                <p x-text="$store.quote.subtotal().toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' TL'"></p>
                            </div>
                            <div class="bg-amber-50 p-2.5 rounded-xl text-[11px] text-amber-800 font-bold border border-amber-200/60 flex items-center gap-2">
                                <i class="fas fa-percent text-amber-600"></i>
                                <span>Talebiniz incelendikten sonra size özel indirimli fiyat teklifi verilecektir.</span>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <a href="{{ route('quote.cart') }}"
                                class="w-full flex items-center justify-center gap-2 rounded-2xl bg-amber-600 hover:bg-amber-700 px-6 py-3.5 text-sm font-black text-white shadow-lg shadow-amber-600/20 transition-all active:scale-95">
                                <span>Teklif Formunu Doldur &amp; Gönder</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                            <button type="button" @click="$store.quote.clear()" class="w-full text-center py-1.5 text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">
                                Sepeti Temizle
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Choice Modal -->
    <div x-data="{ open: false }" 
         @open-checkout-choice.window="open = true"
         x-show="open" 
         x-cloak
         class="fixed inset-0 z-[3000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        
        <div @click.away="open = false" 
             x-show="open"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white w-full max-w-md rounded-[40px] shadow-2xl overflow-hidden relative">
            
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl"></div>
            
            <div class="p-8 md:p-10 relative">
                <button @click="open = false" class="absolute right-6 top-6 text-slate-300 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
                
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-sm border border-emerald-100">
                        <i class="fas fa-shopping-basket text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 uppercase italic tracking-tighter mb-2">Devam Etmek İçin Seçim Yapın</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Avantajlı alışveriş için üye olun veya hızlıca devam edin.</p>
                </div>
                
                <div class="space-y-4">
                    <a href="{{ route('login') }}" class="flex items-center justify-between w-full p-6 bg-emerald-600 text-white rounded-[30px] shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all group overflow-hidden relative">
                        <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="flex items-center gap-4 relative z-10">
                            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/10">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-black uppercase italic leading-none mb-1">Giriş Yap</p>
                                <p class="text-[9px] font-bold text-white/70 uppercase tracking-widest">Siparişlerini takip et</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform relative z-10"></i>
                    </a>
                    
                    <a href="{{ route('checkout') }}" class="flex items-center justify-between w-full p-6 bg-white text-slate-900 rounded-[30px] border-2 border-slate-100 hover:border-emerald-600 transition-all group relative">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center border border-slate-100 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-black uppercase italic leading-none mb-1">Üyeliksiz Devam Et</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Hızlı alışveriş</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-xs group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <div class="mt-8 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Henüz hesabınız yok mu? <a href="{{ route('register') }}" class="text-emerald-600 underline font-bold hover:text-emerald-700 transition-colors">Şimdi Üye Ol</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-data="{ show: false, message: '' }"
        x-on:fav-added.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)" x-show="show"
        x-transition x-cloak
        class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-full shadow-2xl z-[3000] font-black italic text-sm tracking-tighter">
        <span x-text="message"></span>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            if (!Alpine.store('cart')) {
                Alpine.store('cart', {
                    items: JSON.parse(localStorage.getItem('cart_items')) || [],
                    open: false,
                    add(product, qty = 1) {
                        const addQty = parseInt(qty) || 1;
                        const existing = this.items.find(i => String(i.id) === String(product.id));
                        if (existing) {
                            existing.qty += addQty;
                        } else {
                            this.items.push({
                                id: product.id,
                                slug: product.slug || '',
                                name: product.name || '',
                                brand: product.brand || '',
                                price: parseFloat(product.price) || 0,
                                category_id: product.category_id || '',
                                image: product.image || '',
                                free_shipping: Boolean(product.free_shipping),
                                eft_discount: Boolean(product.eft_discount),
                                sku: product.sku || '',
                                qty: addQty
                            });
                        }
                        this.save();
                        this.open = true;
                        if (window.notify) window.notify('success', 'Ürün sepete eklendi!');
                    },
                    increment(id) {
                        const item = this.items.find(i => String(i.id) === String(id));
                        if (item) item.qty++;
                        this.save();
                    },
                    decrement(id) {
                        const item = this.items.find(i => String(i.id) === String(id));
                        if (item && item.qty > 1) {
                            item.qty--;
                        } else {
                            this.remove(id);
                        }
                        this.save();
                    },
                    remove(id) {
                        this.items = this.items.filter(i => String(i.id) !== String(id));
                        this.save();
                    },
                    subtotal() {
                        return this.items.reduce((total, item) => total + (item.price * item.qty), 0);
                    },
                    shipping() {
                        const hasFreeShippingItem = this.items.some(item => item.free_shipping === true);
                        return (this.subtotal() >= 700 || hasFreeShippingItem) ? 0 : (this.items.length > 0 ? 89 : 0);
                    },
                    total() {
                        return (this.subtotal() + this.shipping()).toFixed(2);
                    },
                    clear() {
                        this.items = [];
                        this.save();
                    },
                    save() {
                        localStorage.setItem('cart_items', JSON.stringify(this.items));
                    }
                });
            }

            if (!Alpine.store('fav')) {
                Alpine.store('fav', {
                    items: JSON.parse(localStorage.getItem('fav_items')) || [],
                    toggle(product) {
                        const idx = this.items.findIndex(i => i.id === product.id);
                        if (idx > -1) {
                            this.items.splice(idx, 1);
                            window.dispatchEvent(new CustomEvent('fav-added', { detail: 'FAVORİLERİMDEN KALDIRILDI' }));
                        } else {
                            this.items.push(product);
                            window.dispatchEvent(new CustomEvent('fav-added', { detail: 'FAVORİLERİME EKLENDİ!' }));
                        }
                        this.save();
                    },
                    has(id) {
                        return this.items.some(i => i.id === id);
                    },
                    save() {
                        localStorage.setItem('fav_items', JSON.stringify(this.items));
                    }
                });
            }

            if (!Alpine.store('quote')) {
                Alpine.store('quote', {
                    items: JSON.parse(localStorage.getItem('quote_items')) || [],
                    open: false,
                    add(product, qty = 1) {
                        const addQty = parseInt(qty) || 1;
                        const existing = this.items.find(i => String(i.id) === String(product.id));
                        if (existing) {
                            existing.qty += addQty;
                        } else {
                            this.items.push({
                                id: product.id,
                                slug: product.slug,
                                name: product.name,
                                brand: product.brand || '',
                                price: parseFloat(product.price) || 0,
                                sku: product.sku || '',
                                image: product.image || '',
                                qty: addQty
                            });
                        }
                        this.save();
                        window.dispatchEvent(new CustomEvent('quote-added', { detail: 'ÜRÜN TEKLİF SEPETİNE EKLENDİ' }));
                        if (window.notify) window.notify('success', 'Ürün teklif sepetinize eklendi!');
                        this.open = true;
                    },
                    increment(id) {
                        const item = this.items.find(i => String(i.id) === String(id));
                        if (item) item.qty++;
                        this.save();
                    },
                    decrement(id) {
                        const item = this.items.find(i => String(i.id) === String(id));
                        if (item && item.qty > 1) {
                            item.qty--;
                        } else {
                            this.remove(id);
                        }
                        this.save();
                    },
                    updateQty(id, qty) {
                        const item = this.items.find(i => String(i.id) === String(id));
                        const validQty = parseInt(qty);
                        if (item && validQty > 0) {
                            item.qty = validQty;
                            this.save();
                        }
                    },
                    remove(id) {
                        this.items = this.items.filter(i => String(i.id) !== String(id));
                        this.save();
                    },
                    subtotal() {
                        return this.items.reduce((total, item) => total + (item.price * item.qty), 0);
                    },
                    clear() {
                        this.items = [];
                        this.save();
                    },
                    save() {
                        localStorage.setItem('quote_items', JSON.stringify(this.items));
                    }
                });
            }
        });

        // Tab Title Switcher Logic (Alternating after 30s delay)
        (function() {
            const active = {{ \App\Models\Setting::getValue('tab_switch_active', true) ? 'true' : 'false' }};
            if (!active) return;
            
            let originalTitle = document.title;
            const awayTitle = "{{ \App\Models\Setting::getValue('tab_switch_away_title', 'Bizi Unutma! 😢') }}";
            let switchInterval = null;
            let startTimeout = null;
            
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    originalTitle = document.title;
                    
                    // Start after 30 seconds
                    startTimeout = setTimeout(() => {
                        let showOriginal = false;
                        switchInterval = setInterval(() => {
                            document.title = showOriginal ? originalTitle : awayTitle;
                            showOriginal = !showOriginal;
                        }, 3000); // Switch every 3 seconds
                    }, 30000); // 30 seconds delay
                } else {
                    // Clear both timer and interval
                    if (startTimeout) clearTimeout(startTimeout);
                    if (switchInterval) clearInterval(switchInterval);
                    
                    document.title = originalTitle;
                }
            });
        })();

        // Global Notification Helper
        window.notify = function(type, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        };
    </script>

    @yield('scripts')

    <!-- Back to Top Button -->
    <div x-data="{ showTop: false }" @scroll.window="showTop = (window.pageYOffset > 500)"
        class="fixed bottom-8 right-8 z-[1500]">
        <button x-show="showTop" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-10 scale-90"
            @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
            class="w-14 h-14 bg-slate-900/90 backdrop-blur-md text-white rounded-2xl shadow-2xl flex items-center justify-center hover:bg-[var(--primary-color)] transition-all transform hover:-translate-y-2 group border border-white/10">
            <i class="fas fa-arrow-up text-lg group-hover:animate-bounce"></i>
        </button>
    </div>

    <!-- Cookie Consent Banner -->
    <div x-data="{ 
            accepted: localStorage.getItem('cookie_accepted') === 'true',
            showDetails: false,
            accept() {
                localStorage.setItem('cookie_accepted', 'true');
                this.accepted = true;
            }
         }" 
         x-show="!accepted" 
         x-transition:enter="transition ease-out duration-500"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         class="fixed bottom-0 left-0 right-0 z-[10000] p-4 md:p-6"
         x-cloak>
        <div class="ty-container">
            <div class="bg-slate-900/95 backdrop-blur-xl border border-white/10 rounded-[32px] p-6 shadow-2xl flex flex-col gap-6 select-none relative overflow-hidden text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shrink-0">
                            <i class="fas fa-cookie-bite text-2xl animate-pulse"></i>
                        </div>
                        <div class="text-left">
                            <h4 class="text-white font-black italic tracking-tighter uppercase text-sm mb-1">Çerez Politikası</h4>
                            <p class="text-slate-400 text-xs font-medium leading-relaxed">Size daha iyi bir deneyim sunabilmek için çerezleri kullanıyoruz.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 w-full md:w-auto">
                        <button @click="showDetails = !showDetails" class="flex-grow md:flex-none text-center px-6 py-3 text-white text-[10px] font-black uppercase tracking-widest hover:underline italic opacity-50 hover:opacity-100 transition-opacity">
                            <span x-text="showDetails ? 'Kapat' : 'Detay Bilgi'"></span>
                        </button>
                        <button @click="accept()" class="flex-grow md:flex-none px-10 py-3 bg-[var(--primary-color)] text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-[var(--primary-hover)] transition-all shadow-xl shadow-orange-500/20 active:scale-95 transform active:translate-y-1">Kabul Et</button>
                    </div>
                </div>

                <!-- Detailed Explanation -->
                <div x-show="showDetails" x-collapse x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="pt-6 border-t border-white/5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-[11px] leading-relaxed">
                        <div class="flex gap-4 items-start text-left">
                            <div class="w-8 h-8 rounded-lg bg-green-500/10 flex items-center justify-center text-green-500 shrink-0"><i class="fas fa-user-shield"></i></div>
                            <div class="text-slate-400">
                                <strong class="text-white block mb-1 uppercase italic tracking-tighter">Kişisel Veri Güvenliği</strong>
                                Şahsi bilgileriniz çerezler aracılığıyla asla depolanmaz veya üçüncü taraflarla paylaşılmaz.
                            </div>
                        </div>
                        <div class="flex gap-4 items-start text-left">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 shrink-0"><i class="fas fa-magic"></i></div>
                            <div class="text-slate-400">
                                <strong class="text-white block mb-1 uppercase italic tracking-tighter">Kullanıcı Deneyimi</strong>
                                Çerezler sadece son baktığınız ürünler, sepetiniz ve tercihlerinizi hatırlayarak size özel bir alışveriş deneyimi sunmak için kullanılır.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Customer Reviews Widget -->
    <script id='merchantWidgetScript' src="https://www.gstatic.com/shopping/merchant/merchantwidget.js" defer></script>
    <script>
      merchantWidgetScript.addEventListener('load', function () {
        merchantwidget.start({
             // REQUIRED FIELDS
             merchant_id: 5822707197,

             // OPTIONAL FIELDS
             position: 'BOTTOM_LEFT',
             region: 'TR'
        });
      });
    </script>

    @yield('scripts')
</body>

</html>