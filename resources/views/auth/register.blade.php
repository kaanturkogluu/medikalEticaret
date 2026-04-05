<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye Ol | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
        .btn-primary { background: #f27a1a; transition: all 0.2s; }
        .btn-primary:hover { background: #e67216; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(242,122,26,0.3); }
        .input-field { transition: all 0.2s; }
        .input-field:focus { border-color: #f27a1a; box-shadow: 0 0 0 4px rgba(242,122,26,0.1); }
        .strength-bar div { transition: width 0.3s ease; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="py-5 border-b bg-white/80 backdrop-blur-sm flex justify-center sticky top-0 z-10">
        <a href="/" class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase">
            {{ config('app.name') }}
        </a>
    </header>

    <main class="flex-grow flex items-center justify-center p-6 py-12">
        <div class="w-full max-w-[480px]">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Üye Ol</h1>
                <p class="text-slate-500 mt-2 text-sm">Hızlıca hesap oluşturun ve alışverişe başlayın.</p>
            </div>

            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl text-sm font-semibold text-green-700 flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-sm font-semibold text-red-700">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <!-- Form Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">
                <form action="{{ route('register') }}" method="POST" class="space-y-5" id="registerForm">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest" for="name">Ad Soyad</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            required
                            value="{{ old('name') }}"
                            class="input-field w-full border-2 border-slate-100 rounded-xl px-4 py-3.5 text-sm font-medium text-slate-900 outline-none focus:border-orange-400 bg-slate-50/50"
                            placeholder="Adınız ve soyadınız"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest" for="email">E-posta Adresi</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            required
                            value="{{ old('email') }}"
                            class="input-field w-full border-2 border-slate-100 rounded-xl px-4 py-3.5 text-sm font-medium text-slate-900 outline-none focus:border-orange-400 bg-slate-50/50"
                            placeholder="ornek@email.com"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest" for="password">Şifre</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                class="input-field w-full border-2 border-slate-100 rounded-xl px-4 py-3.5 pr-12 text-sm font-medium text-slate-900 outline-none focus:border-orange-400 bg-slate-50/50"
                                placeholder="En az 8 karakter"
                                oninput="checkStrength(this.value)"
                            >
                            <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <!-- Password strength bar -->
                        <div class="strength-bar h-1.5 rounded-full bg-slate-100 mt-2 overflow-hidden">
                            <div id="strengthBar" class="h-full rounded-full bg-slate-200" style="width:0%"></div>
                        </div>
                        <p id="strengthText" class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-2 uppercase tracking-widest" for="password_confirmation">Şifre Tekrar</label>
                        <div class="relative">
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                required
                                class="input-field w-full border-2 border-slate-100 rounded-xl px-4 py-3.5 pr-12 text-sm font-medium text-slate-900 outline-none focus:border-orange-400 bg-slate-50/50"
                                placeholder="Şifrenizi tekrar girin"
                            >
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 pt-2">
                        <input type="checkbox" id="terms" name="terms" required class="mt-0.5 w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                        <label for="terms" class="text-xs text-slate-500 leading-relaxed">
                            <a href="{{ route('page.show', 'kullanim-kosullari') }}" target="_blank" class="font-bold text-orange-500 hover:underline">Kullanım Koşulları</a>'nı ve
                            <a href="{{ route('page.show', 'gizlilik-politikasi') }}" target="_blank" class="font-bold text-orange-500 hover:underline">Gizlilik Politikası</a>'nı okudum, kabul ediyorum.
                        </label>
                    </div>

                    <button type="submit" class="btn-primary w-full py-4 rounded-xl text-white font-black text-sm uppercase tracking-widest shadow-lg">
                        Üye Ol — Kodu E-postama Gönder
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500">Zaten hesabınız var mı?
                        <a href="{{ route('login') }}" class="font-bold text-orange-500 hover:underline">Giriş Yapın</a>
                    </p>
                </div>
            </div>

            <!-- Security note -->
            <p class="text-center text-xs text-slate-400 mt-6 leading-relaxed">
                🔒 Bilgileriniz SSL ile şifrelenerek güvende tutulur.
            </p>
        </div>
    </main>

    <footer class="py-6 bg-white border-t text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </footer>

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
                { width: '0%', color: 'bg-slate-200', label: '' },
                { width: '25%', color: 'bg-red-400', label: 'Çok Zayıf' },
                { width: '50%', color: 'bg-yellow-400', label: 'Zayıf' },
                { width: '75%', color: 'bg-blue-400', label: 'İyi' },
                { width: '100%', color: 'bg-green-500', label: 'Güçlü' },
            ];

            bar.style.width = levels[score].width;
            bar.className = `h-full rounded-full ${levels[score].color}`;
            text.textContent = levels[score].label;
        }
    </script>
</body>
</html>
