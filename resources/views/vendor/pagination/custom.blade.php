@if ($paginator->hasPages())
    <nav class="dm-page">
        <ul class="dm-pagination d-flex">
            <li class="dm-pagination__item">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="dm-pagination__link pagination-control disabled"><span class="la la-angle-left"></span></span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" class="dm-pagination__link pagination-control" rel="prev"><span class="la la-angle-left"></span></a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="dm-pagination__link pagination-control"><span class="page-number">{{ $element }}</span></span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <a href="#" class="dm-pagination__link active"><span class="page-number">{{ $page }}</span></a>
                            @else
                                <a href="{{ $url }}" class="dm-pagination__link"><span class="page-number">{{ $page }}</span></a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="dm-pagination__link pagination-control" rel="next"><span class="la la-angle-right"></span></a>
                @else
                    <span class="dm-pagination__link pagination-control disabled"><span class="la la-angle-right"></span></span>
                @endif
            </li>
        </ul>
    </nav>
@endif
