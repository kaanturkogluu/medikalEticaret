@extends('layouts.app')

@section('title', 'Teklif Sepeti & Özel Fiyat Talebi - ' . config('app.name'))

@section('content')
<div class="bg-slate-50/50 min-h-screen py-10" x-data="{
    loading: false,
    form: {
        customer_name: '{{ auth()->check() ? auth()->user()->name : '' }}',
        customer_phone: '{{ auth()->check() ? (auth()->user()->phone ?? '') : '' }}',
        customer_email: '{{ auth()->check() ? auth()->user()->email : '' }}',
        organization_name: '',
        type: 'bulk_order',
        customer_note: ''
    },
    async submitQuote() {
        if (!this.form.customer_name.trim()) {
            window.notify('warning', 'Lütfen adınızı ve soyadınızı giriniz.');
            return;
        }
        if (!this.form.customer_phone.trim()) {
            window.notify('warning', 'Lütfen telefon numaranızı giriniz.');
            return;
        }
        if ($store.quote.items.length === 0) {
            window.notify('warning', 'Teklif sepetinizde en az 1 ürün bulunmalıdır.');
            return;
        }

        this.loading = true;
        try {
            const response = await fetch('{{ route('quote.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    customer_name: this.form.customer_name,
                    customer_phone: this.form.customer_phone,
                    customer_email: this.form.customer_email,
                    organization_name: this.form.organization_name,
                    type: this.form.type,
                    customer_note: this.form.customer_note,
                    items: $store.quote.items
                })
            });

            const data = await response.json();
            if (data.success) {
                $store.quote.clear();
                window.location.href = data.redirect_url;
            } else {
                window.notify('error', data.message || 'Teklif talebi gönderilemedi. Lütfen bilgilerinizi kontrol ediniz.');
            }
        } catch (e) {
            window.notify('error', 'Bir bağlantı hatası oluştu. Lütfen tekrar deneyiniz.');
        } finally {
            this.loading = false;
        }
    }
}">
    <div class="ty-container max-w-6xl">
        
        <!-- Breadcrumb & Header -->
        <div class="mb-8">
            <nav class="flex text-xs font-semibold text-slate-400 gap-2 mb-3">
                <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Ana Sayfa</a>
                <span>/</span>
                <span class="text-slate-700">Teklif Sepeti &amp; Özel Fiyat Talebi</span>
            </nav>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs border border-amber-200/60">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <span>Teklif Sepeti &amp; Özel Fiyat Talebi</span>
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">20 adet ve üzeri toplu alımlar, bağışlar ve kurumsal ihtiyaçlarınız için özel indirimli fiyat teklifi talep edin.</p>
                </div>

                <div class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-800 text-xs font-bold px-4 py-2 rounded-xl border border-emerald-200/60 self-start md:self-auto">
                    <i class="fas fa-headset text-emerald-600"></i>
                    <span>Doğrudan Destek: <a href="tel:05469416996" class="underline hover:text-emerald-950">0546 941 69 96</a></span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div x-show="$store.quote.items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Side: Quote Items List -->
            <div class="lg:col-span-7 space-y-4">
                <div class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-wider">
                            Teklif Listesindeki Ürünler (<span x-text="$store.quote.items.length"></span>)
                        </h2>
                        <button type="button" @click="$store.quote.clear()" class="text-xs font-bold text-rose-500 hover:text-rose-700 transition-colors flex items-center gap-1">
                            <i class="fas fa-trash-alt text-[10px]"></i>
                            <span>Tümünü Temizle</span>
                        </button>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <template x-for="item in $store.quote.items" :key="item.id">
                            <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl border border-slate-200/80 bg-slate-50 p-1 flex-shrink-0 flex items-center justify-center">
                                        <img :src="item.image" :alt="item.name" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="space-y-1">
                                        <h3 class="text-xs font-extrabold text-slate-900 line-clamp-2" x-text="item.name"></h3>
                                        <p class="text-[11px] text-slate-400">
                                            Liste Birim: <span class="font-bold text-slate-600" x-text="item.price.toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' TL'"></span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-4 self-stretch sm:self-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-50">
                                    <!-- Quantity Stepper with Quick Buttons -->
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden">
                                            <button type="button" @click="$store.quote.decrement(item.id)" class="px-2.5 py-1 text-slate-600 hover:bg-slate-200 text-xs font-black transition-colors">-</button>
                                            <input type="number" min="1" :value="item.qty" @change="$store.quote.updateQty(item.id, $event.target.value)" class="w-14 text-center text-xs font-black bg-transparent border-0 py-1 focus:ring-0 text-slate-900">
                                            <button type="button" @click="$store.quote.increment(item.id)" class="px-2.5 py-1 text-slate-600 hover:bg-slate-200 text-xs font-black transition-colors">+</button>
                                        </div>

                                        <div class="hidden sm:flex gap-1">
                                            <button type="button" @click="$store.quote.updateQty(item.id, 20)" class="px-2 py-1 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-lg text-[10px] font-black transition-colors" title="20 Adet">+20</button>
                                            <button type="button" @click="$store.quote.updateQty(item.id, 50)" class="px-2 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded-lg text-[10px] font-black transition-colors" title="50 Adet">+50</button>
                                        </div>
                                    </div>

                                    <!-- Subtotal & Remove -->
                                    <div class="text-right">
                                        <p class="text-xs font-black text-emerald-700 whitespace-nowrap" x-text="(item.price * item.qty).toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' TL'"></p>
                                        <button type="button" @click="$store.quote.remove(item.id)" class="text-[11px] font-bold text-slate-400 hover:text-rose-500 transition-colors">
                                            Kaldır
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Total Estimates Box -->
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-500">Standart Katalog Liste Toplamı:</p>
                            <p class="text-[11px] text-amber-700 font-medium">✦ Talebinize özel indirimli fiyat teklifiniz hazırlanacaktır.</p>
                        </div>
                        <p class="text-xl font-black text-slate-900" x-text="$store.quote.subtotal().toLocaleString('tr-TR', {minimumFractionDigits: 2}) + ' TL'"></p>
                    </div>
                </div>

                <!-- Info Banner -->
                <div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white rounded-3xl p-6 shadow-sm flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 text-xl">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-black text-emerald-300 uppercase tracking-wide">Toplu Alım ve Bağışlara Özel Destek</h4>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Hasta bezi, tekerlekli sandalye, havalı yatak ve tüm medikal ürünlerde koli bazlı toptan alımlarda ve hayır/bağış amaçlı siparişlerde ek indirimler tanımlanmaktadır.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Side: Customer Request Form -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl p-6 md:p-8 shadow-xs border border-slate-200/80 space-y-5 sticky top-28">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight">İletişim &amp; Teklif Bilgileri</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Teklifinizi hazırlayıp telefon veya WhatsApp üzerinden size ileteceğiz.</p>
                    </div>

                    <form @submit.prevent="submitQuote()" class="space-y-4">
                        <!-- Request Type -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Talep Türü</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="bulk_order" x-model="form.type" class="peer sr-only">
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center peer-checked:border-emerald-600 peer-checked:bg-emerald-50/50 peer-checked:text-emerald-900 transition-all">
                                        <p class="text-xs font-black">📦 Toplu Alım</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">20+ Adet / Koli</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="donation" x-model="form.type" class="peer sr-only">
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center peer-checked:border-rose-500 peer-checked:bg-rose-50/50 peer-checked:text-rose-900 transition-all">
                                        <p class="text-xs font-black">❤️ Bağış &amp; Yardım</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Dernek / Hayır</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="corporate" x-model="form.type" class="peer sr-only">
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center peer-checked:border-blue-600 peer-checked:bg-blue-50/50 peer-checked:text-blue-900 transition-all">
                                        <p class="text-xs font-black">🏢 Kurumsal</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Klinik / Bakımevi</p>
                                    </div>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="general" x-model="form.type" class="peer sr-only">
                                    <div class="p-3 rounded-2xl border border-slate-200 text-center peer-checked:border-amber-600 peer-checked:bg-amber-50/50 peer-checked:text-amber-900 transition-all">
                                        <p class="text-xs font-black">🏷️ Genel Fiyat</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Özel Fiyat Teklifi</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Adınız Soyadınız <span class="text-rose-500">*</span></label>
                            <input type="text" x-model="form.customer_name" required placeholder="Örn: Ahmet Yılmaz" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Telefon Numaranız <span class="text-rose-500">*</span></label>
                            <input type="tel" x-model="form.customer_phone" required placeholder="Örn: 05XX XXX XX XX" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                        </div>

                        <!-- Email (Optional) -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">E-Posta Adresiniz (İsteğe Bağlı)</label>
                            <input type="email" x-model="form.customer_email" placeholder="Örn: ornek@mail.com" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                        </div>

                        <!-- Organization (Optional) -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Kurum / Dernek / Firma (İsteğe Bağlı)</label>
                            <input type="text" x-model="form.organization_name" placeholder="Örn: Huzurevi, Yardım Derneği, Firma Adı" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-xs font-bold text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all">
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-black uppercase text-slate-500 tracking-wider">Talep Notu / Teslimat Şehri</label>
                            <textarea x-model="form.customer_note" rows="3" placeholder="Örn: İstanbul içi 20 koli hasta bezi için teklif almak istiyoruz. Ambar ile sevk edilebilir." class="w-full bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs font-medium text-slate-900 focus:bg-white focus:ring-4 focus:ring-emerald-50 focus:border-emerald-600 outline-none transition-all"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" :disabled="loading" class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white rounded-2xl font-black text-sm uppercase tracking-wider shadow-lg shadow-emerald-600/25 transition-all flex items-center justify-center gap-2">
                            <template x-if="!loading">
                                <span class="flex items-center gap-2">
                                    <span>Teklif Talebini Gönder</span>
                                    <i class="fas fa-paper-plane text-xs"></i>
                                </span>
                            </template>
                            <template x-if="loading">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span>Talebiniz Alınıyor...</span>
                                </span>
                            </template>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Empty State -->
        <div x-show="$store.quote.items.length === 0" class="bg-white rounded-3xl p-12 text-center shadow-xs border border-slate-200/80 max-w-xl mx-auto space-y-4">
            <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-3xl flex items-center justify-center mx-auto shadow-inner text-3xl">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900">Teklif Sepetiniz Şu An Boş</h2>
            <p class="text-xs text-slate-500 max-w-sm mx-auto">
                20 adet ve üzeri toplu alımlar, bağışlar veya kurumsal ihtiyaçlarınız için ürün detay sayfalarındaki <strong>"Teklif Sepetine Ekle"</strong> butonunu kullanarak teklif listesi oluşturabilirsiniz.
            </p>
            <div class="pt-2">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl shadow-md shadow-emerald-600/20 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Ürünleri İncelemeye Başla</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
