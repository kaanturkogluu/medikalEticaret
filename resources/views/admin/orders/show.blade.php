@extends('layouts.admin')

@section('title', 'Sipariş Detayı #' . ($order->external_order_id ?? $order->id))

@section('content')
@php
    $s = strtolower($order->order_status ?? '');
@endphp
<div class="space-y-6">
    {{-- Breadcrumb & Back --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('admin.orders') }}" class="hover:text-slate-800 transition-colors">Sipariş Yönetimi</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-slate-800 font-semibold">Sipariş #{{ $order->external_order_id ?? $order->id }}</span>
        </div>
        <div class="flex items-center gap-3">
            {{-- Delete Order Button --}}
            <button onclick="confirmDeleteOrder({{ $order->id }})" class="flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-sm border border-rose-100">
                <i class="fas fa-trash-alt"></i> Siparişi Sil
            </button>
            <a href="{{ route('admin.orders') }}" class="flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                <i class="fas fa-arrow-left"></i> Listeye Geri Dön
            </a>
        </div>
    </div>

    {{-- Main Order Info Header --}}
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 bg-brand-50 rounded-2xl flex items-center justify-center text-brand-600 text-2xl font-black">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-black text-slate-800">Sipariş #{{ $order->external_order_id ?? $order->id }}</h1>
                        @if($order->channel)
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider text-white" style="background-color: {{ $order->channel->color ?? '#64748b' }}">
                                {{ $order->channel->name }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-500">
                                WEB SİPARİŞİ
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-slate-500">
                        <span><i class="far fa-calendar-alt mr-1.5"></i>{{ $order->order_date ? $order->order_date->format('d.m.Y H:i') : $order->created_at->format('d.m.Y H:i') }}</span>
                        {{-- Hiding payment method temporarily --}}
                        {{-- <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span> --}}
                        {{-- <span><i class="fas fa-wallet mr-1.5"></i>{{ match($order->payment_method) { 'credit_card' => 'Kredi Kartı', 'eft' => 'EFT / Havale', 'cash_on_delivery' => 'Kapıda Ödeme', default => $order->payment_method ?? '-' } }}</span> --}}
                    </div>
                </div>
            </div>

            {{-- Hiding payment status badge and approval/cancel actions temporarily --}}
            {{--
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $s = strtolower($order->order_status ?? '');
                    $statusMap = [
                        'awaiting' => ['label' => 'Onay Bekliyor', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-100'],
                        'created' => ['label' => 'Hazırlanıyor', 'class' => 'bg-blue-50 text-blue-700 border-blue-100'],
                        'pending_payment' => ['label' => 'Ödeme Bekleniyor', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
                        'pending' => ['label' => 'Beklemede', 'class' => 'bg-slate-50 text-slate-700 border-slate-100'],
                        'picking' => ['label' => 'Toplanıyor', 'class' => 'bg-amber-50 text-amber-700 border-amber-100'],
                        'invoiced' => ['label' => 'Faturalandı', 'class' => 'bg-cyan-50 text-cyan-700 border-cyan-100'],
                        'shipped' => ['label' => 'Kargoya Verildi', 'class' => 'bg-orange-50 text-orange-700 border-orange-100'],
                        'delivered' => ['label' => 'Teslim Edildi', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-100'],
                        'cancelled' => ['label' => 'İptal Edildi', 'class' => 'bg-red-50 text-red-700 border-red-100'],
                        'returned' => ['label' => 'İade Edildi', 'class' => 'bg-gray-50 text-gray-700 border-gray-100'],
                    ];
                    $currentStatus = $statusMap[$s] ?? ['label' => ucfirst($order->order_status), 'class' => 'bg-slate-50 text-slate-700 border-slate-100'];
                @endphp
                <span class="px-4 py-2 border rounded-2xl text-xs font-black uppercase tracking-widest {{ $currentStatus['class'] }}">
                    {{ $currentStatus['label'] }}
                </span>

                @if($order->order_status === 'Awaiting' || $order->order_status === 'pending_payment')
                    <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-md transition-all">
                            Onayla
                        </button>
                    </form>
                @endif

                @if(!in_array($s, ['cancelled', 'iptal edildi', 'shipped', 'delivered']))
                    <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="inline" onsubmit="return confirmCancel(event, this)">
                        @csrf
                        <input type="hidden" name="cancel_reason" id="cancelReasonInput">
                        <button type="submit" class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-md transition-all">
                            İptal Et
                        </button>
                    </form>
                @endif
            </div>
            --}}
        </div>
    </div>

    {{-- Detail Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left 2 Columns: Client Info, Invoice, Items --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Client & Delivery Info Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Client Info --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-user text-brand-500"></i> Müşteri Bilgileri
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Ad Soyad</span>
                            @if($order->user_id)
                                <a href="{{ route('admin.customers', ['q' => $order->customer_email]) }}" class="text-sm font-bold text-brand-600 hover:underline">
                                    {{ $order->customer_name }} <i class="fas fa-external-link-alt text-[10px] ml-1"></i>
                                </a>
                            @else
                                <span class="text-sm font-bold text-slate-800">{{ $order->customer_name }}</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">E-Posta</span>
                            <span class="text-sm font-medium text-slate-700">{{ $order->customer_email ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Telefon</span>
                            <span class="text-sm font-medium text-slate-700">{{ $order->formatted_customer_phone ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-brand-500"></i> Teslimat Adresi
                    </h3>
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-slate-700 leading-relaxed">
                            {{ $order->address_info['address'] ?? ($order->raw_marketplace_data['shipmentAddress']['fullAddress'] ?? ($order->raw_marketplace_data['shippingAddress']['address'] ?? '-')) }}
                        </p>
                        @if(isset($order->address_info['neighborhood']))
                            <p class="text-xs text-slate-500">{{ $order->address_info['neighborhood'] }} Mh.</p>
                        @endif
                        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">
                            {{ $order->address_info['district'] ?? ($order->raw_marketplace_data['shipmentAddress']['district'] ?? ($order->raw_marketplace_data['shippingAddress']['district'] ?? '-')) }} / 
                            {{ $order->address_info['city'] ?? ($order->raw_marketplace_data['shipmentAddress']['city'] ?? ($order->raw_marketplace_data['shippingAddress']['city'] ?? '-')) }}
                        </p>
                    </div>
                </div>

                {{-- Payment Info --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-wallet text-brand-500"></i> Ödeme Bilgileri
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Ödeme Yöntemi</span>
                            <span class="text-sm font-bold text-slate-800">
                                {{ match($order->payment_method) { 
                                    'credit_card' => 'Kredi Kartı (Iyzico)', 
                                    'eft' => 'EFT / Havale', 
                                    'cash_on_delivery' => 'Kapıda Ödeme', 
                                    default => $order->payment_method ?? '-' 
                                } }}
                            </span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Ödeme Durumu</span>
                            @if($order->is_paid)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Ödeme Yapıldı
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Ödeme Yapılmadı
                                </span>
                            @endif

                            @if(!$order->is_paid || $s === 'cancelled')
                                <div class="mt-2">
                                    <button type="button" onclick="confirmUpdatePaymentStatus({{ $order->id }}, true)" class="w-full text-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all flex items-center justify-center gap-1.5">
                                        <i class="fas fa-key"></i> Ücret Ödendi Yap (Şifre İle)
                                    </button>
                                </div>
                            @endif
                        </div>
                        @if($order->payment_method === 'credit_card' && ($order->iyzico_payment_id || $order->is_paid))
                            @if($order->iyzico_payment_id)
                                <div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Iyzico Ödeme ID</span>
                                    <span class="text-xs font-mono text-slate-700">{{ $order->iyzico_payment_id }}</span>
                                </div>
                            @endif
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Banka / Kart Programı</span>
                                <span class="text-xs font-bold text-slate-800">{{ $order->card_family ?: 'Banka / Kredi Kartı' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Taksit Sayısı</span>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i class="fas fa-layer-group text-[10px]"></i>
                                    {{ ($order->installment && $order->installment > 1) ? $order->installment . ' Taksit' : 'Tek Çekim' }}
                                </span>
                            </div>

                            <div class="pt-3 border-t border-slate-100 space-y-2">
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-slate-500 font-medium">Müşteriden Çekilen Tutar</span>
                                    <span class="font-black text-slate-800">{{ number_format($order->paid_price ?? $order->total_price, 2, ',', '.') }} ₺</span>
                                </div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-amber-600 font-medium">Banka & Iyzico Kesintisi</span>
                                    <span class="font-bold text-amber-700">-{{ number_format($order->iyzico_fee ?? 0, 2, ',', '.') }} ₺</span>
                                </div>
                                <div class="flex justify-between items-center text-xs pt-1.5 border-t border-slate-100">
                                    <span class="text-emerald-700 font-bold">Net Hakediş (Satıcıya Kalan)</span>
                                    <span class="font-black text-emerald-700">{{ number_format(($order->paid_price ?? $order->total_price) - ($order->iyzico_fee ?? 0), 2, ',', '.') }} ₺</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Invoice Info (If exists) --}}
            @if($order->invoice_info)
                <div class="bg-amber-50/50 rounded-3xl p-6 border border-amber-100 space-y-4">
                    <h3 class="text-xs font-black text-amber-600 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-file-invoice text-amber-500"></i> Fatura Detayları
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="text-[10px] text-amber-700/60 font-bold uppercase block">Fatura Tipi</span>
                            <span class="text-xs font-bold text-amber-900 uppercase">
                                {{ ($order->invoice_info['type'] ?? '') === 'bireysel' ? 'Bireysel' : 'Kurumsal' }}
                            </span>
                        </div>
                        @if(($order->invoice_info['type'] ?? '') === 'bireysel')
                            <div>
                                <span class="text-[10px] text-amber-700/60 font-bold uppercase block">T.C. Kimlik No</span>
                                <span class="text-xs font-bold text-amber-900">{{ $order->invoice_info['tc_no'] ?? '-' }}</span>
                            </div>
                        @else
                            <div class="col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-[10px] text-amber-700/60 font-bold uppercase block">Firma Adı</span>
                                    <span class="text-xs font-bold text-amber-900">{{ $order->invoice_info['company_name'] ?? '-' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-amber-700/60 font-bold uppercase block">Vergi Dairesi / No</span>
                                    <span class="text-xs font-bold text-amber-900">
                                        {{ $order->invoice_info['tax_office'] ?? '-' }} / {{ $order->invoice_info['tax_number'] ?? '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-amber-700/60 font-bold uppercase block">Yasal Adres</span>
                                    <span class="text-xs font-bold text-amber-900">{{ $order->invoice_info['legal_address'] ?? '-' }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Cancellation Info --}}
            @if($s === 'cancelled')
                <div class="bg-red-50 rounded-3xl p-6 border border-red-100 space-y-4">
                    <h3 class="text-xs font-black text-red-600 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-ban"></i> İptal Detayları
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <span class="text-[10px] text-red-500/60 font-bold uppercase block">İptal Tarihi</span>
                            <span class="text-xs font-bold text-red-900">
                                {{ $order->canceled_at ? \Carbon\Carbon::parse($order->canceled_at)->format('d.m.Y H:i') : $order->updated_at->format('d.m.Y H:i') }}
                            </span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-[10px] text-red-500/60 font-bold uppercase block">İptal Nedeni</span>
                            <span class="text-xs font-bold text-red-900">{{ $order->cancel_reason ?? '-' }}</span>
                        </div>
                    </div>
                    
                    {{-- Manual Payment Update & Iyzico Query Buttons --}}
                    <div class="border-t border-red-200/50 pt-4 mt-2 flex items-center justify-between flex-wrap gap-4">
                        <div class="text-[11px] text-red-600 font-medium leading-relaxed max-w-xl">
                            <i class="fas fa-info-circle mr-1"></i> Bu sipariş iptal durumundadır. Müşteri ödemeyi tamamladıysa yönetici şifrenizle manuel olarak "Ücret Ödendi" durumuna getirebilir veya Iyzico'dan sorgulayabilirsiniz.
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" onclick="confirmUpdatePaymentStatus({{ $order->id }}, true)" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-sm transition-all flex items-center gap-2">
                                <i class="fas fa-key"></i> Manuel Ücret Ödendi Yap
                            </button>
                            @if($order->payment_method === 'credit_card')
                                <form action="{{ route('admin.orders.check-iyzico', $order->id) }}" method="POST" class="flex-shrink-0" id="checkIyzicoForm">
                                    @csrf
                                    <input type="hidden" name="token" id="iyzicoTokenInput" value="{{ $order->payment_token }}">
                                    <button type="button" onclick="submitIyzicoCheck()" class="px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-sm transition-all flex items-center gap-2">
                                        <i class="fas fa-search-dollar"></i> Iyzico'dan Sorgula
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @if($order->payment_method === 'credit_card')
                        <script>
                            function submitIyzicoCheck() {
                                let hasToken = @json(!empty($order->payment_token));
                                if (!hasToken) {
                                    let token = prompt('Bu siparişte kaydedilmiş Iyzico tokenı bulunamadı. Lütfen Iyzico panelindeki/loglardaki tokenı girin:');
                                    if (!token) return;
                                    document.getElementById('iyzicoTokenInput').value = token;
                                }
                                document.getElementById('checkIyzicoForm').submit();
                            }
                        </script>
                    @endif
                </div>
            @endif

            {{-- Items Table --}}
            <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Sipariş Ürünleri</h3>
                    <span class="text-xs font-bold text-brand-600 bg-brand-50 px-3 py-1 rounded-xl">
                        {{ $order->items->count() }} Kalem Ürün
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-4">Ürün Detayı</th>
                                <th class="px-6 py-4 text-center">Adet</th>
                                <th class="px-6 py-4 text-right">Birim Fiyat</th>
                                <th class="px-6 py-4 text-right">İndirim</th>
                                <th class="px-6 py-4 text-right">Toplam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($item->product && $item->product->productImages->count() > 0)
                                                <img src="{{ $item->product->productImages[0]->url }}" class="w-12 h-12 object-cover rounded-xl border border-slate-200 flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-300 flex-shrink-0">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <span class="text-xs font-bold text-slate-800 block">{{ $item->product->name ?? ($item->name ?? 'Ürün') }}</span>
                                                <span class="text-[10px] px-1.5 py-0.5 bg-slate-100 text-slate-500 rounded font-black mt-1 inline-block tracking-widest uppercase">
                                                    SKU: {{ $item->product->sku ?? '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-slate-900 tabular-nums">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-slate-700 tabular-nums">{{ number_format($item->price, 2, ',', '.') }} ₺</td>
                                    <td class="px-6 py-4 text-right text-xs font-bold text-red-500 tabular-nums">
                                        @php
                                            $hasEftDiscount = $order->payment_method === 'eft' && $item->product && $item->product->eft_discount;
                                        @endphp
                                        {{ $hasEftDiscount ? number_format($item->price * $item->quantity * 0.05, 2, ',', '.') . ' ₺' : '0,00 ₺' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-black text-slate-900 tabular-nums">
                                        @php
                                            $lineTotal = $item->price * $item->quantity;
                                            if ($hasEftDiscount) {
                                                $lineTotal *= 0.95;
                                            }
                                        @endphp
                                        {{ number_format($lineTotal, 2, ',', '.') }} ₺
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Summary, Shipping Update, Invoice Sending --}}
        <div class="space-y-6">
            {{-- Summary Totals --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest border-b pb-3">Sipariş Özeti</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-1">
                        <span class="text-slate-500">Ara Toplam</span>
                        <span class="font-bold text-slate-800 tabular-nums">
                            {{ number_format($order->total_price + ($order->discount_amount ?? 0) - ($order->shipping_price ?? 0), 2, ',', '.') }} ₺
                        </span>
                    </div>
                    @if($order->shipping_price > 0)
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500">Kargo Ücreti</span>
                            <span class="font-bold text-slate-800 tabular-nums">+ {{ number_format($order->shipping_price, 2, ',', '.') }} ₺</span>
                        </div>
                    @endif
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between items-center py-1 text-red-600">
                            <span class="text-red-500">Toplam İndirim</span>
                            <span class="font-bold tabular-nums">- {{ number_format($order->discount_amount, 2, ',', '.') }} ₺</span>
                        </div>
                    @endif
                    @if($order->used_points > 0)
                        <div class="flex justify-between items-center py-1 text-amber-600">
                            <span class="text-amber-500">Kullanılan Puan</span>
                            <span class="font-bold">{{ $order->used_points }} Puan</span>
                        </div>
                    @endif
                    <div class="border-t border-slate-100 pt-3 flex justify-between items-end">
                        <span class="text-xs font-black text-slate-800 uppercase">Toplam (NET)</span>
                        <span class="text-2xl font-black text-brand-600 tabular-nums">{{ number_format($order->total_price, 2, ',', '.') }} ₺</span>
                    </div>
                </div>
            </div>

            {{-- Shipping Action (If Created or Picking status) --}}
            @if(in_array($s, ['created', 'picking']))
                <div class="bg-brand-50/50 rounded-3xl p-6 border border-brand-100 space-y-4">
                    <h3 class="text-xs font-black text-brand-700 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-truck-loading"></i> Kargo İşlemleri
                    </h3>
                    <form action="{{ route('admin.orders.update-shipping', $order->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kargo Firması</label>
                            <select name="shipping_company_id" required class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all">
                                <option value="">Seçiniz...</option>
                                @foreach($shippingCompanies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Takip Numarası</label>
                            <input type="text" name="tracking_code" required placeholder="Kargo takip kodu" class="w-full px-4 py-2.5 bg-white border border-slate-200 rounded-2xl text-xs font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all">
                        </div>
                        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-md transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-paper-plane"></i> Kargoya Ver ve Bilgilendir
                        </button>
                    </form>
                </div>
            @endif

            {{-- Print & Invoice Documents --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest border-b pb-3">Belgeler & Çıktılar</h3>
                <div class="space-y-3">
                    {{-- Print Barcode --}}
                    <button onclick="printLabel()" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-sm">
                        <i class="fas fa-barcode"></i> Sipariş Barkodu Çıkar
                    </button>

                    {{-- Invoice Send/Upload --}}
                    @if(in_array($s, ['shipped', 'delivered']))
                        <div x-data="{ open: false }" class="space-y-3">
                            <button @click="open = !open" 
                                    class="w-full py-3 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 shadow-sm
                                           {{ $order->invoice_file ? 'bg-teal-600 hover:bg-teal-700' : 'bg-cyan-600 hover:bg-cyan-700' }}">
                                <i class="fas" :class="open ? 'fa-times' : 'fa-file-invoice'"></i>
                                <span x-text="open ? 'Kapat' : '{{ $order->invoice_file ? 'Fatura Yüklendi (Güncelle)' : 'Fatura PDF Gönder' }}'"></span>
                            </button>
                            
                            <div x-show="open" x-cloak class="p-4 bg-slate-50 border border-slate-100 rounded-2xl space-y-3 animate-in fade-in duration-200">
                                <form action="{{ route('admin.orders.upload-invoice', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                    @csrf
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase">Fatura PDF Seçin</label>
                                    <input type="file" name="invoice_file" accept=".pdf" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                                    <button type="submit" class="w-full py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                                        PDF Gönder
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Cargo Tracking Detail (If Shipped/Delivered) --}}
            @if($order->tracking_code && $order->shippingCompany)
                <div class="bg-slate-50 rounded-3xl p-6 border border-slate-100 space-y-4">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-truck text-brand-500"></i> Kargo Takip Bilgisi
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500">Firma</span>
                            <span class="font-bold text-slate-800">{{ $order->shippingCompany->name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500">Takip Kodu</span>
                            <span class="font-bold text-slate-850 tracking-wide font-mono">{{ $order->tracking_code }}</span>
                        </div>
                        <a href="{{ $order->shippingCompany->getTrackingLink($order->tracking_code) }}" target="_blank" class="block w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 rounded-2xl text-center text-xs font-bold transition-all mt-2 shadow-sm">
                            Kargo Takip Sayfasına Git <i class="fas fa-external-link-alt text-[10px] ml-1"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmCancel(event, form) {
        event.preventDefault();
        let reason = prompt('Lütfen sipariş iptal nedenini giriniz (Müşteriye gönderilecektir):');
        if (reason) {
            document.getElementById('cancelReasonInput').value = reason;
            form.submit();
        }
    }

    function printLabel() {
        const packerName = 'Turgay Vural';
        const url = '{{ route("admin.orders.print-label", $order->id) }}' + '?packer=' + encodeURIComponent(packerName);
        window.open(url, '_blank', 'width=800,height=600');
    }

    function confirmDeleteOrder(orderId) {
        Swal.fire({
            title: 'Siparişi Silmek İstediğinize Emin misiniz?',
            text: 'Bu işlem geri alınamaz! Lütfen onaylamak için yönetici şifrenizi girin:',
            input: 'password',
            inputPlaceholder: 'Yönetici Şifresi',
            showCancelButton: true,
            confirmButtonText: 'Sil',
            cancelButtonText: 'İptal',
            confirmButtonColor: '#e11d48', // rose-600
            cancelButtonColor: '#64748b',  // slate-500
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Şifre girmek zorunludur!');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Create a form dynamically and submit it
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/admin/orders') }}/${orderId}/delete`;
                
                const csrfToken = '{{ csrf_token() }}';
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                const passwordInput = document.createElement('input');
                passwordInput.type = 'hidden';
                passwordInput.name = 'password';
                passwordInput.value = result.value;
                form.appendChild(passwordInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmUpdatePaymentStatus(orderId, isPaid = true) {
        Swal.fire({
            title: 'Ödeme Durumunu "Ücret Ödendi" Yap',
            text: 'Bu siparişi manuel olarak ödenmiş duruma getirmek ve siparişi işleme almak istediğinize emin misiniz? Lütfen onaylamak için yönetici şifrenizi girin:',
            input: 'password',
            inputPlaceholder: 'Yönetici Şifreniz',
            showCancelButton: true,
            confirmButtonText: 'Ücret Ödendi Olarak Güncelle',
            cancelButtonText: 'İptal',
            confirmButtonColor: '#059669', // emerald-600
            cancelButtonColor: '#64748b',  // slate-500
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('Yönetici şifrenizi girmelisiniz!');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ url('/admin/orders') }}/${orderId}/update-payment-status`;
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const isPaidInput = document.createElement('input');
                isPaidInput.type = 'hidden';
                isPaidInput.name = 'is_paid';
                isPaidInput.value = isPaid ? '1' : '0';
                form.appendChild(isPaidInput);

                const passwordInput = document.createElement('input');
                passwordInput.type = 'hidden';
                passwordInput.name = 'password';
                passwordInput.value = result.value;
                form.appendChild(passwordInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
