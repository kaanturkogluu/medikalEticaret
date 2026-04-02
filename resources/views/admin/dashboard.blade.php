@extends('layouts.admin')

@section('content')
<div class="space-y-8" x-data="{ 
    syncStats: { success: 85, failed: 12, pending: 3 },
    ordersOverTime: [12, 19, 3, 5, 2, 3, 10]
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Genel Bakış</h2>
            <p class="text-sm text-slate-500 mt-1">Sistemin anlık durumunu ve başarılı senkronizasyonları inceleyin.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="notify('success', 'Veriler Güncellendi!')" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fas fa-sync text-brand-500 text-[10px]"></i> Verileri Yenile
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <!-- Card 1 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Toplam Ürün</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums tracking-tight tracking-tight tracking-tight">2,543</h3>
                <p class="text-xs text-brand-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-box"></i> Veritabanındaki ürünler
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-brand-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-box text-brand-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Senkronize Ürün</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums">2,481</h3>
                <p class="text-xs text-emerald-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-check-circle"></i> %97.5 Başarı Oranı
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-emerald-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-check-double text-emerald-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group border-l-4 border-l-amber-500">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Bekleyen Kuyruk</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums">42</h3>
                <p class="text-xs text-amber-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-history"></i> İşlenmeyi bekliyor
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-amber-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-hourglass-half text-amber-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group border-l-4 border-l-red-500">
            <div class="z-10">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Hatalı Joblar</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tabular-nums">12</h3>
                <p class="text-xs text-red-500 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-exclamation-triangle"></i> Acil müdahale gerekir
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-red-50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-bug text-red-200 text-4xl"></i>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden group border-l-4 border-l-brand-600 bg-brand-600 !text-white">
            <div class="z-10">
                <p class="text-[10px] font-bold text-brand-200 uppercase tracking-widest mb-1">Günlük Sipariş</p>
                <h3 class="text-3xl font-extrabold text-white tabular-nums">1,120₺</h3>
                <p class="text-xs text-brand-100 mt-3 font-semibold flex items-center gap-1">
                    <i class="fas fa-shopping-cart"></i> Bugün gelen siparişler
                </p>
            </div>
            <div class="absolute -right-4 -bottom-4 h-24 w-24 bg-brand-500/50 rounded-full flex items-center justify-center transition-transform group-hover:scale-110">
                <i class="fas fa-shopping-bag text-brand-400 text-4xl"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Success Rate Chart -->
        <div class="lg:col-span-4 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col">
            <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-brand-500"></i> Senkronizasyon Başarısı
            </h3>
            <div class="flex-1 flex items-center justify-center relative">
                <canvas id="syncChart" class="max-h-[250px]"></canvas>
                <div class="absolute flex flex-col items-center">
                    <span class="text-2xl font-bold text-slate-800">%92</span>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Başarı</span>
                </div>
            </div>
            <div class="mt-6 flex flex-col gap-3">
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 font-medium tracking-tight">Başarılı</span>
                    </div>
                    <span class="font-bold text-slate-800">850 Adet</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-red-500"></span>
                        <span class="text-slate-600 font-medium tracking-tight">Hatalı</span>
                    </div>
                    <span class="font-bold text-slate-800">120 Adet</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                        <span class="text-slate-600 font-medium tracking-tight">Bekleyen</span>
                    </div>
                    <span class="font-bold text-slate-800">30 Adet</span>
                </div>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="lg:col-span-8 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-brand-500"></i> Sipariş Akışı
                </h3>
                <div class="flex items-center gap-2 bg-slate-50 p-1 rounded-lg">
                    <button class="px-3 py-1 text-xs font-bold bg-white text-brand-600 rounded-md shadow-sm border border-slate-200">Haftalık</button>
                    <button class="px-3 py-1 text-xs font-bold text-slate-500 hover:text-slate-700 transition-colors">Aylık</button>
                </div>
            </div>
            <div class="flex-1 flex items-center justify-center">
                <canvas id="ordersChart" class="max-h-[300px] w-full"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Logs / Debug Quick View -->
    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-terminal text-brand-600"></i> Son Sistem Logları
            </h3>
            <a href="/admin/logs" class="text-brand-500 text-xs font-bold hover:underline tracking-tight">Tümünü İncele</a>
        </div>
        <div class="space-y-3">
            <template x-for="i in 3" :key="i">
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100 hover:border-brand-300 transition-all cursor-pointer group">
                    <div class="h-10 w-10 flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold text-slate-800 tracking-tight">Trendyol Stock Update Success</span>
                            <span class="text-[10px] px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full font-bold uppercase tracking-wider">SKU: TR-99AD</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">Update stock to 45 for provider Trendyol at 15:42:01</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-slate-400">2 dk önce</p>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sync Success/Fail Chart
        const ctxSync = document.getElementById('syncChart').getContext('2d');
        const syncChart = new Chart(ctxSync, {
            type: 'doughnut',
            data: {
                labels: ['Başarılı', 'Hatalı', 'Bekleyen'],
                datasets: [{
                    data: [85, 12, 3],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                cutout: '80%',
                responsive: true
            }
        });

        // Orders Over Time Chart
        const ctxOrders = document.getElementById('ordersChart').getContext('2d');
        const ordersChart = new Chart(ctxOrders, {
            type: 'line',
            data: {
                labels: ['Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt', 'Paz'],
                datasets: [{
                    label: 'Siparişler',
                    data: [12, 19, 3, 5, 2, 3, 10],
                    borderColor: '#0ea5e9',
                    backgroundColor: 'rgba(14, 165, 233, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9'
                }]
            },
            options: {
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        grid: { borderDash: [5, 5] },
                        beginAtZero: true
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endsection
