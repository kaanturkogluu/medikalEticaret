@extends('layouts.admin')

@section('title', 'Teklif Talepleri Yönetimi')

@section('content')
<div class="space-y-6">
    
    <!-- Top Header & Breadcrumbs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="fas fa-file-invoice-dollar text-amber-500"></i>
                <span>Teklif Talepleri</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Müşterilerin oluşturduğu toplu alım ve bağış teklif sepetlerini inceleyin, özel fiyat verin ve hızlı sipariş linki oluşturun.</p>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl shrink-0">
                <i class="fas fa-list-alt"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Toplam Talep</p>
                <p class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</p>
            </div>
        </div>

        <div class="bg-amber-50/70 p-5 rounded-2xl border border-amber-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm shadow-amber-500/30">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-amber-800 uppercase tracking-wider">Bekleyen Talepler</p>
                <p class="text-2xl font-black text-amber-900">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <div class="bg-blue-50/70 p-5 rounded-2xl border border-blue-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center text-xl shrink-0 shadow-sm shadow-blue-500/30">
                <i class="fas fa-tag"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-blue-800 uppercase tracking-wider">Fiyat Verilen</p>
                <p class="text-2xl font-black text-blue-900">{{ $stats['offered'] }}</p>
            </div>
        </div>

        <div class="bg-emerald-50/70 p-5 rounded-2xl border border-emerald-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xl shrink-0 shadow-sm shadow-emerald-600/30">
                <i class="fas fa-link"></i>
            </div>
            <div>
                <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider">Özel Ürün Hazır</p>
                <p class="text-2xl font-black text-emerald-900">{{ $stats['converted'] }}</p>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.quotes.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="lg:col-span-5 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Teklif no, müşteri adı, telefon, kurum adı..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-10 pr-4 text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 outline-none transition-all">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <!-- Status Filter -->
            <div class="lg:col-span-3">
                <select name="status" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 outline-none transition-all">
                    <option value="">Tüm Durumlar</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ Beklemede</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>👁️ İncelendi</option>
                    <option value="offered" {{ request('status') === 'offered' ? 'selected' : '' }}>🏷️ Fiyat Verildi</option>
                    <option value="converted" {{ request('status') === 'converted' ? 'selected' : '' }}>⚡ Özel Ürün Hazırlandı</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>✅ Tamamlandı</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>❌ İptal</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="lg:col-span-2">
                <select name="type" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3 text-xs font-bold text-slate-800 focus:bg-white focus:ring-4 focus:ring-amber-50 focus:border-amber-500 outline-none transition-all">
                    <option value="">Tüm Türler</option>
                    <option value="bulk_order" {{ request('type') === 'bulk_order' ? 'selected' : '' }}>📦 Toplu Alım</option>
                    <option value="donation" {{ request('type') === 'donation' ? 'selected' : '' }}>❤️ Bağış</option>
                    <option value="corporate" {{ request('type') === 'corporate' ? 'selected' : '' }}>🏢 Kurumsal</option>
                    <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>🏷️ Genel</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Filtrele
                </button>
                @if(request()->hasAny(['search', 'status', 'type']))
                <a href="{{ route('admin.quotes.index') }}" class="px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition-colors" title="Filtreleri Temizle">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quotes Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-black text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-5">Teklif No</th>
                        <th class="py-4 px-4">Müşteri / Kurum</th>
                        <th class="py-4 px-4">Tür</th>
                        <th class="py-4 px-4">Ürünler</th>
                        <th class="py-4 px-4">Liste Tutarı</th>
                        <th class="py-4 px-4">Teklif Edilen Fiyat</th>
                        <th class="py-4 px-4">Durum</th>
                        <th class="py-4 px-4">Tarih</th>
                        <th class="py-4 px-5 text-right">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($quotes as $quote)
                    @php 
                        $badge = $quote->status_badge;
                        $phoneClean = preg_replace('/[^0-9]/', '', $quote->customer_phone);
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Quote No -->
                        <td class="py-4 px-5 font-black text-slate-900">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-xs font-black">
                                {{ $quote->quote_no }}
                            </span>
                        </td>

                        <!-- Customer Info -->
                        <td class="py-4 px-4">
                            <div class="space-y-0.5">
                                <p class="font-extrabold text-slate-900">{{ $quote->customer_name }}</p>
                                <div class="flex items-center gap-2">
                                    <a href="tel:{{ $quote->customer_phone }}" class="text-slate-500 hover:text-emerald-600 font-semibold text-[11px] flex items-center gap-1">
                                        <i class="fas fa-phone-alt text-[9px]"></i>
                                        <span>{{ $quote->customer_phone }}</span>
                                    </a>
                                    <a href="https://wa.me/{{ $phoneClean }}" target="_blank" rel="noopener noreferrer" class="text-emerald-600 hover:text-emerald-700 text-xs" title="WhatsApp'tan Yaz">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                                @if($quote->organization_name)
                                <p class="text-[10px] text-slate-400 font-medium truncate max-w-[180px]">{{ $quote->organization_name }}</p>
                                @endif
                            </div>
                        </td>

                        <!-- Type -->
                        <td class="py-4 px-4">
                            <span class="text-[11px] font-bold text-slate-700 whitespace-nowrap">
                                {{ $quote->type_label }}
                            </span>
                        </td>

                        <!-- Items Count -->
                        <td class="py-4 px-4">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-800">{{ $quote->items->sum('quantity') }} Adet</span>
                                <span class="text-[10px] text-slate-400 block">({{ $quote->items->count() }} çeşit ürün)</span>
                            </div>
                        </td>

                        <!-- Estimated Total -->
                        <td class="py-4 px-4 font-bold text-slate-600 whitespace-nowrap">
                            {{ number_format($quote->estimated_total, 2, ',', '.') }} TL
                        </td>

                        <!-- Offered Total -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            @if($quote->offered_total)
                            <span class="font-black text-emerald-700 text-sm">
                                {{ number_format($quote->offered_total, 2, ',', '.') }} TL
                            </span>
                            @else
                            <span class="text-slate-300 font-semibold italic">Henüz verilmedi</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="py-4 px-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-black text-[11px] {{ $badge['bg'] }} {{ $badge['text'] }}">
                                <i class="fas {{ $badge['icon'] }} text-[9px]"></i>
                                <span>{{ $badge['label'] }}</span>
                            </span>
                        </td>

                        <!-- Date -->
                        <td class="py-4 px-4 text-slate-500 whitespace-nowrap text-[11px]">
                            {{ $quote->created_at->translatedFormat('d M Y H:i') }}
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.quotes.show', $quote) }}" class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl text-xs transition-colors inline-flex items-center gap-1.5 shadow-2xs">
                                    <i class="fas fa-eye text-xs"></i>
                                    <span>İncele &amp; Teklif Ver</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <div class="space-y-2">
                                <i class="fas fa-file-invoice-dollar text-4xl opacity-30"></i>
                                <p class="text-sm font-bold text-slate-600">Henüz teklif talebi bulunmuyor.</p>
                                <p class="text-xs text-slate-400">Müşteriler teklif sepetinden talep gönderdiğinde burada listelenecektir.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotes->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $quotes->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
