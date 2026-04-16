@extends('layouts.user')

@section('title', 'Kullanıcı Bilgilerim')

@section('user_content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Profile Info --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="font-black text-gray-900">Üyelik Bilgilerim</h2>
        </div>
        <form action="{{ route('user.profile.update') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Ad Soyad</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition-all">
                @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">E-posta</label>
                <input type="email" value="{{ $user->email }}" disabled
                    class="w-full px-4 py-3 border border-gray-100 bg-gray-50 rounded-xl text-sm font-medium text-gray-400 cursor-not-allowed">
                <p class="text-[10px] text-gray-400 mt-1">E-posta adresi değiştirilemez.</p>
            </div>
            <button type="submit" class="w-full py-3.5 bg-orange-500 text-white text-sm font-black rounded-xl hover:bg-orange-600 transition-all transform hover:scale-[1.01] active:scale-[0.99] shadow-lg shadow-orange-500/20">
                Güncelle
            </button>
        </form>
    </div>

    {{-- Password Update --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50">
            <h2 class="font-black text-gray-900">Şifre Güncelleme</h2>
        </div>
        <form action="{{ route('user.password.update') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Şu Anki Şifre</label>
                <div class="relative">
                    <input type="password" name="current_password" id="cur_pw" required
                        class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition-all">
                    <button type="button" onclick="toggle('cur_pw')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Yeni Şifre</label>
                <div class="relative">
                    <input type="password" name="password" id="new_pw" required
                        class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition-all">
                    <button type="button" onclick="toggle('new_pw')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">Şifreniz en az 8 karakter olmalıdır.</p>
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Yeni Şifre (Tekrar)</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="new_pw2" required
                        class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm font-medium text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400/30 focus:border-orange-400 transition-all">
                    <button type="button" onclick="toggle('new_pw2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-gray-500">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="w-full py-3.5 bg-slate-900 text-white text-sm font-black rounded-xl hover:bg-slate-800 transition-all transform hover:scale-[1.01] active:scale-[0.99]">
                Şifremi Güncelle
            </button>
        </form>
    </div>

    {{-- Account Info --}}
    <div class="lg:col-span-2 bg-orange-50 border border-orange-100 rounded-2xl p-5 flex items-start gap-4">
        <i class="fas fa-shield-halved text-orange-400 text-2xl mt-0.5"></i>
        <div>
            <p class="text-sm font-bold text-orange-800">İki Adımlı Doğrulama</p>
            <p class="text-xs text-orange-600 mt-1 leading-relaxed">Hesabınızı daha güvenli hale getirmek için iki adımlı doğrulama özelliğini yakında aktive edebileceksiniz.</p>
        </div>
    </div>
</div>

<script>
function toggle(id) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
