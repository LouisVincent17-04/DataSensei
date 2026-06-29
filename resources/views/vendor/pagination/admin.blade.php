@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Pagination Navigation">
        <div class="admin-pagination__summary">
            Showing
            <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
            to
            <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
        </div>

        <div class="admin-pagination__links">
            @if ($paginator->onFirstPage())
                <span class="admin-page-link is-disabled" aria-disabled="true" aria-label="Previous page">‹</span>
            @else
                <a class="admin-page-link" href="{{ $paginator->previousPageUrl() }}{{ isset($fragment) ? '#' . $fragment : '' }}" rel="prev" aria-label="Previous page">‹</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="admin-page-link is-disabled" aria-hidden="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="admin-page-link is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="admin-page-link" href="{{ $url }}{{ isset($fragment) ? '#' . $fragment : '' }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="admin-page-link" href="{{ $paginator->nextPageUrl() }}{{ isset($fragment) ? '#' . $fragment : '' }}" rel="next" aria-label="Next page">›</a>
            @else
                <span class="admin-page-link is-disabled" aria-disabled="true" aria-label="Next page">›</span>
            @endif
        </div>
    </nav>
@endif
