@extends('layouts.app')

@section('title', 'Giriş Yap')

@section('styles')
<style>
    .btn-trendyol { background-color: #f27a1a; transition: all 0.2s; }
    .btn-trendyol:hover { background-color: #e67216; }
    .google-btn { border: 1px solid #e6e6e6; transition: all 0.2s; }
    .google-btn:hover { background-color: #f1f1f1; }
</style>
@endsection

@section('content')
<main class="flex-grow flex items-center justify-center p-6 py-20 bg-gray-50/50">
    <div class="w-full max-w-[450px] bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-12">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-2xl mb-4">
                <i class="fas fa-user-lock text-orange-500 text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 italic uppercase italic tracking-tighter">Giriş Yap</h1>
            <p class="text-slate-500 mt-2 text-sm font-medium">Hesabınıza güvenli bir şekilde erişin.</p>
        </div>

        <!-- Social Login Part -->
        <div class="space-y-4 mb-8">
            <a href="{{ route('auth.google') }}" class="w-full google-btn rounded-xl py-4 px-4 flex items-center justify-center gap-3 font-bold text-slate-700 text-sm italic tracking-tighter uppercase">
                <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google" class="w-5 h-5">
                Google ile Giriş Yap
            </a>
            <div class="relative flex items-center py-5">
                <div class="flex-grow border-t border-slate-100"></div>
                <span class="flex-shrink mx-4 text-slate-300 text-[10px] font-black uppercase tracking-[0.2em]">veya</span>
                <div class="flex-grow border-t border-slate-100"></div>
            </div>
        </div>

        <form action="{{ route('login.authenticate') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1" for="email">E-POSTA ADRESİ</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}"
                    required 
                    class="w-full border-2 {{ $errors->has('email') ? 'border-red-500' : 'border-slate-100' }} rounded-xl px-4 py-4 focus:border-orange-500 outline-none transition-all text-sm font-bold bg-slate-50/50"
                    placeholder="ornek@email.com"
                >
                @error('email')
                    <p class="text-red-500 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1" for="password">ŞİFRE</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    class="w-full border-2 {{ $errors->has('password') ? 'border-red-500' : 'border-slate-100' }} rounded-xl px-4 py-4 focus:border-orange-500 outline-none transition-all text-sm font-bold bg-slate-50/50"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="text-red-500 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between px-1">
                <label class="flex items-center cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500 transition-all">
                    <span class="ml-2 text-[11px] font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Beni Hatırla</span>
                </label>
                <a href="#" class="text-[11px] font-black text-orange-500 hover:text-orange-600 transition-colors uppercase italic tracking-tighter">Şifremi Unuttum</a>
            </div>

            <button type="submit" class="w-full btn-trendyol py-4 rounded-2xl text-white font-black text-sm uppercase italic tracking-widest shadow-lg shadow-orange-500/20 active:scale-[0.98] transition-transform">
                Giriş Yap
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-xs text-slate-500 font-medium">Hesabınız yok mu? <a href="{{ route('register') }}" class="font-black text-orange-500 hover:text-orange-600 transition-colors uppercase italic tracking-tighter">Hemen Üye Olun</a></p>
        </div>
    </div>
</main>
@endsection
