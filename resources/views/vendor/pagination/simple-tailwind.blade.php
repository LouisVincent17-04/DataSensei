{{-- Previous / next pagination for simplePaginate(), matching the shared control. --}}
@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Pagination Navigation">
        <div class="admin-pagination__links">
            @if ($paginator->onFirstPage())
                <span class="admin-page-link is-disabled" aria-disabled="true">‹ Previous</span>
            @else
                <a class="admin-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Previous</a>
            @endif

            @if ($paginator->hasMorePages())
                <a class="admin-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Next ›</a>
            @else
                <span class="admin-page-link is-disabled" aria-disabled="true">Next ›</span>
            @endif
        </div>
    </nav>
@endif
