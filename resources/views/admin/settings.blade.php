@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Sistem Ayarları</h2>
                <p class="text-sm text-slate-500 mt-1">Kuyruk yapısı, bildirimler ve entegrasyon ayarlarını belirleyin.</p>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-bold flex items-center gap-2 shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition-colors">
                <i class="fas fa-save"></i> 
                Ayarları Kaydet
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Settings Grid Left -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Notification Section -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-6">
                        <div class="h-10 w-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Bildirim Ayarları</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Yönetici Bildirim E-Posta Adresi</label>
                            <input type="email" name="admin_order_notification_email" value="{{ \App\Models\Setting::getValue('admin_order_notification_email') }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all" placeholder="admin@example.com">
                            <p class="text-[10px] text-slate-400 font-medium px-1 leading-relaxed">Web sitesinden yeni bir sipariş geldiğinde bu adrese bilgilendirme e-postası gönderilecektir.</p>
                        </div>
                    </div>
                </div>

                <!-- Queue Section -->
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-8 border-b border-slate-50 pb-6">
                        <div class="h-10 w-10 bg-brand-50 rounded-xl flex items-center justify-center text-brand-600">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Kuyruk & İşlemci Ayarları</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Worker Sayısı</label>
                            <input type="number" name="queue_workers" value="{{ \App\Models\Setting::getValue('queue_workers', 4) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-widest px-1">Batch Boyutu</label>
                            <input type="number" name="sync_batch_size" value="{{ \App\Models\Setting::getValue('sync_batch_size', 100) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Options -->
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 tracking-tight mb-6 flex items-center gap-2">
                        <i class="fas fa-bell text-brand-500"></i> Sistem Bildirimleri
                    </h3>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <p class="text-xs font-bold text-slate-700">Hata Bildirimleri</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-tight">Sistem hatasında uyar</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="enable_error_notifications" value="0">
                            <input type="checkbox" name="enable_error_notifications" value="1" {{ \App\Models\Setting::getValue('enable_error_notifications', true) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
