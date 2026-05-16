<div class="status-tabs" role="tablist" aria-label="Product moderation statuses">
    @foreach ($statusMeta as $tab => $meta)
        <a class="chip {{ $currentTab === $tab ? 'is-active' : '' }}"
            href="{{ route('admin.products', array_merge($tabQuery, ['status' => $tab])) }}">
            <span>{{ $meta['label'] }}</span>
            <strong>{{ $statusCounts[$tab] ?? 0 }}</strong>
        </a>
    @endforeach
</div>
