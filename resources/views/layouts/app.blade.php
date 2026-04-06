<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteTitle = \App\Models\Setting::getValue('site_title', 'umutMed Market');
        $siteFavicon = \App\Models\Setting::getValue('site_favicon', '');
        $primaryColor = \App\Models\Setting::getValue('site_primary_color', '#f27a1a');
        $footerQr = \App\Models\Setting::getValue('site_footer_qr', '');
        $defaultFooter = [
            ["title" => "umutMed", "links" => [["text" => "Hakkımızda", "url" => "#"], ["text" => "Kariyer", "url" => "#"], ["text" => "İletişim", "url" => "/iletisim"], ["text" => "Sürdürülebilirlik", "url" => "#"]]],
            ["title" => "Kampanyalar", "links" => [["text" => "Aktif Kampanyalar", "url" => "#"], ["text" => "Elite Üyelik", "url" => "#"], ["text" => "Hediye Fikirleri", "url" => "#"], ["text" => "umutMed Blog", "url" => "#"]]],
            ["title" => "Yardım", "links" => [["text" => "Sıkça Sorulan Sorular", "url" => route('sss')], ["text" => "İade Politikası", "url" => route('page.show', 'iade-iptal-politikasi')], ["text" => "Ödeme Seçenekleri", "url" => route('page.show', 'odeme-politikasi')], ["text" => "Kullanım Koşulları", "url" => route('page.show', 'kullanim-kosullari')]]]
        ];
        $footerCols = json_decode(\App\Models\Setting::getValue('site_footer_columns', json_encode($defaultFooter)), true);
    @endphp

    <title>@yield('title', config('app.name')) - {{ $siteTitle }}</title>

    @if($siteFavicon)
        <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    @endif

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

        @yield('styles')

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
</head>

<body x-data>

    @php
        $defaultMarketplaces = [
            ['name' => 'TRENDYOL', 'url' => 'https://trendyol.com', 'logo' => 'https://www.google.com/s2/favicons?domain=trendyol.com&sz=128', 'color' => '#f27a1a'],
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
                <a href="{{ route('contact') }}"
                    class="hover:text-amber-600 transition-colors uppercase font-black italic tracking-tighter text-[12px]">İletişim
                    & Konum</a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="py-4 shadow-sm">
        <div class="ty-container">
            <div class="flex items-center gap-8">
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
                                    <span class="hidden lg:inline">Yönetim Paneli</span>
                                </a>
                            @else
                                <a href="{{ route('user.dashboard') }}"
                                    class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                                    <i class="far fa-user text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                    <span class="hidden lg:inline">Hesabım</span>
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}"
                                class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                                <i class="far fa-user text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                                <span class="hidden lg:inline">Giriş Yap</span>
                            </a>
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
        </div>
    </header>

    @yield('sub_header')

    <main>
        @yield('content')
    </main>

    <!-- Pre-Footer Action Bar -->
    @php
        $socialActive = \App\Models\Setting::getValue('social_media_active', true);
        $whatsappActive = \App\Models\Setting::getValue('whatsapp_support_active', true);
        $appsActive = \App\Models\Setting::getValue('app_stores_active', true);

        $facebook = \App\Models\Setting::getValue('social_facebook', '#');
        $instagram = \App\Models\Setting::getValue('social_instagram', '#');
        $twitter = \App\Models\Setting::getValue('social_twitter', '#');
        $linkedin = \App\Models\Setting::getValue('social_linkedin', '#');

        $googlePlay = \App\Models\Setting::getValue('app_google_play', '#');
        $appleStore = \App\Models\Setting::getValue('app_apple_store', '#');
    @endphp
    @if($socialActive || $whatsappActive || $appsActive)
        <section class="bg-white border-t border-gray-50 py-16">
            <div class="ty-container flex flex-col md:flex-row items-center justify-between gap-12">
                <!-- Social Media -->
                @if($socialActive)
                    <div class="flex flex-col gap-6 w-full md:w-auto items-center md:items-start text-center md:text-left">
                        <h4
                            class="text-xs font-black text-slate-900 uppercase italic tracking-tighter border-b-2 md:border-b-0 md:border-l-4 border-[var(--primary-color)] md:pl-3 pb-2 md:pb-0 w-fit">
                            Sosyal Medyada Biz</h4>
                        <div class="flex gap-4">
                            <a href="{{ $facebook }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-slate-50 border border-gray-100 flex items-center justify-center text-slate-600 hover:bg-[#3b5998] hover:text-white transition-all transform hover:-translate-y-1 shadow-sm"><i
                                    class="fab fa-facebook-f"></i></a>
                            <a href="{{ $instagram }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-slate-50 border border-gray-100 flex items-center justify-center text-slate-600 hover:bg-[#E1306C] hover:text-white transition-all transform hover:-translate-y-1 shadow-sm"><i
                                    class="fab fa-instagram"></i></a>
                            <a href="{{ $twitter }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-slate-50 border border-gray-100 flex items-center justify-center text-slate-600 hover:bg-black hover:text-white transition-all transform hover:-translate-y-1 shadow-sm"><i
                                    class="fa-brands fa-x-twitter text-lg"></i></a>
                            <a href="{{ $linkedin }}" target="_blank"
                                class="w-12 h-12 rounded-2xl bg-slate-50 border border-gray-100 flex items-center justify-center text-slate-600 hover:bg-[#0077b5] hover:text-white transition-all transform hover:-translate-y-1 shadow-sm"><i
                                    class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                @endif

                <!-- WhatsApp Support -->
                @if($whatsappActive)
                    <div class="flex flex-col items-center gap-6">
                        <h4
                            class="text-xs font-black text-slate-900 uppercase italic tracking-tighter border-b-2 border-green-500 pb-2 w-fit">
                            Hızlı Destek Hattı</h4>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('contact_whatsapp', '905300000000')) }}"
                            target="_blank"
                            class="bg-[#25D366] text-white px-10 py-5 rounded-2xl font-black italic shadow-2xl shadow-green-100 flex items-center gap-5 hover:bg-[#128C7E] transition-all transform hover:-translate-y-1 border-b-4 border-green-700 active:border-b-0 active:translate-y-1">
                            <i class="fab fa-whatsapp text-4xl"></i>
                            <div class="flex flex-col leading-none text-left">
                                <span class="text-[10px] opacity-80 uppercase tracking-widest font-bold mb-1">Sorularınız
                                    İçin</span>
                                <span class="text-xl">WHATSAPP DESTEK</span>
                            </div>
                        </a>
                    </div>
                @endif

                <!-- App Stores -->
                @if($appsActive)
                    <div class="flex flex-col items-center md:items-end gap-6 w-full md:w-auto text-center md:text-right">
                        <h4
                            class="text-xs font-black text-slate-900 uppercase italic tracking-tighter border-b-2 md:border-b-0 md:border-r-4 border-slate-900 md:pr-3 pb-2 md:pb-0 w-fit">
                            Mobil Uygulamamız</h4>
                        <div class="flex flex-wrap gap-4 justify-center md:justify-end">
                            <a href="{{ $googlePlay }}" target="_blank"
                                class="bg-slate-900 text-white px-6 py-4 rounded-2xl flex items-center gap-4 hover:bg-black transition-all border border-slate-800 shadow-2xl transform hover:-translate-y-1 group">
                                <i
                                    class="fab fa-google-play text-3xl text-white group-hover:text-green-400 transition-colors"></i>
                                <div class="flex flex-col leading-none items-start">
                                    <span class="text-[9px] opacity-50 uppercase font-bold mb-1">Google Play'den</span>
                                    <span class="text-sm font-black font-sans tracking-tight italic">İNDİRİN</span>
                                </div>
                            </a>
                            <a href="{{ $appleStore }}" target="_blank"
                                class="bg-slate-900 text-white px-6 py-4 rounded-2xl flex items-center gap-4 hover:bg-black transition-all border border-slate-800 shadow-2xl transform hover:-translate-y-1 group">
                                <i class="fab fa-apple text-3xl text-white group-hover:text-amber-400 transition-colors"></i>
                                <div class="flex flex-col leading-none items-start">
                                    <span class="text-[9px] opacity-50 uppercase font-bold mb-1">App Store'dan</span>
                                    <span class="text-sm font-black font-sans tracking-tight italic">İNDİRİN</span>
                                </div>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-16">
        <div class="ty-container grid grid-cols-1 md:grid-cols-4 gap-12">
            @foreach($footerCols as $col)
                <div>
                    <h4 class="text-sm font-black italic tracking-tighter uppercase mb-6 text-white/90">{{ $col['title'] }}
                    </h4>
                    <ul class="space-y-3 text-sm text-gray-400 font-medium">
                        @foreach($col['links'] as $link)
                            <li><a href="{{ $link['url'] }}"
                                    class="hover:text-[var(--primary-color)] transition-colors">{{ $link['text'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            <div>
                <h4 class="text-sm font-black italic tracking-tighter uppercase mb-6 text-white/90">Güvenli Alışveriş
                </h4>
                <div class="flex flex-col gap-6">
                    <div
                        class="flex flex-wrap gap-4 grayscale opacity-50 hover:grayscale-0 hover:opacity-100 transition-all cursor-pointer">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" class="h-6"
                            alt="Paypal">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" class="h-8"
                            alt="Mastercard">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/d/d6/Visa_2021.svg" class="h-4"
                            alt="Visa">
                    </div>

                    @if($footerQr)
                        <div
                            class="bg-white p-2 rounded-2xl w-fit shadow-2xl shadow-black/40 group hover:scale-105 transition-transform duration-300">
                            <img src="{{ $footerQr }}" class="w-24 h-24 object-contain" alt="QR Kod">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div
            class="ty-container border-t border-slate-800 mt-16 pt-8 flex flex-col md:flex-row items-center justify-between text-xs text-slate-500 font-bold uppercase tracking-widest">
            <p>&copy; 2026 {{ config('app.name') }} | Tüm Hakları Saklıdır.</p>
        </div>
    </footer>

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
                    <div x-show="$store.cart.items.length > 0" class="border-t border-gray-200 px-4 py-6 sm:px-6">
                        <div class="flex justify-between text-base font-bold text-gray-900">
                            <p>Toplam</p>
                            <p x-text="$store.cart.total() + ' TL'"></p>
                        </div>
                        <div class="mt-6">
                            <a href="#"
                                class="flex items-center justify-center rounded-md border border-transparent bg-[var(--primary-color)] px-6 py-3 text-base font-black text-white shadow-sm hover:bg-[var(--primary-hover)]">Ödemeye
                                Geç</a>
                        </div>
                    </div>
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
                    add(product) {
                        const existing = this.items.find(i => i.id === product.id);
                        if (existing) {
                            existing.qty++;
                        } else {
                            this.items.push({ ...product, qty: 1 });
                        }
                        this.save();
                        this.open = true;
                    },
                    increment(id) {
                        const item = this.items.find(i => i.id === id);
                        if (item) item.qty++;
                        this.save();
                    },
                    decrement(id) {
                        const item = this.items.find(i => i.id === id);
                        if (item && item.qty > 1) {
                            item.qty--;
                        } else {
                            this.remove(id);
                        }
                        this.save();
                    },
                    remove(id) {
                        this.items = this.items.filter(i => i.id !== id);
                        this.save();
                    },
                    total() {
                        return this.items.reduce((total, item) => total + (item.price * item.qty), 0).toFixed(2);
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
</body>

</html>