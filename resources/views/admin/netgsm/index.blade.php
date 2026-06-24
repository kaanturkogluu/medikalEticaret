@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Netgsm SMS Entegrasyonu</h2>
                <p class="text-sm text-slate-500 mt-1">Netgsm üzerinden SMS gönderim durumunu kontrol edin ve test mesajı gönderin.</p>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Durum Kartı -->
            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm relative">
                <div class="absolute top-6 right-6">
                    @if($hasCredentials)
                        <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Aktif (Bilgiler Girilmiş)</span>
                    @else
                        <span class="bg-rose-100 text-rose-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Eksik Kurulum</span>
                    @endif
                </div>

                <div class="h-16 w-16 bg-blue-50 border-blue-100 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner border">
                    <i class="fas fa-sms text-blue-500"></i>
                </div>

                <h3 class="text-xl font-black text-slate-800 tracking-tight mb-1">Bağlantı Durumu</h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-8">Netgsm API Bilgileri</p>

                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Kurulum Durumu</p>
                        @if($hasCredentials)
                            <p class="text-xs font-black text-emerald-600">.env dosyasında bilgiler tanımlı.</p>
                        @else
                            <p class="text-xs font-black text-rose-600">Lütfen .env dosyasına NETGSM_USERCODE, NETGSM_PASSWORD ve NETGSM_HEADER bilgilerini ekleyin.</p>
                        @endif
                    </div>
                </div>
            </div>

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
