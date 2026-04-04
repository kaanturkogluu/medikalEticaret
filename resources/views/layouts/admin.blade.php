<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

            <div class="flex items-center gap-3 md:gap-6">
                <!-- Marketplace Selector -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-200 transition-colors">
                        <i class="fas fa-store text-brand-500"></i>
                        <span class="hidden sm:inline">Tüm Kanallar</span>
                        <i class="fas fa-chevron-down text-[10px] ml-1"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-50">
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

                <!-- Notifications -->
                <button class="relative text-slate-500 hover:text-brand-600 transition-colors">
                    <i class="far fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-[10px] text-white flex items-center justify-center rounded-full border-2 border-white">3</span>
                </button>

                <!-- Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 group">
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
                    <div x-show="open" @click.away="open = false" x-cloak 
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
    </script>
</body>
</html>
