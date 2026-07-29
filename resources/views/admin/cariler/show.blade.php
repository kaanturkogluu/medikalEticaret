@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    {{-- Top Header & Navigation --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.cariler.index') }}" class="h-11 w-11 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 font-mono text-xs font-bold">{{ $cari->code }}</span>
                    <h1 class="text-xl font-black text-slate-800 tracking-tight">{{ $cari->name }}</h1>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Müşteri Web Alışveriş & İade Ekstresi</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-md transition-all">
                <i class="fas fa-print"></i>
                <span>Ekstreyi Yazdır / PDF</span>
            </button>
        </div>
    </div>

    {{-- Customer Summary Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Paid Spent Card --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">TOPLAM WEB HARCAMASI</span>
                <div class="mt-3">
                    @php
                        $netSpent = $cari->transactions->where('type', 'debit')->sum('amount') - $cari->transactions->where('type', 'credit')->sum('amount');
                        $netSpent = max(0, $netSpent);
                    @endphp
                    <div class="text-3xl font-black text-emerald-600 tabular-nums">{{ number_format($netSpent, 2, ',', '.') }} ₺</div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold mt-2">
                        <i class="fas fa-shopping-bag text-emerald-500"></i> Tamamlanan Net Sipariş Tutarı
                    </span>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Son Alışveriş</span>
                <span class="font-bold text-slate-700">{{ $cari->transactions->first()?->transaction_date?->format('d.m.Y H:i') ?: '-' }}</span>
            </div>
        </div>

        {{-- Contact Details --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">İLETİŞİM & MÜŞTERİ BİLGİLERİ</span>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">E-Posta Adresi</span>
                    <span class="font-bold text-slate-800">{{ $cari->email ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Telefon Numarası</span>
                    <span class="font-bold text-slate-800">{{ $cari->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400 font-medium">Sistem Üyeliği</span>
                    @if($cari->user)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-[11px]">
                            <i class="fas fa-user-check"></i> Kayıtlı Üye (#{{ $cari->user_id }})
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-600 font-bold text-[11px]">
                            <i class="fas fa-user"></i> Misafir Müşteri
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Address Details --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">TESLİMAT / ADRES BİLGİSİ</span>
            <div class="text-xs">
                <p class="font-medium text-slate-700 leading-relaxed">{{ $cari->address ?: 'Kayıtlı teslimat adresi bulunmuyor.' }}</p>
            </div>
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-slate-800">Sipariş & İşlem Geçmişi Ekstresi</h2>
                <p class="text-xs text-slate-400 font-medium">Müşterinin web sitemiz üzerinden yaptığı tüm sipariş ve iptal kayıtları</p>
            </div>
            <span class="px-3 py-1 rounded-xl bg-slate-100 text-slate-600 text-xs font-bold">
                Toplam {{ $cari->transactions->count() }} Kayıt
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="py-4 px-6">Tarih</th>
                        <th class="py-4 px-6">İşlem Durumu</th>
                        <th class="py-4 px-6">Açıklama / Sipariş No</th>
                        <th class="py-4 px-6 text-right">Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cari->transactions as $transaction)
                        <tr class="hover:bg-slate-50/75 transition-colors">
                            <td class="py-4 px-6 text-xs font-bold text-slate-700 whitespace-nowrap">
                                <i class="far fa-calendar-alt text-slate-400 mr-1.5"></i>
                                {{ $transaction->transaction_date->format('d.m.Y H:i') }}
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if($transaction->type === 'debit')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-100">
                                        <i class="fas fa-shopping-bag text-emerald-500"></i> Web Satışı (Ödendi)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-100">
                                        <i class="fas fa-times-circle text-rose-500"></i> İptal / İade
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-xs font-bold text-slate-800">{{ $transaction->description }}</div>
                                @if($transaction->order_id)
                                    <a href="{{ route('admin.orders.show', $transaction->order_id) }}" class="inline-flex items-center gap-1 text-[11px] text-brand-600 font-bold hover:underline mt-0.5">
                                        <span>Sipariş #{{ $transaction->order->external_order_id ?? $transaction->order_id }} Detayına Git</span>
                                        <i class="fas fa-external-link-alt text-[9px]"></i>
                                    </a>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-black {{ $transaction->type === 'debit' ? 'text-slate-900' : 'text-rose-600' }} text-sm tabular-nums">
                                {{ $transaction->type === 'debit' ? number_format($transaction->amount, 2, ',', '.') . ' ₺' : '- ' . number_format($transaction->amount, 2, ',', '.') . ' ₺' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-400">
                                <i class="fas fa-file-invoice text-4xl text-slate-300 mb-3 block"></i>
                                <p class="text-sm font-bold text-slate-600">Bu müşteri için kayıtlı alışveriş bulunmuyor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
