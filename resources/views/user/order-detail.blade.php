@extends('layouts.user')

@section('title', 'Sipariş #' . ($order->external_order_id ?? $order->id))

@section('content')
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
                    $isCancelled = str_contains($status, 'iptal') || str_contains($status, 'cancel');
                @endphp
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold
                    {{ $isDelivered ? 'bg-green-50 text-green-600' : ($isCancelled ? 'bg-red-50 text-red-600' : 'bg-orange-50 text-orange-600') }}">
                    <i class="fas fa-{{ $isDelivered ? 'check-circle' : ($isCancelled ? 'times-circle' : 'clock') }}"></i>
                    {{ $order->order_status ?? 'İşleniyor' }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Order Items --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h2 class="font-bold text-gray-900">Sipariş Ürünleri</h2>
            </div>
            @forelse($order->items as $item)
            <div class="px-6 py-4 border-b border-gray-50 last:border-b-0 flex items-center gap-4">
                <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-gray-300 text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm text-gray-900 truncate">{{ $item->product_name ?? 'Ürün' }}</p>
                    <p class="text-xs text-gray-400">SKU: {{ $item->sku ?? '—' }}</p>
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

        {{-- Right Column: Summary + Address --}}
        <div class="space-y-4">
            {{-- Price Summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-bold text-gray-900 mb-4">Ödeme Özeti</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Ürünler Toplamı</span>
                        <span class="font-semibold">{{ number_format($order->total_price, 2, ',', '.') }} TL</span>
                    </div>
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
