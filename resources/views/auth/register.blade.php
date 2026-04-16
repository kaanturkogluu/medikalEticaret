@extends('layouts.app')

@section('title', 'Üye Ol')

@section('styles')
<style>
    .btn-primary { background: #f27a1a; transition: all 0.2s; }
    .btn-primary:hover { background: #e67216; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(242,122,26,0.3); }
    .input-field { transition: all 0.2s; }
    .input-field:focus { border-color: #f27a1a; box-shadow: 0 0 0 4px rgba(242,122,26,0.1); }
    .strength-bar div { transition: width 0.3s ease; }
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #f27a1a; }
</style>
@endsection

@section('content')
<div x-data="{ 
    modal: {
        show: false,
        title: '',
        content: '',
        loading: false,
        async open(slug) {
            this.show = true;
            this.loading = true;
            this.title = 'Yükleniyor...';
            this.content = '<div class=\'flex justify-center p-20\'><i class=\'fas fa-circle-notch fa-spin text-4xl text-orange-500\'></i></div>';
            
            try {
                const res = await fetch(`/sayfa/${slug}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                this.title = data.title;
                this.content = data.content;
            } catch (e) {
                this.title = 'Hata';
                this.content = 'İçerik yüklenirken bir hata oluştu.';
            } finally {
                this.loading = false;
            }
        }
    }
}">
    <main class="flex-grow flex items-center justify-center p-6 py-20 bg-gray-50/50">
        <div class="w-full max-w-[480px]">

            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 rounded-[2rem] mb-6 shadow-lg shadow-orange-500/10">
                    <i class="fas fa-user-plus text-orange-500 text-3xl"></i>
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-900 italic uppercase italic tracking-tighter">Üye Ol</h1>
                <p class="text-slate-500 mt-2 text-sm font-medium">umutMed dünyasına hoş geldiniz.</p>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-sm font-bold text-green-700 flex items-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-5 bg-red-50 border border-red-100 rounded-3xl text-[11px] font-bold text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p>{{ $error }}</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/60 border border-slate-100 p-8 md:p-12">
                <form action="{{ route('register') }}" method="POST" class="space-y-6" id="registerForm">
                    @csrf
                    
                    <!-- Honeypot -->
                    <div class="hidden" aria-hidden="true">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 mb-1 uppercase tracking-widest px-1" for="name">Ad Soyad</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            required
                            value="{{ old('name') }}"
                            class="input-field w-full border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 outline-none focus:border-orange-500 bg-slate-50/50 transition-all"
                            placeholder="Adınız Soyadınız"
                        >
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 mb-1 uppercase tracking-widest px-1" for="email">E-posta Adresi</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            required
                            value="{{ old('email') }}"
                            class="input-field w-full border-2 border-slate-100 rounded-2xl px-5 py-4 text-sm font-bold text-slate-900 outline-none focus:border-orange-500 bg-slate-50/50 transition-all"
                            placeholder="ornek@email.com"
                        >
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 mb-1 uppercase tracking-widest px-1" for="password">Şifre</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                class="input-field w-full border-2 border-slate-100 rounded-2xl px-5 py-4 pr-14 text-sm font-bold text-slate-900 outline-none focus:border-orange-500 bg-slate-50/50 transition-all"
                                placeholder="••••••••"
                                oninput="checkStrength(this.value)"
                            >
                            <button type="button" onclick="togglePassword('password')" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors">
                                <i class="fas fa-eye text-lg"></i>
                            </button>
                        </div>
                        <!-- Strength bar -->
                        <div class="strength-bar h-1.5 rounded-full bg-slate-100 mt-3 overflow-hidden">
                            <div id="strengthBar" class="h-full rounded-full bg-slate-200" style="width:0%"></div>
                        </div>
                        <p id="strengthText" class="text-[9px] font-black text-slate-400 mt-1.5 uppercase tracking-widest text-right"></p>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-400 mb-1 uppercase tracking-widest px-1" for="password_confirmation">Şifre Tekrar</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                class="input-field w-full border-2 border-slate-100 rounded-2xl px-5 py-4 pr-14 text-sm font-bold text-slate-900 outline-none focus:border-orange-500 bg-slate-50/50 transition-all"
                                placeholder="••••••••"
                            >
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors">
                                <i class="fas fa-eye text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 pt-2 group">
                        <input type="checkbox" id="terms" name="terms" required class="mt-1 w-5 h-5 rounded-lg border-slate-200 text-orange-500 focus:ring-orange-500 transition-all">
                        <label for="terms" class="text-[11px] font-bold text-slate-500 leading-relaxed group-hover:text-slate-700 transition-colors">
                            <a href="javascript:void(0)" @click="modal.open('kullanim-kosullari')" class="font-black text-orange-500 hover:underline">Kullanım Koşulları</a> ve 
                            <a href="javascript:void(0)" @click="modal.open('gizlilik-politikasi')" class="font-black text-orange-500 hover:underline">Gizlilik Politikası</a> metinlerini okudum, onaylıyorum.
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-4.5 rounded-2xl text-white font-black text-sm uppercase italic tracking-widest shadow-xl shadow-orange-500/20 active:scale-[0.98] transition-all">
                        Üye Ol & Devam Et
                    </button>
                </form>

                <div class="mt-10 text-center">
                    <p class="text-xs text-slate-500 font-medium">Zaten bir hesabınız var mı?
                        <a href="{{ route('login') }}" class="font-black text-orange-500 hover:text-orange-600 uppercase italic tracking-tighter">Giriş Yapın</a>
                    </p>
                </div>
            </div>

            <!-- Security info -->
            <div class="mt-10 flex items-center justify-center gap-6 opacity-40 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-shield-halved text-slate-900"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">SSL Secure</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-lock text-slate-900"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Safe Payment</span>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Component -->
    <div x-show="modal.show" 
         x-cloak
         class="fixed inset-0 z-[2000] overflow-y-auto" 
         role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="modal.show" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0" 
                 x-transition:enter-end="opacity-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100" 
                 x-transition:leave-end="opacity-0" 
                 class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" 
                 @click="modal.show = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="modal.show" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100">
                
                <div class="bg-white px-10 pt-10 pb-6 flex justify-between items-center sticky top-0 z-10">
                    <h3 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase" x-text="modal.title"></h3>
                    <button @click="modal.show = false" class="text-slate-400 hover:text-orange-500 w-12 h-12 rounded-2xl flex items-center justify-center transition-all bg-slate-50 hover:bg-orange-50">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="px-10 py-10 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="prose prose-slate max-w-none text-slate-600 text-sm font-medium leading-relaxed italic" x-html="modal.content"></div>
                </div>

                <div class="bg-slate-50 px-10 py-8 flex justify-end">
                    <button @click="modal.show = false" class="px-10 py-4 bg-slate-900 text-white rounded-2xl text-xs font-black uppercase italic tracking-tighter hover:bg-orange-600 transition-all shadow-xl active:scale-95">
                        Kapat
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function checkStrength(password) {
        const bar = document.getElementById('strengthBar');
        const text = document.getElementById('strengthText');
        let score = 0;
        if (password.length >= 8) score++;
        if (password.match(/[A-Z]/)) score++;
        if (password.match(/[0-9]/)) score++;
        if (password.match(/[^a-zA-Z0-9]/)) score++;

        const levels = [
            { width: '5%', color: 'bg-slate-200', label: 'Geçersiz' },
            { width: '25%', color: 'bg-red-400', label: 'Çok Zayıf' },
            { width: '50%', color: 'bg-yellow-400', label: 'Zayıf' },
            { width: '75%', color: 'bg-blue-400', label: 'İyi' },
            { width: '100%', color: 'bg-green-500', label: 'Güçlü' },
        ];

        bar.style.width = levels[score].width;
        bar.className = `h-full rounded-full ${levels[score].color} transition-all duration-500`;
        text.textContent = levels[score].label;
    }
</script>
@endsection

