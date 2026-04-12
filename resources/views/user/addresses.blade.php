@extends('layouts.user')

@section('title', 'Adreslerim')

@section('content')
<div class="space-y-5">

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <h1 class="font-black text-xl text-gray-900">Adres Bilgilerim</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        {{-- Existing Addresses --}}
        @foreach($addresses as $address)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 relative group">
            @if($address->is_default)
            <span class="absolute top-4 right-4 text-[10px] font-black bg-orange-100 text-orange-600 px-3 py-1 rounded-full uppercase tracking-widest">Varsayılan</span>
            @endif
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-{{ $address->title === 'İş' ? 'building' : 'home' }} text-orange-400"></i>
                </div>
                <div>
                    <p class="font-black text-sm text-gray-900 uppercase">{{ $address->title }}</p>
                    <p class="text-xs text-gray-400 font-medium">{{ $address->full_name }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">{{ $address->address }}</p>
            <p class="text-sm text-gray-500 mt-1 font-medium">{{ $address->district }} / {{ $address->city }}</p>
            <p class="text-sm text-gray-400">{{ $address->phone }}</p>

            <form action="{{ route('user.addresses.destroy', $address->id) }}" method="POST" class="mt-4">
                @csrf @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Bu adresi silmek istediğinize emin misiniz?')"
                    class="text-xs font-bold text-red-400 hover:text-red-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-trash"></i> Adresi Sil
                </button>
            </form>
        </div>
        @endforeach

        {{-- Add New Address Form --}}
        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-plus text-gray-400"></i>
                </div>
                <h2 class="font-black text-sm text-gray-700 uppercase">Yeni Adres Ekle</h2>
            </div>

            <form action="{{ route('user.addresses.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Adres Başlığı</label>
                        <input type="text" name="title" placeholder="Ev, İş..." value="{{ old('title') }}" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all">
                        @error('title') <p class="text-red-400 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Ad Soyad</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Telefon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">İl</label>
                        <select name="city" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all bg-white">
                            <option value="">Seçiniz</option>
                            @php
                                $cities = ["Adana", "Adıyaman", "Afyonkarahisar", "Ağrı", "Amasya", "Ankara", "Antalya", "Artvin", "Aydın", "Balıkesir", "Bilecik", "Bingöl", "Bitlis", "Bolu", "Burdur", "Bursa", "Çanakkale", "Çankırı", "Çorum", "Denizli", "Diyarbakır", "Edirne", "Elazığ", "Erzincan", "Erzurum", "Eskişehir", "Gaziantep", "Giresun", "Gümüşhane", "Hakkari", "Hatay", "Isparta", "Mersin", "İstanbul", "İzmir", "Kars", "Kastamonu", "Kayseri", "Kırklareli", "Kırşehir", "Kocaeli", "Konya", "Kütahya", "Malatya", "Manisa", "Kahramanmaraş", "Mardin", "Muğla", "Muş", "Nevşehir", "Niğde", "Ordu", "Rize", "Sakarya", "Samsun", "Siirt", "Sinop", "Sivas", "Tekirdağ", "Tokat", "Trabzon", "Tunceli", "Şanlıurfa", "Uşak", "Van", "Yozgat", "Zonguldak", "Aksaray", "Bayburt", "Karaman", "Kırıkkale", "Batman", "Şırnak", "Bartın", "Ardahan", "Iğdır", "Yalova", "Karabük", "Kilis", "Osmaniye", "Düzce"];
                            @endphp
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">İlçe</label>
                        <input type="text" name="district" value="{{ old('district') }}" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Posta Kodu</label>
                        <input type="text" name="zip_code" value="{{ old('zip_code') }}"
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">Açık Adres</label>
                    <textarea name="address" rows="2" required
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-medium focus:outline-none focus:border-orange-400 transition-all resize-none">{{ old('address') }}</textarea>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" class="w-4 h-4 text-orange-500 rounded focus:ring-orange-400">
                    <span class="text-xs font-medium text-gray-600">Varsayılan adres olarak belirle</span>
                </label>
                <button type="submit" class="w-full py-3 bg-orange-500 text-white text-xs font-black rounded-xl hover:bg-orange-600 transition-all">
                    <i class="fas fa-plus mr-2"></i> Adresi Kaydet
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
