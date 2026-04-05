<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oturum Süresi Doldu | umutMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #fafafa; }
        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .animate-reverse-spin {
            animation: reverse-spin 1.5s linear infinite;
        }
        @keyframes reverse-spin {
            form { transform: rotate(0deg); }
            to { transform: rotate(-360deg); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[#f8fafc]">
    <div class="max-w-2xl w-full text-center">
        <div class="relative mb-12">
            <h1 class="text-[180px] font-black tracking-tighter leading-none text-slate-200/50">419</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-32 h-32 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-history text-5xl text-indigo-500 animate-reverse-spin"></i>
                </div>
            </div>
        </div>

        <h2 class="text-4xl font-black italic tracking-tighter text-slate-900 uppercase mb-4">
            Oturum Süresi <span class="gradient-text">Doldu!</span>
        </h2>
        <p class="text-slate-500 text-lg mb-10 max-w-sm mx-auto leading-relaxed">
            Güvenlik nedeniyle sayfanızın süresi doldu. Lütfen sayfayı yenileyip tekrar deneyin.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button onclick="window.location.reload()" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black italic uppercase tracking-tighter hover:bg-slate-900 transition-all shadow-xl shadow-indigo-200 active:scale-95">
                Sayfayı Yenile
            </button>
            <a href="/" class="px-8 py-4 border-2 border-slate-200 text-slate-600 rounded-2xl font-black italic uppercase tracking-tighter hover:border-indigo-500 hover:text-indigo-500 transition-all active:scale-95">
                Ana Sayfaya Dön
            </a>
        </div>

        <div class="mt-16 flex items-center justify-center gap-8 opacity-40">
             <span class="text-xs font-black italic uppercase tracking-widest text-slate-400">umut<span class="text-indigo-500">Med</span> Platform Security</span>
        </div>
    </div>
</body>
</html>
