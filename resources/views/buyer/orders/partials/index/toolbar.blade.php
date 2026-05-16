<div class="orders-toolbar panel">
    <div class="toolbar-copy">
        <span class="toolbar-label">History</span>
        <h2>My Orders</h2>
    </div>

    <div class="orders-tabs">
        <a href="{{ route('buyer.orders') }}" class="tab-btn {{ $currentStatus === 'all' ? 'active' : '' }}">
            All ({{ $statusCounts->sum() }})
        </a>
        <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::SHIPPING_PENDING]) }}"
            class="tab-btn {{ $currentStatus === \App\Models\Order::SHIPPING_PENDING ? 'active' : '' }}">
            Pending ({{ $statusCounts->get(\App\Models\Order::SHIPPING_PENDING, 0) }})
        </a>
        <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::SHIPPING_TO_SHIP]) }}"
            class="tab-btn {{ $currentStatus === \App\Models\Order::SHIPPING_TO_SHIP ? 'active' : '' }}">
            To Ship ({{ $statusCounts->get(\App\Models\Order::SHIPPING_TO_SHIP, 0) }})
        </a>
        <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::SHIPPING_SHIPPED]) }}"
            class="tab-btn {{ $currentStatus === \App\Models\Order::SHIPPING_SHIPPED ? 'active' : '' }}">
            Shipped ({{ $statusCounts->get(\App\Models\Order::SHIPPING_SHIPPED, 0) }})
        </a>
        <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::SHIPPING_COMPLETED]) }}"
            class="tab-btn {{ $currentStatus === \App\Models\Order::SHIPPING_COMPLETED ? 'active' : '' }}">
            Completed ({{ $statusCounts->get(\App\Models\Order::SHIPPING_COMPLETED, 0) }})
        </a>
        <a href="{{ route('buyer.orders', ['status' => \App\Models\Order::SHIPPING_CANCELLED]) }}"
            class="tab-btn {{ $currentStatus === \App\Models\Order::SHIPPING_CANCELLED ? 'active' : '' }}">
            Cancelled ({{ $statusCounts->get(\App\Models\Order::SHIPPING_CANCELLED, 0) }})
        </a>
    </div>
</div>
