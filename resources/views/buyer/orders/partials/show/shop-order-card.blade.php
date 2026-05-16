@php
    $shopHasRateableItems = $shopOrder->shippingStatus() === \App\Models\Order::SHIPPING_COMPLETED
        && $shopOrder->items->contains(fn($item) => $item->product && !$item->review);
    $shopSubtotal = $shopOrder->subtotalAmount();
    $returnRequest = $shopOrder->returnRequest;
@endphp

<article class="order-card panel">
    <div class="order-card-top">
        <div class="shop-info">
            <i class="fa-solid fa-store"></i>
            <div>
                <span class="toolbar-label">{{ $shopOrder->shopDisplayName() }}</span>
            </div>
        </div>

        <div class="order-status {{ $shopOrder->shippingToneClass() }}">
            {{ $shopOrder->shippingStatusLabel() }}
        </div>
    </div>

    @if($groupOrders->count() > 1)
        <div class="order-card-body order-card-body--summary">
            <div class="order-product-info">
                <p>Date: {{ $shopOrder->created_at->format('M d, Y h:i A') }}</p>
                <p>Items: {{ $shopOrder->itemCount() }}</p>
                <p>Payment: {{ $shopOrder->paymentMethodLabel() }} | {{ $shopOrder->paymentStatusLabel() }}</p>
                <p>Shipping Fee: &#8369; {{ number_format($shopOrder->shipping_fee, 2) }}</p>
            </div>

            <div class="order-product-price">
                &#8369; {{ number_format($shopOrder->total_price, 2) }}
            </div>
        </div>
    @endif

    <div class="order-items">
        @foreach($shopOrder->items as $item)
            @include('buyer.orders.partials.show.item-row', ['shopOrder' => $shopOrder, 'item' => $item])
        @endforeach
    </div>

    @if($returnRequest)
        @include('buyer.orders.partials.show.return-request-panel', ['returnRequest' => $returnRequest])
    @endif

    @if($shopOrder->payment_status === \App\Models\Order::PAYMENT_PENDING)
        <div class="payment-instruction-panel">
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <strong>{{ $shopOrder->paymentMethodLabel() }}</strong>
                <p>{{ $shopOrder->paymentInstruction() }}</p>
            </div>
        </div>
    @endif

    @include('buyer.orders.partials.show.card-footer', [
        'shopOrder' => $shopOrder,
        'shopSubtotal' => $shopSubtotal,
        'shopHasRateableItems' => $shopHasRateableItems,
        'returnRequest' => $returnRequest,
    ])
</article>
