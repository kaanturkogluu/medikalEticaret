@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    {{-- Header Bar --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-brand-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-slate-800 tracking-tight">Web Satış & Cari Raporlama</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Sadece web sitesi satışları, ciro trendi, müşteri alışveriş geçmişi ve iade analizi</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.cariler.sync') }}" method="POST" onsubmit="return confirm('Tüm web sitesi siparişleri cari satış kayıtlarıyla senkronize edilecek. Devam etmek istiyor musunuz?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-lg shadow-slate-900/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    <i class="fas fa-sync-alt text-brand-400"></i>
                    <span>Web Satışlarını Senkronize Et</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-800 px-5 py-4 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-500 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Time Period Filter Bar --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <form action="{{ route('admin.cariler.index') }}" method="GET" id="filterForm">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest mr-2">DÖNEM:</span>
                    
                    <button type="submit" name="period" value="today" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'today' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Bugün
                    </button>
                    <button type="submit" name="period" value="yesterday" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'yesterday' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Dün
                    </button>
                    <button type="submit" name="period" value="this_week" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'this_week' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Bu Hafta
                    </button>
                    <button type="submit" name="period" value="this_month" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'this_month' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Bu Ay
                    </button>
                    <button type="submit" name="period" value="last_30_days" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'last_30_days' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Son 30 Gün
                    </button>
                    <button type="submit" name="period" value="this_year" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'this_year' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Bu Yıl
                    </button>
                    <button type="submit" name="period" value="all" class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $period === 'all' ? 'bg-brand-600 text-white shadow-md shadow-brand-500/20' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        Tüm Zamanlar
                    </button>
                </div>

                {{-- Custom Date Inputs --}}
                <div class="flex items-center gap-2">
                    <input type="date" name="start_date" value="{{ request('start_date', $startDate ? $startDate->format('Y-m-d') : '') }}"
                           class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500">
                    <span class="text-xs text-slate-400 font-bold">-</span>
                    <input type="date" name="end_date" value="{{ request('end_date', $endDate ? $endDate->format('Y-m-d') : '') }}"
                           class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-700 focus:outline-none focus:border-brand-500">
                    <button type="submit" name="period" value="custom" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all">
                        Filtrele
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Gross Sales --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">TOPLAM SATIŞ CİROSU</span>
                <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <i class="fas fa-shopping-cart text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_gross_sales'], 2, ',', '.') }} ₺</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1 font-medium">
                <i class="fas fa-globe text-blue-500"></i> Web sitesi brüt satış tutarı
            </p>
        </div>

        {{-- Net Sales --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">NET TAHSİL EDİLEN (HAKEDİŞ)</span>
                <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="fas fa-check-circle text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-emerald-600 tabular-nums">{{ number_format($stats['total_net_sales'], 2, ',', '.') }} ₺</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1 font-medium">
                <i class="fas fa-credit-card text-emerald-500"></i> Başarıyla ödenmiş net tutar
            </p>
        </div>

        {{-- Cancelled Sales --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">İPTAL / İADE TUTARI</span>
                <div class="h-9 w-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <i class="fas fa-times-circle text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-rose-600 tabular-nums">{{ number_format($stats['total_cancelled'], 2, ',', '.') }} ₺</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1 font-medium">
                <i class="fas fa-undo text-rose-500"></i> İptal edilen sipariş tutarı
            </p>
        </div>

        {{-- Total Orders --}}
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">TOPLAM SİPARİŞ ADEDİ</span>
                <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <i class="fas fa-box text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_order_count']) }}</span>
            </div>
            <p class="text-[11px] text-slate-500 mt-2 flex items-center gap-1 font-medium">
                <i class="fas fa-receipt text-indigo-500"></i> Dönem içi web sipariş sayısı
            </p>
        </div>
    </div>

    {{-- Interactive Sales Chart --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-black text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-area text-brand-600"></i> Web Satış & Ciro Trendi Grafiği
                </h3>
                <p class="text-xs text-slate-400 font-medium">Seçilen döneme ait günlük brüt satış, net tahsilat ve iptal trendi</p>
            </div>
        </div>
        <div class="h-72 w-full pt-2">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Customer Search & Table --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden space-y-4">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-base font-black text-slate-800">Müşteri Web Alışveriş Kayıtları</h2>
                <p class="text-xs text-slate-400 font-medium">Web sitemizden alışveriş yapan kayıtlı ve misafir müşteriler</p>
            </div>
            <form action="{{ route('admin.cariler.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <div class="relative w-72">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Müşteri Adı, E-Posta veya Telefon..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:border-brand-500 transition-all">
                </div>
                <button type="submit" class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition-all">
                    Ara
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="py-4 px-6">Cari Kodu</th>
                        <th class="py-4 px-6">Müşteri Adı</th>
                        <th class="py-4 px-6">İletişim Bilgileri</th>
                        <th class="py-4 px-6 text-center">Sipariş Sayısı</th>
                        <th class="py-4 px-6 text-right">Detay / Ekstre</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($cariler as $cari)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-700 font-mono text-xs font-bold">
                                    {{ $cari->code }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-800 text-sm">{{ $cari->name }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="space-y-1">
                                    @if($cari->email)
                                        <div class="text-xs text-slate-600 flex items-center gap-1.5 font-medium">
                                            <i class="fas fa-envelope text-slate-400 text-[10px]"></i>
                                            <span>{{ $cari->email }}</span>
                                        </div>
                                    @endif
                                    @if($cari->phone)
                                        <div class="text-xs text-slate-600 flex items-center gap-1.5 font-medium">
                                            <i class="fas fa-phone text-slate-400 text-[10px]"></i>
                                            <span>{{ $cari->phone }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold">
                                    <i class="fas fa-shopping-bag text-[10px]"></i>
                                    {{ $cari->total_sales_count }} Satış Kaydı
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.cariler.show', $cari->id) }}"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-brand-600 text-white text-xs font-bold transition-all shadow-sm">
                                    <span>Satış Ekstresi & Detay</span>
                                    <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fas fa-folder-open text-4xl text-slate-300 mb-3 block"></i>
                                <p class="text-sm font-bold text-slate-600">Henüz müşteri alışveriş kaydı bulunmuyor.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cariler->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $cariler->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Include Chart.js script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function initSalesChart() {
        const canvas = document.getElementById('salesChart');
        if (!canvas) return;
        
        if (window.mySalesChartInstance) {
            window.mySalesChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        const labels = {!! json_encode($chartData['labels']) !!};
        const grossData = {!! json_encode($chartData['gross']) !!};
        const netData = {!! json_encode($chartData['net']) !!};
        const cancelledData = {!! json_encode($chartData['cancelled']) !!};

        window.mySalesChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Net Tahsil Edilen (₺)',
                        data: netData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981'
                    },
                    {
                        label: 'Toplam Brüt Satış (₺)',
                        data: grossData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.04)',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#3b82f6'
                    },
                    {
                        label: 'İptal / İade (₺)',
                        data: cancelledData,
                        borderColor: '#f43f5e',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        fill: false,
                        tension: 0.3,
                        pointRadius: 3,
                        pointBackgroundColor: '#f43f5e'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9'
                        },
                        ticks: {
                            font: { size: 11 },
                            callback: function(value) {
                                return value.toLocaleString('tr-TR') + ' ₺';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initSalesChart);
    } else {
        initSalesChart();
    }
</script>
@endsection
