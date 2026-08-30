@extends('layouts.app')

@php
    $primaryColor = \App\Models\Setting::getValue('site_primary_color', '#f27a1a');
    $siteTitle = \App\Models\Setting::getValue('site_title', 'umutMed Market');
    $contactPhone = \App\Models\Setting::getValue('contact_phone', '0546 941 69 96');
    $contactWhatsapp = \App\Models\Setting::getValue('contact_whatsapp', '905469416996');
    $contactAddress = \App\Models\Setting::getValue('contact_address', 'Numune Evler, Ezgi Sk., 31600 Dörtyol/Hatay');
    $etbisUrl = \App\Models\Setting::getValue('etbis_url', 'https://etbis.ticaret.gov.tr/tr/SiteSorgulamaSonuc?siteId=3d4a4a22-900c-4b91-bb0');

    $isMedicalDiaper = str_contains(mb_strtolower($product->name . ' ' . ($product->category->name ?? '')), 'bez') ||
                        str_contains(mb_strtolower($product->name), 'giggles') ||
                        str_contains(mb_strtolower($product->name), 'külot') ||
                        str_contains(mb_strtolower($product->name), 'ped');

    $comments = $product->approvedComments;
    $commentCount = $comments->count();
    $avgRating = $commentCount > 0 ? round($comments->avg('rating'), 1) : null;
    $mainImage = $product->productImages->first()?->url ?? 'https://via.placeholder.com/600x600?text=Ürün+Görseli';
    
    // WhatsApp prefilled message
    $productUrl = request()->fullUrl();
    $waMessage = urlencode("Merhaba {$siteTitle}, {$product->name} (Ürün Kodu: " . ($product->sku ?: $product->id) . ") hakkında bilgi almak / sipariş vermek istiyorum.\nÜrün Linki: {$productUrl}");
    $waLink = "https://wa.me/{$contactWhatsapp}?text={$waMessage}";

    // Marketplaces with entered custom URLs only
    $customUrls = $product->raw_marketplace_data['custom_urls'] ?? [];
    $activeMarketplaces = collect($marketplaces ?? [])->filter(function($mt) use ($customUrls) {
        return !empty($customUrls[$mt['name']]) && trim($customUrls[$mt['name']]) !== '' && trim($customUrls[$mt['name']]) !== '#';
    });
@endphp

@section('title', $product->name . ' - ' . ($product->brand->name ?? 'Medikal Ürün') . ' | ' . $siteTitle)

@section('head')
    <!-- Schema.org JSON-LD for Medical E-Commerce Product -->
    @php
        $schemaData = [
            '@context' => 'https://schema.org/',
            '@type' => 'Product',
            'name' => $product->name,
            'image' => asset($mainImage),
            'description' => strip_tags(Str::limit($product->description, 200)),
            'sku' => (string)($product->sku ?: $product->id),
            'brand' => [
                '@type' => 'Brand',
                'name' => $product->brand->name ?? $siteTitle,
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => request()->fullUrl(),
                'priceCurrency' => 'TRY',
                'price' => (string)$product->price,
                'priceValidUntil' => now()->addMonths(6)->toDateString(),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $siteTitle,
                ],
            ],
        ];

        if ($product->barcode) {
            $schemaData['gtin13'] = $product->barcode;
        }

        if ($commentCount > 0) {
            $schemaData['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => (string)$avgRating,
                'reviewCount' => (string)$commentCount,
            ];
        }
    @endphp
    <script type="application/ld+json">
    {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <style>
        /* Hide awkward scrollbars while allowing smooth touch/scroll navigation */
        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        /* Modern subtle thin scrollbar */
        .custom-scrollbar::-webkit-scrollbar,
        .tab-scroll-container::-webkit-scrollbar {
            height: 4px;
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track,
        .tab-scroll-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb,
        .tab-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover,
        .tab-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endsection

@section('content')
    <div class="ty-container pb-20 pt-4" x-data="{ videoModalOpen: false, selectedQty: 1, activeTab: 'features' }">
        
        <!-- Breadcrumb Navigation -->
        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs text-slate-500 py-3 mb-4 border-b border-slate-100">
            <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors flex items-center gap-1 font-medium">
                <i class="fas fa-home text-[11px]"></i> Ana Sayfa
            </a>
            <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
            
            @if($product->category)
                <a href="{{ route('home', ['category' => $product->category->slug ?? $product->category_id]) }}" class="hover:text-slate-900 transition-colors font-medium">
                    {{ $product->category->name }}
                </a>
                <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
            @else
                <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors font-medium">Medikal Ürünler</a>
                <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
            @endif

            @if($product->brand)
                <a href="{{ route('home', ['brand' => $product->brand->slug ?? $product->brand_id]) }}" class="hover:text-slate-900 transition-colors font-medium">
                    {{ $product->brand->name }}
                </a>
                <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
            @endif

            <span class="text-slate-700 font-semibold truncate max-w-[280px] sm:max-w-[450px]" title="{{ $product->name }}">
                {{ $product->name }}
            </span>
        </nav>

        <!-- Top Trust Notification Bar -->
        <div class="mb-6 bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200/70 rounded-xl p-3 sm:px-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-slate-700 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <i class="fas fa-shield-halved text-xs"></i>
                </div>
                <div class="leading-tight">
                    <strong class="text-emerald-950 font-bold block sm:inline">%100 Orijinal Medikal Güvence:</strong>
                    <span class="text-slate-600 sm:ml-1">T.C. Sağlık Bakanlığı ÜTS kayıtlı, adınıza resmi e-faturalı ve hızlı güvenli teslimat.</span>
                </div>
            </div>
            <a href="#medical-trust" class="inline-flex items-center gap-1.5 font-semibold text-emerald-700 hover:text-emerald-900 transition-colors shrink-0 bg-white/80 px-3 py-1.5 rounded-lg border border-emerald-200 shadow-2xs">
                <span>Mağaza Güvenceleri</span>
                <i class="fas fa-arrow-down text-[10px]"></i>
            </a>
        </div>

        <!-- Product Core Details: 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12" x-data="{ activeImage: '{{ $mainImage }}' }">
            
            <!-- LEFT: Gallery & Visual Verification (5 cols) -->
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-28 space-y-4">
                    
                    <div class="flex flex-col-reverse sm:flex-row gap-4">
                        <!-- Thumbnails -->
                        @if($product->productImages->count() > 1 || $product->youtube_embed_url)
                            <div class="flex sm:flex-col gap-2.5 overflow-x-auto sm:overflow-y-auto max-h-[500px] no-scrollbar shrink-0 pb-2 sm:pb-0">
                                @foreach($product->productImages as $image)
                                    <button type="button" 
                                            @click="activeImage = '{{ $image->url }}'" 
                                            :class="activeImage === '{{ $image->url }}' ? 'border-emerald-600 ring-2 ring-emerald-100' : 'border-slate-200 hover:border-slate-300'"
                                            class="w-16 h-16 sm:w-20 sm:h-20 bg-white border rounded-xl cursor-pointer p-1.5 flex items-center justify-center transition-all bg-slate-50/50 shrink-0">
                                        <img src="{{ $image->url }}" class="w-full h-full object-contain" alt="{{ $product->name }} küçük resim">
                                    </button>
                                @endforeach

                                @if($product->youtube_embed_url)
                                    <button type="button" @click="videoModalOpen = true" 
                                            class="w-16 h-16 sm:w-20 sm:h-20 bg-red-50 border border-red-200 rounded-xl cursor-pointer p-1.5 flex flex-col items-center justify-center text-red-600 hover:bg-red-100 transition-all shrink-0 group">
                                        <i class="fab fa-youtube text-2xl group-hover:scale-110 transition-transform"></i>
                                        <span class="text-[10px] font-bold mt-0.5">Video</span>
                                    </button>
                                @endif
                            </div>
                        @endif

                        <!-- Main Image Display Container with Zoom -->
                        <div class="flex-grow bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-8 relative group shadow-sm flex items-center justify-center min-h-[380px] sm:min-h-[460px] overflow-hidden"
                             @mousemove="zoom = true; handleZoom($event)" @mouseleave="zoom = false"
                             x-data="{ 
                                zoom: false, zoomX: 0, zoomY: 0,
                                handleZoom(e) {
                                    const rect = e.currentTarget.getBoundingClientRect();
                                    this.zoomX = ((e.clientX - rect.left) / rect.width) * 100;
                                    this.zoomY = ((e.clientY - rect.top) / rect.height) * 100;
                                }
                             }">

                            <!-- Medical Badge on Image -->
                            <div class="absolute top-3 left-3 z-20 flex flex-col gap-1.5 pointer-events-none">
                                <span class="inline-flex items-center gap-1.5 bg-emerald-700/95 text-white text-[11px] font-bold px-2.5 py-1 rounded-md shadow-sm backdrop-blur-xs">
                                    <i class="fas fa-check-circle text-[10px]"></i> Orijinal Medikal Ürün
                                </span>
                            </div>

                            @if($product->youtube_embed_url)
                                <button type="button" @click="videoModalOpen = true" 
                                        class="absolute top-3 right-3 z-20 inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg shadow-sm transition-transform hover:scale-105">
                                    <i class="fab fa-youtube text-sm"></i>
                                    <span>Tanıtım Videosu</span>
                                </button>
                            @endif

                            <!-- Image -->
                            <img :src="activeImage" 
                                 :style="zoom ? `transform: scale(2.2); transform-origin: ${zoomX}% ${zoomY}%;` : ''"
                                 class="max-h-[380px] w-auto object-contain transition-transform duration-100" 
                                 alt="{{ $product->name }}">

                            <!-- Zoom indicator -->
                            <div x-show="!zoom" class="absolute bottom-3 right-3 bg-white/90 border border-slate-200 rounded-lg px-2 py-1 text-[11px] text-slate-500 flex items-center gap-1 shadow-xs pointer-events-none opacity-80 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-search-plus text-slate-400"></i> Yakınlaştırmak için üzerine gelin
                            </div>
                        </div>
                    </div>

                    <!-- Medical Quality Guarantee Reassurance Card -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 flex items-start gap-3.5 text-xs text-slate-600">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 font-bold">
                            <i class="fas fa-shield-halved text-sm"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                <span>Resmi Medikal Ürün Güvencesi</span>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] px-1.5 py-0.2 rounded font-semibold">T.C. Sağlık Bakanlığı Kayıtlı</span>
                            </h4>
                            <p class="text-slate-600 leading-relaxed">
                                Satışa sunulan tüm ürünlerimiz üretici/ithalatçı güvencesiyle adınıza düzenlenen resmi fatura ile gönderilmektedir.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT: Product Info, Price & Actions (7 cols) -->
            <div class="lg:col-span-7 flex flex-col space-y-6">
                
                <!-- Product Header Info -->
                <div class="space-y-3">
                    
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <!-- Brand badge -->
                        <div class="flex items-center gap-2">
                            @if($product->brand)
                                <a href="{{ route('home', ['brand' => $product->brand->slug ?? $product->brand_id]) }}" 
                                   class="text-xs font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-md border border-emerald-200 transition-colors">
                                    {{ $product->brand->name }}
                                </a>
                            @else
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                    Medikal Sağlık
                                </span>
                            @endif

                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 bg-slate-100/80 px-2.5 py-1 rounded-md border border-slate-200">
                                <i class="fas fa-circle-check text-emerald-600 text-xs"></i>
                                Yetkili Bayi
                            </span>
                        </div>

                        <!-- Product Code & Stock status badge -->
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-slate-400 font-mono">Ürün Kodu: <strong class="text-slate-700 font-sans">{{ $product->sku ?: $product->id }}</strong></span>
                            @if($product->barcode)
                                <span class="text-slate-300">|</span>
                                <span class="text-slate-400 font-mono">Barkod: <strong class="text-slate-700 font-sans">{{ $product->barcode }}</strong></span>
                            @endif
                        </div>
                    </div>

                    <!-- Main Product Title -->
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-900 leading-snug tracking-tight">
                        {{ $product->name }}
                    </h1>

                    <!-- Rating & Views Bar (Clean, no 'Satıcı' text) -->
                    <div class="flex flex-wrap items-center gap-4 pt-1 text-xs">
                        @if($commentCount > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex text-amber-400 text-sm">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="{{ $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                                <span class="font-bold text-slate-800">{{ number_format($avgRating, 1) }}</span>
                                <a href="#product-tabs-section" @click="activeTab = 'comments'" class="text-emerald-700 hover:underline font-medium">
                                    ({{ $commentCount }} Müşteri Yorumu)
                                </a>
                            </div>
                        @else
                            <div class="flex items-center gap-2 text-slate-500">
                                <div class="flex text-slate-300 text-sm">
                                    @for($i=1; $i<=5; $i++)
                                        <i class="far fa-star"></i>
                                    @endfor
                                </div>
                                <span>Henüz değerlendirme yapılmadı</span>
                                <span class="text-slate-300">•</span>
                                <a href="#product-tabs-section" @click="activeTab = 'comments'" class="text-emerald-700 hover:underline font-medium">
                                    İlk değerlendiren siz olun
                                </a>
                            </div>
                        @endif

                        <span class="text-slate-300 hidden sm:inline">|</span>

                        <div class="flex items-center gap-1.5 text-slate-500">
                            <i class="far fa-eye text-slate-400"></i>
                            <span>{{ number_format($product->views) }} kez incelendi</span>
                        </div>
                    </div>
                </div>

                <!-- Price and Purchase Box -->
                <div class="bg-slate-50/70 border-2 border-slate-200/90 rounded-2xl p-5 sm:p-6 space-y-5 shadow-xs">
                    
                    <!-- Pricing Header -->
                    <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-3 pb-4 border-b border-slate-200">
                        <div>
                            <div class="text-xs text-slate-500 font-medium mb-1">Satış Fiyatı (KDV Dahil)</div>
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                                    {{ number_format($product->price, 2, ',', '.') }} <span class="text-xl sm:text-2xl font-bold text-slate-700">TL</span>
                                </span>
                            </div>
                        </div>

                        <!-- EFT Discount Box if active -->
                        @if($product->eft_discount)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl px-3.5 py-2 text-right">
                                <div class="text-[11px] font-bold text-emerald-800 uppercase tracking-wide">Havale / EFT İle Ek %5 İndirim</div>
                                <div class="text-base font-bold text-emerald-700">
                                    {{ number_format($product->price * 0.95, 2, ',', '.') }} TL
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Stock & Shipping Reassurance Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <!-- Stock -->
                        <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-white border border-slate-200/80">
                            @if($product->stock > 0)
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                <div>
                                    <span class="font-bold text-emerald-800">Stokta Var</span>
                                    <span class="text-slate-500 block text-[11px]">
                                        @if($product->stock <= 10)
                                            Son {{ $product->stock }} adet kaldı
                                        @else
                                            Hızlı kargo için hazır
                                        @endif
                                    </span>
                                </div>
                            @else
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                <div>
                                    <span class="font-bold text-red-700">Tükendi</span>
                                    <span class="text-slate-500 block text-[11px]">Stok gelince haber ver</span>
                                </div>
                            @endif
                        </div>

                        <!-- Same Day Cargo -->
                        <div class="flex items-center gap-2.5 p-2.5 rounded-lg bg-white border border-slate-200/80">
                            <i class="fas fa-truck-fast text-emerald-600 text-base"></i>
                            <div>
                                <span class="font-bold text-slate-800">Aynı Gün Kargo</span>
                                <span class="text-slate-500 block text-[11px]">16:00'a kadar verilen siparişlerde</span>
                            </div>
                        </div>
                    </div>

                    <!-- Badges (Free shipping & Points) -->
                    <div class="flex flex-wrap gap-2">
                        @if($product->free_shipping || $product->price >= 700)
                            <div class="inline-flex items-center gap-1.5 bg-emerald-100/80 text-emerald-900 border border-emerald-200 text-xs font-semibold px-3 py-1 rounded-full">
                                <i class="fas fa-check-circle text-emerald-700 text-xs"></i>
                                <span>Bu Üründe <strong>Kargo Ücretsiz!</strong></span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-900 border border-amber-200 text-xs font-semibold px-3 py-1 rounded-full">
                                <i class="fas fa-info-circle text-amber-700 text-xs"></i>
                                <span>700 TL ve Üzeri Siparişlerde <strong>Kargo Bedava</strong></span>
                            </div>
                        @endif

                        @if($product->earned_points > 0)
                            <div class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-900 border border-blue-200 text-xs font-semibold px-3 py-1 rounded-full">
                                <i class="fas fa-coins text-blue-600 text-xs"></i>
                                <span>+{{ $product->earned_points }} Med Puan Kazandırır</span>
                            </div>
                        @endif
                    </div>

                    <!-- Quantity and Action Buttons -->
                    @php $imgArr = $product->productImages->first()?->url ?? $mainImage; @endphp
                    <div class="pt-2 space-y-3">
                        
                        <div class="flex flex-col sm:flex-row gap-3">
                            
                            <!-- Quantity selector -->
                            @if($product->stock > 0)
                                <div class="flex items-center border-2 border-slate-300 bg-white rounded-xl h-14 shrink-0 px-2 justify-between w-full sm:w-36">
                                    <button type="button" @click="selectedQty = Math.max(1, selectedQty - 1)" 
                                            class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors font-bold text-lg">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="font-bold text-slate-900 text-base w-8 text-center" x-text="selectedQty">1</span>
                                    <button type="button" @click="selectedQty = Math.min({{ $product->stock }}, selectedQty + 1)" 
                                            class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors font-bold text-lg">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>

                                <!-- Add to cart button -->
                                <button type="button" 
                                        @click="for(let i=0; i<selectedQty; i++){ $store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $imgArr }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}, eft_discount: {{ $product->eft_discount ? 'true' : 'false' }}}) }" 
                                        class="flex-grow h-14 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-base rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                                    <i class="fas fa-shopping-basket text-lg"></i>
                                    <span>SEPETE EKLE</span>
                                </button>
                            @else
                                <button disabled class="flex-grow h-14 bg-slate-300 text-slate-600 font-bold text-base rounded-xl flex items-center justify-center gap-3 cursor-not-allowed">
                                    <i class="fas fa-times-circle text-lg"></i>
                                    <span>BU ÜRÜN ŞU AN STOKTA YOK</span>
                                </button>
                            @endif

                            <!-- Favorite button -->
                            <button type="button" 
                                    @click="$store.fav.toggle({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $imgArr }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}})" 
                                    class="h-14 w-full sm:w-14 border-2 border-slate-300 bg-white hover:border-red-300 rounded-xl flex items-center justify-center transition-all shrink-0 active:scale-95"
                                    :class="$store.fav.has('{{ $product->id }}') ? 'text-red-500 border-red-200 bg-red-50' : 'text-slate-400 hover:text-red-500'"
                                    title="Favorilere Ekle">
                                <i :class="$store.fav.has('{{ $product->id }}') ? 'fas fa-heart text-xl text-red-500' : 'far fa-heart text-xl'"></i>
                                <span class="sm:hidden ml-2 font-bold text-xs" x-text="$store.fav.has('{{ $product->id }}') ? 'Favorilerinizde' : 'Favorilere Ekle'"></span>
                            </button>
                        </div>

                        <!-- Direct WhatsApp Quick Order Button -->
                        <div class="pt-1">
                            <a href="{{ $waLink }}" target="_blank" 
                               class="w-full h-12 bg-white hover:bg-emerald-50 border-2 border-emerald-600 text-emerald-800 font-bold text-xs sm:text-sm rounded-xl transition-all flex items-center justify-center gap-2 shadow-2xs hover:shadow-sm">
                                <i class="fab fa-whatsapp text-emerald-600 text-lg"></i>
                                <span>WhatsApp ile Hızlı Sipariş Ver & Uzmana Danış</span>
                            </a>
                        </div>

                    </div>

                    <!-- Medical Phone Support Note -->
                    <div class="flex items-center justify-between text-xs text-slate-500 pt-2 border-t border-slate-200">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-headset text-slate-400"></i>
                            <span>Beden veya Ürün Danışma Hattı:</span>
                        </div>
                        <a href="tel:{{ $contactPhone }}" class="font-bold text-slate-800 hover:text-emerald-700 transition-colors">
                            {{ $contactPhone }}
                        </a>
                    </div>
                </div>

                <!-- 4 Pillars Medical Trust Matrix -->
                <div id="medical-trust" class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                    
                    <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-center flex flex-col items-center gap-2 shadow-2xs hover:border-emerald-300 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-900 leading-snug">ÜTS & Orijinal Ürün</div>
                        <div class="text-[11px] text-slate-500 leading-tight">Sağlık Bakanlığı kayıtlı %100 orijinal</div>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-center flex flex-col items-center gap-2 shadow-2xs hover:border-emerald-300 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-900 leading-snug">Güvenli Ödeme</div>
                        <div class="text-[11px] text-slate-500 leading-tight">256-Bit SSL & 3D Secure güvencesi</div>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-center flex flex-col items-center gap-2 shadow-2xs hover:border-emerald-300 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-900 leading-snug">SGK Uyumlu Fatura</div>
                        <div class="text-[11px] text-slate-500 leading-tight">Adınıza resmi e-fatura düzenlenir</div>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-center flex flex-col items-center gap-2 shadow-2xs hover:border-emerald-300 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <div class="text-xs font-bold text-slate-900 leading-snug">Hızlı Teslimat</div>
                        <div class="text-[11px] text-slate-500 leading-tight">16:00'a kadar aynı gün sevk</div>
                    </div>

                </div>

                <!-- Verified Merchant & Other Official Marketplace Channels (Only rendered if custom link exists!) -->
                @if($activeMarketplaces->isNotEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wide flex items-center gap-1.5">
                                <i class="fas fa-store text-slate-400"></i> Diğer Pazar Yeri Mağazalarımız
                            </span>
                            <span class="text-[11px] text-slate-400">Resmi Mağazalarımız</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($activeMarketplaces as $mt)
                                <a href="{{ $customUrls[$mt['name']] }}" target="_blank" rel="noopener noreferrer" 
                                   class="inline-flex items-center gap-2 bg-slate-50 hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 transition-colors">
                                    @if(!empty($mt['logo']))
                                        <img src="{{ $mt['logo'] }}" alt="{{ $mt['name'] }}" class="w-4 h-4 object-contain">
                                    @endif
                                    <span>{{ $mt['name'] }}</span>
                                    <i class="fas fa-external-link-alt text-[9px] text-slate-400"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- Comprehensive Details & Medical Tabs Section -->
        <div class="mt-16 bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden" id="product-tabs-section">
            
            <!-- Tabs Header (Smooth navigation without browser scrollbar) -->
            <div class="flex border-b border-slate-200 bg-slate-50/70 overflow-x-auto no-scrollbar tab-scroll-container">
                <button type="button" @click="activeTab = 'features'" 
                        :class="activeTab === 'features' ? 'border-emerald-600 text-emerald-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-6 py-4 text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-list-check"></i>
                    <span>Ürün Özellikleri & Detaylar</span>
                </button>

                @if($isMedicalDiaper)
                    <button type="button" @click="activeTab = 'sizeguide'" 
                            :class="activeTab === 'sizeguide' ? 'border-emerald-600 text-emerald-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                            class="px-6 py-4 text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                        <i class="fas fa-ruler-combined"></i>
                        <span>Beden & Ölçü Rehberi</span>
                    </button>
                @endif

                <button type="button" @click="activeTab = 'shipping'" 
                        :class="activeTab === 'shipping' ? 'border-emerald-600 text-emerald-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-6 py-4 text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-truck-fast"></i>
                    <span>Kargo & Teslimat Süreçleri</span>
                </button>

                <button type="button" @click="activeTab = 'returns'" 
                        :class="activeTab === 'returns' ? 'border-emerald-600 text-emerald-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-6 py-4 text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-rotate-left"></i>
                    <span>İade & Değişim Şartları</span>
                </button>

                <button type="button" @click="activeTab = 'comments'" 
                        :class="activeTab === 'comments' ? 'border-emerald-600 text-emerald-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-6 py-4 text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-comments"></i>
                    <span>Değerlendirmeler ({{ $commentCount }})</span>
                </button>

                <button type="button" @click="activeTab = 'faq'" 
                        :class="activeTab === 'faq' ? 'border-emerald-600 text-emerald-800 bg-white font-bold' : 'border-transparent text-slate-600 hover:text-slate-900 font-medium'"
                        class="px-6 py-4 text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors flex items-center gap-2">
                    <i class="fas fa-circle-question"></i>
                    <span>Sıkça Sorulan Sorular</span>
                </button>
            </div>

            <!-- Tab 1: Product Features & Description -->
            <div x-show="activeTab === 'features'" class="p-6 sm:p-10 space-y-8">
                
                <!-- Authentic Product Description -->
                <div class="prose max-w-none text-slate-700 leading-relaxed text-sm sm:text-base space-y-4">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-emerald-600"></i>
                        <span>Ürün Bilgileri ve Tanıtımı</span>
                    </h3>
                    
                    <div class="bg-slate-50 border border-slate-200/90 rounded-xl p-5 text-slate-800 leading-relaxed break-words">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>

                <!-- Structured Highlights for Adult Diaper / Incontinence Products -->
                @if($isMedicalDiaper)
                    <div class="space-y-4">
                        <h4 class="text-base font-bold text-slate-900">Medikal & Hijyenik Ürün Avantajları</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base">
                                    <i class="fas fa-droplet"></i>
                                </div>
                                <h5 class="font-bold text-slate-900 text-sm">Yüksek Emicilik & Hızlı Emilim</h5>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Gelişmiş polimer teknolojisi sayesinde sıvıyı saniyeler içinde alt tabakaya aktararak hapseder ve cildi sürekli kuru tutar.
                                </p>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base">
                                    <i class="fas fa-wind"></i>
                                </div>
                                <h5 class="font-bold text-slate-900 text-sm">Nefes Alan Pamuksu Dış Yüzey</h5>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Hava geçiren tekstil dış yüzey, cildin doğal nem dengesini korur; pişik, kızarıklık ve tahriş riskini minimize eder.
                                </p>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                                <div class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-base">
                                    <i class="fas fa-shield-virus"></i>
                                </div>
                                <h5 class="font-bold text-slate-900 text-sm">Koku Nötralize Edici Teknoloji</h5>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Odor-Lock koku hapsetme bariyerleri istenmeyen kokuları nötralize ederek gün boyu hijyenik ve güvenli bir kullanım sunar.
                                </p>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-base">
                                    <i class="fas fa-water"></i>
                                </div>
                                <h5 class="font-bold text-slate-900 text-sm">Sızdırmaz Esnek Yan Bariyerler</h5>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Yüksek hidrofobik yan bariyerler ve esnek bacak manşetleri hareket halinde veya yatış pozisyonunda sızdırmayı önler.
                                </p>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                                <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-base">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <h5 class="font-bold text-slate-900 text-sm">Pratik Islaklık Göstergesi</h5>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Renk değiştiren gösterge çizgileri sayesinde bezin doluluk oranı dışarıdan kolayca kontrol edilir, gereksiz değişimler önlenir.
                                </p>
                            </div>

                            <div class="bg-white border border-slate-200 rounded-xl p-4 space-y-2">
                                <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-base">
                                    <i class="fas fa-shirt"></i>
                                </div>
                                <h5 class="font-bold text-slate-900 text-sm">İç Çamaşırı Konforunda Kullanım</h5>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Normal iç çamaşırı gibi kolayca giyilir; değişim sırasında yan dikişlerinden yırtılarak hijyenik şekilde çıkartılır.
                                </p>
                            </div>

                        </div>
                    </div>
                @endif

                <!-- Product Specifications Table -->
                <div class="space-y-4">
                    <h4 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-table-list text-emerald-600"></i>
                        <span>Teknik Özellikler & Ürün Detayları</span>
                    </h4>

                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
                        <table class="w-full text-left text-xs sm:text-sm">
                            <tbody class="divide-y divide-slate-200">
                                <tr class="bg-slate-50/80">
                                    <th class="py-3 px-4 font-semibold text-slate-700 w-1/3">Marka</th>
                                    <td class="py-3 px-4 text-slate-900 font-bold">{{ $product->brand->name ?? 'Giggles' }}</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-4 font-semibold text-slate-700 bg-slate-50/40">Ürün Tipi / Çeşit</th>
                                    <td class="py-3 px-4 text-slate-900">Emici Külot Hasta Bezi / Medikal Hijyen</td>
                                </tr>
                                @if(str_contains(mb_strtolower($product->name), 'xl') || str_contains(mb_strtolower($product->name), 'ekstra büyük'))
                                    <tr class="bg-slate-50/80">
                                        <th class="py-3 px-4 font-semibold text-slate-700">Beden / Boy</th>
                                        <td class="py-3 px-4 text-slate-900 font-bold text-emerald-700">XL (Extra Large - Ekstra Büyük Boy)</td>
                                    </tr>
                                    <tr>
                                        <th class="py-3 px-4 font-semibold text-slate-700 bg-slate-50/40">Uygun Bel Çevresi</th>
                                        <td class="py-3 px-4 text-slate-900 font-semibold">130 cm - 170 cm</td>
                                    </tr>
                                @elseif(str_contains(mb_strtolower($product->name), 'large') || str_contains(mb_strtolower($product->name), ' l ') || str_contains(mb_strtolower($product->name), 'büyük boy'))
                                    <tr class="bg-slate-50/80">
                                        <th class="py-3 px-4 font-semibold text-slate-700">Beden / Boy</th>
                                        <td class="py-3 px-4 text-slate-900 font-bold text-emerald-700">L (Large - Büyük Boy)</td>
                                    </tr>
                                    <tr>
                                        <th class="py-3 px-4 font-semibold text-slate-700 bg-slate-50/40">Uygun Bel Çevresi</th>
                                        <td class="py-3 px-4 text-slate-900 font-semibold">100 cm - 150 cm</td>
                                    </tr>
                                @elseif(str_contains(mb_strtolower($product->name), 'medium') || str_contains(mb_strtolower($product->name), ' m ') || str_contains(mb_strtolower($product->name), 'orta boy'))
                                    <tr class="bg-slate-50/80">
                                        <th class="py-3 px-4 font-semibold text-slate-700">Beden / Boy</th>
                                        <td class="py-3 px-4 text-slate-900 font-bold text-emerald-700">M (Medium - Orta Boy)</td>
                                    </tr>
                                    <tr>
                                        <th class="py-3 px-4 font-semibold text-slate-700 bg-slate-50/40">Uygun Bel Çevresi</th>
                                        <td class="py-3 px-4 text-slate-900 font-semibold">80 cm - 120 cm</td>
                                    </tr>
                                @endif

                                @if(str_contains(mb_strtolower($product->name), '4 adet') || str_contains(mb_strtolower($product->name), '4 paket') || str_contains(mb_strtolower($product->name), '30\'lu 4'))
                                    <tr class="bg-slate-50/80">
                                        <th class="py-3 px-4 font-semibold text-slate-700">Paketleme & Adet</th>
                                        <td class="py-3 px-4 text-slate-900 font-bold">4 Paket x 30 Adet = Toplam 120 Adet</td>
                                    </tr>
                                @endif

                                @foreach($product->productAttributes as $attr)
                                    <tr class="{{ $loop->even ? 'bg-slate-50/80' : '' }}">
                                        <th class="py-3 px-4 font-semibold text-slate-700">{{ $attr->name }}</th>
                                        <td class="py-3 px-4 text-slate-900">{!! $attr->value !!}</td>
                                    </tr>
                                @endforeach

                                <tr class="bg-slate-50/80">
                                    <th class="py-3 px-4 font-semibold text-slate-700">Kullanım Amacı</th>
                                    <td class="py-3 px-4 text-slate-900">Yoğun İdrar ve Sıvı Kaçırma Koruma / Yatan ve Ayaktaki Hastalar</td>
                                </tr>
                                <tr>
                                    <th class="py-3 px-4 font-semibold text-slate-700 bg-slate-50/40">Dermatolojik Durum</th>
                                    <td class="py-3 px-4 text-slate-900">Dermatolojik Olarak Test Edilmiş, Hipoalerjenik</td>
                                </tr>
                                <tr class="bg-slate-50/80">
                                    <th class="py-3 px-4 font-semibold text-slate-700">Menşei</th>
                                    <td class="py-3 px-4 text-slate-900">Türkiye (Yerli Üretim)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- SGK and Official Medical Invoice Notice -->
                <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-5 text-xs text-amber-950 space-y-2">
                    <div class="font-bold text-sm text-amber-900 flex items-center gap-2">
                        <i class="fas fa-file-medical text-amber-700"></i>
                        <span>SGK Hasta Bezi Geri Ödeme & Resmi Fatura Bilgilendirmesi</span>
                    </div>
                    <p class="leading-relaxed text-slate-700">
                        Hasta bezi ve medikal sarf malzemesi raporu bulunan hastalarımız için siparişlerinizde hastanızın T.C. Kimlik Numarası ve isim bilgilerine uygun resmi e-fatura düzenlenmektedir. Düzenlenen e-fatura ile SGK İl Müdürlüklerine başvurarak geri ödeme süreçlerinizi sorunsuz şekilde gerçekleştirebilirsiniz. Fatura bilgisi için sipariş aşamasında not bırakabilir veya bizimle iletişime geçebilirsiniz.
                    </p>
                </div>

            </div>

            <!-- Tab 2: Size Guide (Beden & Ölçü Rehberi) -->
            @if($isMedicalDiaper)
                <div x-show="activeTab === 'sizeguide'" class="p-6 sm:p-10 space-y-8">
                    <div class="space-y-3">
                        <h3 class="text-lg font-bold text-slate-900">Hasta Bezi & Emici Külot Beden Tablosu</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Doğru beden seçimi, hasta bezi kullanımında sızdırmazlık ve cilt sağlığı için en kritik unsurdur. Küçük beden vücudu sıkarak tahrişe sebep olabilir; gereğinden büyük beden ise kenarlardan sızdırma yapabilir. Lütfen hastanızın bel/göbek çevresini mezura ile ölçerek aşağıdaki tablodan uygun bedeni seçiniz:
                        </p>
                    </div>

                    <!-- Size Chart -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        <div class="bg-white border-2 {{ str_contains(mb_strtolower($product->name), 'medium') || str_contains(mb_strtolower($product->name), 'orta') ? 'border-emerald-600 ring-2 ring-emerald-100' : 'border-slate-200' }} rounded-xl p-5 text-center space-y-3">
                            <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 font-bold text-xs rounded-full">Medium (M)</span>
                            <div class="text-2xl font-black text-slate-900">80 - 120 cm</div>
                            <div class="text-xs text-slate-500 font-medium">Orta Boy Bel Ölçüsü</div>
                            <p class="text-[11px] text-slate-600 leading-normal border-t border-slate-100 pt-3">
                                50 - 75 kg arası bireyler için genellikle uygundur.
                            </p>
                        </div>

                        <div class="bg-white border-2 {{ str_contains(mb_strtolower($product->name), 'large') && !str_contains(mb_strtolower($product->name), 'extra') ? 'border-emerald-600 ring-2 ring-emerald-100' : 'border-slate-200' }} rounded-xl p-5 text-center space-y-3">
                            <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 font-bold text-xs rounded-full">Large (L)</span>
                            <div class="text-2xl font-black text-slate-900">100 - 150 cm</div>
                            <div class="text-xs text-slate-500 font-medium">Büyük Boy Bel Ölçüsü</div>
                            <p class="text-[11px] text-slate-600 leading-normal border-t border-slate-100 pt-3">
                                70 - 100 kg arası bireyler için en sık tercih edilen bedendir.
                            </p>
                        </div>

                        <div class="bg-white border-2 {{ str_contains(mb_strtolower($product->name), 'xl') || str_contains(mb_strtolower($product->name), 'ekstra') ? 'border-emerald-600 ring-2 ring-emerald-100' : 'border-slate-200' }} rounded-xl p-5 text-center space-y-3">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-full">Extra Large (XL)</span>
                                @if(str_contains(mb_strtolower($product->name), 'xl') || str_contains(mb_strtolower($product->name), 'ekstra'))
                                    <span class="text-[10px] font-bold bg-emerald-600 text-white px-2 py-0.5 rounded">Bu Ürünün Bedeni</span>
                                @endif
                            </div>
                            <div class="text-2xl font-black text-emerald-800">130 - 170 cm</div>
                            <div class="text-xs text-slate-500 font-medium">Ekstra Büyük Boy Bel Ölçüsü</div>
                            <p class="text-[11px] text-slate-600 leading-normal border-t border-slate-100 pt-3">
                                95 kg ve üzeri geniş bel ölçüsüne sahip bireyler için uygundur.
                            </p>
                        </div>

                    </div>

                    <!-- Measurement Tips -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-3 text-xs text-slate-700">
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fas fa-question-circle text-emerald-600"></i>
                            <span>Doğru Ölçüm Nasıl Yapılır?</span>
                        </h4>
                        <ol class="list-decimal list-inside space-y-1.5 leading-relaxed text-slate-600">
                            <li>Hastanız yatar veya ayakta pozisyondayken göbek deliği hizasından mezura ile bel çevresini sarınız.</li>
                            <li>Mezurayı fazla sıkmadan ve aşırı bol bırakmadan çıkan ölçüyü yukarıdaki santimetre tablosuyla eşleştiriniz.</li>
                            <li>Beden konusunda tereddüt ederseniz WhatsApp destek hattımızdan uzman medikal ekibimize danışabilirsiniz.</li>
                        </ol>
                    </div>
                </div>
            @endif

            <!-- Tab 3: Shipping & Delivery Information -->
            <div x-show="activeTab === 'shipping'" class="p-6 sm:p-10 space-y-8">
                
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-truck-fast text-emerald-600"></i>
                        <span>Kargo ve Teslimat Standartlarımız</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Siparişlerinizin güvenli, hasarsız ve zamanında kapınıza ulaşması için titizlikle çalışıyoruz.
                    </p>
                </div>

                <!-- 3 Pillars Shipping Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-2.5">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-base">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Aynı Gün Sevk</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Hafta içi saat 16:00'a, Cumartesi günleri 12:00'ye kadar onaylanan siparişleriniz aynı gün kargoya teslim edilir.
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-2.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-base">
                            <i class="fas fa-truck-ramp-box"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Güvenli ve Sağlam Paketleme</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Ürünler taşıma esnasında ezilme, yırtılma veya ıslanmaya karşı dayanıklı çift oluklu korumalı kolilerde sevk edilir.
                        </p>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-2.5">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-base">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 text-xs sm:text-sm">Anlık Kargo Takibi</h4>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Siparişiniz kargoya verildikten sonra kargo takip kodunuz SMS ve e-posta ile tarafınıza iletilir.
                        </p>
                    </div>

                </div>

                <!-- Shipping Time and Courier Partners -->
                <div class="bg-emerald-50/50 border border-emerald-200 rounded-xl p-5 space-y-3 text-xs text-slate-700">
                    <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fas fa-circle-info text-emerald-700"></i>
                        <span>Teslimat Süreleri & Anlaşmalı Kargo Ağlarımız:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-600 leading-relaxed">
                        <li>Türkiye geneli teslimatlarımız Yurtiçi Kargo ve MNG Kargo güvencesiyle 1-3 iş günü içerisinde adresinize ulaştırılmaktadır.</li>
                        <li>700 TL ve üzeri tüm alışverişlerinizde kargo ücretsizdir.</li>
                        <li>Kargonuzu teslim alırken dış ambalajda hasar olması durumunda kargo görevlisine tutanak tutturabilirsiniz.</li>
                    </ul>
                </div>

            </div>

            <!-- Tab 4: Returns & Hygiene Policy -->
            <div x-show="activeTab === 'returns'" class="p-6 sm:p-10 space-y-6">
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-shield-halved text-emerald-600"></i>
                        <span>{{ $product->returnTemplate ? $product->returnTemplate->name : 'İade ve Değişim Koşulları' }}</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Tüketici Hakları mevzuatı ve Sağlık Bakanlığı Medikal Hijyen Standartları uyarınca uygulanan iade süreçleri:
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-3">
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fas fa-check text-emerald-600"></i>
                            <span>14 Gün İade Hakkı & Şartlar</span>
                        </h4>
                        
                        @if($product->returnTemplate)
                            <div class="space-y-2 text-xs text-slate-700 leading-relaxed">
                                {!! nl2br(e($product->returnTemplate->content)) !!}
                            </div>
                        @else
                            <ul class="space-y-2 text-xs text-slate-700 leading-relaxed">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span>Teslim aldığınız tarihten itibaren 14 gün içinde koşulsuz iade talebi oluşturabilirsiniz.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span><strong>Sağlık & Hijyen Kuralı:</strong> Hasta bezi, medikal sarf malzemeleri ve kişisel hijyen ürünlerinin koruma ambalajının açılmamış, yırtılmamış ve tekrar satılabilir durumda olması kanunen zorunludur.</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span>İade kargoları anlaşmalı kargo kodumuz ile ücretsiz olarak gönderilebilir.</span>
                                </li>
                            </ul>
                        @endif
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-3">
                        <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                            <i class="fas fa-list-ol text-emerald-600"></i>
                            <span>İade Süreci Nasıl İşler?</span>
                        </h4>
                        <ol class="space-y-3 text-xs text-slate-700">
                            <li class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">1</span>
                                <div><strong>Talep Oluşturun:</strong> Hesabım > Siparişlerim sayfasından iade talebi başlatın veya müşteri hizmetlerimize bildirin.</div>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">2</span>
                                <div><strong>Ücretsiz Gönderin:</strong> Size iletilen iade kargo kodu ile paketi en yakın kargo şubesine teslim edin.</div>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">3</span>
                                <div><strong>Hızlı Ücret İadesi:</strong> Ürün depomuza ulaşıp kontrol edildikten sonra 1-3 iş günü içinde kartınıza/hesabınıza iadeniz gerçekleştirilir.</div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Tab 5: Real Reviews and Feedback Form -->
            <div x-show="activeTab === 'comments'" class="p-6 sm:p-10 space-y-8">
                
                @php
                    $distribution = [];
                    for($i=5; $i>=1; $i--) {
                        $distribution[$i] = $commentCount > 0 ? ($comments->where('rating', $i)->count() / $commentCount) * 100 : 0;
                    }
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- Rating Summary & Comment Form (4 cols) -->
                    <div class="lg:col-span-5 space-y-6">
                        
                        <!-- Summary Card -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-6 space-y-4">
                            <h4 class="font-bold text-slate-900 text-sm">Müşteri Değerlendirmeleri</h4>
                            
                            <div class="flex items-center gap-4">
                                <div class="text-4xl font-extrabold text-slate-900">
                                    {{ $avgRating ? number_format($avgRating, 1) : '-' }}
                                </div>
                                <div>
                                    <div class="flex text-amber-400 text-sm">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="{{ $avgRating && $i <= round($avgRating) ? 'fas' : 'far' }} fa-star"></i>
                                        @endfor
                                    </div>
                                    <div class="text-xs text-slate-500 font-medium mt-0.5">
                                        {{ $commentCount }} Onaylı Değerlendirme
                                    </div>
                                </div>
                            </div>

                            <!-- Star breakdown -->
                            <div class="space-y-2 pt-2 border-t border-slate-200 text-xs">
                                @foreach($distribution as $star => $percent)
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 text-slate-600 font-medium text-[11px]">{{ $star }}</span>
                                        <i class="fas fa-star text-[10px] text-amber-400"></i>
                                        <div class="flex-grow h-2 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <span class="w-8 text-right text-[11px] text-slate-500 font-mono">{{ round($percent) }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Write a review box -->
                        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4 shadow-2xs">
                            <h4 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                <i class="fas fa-pen text-emerald-600"></i>
                                <span>Deneyiminizi Paylaşın</span>
                            </h4>

                            @auth
                                <form action="{{ route('comment.store', $product) }}" method="POST" class="space-y-4" x-data="{ loading: false, userRating: 5 }" @submit="loading = true">
                                    @csrf
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Puanınız:</label>
                                        <input type="hidden" name="rating" :value="userRating">
                                        <div class="flex gap-1.5">
                                            <template x-for="i in 5">
                                                <button type="button" @click="userRating = i" class="text-xl transition-transform hover:scale-110" :class="i <= userRating ? 'text-amber-400' : 'text-slate-300'">
                                                    <i :class="i <= userRating ? 'fas fa-star' : 'far fa-star'"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Yorumunuz:</label>
                                        <textarea name="content" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-slate-900 text-xs focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all placeholder:text-slate-400" placeholder="Ürün kalitesi ve teslimat hakkındaki görüşlerinizi yazın..." required :readonly="loading"></textarea>
                                    </div>

                                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-2xs" :disabled="loading">
                                        <span x-show="!loading">Yorumu Yayınlanmak Üzere Gönder</span>
                                        <span x-show="loading"><i class="fas fa-spinner animate-spin mr-1"></i> Gönderiliyor...</span>
                                    </button>
                                </form>
                            @else
                                <div class="text-center py-4 space-y-3">
                                    <p class="text-xs text-slate-600">Ürün hakkında yorum yapabilmek için lütfen giriş yapınız.</p>
                                    <a href="{{ route('login') }}" class="inline-block px-5 py-2 bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition-colors">
                                        Giriş Yap
                                    </a>
                                </div>
                            @endauth
                        </div>

                    </div>

                    <!-- Reviews List (7 cols) -->
                    <div class="lg:col-span-7 space-y-4">
                        @forelse($comments as $comment)
                            <div class="bg-slate-50/70 border border-slate-200 rounded-xl p-5 space-y-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs flex items-center justify-center">
                                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-2">
                                                <span>{{ Str::mask($comment->user->name, '*', 2, -2) }}</span>
                                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded">
                                                    <i class="fas fa-circle-check text-[9px]"></i> Onaylı Müşteri
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-0.5">
                                                <div class="flex text-amber-400 text-[10px]">
                                                    @for($i=1; $i<=5; $i++)
                                                        <i class="{{ $i <= $comment->rating ? 'fas' : 'far' }} fa-star"></i>
                                                    @endfor
                                                </div>
                                                <span>•</span>
                                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <p class="text-slate-700 text-xs sm:text-sm leading-relaxed">
                                    {{ $comment->content }}
                                </p>

                                @if($comment->admin_reply)
                                    <div class="mt-3 bg-white border-l-3 border-emerald-600 p-3 rounded-r-lg space-y-1 text-xs">
                                        <div class="flex items-center justify-between font-bold text-emerald-900 text-[11px]">
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-reply text-emerald-600"></i> {{ $siteTitle }} Yanıtı
                                            </span>
                                            <span class="text-slate-400 font-normal">{{ $comment->replied_at?->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-slate-600">{{ $comment->admin_reply }}</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="bg-slate-50 border border-dashed border-slate-300 rounded-xl p-10 text-center space-y-2">
                                <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center mx-auto text-xl">
                                    <i class="far fa-comment-dots"></i>
                                </div>
                                <h5 class="font-bold text-slate-800 text-sm">Bu ürün için henüz değerlendirme yapılmamış</h5>
                                <p class="text-xs text-slate-500 max-w-md mx-auto">
                                    Siparişiniz sonrasında deneyiminizi paylaşarak diğer hasta yakınlarına ve müşterilerimize yardımcı olabilirsiniz.
                                </p>
                            </div>
                        @endforelse
                    </div>

                </div>

            </div>

            <!-- Tab 6: Frequently Asked Questions (FAQ) -->
            <div x-show="activeTab === 'faq'" class="p-6 sm:p-10 space-y-4">
                <div class="space-y-2 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-circle-question text-emerald-600"></i>
                        <span>Medikal & Sipariş Süreçleri Hakkında Sıkça Sorulan Sorular</span>
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600">
                        Hasta bezi ve medikal ürün siparişlerinizle ilgili en çok merak edilen konular:
                    </p>
                </div>

                <div class="space-y-3" x-data="{ openFaq: null }">
                    
                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                        <button type="button" @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-slate-100/80 transition-colors">
                            <span>SGK geri ödemesi için gerekli fatura tarafıma ulaştırılıyor mu?</span>
                            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 1 ? 'rotate-180 text-emerald-600' : ''"></i>
                        </button>
                        <div x-show="openFaq === 1" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-white">
                            Evet. Siparişiniz esnasında belirttiğiniz hasta veya vasi adına düzenlenmiş, T.C. Kimlik Numarası içeren resmi e-faturanız e-posta adresinize ve kargo paketi içine iletilmektedir. Bu fatura ile SGK İl Müdürlüklerine başvurarak hasta bezi geri ödemenizi alabilirsiniz.
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                        <button type="button" @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-slate-100/80 transition-colors">
                            <span>Beden uyumsuzluğunda değişim yapabilir miyim?</span>
                            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 2 ? 'rotate-180 text-emerald-600' : ''"></i>
                        </button>
                        <div x-show="openFaq === 2" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-white">
                            Koli içerisindeki paketlerin orijinal ambalajı açılmadığı sürece 14 gün içinde değişim veya iade hakkınız bulunmaktadır. Beden konusunda emin değilseniz sipariş öncesinde WhatsApp destek hattımızdan destek alabilirsiniz.
                        </div>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-50/50">
                        <button type="button" @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-4 text-left font-bold text-xs sm:text-sm text-slate-900 flex items-center justify-between hover:bg-slate-100/80 transition-colors">
                            <span>Hangi kargo firmaları ile çalışıyorsunuz ve ne zaman teslim edilir?</span>
                            <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform" :class="openFaq === 3 ? 'rotate-180 text-emerald-600' : ''"></i>
                        </button>
                        <div x-show="openFaq === 3" class="p-4 pt-0 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-white">
                            Yurtiçi Kargo ve MNG Kargo gibi kurumsal kargo ağlarıyla çalışmaktayız. Hafta içi saat 16:00'a kadar verilen siparişler aynı gün kargoya teslim edilmekte olup, teslimat adresinize bağlı olarak 1-3 iş günü içerisinde kapınıza ulaşmaktadır.
                        </div>
                    </div>

                </div>
            </div>

        </div>

        <!-- Official Store Details & Verification Banner -->
        <div class="mt-12 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white rounded-2xl p-6 sm:p-10 shadow-lg relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 bg-emerald-600/90 text-white text-[11px] font-bold px-3 py-1 rounded-full">
                        <i class="fas fa-certificate"></i>
                        <span>T.C. Ticaret Bakanlığı ETBİS Kayıtlı Resmi İşletme</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold tracking-tight">
                        {{ $siteTitle }} Güvencesiyle Alışveriş
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Fiziki medikal mağazamız ve yetkili distribütör bağlantılarımızla tüm medikal sarf malzemelerini en taze üretim tarihleriyle, orijinal ambalajında ve resmi garantili olarak kapınıza ulaştırıyoruz.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-2 text-xs text-slate-300">
                        <span class="flex items-center gap-1.5"><i class="fas fa-location-dot text-emerald-400"></i> {{ $contactAddress }}</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-phone text-emerald-400"></i> {{ $contactPhone }}</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0">
                    <a href="{{ $etbisUrl }}" target="_blank" rel="noopener noreferrer" 
                       class="px-5 py-3 bg-white hover:bg-slate-100 text-slate-900 text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                        <i class="fas fa-shield-halved text-emerald-600"></i>
                        <span>ETBİS Doğrula</span>
                    </a>
                    <a href="{{ $waLink }}" target="_blank" 
                       class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>WhatsApp Destek</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products Section -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16 space-y-6">
                <div class="flex items-baseline justify-between border-b border-slate-200 pb-4">
                    <h3 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        <i class="fas fa-boxes-stacked text-emerald-600"></i>
                        <span>Benzer Medikal Ürünler</span>
                    </h3>
                    @if($product->category)
                        <a href="{{ route('home', ['category' => $product->category->slug ?? $product->category_id]) }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 transition-colors">
                            Kategorideki Tüm Ürünler <i class="fas fa-chevron-right text-[10px] ml-1"></i>
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($relatedProducts as $rp)
                        <div class="group bg-white border border-slate-200 hover:border-emerald-500 rounded-xl p-3.5 hover:shadow-md transition-all flex flex-col justify-between">
                            
                            <div>
                                <a href="{{ route('product.show', $rp) }}" class="block aspect-square bg-slate-50/70 rounded-lg overflow-hidden p-3 relative mb-3">
                                    <img src="{{ $rp->productImages->first()?->url ?? 'https://via.placeholder.com/300x300?text=Medikal' }}" 
                                         alt="{{ $rp->name }}" 
                                         class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                                    
                                    @if($rp->free_shipping || $rp->price >= 700)
                                        <span class="absolute top-2 left-2 bg-emerald-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded">
                                            Ücretsiz Kargo
                                        </span>
                                    @endif
                                </a>

                                <div class="text-[11px] font-bold text-emerald-700 uppercase tracking-wide mb-1">
                                    {{ $rp->brand->name ?? 'Medikal' }}
                                </div>
                                <a href="{{ route('product.show', $rp) }}" class="block text-xs font-semibold text-slate-800 hover:text-emerald-700 line-clamp-2 leading-tight transition-colors" title="{{ $rp->name }}">
                                    {{ $rp->name }}
                                </a>
                            </div>

                            <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-extrabold text-slate-900">
                                        {{ number_format($rp->price, 2, ',', '.') }} TL
                                    </div>
                                </div>
                                
                                @if($rp->stock > 0)
                                    <button type="button" 
                                            @click="$store.cart.add({id: '{{ $rp->id }}', slug: '{{ $rp->slug }}', name: '{{ addslashes($rp->name) }}', brand: '{{ addslashes($rp->brand->name ?? '') }}', price: {{ $rp->price }}, category_id: '{{ $rp->category_id }}', image: '{{ $rp->productImages->first()?->url ?? '' }}', free_shipping: {{ $rp->free_shipping ? 'true' : 'false' }}, eft_discount: {{ $rp->eft_discount ? 'true' : 'false' }}})" 
                                            class="w-8 h-8 rounded-lg bg-slate-900 hover:bg-emerald-600 text-white flex items-center justify-center transition-colors shadow-2xs"
                                            title="Sepete Ekle">
                                        <i class="fas fa-cart-plus text-xs"></i>
                                    </button>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Floating Mobile Quick Buy Bar (visible on scroll) -->
        <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-slate-200 p-3 shadow-xl"
             x-data="{ showMobileBar: false }"
             @scroll.window="showMobileBar = (window.pageYOffset > 500)"
             x-show="showMobileBar"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="translate-y-full opacity-0">
            
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <img src="{{ $mainImage }}" class="w-11 h-11 object-contain rounded-md border border-slate-200 bg-slate-50 shrink-0" alt="">
                    <div class="overflow-hidden">
                        <div class="text-xs font-bold text-slate-900 truncate">{{ $product->name }}</div>
                        <div class="text-sm font-extrabold text-emerald-800">{{ number_format($product->price, 2, ',', '.') }} TL</div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ $waLink }}" target="_blank" class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center text-base">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    @if($product->stock > 0)
                        <button type="button" 
                                @click="$store.cart.add({id: '{{ $product->id }}', slug: '{{ $product->slug }}', name: '{{ addslashes($product->name) }}', brand: '{{ addslashes($product->brand->name ?? '') }}', price: {{ $product->price }}, category_id: '{{ $product->category_id }}', image: '{{ $imgArr }}', free_shipping: {{ $product->free_shipping ? 'true' : 'false' }}, eft_discount: {{ $product->eft_discount ? 'true' : 'false' }}})" 
                                class="px-4 h-10 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow-md flex items-center gap-1.5 active:scale-95">
                            <i class="fas fa-shopping-basket"></i>
                            <span>Sepete Ekle</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- YouTube Video Modal (if video available) -->
        @if($product->youtube_embed_url)
            <div x-show="videoModalOpen" 
                 x-cloak 
                 x-effect="document.body.style.overflow = videoModalOpen ? 'hidden' : ''"
                 @keydown.escape.window="videoModalOpen = false"
                 class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                 
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs" @click="videoModalOpen = false"></div>

                <!-- Modal Body -->
                <div class="relative bg-slate-900 w-full max-w-3xl rounded-2xl shadow-2xl border border-slate-800 overflow-hidden z-10 flex flex-col">
                    <div class="p-4 sm:p-5 flex items-center justify-between border-b border-slate-800">
                        <div class="flex items-center gap-2.5 text-white">
                            <i class="fab fa-youtube text-red-500 text-xl"></i>
                            <h3 class="text-sm font-bold truncate">{{ $product->name }} - Tanıtım Videosu</h3>
                        </div>
                        <button type="button" @click="videoModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="p-2 sm:p-4 bg-black">
                        <div class="relative w-full aspect-video rounded-lg overflow-hidden bg-black">
                            <iframe :src="videoModalOpen ? '{{ $product->youtube_embed_url }}&autoplay=1' : ''" 
                                    title="{{ $product->name }} Video" 
                                    class="w-full h-full border-0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
            Swal.fire({
                title: 'BAŞARILI!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#059669',
                confirmButtonText: 'TAMAM',
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
                }
            });
            @endif

            @if(session('error') || $errors->any())
            Swal.fire({
                title: 'BİLGİ',
                text: "{{ session('error') ?? $errors->first() }}",
                icon: 'error',
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'TAMAM',
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
                }
            });
            @endif
        });
    </script>
@endsection