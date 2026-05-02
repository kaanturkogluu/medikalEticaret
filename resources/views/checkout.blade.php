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

@if(count($savedAddresses) > 0)
                    <div class="mb-10">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1 mb-4">Kayıtlı Adreslerim</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($savedAddresses as $addr)
                            <div @click="selectAddress({
                                    first_name: '{{ explode(' ', $addr->full_name)[0] }}',
                                    last_name: '{{ count(explode(' ', $addr->full_name)) > 1 ? str_replace(explode(' ', $addr->full_name)[0] . ' ', '', $addr->full_name) : '' }}',
                                    phone: '{{ $addr->phone }}',
                                    city: '{{ $addr->city }}',
                                    district: '{{ $addr->district }}',
                                    neighborhood: '{{ $addr->neighborhood }}',
                                    address: '{{ $addr->address }}',
                                    id: {{ $addr->id }}
                                 })"
                                 class="p-5 border-2 border-gray-100 rounded-[30px] cursor-pointer hover:border-orange-500 hover:bg-orange-50/30 transition-all relative group"
                                 :class="form.selected_address_id === {{ $addr->id }} ? 'border-orange-500 bg-orange-50' : ''">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 bg-slate-100 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-orange-100 group-hover:text-orange-500 transition-colors">
                                        <i class="fas fa-{{ $addr->title === 'iş' ? 'building' : 'home' }}"></i>
                                    </div>
                                    <p class="font-black text-xs uppercase italic tracking-tighter text-slate-900">{{ $addr->title }}</p>
                                </div>
                                <p class="text-[11px] font-bold text-slate-700 mb-1">{{ $addr->full_name }}</p>
                                <p class="text-[10px] text-slate-500 line-clamp-2 leading-relaxed">{{ $addr->address }}</p>
                                <div class="mt-2 pt-2 border-t border-gray-100/50 flex items-center justify-between">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $addr->district }} / {{ $addr->city }}</p>
                                    <div class="w-4 h-4 rounded-full border-2 border-gray-200 flex items-center justify-center" :class="form.selected_address_id === {{ $addr->id }} ? 'border-orange-500' : ''">
                                        <div x-show="form.selected_address_id === {{ $addr->id }}" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="relative flex items-center justify-center py-4 mb-10">
                        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-100"></div></div>
                        <span class="relative px-6 bg-white text-[9px] font-black text-gray-300 uppercase italic tracking-widest">veya yeni adres girin</span>
                    </div>
@endif
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
                            <input type="tel" x-model="form.phone" @input="formatPhone($event)" maxlength="15" placeholder="05XX XXX XX XX" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all">
                            <span class="text-[9px] text-gray-400 px-1 font-medium">Lütfen 11 haneli telefon numaranızı giriniz.</span>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">İl</label>
                            <select id="checkout_city" x-model="form.city" class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all appearance-none select2">
                                <option value="">İl Seçiniz</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}" data-name="{{ $province->name }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">İlçe</label>
                            <select id="checkout_district" x-model="form.district" disabled class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all appearance-none select2">
                                <option value="">İlçe Seçiniz</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1">Mahalle</label>
                            <select id="checkout_neighborhood" x-model="form.neighborhood" disabled class="w-full px-6 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:outline-none focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 transition-all appearance-none select2">
                                <option value="">Mahalle Seçiniz</option>
                            </select>
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
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-credit-card text-2xl" :class="form.payment_method === 'credit_card' ? 'text-slate-900' : 'text-gray-300 group-hover:text-gray-400'"></i>
                                    <div class="flex items-center gap-2 opacity-50 grayscale group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5LLCKGiA1A54u5bl67CTTP8v9eaDQWTllIw&s" class="h-4">
                                        <img src="https://img.icons8.com/clr-gls/1200/visa.jpg" class="h-4">
                                        <img src="https://www.troyodeme.com/Upload/CmsPageFile/Image/medias_02.webp" class="h-3.5">
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center" :class="form.payment_method === 'credit_card' ? 'border-slate-900' : 'border-gray-200'">
                                    <div x-show="form.payment_method === 'credit_card'" class="w-2.5 h-2.5 bg-slate-900 rounded-full"></div>
                                </div>
                            </div>
                            <p class="font-black text-sm uppercase italic">Kredi / Banka Kartı</p>
                            <p class="text-xs text-gray-500 mt-1">Güvenli ödeme altyapısı</p>
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
                    <div x-show="form.payment_method === 'credit_card'" x-collapse class="mt-8 p-8 bg-slate-900 rounded-[30px] text-white overflow-hidden relative">
                        <div class="absolute -right-4 -bottom-4 opacity-5 pointer-events-none">
                            <i class="fas fa-shield-alt text-[120px]"></i>
                        </div>
                        <div class="flex flex-col items-center justify-center py-4 gap-6 relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg border border-white/10">
                                    <i class="fas fa-lock text-[10px] text-green-400"></i>
                                    <span class="text-[10px] font-black italic tracking-tighter">256-BIT SSL</span>
                                </div>
                                <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-lg border border-white/10">
                                    <i class="fas fa-shield-alt text-[10px] text-blue-400"></i>
                                    <span class="text-[10px] font-black italic tracking-tighter">3D SECURE</span>
                                </div>
                            </div>
                            <div class="text-center group">
                                <p class="text-[11px] font-bold leading-relaxed opacity-70 mb-4 max-w-sm">Tüm ödemeleriniz yüksek güvenlikli şifreleme yöntemleri ile taranmaktadır. Kart bilgileriniz kesinlikle sistemimizde saklanmaz.</p>
                                <div class="flex items-center justify-center gap-4 grayscale opacity-40 group-hover:grayscale-0 group-hover:opacity-100 transition-all">
                                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS5LLCKGiA1A54u5bl67CTTP8v9eaDQWTllIw&s" class="h-5">
                                    <img src="https://img.icons8.com/clr-gls/1200/visa.jpg" class="h-5">
                                    <img src="https://www.troyodeme.com/Upload/CmsPageFile/Image/medias_02.webp" class="h-4">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EFT Info --}}
                    <div x-show="form.payment_method === 'eft'" x-collapse class="mt-8 p-8 bg-green-50 border border-green-100 rounded-[30px]">
                        <div class="flex items-start gap-4 mb-6">
                            <i class="fas fa-info-circle text-green-600 mt-1"></i>
                            <div>
                                <h4 class="font-black text-sm text-green-800 uppercase italic mb-2 tracking-tighter">İndirimli Ödeme (Havale/EFT)</h4>
                                <p class="text-xs text-green-700 leading-relaxed font-medium">Siparişi tamamladıktan sonra belirtilen IBAN numarasına ödemeyi yapmanız gerekmektedir. Ödeme açıklama kısmına <strong>Sipariş No</strong> yazmayı unutmayınız.</p>
                            </div>
                        </div>

                        <div class="space-y-4 bg-white/50 p-6 rounded-2xl border border-green-200/50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Banka Adı</p>
                                    <p class="text-xs font-black text-slate-900">{{ $bankDetails['bank_name'] }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Hesap Sahibi</p>
                                    <p class="text-xs font-black text-slate-900">{{ $bankDetails['bank_account_holder'] }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">IBAN</p>
                                    <div class="flex items-center justify-between gap-4 bg-white px-4 py-3 rounded-xl border border-gray-100">
                                        <p class="text-xs font-black text-slate-900 tracking-wider">{{ $bankDetails['bank_iban'] }}</p>
                                        <button @click="copyToClipboard('{{ $bankDetails['bank_iban'] }}')" class="text-green-600 hover:text-green-700 transition-colors">
                                            <i class="far fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
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
                        <div x-show="appliedCoupon" x-cloak class="flex justify-between text-indigo-600 bg-indigo-50 px-3 py-2 rounded-xl border border-indigo-100">
                            <span>Kupon İndirimi</span>
                            <span x-text="'-' + calculateCouponDiscount().toFixed(2) + ' TL'"></span>
                        </div>
                        <div x-show="form.payment_method === 'eft'" class="flex justify-between text-green-600 bg-green-50 px-3 py-2 rounded-xl border border-green-100">
                            <span>EFT İndirimi (%5)</span>
                            <span x-text="'-' + (Math.max(0, cart.subtotal() - calculateCouponDiscount()) * 0.05).toFixed(2) + ' TL'"></span>
                        </div>
                        <div class="flex justify-between text-lg font-black text-slate-900 pt-4 uppercase italic">
                            <span>Toplam</span>
                            <span x-text="grandTotal() + ' TL'"></span>
                        </div>
                    </div>

                    {{-- Coupon Section --}}
                    <div class="mb-8 pt-8 border-t border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-1 mb-4">İndirim Kuponu</p>
                        <div class="flex gap-2" x-show="!appliedCoupon">
                            <input type="text" x-model="couponCode" placeholder="Kupon Kodunuz" 
                                   class="flex-grow px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-xs font-bold focus:ring-2 focus:ring-slate-900 focus:border-slate-900 outline-none uppercase">
                            <button @click="applyCoupon" :disabled="couponLoading" 
                                    class="px-4 py-2.5 bg-slate-900 text-white rounded-xl text-[10px] font-bold hover:bg-orange-600 transition-all disabled:opacity-50">
                                <span x-show="!couponLoading">UYGULA</span>
                                <i x-show="couponLoading" class="fas fa-spinner fa-spin"></i>
                            </button>
                        </div>
                        <div x-show="appliedCoupon" x-cloak class="flex items-center justify-between bg-green-50 px-4 py-3 rounded-xl border border-green-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-600 text-xs"></i>
                                <span class="text-[10px] font-black text-green-800 uppercase tracking-widest" x-text="appliedCoupon.code"></span>
                                <span class="text-[9px] font-bold text-green-600" x-text="appliedCoupon.type === 'percent' ? '(%' + parseFloat(appliedCoupon.value).toFixed(0) + ' İndirim)' : '(₺' + parseFloat(appliedCoupon.value).toFixed(0) + ' İndirim)'"></span>
                            </div>
                            <button @click="removeCoupon" class="text-rose-600 hover:text-rose-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl mb-8">
                        <p class="text-[10px] text-amber-800 leading-relaxed font-bold italic">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            UYARI: Girilen bilgilerin yükümlülüğü tamamen kullanıcıya aittir. Hatalı girilen bilgilerden doğabilecek aksaklıkların sorumluluğu firmaya atfedilemez.
                        </p>
                    </div>

                    <button @click="submitOrder" 
                            :disabled="loading || cart.items.length === 0"
                            class="w-full py-5 bg-slate-900 text-white rounded-[25px] font-black italic shadow-xl shadow-slate-900/20 hover:bg-orange-600 transition-all flex items-center justify-center gap-3 active:scale-95 disabled:opacity-50 group">
                        <span x-text="loading ? 'İşleniyor...' : 'SİPARİŞİ TAMAMLA'"></span>
                        <i class="fas fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </button>

                    <p class="text-[10px] text-center text-gray-400 mt-6 font-medium leading-relaxed">Siparişi tamamlayarak <a href="javascript:void(0)" @click="showAgreement = true" class="underline hover:text-slate-900">Mesafeli Satış Sözleşmesi</a>'ni kabul etmiş sayılırsınız.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Agreement Modal --}}
    <div x-show="showAgreement" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showAgreement = false"
             x-show="showAgreement"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white w-full max-w-4xl max-h-[85vh] rounded-[40px] shadow-2xl flex flex-col relative overflow-hidden">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase italic tracking-tighter">{{ $agreement->title ?? 'Mesafeli Satış Sözleşmesi' }}</h3>
                </div>
                <button @click="showAgreement = false" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-8 md:p-12 overflow-y-auto custom-scrollbar flex-grow">
                <div class="prose prose-slate max-w-none prose-headings:font-black prose-headings:italic prose-headings:uppercase prose-headings:tracking-tighter prose-p:text-sm prose-p:font-medium prose-p:leading-relaxed text-slate-700">
                    {!! $agreement->content ?? 'Sözleşme içeriği bulunamadı.' !!}
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-8 py-6 border-t border-gray-100 bg-gray-50 flex justify-end">
                <button @click="showAgreement = false" class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black italic shadow-lg hover:bg-orange-600 transition-all active:scale-95 uppercase tracking-tighter">ANLADIM</button>
            </div>
        </div>
    </div>
</div>

<script>
function checkoutPage() {
    return {
        cart: null,
        loading: false,
        form: {
            first_name: '{{ auth()->check() ? explode(" ", auth()->user()->name)[0] : "" }}',
            last_name: '{{ auth()->check() ? (count(explode(" ", auth()->user()->name)) > 1 ? explode(" ", auth()->user()->name)[1] : "") : "" }}',
            email: '{{ auth()->user()->email ?? "" }}',
            phone: '',
            city: '',
            district: '',
            neighborhood: '',
            address: '',
            payment_method: 'credit_card',
            selected_address_id: null
        },
        appliedCoupon: null,
        couponCode: '',
        couponLoading: false,
        async applyCoupon() {
            if (!this.couponCode) return;
            this.couponLoading = true;
            try {
                const response = await fetch('{{ route("coupon.apply") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ code: this.couponCode, cart_items: this.cart.items })
                });
                const result = await response.json();
                if (result.success) {
                    this.appliedCoupon = result.coupon;
                    this.couponCode = '';
                    notify('success', result.message);
                } else {
                    notify('error', result.message);
                }
            } catch (e) {
                notify('error', 'Kupon uygulanırken bir hata oluştu.');
            } finally {
                this.couponLoading = false;
            }
        },
        async removeCoupon() {
            try {
                await fetch('{{ route("coupon.remove") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                this.appliedCoupon = null;
                notify('success', 'Kupon kaldırıldı.');
            } catch (e) {}
        },
        selectAddress(addr) {
            this.form.selected_address_id = addr.id;
            this.form.first_name = addr.first_name;
            this.form.last_name = addr.last_name;
            this.form.phone = addr.phone;
            this.form.address = addr.address;
            
            // For City/District/Neighborhood selects (which use IDs and Select2)
            // We set the Alpine data first
            this.form.city = addr.city;
            this.form.district = addr.district;
            this.form.neighborhood = addr.neighborhood;

            // To update visuals of Select2 and load sub-options:
            // 1. Find the option in #checkout_city that has data-name === addr.city
            // 2. Set its value to the select and trigger change
            const cityOption = $('#checkout_city option').filter(function() {
                return $(this).data('name') === addr.city;
            });

            if (cityOption.length) {
                const cityId = cityOption.val();
                $('#checkout_city').val(cityId).trigger('change');
                
                // Since city change starts an AJAX call for districts, 
                // we need to wait for it before setting district.
                // However, we can also just manually append the options if they are just strings we have.
                // But it's better to let the existing logic work.
                
                // We'll use a small interval or just set the names.
                setTimeout(() => {
                    const distOption = $('#checkout_district option').filter(function() {
                        return $(this).data('name') === addr.district;
                    });
                    if (distOption.length) {
                        $('#checkout_district').val(distOption.val()).trigger('change');
                        
                        setTimeout(() => {
                            const neighOption = $('#checkout_neighborhood option').filter(function() {
                                return $(this).data('name') === addr.neighborhood;
                            });
                            if (neighOption.length) {
                                $('#checkout_neighborhood').val(neighOption.val()).trigger('change');
                            }
                        }, 500);
                    } else {
                        // If option doesn't exist yet (AJAX still loading or name mismatch), 
                        // we manually add it to allow the form to have it.
                        let opt = new Option(addr.district, '99999', true, true);
                        $(opt).data('name', addr.district);
                        $('#checkout_district').append(opt).trigger('change').prop('disabled', false);

                        setTimeout(() => {
                            let nOpt = new Option(addr.neighborhood, '88888', true, true);
                            $(nOpt).data('name', addr.neighborhood);
                            $('#checkout_neighborhood').append(nOpt).trigger('change').prop('disabled', false);
                        }, 300);
                    }
                }, 500);
            }
        },
        showAgreement: false,
        init() {
            window.checkoutData = this;
            this.cart = Alpine.store('cart');
            
            // Handle existing coupon from session
            @if(session()->has('applied_coupon'))
                @php
                    $sessionCoupon = \App\Models\Coupon::where('code', session('applied_coupon'))->first();
                @endphp
                @if($sessionCoupon)
                    this.appliedCoupon = {
                        code: '{{ $sessionCoupon->code }}',
                        type: '{{ $sessionCoupon->type }}',
                        value: '{{ $sessionCoupon->value }}',
                        category_ids: @json($sessionCoupon->categories->pluck('id'))
                    };
                @endif
            @endif
        },
        calculateCouponDiscount() {
            if (!this.appliedCoupon) return 0;
            let eligibleSubtotal = 0;
            if (this.appliedCoupon.category_ids && this.appliedCoupon.category_ids.length > 0) {
                this.cart.items.forEach(item => {
                    if (this.appliedCoupon.category_ids.map(Number).includes(Number(item.category_id))) {
                        eligibleSubtotal += item.price * item.qty;
                    }
                });
            } else {
                eligibleSubtotal = this.cart.subtotal();
            }

            if (this.appliedCoupon.type === 'percent') {
                return (eligibleSubtotal * parseFloat(this.appliedCoupon.value)) / 100;
            } else {
                return parseFloat(this.appliedCoupon.value);
            }
        },
        grandTotal() {
            let total = parseFloat(this.cart.total());
            
            // Coupon Discount
            let couponDiscount = this.calculateCouponDiscount();
            total -= couponDiscount;

            // EFT Discount
            if (this.form.payment_method === 'eft') {
                let subtotalAfterCoupon = Math.max(0, this.cart.subtotal() - couponDiscount);
                let eftDiscount = subtotalAfterCoupon * 0.05;
                total -= eftDiscount;
            }
            return Math.max(0, total).toFixed(2);
        },
        formatPhone(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,4})(\d{0,3})(\d{0,2})(\d{0,2})/);
            if (!x[2]) {
                e.target.value = x[1];
            } else {
                e.target.value = x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '') + (x[4] ? ' ' + x[4] : '');
            }
            this.form.phone = e.target.value;
        },
        async submitOrder() {
            // Detailed field check
            const labels = {
                first_name: 'Adınız',
                last_name: 'Soyadınız',
                email: 'E-Posta Adresiniz',
                phone: 'Telefon Numaranız',
                city: 'İl',
                district: 'İlçe',
                neighborhood: 'Mahalle',
                address: 'Açık Adres'
            };

            for (const field in labels) {
                if (!this.form[field]) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Eksik Bilgi',
                        text: `Lütfen "${labels[field]}" alanını doldurun.`,
                        confirmButtonText: 'Tamam',
                        confirmButtonColor: '#0f172a'
                    });
                    return;
                }
            }

            const cleanPhone = this.form.phone.replace(/[^\d+]/g, '');
            const phoneRegex = /^(\+90|0)?5[0-9]{9}$/;
            if (!phoneRegex.test(cleanPhone)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Geçersiz Numara',
                    text: 'Lütfen geçerli bir telefon numarası giriniz (Örn: 05xx xxx xx xx).',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#0f172a'
                });
                return;
            }

            this.loading = true;
            try {
                const response = await fetch('{{ route("checkout.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ...this.form,
                        phone: cleanPhone,
                        cart_items: this.cart.items
                    })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server Error Response:', errorText);
                    try {
                        const errorJson = JSON.parse(errorText);
                        throw new Error(errorJson.message || 'Bir hata oluştu');
                    } catch(e) {
                        throw new Error('Sunucu hatası oluştu. Lütfen daha sonra tekrar deneyin.');
                    }
                }

                const result = await response.json();
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Başarılı!',
                        text: result.message,
                        confirmButtonText: 'Tamam',
                        confirmButtonColor: '#0f172a'
                    }).then(() => {
                        this.cart.clear();
                        window.location.href = result.redirect_url;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata',
                        text: result.message,
                        confirmButtonText: 'Tamam',
                        confirmButtonColor: '#0f172a'
                    });
                }
            } catch (e) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sistemsel Hata',
                    text: 'Sipariş gönderilirken bir hata oluştu.',
                    confirmButtonText: 'Tamam',
                    confirmButtonColor: '#0f172a'
                });
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Panoya kopyalandı',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            });
        }
    }
}

$(document).ready(function() {
    $('.select2').select2({
        width: '100%',
        language: {
            noResults: function() {
                return "Sonuç bulunamadı";
            }
        }
    });

    // Use the global reference we set in Alpine's init()
    const getAlpineData = () => {
        return window.checkoutData;
    };

    $('#checkout_city').on('change', function() {
        const provinceId = $(this).val();
        const provinceName = $(this).find(':selected').data('name');
        const data = getAlpineData();
        if (!data) return;
        
        data.form.city = provinceName;
        data.form.district = '';
        data.form.neighborhood = '';
        
        $('#checkout_district').prop('disabled', true).html('<option value="">Yükleniyor...</option>').trigger('change');
        $('#checkout_neighborhood').prop('disabled', true).html('<option value="">Önce İlçe Seçiniz</option>').trigger('change');
        
        if (provinceId) {
            $.get(`/location/districts/${provinceId}`, function(res) {
                let options = '<option value="">İlçe Seçiniz</option>';
                res.forEach(function(district) {
                    options += `<option value="${district.id}" data-name="${district.name}">${district.name}</option>`;
                });
                $('#checkout_district').html(options).prop('disabled', false).trigger('change');
            });
        }
    });

    $('#checkout_district').on('change', function() {
        const districtId = $(this).val();
        const districtName = $(this).find(':selected').data('name');
        const data = getAlpineData();
        if (!data) return;
        
        data.form.district = districtName;
        data.form.neighborhood = '';

        $('#checkout_neighborhood').prop('disabled', true).html('<option value="">Yükleniyor...</option>').trigger('change');
        
        if (districtId) {
            $.get(`/location/neighborhoods/${districtId}`, function(res) {
                let options = '<option value="">Mahalle Seçiniz</option>';
                res.forEach(function(neighborhood) {
                    options += `<option value="${neighborhood.id}" data-name="${neighborhood.name}">${neighborhood.name}</option>`;
                });
                $('#checkout_neighborhood').html(options).prop('disabled', false).trigger('change');
            });
        }
    });

    $('#checkout_neighborhood').on('change', function() {
        const neighborhoodName = $(this).find(':selected').data('name');
        const data = getAlpineData();
        if (!data) return;
        data.form.neighborhood = neighborhoodName;
    });
});
</script>
<style>
.select2-container--default .select2-selection--single {
    border: 1px solid #f3f4f6;
    background-color: #f9fafb;
    border-radius: 1rem;
    height: 58px;
    padding: 14px 12px;
    font-size: 0.875rem;
    font-weight: 700;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 56px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #111827;
}
</style>
@endsection
