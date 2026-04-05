<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sakin Olun! | umutMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }
        .dot-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 1; }
        }
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6">
    <div class="max-w-2xl w-full text-center">
        <div class="relative mb-12 animate-float">
            <h1 class="text-[180px] font-black tracking-tighter leading-none opacity-5 text-indigo-500">429</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-indigo-100 rounded-full flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 border-4 border-indigo-500/20 rounded-full border-t-indigo-600 animate-spin"></div>
                    <i class="fas fa-hourglass-half text-5xl text-indigo-600 group-hover:rotate-180 transition-transform duration-1000"></i>
                </div>
            </div>
        </div>

        <h2 class="text-4xl font-black italic tracking-tighter text-slate-900 uppercase mb-4">
            Biraz <span class="gradient-text">Sakin!</span>
        </h2>
        <p class="text-slate-500 text-lg mb-10 max-w-md mx-auto leading-relaxed italic">
            Çok hızlı ilerliyorsunuz! Sistemimizi yormamak adına kısa bir ara vermeniz gerekiyor. Lütfen bir süre sonra tekrar deneyin.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
            <div class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-indigo-100 rounded-2xl shadow-xl shadow-indigo-100/50">
               <span class="w-3 h-3 bg-indigo-500 rounded-full dot-pulse"></span>
               <span class="text-xs font-black italic text-indigo-900 tracking-widest uppercase">Gereksiz Trafik Tespit Edildi</span>
            </div>
        </div>

        <div class="mt-12">
            <a href="/" class="px-10 py-5 bg-slate-900 text-white rounded-3xl font-black italic uppercase tracking-tighter hover:bg-indigo-600 transition-all shadow-2xl hover:shadow-indigo-500/30 active:scale-95 group flex items-center justify-center gap-3 w-fit mx-auto">
                <i class="fas fa-home transition-transform group-hover:-translate-y-1"></i>
                Ana Sayfaya Dön
            </a>
        </div>

        <div class="mt-16 flex flex-col items-center gap-4 opacity-50">
            <span class="text-xs font-black italic uppercase tracking-[0.4em] text-slate-400">Gelişmiş <span class="text-indigo-600">Bot</span> Koruması Aktif</span>
            <span class="text-[10px] font-black text-slate-400 uppercase italic">umut<span class="text-orange-500">Med</span> Sistem Güvenliği</span>
        </div>
    </div>
</body>
</html>
