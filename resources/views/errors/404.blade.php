<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Bulunamadı | umutMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #f27a1a 0%, #ff9d4d 100%);
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
    </style>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6">
    <div class="max-w-2xl w-full text-center">
        <div class="relative mb-12 animate-float">
            <h1 class="text-[180px] font-black tracking-tighter leading-none opacity-5">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-5xl text-orange-500"></i>
                </div>
            </div>
        </div>

        <h2 class="text-4xl font-black italic tracking-tighter text-slate-900 uppercase mb-4">
            Aradığınız Sayfa <span class="gradient-text">Kayıp!</span>
        </h2>
        <p class="text-slate-500 text-lg mb-10 max-w-md mx-auto leading-relaxed">
            Görünüşe göre aradığınız ürün veya sayfa raflarımızdan kaldırılmış veya adresi değişmiş olabilir.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black italic uppercase tracking-tighter hover:bg-orange-500 transition-all shadow-lg hover:shadow-orange-500/20 active:scale-95">
                Ana Sayfaya Dön
            </a>
            <button onclick="window.history.back()" class="px-8 py-4 border-2 border-slate-200 text-slate-600 rounded-2xl font-black italic uppercase tracking-tighter hover:border-orange-500 hover:text-orange-500 transition-all active:scale-95">
                Geri Git
            </button>
        </div>

        <div class="mt-16 flex items-center justify-center gap-8 opacity-40">
            <span class="text-xs font-black italic uppercase tracking-widest text-slate-400">umut<span class="text-orange-500">Med</span></span>
        </div>
    </div>
</body>
</html>
