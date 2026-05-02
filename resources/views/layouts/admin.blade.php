<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
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

        $primaryColor = \App\Models\Setting::getValue('site_primary_color', '#f27a1a');
    @endphp
    <link rel="icon" type="{{ $faviconType }}" href="{{ $faviconUrl }}">
    <title>MultiSync | Pazaryeri Entegrasyon Paneli</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 for Toasts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 
                            800: '#075985', 900: '#0c4a6e', 950: '#082f49',
                        },
                        corporate: '#0f172a'
                    }
                }
            }
        }
    </script>
    
    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --primary-hover: {{ $primaryColor }}ee;
        }
        [x-cloak] { display: none !important; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .sidebar-item-active { background: #0ea5e9; color: white; border-right: 4px solid white; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        @keyframes pulse-soft { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        .animate-pulse-soft { animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="h-full overflow-hidden flex" x-data="{ sidebarOpen: true, mobileMenu: false }">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="hidden md:flex flex-col h-full bg-corporate text-slate-300 transition-all duration-300 z-50 shadow-2xl relative">
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800 shrink-0 overflow-hidden">
            <div class="h-9 w-9 bg-brand-500 rounded-lg flex items-center justify-center shrink-0 shadow-lg shadow-brand-500/20">
                <i class="fas fa-sync-alt text-white"></i>
            </div>
            <span x-show="sidebarOpen" x-transition class="ml-3 font-bold text-lg text-white tracking-tight whitespace-nowrap">MultiSync</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1 custom-scrollbar">
            <p x-show="sidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-2">Genel</p>
            
            <a href="/admin" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-th-large w-6 flex justify-center text-lg {{ Request::is('admin') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Dashboard</span>
            </a>

            <a href="/admin/products" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/products*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-box w-6 flex justify-center text-lg {{ Request::is('admin/products*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Ürünler</span>
            </a>

            <a href="/admin/orders" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/orders*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-shopping-cart w-6 flex justify-center text-lg {{ Request::is('admin/orders*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Siparişler</span>
            </a>

            <a href="{{ route('admin.coupons.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/coupons*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-ticket-alt w-6 flex justify-center text-lg {{ Request::is('admin/coupons*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Kuponlar</span>
            </a>

            <a href="{{ route('admin.comments.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/comments*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-comments w-6 flex justify-center text-lg {{ Request::is('admin/comments*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Yorumlar</span>
            </a>

            <div class="pt-6 pb-2">
                <p x-show="sidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sistem Ayarları</p>
            </div>

            <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/brands*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-industry w-6 flex justify-center text-lg {{ Request::is('admin/brands*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Markalar</span>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/categories*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-tags w-6 flex justify-center text-lg {{ Request::is('admin/categories*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Kategoriler</span>
            </a>

            <a href="{{ route('admin.pages.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/pages*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-file-contract w-6 flex justify-center text-lg {{ Request::is('admin/pages*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm text-[11px] leading-tight flex-grow">Sözleşmeler & Politikalar</span>
            </a>

            <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/faqs*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-question-circle w-6 flex justify-center text-lg {{ Request::is('admin/faqs*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Sıkça Sorulan Sorular</span>
            </a>

            <a href="/admin/appearance" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/appearance*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-eye w-6 flex justify-center text-lg {{ Request::is('admin/appearance*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Site Görünümü</span>
            </a>

            <div class="pt-6 pb-2">
                <p x-show="sidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Senkronizasyon</p>
            </div>

            <a href="/admin/sync/stock" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/sync/stock*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-cubes w-6 flex justify-center text-lg {{ Request::is('admin/sync/stock*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Stok Senkronize</span>
            </a>

            <a href="/admin/sync/price" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/sync/price*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-tag w-6 flex justify-center text-lg {{ Request::is('admin/sync/price*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Fiyat Senkronize</span>
            </a>

            <div class="pt-6 pb-2">
                <p x-show="sidebarOpen" class="px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Altyapı</p>
            </div>

            <a href="/admin/marketplaces" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/marketplaces*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-plug w-6 flex justify-center text-lg {{ Request::is('admin/marketplaces*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Pazaryeri Bağlantıları</span>
            </a>

            <a href="/admin/logs" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/logs*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-terminal w-6 flex justify-center text-lg {{ Request::is('admin/logs*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Loglar & Debug</span>
            </a>

            <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-slate-800 transition-all group {{ Request::is('admin/settings*') ? 'sidebar-item-active' : '' }}">
                <i class="fas fa-sliders-h w-6 flex justify-center text-lg {{ Request::is('admin/settings*') ? 'text-white' : 'text-slate-500 group-hover:text-brand-400' }}"></i>
                <span x-show="sidebarOpen" class="font-medium text-sm">Ayarlar</span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center p-2 rounded-lg bg-slate-800 hover:bg-slate-700 transition-colors">
                <i class="fas" :class="sidebarOpen ? 'fa-angle-double-left' : 'fa-angle-double-right'"></i>
            </button>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 overflow-hidden">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 md:px-8 shrink-0 z-40">
            <div class="flex items-center gap-4">
                <button @click="mobileMenu = true" class="md:hidden p-2 text-slate-500">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="hidden md:flex items-center gap-2">
                    <span class="flex h-2 w-2 rounded-full bg-emerald-500 animate-pulse-soft"></span>
                    <span class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Sistem Aktif</span>
                </div>
            </div>

            <div class="flex items-center gap-3 md:gap-6" x-data="{ 
                count: 0,
                notifications: [],
                latestId: 0,
                open: false,
                sound: new Audio('/assets/sounds/order-notification.wav'),
                unlocked: false,
                init() {
                    this.fetchUpdates();
                    setInterval(() => this.fetchUpdates(), 10000);
                    
                    // Audio unlock mechanism
                    const unlock = () => {
                        this.sound.play().then(() => {
                            this.sound.pause();
                            this.sound.currentTime = 0;
                            this.unlocked = true;
                            console.log('Audio unlocked');
                        }).catch(err => {
                            console.log('Unlock failed', err);
                        });
                        document.removeEventListener('click', unlock);
                    };
                    document.addEventListener('click', unlock);
                },
                fetchUpdates() {
                    fetch('/admin/api/notifications')
                        .then(res => res.json())
                        .then(data => {
                            if (data.latest_id > this.latestId && this.latestId !== 0) {
                                this.sound.play().catch(e => {
                                    console.log('Audio play blocked', e);
                                    notify('warning', 'Yeni Sipariş! (Ses için sayfada bir yere tıklayın)');
                                });
                                notify('info', 'Yeni bir siparişiniz var!');
                            }
                            this.count = data.count;
                            this.notifications = data.notifications;
                            this.latestId = data.latest_id;
                        });
                },
                markAsRead() {
                    if (this.count > 0) {
                        this.count = 0;
                        this.notifications = this.notifications.map(n => ({...n, is_new: false}));
                        fetch('/admin/api/notifications/read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        });
                    }
                }
            }">
                <!-- View Site Button -->
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:flex items-center gap-2 px-4 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-brand-600 transition-all shadow-lg hover:shadow-brand-500/20 active:scale-95 transform">
                    <i class="fas fa-external-link-alt text-[10px] opacity-70"></i>
                    SİTEYİ GÖRÜNTÜLE
                </a>

                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

                <!-- Marketplace Selector -->
                <div class="relative" x-data="{ selectorOpen: false }">
                    <button @click="selectorOpen = !selectorOpen" class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-200 transition-colors">
                        <i class="fas fa-store text-brand-500"></i>
                        <span class="hidden sm:inline">Tüm Kanallar</span>
                        <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                    </button>
                    <div x-show="selectorOpen" @click.away="selectorOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50">
                        <a href="#" class="px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-brand-500"></span> Tüm Kanallar
                        </a>
                        <a href="#" class="px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-slate-300"></span> Trendyol
                        </a>
                        <a href="#" class="px-4 py-2 text-sm text-slate-700 hover:bg-brand-50 hover:text-brand-600 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-slate-300"></span> Hepsiburada
                        </a>
                    </div>
                </div>

                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

                <!-- Sound Status Connector -->
                <div class="hidden sm:block">
                    <button @click="if(!unlocked) { sound.play().then(() => { sound.pause(); sound.currentTime = 0; unlocked = true; notify('success', 'Sesli bildirimler aktif edildi!'); }).catch(() => notify('error', 'Ses açılamadı, lütfen sayfada bir yere tıklayın.')); }" 
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold transition-all relative border"
                        :class="unlocked ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-rose-50 border-rose-100 text-rose-600 animate-pulse'">
                        <i class="fas" :class="unlocked ? 'fa-volume-up' : 'fa-volume-mute'"></i>
                        <span x-text="unlocked ? 'SES AKTİF' : 'SESİ AÇ'"></span>
                        <template x-if="!unlocked">
                            <span class="absolute -top-1 -right-1 h-2 w-2 bg-rose-500 rounded-full animate-ping"></span>
                        </template>
                    </button>
                </div>

                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

                <!-- Notifications -->
                <div class="relative">
                    <button @click="open = !open; if(open) markAsRead();" class="relative text-slate-500 hover:text-brand-600 transition-colors">
                        <i class="far fa-bell text-xl"></i>
                        <template x-if="count > 0">
                            <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-[10px] text-white flex items-center justify-center rounded-full border-2 border-white" x-text="count"></span>
                        </template>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div x-show="open" @click.away="open = false" x-cloak 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 py-2 z-50 overflow-hidden">
                        <div class="px-4 py-2 border-b border-slate-50 flex items-center justify-between">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">BİLDİRİMLER</p>
                            <span class="text-[9px] px-1.5 py-0.5 bg-brand-50 text-brand-600 rounded font-black" x-text="count + ' YENİ'"></span>
                        </div>
                        <div class="max-h-96 overflow-y-auto custom-scrollbar">
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.url" class="px-4 py-3 hover:bg-slate-50 flex items-start gap-3 border-b border-slate-50 last:border-0 transition-colors" :class="n.is_new ? 'bg-emerald-50/50' : ''">
                                    <div class="h-8 w-8 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-shopping-bag text-xs"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-800" x-text="n.title"></p>
                                        <p class="text-[11px] text-slate-500 truncate" x-text="n.message"></p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black uppercase text-brand-500" x-text="n.channel"></span>
                                            <span class="text-[9px] text-slate-400" x-text="n.time"></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-10 text-center">
                                    <i class="far fa-bell-slash text-2xl text-slate-200 mb-2"></i>
                                    <p class="text-xs text-slate-400 font-medium">Yeni bildirim bulunmuyor.</p>
                                </div>
                            </template>
                        </div>
                        <a href="/admin/orders" class="block py-2 text-center text-[10px] font-bold text-slate-400 hover:text-brand-600 uppercase tracking-widest border-t border-slate-50 mt-1">Tümünü Gör</a>
                    </div>
                </div>

                <div class="w-px h-6 bg-slate-200 hidden sm:block"></div>

                <!-- Profile -->
                <div class="relative" x-data="{ profileOpen: false }">
                    <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 group">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center text-white font-bold text-sm shadow-md group-hover:scale-105 transition-transform">
                            {{ substr(auth()->user()->name, 0, 1) . substr(strrchr(auth()->user()->name, ' '), 1, 1) }}
                        </div>
                        <div class="hidden lg:block text-left">
                            <p class="text-sm font-bold text-slate-800 leading-none">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-slate-500 mt-1 uppercase font-semibold">Super Admin</p>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-slate-400 group-hover:text-slate-600 transition-colors"></i>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="profileOpen" @click.away="profileOpen = false" x-cloak 
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 overflow-hidden">
                        <div class="px-4 py-2 border-b border-slate-50 mb-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">HESABIM</p>
                        </div>
                        <a href="{{ route('admin.settings') }}" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 flex items-center gap-3 transition-colors">
                            <i class="fas fa-user-circle text-slate-400"></i> Profilim
                        </a>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50 flex items-center gap-3 transition-colors">
                                <i class="fas fa-sign-out-alt"></i> Güvenli Çıkış
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto bg-slate-50/50 relative p-4 md:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Toast Notification Logic Example -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        window.notify = (type, title) => {
            Toast.fire({
                icon: type,
                title: title
            });
        };

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
</body>
</html>
