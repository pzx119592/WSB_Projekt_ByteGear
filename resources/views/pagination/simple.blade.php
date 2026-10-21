@if ($paginator->hasPages())
    <nav class="pagination-nav" aria-label="Strony wyników">

        @if ($paginator->onFirstPage())
            <span class="page-disabled" aria-disabled="true">← Poprzednia</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Poprzednia</a>
        @endif

        <span class="page-number">Strona {{ $paginator->currentPage() }}</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">Następna →</a>
        @else
            <span class="page-disabled" aria-disabled="true">Następna →</span>
        @endif

    </nav>

@endif
