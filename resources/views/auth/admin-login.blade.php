<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yönetim Paneli | Giriş</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at top left, #1e1b4b 0%, #0f172a 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .input-glass:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
        }
        .btn-premium {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
            transition: all 0.3s ease;
        }
        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(99, 102, 241, 0.5);
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="flex items-center justify-center p-6 bg-[#0f172a]">
    <div class="w-full max-w-md">
        <!-- Logo/Header Area -->
        <div class="text-center mb-10 animate-float">
            <div class="inline-flex items-center justify-center w-20 h-20 mb-4 rounded-3xl bg-indigo-600/20 glass-card">
                <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight">Medikal E-Ticaret</h1>
            <p class="text-indigo-300/60 mt-2 font-light">Yönetim Paneline Erişin</p>
        </div>

        <div class="glass-card rounded-[2.5rem] p-10 relative overflow-hidden group">
            <!-- Decorative light -->
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-600/20 blur-3xl rounded-full"></div>
            
            <form action="{{ route('login.authenticate') }}" method="POST" class="space-y-6 relative z-10">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2 ml-1" for="email">E-Posta Adresi</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        required 
                        class="w-full input-glass rounded-2xl px-5 py-4 text-white placeholder-indigo-300/30 outline-none"
                        placeholder="test@example.com"
                        value="{{ old('email') }}"
                    >
                    @error('email')
                        <p class="text-rose-400 text-xs mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-indigo-200 mb-2 ml-1" for="password">Şifre</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        class="w-full input-glass rounded-2xl px-5 py-4 text-white placeholder-indigo-300/30 outline-none {{ $errors->has('password') ? 'border-rose-500 ring-4 ring-rose-500/10' : '' }}"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="text-rose-400 text-xs mt-2 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between px-1">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-indigo-500/20 bg-indigo-900/50 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-transparent outline-none">
                        <span class="ml-2 text-sm text-indigo-300/70 group-hover:text-indigo-200 transition-colors">Beni Hatırla</span>
                    </label>
                </div>

                <button type="submit" class="w-full btn-premium py-4 rounded-2xl text-white font-semibold text-lg hover:brightness-110 active:scale-[0.98]">
                    Giriş Yap
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-indigo-300/30 text-sm font-light">
            &copy; 2026 Medikal Marketplace. Tüm hakları saklıdır.
        </p>
    </div>
</body>
</html>
