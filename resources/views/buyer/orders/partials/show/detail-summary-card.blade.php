<div class="order-detail-grid">
    <div class="panel detail-summary-card">
        <div class="detail-summary-grid">
            <div>
                <span class="toolbar-label">Placed On</span>
                <p>{{ $groupPlacedAt?->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <span class="toolbar-label">Shops</span>
                <p>{{ $groupSummary['shops'] }}</p>
            </div>
            <div>
                <span class="toolbar-label">Items</span>
                <p>{{ $groupSummary['items'] }}</p>
            </div>
            <div>
                <span class="toolbar-label">Subtotal</span>
                <p>&#8369; {{ number_format($groupSummary['subtotal'], 2) }}</p>
            </div>
            <div>
                <span class="toolbar-label">Shipping</span>
                <p>&#8369; {{ number_format($groupSummary['shipping'], 2) }}</p>
            </div>
            <div>
                <span class="toolbar-label">Payment</span>
                <p>{{ $order->paymentMethodLabel() }}</p>
            </div>
            <div>
                <span class="toolbar-label">Payment Status</span>
                <p>{{ $order->paymentStatusLabel() }}</p>
            </div>
            <div>
                <span class="toolbar-label">Total</span>
                <p>&#8369; {{ number_format($groupSummary['total'], 2) }}</p>
            </div>
        </div>
    </div>
</div>
