@extends('layouts.app')

@section('title', 'Siparişiniz Alındı')

@section('content')
<div class="bg-gray-50/50 min-h-screen py-20">
    <div class="ty-container max-w-4xl">
        <div class="bg-white rounded-[50px] p-12 md:p-20 shadow-2xl border border-gray-100 text-center relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-green-50 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-50"></div>

            <div class="relative z-10">
                <div class="w-24 h-24 bg-green-100 text-green-600 rounded-[35px] flex items-center justify-center mx-auto mb-10 shadow-lg shadow-green-100/50 transform hover:scale-110 transition-transform">
                    <i class="fas fa-check text-4xl"></i>
                </div>

                <h2 class="text-4xl font-black text-slate-900 uppercase italic tracking-tighter mb-4">Siparişiniz Alındı!</h2>
                <p class="text-gray-500 font-bold mb-12">Bizi tercih ettiğiniz için teşekkür ederiz.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16 px-4">
                    <div class="bg-gray-50 p-8 rounded-[35px] border border-gray-100 group hover:bg-slate-900 hover:text-white transition-all duration-500">
                        <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 group-hover:text-white/50 mb-2">Sipariş Numarası</p>
                        <p class="text-3xl font-black italic tracking-tighter">#{{ $order->id }}</p>
                    </div>
                    <div class="bg-gray-50 p-8 rounded-[35px] border border-gray-100 group hover:bg-slate-900 hover:text-white transition-all duration-500">
                        <p class="text-[10px] uppercase font-black tracking-widest text-gray-400 group-hover:text-white/50 mb-2">Toplam Tutar</p>
                        <p class="text-3xl font-black italic tracking-tighter">{{ number_format($order->total_price, 2) }} TL</p>
                    </div>
                </div>

                @if($order->payment_method === 'eft')
                <div class="bg-amber-50 rounded-[40px] p-10 md:p-12 border border-amber-100 text-left relative overflow-hidden mb-12">
                    <div class="absolute top-0 right-0 bg-amber-500 text-white px-6 py-2 rounded-bl-3xl font-black italic text-xs uppercase tracking-tighter">
                        Ödeme Bekleniyor
                    </div>
                    
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center">
                            <i class="fas fa-university text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-amber-900 uppercase italic tracking-tighter leading-none">Havale/EFT Talimatları</h3>
                    </div>

                    <div class="space-y-6">
                        <p class="text-sm font-bold text-amber-800 leading-relaxed">
                            Siparişinizin onaylanması için lütfen aşağıdaki banka hesabına <span class="underline decoration-2">sipariş tutarını</span> gönderiniz.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white/50 p-8 rounded-3xl border border-amber-200">
                            <div>
                                <p class="text-[10px] font-bold text-amber-800/50 uppercase mb-1">Banka Adı</p>
                                <p class="text-sm font-black text-amber-900">{{ $bankDetails['bank_name'] }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-amber-800/50 uppercase mb-1">Hesap Sahibi</p>
                                <p class="text-sm font-black text-amber-900">{{ $bankDetails['bank_account_holder'] }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-[10px] font-bold text-amber-800/50 uppercase mb-1">IBAN</p>
                                <div class="flex items-center justify-between gap-4 bg-white px-5 py-4 rounded-2xl border border-amber-200 shadow-sm group/iban cursor-pointer" onclick="copyToClipboard('{{ $bankDetails['bank_iban'] }}')">
                                    <p class="text-sm font-black text-slate-900 tracking-wider font-mono">{{ $bankDetails['bank_iban'] }}</p>
                                    <i class="far fa-copy text-amber-500 group-hover/iban:scale-110 transition-transform"></i>
                                </div>
                            </div>
                            <div class="md:col-span-2 bg-slate-900 text-white p-6 rounded-2xl shadow-xl">
                                <p class="text-[10px] font-black text-white/50 uppercase tracking-widest mb-1">Kritik: Ödeme Açıklaması</p>
                                <div class="flex items-center justify-between gap-4">
                                    <p class="text-lg font-black italic text-orange-400 tracking-tighter uppercase">Sipariş No: {{ $order->id }}</p>
                                    <button onclick="copyToClipboard('Sipariş No: {{ $order->id }}')" class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center hover:bg-white/20 transition-all">
                                        <i class="far fa-copy text-sm"></i>
                                    </button>
                                </div>
                                <p class="text-[9px] text-white/40 mt-3 font-bold uppercase italic">Havale yaparken açıklama kısmına sadece yukarıdaki metni yazınız.</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex flex-col md:flex-row items-center justify-center gap-6">
                    <a href="{{ route('user.orders') }}" class="w-full md:w-auto px-12 py-5 bg-slate-900 text-white rounded-[25px] font-black italic shadow-xl shadow-slate-900/20 hover:bg-orange-600 transition-all active:scale-95 uppercase tracking-tighter">Siparişlerimi Görüntüle</a>
                    <a href="{{ route('home') }}" class="w-full md:w-auto px-12 py-5 bg-gray-100 text-slate-900 rounded-[25px] font-black italic hover:bg-gray-200 transition-all active:scale-95 uppercase tracking-tighter">Alışverişe Devam Et</a>
                </div>

                <div class="mt-16 pt-8 border-t border-gray-100 flex items-center justify-center gap-12 grayscale opacity-40">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-shield-alt text-2xl"></i>
                        <span class="text-[10px] font-black uppercase tracking-tighter">GÜVENLİ ÖDEME</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-truck text-2xl"></i>
                        <span class="text-[10px] font-black uppercase tracking-tighter">HIZLI TESLİMAT</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Kopyalandı',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true
        });
    });
}
</script>
@endsection
