@extends('layouts.user')

@section('title', 'Puanlarım')

@section('user_content')
<div class="user-content-card">
    <div class="flex items-center gap-4 border-b border-gray-100 pb-6 mb-6">
        <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner shadow-orange-100/50">
            <i class="fas fa-star"></i>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Med Puanlarım</h1>
            <p class="text-sm text-slate-500 font-medium">Kazandığınız puanları görüntüleyebilir ve harcayabilirsiniz.</p>
        </div>
    </div>

    <!-- Puan Bakiyesi -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-3xl p-8 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg shadow-orange-500/30 mb-10 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 text-white/10 text-9xl">
            <i class="fas fa-star"></i>
        </div>
        <div class="relative z-10 flex items-center gap-6">
            <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center text-4xl border-2 border-white/30 shadow-inner">
                <span class="text-white font-bold drop-shadow-md">₺</span>
            </div>
            <div>
                <p class="text-orange-100 font-bold uppercase tracking-widest text-sm mb-1">Mevcut Bakiyeniz</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black tracking-tighter drop-shadow-sm">{{ $user->med_puan }}</span>
                    <span class="text-xl font-bold text-orange-100">Puan</span>
                </div>
            </div>
        </div>
        <div class="relative z-10 bg-white text-orange-700 px-6 py-4 rounded-2xl shadow-xl flex items-center gap-4 min-w-[200px]">
            <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 text-xl font-bold">
                ₺
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Toplam Değer</p>
                <p class="text-2xl font-black tabular-nums leading-none">{{ number_format($user->med_puan * $rate, 2, ',', '.') }} ₺</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Med Puan Nedir? -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-3">
                    <i class="fas fa-question-circle text-orange-500"></i> Med Puan Nedir?
                </h3>
                <p class="text-sm text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    Sitemiz üzerinden yaptığınız alışverişlerden kazandığınız sadakat puanlarıdır. Bu puanlar hesabınızda birikir ve bir sonraki siparişinizde anında indirim olarak kullanabilirsiniz.
                </p>
            </div>

            <div>
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-3">
                    <i class="fas fa-shopping-bag text-orange-500"></i> Nasıl Kullanırım?
                </h3>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Sipariş vermek istediğiniz ürünleri sepetinize ekleyin ve ödeme sayfasına gidin.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Ödeme sayfasında yer alan <strong>"Med Puan"</strong> bölümünden hesabınızdaki puanları girin veya "Tümünü Kullan" diyerek bakiyenizi sıfırlayın.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check text-green-500 mt-1"></i>
                            <span>Girdiğiniz puan değeri anında genel toplamdan düşülecektir. Puanlarınızın karşılığı: <strong class="text-orange-600">1 Puan = {{ number_format($rate, 2, ',', '.') }} TL</strong></span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-orange-50 text-orange-600 border border-orange-100 rounded-xl text-sm font-bold hover:bg-orange-100 transition-colors">
                <i class="fas fa-cart-arrow-down"></i> Alışverişe Başla
            </a>
        </div>

        <!-- Nasıl Kazanılır? -->
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-3">
                    <i class="fas fa-gift text-orange-500"></i> Puan Kazanım Tablosu
                </h3>
                <p class="text-sm text-slate-500 mb-4">Aşağıdaki tabloda belirlenen sepet tutarlarına karşılık olarak ne kadar Med Puan kazanacağınızı görebilirsiniz:</p>
                
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Harcama Aralığı</th>
                                <th class="px-5 py-3 text-right">Kazanılacak Puan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($rules as $rule)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2 font-bold text-slate-700 text-sm">
                                        <span class="text-slate-900">{{ number_format($rule->min_amount, 2, ',', '.') }} ₺</span>
                                        <i class="fas fa-arrow-right text-slate-300 text-xs"></i>
                                        @if($rule->max_amount)
                                            <span class="text-slate-900">{{ number_format($rule->max_amount, 2, ',', '.') }} ₺</span>
                                        @else
                                            <span class="text-slate-900">ve üzeri</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-600 rounded-lg text-sm font-black border border-orange-100">
                                        +{{ $rule->points }} Puan
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="px-5 py-8 text-center text-sm text-slate-500 italic">Şu anda belirlenmiş bir puan kuralı bulunmamaktadır.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sadakat Çarpanı Tablosu -->
            @if(isset($multipliers) && $multipliers->count() > 0)
            <div>
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2 mb-3">
                    <i class="fas fa-bolt text-amber-500"></i> Sadakat Çarpanı (Bonus)
                </h3>
                <p class="text-sm text-slate-500 mb-4">Sık alışveriş yaparak kazandığınız puanları katlayabilirsiniz! Belirtilen gün içinde ilgili alışveriş sayısına ulaşırsanız puanlarınız belirtilen çarpan kadar artar:</p>
                
                <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-100 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <tr>
                                <th class="px-5 py-3">Süre</th>
                                <th class="px-5 py-3 text-center">Gerekli Sipariş</th>
                                <th class="px-5 py-3 text-right">Kazanılacak Çarpan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($multipliers as $multiplier)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-800 text-sm">
                                        Son {{ $multiplier->duration_days }} Gün İçinde
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="font-bold text-slate-700 text-sm">
                                        {{ $multiplier->order_count }} Sipariş
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-sm font-black border border-amber-100 shadow-sm">
                                        x{{ number_format($multiplier->multiplier, 1) }} Puan
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            
            <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl">
                <p class="text-xs text-amber-800 font-medium flex gap-2">
                    <i class="fas fa-info-circle mt-0.5"></i>
                    Puanlarınız siparişiniz oluşturulduğunda kazanılır. Ancak, iptal veya iade edilen siparişlerde kazanılan puanlar bakiyenizden düşülür.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
