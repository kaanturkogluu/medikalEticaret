@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
        activeTab: new URLSearchParams(window.location.search).get('tab') || 'uyeler',
        setTab(tab) {
            this.activeTab = tab;
            let url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);
        }
    }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Müşteriler</h1>
            <p class="text-sm text-slate-500 mt-1">Sisteme kayıtlı üyeler ve sipariş veren müşteriler</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <form action="{{ route('admin.customers') }}" method="GET" class="flex items-center gap-4">
            <input type="hidden" name="tab" :value="activeTab">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" class="block w-full pl-10 pr-3 py-2 border border-slate-200 rounded-xl leading-5 bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-colors" placeholder="Müşteri Adı, E-posta veya Telefon...">
            </div>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-xl text-sm font-semibold hover:bg-brand-600 transition-colors shadow-lg shadow-slate-200">
                Ara
            </button>
            @if(request()->has('q'))
                <a href="{{ route('admin.customers') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-200 transition-colors">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200">
        <button @click="setTab('uyeler')" 
                :class="activeTab === 'uyeler' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" 
                class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors outline-none">
            <i class="fas fa-users mr-2"></i> Üyeler
        </button>
        <button @click="setTab('siparisler')" 
                :class="activeTab === 'siparisler' ? 'border-brand-600 text-brand-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" 
                class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors outline-none">
            <i class="fas fa-shopping-bag mr-2"></i> Sipariş Verenler
        </button>
    </div>

    <!-- Üyeler Tablosu -->
    <div x-show="activeTab === 'uyeler'" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Kullanıcı</th>
                        <th class="px-6 py-4 font-semibold">İletişim</th>
                        <th class="px-6 py-4 font-semibold text-center">Med Puan</th>
                        <th class="px-6 py-4 font-semibold text-right">Kayıt Tarihi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold">
                                            {{ mb_substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $user->name }}</div>
                                        @if($user->isAdmin())
                                            <div class="text-[10px] font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded mt-1 inline-block">Yönetici</div>
                                        @else
                                            <div class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-1 inline-block">Üye</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-600 flex items-center gap-2">
                                    <i class="fas fa-envelope text-slate-400 w-4"></i> {{ $user->email }}
                                </div>
                                @if($user->email_verified_at)
                                    <div class="text-[10px] font-bold text-emerald-600 mt-1"><i class="fas fa-check-circle"></i> Onaylı</div>
                                @else
                                    <div class="text-[10px] font-bold text-amber-600 mt-1"><i class="fas fa-clock"></i> Onay Bekliyor</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-brand-600">{{ $user->med_puan ?? 0 }} Puan</span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500">
                                {{ $user->created_at->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-users-slash text-4xl mb-3 text-slate-300"></i>
                                <p>Henüz kayıtlı üye bulunmuyor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Sipariş Verenler Tablosu -->
    <div x-show="activeTab === 'siparisler'" x-cloak class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Müşteri</th>
                        <th class="px-6 py-4 font-semibold">İletişim</th>
                        <th class="px-6 py-4 font-semibold text-center">Sipariş Sayısı</th>
                        <th class="px-6 py-4 font-semibold text-right">Toplam Harcama</th>
                        <th class="px-6 py-4 font-semibold text-right">Son Sipariş</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center font-bold">
                                        {{ mb_substr($customer->customer_name ?? 'M', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800">{{ $customer->customer_name ?? 'İsimsiz' }}</div>
                                        @if($customer->user_id)
                                            <div class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-1 inline-block">Kayıtlı Üye</div>
                                        @else
                                            <div class="text-[10px] font-medium text-slate-500 bg-slate-100 px-2 py-0.5 rounded mt-1 inline-block">Misafir</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-slate-600 flex items-center gap-2">
                                    <i class="fas fa-envelope text-slate-400 w-4"></i> {{ $customer->customer_email }}
                                </div>
                                <div class="text-slate-600 flex items-center gap-2 mt-1">
                                    <i class="fas fa-phone text-slate-400 w-4"></i> {{ $customer->customer_phone ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $customer->total_orders }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-brand-600">
                                    {{ number_format($customer->total_spent, 2, ',', '.') }} ₺
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500">
                                {{ \Carbon\Carbon::parse($customer->last_order_date)->format('d.m.Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fas fa-users-slash text-4xl mb-3 text-slate-300"></i>
                                <p>Henüz sipariş veren müşteri kaydı bulunmuyor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
