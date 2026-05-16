<div class="order-card-footer">
    <div class="total-text">
        <div>
            <span>Subtotal</span>
            <strong>&#8369; {{ number_format($shopSubtotal, 2) }}</strong>
        </div>
        <div>
            <span>Shipping</span>
            <strong>&#8369; {{ number_format($shopOrder->shipping_fee, 2) }}</strong>
        </div>
        <div>
            <span>Order Total</span>
            <strong>&#8369; {{ number_format($shopOrder->total_price, 2) }}</strong>
        </div>
    </div>

    <div class="order-actions">
        @if($shopOrder->canBeCancelled())
            <button type="button" class="order-btn danger-btn open-cancel-order" data-order-id="{{ $shopOrder->id }}"
                data-order-action="{{ route('buyer.orders.cancel', $shopOrder) }}">
                Cancel Order
            </button>
        @elseif($shopOrder->canConfirmReceipt())
            <form action="{{ route('buyer.orders.received', $shopOrder) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="order-btn primary-btn">
                    Order Received
                </button>
            </form>
        @elseif(in_array($shopOrder->shippingStatus(), [\App\Models\Order::SHIPPING_COMPLETED, \App\Models\Order::SHIPPING_CANCELLED], true))
            @if($returnRequest)
                <span class="order-btn secondary-btn is-static">
                    Return: {{ $returnRequest->statusLabel() }}
                </span>
            @elseif($shopOrder->canRequestReturnRefund())
                <button type="button" class="order-btn danger-btn open-return-request"
                    data-order-action="{{ route('buyer.orders.return-request', $shopOrder) }}">
                    Return / Refund
                </button>
            @endif



            <form action="{{ route('buyer.orders.buyAgain', $shopOrder) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="order-btn primary-btn">
                    {{ $shopOrder->shippingStatus() === \App\Models\Order::SHIPPING_CANCELLED ? 'Reorder' : 'Buy Again' }}
                </button>
            </form>
        @endif
    </div>
</div>