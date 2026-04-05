@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Yorum Yönetimi</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kullanıcı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Yorum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Puan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" x-data="{ openReply: null }">
                @foreach($comments as $comment)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-black text-slate-900 uppercase italic tracking-tighter">{{ $comment->user->name }}</div>
                        <div class="text-[10px] text-slate-400 font-bold">{{ $comment->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('product.show', $comment->product) }}" target="_blank" class="block group">
                            <div class="text-[11px] font-black text-slate-900 uppercase italic tracking-tighter group-hover:text-amber-600 transition-colors max-w-[200px] truncate">{{ $comment->product->name }}</div>
                            <div class="text-[9px] text-slate-400 font-bold mt-1 inline-flex items-center gap-1 group-hover:text-amber-500 underline underline-offset-2">
                                <i class="fas fa-external-link-alt text-[8px]"></i> Ürünü Detayda Gör
                            </div>
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-slate-800 font-medium leading-relaxed max-w-md">{{ $comment->content }}</div>
                        <div class="text-[10px] text-slate-400 mt-1 italic font-bold">
                            <i class="far fa-calendar-alt mr-1"></i> {{ $comment->created_at->format('d.m.Y H:i') }}
                        </div>
                        @if($comment->admin_reply)
                            <div class="mt-3 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r-xl">
                                <p class="text-[10px] font-black text-amber-800 uppercase italic mb-1">Mağaza Yanıtı:</p>
                                <p class="text-xs text-amber-900 font-medium italic">"{{ $comment->admin_reply }}"</p>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex text-amber-400 text-xs">
                            @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $comment->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($comment->is_approved)
                            <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-full bg-green-100 text-green-700 border border-green-200">Yayında</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-[9px] font-black uppercase tracking-widest rounded-full bg-amber-100 text-amber-700 border border-amber-200">Sırada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-[11px] font-black uppercase italic tracking-tighter space-x-3">
                        @if(!$comment->is_approved)
                        <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800">Onayla</button>
                        </form>
                        @endif
                        
                        <button @click="openReply = (openReply === {{ $comment->id }} ? null : {{ $comment->id }})" class="text-amber-600 hover:text-amber-800 font-black">
                            {{ $comment->admin_reply ? 'Düzenle' : 'Yanıtla' }}
                        </button>

                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
                <!-- Reply Form Row -->
                <tr x-show="openReply === {{ $comment->id }}" x-cloak x-transition class="bg-gray-50/50">
                    <td colspan="6" class="px-6 py-6 transition-all">
                        <div class="max-w-2xl mx-auto bg-white p-6 rounded-3xl border-2 border-amber-100 shadow-xl shadow-amber-900/5">
                            <h4 class="text-xs font-black text-slate-800 uppercase italic tracking-tighter mb-4 flex items-center gap-2">
                                <i class="fas fa-reply text-amber-500"></i> Mağaza Yanıtı Yazın
                            </h4>
                            <form action="{{ route('admin.comments.reply', $comment) }}" method="POST" class="space-y-4">
                                @csrf
                                <textarea name="admin_reply" rows="3" class="w-full border-2 border-gray-100 rounded-2xl p-4 text-xs font-medium focus:border-amber-400 focus:ring-0 transition-all placeholder:text-gray-300" placeholder="Müşteriye nazik bir yanıt verin..." required>{{ $comment->admin_reply }}</textarea>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="openReply = null" class="px-6 py-2.5 text-[10px] font-black uppercase text-slate-400 hover:text-slate-900 tracking-widest italic transition-all">Vazgeç</button>
                                    <button type="submit" class="px-8 py-2.5 bg-amber-500 text-white text-[10px] font-black uppercase rounded-xl hover:bg-amber-600 shadow-lg shadow-amber-500/20 transition-all active:scale-95">Yanıtı Gönder</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $comments->links() }}
    </div>
</div>
@endsection
