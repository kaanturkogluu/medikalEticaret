@extends('layouts.user')

@section('title', 'Siparişlerim')

@section('content')
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    {{-- Header with filters --}}
    <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="font-black text-xl text-gray-900">Siparişlerim</h1>
            {{-- Status filter pills (Trendyol style) --}}
            <div class="flex items-center gap-2 flex-wrap">
                @foreach(['' => 'Tümü', 'ongoing' => 'Devam Eden', 'cancelled' => 'İptal Edilen', 'returned' => 'İade Edilen'] as $val => $label)
                <a href="{{ route('user.orders', ['status' => $val]) }}"
                   class="px-4 py-2 rounded-full text-xs font-bold border transition-all
                   {{ request('status', '') === $val ? 'border-orange-500 bg-orange-500 text-white' : 'border-gray-200 text-gray-600 hover:border-orange-300 hover:text-orange-500' }}">
                   {{ $label }}
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Order list --}}
    @forelse($orders as $order)
    <div class="border-b border-gray-50 last:border-b-0">
        {{-- Order meta row --}}
        <div class="px-6 py-4 flex items-center justify-between bg-gray-50/70 border-b border-gray-100">
            <div class="flex items-center gap-8 text-xs text-gray-500 font-medium">
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Sipariş Tarihi</span>
                    <span class="text-gray-700 font-semibold">{{ $order->created_at->format('d M Y') }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Sipariş Özeti</span>
                    <span class="text-gray-700 font-semibold">{{ $order->items->count() ?? '—' }} Ürün</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Alıcı</span>
                    <span class="text-gray-700 font-semibold">{{ $order->customer_name }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest block">Toplam</span>
                    <span class="text-orange-500 font-black">{{ number_format($order->total_price, 2, ',', '.') }} TL</span>
                </div>
            </div>
            <a href="{{ route('user.orders.show', $order->id) }}"
               class="px-5 py-2.5 bg-orange-500 text-white text-xs font-bold rounded-lg hover:bg-orange-600 transition-all flex-shrink-0">
               Detaylar
            </a>
        </div>

        {{-- Order status row --}}
        <div class="px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php
                    $status = strtolower($order->order_status ?? '');
                    $isDelivered = str_contains($status, 'teslim') || str_contains($status, 'delivered');
                    $isCancelled = str_contains($status, 'iptal') || str_contains($status, 'cancel');
                @endphp
                <span class="inline-flex items-center gap-2 text-sm font-bold {{ $isDelivered ? 'text-green-600' : ($isCancelled ? 'text-red-500' : 'text-orange-500') }}">
                    <i class="fas fa-{{ $isDelivered ? 'check-circle' : ($isCancelled ? 'times-circle' : 'clock') }} text-base"></i>
                    {{ $order->order_status ?? 'İşleniyor' }}
                </span>
            </div>
        </div>
    </div>
    @empty
    <div class="py-20 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-box-open text-gray-300 text-3xl"></i>
        </div>
        <p class="text-gray-500 font-semibold">Bu kategoride siparişiniz bulunmuyor.</p>
        <a href="{{ route('home') }}" class="mt-5 inline-block px-8 py-3 bg-orange-500 text-white font-bold text-sm rounded-xl hover:bg-orange-600 transition-all">Alışverişe Başla</a>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
