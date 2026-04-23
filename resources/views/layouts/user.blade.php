@extends('layouts.app')

@section('styles')
<style>
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
    
    .user-content-card {
        background: white;
        border-radius: 1.5rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        padding: 2rem;
    }
</style>
@yield('user_styles')
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-12 w-full">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR --}}
        <aside class="w-full lg:w-72 flex-shrink-0">
            {{-- User Info Card --}}
            <div class="sidebar-card p-5 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center border-2 border-white shadow-sm flex-shrink-0">
                        <span class="text-2xl font-black text-orange-500">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="font-black text-sm text-slate-900 truncate uppercase italic tracking-tighter">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-slate-400 truncate opacity-70">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                @unless(auth()->user()->email_verified_at)
                <a href="{{ route('verify.send') }}" class="mt-4 block text-center text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 border border-orange-100 rounded-xl py-3 hover:bg-orange-100 transition-all">
                    <i class="fas fa-exclamation-circle mr-1"></i> E-postanı Doğrula
                </a>
                @endunless
            </div>

            {{-- Navigation --}}
            <nav class="sidebar-card p-4 space-y-6">
                {{-- Siparişlerim Group --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-3 opacity-50">Siparişlerim</p>
                    <a href="{{ route('user.orders') }}" class="nav-link {{ Request::is('hesabim/siparislerim*') ? 'active' : '' }}">
                        <i class="fas fa-box-open text-orange-400"></i> Tüm Siparişlerim
                    </a>
                </div>

                {{-- Favoriler Group --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-3 opacity-50">Alışveriş Listem</p>
                    <a href="{{ route('favorites') }}" class="nav-link {{ Request::is('favorites') ? 'active' : '' }}">
                        <i class="fas fa-heart text-red-400"></i> Favorilerim
                    </a>
                </div>

                {{-- Hesabım Group --}}
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-3 opacity-50">Hesabım</p>
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
                
                {{-- Destek Group --}}
                <div class="space-y-3">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4 mb-2 opacity-50">Yardım & Destek</p>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', \App\Models\Setting::getValue('contact_whatsapp', '905300000000')) }}" target="_blank" class="flex items-center gap-3 px-4 py-3 bg-green-50 text-green-700 rounded-2xl text-[11px] font-black uppercase italic tracking-tighter hover:bg-green-100 transition-all border border-green-100/50 shadow-sm shadow-green-100/50">
                        <i class="fab fa-whatsapp text-lg"></i>
                        <span>WhatsApp Destek</span>
                    </a>
                </div>

                <div class="border-t border-slate-50 pt-4 px-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full text-left nav-link text-red-500 hover:bg-red-50">
                            <i class="fas fa-power-off"></i> Çıkış Yap
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 min-w-0">
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

            <div class="user-main-content">
                @yield('user_content')
            </div>
        </div>

    </div>
</div>
@endsection

