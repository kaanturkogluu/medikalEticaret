@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    statusMap: {
        'awaiting': { label: 'Onay Bekliyor', color: 'bg-indigo-100 text-indigo-700' },
        'created': { label: 'Hazırlanıyor', color: 'bg-blue-100 text-blue-700' },
        'pending_payment': { label: 'Ödeme Bekleniyor', color: 'bg-amber-100 text-amber-700' },
        'pending': { label: 'Beklemede', color: 'bg-slate-100 text-slate-700' },
        'picking': { label: 'Toplanıyor', color: 'bg-amber-100 text-amber-700' },
        'invoiced': { label: 'Faturalandı', color: 'bg-cyan-100 text-cyan-700' },
        'shipped': { label: 'Kargoya Verildi', color: 'bg-orange-100 text-orange-700' },
        'atcollectionpoint': { label: 'Teslimat Noktasında', color: 'bg-purple-100 text-purple-700' },
        'cancelled': { label: 'İptal Edildi', color: 'bg-red-100 text-red-700' },
        'unpacked': { label: 'Paket Bölündü', color: 'bg-slate-100 text-slate-700' },
        'delivered': { label: 'Teslim Edildi', color: 'bg-emerald-100 text-emerald-700' },
        'undelivered': { label: 'Teslim Edilemedi', color: 'bg-rose-100 text-rose-700' },
        'returned': { label: 'İade Edildi', color: 'bg-gray-100 text-gray-700' },
        'undeliveredandreturned': { label: 'İade Edildi', color: 'bg-gray-100 text-gray-700' },
        'readytoship': { label: 'Kargoya Hazır', color: 'bg-indigo-100 text-indigo-700' }
    },
    getStatus(status) {
        const s = (status || '').toLowerCase();
        return this.statusMap[s] || { label: status, color: 'bg-slate-100 text-slate-600' };
    }
}">
    <!-- Header -->
    <div class="flex flex-col xl:flex-row gap-4 xl:items-center xl:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sipariş Yönetimi</h2>
            <p class="text-sm text-slate-500 mt-1">Web sitenizden gelen tüm siparişleri buradan yönetebilirsiniz.</p>
        </div>
        <div class="flex flex-wrap items-stretch sm:items-center gap-3 w-full xl:w-auto">
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[1000px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sipariş / Paket No</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pazaryeri</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Müşteri</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Tutar</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Durum</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Oluşturma</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                @php $o->load(['items.product.productImages', 'channel', 'shippingCompany']); @endphp
                <tr onclick="window.location='{{ route('admin.orders.show', $o->id) }}'" class="hover:bg-slate-50 transition-all group border-b border-slate-50 cursor-pointer">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-black text-slate-800 tracking-tighter">#{{ $o->external_order_id ?? $o->id }}</span>
                                @if($o->invoice_file)
                                    <span title="Fatura Gönderildi" class="text-teal-500 text-[10px]"><i class="fas fa-file-invoice"></i></span>
                                @endif
                            </div>
                            @if($o->channel_id)
                                <span class="text-[9px] font-bold text-brand-600 uppercase tracking-tighter opacity-70">
                                    {{ $o->raw_marketplace_data['shipmentNumber'] ?? 'Paket No Yok' }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($o->channel)
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase tracking-tighter" style="background-color: {{ $o->channel->color ?? '#64748b' }}">
                                    {{ $o->channel->name }}
                                </span>
                            </div>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-500 uppercase tracking-tighter">
                                WEB SİPARİŞİ
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">{{ $o->customer_name }}</span>
                            <span class="text-[10px] text-slate-400 lowercase">{{ $o->customer_email }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-black text-slate-900 tabular-nums">{{ number_format($o->total_price, 2, ',', '.') }} ₺</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span :class="getStatus('{{ $o->order_status }}').color" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider" x-text="getStatus('{{ $o->order_status }}').label"></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-[10px] font-bold text-slate-500">
                            {{ $o->order_date ? $o->order_date->format('d.m.Y H:i') : $o->created_at->format('d.m.Y H:i') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orders.show', $o->id) }}" onclick="event.stopPropagation()" class="inline-flex p-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-brand-500 hover:text-brand-600 transition-all lg:opacity-0 lg:group-hover:opacity-100 opacity-100">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
