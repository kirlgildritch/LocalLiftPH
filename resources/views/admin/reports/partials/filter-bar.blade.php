<div class="filter-bar">
    <div class="chip is-active">All {{ $reports->count() }}</div>
    <div class="chip">Pending {{ $pendingCount }}</div>
    <div class="chip">Resolved {{ $resolvedCount }}</div>
    <div class="chip">Dismissed {{ $dismissedCount }}</div>
</div>
