@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Site Görünümü Ayarları</h1>
            <p class="text-sm text-slate-500 mt-1">Sitenizin ön yüzündeki görsel alanları ve iletişim bilgilerini buradan yönetin.</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('home') }}" target="_blank" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-xs hover:bg-slate-200 transition-colors flex items-center gap-2">
                 <i class="fas fa-external-link-alt"></i> Siteyi Önizle
             </a>
        </div>
    </div>

    <!-- Hub Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Homepage Banner -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-brand-50 text-brand-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-brand-50/50 group-hover:scale-110 transition-transform">
                <i class="fas fa-image text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">Ana Sayfa Banner</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Ana sayfanın en üstündeki kampanyalar ve banner alanını güncelleyin.</p>
            <a href="{{ route('admin.appearance.banner.index') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Marketplace Links -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-orange-50/50 group-hover:scale-110 transition-transform">
                <i class="fas fa-store text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">Pazaryeri & Logolar</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Top bar'daki pazaryeri linklerini, logolarını ve kayan yazıyı yönetin.</p>
            <a href="{{ route('admin.appearance.marketplaces') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Social & Pre-Footer -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-blue-50/50 group-hover:scale-110 transition-transform">
                <i class="fab fa-whatsapp text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">Sosyal Medya & Destek</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">WhatsApp butonu, sosyal medya ikonları ve pre-footer alanını güncelleyin.</p>
            <a href="{{ route('admin.appearance.social') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Contact Info -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-green-50/50 group-hover:scale-110 transition-transform">
                <i class="fas fa-map-marker-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">İletişim Bilgileri</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Adres, Telefon ve Harita (Map) konum bilgilerini buradan belirleyin.</p>
            <a href="{{ route('admin.appearance.contact') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Footer Settings -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-slate-100 text-slate-900 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-slate-50 group-hover:scale-110 transition-transform">
                <i class="fas fa-window-maximize text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">Genel Görünüm</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Alt bilgi (Footer) linkleri ve genel web sitesi stil ayarlarını yapın.</p>
            <a href="{{ route('admin.appearance.general') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Tab Title Switcher -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-indigo-50/50 group-hover:scale-110 transition-transform">
                <i class="fas fa-window-restore text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">Sekme Başlık Ayarları</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Kullanıcı başka sekmeye geçince değişecek sekme yazılarını yönetin.</p>
            <a href="{{ route('admin.appearance.tab_switch') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Popular Products Section -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-orange-50/50 group-hover:scale-110 transition-transform">
                <i class="fas fa-fire text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">Popüler Ürünler</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Ana sayfadaki popüler ürünler (vitrin) bölümünü ve başlıklarını yönetin.</p>
            <a href="{{ route('admin.appearance.popular') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <!-- Return Templates -->
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all group flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mb-6 ring-8 ring-indigo-50/50 group-hover:scale-110 transition-transform">
                <i class="fas fa-undo-alt text-2xl"></i>
            </div>
            <h3 class="text-lg font-black text-slate-900 italic tracking-tighter uppercase mb-2">İade Koşulları</h3>
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Ürün gruplarına özel farklı iade kuralları ve şablonları oluşturun.</p>
            <a href="{{ route('admin.return-templates.index') }}" class="mt-auto px-6 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-brand-600 transition-colors flex items-center gap-2">
                Düzenle <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- Tip Section -->
    <div class="bg-brand-900 p-8 rounded-[32px] text-white overflow-hidden relative shadow-2xl">
        <div class="absolute -right-20 -bottom-20 text-white/5 text-[200px] pointer-events-none">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="relative z-10">
            <h4 class="text-xl font-bold italic tracking-tighter uppercase flex items-center gap-3 mb-2">
                <i class="fas fa-magic text-yellow-400"></i> Bir İpucu
            </h4>
            <p class="text-slate-300 text-sm font-medium leading-relaxed max-w-2xl">
                Sitenizin görünümünü düzenlerken yüksek çözünürlüklü görseller kullanmaya özen gösterin. 
                Değişikliklerinizi yapıp kaydettikten sonra "Siteyi Önizle" butonuyla anında sonuçları görebilirsiniz.
            </p>
        </div>
    </div>
</div>
@endsection
