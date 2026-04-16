<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteFavicon = \App\Models\Setting::getValue('site_favicon', asset('favicon.svg'));
    @endphp
    <link rel="icon" type="image/x-icon" href="{{ $siteFavicon }}">
    <link rel="shortcut icon" href="{{ $siteFavicon }}" type="image/x-icon">
    <title>E-posta Doğrulama | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }
        .btn-primary { background: #f27a1a; transition: all 0.2s; }
        .btn-primary:hover { background: #e67216; transform: translateY(-1px); box-shadow: 0 8px 20px rgba(242,122,26,0.3); }
        .code-input { font-family: 'Courier New', monospace; letter-spacing: 0.5em; font-size: 2rem; font-weight: 900; text-align: center; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <header class="py-5 border-b bg-white/80 backdrop-blur-sm flex justify-center sticky top-0 z-10">
        <a href="/" class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase">
            {{ config('app.name') }}
        </a>
    </header>

    <main class="flex-grow flex items-center justify-center p-6 py-12">
        <div class="w-full max-w-[440px]">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">E-posta Doğrulama</h1>
                <p class="text-slate-500 mt-2 text-sm">
                    <strong class="text-slate-700">{{ $email }}</strong> adresine gönderilen<br>
                    6 haneli kodu aşağıya girin.
                </p>
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

            <!-- Verification Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8">

                <!-- Timer -->
                <div class="flex items-center justify-center gap-2 bg-orange-50 rounded-2xl p-4 mb-6">
                    <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm font-bold text-orange-600">Kodun geçerlilik süresi: <span id="countdown" class="font-black">30:00</span></span>
                </div>

                <form action="{{ route('verify.submit') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-3 uppercase tracking-widest text-center">Doğrulama Kodu</label>
                        <input
                            type="text"
                            name="code"
                            id="code"
                            required
                            maxlength="6"
                            inputmode="numeric"
                            pattern="[0-9]{6}"
                            autocomplete="one-time-code"
                            class="code-input w-full border-2 border-slate-100 rounded-2xl px-4 py-5 text-slate-900 outline-none focus:border-orange-400 focus:ring-4 focus:ring-orange-400/10 bg-slate-50/50 transition-all"
                            placeholder="• • • • • •"
                        >
                    </div>

                    <button type="submit" class="btn-primary w-full py-4 rounded-xl text-white font-black text-sm uppercase tracking-widest shadow-lg">
                        Hesabımı Doğrula
                    </button>
                </form>

                <!-- Resend -->
                <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500 mb-3">Kod gelmedi mi?</p>
                    <form action="{{ route('verify.resend') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit" class="text-sm font-bold text-orange-500 hover:underline inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Yeni Kod Gönder
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">
                Yanlış e-posta mı?
                <a href="{{ route('register') }}" class="font-bold text-orange-500 hover:underline">Tekrar kayıt olun</a>
            </p>
        </div>
    </main>

    <footer class="py-6 bg-white border-t text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </footer>

    <script>
        // 30-minute countdown
        let seconds = 30 * 60;
        const el = document.getElementById('countdown');
        const interval = setInterval(() => {
            seconds--;
            const m = String(Math.floor(seconds / 60)).padStart(2, '0');
            const s = String(seconds % 60).padStart(2, '0');
            el.textContent = `${m}:${s}`;
            if (seconds <= 0) {
                clearInterval(interval);
                el.textContent = 'Süresi Doldu';
                el.classList.add('text-red-500');
            }
        }, 1000);

        // Auto-format: only digits
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    </script>
</body>
</html>
