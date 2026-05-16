@php
    $hasRateableItems = $order->shippingStatus() === \App\Models\Order::SHIPPING_COMPLETED
        && $order->items->contains(fn($item) => $item->product && !$item->review);
    $returnRequest = $order->returnRequest;
@endphp

<article class="order-card panel">
    <div class="order-card-top">
        <div class="shop-info">
            <i class="fa-solid fa-store"></i>
            <div>
                <span class="toolbar-label">{{ $order->shopDisplayName() }}</span>
            </div>
        </div>

        <div class="order-status {{ $order->shippingToneClass() }}">
            {{ $order->shippingStatusLabel() }}
        </div>
    </div>

    <div class="order-items">
        @foreach($order->items as $item)
            @include('buyer.orders.partials.index.item-row', ['order' => $order, 'item' => $item])
        @endforeach
    </div>

    <div class="order-card-footer">
        <div class="total-text">
            <span>{{ $order->paymentMethodShortLabel() }} | {{ $order->paymentStatusLabel() }}</span>
            <span>Total</span>
            <strong>P{{ number_format($order->total_price, 2) }}</strong>
        </div>

        <div class="order-actions">
            <a href="{{ route('buyer.orders.show', $order) }}" class="order-btn secondary-btn">View Summary</a>

            @if($order->canBeCancelled())
                <button type="button" class="order-btn secondary-btn open-cancel-order"
                    data-order-id="{{ $order->id }}"
                    data-order-action="{{ route('buyer.orders.cancel', $order) }}">
                    Cancel Order
                </button>
            @elseif($order->canConfirmReceipt())
                <form action="{{ route('buyer.orders.received', $order) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="order-btn primary-btn">Order Received</button>
                </form>
            @elseif(in_array($order->shippingStatus(), [\App\Models\Order::SHIPPING_COMPLETED, \App\Models\Order::SHIPPING_CANCELLED], true))
                @if($returnRequest)
                    <span class="order-btn secondary-btn is-static">
                        Return: {{ $returnRequest->statusLabel() }}
                    </span>
                @elseif($order->canRequestReturnRefund())
                    <button type="button" class="order-btn danger-btn open-return-request"
                        data-order-action="{{ route('buyer.orders.return-request', $order) }}">
                        Return / Refund
                    </button>
                @endif

                @if($hasRateableItems)
                    <a href="{{ route('buyer.orders.show', $order) }}#rate-products"
                        class="order-btn secondary-btn">
                        Rate Products
                    </a>
                @endif

                <form action="{{ route('buyer.orders.buyAgain', $order) }}" method="POST"
                    style="display: inline;">
                    @csrf
                    <button type="submit" class="order-btn primary-btn">
                        {{ $order->shippingStatus() === \App\Models\Order::SHIPPING_CANCELLED ? 'Reorder' : 'Buy Again' }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</article>
