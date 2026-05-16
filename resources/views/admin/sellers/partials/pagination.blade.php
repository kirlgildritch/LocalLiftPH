@php
    $startPage = max(1, $sellers->currentPage() - 1);
    $endPage = min($sellers->lastPage(), $sellers->currentPage() + 1);
@endphp

<div class="pagination-bar">
    @if ($sellers->onFirstPage())
        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-left"></i></span>
    @else
        <a class="pagination-button" href="{{ $sellers->previousPageUrl() }}"><i
                class="fa-solid fa-chevron-left"></i></a>
    @endif

    @foreach ($sellers->getUrlRange($startPage, $endPage) as $page => $url)
        <a class="pagination-button {{ $page === $sellers->currentPage() ? 'is-active' : '' }}"
            href="{{ $url }}">{{ $page }}</a>
    @endforeach

    @if ($sellers->hasMorePages())
        <a class="pagination-button" href="{{ $sellers->nextPageUrl() }}"><i
                class="fa-solid fa-chevron-right"></i></a>
    @else
        <span class="pagination-button is-disabled"><i class="fa-solid fa-chevron-right"></i></span>
    @endif
</div>
