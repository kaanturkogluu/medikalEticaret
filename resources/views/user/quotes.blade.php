@extends('layouts.user')

@section('title', 'Teklif Taleplerim')

@section('user_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fas fa-file-invoice-dollar text-amber-500"></i>
                <span>Teklif Taleplerim</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Toplu alım ve bağışlarınız için oluşturduğunuz özel fiyat tekliflerini buradan takip edebilir ve satın alabilirsiniz.</p>
        </div>

        <a href="{{ route('quote.track') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all self-start sm:self-auto">
            <i class="fas fa-search text-xs"></i>
            <span>No ile Teklif Sorgula</span>
        </a>
    </div>

    <!-- Quotes List -->
    <div class="space-y-4">
        @forelse($quotes as $quote)
        @php $badge = $quote->status_badge; @endphp
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 md:p-6 shadow-xs hover:border-emerald-200 transition-all space-y-4">
            
            <!-- Top Row: Quote No, Date & Status -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-800 rounded-xl text-xs font-black">
                        {{ $quote->quote_no }}
                    </span>
                    <span class="text-xs font-bold text-slate-500">
                        {{ $quote->type_label }}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-[11px] text-slate-400 font-medium">
                        {{ $quote->created_at->translatedFormat('d M Y H:i') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-black text-[11px] {{ $badge['bg'] }} {{ $badge['text'] }}">
                        <i class="fas {{ $badge['icon'] }} text-[9px]"></i>
                        <span>{{ $badge['label'] }}</span>
                    </span>
                </div>
            </div>

            <!-- Middle Row: Items preview & Pricing -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                <!-- Items count & list preview -->
                <div class="md:col-span-7 space-y-2">
                    <p class="text-xs font-bold text-slate-800">
                        Talep Edilen Ürünler ({{ $quote->items->sum('quantity') }} Adet)
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($quote->items->take(3) as $item)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[11px] font-medium text-slate-700 max-w-[200px] truncate">
                            <span class="font-bold text-slate-900">{{ $item->quantity }}x</span>
                            <span class="truncate">{{ $item->product_name }}</span>
                        </div>
                        @endforeach
                        @if($quote->items->count() > 3)
                        <span class="px-2 py-1 bg-slate-100 rounded-lg text-[10px] font-bold text-slate-500 self-center">
                            +{{ $quote->items->count() - 3 }} ürün daha
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Price details -->
                <div class="md:col-span-5 text-left md:text-right space-y-1">
                    <p class="text-[11px] text-slate-400">Liste Fiyat Toplamı: <strong class="text-slate-600">{{ number_format($quote->estimated_total, 2, ',', '.') }} TL</strong></p>
                    @if($quote->offered_total)
                    <div>
                        <p class="text-[11px] text-emerald-600 font-bold uppercase tracking-wider">Size Özel Teklif Fiyatı:</p>
                        <p class="text-lg font-black text-emerald-700">{{ number_format($quote->offered_total, 2, ',', '.') }} TL</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="{{ route('quote.track', ['quote_no' => $quote->quote_no]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 hover:text-emerald-900 transition-colors">
                    <i class="fas fa-eye text-xs"></i>
                    <span>Talebi &amp; Süreci İncele</span>
                </a>

                @if($quote->custom_payment_link)
                <a href="{{ $quote->custom_payment_link }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-black text-xs uppercase tracking-wider shadow-md shadow-emerald-600/20 transition-all w-full sm:w-auto justify-center">
                    <i class="fas fa-shopping-cart text-xs"></i>
                    <span>Özel Fiyatla Hemen Satın Al</span>
                </a>
                @endif
            </div>

        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center space-y-3">
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h3 class="text-base font-black text-slate-900">Henüz Teklif Talebiniz Bulunmuyor</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                20 adet ve üzeri toplu alımlarınız veya bağışlarınız için ürün detay sayfalarından teklif sepeti oluşturabilirsiniz.
            </p>
            <div class="pt-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all">
                    <span>Ürünleri İncele</span>
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($quotes->hasPages())
    <div class="pt-4">
        {{ $quotes->links() }}
    </div>
    @endif
</div>
@endsection
