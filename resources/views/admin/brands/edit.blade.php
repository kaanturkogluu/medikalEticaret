@extends('layouts.admin')

@section('title', 'Marka Düzenle: ' . $brand->name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center mb-8">
        <div class="sm:flex-auto">
            <h1 class="text-2xl font-black italic tracking-tighter text-slate-900 uppercase underline decoration-[var(--primary-color)] decoration-4 underline-offset-8">Marka Düzenle</h1>
            <p class="mt-4 text-sm text-gray-500 font-medium"><span class="font-black text-slate-900 uppercase italic">{{ $brand->name }}</span> markasını güncelliyorsunuz.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('admin.brands.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-slate-200 text-sm font-black italic tracking-tighter uppercase rounded-xl bg-white shadow-xl shadow-slate-200/50 text-slate-600 hover:bg-slate-50 transition-all transform hover:scale-105 active:scale-95">
                <i class="fas fa-chevron-left mr-2"></i> Listeye Dön
            </a>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8 bg-white/50 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 overflow-hidden relative group">
                @csrf
                @method('PUT')
                
                <!-- Decorative Element -->
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-[var(--primary-color)]/5 rounded-full blur-[100px] transition-all group-hover:bg-[var(--primary-color)]/10"></div>
                
                <div class="relative space-y-6">
                    <div>
                        <label for="name" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">Marka Adı</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $brand->name) }}"
                               class="block w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl text-sm font-bold text-slate-900 placeholder-slate-300 focus:outline-none focus:ring-4 focus:ring-[var(--primary-color)]/10 focus:border-[var(--primary-color)] transition-all">
                        @error('name') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{ logoPreview: '{{ $brand->logo ? asset('storage/' . $brand->logo) : null }}' }">
                        <label for="logo" class="block text-xs font-black italic tracking-widest text-slate-400 uppercase mb-3">Marka Logosu</label>
                        <div class="relative">
                            <input type="file" name="logo" id="logo" class="hidden" accept="image/*" 
                                   @change="let reader = new FileReader(); reader.onload = (e) => { logoPreview = e.target.result }; reader.readAsDataURL($event.target.files[0])">
                            
                            <div class="flex items-center gap-6">
                                <template x-if="logoPreview">
                                    <div class="h-32 w-32 rounded-3xl border-2 border-dashed border-[var(--primary-color)]/30 p-2 relative group/preview bg-white shadow-xl">
                                        <img :src="logoPreview" class="h-full w-full object-contain">
                                        <button type="button" @click="logoPreview = null; document.getElementById('logo').value = ''" 
                                                class="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full shadow-lg opacity-0 group-hover/preview:opacity-100 transition-opacity">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </div>
                                </template>
                                
                                <div @click="document.getElementById('logo').click()" 
                                     class="flex-grow border-2 border-dashed border-slate-200 rounded-3xl p-8 flex flex-col items-center justify-center cursor-pointer hover:border-[var(--primary-color)] hover:bg-[var(--primary-color)]/5 transition-all group/upload bg-slate-50/50">
                                    <i class="fas fa-image text-3xl text-slate-300 mb-2 group-hover/upload:text-[var(--primary-color)] transition-colors"></i>
                                    <span class="text-xs font-black italic uppercase tracking-tighter text-slate-400 group-hover/upload:text-[var(--primary-color)] transition-colors">Logoyu Değiştirmek İçin Tıklayın</span>
                                    <span class="text-[9px] text-slate-300 font-bold mt-1 uppercase">PNG, JPG (Max 2MB)</span>
                                </div>
                            </div>
                        </div>
                        @error('logo') <p class="mt-2 text-xs text-red-500 font-bold italic">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-6 p-6 bg-slate-50/50 rounded-3xl border border-slate-100">
                        <div class="h-10 w-10 bg-white rounded-xl shadow-sm flex items-center justify-center text-[var(--primary-color)]">
                            <i class="fas fa-power-off"></i>
                        </div>
                        <div class="flex-grow">
                             <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase">Durum</h4>
                             <p class="text-[10px] text-slate-400 font-bold">Markanın sistemde aktif olup olmayacağını belirleyin.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="active" value="1" class="sr-only peer" {{ $brand->active ? 'checked' : '' }}>
                            <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500 shadow-inner"></div>
                        </label>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex gap-4 text-center">
                        <button type="submit" class="flex-grow px-10 py-5 bg-slate-900 text-white font-black italic uppercase tracking-tighter rounded-2xl shadow-2xl shadow-slate-200/50 hover:bg-slate-800 transition-all transform hover:scale-105 active:scale-95 flex items-center justify-center gap-3">
                            <i class="fas fa-save text-orange-400"></i> Güncellemeleri Kaydet
                        </button>
                        <a href="{{ route('admin.brands.index') }}" class="px-10 py-5 bg-gray-100 text-gray-500 font-black italic uppercase tracking-tighter rounded-2xl hover:bg-gray-200 transition-all">Vazgeç</a>
                    </div>
                </div>
            </form>
        </div>

        <div>
            <!-- Info Sidebar -->
            <div class="space-y-6">
                <!-- Statistics Card -->
                <div class="bg-white p-8 rounded-[40px] shadow-xl shadow-slate-200/50 border border-slate-50 relative overflow-hidden group">
                    <div class="absolute right-0 bottom-0 p-8 opacity-10 scale-150 rotate-12 transition-transform group-hover:rotate-0">
                        <i class="fas fa-chart-pie text-slate-900 text-6xl"></i>
                    </div>
                    <h3 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                         <span class="w-2 h-2 bg-blue-400 rounded-full"></span> Marka Özeti
                    </h3>
                    <div class="flex flex-col gap-6 relative z-10 text-center">
                        <div>
                            <div class="text-4xl font-black italic tracking-tighter text-slate-900">{{ $brand->products()->count() }}</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Tanımlı Ürün</div>
                        </div>
                        <div class="pt-6 border-t border-slate-50">
                            <div class="text-xs font-bold text-slate-500 italic uppercase tracking-tighter">Oluşturulma: <span class="text-slate-900">{{ $brand->created_at->format('d/m/Y') }}</span></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[40px] shadow-xl shadow-slate-200/50 border border-slate-50">
                    <h4 class="text-xs font-black italic tracking-widest text-slate-400 uppercase mb-6 flex items-center gap-3">
                        <span class="w-2 h-2 bg-orange-400 rounded-full"></span> Güvenlik Notu
                    </h4>
                    <p class="text-[11px] text-slate-600 font-medium leading-relaxed italic">Marka silme işlemi için markaya tanımlı herhangi bir ürün olmaması gerekmektedir. Eğer markaya ait ürünler varsa, silme işlemi engellenecektir.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
