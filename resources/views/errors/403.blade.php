<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erişim Engellendi | umutMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0f172a; }
        .bg-glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .gradient-text {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out infinite;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[#0f172a]">
    <div class="max-w-2xl w-full text-center">
        <div class="relative mb-12">
            <h1 class="text-[180px] font-black tracking-tighter leading-none text-white opacity-5">403</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-red-900/30 rounded-full flex items-center justify-center border-4 border-red-500/20 animate-pulse">
                    <i class="fas fa-lock text-5xl text-red-500"></i>
                </div>
            </div>
        </div>

        <h2 class="text-4xl font-black italic tracking-tighter text-white uppercase mb-4">
            Erişim <span class="gradient-text">Engellendi!</span>
        </h2>
        <p class="text-indigo-200/50 text-lg mb-10 max-w-sm mx-auto leading-relaxed font-light">
            Bu alanda yetkiniz bulunmamaktadır. Lütfen yönetici ile iletişime geçin.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" class="px-8 py-4 bg-white text-slate-900 rounded-2xl font-black italic uppercase tracking-tighter hover:bg-red-500 hover:text-white transition-all shadow-lg active:scale-95">
                Güvenli Bölgeye Dön
            </a>
            <a href="{{ route('login') }}" class="px-8 py-4 border-2 border-slate-700 text-indigo-200 rounded-2xl font-black italic uppercase tracking-tighter hover:border-red-500 hover:text-red-500 transition-all active:scale-95">
                Giriş Yap
            </a>
        </div>

        <div class="mt-16 flex items-center justify-center gap-8 opacity-20">
            <span class="text-xs font-black italic uppercase tracking-widest text-slate-400">umut<span class="text-white">Med</span> Security</span>
        </div>
    </div>
</body>
</html>
