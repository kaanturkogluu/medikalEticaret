@extends('layouts.user')

@section('title', 'Yorumlarım')

@section('content')
<div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
        <h2 class="font-black italic text-slate-900 uppercase tracking-tighter flex items-center gap-3">
            <i class="fas fa-comment-dots text-amber-500"></i> Değerlendirmelerim
        </h2>
        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest bg-white px-3 py-1 rounded-full border border-gray-100">
            Toplam {{ $comments->total() }} Yorum
        </span>
    </div>

    <div class="divide-y divide-gray-50">
        @forelse($comments as $comment)
        <div class="p-8 hover:bg-gray-50/50 transition-all flex flex-col md:flex-row gap-8">
            <!-- Product Info -->
            <div class="w-full md:w-48 shrink-0">
                <a href="{{ route('product.show', $comment->product) }}" target="_blank" class="block group">
                    <div class="aspect-square bg-gray-50 rounded-2xl p-4 mb-3 relative overflow-hidden flex items-center justify-center border border-gray-100 group-hover:border-amber-200 transition-colors">
                        <img src="{{ $comment->product->productImages->first()?->url ?? 'https://via.placeholder.com/200' }}" class="w-full h-full object-contain mix-blend-multiply group-hover:scale-110 transition-transform" alt="">
                    </div>
                    <h4 class="text-[11px] font-bold text-slate-900 line-clamp-2 leading-relaxed group-hover:text-amber-600 transition-colors">{{ $comment->product->name }}</h4>
                </a>
            </div>

            <!-- Comment Content -->
            <div class="flex-grow">
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-0.5 text-[10px]">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star {{ $i <= $comment->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                            @endfor
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>

                    @if($comment->is_approved)
                        <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-green-100 flex items-center gap-1.5 animate-pulse">
                            <i class="fas fa-check-circle"></i> Yayında
                        </span>
                    @else
                        <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-amber-100 flex items-center gap-1.5">
                            <i class="fas fa-clock"></i> Onay Bekliyor
                        </span>
                    @endif
                </div>

                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 relative group">
                    <i class="fas fa-quote-left absolute -top-3 -left-1 text-gray-200 text-2xl group-hover:text-amber-100 transition-colors"></i>
                    <p class="text-xs text-slate-600 font-medium leading-relaxed italic relative z-10">"{{ $comment->content }}"</p>
                </div>
                
                @if($comment->admin_reply)
                    <div class="mt-6 p-5 bg-slate-900 rounded-2xl relative border-l-4 border-amber-500 shadow-xl shadow-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] font-black text-amber-500 uppercase italic tracking-widest">Mağaza Yanıtı</span>
                            <span class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">{{ $comment->replied_at?->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-white/80 font-medium italic leading-relaxed">"{{ $comment->admin_reply }}"</p>
                    </div>
                @endif

                @if(!$comment->is_approved)
                    <p class="mt-4 text-[10px] text-slate-400 font-medium italic flex items-center gap-2">
                        <i class="fas fa-info-circle text-amber-400"></i> Yorumunuz en kısa sürede moderatörler tarafından incelenecektir.
                    </p>
                @endif
            </div>
        </div>
        @empty
        <div class="py-32 text-center flex flex-col items-center gap-6">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-200">
                <i class="fas fa-comment-slash text-3xl"></i>
            </div>
            <div>
                <p class="text-sm font-black text-slate-900 uppercase tracking-widest mb-1 italic">Henüz Bir Değerlendirmeniz Yok</p>
                <p class="text-xs text-slate-400 font-medium italic leading-relaxed">Aldığınız ürünlere yorum yaparak başkalarına yardımcı olabilirsiniz.</p>
            </div>
            <a href="{{ route('home') }}" class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-black italic uppercase tracking-tighter hover:bg-amber-500 transition-all shadow-xl hover:shadow-amber-500/20 active:scale-95">
                Ürünleri Keşfet
            </a>
        </div>
        @endforelse
    </div>

    @if($comments->hasPages())
    <div class="px-8 py-6 border-t border-gray-50">
        {{ $comments->links() }}
    </div>
    @endif
</div>
@endsection
