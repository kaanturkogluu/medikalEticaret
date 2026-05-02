@extends('layouts.user')

@section('title', 'Sipariş #' . ($order->external_order_id ?? $order->id))

@section('user_content')
<div class="space-y-4">

    {{-- Back --}}
    <a href="{{ route('user.orders') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-orange-500 transition-colors">
        <i class="fas fa-arrow-left"></i> Siparişlerime Dön
    </a>

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-start justify-between">
            <div>
                <h1 class="font-black text-xl text-gray-900">Sipariş Detayı</h1>
                <p class="text-sm text-gray-400 mt-1">Sipariş No: <span class="font-bold text-gray-600">#{{ $order->external_order_id ?? $order->id }}</span></p>
                <p class="text-sm text-gray-400">Tarih: <span class="font-bold text-gray-600">{{ $order->created_at->format('d M Y, H:i') }}</span></p>
            </div>
            <div class="text-right">
                @php
                    $status = strtolower($order->order_status ?? '');
                    $isDelivered = str_contains($status, 'teslim') || str_contains($status, 'delivered');
                    $isCancelled = str_contains($status, 'iptal') || str_contains($status, 'cancel') || str_contains($status, 'return');
                @endphp
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold
                    {{ $isDelivered ? 'bg-green-50 text-green-600' : ($isCancelled ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600') }}">
                    <i class="fas fa-{{ $isDelivered ? 'check-circle' : ($isCancelled ? 'times-circle' : 'clock') }}"></i>
                    {{ $order->status_label }}
                </span>
                @if(strtolower($order->order_status) === 'pending_payment' && $order->payment_method === 'credit_card')
                <div class="mt-4">
                    <a href="{{ route('iyzico.pay', $order->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-black hover:bg-green-700 transition-all shadow-lg shadow-green-100 animate-pulse uppercase italic tracking-tighter">
                        <i class="fas fa-credit-card"></i> ÖDEMEYİ TAMAMLA
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Shipping Info Card --}}
    @if($order->tracking_code && $order->shippingCompany)
    <div class="bg-indigo-50 rounded-2xl border border-indigo-100 p-8 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                <i class="fas fa-truck text-xl"></i>
            </div>
            <div>
                <h3 class="font-black text-xl text-indigo-900 uppercase italic tracking-tighter">Kargonuz Yolda!</h3>
                <p class="text-xs font-bold text-indigo-800 opacity-70">Paketiniz kargo firmasına teslim edilmiştir.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white/60 p-5 rounded-2xl border border-indigo-200">
                <p class="text-[10px] font-black text-indigo-800/40 uppercase tracking-widest mb-1">Kargo Firması</p>
                <p class="text-sm font-black text-slate-900">{{ $order->shippingCompany->name }}</p>
            </div>
            <div class="bg-white/60 p-5 rounded-2xl border border-indigo-200 flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black text-indigo-800/40 uppercase tracking-widest mb-1">Takip Numarası</p>
                    <p class="text-sm font-black text-slate-900 tracking-wider">{{ $order->tracking_code }}</p>
                </div>
                <a href="{{ $order->shippingCompany->getTrackingLink($order->tracking_code) }}" target="_blank" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl text-[11px] font-black uppercase italic tracking-tighter hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                    KARGOMU TAKİP ET
                </a>
            </div>
        </div>
    </div>
    @endif

    @if(strtolower($order->order_status) === 'awaiting' && $order->payment_method === 'eft')
    <div class="bg-amber-50 rounded-2xl border border-amber-100 p-8 shadow-sm">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-amber-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-200">
                <i class="fas fa-university text-xl"></i>
            </div>
            <div>
                <h3 class="font-black text-xl text-amber-900 uppercase italic tracking-tighter">Ödeme Bekleniyor (Havale/EFT)</h3>
                <p class="text-xs font-bold text-amber-800 opacity-70">Lütfen aşağıdaki bilgileri kullanarak ödemenizi gerçekleştiriniz.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white/60 p-5 rounded-2xl border border-amber-200">
                <p class="text-[10px] font-black text-amber-800/40 uppercase tracking-widest mb-1">Banka & Alıcı</p>
                <p class="text-sm font-black text-slate-900">{{ $bankDetails['bank_name'] }}</p>
                <p class="text-xs font-bold text-slate-500">{{ $bankDetails['bank_account_holder'] }}</p>
            </div>
            <div class="bg-white/60 p-5 rounded-2xl border border-amber-200">
                <p class="text-[10px] font-black text-amber-800/40 uppercase tracking-widest mb-1">IBAN Numarası</p>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-black text-slate-900 tracking-wider">{{ $bankDetails['bank_iban'] }}</p>
                    <button onclick="navigator.clipboard.writeText('{{ $bankDetails['bank_iban'] }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'IBAN Kopyalandı', showConfirmButton:false, timer:1500})" class="text-amber-600 hover:text-amber-700">
                        <i class="far fa-copy"></i>
                    </button>
                </div>
            </div>
            <div class="bg-slate-900 p-5 rounded-2xl shadow-xl">
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-1">Açıklama (Kritik)</p>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-black text-orange-400 italic">Sipariş No: {{ $order->id }}</p>
                    <button onclick="navigator.clipboard.writeText('Sipariş No: {{ $order->id }}'); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Açıklama Kopyalandı', showConfirmButton:false, timer:1500})" class="text-white/40 hover:text-white">
                        <i class="far fa-copy text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Order Items --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900">Sipariş Ürünleri</h2>
            </div>
            @forelse($order->items as $item)
            <div class="px-6 py-4 border-b border-gray-50 last:border-b-0 flex items-center gap-4">
                <div class="w-16 h-16 bg-gray-50 rounded-xl flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-100">
                    @if($item->product && $item->product->productImages->count() > 0)
                        <img src="{{ $item->product->productImages->first()->url }}" class="w-full h-full object-contain p-1" alt="{{ $item->product->name }}">
                    @else
                        <i class="fas fa-box text-gray-300 text-xl"></i>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $item->product->name ?? 'Ürün' }}</p>
                    <p class="text-xs text-gray-400">SKU: {{ $item->product->sku ?? '—' }}</p>
                    <p class="text-xs text-gray-400">Adet: {{ $item->quantity ?? 1 }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-black text-orange-500">{{ number_format(($item->price ?? 0), 2, ',', '.') }} TL</p>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-sm text-gray-400">
                <i class="fas fa-box-open text-2xl mb-2 block text-gray-200"></i>
                Ürün bilgisi bulunamadı.
            </div>
            @endforelse
        </div>

        </div>

        {{-- Right Column: Summary + Address --}}
        <div class="space-y-4">
            {{-- Price Summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-900 mb-4">Ödeme Özeti</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Ara Toplam</span>
                        <span class="font-semibold">{{ number_format($order->total_price + $order->discount_amount, 2, ',', '.') }} TL</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-gray-600">
                        <span>
                            @if($order->coupon_id)
                                İndirim Kuponu ({{ $order->coupon->code ?? 'Kupon' }})
                            @else
                                %5 EFT İndirimi
                            @endif
                        </span>
                        <span class="font-semibold text-red-500">- {{ number_format($order->discount_amount, 2, ',', '.') }} TL</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Kargo</span>
                        <span class="font-semibold text-green-600">Ücretsiz</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between font-black text-gray-900">
                        <span>Genel Toplam</span>
                        <span class="text-orange-500 text-lg">{{ number_format($order->total_price, 2, ',', '.') }} TL</span>
                    </div>
                </div>
            </div>

            {{-- Delivery Address --}}
            @if($order->address_info)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-orange-400"></i> Teslimat Adresi
                </h2>
                <p class="text-sm text-gray-700 font-semibold">{{ $order->address_info['full_name'] ?? $order->customer_name }}</p>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $order->address_info['address'] ?? json_encode($order->address_info) }}</p>
                @if(isset($order->address_info['city']))
                <p class="text-xs text-gray-500">{{ $order->address_info['city'] ?? '' }} / {{ $order->address_info['district'] ?? '' }}</p>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
