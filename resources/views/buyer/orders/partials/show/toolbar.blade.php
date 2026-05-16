<div class="orders-toolbar panel">
    <div class="toolbar-copy">
        <span class="toolbar-label">Details</span>
        <h2>
            @if($groupSummary['shops'] > 1)
                Checkout Summary
            @else
                Order #{{ $order->id }}
            @endif
        </h2>
    </div>

    <div class="order-actions">
        <a href="{{ route('buyer.orders') }}" class="order-btn secondary-btn">Back to Orders</a>
    </div>
</div>
