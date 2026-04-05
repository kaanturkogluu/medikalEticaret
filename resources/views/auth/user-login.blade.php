<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap | umutMed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #fafafa; }
        .btn-trendyol { background-color: #f27a1a; transition: all 0.2s; }
        .btn-trendyol:hover { background-color: #e67216; }
        .google-btn { border: 1px solid #e6e6e6; transition: all 0.2s; }
        .google-btn:hover { background-color: #f1f1f1; }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    <!-- Mini Header -->
    <header class="py-6 border-b bg-white flex justify-center">
        <a href="/" class="text-3xl font-black italic tracking-tighter text-slate-900 uppercase">
            umut<span class="text-[#f27a1a]">Med</span>
        </a>
    </header>

    <main class="flex-grow flex items-center justify-center p-6">
        <div class="w-full max-w-[450px] bg-white rounded-lg shadow-sm border p-8 md:p-12">
            <h1 class="text-2xl font-bold text-gray-800 text-center mb-2">Giriş Yap</h1>
            <p class="text-gray-500 text-center text-sm mb-10">Kişisel hesabınıza erişmek için bilgilerinizi girin.</p>

            <!-- Social Login Part (Requested: Google Button) -->
            <div class="space-y-4 mb-8">
                <a href="{{ route('auth.google') }}" class="w-full google-btn rounded-md py-3 px-4 flex items-center justify-center gap-3 font-medium text-gray-700">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google" class="w-5 h-5">
                    Google ile Giriş Yap
                </a>
                <div class="relative flex items-center py-5">
                    <div class="flex-grow border-t border-gray-100"></div>
                    <span class="flex-shrink mx-4 text-gray-300 text-xs font-bold uppercase tracking-widest">veya</span>
                    <div class="flex-grow border-t border-gray-100"></div>
                </div>
            </div>

            <form action="{{ route('login.authenticate') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="email">E-Posta</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        class="w-full border-2 border-gray-100 rounded-md px-4 py-3 focus:border-[#f27a1a] outline-none transition-colors"
                        placeholder="E-posta adresiniz"
                    >
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="password">Şifre</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        class="w-full border-2 border-gray-100 rounded-md px-4 py-3 focus:border-[#f27a1a] outline-none transition-colors"
                        placeholder="Şifreniz"
                    >
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#f27a1a] focus:ring-[#f27a1a]">
                        <span class="ml-2 text-sm text-gray-500">Beni Hatırla</span>
                    </label>
                    <a href="#" class="text-xs font-bold text-[#f27a1a] hover:underline">Şifremi Unuttum</a>
                </div>

                <button type="submit" class="w-full btn-trendyol py-4 rounded-md text-white font-bold text-lg shadow-sm">
                    Giriş Yap
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Hesabınız yok mu? <a href="{{ route('register') }}" class="font-bold text-[#f27a1a] hover:underline">Üye Olun</a></p>
            </div>
        </div>
    </main>

    <footer class="py-8 bg-gray-50 border-t flex flex-col items-center gap-4 text-xs text-gray-400 font-medium">
        <div class="flex gap-8">
            <a href="#" class="hover:underline">Yardım & Destek</a>
            <a href="#" class="hover:underline">Gizlilik Politikası</a>
            <a href="#" class="hover:underline">Çerez Ayarları</a>
        </div>
        <p>&copy; 2026 umutMed</p>
    </footer>
</body>
</html>
