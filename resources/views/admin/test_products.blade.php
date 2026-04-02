@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Trendyol Ürün Listesi (API Test)</h2>
            <p class="text-sm text-slate-500 mt-1">İstek Atılan URL: <code class="bg-slate-100 px-1 py-0.5 rounded text-brand-600">{{ $url }}</code></p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
            Geri Dön
        </a>
    </div>

    <!-- JSON Output -->
    <div class="bg-slate-900 rounded-[2rem] p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4">
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">RAW JSON Response</span>
        </div>
        <pre class="text-emerald-400 font-mono text-xs overflow-x-auto custom-scrollbar leading-relaxed">
{{ json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
        </pre>
    </div>
</div>

<style>
/* Custom Scrollbar for Pre Tag */
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #1e293b; /* slate-800 */
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #475569; /* slate-600 */
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #64748b; /* slate-500 */
}
</style>
@endsection
