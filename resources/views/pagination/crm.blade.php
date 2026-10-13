<div class="pagination-links">
    @if ($paginator->onFirstPage())<span class="disabled" aria-disabled="true"><x-icon name="chevron-left" size="13" /></span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page"><x-icon name="chevron-left" size="13" /></a>@endif
    @foreach ($elements as $element)
        @if (is_string($element))<span>{{ $element }}</span>@endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())<span class="active" aria-current="page">{{ $page }}</span>@else<a href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>@endif
            @endforeach
        @endif
    @endforeach
    @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page"><x-icon name="chevron-right" size="13" /></a>@else<span class="disabled" aria-disabled="true"><x-icon name="chevron-right" size="13" /></span>@endif
</div>
