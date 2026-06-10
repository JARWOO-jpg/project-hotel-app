@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())
        <span style="opacity:0.4">← Prev</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}">← Prev</a>
    @endif
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}">Next →</a>
    @else
        <span style="opacity:0.4">Next →</span>
    @endif
</div>
@endif
