@extends('layouts.app')

@section('title', 'Yeni Şifre Oluştur')

@section('content')
<main class="flex-grow flex items-center justify-center p-6 py-20 bg-gray-50/50">
    <div class="w-full max-w-[450px] bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 md:p-12">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-2xl mb-4">
                <i class="fas fa-lock-open text-orange-500 text-2xl"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 italic uppercase italic tracking-tighter">Yeni Şifre</h1>
            <p class="text-slate-500 mt-2 text-sm font-medium">Lütfen yeni şifrenizi belirleyin.</p>
        </div>

        <form action="{{ route('password.update.submit') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1" for="email">E-POSTA ADRESİ</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ $email ?? old('email') }}"
                    required 
                    class="w-full border-2 {{ $errors->has('email') ? 'border-red-500' : 'border-slate-100' }} rounded-xl px-4 py-4 focus:border-orange-500 outline-none transition-all text-sm font-bold bg-slate-50/50"
                    placeholder="ornek@email.com"
                >
                @error('email')
                    <p class="text-red-500 text-[10px] mt-2 font-black uppercase tracking-widest">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1" for="password">YENİ ŞİFRE</label>
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

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1" for="password_confirmation">ŞİFRE TEKRARI</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    id="password_confirmation" 
                    required 
                    class="w-full border-2 border-slate-100 rounded-xl px-4 py-4 focus:border-orange-500 outline-none transition-all text-sm font-bold bg-slate-50/50"
                    placeholder="••••••••"
                >
            </div>

            <button type="submit" class="w-full bg-[#f27a1a] py-4 rounded-2xl text-white font-black text-sm uppercase italic tracking-widest shadow-lg shadow-orange-500/20 active:scale-[0.98] transition-transform">
                Şifreyi Güncelle
            </button>
        </form>
    </div>
</main>
@endsection
