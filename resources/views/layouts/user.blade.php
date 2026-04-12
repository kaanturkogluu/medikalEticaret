<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hesabım') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .sidebar-card { background: white; border: 1px solid #f1f5f9; border-radius: 1.5rem; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1); }
        .nav-link { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            padding: 0.75rem 1rem; 
            border-radius: 0.75rem; 
            font-size: 0.875rem; 
            font-weight: 500; 
            color: #475569; 
            transition: all 0.2s; 
        }
        .nav-link:hover { background-color: #fff7ed; color: #f27a1a; }
        .nav-link.active { background-color: #fff7ed; color: #f27a1a; font-weight: 700; }
        .nav-link i { width: 1.25rem; text-align: center; font-size: 1.1rem; }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen flex flex-col bg-[#f8fafc]">

    {{-- Top Navigation --}}
    <header class="bg-white border-b border-slate-100 sticky top-0 z-50 py-4 shadow-sm">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <h1 class="text-2xl font-black italic tracking-tighter text-slate-900">
                    umut<span class="text-orange-500">Med</span>
                </h1>
            </a>
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-xs font-black uppercase italic tracking-tighter text-slate-400 hover:text-orange-500 transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Alışverişe Devam Et
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-black uppercase italic tracking-tighter text-slate-400 hover:text-red-500 transition-all flex items-center gap-2">
                        <i class="fas fa-power-off"></i> Çıkış Yap
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 py-8 w-full">
        <div class="flex gap-6">

            {{-- SIDEBAR --}}
            <aside class="w-64 flex-shrink-0">
                {{-- User Info Card --}}
                <div class="sidebar-card p-5 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center">
                            <span class="text-xl font-black text-orange-500">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    @unless(auth()->user()->email_verified_at)
                    <a href="{{ route('verify.form', ['email' => auth()->user()->email]) }}" class="mt-4 block text-center text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 border border-orange-100 rounded-xl py-2.5 hover:bg-orange-100 transition-all">
                        <i class="fas fa-exclamation-circle mr-1"></i> E-postanı Doğrula
                    </a>
                    @endunless
                </div>

                {{-- Navigation --}}
                <nav class="sidebar-card p-3 space-y-4">
                    {{-- Siparişlerim Group --}}
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-2">Siparişlerim</p>
                        <a href="{{ route('user.orders') }}" class="nav-link {{ Request::is('hesabim/siparislerim*') ? 'active' : '' }}">
                            <i class="fas fa-box-open text-orange-400"></i> Tüm Siparişlerim
                        </a>
                    </div>

                    {{-- Favoriler Group --}}
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-2">Alışveriş Listem</p>
                        <a href="{{ route('favorites') }}" class="nav-link">
                            <i class="fas fa-heart text-red-400"></i> Favorilerim
                        </a>
                    </div>

                    {{-- Hesabım Group --}}
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-2">Hesabım</p>
                        <div class="space-y-1">
                            <a href="{{ route('user.profile') }}" class="nav-link {{ Request::is('hesabim/bilgilerim') ? 'active' : '' }}">
                                <i class="fas fa-user text-blue-400"></i> Kullanıcı Bilgilerim
                            </a>
                            <a href="{{ route('user.addresses') }}" class="nav-link {{ Request::is('hesabim/adreslerim') ? 'active' : '' }}">
                                <i class="fas fa-map-marker-alt text-green-400"></i> Adres Bilgilerim
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="nav-link {{ Request::is('hesabim') && !Request::is('hesabim/*') ? 'active' : '' }}">
                                <i class="fas fa-tachometer-alt text-purple-400"></i> Özet Sayfam
                            </a>
                            <a href="{{ route('user.comments') }}" class="nav-link {{ Request::is('hesabim/yorumlarim') ? 'active' : '' }}">
                                <i class="fas fa-comment-dots text-amber-400"></i> Yorumlarım
                            </a>
                        </div>
                    </div>
                </nav>
            </aside>

            {{-- MAIN CONTENT --}}
            <main class="flex-1 min-w-0">
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-sm font-semibold text-green-700 flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500"></i> {{ session('success') }}
                </div>
                @endif
                @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm font-semibold text-red-700 flex items-center gap-3">
                    <i class="fas fa-times-circle text-red-500"></i> {{ session('error') }}
                </div>
                @endif
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Footer --}}
    <footer class="mt-16 py-8 border-t border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} {{ config('app.name') }} — Tüm Hakları Saklıdır</p>
            <div class="flex items-center justify-center gap-6 mt-3 text-xs text-gray-400">
                <a href="{{ route('page.show', 'kullanim-kosullari') }}" class="hover:text-orange-500 transition-colors">Kullanım Koşulları</a>
                <a href="{{ route('page.show', 'gizlilik-politikasi') }}" class="hover:text-orange-500 transition-colors">Gizlilik Politikası</a>
                <a href="{{ route('page.show', 'iade-iptal-politikasi') }}" class="hover:text-orange-500 transition-colors">İade & İptal</a>
            </div>
        </div>
    </footer>

</body>
</html>
