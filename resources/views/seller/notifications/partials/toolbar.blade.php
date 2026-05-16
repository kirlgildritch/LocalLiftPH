<div class="seller-notifications-toolbar">
    @foreach ($filterLabels as $key => $label)
        <a href="{{ route('seller.notifications.index', array_filter(['filter' => $key])) }}"
            class="seller-notifications-chip {{ $filter === $key ? 'is-active' : '' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
