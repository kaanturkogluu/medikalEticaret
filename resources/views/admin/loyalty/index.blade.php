@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sadakat Sistemi (Med Puan)</h2>
            <p class="text-sm text-slate-500 mt-1">Müşterilerinize puan kazandırma kurallarını ve oranları yönetin.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-600 font-bold text-sm shadow-sm">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- Puan Aralık Kuralları -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-4">Puan Kazanma Kuralları</h3>
                
                <form action="{{ route('admin.loyalty.rules.store') }}" method="POST" class="flex gap-4 items-end mb-6 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Min. Tutar (TL)</label>
                        <input type="number" step="0.01" name="min_amount" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Max. Tutar (TL)</label>
                        <input type="number" step="0.01" name="max_amount" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Kazanılacak Puan</label>
                        <input type="number" name="points" required class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <button type="submit" class="py-2.5 px-4 bg-brand-500 text-white rounded-lg font-bold text-sm hover:bg-brand-600 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Ekle
                    </button>
                </form>

                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Aralık</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Kazanılacak Puan</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($rules as $rule)
                            <tr class="hover:bg-slate-50">
                                <td class="p-4 text-sm font-medium text-slate-800">
                                    {{ number_format($rule->min_amount, 2) }} TL - {{ number_format($rule->max_amount, 2) }} TL
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-brand-50 text-brand-600 font-bold px-3 py-1 rounded-full text-xs">
                                        {{ $rule->points }} Puan
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.loyalty.rules.destroy', $rule->id) }}" method="POST" onsubmit="return confirm('Bu kuralı silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 bg-rose-50 w-8 h-8 rounded-lg flex items-center justify-center transition-colors ml-auto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-slate-500">Henüz puan kuralı eklenmemiş.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Çarpan Kuralları (Bonus) -->
        <div class="md:col-span-2 space-y-6 mt-8">
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-4">Puan Çarpanı Kuralları (Bonus)</h3>
                <p class="text-xs text-slate-500 mb-4">Belirli bir gün sayısı içerisinde, belirtilen sayıda sipariş veren müşterilerinizin puanlarını katlayın.</p>
                
                <form action="{{ route('admin.loyalty.multipliers.store') }}" method="POST" class="flex gap-4 items-end mb-6 bg-brand-50/50 p-4 rounded-xl border border-brand-100/50">
                    @csrf
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1" title="Örn: 30 gün">Süre (Gün)</label>
                        <input type="number" name="duration_days" required min="1" placeholder="30" class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1" title="Örn: 5 Sipariş">Sipariş Sayısı</label>
                        <input type="number" name="order_count" required min="1" placeholder="5" class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1" title="Örn: 2 Katı">Puan Çarpanı</label>
                        <input type="number" step="0.1" name="multiplier" required min="1" placeholder="2" class="w-full p-2.5 bg-white border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <button type="submit" class="py-2.5 px-4 bg-brand-500 text-white rounded-lg font-bold text-sm hover:bg-brand-600 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Ekle
                    </button>
                </form>

                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Süre</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Gerekli Sipariş</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-center">Çarpan</th>
                                <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($multipliers as $multiplier)
                            <tr class="hover:bg-slate-50">
                                <td class="p-4 text-sm font-medium text-slate-800">
                                    Son {{ $multiplier->duration_days }} Gün
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-slate-100 text-slate-600 font-bold px-3 py-1 rounded-full text-xs">
                                        {{ $multiplier->order_count }} Sipariş
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-brand-50 text-brand-600 font-bold px-3 py-1 rounded-full text-xs">
                                        x{{ number_format($multiplier->multiplier, 1) }} Kat
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <form action="{{ route('admin.loyalty.multipliers.destroy', $multiplier->id) }}" method="POST" onsubmit="return confirm('Bu çarpan kuralını silmek istediğinize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 hover:text-rose-700 bg-rose-50 w-8 h-8 rounded-lg flex items-center justify-center transition-colors ml-auto">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="p-8 text-center text-slate-500">Henüz çarpan kuralı eklenmemiş.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sağ Taraf: Ayarlar ve Manuel Ekleme -->
        <div class="space-y-6">
            
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-4">Puan Dönüşüm Oranı</h3>
                <p class="text-xs text-slate-500 mb-4">1 Med Puan'ın alışverişte kaç TL indirim sağlayacağını belirleyin.</p>
                <form action="{{ route('admin.loyalty.rate.update') }}" method="POST">
                    @csrf
                    <div class="flex items-center gap-3">
                        <div class="bg-slate-100 px-4 py-3 rounded-xl font-bold text-slate-600">
                            1 Med Puan =
                        </div>
                        <input type="number" step="0.01" name="rate" value="{{ $rate }}" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-brand-500 outline-none">
                        <div class="bg-slate-100 px-4 py-3 rounded-xl font-bold text-slate-600">
                            TL
                        </div>
                    </div>
                    <button type="submit" class="mt-4 w-full py-3 bg-slate-800 text-white rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">
                        Oranı Güncelle
                    </button>
                </form>
            </div>

            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                <h3 class="text-lg font-black text-slate-800 mb-4">Manuel Puan Yükle</h3>
                <p class="text-xs text-slate-500 mb-4">İstediğiniz müşteriye manuel olarak Med Puan tanımlayabilirsiniz.</p>
                <form action="{{ route('admin.loyalty.assign') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Müşteri Seçin</label>
                        <select name="user_id" required class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                            <option value="">Müşteri Seç...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }}) - {{ $customer->med_puan }} Puanı Var</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Eklenecek Puan</label>
                        <input type="number" name="points" required min="1" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-500 outline-none">
                    </div>
                    <button type="submit" class="w-full py-3 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:bg-emerald-600 shadow-lg shadow-emerald-500/20 transition-all">
                        <i class="fas fa-gift mr-1"></i> Puan Yükle
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>
@endsection
