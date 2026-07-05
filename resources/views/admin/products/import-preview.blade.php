@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="importPreview()">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Ürünleri İçe Aktar (Ön İzleme)</h2>
            <p class="text-sm text-slate-500 mt-1">Excel/CSV dosyanızdaki ürünleri eşleştirin ve onaylayın.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.products') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-semibold hover:bg-slate-50 transition-colors flex items-center gap-2 shadow-sm">
                <i class="fas fa-arrow-left text-xs"></i> İptal Et
            </a>
            <button @click="processImport()" :disabled="loading" class="px-6 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition-colors flex items-center gap-2 shadow-sm disabled:opacity-50">
                <span x-show="loading"><i class="fas fa-spinner fa-spin"></i> İşleniyor...</span>
                <span x-show="!loading"><i class="fas fa-check"></i> Onayla ve İçe Aktar</span>
            </button>
        </div>
    </div>

    <div class="mb-4 flex items-start gap-3 p-4 bg-amber-50 rounded-xl border border-amber-100">
        <i class="fas fa-exclamation-circle text-amber-500 mt-0.5 text-lg"></i>
        <div>
            <h4 class="text-sm font-bold text-amber-800">Kategori ve Marka Eşleştirmesi</h4>
            <p class="text-xs text-amber-700 mt-1">Sistemde bulunamayan kategori ve markaları <strong class="font-black underline">manuel olarak</strong> seçmeniz gerekmektedir.</p>
        </div>
    </div>

    <template x-if="previewData.some(p => p.is_duplicate)">
        <div class="mb-4 flex items-start gap-3 p-4 bg-red-50 rounded-xl border border-red-200">
            <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 text-xl"></i>
            <div>
                <h4 class="text-sm font-bold text-red-800">Çakışan Ürünler Tespit Edildi!</h4>
                <p class="text-xs text-red-700 mt-1">Dosyanızda, sistemde zaten kayıtlı olan (aynı stok koduna sahip) ürünler bulunmaktadır. Bu ürünler varsayılan olarak <strong class="font-black underline">atlanacaktır</strong>. Eğer mevcut ürünlerin üzerine yazarak güncellemek isterseniz seçim kutularını işaretleyebilirsiniz.</p>
                <div class="mt-3 flex gap-2">
                    <button @click="previewData.forEach(p => { if(p.is_duplicate) p.selected = false })" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded text-xs font-bold hover:bg-red-50 transition-colors">Tüm Tekrar Edenleri Atla</button>
                    <button @click="previewData.forEach(p => { if(p.is_duplicate) p.selected = true })" class="px-3 py-1.5 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700 transition-colors shadow-sm">Tüm Tekrar Edenleri Güncelle</button>
                </div>
            </div>
        </div>
    </template>

    <!-- Products Table -->
    <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 w-16 text-center">Durum</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 w-24">Resimler</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100">Ürün Bilgisi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 w-64">Kategori (Seçim)</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 w-64">Marka (Seçim)</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 text-right">Fiyat & Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="(product, index) in previewData" :key="index">
                        <tr :class="product.selected ? 'hover:bg-slate-50/50 transition-colors' : 'bg-slate-50 opacity-60 hover:opacity-100 transition-all'">
                            <td class="px-4 py-4">
                                <div class="flex flex-col items-center gap-2">
                                    <input type="checkbox" x-model="product.selected" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 cursor-pointer">
                                    <template x-if="product.is_duplicate">
                                        <span class="text-[9px] font-bold bg-red-100 text-red-700 px-1.5 py-0.5 rounded text-center leading-tight shadow-sm border border-red-200">Kayıtlı<br>Mevcut</span>
                                    </template>
                                    <template x-if="!product.is_duplicate">
                                        <span class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded text-center border border-emerald-200">Yeni</span>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1 overflow-x-auto max-w-[120px] custom-scrollbar pb-1">
                                    <template x-for="img in product.images" :key="img">
                                        <div class="h-10 w-10 flex-shrink-0 rounded bg-slate-100 border border-slate-200 overflow-hidden relative">
                                            <img :src="img" class="h-full w-full object-cover" @@error="$el.src='https://via.placeholder.com/150?text=Hata'">
                                        </div>
                                    </template>
                                    <template x-if="!product.images || product.images.length === 0">
                                        <div class="h-10 w-10 flex-shrink-0 rounded bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-300">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-slate-800" x-text="product.name"></div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">SKU:</span>
                                    <span class="text-[11px] font-bold text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded tracking-tighter" x-text="product.sku || '-'"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <select :id="'cat_' + index" x-model="product.category_id" class="select2-category w-full text-sm">
                                    <option value="">-- Kategori Seçiniz --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="!product.category_id && product.category_name" class="text-[11px] font-bold text-amber-600 mt-1.5 flex items-center gap-1 bg-amber-50 px-2 py-1 rounded inline-block">
                                    <i class="fas fa-exclamation-triangle"></i> Eşleşmedi: <span x-text="product.category_name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <select :id="'brand_' + index" x-model="product.brand_id" class="select2-brand w-full text-sm">
                                    <option value="">-- Marka Seçiniz --</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                <div x-show="!product.brand_id && product.brand_name" class="text-[11px] font-bold text-amber-600 mt-1.5 flex items-center gap-1 bg-amber-50 px-2 py-1 rounded inline-block">
                                    <i class="fas fa-exclamation-triangle"></i> Eşleşmedi: <span x-text="product.brand_name"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-black text-brand-600 tabular-nums" x-text="product.price + ' ₺'"></div>
                                <div class="text-xs font-bold text-slate-500 tabular-nums mt-0.5" x-text="product.stock + ' Adet'"></div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-500">
            <p>Seçili olan <span class="text-slate-800 text-sm font-black" x-text="previewData.filter(p => p.selected).length"></span> ürün aktarılacak / güncellenecek.</p>
        </div>
    </div>
</div>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
        height: 38px;
        display: flex;
        align-items: center;
        background-color: #f8fafc;
    }
    .select2-container--default .select2-selection--single:focus {
        border-color: #0ea5e9;
        outline: none;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-dropdown {
        border-color: #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('importPreview', () => ({
            importKey: '{{ $importKey }}',
            previewData: @json($previewData).map(p => ({
                ...p,
                selected: !p.is_duplicate // Duplicates are unselected by default
            })),
            loading: false,

            init() {
                setTimeout(() => {
                    this.initSelect2();
                }, 100);
            },
            
            initSelect2() {
                const self = this;
                
                $('.select2-category').select2({
                    placeholder: "Kategori Seçiniz",
                    allowClear: true
                }).on('change', function() {
                    const index = $(this).attr('id').replace('cat_', '');
                    self.previewData[index].category_id = $(this).val();
                });

                // Set initial values
                $('.select2-category').each(function() {
                    const index = $(this).attr('id').replace('cat_', '');
                    $(this).val(self.previewData[index].category_id).trigger('change.select2');
                });

                $('.select2-brand').select2({
                    placeholder: "Marka Seçiniz",
                    allowClear: true
                }).on('change', function() {
                    const index = $(this).attr('id').replace('brand_', '');
                    self.previewData[index].brand_id = $(this).val();
                });
                
                // Set initial values
                $('.select2-brand').each(function() {
                    const index = $(this).attr('id').replace('brand_', '');
                    $(this).val(self.previewData[index].brand_id).trigger('change.select2');
                });
            },

            processImport() {
                this.loading = true;
                
                let productsInput = {};
                this.previewData.forEach((item, index) => {
                    if (item.selected) {
                        productsInput[index] = {
                            category_id: item.category_id,
                            brand_id: item.brand_id
                        };
                    }
                });

                if (Object.keys(productsInput).length === 0) {
                    this.loading = false;
                    notify('warning', 'İçe aktarılacak ürün seçmediniz.');
                    return;
                }

                fetch('{{ route("admin.products.import.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        import_key: this.importKey,
                        products: productsInput
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        notify('success', data.message);
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.products") }}';
                        }, 1500);
                    } else {
                        notify('error', data.message || 'Bir hata oluştu.');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    notify('error', 'Sunucu hatası oluştu.');
                });
            }
        }));
    });
</script>
@endsection
