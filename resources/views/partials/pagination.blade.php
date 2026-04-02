@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 py-10 border-t border-gray-100 mt-10">
        
        <!-- Total count -->
        <div class="text-sm font-bold text-gray-500 italic">
            <span class="text-slate-900 border-b-2 border-[var(--primary-color)]">{{ $paginator->total() }}</span> Ürün bulundu
        </div>

        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 border border-gray-200 rounded text-gray-300 text-sm font-bold cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-2"></i> Önceki
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 border border-gray-200 rounded text-gray-600 hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors text-sm font-bold">
                    <i class="fas fa-chevron-left mr-2"></i> Önceki
                </a>
            @endif

            {{-- Pagination Elements --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="px-3 py-2 text-gray-400">...</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-10 h-10 flex items-center justify-center bg-[var(--primary-color)] text-white rounded font-black text-sm shadow-lg shadow-orange-100 ring-2 ring-white">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center border border-gray-200 rounded text-gray-600 hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors font-bold text-sm bg-white">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 border border-gray-200 rounded text-gray-600 hover:border-[var(--primary-color)] hover:text-[var(--primary-color)] transition-colors text-sm font-bold">
                    Sonraki <i class="fas fa-chevron-right ml-2"></i>
                </a>
            @else
                <span class="px-4 py-2 border border-gray-200 rounded text-gray-300 text-sm font-bold cursor-not-allowed">
                    Sonraki <i class="fas fa-chevron-right ml-2"></i>
                </span>
            @endif
        </nav>
    </div>
@endif
