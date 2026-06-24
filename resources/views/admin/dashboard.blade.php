@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Toplam Ürün</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums tracking-tight">{{ number_format($stats['total_products']) }}</h3>
                <p class="text-xs text-brand-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-box"></i> Stoktaki Ürünler
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-brand-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-box text-brand-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Aktif Kanallar</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums">{{ $stats['active_channels'] }}</h3>
                <p class="text-xs text-emerald-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-check-circle"></i> Bağlı Entegrasyonlar
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-emerald-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-link text-emerald-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group border-l-4 border-l-amber-500">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Toplam Sipariş</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($stats['total_orders']) }}</h3>
                <p class="text-xs text-amber-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-history"></i> Tüm siparişler
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-amber-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-shopping-cart text-amber-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 4: Netgsm Bakiyesi -->
        <div class="bg-gradient-to-br from-blue-500 to-brand-600 p-6 rounded-2xl shadow-sm border border-brand-500 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group !text-white shadow-xl shadow-brand-500/20">
            <div class="z-10">
                <p class="text-[10px] font-bold text-brand-200 uppercase tracking-widest mb-1">Netgsm Bakiyesi</p>
                
                @if(isset($smsBalance) && is_array($smsBalance) && count($smsBalance) > 0)
                    <div class="flex flex-col gap-2 mt-2">
                        @foreach($smsBalance as $bal)
                            <div class="flex items-center justify-between bg-white/10 rounded-lg px-3 py-1.5 backdrop-blur-sm">
                                <span class="text-xs text-white/80 font-semibold">{{ $bal['balance_name'] ?? 'Bakiye' }}</span>
                                <span class="text-lg font-black text-white tabular-nums">{{ $bal['amount'] ?? '0' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mt-2 bg-rose-500/50 rounded-lg px-3 py-2 backdrop-blur-sm text-center">
                        <span class="text-xs font-bold">Bakiye çekilemedi veya yok.</span>
                    </div>
                @endif
                
                <p class="text-[10px] text-brand-100 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-envelope"></i> SMS Altyapısı
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-brand-500/50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-sms text-brand-400 text-4xl"></i>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-shopping-cart text-brand-600"></i> Son Web Siparişleri
            </h3>
            <a href="{{ route('admin.orders') }}" class="text-brand-500 text-xs font-bold hover:underline tracking-tight">Tüm Siparişler</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50">
                        <th class="pb-3 px-2">Sipariş No</th>
                        <th class="pb-3 px-2">Pazaryeri / Kaynak</th>
                        <th class="pb-3 px-2">Müşteri</th>
                        <th class="pb-3 px-2 text-right">Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($recentOrders as $ro)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="py-4 px-2">
                                <span class="text-xs font-bold text-slate-800">#{{ $ro->external_order_id ?? $ro->id }}</span>
                            </td>
                            <td class="py-4 px-2">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[9px] font-bold uppercase">{{ $ro->channel->name ?? 'Web Sitesi' }}</span>
                            </td>
                            <td class="py-4 px-2 text-xs text-slate-600 font-medium">{{ $ro->customer_name }}</td>
                            <td class="py-4 px-2 text-xs font-black text-slate-900 text-right tabular-nums">{{ number_format($ro->total_price, 2) }} ₺</td>
                        </tr>
                    @endforeach
                    @if($recentOrders->isEmpty())
                        <tr>
                            <td colspan="4" class="py-8 text-center text-xs text-slate-400 italic">Henüz sipariş bulunmuyor.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
