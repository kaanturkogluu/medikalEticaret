@extends('layouts.app')

@section('title', 'Şifremi Unuttum')

@section('content')
<main class="flex-grow flex items-center justify-center p-6 py-20 bg-gray-50/50">
    <div class="w-full max-w-[450px] bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-12">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-2xl mb-4">
                <i class="fas fa-key text-orange-500 text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 italic uppercase italic tracking-tighter">Şifremi Unuttum</h1>
            <p class="text-slate-500 mt-2 text-sm font-medium">E-posta adresinizi girerek şifre sıfırlama bağlantısı isteyin.</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-100 rounded-2xl text-[11px] font-black uppercase tracking-widest text-green-600 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6" onsubmit="this.submitBtn.disabled = true; this.submitBtn.innerHTML = '<i class=\'fas fa-circle-notch fa-spin mr-2\'></i> Gönderiliyor...';">
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

            <button type="submit" name="submitBtn" class="w-full bg-[#f27a1a] py-4 rounded-2xl text-white font-black text-sm uppercase italic tracking-widest shadow-lg shadow-orange-500/20 active:scale-[0.98] transition-transform disabled:opacity-70 disabled:cursor-not-allowed">
                Bağlantı Gönder
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-xs text-slate-500 font-medium">Şifrenizi hatırladınız mı? <a href="{{ route('login') }}" class="font-black text-orange-500 hover:text-orange-600 transition-colors uppercase italic tracking-tighter">Giriş Yapın</a></p>
        </div>
    </div>
</main>
@endsection
