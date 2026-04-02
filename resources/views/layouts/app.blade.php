<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name')) - Trendyol Market</title>
    
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
            background-color: var(--background-color);
            color: var(--text-main);
        }

        .ty-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        [x-cloak] { display: none !important; }

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
    </style>
</head>
<body x-data>

    <!-- Top Info Bar -->
    <div class="bg-gray-100 hidden md:block border-b border-gray-200">
        <div class="ty-container h-8 flex items-center justify-end gap-6 text-[11px] text-gray-500 font-medium">
            <a href="#" class="hover:text-amber-600 transition-colors">İndirim Kuponlarım</a>
            <a href="#" class="hover:text-amber-600 transition-colors">Satış Yap</a>
            <a href="#" class="hover:text-amber-600 transition-colors">Yardım & Destek</a>
        </div>
    </div>

    <!-- Header -->
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
                    
                    <a href="{{ route('favorites') }}" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                        <div class="relative">
                            <i class="far fa-heart text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                            <span x-show="$store.fav.items.length" x-text="$store.fav.items.length" class="absolute -top-2 -right-2 bg-red-500 text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white"></span>
                        </div>
                        <span class="hidden lg:inline">Favorilerim</span>
                    </a>

                    <a href="#" @click.prevent="$store.cart.open = true" class="flex items-center gap-2 hover:text-[var(--primary-color)] group">
                        <div class="relative">
                            <i class="fas fa-shopping-cart text-lg text-gray-400 group-hover:text-[var(--primary-color)]"></i>
                            <span x-show="$store.cart.items.length" x-text="$store.cart.items.length" class="absolute -top-2 -right-2 bg-[var(--primary-color)] text-white text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-white"></span>
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

    <!-- Cart Drawer -->
    <div x-show="$store.cart.open" x-cloak class="fixed inset-0 z-[2000]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$store.cart.open = false"></div>
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div class="w-screen max-w-md" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                    <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                        <div class="flex items-start justify-between">
                            <h2 class="text-lg font-black text-gray-900" id="slide-over-title">Sepetim (<span x-text="$store.cart.items.length"></span>)</h2>
                            <button @click="$store.cart.open = false" type="button" class="text-gray-400 hover:text-gray-500">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="mt-8 px-2">
                            <div class="flow-root">
                                <ul role="list" class="-my-6 divide-y divide-gray-200">
                                    <template x-for="item in $store.cart.items" :key="item.id">
                                        <li class="flex py-6">
                                            <div class="h-24 w-20 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 bg-gray-50">
                                                <img :src="item.image" :alt="item.name" class="h-full w-full object-contain p-2">
                                            </div>
                                            <div class="ml-4 flex flex-1 flex-col">
                                                <div>
                                                    <div class="flex justify-between text-sm font-bold text-gray-900 leading-tight">
                                                        <h3 x-text="item.brand" class="uppercase"></h3>
                                                        <p class="ml-1 whitespace-nowrap" x-text="item.price + ' TL'"></p>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500 line-clamp-2" x-text="item.name"></p>
                                                </div>
                                                <div class="flex flex-1 items-end justify-between text-xs">
                                                    <div class="flex items-center gap-3 border rounded px-2">
                                                        <button @click="$store.cart.decrement(item.id)">-</button>
                                                        <span x-text="item.qty"></span>
                                                        <button @click="$store.cart.increment(item.id)">+</button>
                                                    </div>
                                                    <button @click="$store.cart.remove(item.id)" class="font-bold text-[var(--primary-color)]">Kaldır</button>
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
                            <a href="#" class="flex items-center justify-center rounded-md border border-transparent bg-[var(--primary-color)] px-6 py-3 text-base font-black text-white shadow-sm hover:bg-[var(--primary-hover)]">Ödemeye Geç</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-data="{ show: false, message: '' }" x-on:fav-added.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)" x-show="show" x-transition x-cloak class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-8 py-4 rounded-full shadow-2xl z-[3000] font-black italic text-sm tracking-tighter">
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
                            this.items.push({...product, qty: 1});
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
        })
    </script>

    @yield('scripts')

</body>
</html>
