@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ 
    editMode: false,
    selectedCompany: null,
    openModal: false,
    formData: { name: '', tracking_url: '' },
    resetForm() {
        this.formData = { name: '', tracking_url: '' };
        this.editMode = false;
        this.selectedCompany = null;
    },
    editCompany(company) {
        this.selectedCompany = company;
        this.formData = { name: company.name, tracking_url: company.tracking_url };
        this.editMode = true;
        this.openModal = true;
    }
}">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Kargo Firmaları</h2>
            <p class="text-sm text-slate-500 mt-1">Siparişlerin gönderileceği kargo şirketlerini ve takip linklerini yönetin.</p>
        </div>
        <button @click="resetForm(); openModal = true" class="px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-semibold hover:bg-brand-700 transition-colors flex items-center gap-2 shadow-lg shadow-brand-500/20">
            <i class="fas fa-plus text-[10px]"></i> Yeni Kargo Firması Ekle
        </button>
    </div>

    <!-- Info Box -->
    <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex gap-4">
        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-blue-500 flex-shrink-0 shadow-sm">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="text-xs text-blue-700 leading-relaxed">
            <p class="font-bold mb-1 underline">Takip Linki Kullanımı:</p>
            <p>Takip linki alanına kargo firmasının takip adresini yazın ve takip kodunun geleceği yere <code class="bg-blue-100 px-1 rounded font-black">[TRACKING_CODE]</code> yazın.</p>
            <p class="mt-1 opacity-70">Örn: https://kargo.com/takip?no=[TRACKING_CODE]</p>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Firma Adı</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Takip Linki Şablonu</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Durum</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $c)
                <tr class="hover:bg-slate-50 transition-all group border-b border-slate-50">
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-slate-700">{{ $c->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <code class="text-[10px] text-slate-400 break-all">{{ $c->tracking_url ?? '-' }}</code>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.shipping-companies.toggle', $c->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider transition-all {{ $c->active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                {{ $c->active ? 'Aktif' : 'Pasif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button @click="editCompany({{ json_encode($c) }})" class="p-2 text-slate-400 hover:text-brand-600 transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('admin.shipping-companies.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add/Edit Modal -->
    <div x-show="openModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div @click.away="openModal = false" class="bg-white rounded-[2.5rem] w-full max-w-lg shadow-2xl overflow-hidden">
            <form :action="editMode ? '{{ route('admin.shipping-companies.update', ':id') }}'.replace(':id', selectedCompany.id) : '{{ route('admin.shipping-companies.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    @method('PUT')
                </template>

                <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-bold text-slate-800" x-text="editMode ? 'Kargo Firmasını Güncelle' : 'Yeni Kargo Firması Ekle'"></h3>
                    <button type="button" @click="openModal = false" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-8 space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 px-1">Firma Adı</label>
                        <input type="text" name="name" x-model="formData.name" required placeholder="Örn: Aras Kargo" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 px-1">Takip Linki Şablonu</label>
                        <input type="text" name="tracking_url" x-model="formData.tracking_url" placeholder="Örn: https://kargo.com/track?no=[TRACKING_CODE]" class="w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 outline-none transition-all">
                        <p class="text-[10px] text-slate-400 mt-2 px-1 italic">Not: [TRACKING_CODE] alanı otomatik olarak takip numarası ile değiştirilecektir.</p>
                    </div>
                </div>

                <div class="p-8 pt-0 flex gap-4">
                    <button type="button" @click="openModal = false" class="flex-1 py-4 border border-slate-200 text-slate-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all">Vazgeç</button>
                    <button type="submit" class="flex-1 py-4 bg-brand-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-brand-700 transition-all shadow-lg shadow-brand-500/20" x-text="editMode ? 'Güncelle' : 'Kaydet'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
