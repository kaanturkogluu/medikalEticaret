@extends('layouts.user')

@section('title', 'Özet Sayfam')

@section('content')
{{-- Stats Row --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-box text-orange-500 text-lg"></i>
        </div>
        <p class="text-3xl font-black text-gray-900">{{ $orderCount }}</p>
        <p class="text-xs text-gray-500 font-medium mt-1">Toplam Sipariş</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-map-marker-alt text-green-500 text-lg"></i>
        </div>
        <p class="text-3xl font-black text-gray-900">{{ $addressCount }}</p>
        <p class="text-xs text-gray-500 font-medium mt-1">Kayıtlı Adres</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-comment-dots text-amber-500 text-lg"></i>
        </div>
        <p class="text-3xl font-black text-gray-900">{{ $commentCount }}</p>
        <p class="text-xs text-gray-500 font-medium mt-1">Değerlendirmem</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm text-center">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-heart text-purple-500 text-lg"></i>
        </div>
        <p class="text-3xl font-black text-gray-900">—</p>
        <p class="text-xs text-gray-500 font-medium mt-1">Favori Ürün</p>
    </div>
</div>

{{-- Recent Orders --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
        <h2 class="font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-clock-rotate-left text-orange-400"></i> Son Siparişlerim
        </h2>
        <a href="{{ route('user.orders') }}" class="text-xs font-bold text-orange-500 hover:underline">Tümünü Gör →</a>
    </div>

    @forelse($orders as $order)
    <div class="px-6 py-5 border-b border-gray-50 last:border-b-0 hover:bg-gray-50/50 transition-colors">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-3 mb-1">
                    <span class="text-xs font-bold text-gray-400">#{{ $order->external_order_id ?? $order->id }}</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold
                        {{ str_contains(strtolower($order->order_status ?? ''), 'delivered') || str_contains(strtolower($order->order_status ?? ''), 'teslim') ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600' }}">
                        <i class="fas fa-{{ str_contains(strtolower($order->order_status ?? ''), 'teslim') ? 'check-circle' : 'clock' }} mr-1"></i>
                        {{ $order->order_status ?? 'İşleniyor' }}
                    </span>
                </div>
                <p class="text-sm font-semibold text-gray-800">{{ $order->customer_name }}</p>
                <p class="text-xs text-gray-400">{{ $order->created_at->translatedFormat('d F Y') }}</p>
            </div>
            <div class="text-right flex-shrink-0">
                <p class="font-black text-orange-500 text-lg">{{ number_format($order->total_price, 2, ',', '.') }} TL</p>
                <a href="{{ route('user.orders.show', $order->id) }}" class="text-xs font-bold text-orange-500 border border-orange-500 px-3 py-1 rounded-lg hover:bg-orange-500 hover:text-white transition-all mt-1 inline-block">Detaylar</a>
            </div>
        </div>
    </div>
    @empty
    <div class="px-6 py-16 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-box-open text-gray-300 text-2xl"></i>
        </div>
        <p class="text-gray-400 font-medium text-sm">Henüz siparişiniz bulunmamaktadır.</p>
        <a href="{{ route('home') }}" class="mt-4 inline-block px-6 py-3 bg-orange-500 text-white text-sm font-bold rounded-xl hover:bg-orange-600 transition-all">Alışverişe Başla</a>
    </div>
    @endforelse
</div>
@endsection
