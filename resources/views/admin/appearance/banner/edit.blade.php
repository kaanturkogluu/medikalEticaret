@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-20">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.appearance.banner.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-brand-500 transition-colors shadow-sm">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight italic uppercase">Banner Düzenle</h1>
                <p class="text-sm text-slate-500 mt-1">Mevcut banner kampanyanızı güncelleyin.</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl overflow-hidden p-10">
        <form action="{{ route('admin.appearance.banner.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-8" x-data="{ preview: '{{ asset('storage/' . $banner->image_path) }}' }">
            @csrf
            
            <!-- Image Upload -->
            <div class="space-y-4">
                <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Banner Görseli (Değiştirmek isterseniz seçin)</label>
                <div class="relative h-64 bg-slate-50 rounded-[30px] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center overflow-hidden transition-all hover:border-brand-300">
                    <img :src="preview" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black/30 opacity-0 hover:opacity-100 flex flex-col items-center justify-center text-white transition-opacity duration-300">
                        <i class="fas fa-camera text-4xl mb-2"></i>
                        <p class="text-xs font-bold italic uppercase tracking-widest leading-none">Görseli Değiştir</p>
                    </div>
                    <input type="file" name="image" @change="preview = URL.createObjectURL($event.target.files[0])" class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
                @error('image') <p class="text-xs text-rose-500 font-bold italic mt-2">{{ $message }}</p> @enderror
            </div>

            <!-- Fields Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Başlık</label>
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}" placeholder="BANNER BAŞLIĞI" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-md font-black text-slate-900 italic tracking-tighter focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all outline-none">
                </div>
                
                <!-- Subtitle -->
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Alt Başlık</label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="Kampanya detayları..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 text-sm font-medium text-slate-700 focus:bg-white focus:ring-4 focus:ring-brand-50 focus:border-brand-500 transition-all outline-none">
                </div>

            <!-- Buttons Section (Dynamic Repeater) -->
            <div class="space-y-4" x-data="{ 
                buttons: {{ $banner->buttons ? json_encode($banner->buttons) : "[{ 'text': 'ALIŞVERİŞE BAŞLA', 'link': '#', 'bg': '#FB923C', 'color': '#FFFFFF' }]" }},
                addButton() {
                    this.buttons.push({ text: 'YENİ BUTON', link: '#', bg: '#000000', color: '#FFFFFF' })
                },
                removeButton(index) {
                    this.buttons.splice(index, 1)
                }
            }">
                <div class="flex items-center justify-between pl-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Eylem Butonları</label>
                    <button type="button" @click="addButton()" class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black italic uppercase tracking-tighter hover:bg-emerald-100 transition-colors">
                        <i class="fas fa-plus mr-1"></i> YENİ BUTON EKLE
                    </button>
                </div>
                
                <div class="space-y-4">
                    <template x-for="(btn, index) in buttons" :key="index">
                        <div class="bg-slate-50 p-6 rounded-[24px] border border-slate-100 relative group/btn-row">
                            <button type="button" @click="removeButton(index)" x-show="buttons.length > 0" class="absolute -right-2 -top-2 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover/btn-row:opacity-100 transition-opacity shadow-lg">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                            
                            <div class="grid grid-cols-1 gap-5">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Buton Metni</label>
                                    <input type="text" :name="'buttons['+index+'][text]'" x-model="btn.text" placeholder="GÖRÜNEN METİN" class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm font-black italic tracking-tighter outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-50 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Yönlendirilecek Link</label>
                                    <input type="text" :name="'buttons['+index+'][link]'" x-model="btn.link" placeholder="/kategori/..." class="w-full bg-white border border-slate-200 rounded-2xl px-6 py-4 text-sm outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-50 transition-all font-medium">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Arka Plan Rengi</label>
                                        <input type="color" :name="'buttons['+index+'][bg]'" x-model="btn.bg" class="w-full h-14 rounded-2xl border border-slate-200 p-1 cursor-pointer bg-white">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Yazı Rengi</label>
                                        <input type="color" :name="'buttons['+index+'][color]'" x-model="btn.color" class="w-full h-14 rounded-2xl border border-slate-200 p-1 cursor-pointer bg-white">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Styling Section -->
            <div class="bg-slate-50 p-8 rounded-[30px] border border-slate-100 space-y-8">
                <div class="flex items-center gap-3 border-b border-slate-200 pb-4">
                    <i class="fas fa-paint-brush text-brand-500"></i>
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Yazı Tasarımı</h4>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Title Color -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Başlık Rengi</label>
                        <input type="color" name="title_color" value="{{ $banner->title_color ?? '#FFFFFF' }}" class="w-full h-12 rounded-xl border border-slate-200 p-1 cursor-pointer">
                    </div>
                    
                    <!-- Title Size -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Başlık Boyutu (px)</label>
                        <input type="number" name="title_size" value="{{ $banner->title_size ?? 60 }}" min="10" max="150" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-brand-50 outline-none transition-all">
                    </div>

                    <!-- Subtitle Color -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Alt Başlık Rengi</label>
                        <input type="color" name="subtitle_color" value="{{ $banner->subtitle_color ?? '#FFFFFF' }}" class="w-full h-12 rounded-xl border border-slate-200 p-1 cursor-pointer">
                    </div>
                    
                    <!-- Subtitle Size -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Alt Başlık Boyutu (px)</label>
                        <input type="number" name="subtitle_size" value="{{ $banner->subtitle_size ?? 12 }}" min="8" max="50" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-900 focus:ring-4 focus:ring-brand-50 outline-none transition-all">
                    </div>

                    <!-- Button BG Color -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Buton Arka Plan Rengi</label>
                        <input type="color" name="button_color" value="{{ $banner->button_color ?? '#FB923C' }}" class="w-full h-12 rounded-xl border border-slate-200 p-1 cursor-pointer">
                    </div>

                    <!-- Button Text Color -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Buton Yazı Rengi</label>
                        <input type="color" name="button_text_color" value="{{ $banner->button_text_color ?? '#FFFFFF' }}" class="w-full h-12 rounded-xl border border-slate-200 p-1 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Toggle Status -->
            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="flex flex-col gap-1">
                    <h4 class="text-xs font-black text-slate-900 uppercase italic tracking-tighter">Banner Durumu</h4>
                    <p class="text-[10px] text-slate-400 font-medium">Bu banner'ı yayından kaldırmak için kapatabilirsiniz.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-14 h-7 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500 shadow-inner"></div>
                </label>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-8 border-t border-slate-50">
                <button type="submit" class="bg-slate-900 text-white px-10 py-5 rounded-[20px] font-black italic shadow-2xl shadow-slate-200 hover:bg-brand-600 transition-all transform hover:-translate-y-1 flex items-center gap-3">
                    <i class="fas fa-save text-lg opacity-50"></i>
                    <span>DEĞİŞİKLİKLERİ KAYDET</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
