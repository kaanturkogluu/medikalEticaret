@extends('layouts.admin')

@section('content')
    <div class="space-y-8" x-data="{ 
        testing: null,
        testConnection(channelId, slug) {
            this.testing = channelId;

            let url = '{{ url('/admin/marketplaces') }}/' + channelId + '/test';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                this.testing = null;
                if (data.success) {
                    notify('success', data.message);
                } else {
                    notify('error', data.message);
                }
            })
            .catch(err => {
                this.testing = null;
                notify('error', 'Bir hata oluştu!');
            });
        }
    }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Pazaryeri Bağlantıları</h2>
                <p class="text-sm text-slate-500 mt-1">API anahtarlarını, tedarikçi kimliklerini ve bağlantı ayarlarını
                    yönetin.</p>
            </div>

        </div>

        @if(session('success'))
            <div
                class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-600 font-bold text-sm shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Marketplaces Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($marketplaces as $m)
                <div
                    class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col relative group">
                    <div class="absolute top-6 right-6">
                        @if($m->active)
                            <span
                                class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Bağlı</span>
                        @else
                            <span
                                class="bg-slate-100 text-slate-400 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Pasif</span>
                        @endif
                    </div>

                    <div style="background-color: {{ $m->color ?? '#f8fafc' }}20; border-color: {{ $m->color ?? '#cbd5e1' }}40;"
                        class="h-16 w-16 rounded-2xl flex items-center justify-center text-3xl mb-6 shadow-inner border group-hover:scale-110 transition-transform">
                        @php
                            $icon = match ($m->slug) {
                                'trendyol' => 'fa-bolt text-brand-500',
                                'hepsiburada' => 'fa-shopping-cart text-orange-500',
                                'n11' => 'fa-store text-emerald-600',
                                'ptt' => 'fa-envelope text-amber-500',
                                default => 'fa-plug text-slate-400'
                            };
                        @endphp
                        <i class="fas {{ $icon }}"></i>
                    </div>

                    <h3 class="text-xl font-black text-slate-800 tracking-tight mb-1">{{ $m->name }}</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-8">Pazaryeri Entegrasyonu</p>

                    <div class="space-y-4 flex-1">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Tedarikçi Kimliği (Supplier ID)</p>
                            <p class="text-xs font-black text-slate-800 tabular-nums tracking-tighter">
                                {{ $m->credential?->supplier_id ?? '---' }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 relative group/key">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">API Key & Secret</p>
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-black text-slate-800 tabular-nums tracking-tighter">
                                    {{ $m->credential?->api_key ? substr($m->credential?->api_key, 0, 5) . '**********' : '---' }}
                                </p>
                                <button class="p-1.5 hover:bg-slate-200 rounded transition-colors text-slate-400">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 flex flex-col gap-3">
                        <div class="grid grid-cols-2 gap-3">
                            <button @click="testConnection('{{ $m->id }}', '{{ $m->slug }}')"
                                :disabled="testing === '{{ $m->id }}'"
                                class="py-3 bg-white border border-slate-200 rounded-xl text-xs font-extrabold text-slate-600 hover:bg-slate-50 transition-colors flex items-center justify-center gap-2 disabled:opacity-50">
                                <i :class="testing === '{{ $m->id }}' ? 'fa-spinner fa-spin' : 'fa-vial'"
                                    class="fas text-[10px]"></i>
                                Test Et
                            </button>
                            <a href="{{ route('admin.marketplaces.edit', $m->id) }}"
                                class="py-3 bg-slate-900 text-white rounded-xl text-xs font-extrabold hover:bg-black transition-all shadow-lg flex items-center justify-center gap-2">
                                <i class="fas fa-edit text-[10px]"></i>
                                Düzenle
                            </a>
                        </div>

                        @if($m->slug === 'n11')
                            <a href="{{ route('admin.n11-orders') }}"
                                class="py-3 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-extrabold hover:bg-emerald-100 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-basket text-[10px]"></i>
                                N11 Siparişlerini Gör
                            </a>
                        @elseif($m->slug === 'ptt')
                            <a href="{{ route('admin.ptt-orders') }}"
                                class="py-3 bg-amber-50 text-amber-700 border border-amber-100 rounded-xl text-xs font-extrabold hover:bg-amber-100 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-envelope text-[10px]"></i>
                                PTT Siparişlerini Gör
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection