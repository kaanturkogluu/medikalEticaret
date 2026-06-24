@extends('layouts.admin')

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Toplu SMS Gönderimi</h2>
                <p class="text-sm text-slate-500 mt-1">Geçmişte sipariş veren müşterilerinizi seçin ve onlara toplu mesaj gönderin.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.netgsm.history') }}" class="py-2 px-4 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-history text-slate-400"></i> Geçmiş SMS'ler
                </a>
                <a href="{{ route('admin.netgsm.index') }}" class="py-2 px-4 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Test & Rapor Paneli
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 text-emerald-600 font-bold text-sm shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl flex items-center gap-3 text-rose-600 font-bold text-sm shadow-sm animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-600 font-bold text-sm shadow-sm">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(isset($balanceResult))
            <div class="bg-gradient-to-r from-blue-500 to-brand-600 rounded-3xl p-6 shadow-sm text-white flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl backdrop-blur-sm">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg tracking-tight">Kalan Netgsm Bakiyeniz</h3>
                        <p class="text-white/80 text-xs font-medium mt-1">Gönderim yapmadan önce bakiyenizi kontrol edin.</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    @if($balanceResult['status'] && !empty($balanceResult['data']))
                        @foreach($balanceResult['data'] as $bal)
                            <div class="bg-white/10 rounded-xl px-4 py-2 border border-white/20 backdrop-blur-sm text-center min-w-[120px]">
                                <p class="text-[10px] text-white/70 uppercase font-bold tracking-widest mb-1">{{ $bal['balance_name'] ?? 'Bakiye' }}</p>
                                <p class="text-xl font-black">{{ $bal['amount'] ?? '0' }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-rose-500/50 rounded-xl px-4 py-2 border border-rose-500/20 backdrop-blur-sm text-center">
                            <p class="text-xs font-bold text-white">{{ $balanceResult['message'] ?? 'Bakiye çekilemedi.' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">
            <form action="{{ route('admin.netgsm.bulk.send') }}" method="POST" id="bulkSmsForm" class="space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Sol Taraf: Müşteri Listesi -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Müşteri Listesi</h3>
                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">{{ count($customers) }} Müşteri Bulundu</span>
                        </div>
                        
                        <div class="overflow-x-auto border border-slate-200 rounded-2xl max-h-[500px] overflow-y-auto">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10 bg-slate-50 border-b border-slate-200">
                                    <tr>
                                        <th class="p-4 w-12 text-center">
                                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-brand-500 border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                        </th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Müşteri</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest">Telefon</th>
                                        <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-widest w-1/2">Aldığı Ürünler</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($customers as $customer)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="p-4 text-center">
                                                <input type="checkbox" name="phones[]" value="{{ $customer['phone'] }}" class="customer-checkbox w-4 h-4 text-brand-500 border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                                <input type="hidden" name="names[]" value="{{ $customer['name'] ?? '' }}" class="customer-name-hidden" disabled>
                                            </td>
                                            <td class="p-4 text-sm font-medium text-slate-800">{{ $customer['name'] ?? '-' }}</td>
                                            <td class="p-4 text-sm text-slate-600 font-mono text-xs">{{ $customer['phone'] }}</td>
                                            <td class="p-4 text-xs text-slate-500 truncate max-w-[200px]" title="{{ $customer['products'] }}">
                                                {{ str($customer['products'])->limit(50) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-slate-500 text-sm">Hiçbir müşteri kaydı bulunamadı.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Sağ Taraf: Mesaj Formu -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-black text-slate-800 tracking-tight">SMS İçeriği</h3>
                        
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2">Mesajınız</label>
                            <textarea name="message" id="smsMessage" rows="6" required placeholder="Tüm seçili müşterilere gidecek ortak mesaj içeriği..."
                                class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-all placeholder:text-slate-400"></textarea>
                            <p class="text-[10px] text-slate-400 mt-2 font-medium text-right"><span id="charCount">0</span> / 912 Karakter</p>
                        </div>

                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <h4 class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-1 flex items-center gap-1">
                                <i class="fas fa-info-circle"></i> Önemli Bilgi
                            </h4>
                            <p class="text-xs text-blue-700">Seçtiğiniz <span id="selectedCount" class="font-black">0</span> kişiye tek seferde toplu gönderim yapılacaktır.</p>
                        </div>

                        <button type="button" onclick="confirmBulkSend()" class="w-full py-4 bg-brand-500 hover:bg-brand-600 text-white rounded-xl text-sm font-black tracking-wide shadow-lg shadow-brand-500/20 transition-all flex items-center justify-center gap-2 group">
                            <i class="fas fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                            <span>Seçili Müşterilere Gönder</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        const selectedCountEl = document.getElementById('selectedCount');
        const smsMessage = document.getElementById('smsMessage');
        const charCount = document.getElementById('charCount');

        // Karakter sayacı
        smsMessage.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Seçili kişi sayısını güncelle
        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
            selectedCountEl.textContent = checkedCount;
        }

        // Tümünü seç
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                    const hiddenInput = cb.nextElementSibling;
                    if(hiddenInput && hiddenInput.classList.contains('customer-name-hidden')) {
                        hiddenInput.disabled = !selectAll.checked;
                    }
                });
                updateSelectedCount();
            });
        }

        // Tekli seçimlerde sayacı güncelle ve hidden input'u ayarla
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateSelectedCount();
                const hiddenInput = this.nextElementSibling;
                if(hiddenInput && hiddenInput.classList.contains('customer-name-hidden')) {
                    hiddenInput.disabled = !this.checked;
                }
            });
        });
    });

    // Gönderim öncesi onay ekranı
    function confirmBulkSend() {
        const checkedCount = document.querySelectorAll('.customer-checkbox:checked').length;
        const message = document.getElementById('smsMessage').value.trim();

        if (checkedCount === 0) {
            alert("Lütfen en az bir müşteri seçin.");
            return;
        }

        if (message === '') {
            alert("Lütfen bir mesaj içeriği yazın.");
            return;
        }

        const confirmMsg = "Dikkat!\n\nYazdığınız mesaj " + checkedCount + " farklı müşteriye gönderilecektir.\nOnaylıyor musunuz?";
        
        if (confirm(confirmMsg)) {
            document.getElementById('bulkSmsForm').submit();
        }
    }
</script>
@endpush
