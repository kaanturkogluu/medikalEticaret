@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Netgsm SMS Entegrasyonu</h2>
                    @if($hasCredentials)
                        <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1"><i class="fas fa-check-circle"></i> Aktif</span>
                    @else
                        <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest flex items-center gap-1"><i class="fas fa-times-circle"></i> Eksik Kurulum</span>
                    @endif
                </div>
                <p class="text-sm text-slate-500 mt-1">Netgsm üzerinden SMS gönderim durumunu kontrol edin ve test mesajı gönderin.</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.netgsm.history') }}" class="py-3 px-6 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-sm font-bold shadow-sm transition-all flex items-center justify-center gap-2 group">
                    <i class="fas fa-history text-slate-400 group-hover:text-slate-600 transition-colors"></i>
                    <span>Geçmiş SMS'ler</span>
                </a>
                <a href="{{ route('admin.netgsm.bulk') }}" class="py-3 px-6 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-black tracking-wide shadow-lg shadow-brand-500/20 transition-all flex items-center justify-center gap-2 group">
                    <i class="fas fa-users group-hover:scale-110 transition-transform"></i>
                    <span>Toplu SMS Gönder</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-600 font-bold text-sm shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-600 font-bold text-sm shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-600 font-bold text-sm shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="max-w-2xl">
            <!-- Test Formu -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                <h3 class="text-xl font-black text-slate-800 tracking-tight mb-6">Test SMS Gönder</h3>
                
                <form action="{{ route('admin.netgsm.test') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Telefon Numarası (Başında 0 ile)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-slate-400"></i>
                            </div>
                            <input type="text" name="phone" placeholder="05xxxxxxxxx" required
                                class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all placeholder:text-slate-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Mesaj İçeriği</label>
                        <div class="relative">
                            <textarea name="message" rows="3" required placeholder="Test mesajı içeriği..."
                                class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-4 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-black tracking-wide shadow-lg shadow-brand-500/20 transition-all flex items-center justify-center gap-2 group">
                        <span>Gönder</span>
                        <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
