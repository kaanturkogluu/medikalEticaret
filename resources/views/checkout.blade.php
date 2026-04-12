@extends('layouts.app')

@section('title', 'Ödeme ve Sipariş')

@section('content')
<div class="bg-gray-50/50 min-h-screen py-12" x-data="checkoutPage()">
    <div class="ty-container">
        <div class="flex flex-col lg:flex-row gap-12">
            
            <!-- Left Side: Form -->
            <div class="flex-grow space-y-8">
                
                {{-- Step 1: Contact Info --}}
                <div class="bg-white rounded-[40px] p-8 md:p-12 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black italic">01</div>
                        <h2 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Teslimat Bilgileri</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Adınız</label>
                            <input type="text" x-model="form.first_name" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Soyadınız</label>
                            <input type="text" x-model="form.last_name" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">E-Posta</label>
                            <input type="email" x-model="form.email" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Telefon</label>
                            <input type="tel" x-model="form.phone" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">İl</label>
                            <select x-model="form.city" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all appearance-none">
                                <option value="">İl Seçiniz</option>
                                <template x-for="city in cities" :key="city">
                                    <option :value="city" x-text="city"></option>
                                </template>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">İlçe</label>
                            <input type="text" x-model="form.district" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all">
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Açık Adres</label>
                            <textarea x-model="form.address" rows="3" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all resize-none"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Payment --}}
                <div class="bg-white rounded-[40px] p-8 md:p-12 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-slate-900 text-white rounded-2xl flex items-center justify-center font-black italic">02</div>
                        <h2 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Ödeme Yöntemi</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div @click="form.payment_method = 'credit_card'" 
                             :class="form.payment_method === 'credit_card' ? 'border-slate-900 bg-slate-50' : 'border-gray-100 hover:border-gray-200'"
                             class="cursor-pointer p-6 border-2 rounded-[30px] transition-all group">
                            <div class="flex items-center justify-between mb-4">
                                <i class="fas fa-credit-card text-2xl" :class="form.payment_method === 'credit_card' ? 'text-slate-900' : 'text-gray-300 group-hover:text-gray-400'"></i>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="form.payment_method === 'credit_card' ? 'border-slate-900' : 'border-gray-200'">
                                    <div x-show="form.payment_method === 'credit_card'" class="w-2.5 h-2.5 bg-slate-900 rounded-full"></div>
                                </div>
                            </div>
                            <p class="font-black text-sm uppercase italic">Kredi / Banka Kartı</p>
                            <p class="text-xs text-gray-500 mt-1">PayTR ile güvenli ödeme</p>
                        </div>

                        <div @click="form.payment_method = 'eft'"
                             :class="form.payment_method === 'eft' ? 'border-green-600 bg-green-50/30' : 'border-gray-100 hover:border-gray-200'"
                             class="cursor-pointer p-6 border-2 rounded-[30px] transition-all group relative overflow-hidden">
                            <div class="absolute top-0 right-0 bg-green-600 text-white text-[9px] font-black px-4 py-1.5 rounded-bl-2xl uppercase tracking-tighter italic shadow-lg">%5 İndirim</div>
                            <div class="flex items-center justify-between mb-4">
                                <i class="fas fa-university text-2xl" :class="form.payment_method === 'eft' ? 'text-green-600' : 'text-gray-300 group-hover:text-gray-400'"></i>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="form.payment_method === 'eft' ? 'border-green-600' : 'border-gray-200'">
                                    <div x-show="form.payment_method === 'eft'" class="w-2.5 h-2.5 bg-green-600 rounded-full"></div>
                                </div>
                            </div>
                            <p class="font-black text-sm uppercase italic">Havale / EFT</p>
                            <p class="text-xs text-gray-500 mt-1">Banka hesabımıza doğrudan transfer</p>
                        </div>
                    </div>

                    {{-- Credit Card Form placeholder --}}
                    <div x-show="form.payment_method === 'credit_card'" x-collapse class="mt-8 p-8 bg-slate-900 rounded-[30px] text-white">
                        <div class="flex flex-col items-center justify-center py-6 gap-4">
                            <i class="fas fa-shield-alt text-4xl text-brand-400"></i>
                            <p class="text-center text-xs font-bold leading-relaxed opacity-70">PayTR altyapısı ile şifrelenmiş güvenli ödeme ekranına siparişi tamamladıktan sonra yönlendirileceksiniz.</p>
                        </div>
                    </div>

                    {{-- EFT Info --}}
                    <div x-show="form.payment_method === 'eft'" x-collapse class="mt-8 p-8 bg-green-50 border border-green-100 rounded-[30px]">
                        <div class="flex items-start gap-4">
                            <i class="fas fa-info-circle text-green-600 mt-1"></i>
                            <div>
                                <h4 class="font-black text-sm text-green-800 uppercase italic mb-2 tracking-tighter">Havale/EFT Bilgileri</h4>
                                <p class="text-xs text-green-700 leading-relaxed font-medium">Siparişi tamamladıktan sonra belirtilen IBAN numarasına ödemeyi yapmanız gerekmektedir. Ödeme açıklama kısmına <strong>Sipariş No</strong> yazmayı unutmayınız.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Summary -->
            <div class="lg:w-96 shrink-0">
                <div class="bg-white rounded-[40px] p-8 shadow-2xl shadow-slate-200/50 border border-gray-100 sticky top-32">
                    <h3 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter mb-8 pb-4 border-b border-gray-100">Sipariş Özeti</h3>
                    
                    <div class="space-y-4 mb-8 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                        <template x-for="item in cart.items" :key="item.id">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-center shrink-0 overflow-hidden p-1">
                                    <img :src="item.image" class="w-full h-full object-contain">
                                </div>
                                <div class="flex-grow min-w-0">
                                    <p class="text-[10px] font-black text-slate-900 uppercase truncate" x-text="item.name"></p>
                                    <p class="text-[10px] text-gray-400 font-bold" x-text="item.qty + ' Adet x ' + item.price + ' TL'"></p>
                                </div>
                                <p class="text-[10px] font-black text-slate-900 whitespace-nowrap" x-text="(item.qty * item.price).toFixed(2) + ' TL'"></p>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-3 pt-6 border-t border-gray-100 text-xs font-bold text-gray-500">
                        <div class="flex justify-between">
                            <span>Sipariş Toplamı</span>
                            <span x-text="cart.subtotal().toFixed(2) + ' TL'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kargo Ücreti</span>
                            <span :class="cart.shipping() === 0 ? 'text-green-600' : ''" x-text="cart.shipping() === 0 ? 'Ücretsiz' : cart.shipping().toFixed(2) + ' TL'"></span>
                        </div>
                        <div x-show="form.payment_method === 'eft'" class="flex justify-between text-green-600 bg-green-50 px-3 py-2 rounded-xl border border-green-100">
                            <span>EFT İndirimi (%5)</span>
                            <span x-text="'-' + (cart.subtotal() * 0.05).toFixed(2) + ' TL'"></span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-slate-900 pt-4 uppercase italic">
                            <span>Toplam</span>
                            <span x-text="grandTotal() + ' TL'"></span>
                        </div>
                    </div>

                    <button @click="submitOrder" 
                            :disabled="loading || cart.items.length === 0"
                            class="w-full mt-10 py-5 bg-slate-900 text-white rounded-[25px] font-black italic shadow-xl shadow-slate-900/20 hover:bg-orange-600 transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50 group">
                        <span x-text="loading ? 'İşleniyor...' : 'SİPARİŞİ TAMAMLA'"></span>
                        <i class="fas fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </button>

                    <p class="text-[10px] text-center text-gray-400 mt-6 font-medium leading-relaxed">Siparişi tamamlayarak <a href="/sayfa/mesafeli-satis-sozlesmesi" class="underline hover:text-slate-900">Mesafeli Satış Sözleşmesi</a>'ni kabul etmiş sayılırsınız.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkoutPage() {
    return {
        cart: null,
        loading: false,
        cities: ["Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Amasya", "Ankara", "Antalya", "Artvin", "Aydın", "Balıkesir", "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır", "Edirne", "Elazığ", "Erzincan", "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay", "Isparta", "Mersin", "İstanbul", "İzmir", "Kars", "Kastamonu", "Kayseri", "Kırklareli", "Kırşehir", "Kocaeli", "Konya", "Kütahya", "Malatya", "Manisa", "Kahramanmaraş", "Mardin", "Muğla", "Muş", "Nevşehir", "Niğde", "Ordu", "Rize", "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Şanlıurfa", "Uşak", "Van", "Yozgat", "Zonguldak", "Aksaray", "Bayburt", "Karaman", "Kırıkkale", "Batman", "Şırnak", "Bartın", "Ardahan", "Iğdır", "Yalova", "Karabük", "Kilis", "Osmaniye", "Düzce"],
        form: {
            first_name: '{{ auth()->check() ? explode(" ", auth()->user()->name)[0] : "" }}',
            last_name: '{{ auth()->check() ? (count(explode(" ", auth()->user()->name)) > 1 ? explode(" ", auth()->user()->name)[1] : "") : "" }}',
            email: '{{ auth()->user()->email ?? "" }}',
            phone: '',
            city: '',
            district: '',
            address: '',
            payment_method: 'credit_card'
        },
        init() {
            this.cart = Alpine.store('cart');
            if (this.cart.items.length === 0) {
                // window.location.href = '/';
            }
        },
        grandTotal() {
            let total = parseFloat(this.cart.total());
            if (this.form.payment_method === 'eft') {
                let discount = this.cart.subtotal() * 0.05;
                total -= discount;
            }
            return total.toFixed(2);
        },
        async submitOrder() {
            if (!this.form.first_name || !this.form.last_name || !this.form.email || !this.form.address || !this.form.city) {
                alert('Lütfen tüm zorunlu alanları doldurun.');
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route("checkout.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ...this.form,
                        cart_items: this.cart.items
                    })
                });

                const result = await response.json();
                if (result.success) {
                    alert(result.message);
                    localStorage.removeItem('cart_items');
                    window.location.href = '/hesabim/siparislerim';
                } else {
                    alert(result.message);
                }
            } catch (e) {
                alert('Sipariş gönderilirken bir hata oluştu.');
                console.error(e);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
