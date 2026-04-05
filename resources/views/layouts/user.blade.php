<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hesabım') — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f3f3f3; }
        .nav-link { @apply flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium text-gray-600 hover:bg-orange-50 hover:text-orange-600 transition-all; }
        .nav-link.active { @apply bg-orange-50 text-orange-600 font-semibold; }
        .nav-link .icon { @apply w-5 h-5 flex-shrink-0; }
        .badge { @apply ml-auto text-xs bg-orange-500 text-white rounded-full w-5 h-5 flex items-center justify-center font-bold; }
    </style>
</head>
<body class="min-h-screen">

    {{-- Top Navigation --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 flex items-center justify-between h-16">
            <a href="{{ route('home') }}" class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase">
                {{ config('app.name') }}
            </a>
            <div class="flex items-center gap-6 text-sm">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-2">
                    <i class="fas fa-shopping-bag"></i> Alışverişe Devam Et
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-500 hover:text-red-500 transition-colors flex items-center gap-2">
                        <i class="fas fa-sign-out-alt"></i> Çıkış
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex gap-6">

            {{-- SIDEBAR --}}
            <aside class="w-64 flex-shrink-0">
                {{-- User Info Card --}}
                <div class="bg-white rounded-2xl p-5 mb-4 border border-gray-100 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <span class="text-xl font-black text-orange-500">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    @unless(auth()->user()->email_verified_at)
                    <a href="{{ route('verify.form', ['email' => auth()->user()->email]) }}" class="mt-3 block text-center text-xs font-bold text-orange-600 bg-orange-50 border border-orange-200 rounded-lg py-2 hover:bg-orange-100 transition-all">
                        <i class="fas fa-exclamation-circle mr-1"></i> E-postanı Doğrula
                    </a>
                    @endunless
                </div>

                {{-- Navigation --}}
                <nav class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Siparişlerim --}}
                    <div class="p-3 border-b border-gray-50">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 mb-2">Siparişlerim</p>
                        <a href="{{ route('user.orders') }}" class="nav-link {{ Request::is('hesabim/siparislerim*') ? 'active' : '' }}">
                            <i class="fas fa-box-open icon text-orange-400"></i> Tüm Siparişlerim
                        </a>
                    </div>

                    {{-- Favoriler --}}
                    <div class="p-3 border-b border-gray-50">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 mb-2">Favorilerim</p>
                        <a href="{{ route('favorites') }}" class="nav-link">
                            <i class="fas fa-heart icon text-red-400"></i> Favori Listesi
                        </a>
                    </div>

                    {{-- Hesabım --}}
                    <div class="p-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-2 mb-2">Hesabım</p>
                        <a href="{{ route('user.profile') }}" class="nav-link {{ Request::is('hesabim/bilgilerim') ? 'active' : '' }}">
                            <i class="fas fa-user icon text-blue-400"></i> Kullanıcı Bilgilerim
                        </a>
                        <a href="{{ route('user.addresses') }}" class="nav-link {{ Request::is('hesabim/adreslerim') ? 'active' : '' }}">
                            <i class="fas fa-map-marker-alt icon text-green-400"></i> Adres Bilgilerim
                        </a>
                        <a href="{{ route('user.dashboard') }}" class="nav-link {{ Request::is('hesabim') && !Request::is('hesabim/*') ? 'active' : '' }}">
                            <i class="fas fa-tachometer-alt icon text-purple-400"></i> Özet Sayfam
                        </a>
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
