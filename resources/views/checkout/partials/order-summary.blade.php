<div class="order-summary panel">
    <span class="section-kicker">Final Review</span>
    <h3>Review Your Order</h3>

    <div class="review-checklist">
        <div>
            <i class="fa-solid fa-location-dot"></i>
            <span>Delivering to</span>
            <strong>{{ $defaultAddress?->city ?? 'Saved address' }}{{ filled($defaultAddress?->province) ? ', ' . $defaultAddress->province : '' }}</strong>
        </div>
        <div>
            <i class="fa-solid fa-calendar-check"></i>
            <span>Estimated delivery</span>
            <strong>{{ $overallDeliveryEstimate['date_range'] ?? '3-5 days' }}</strong>
        </div>
        <div>
            <i class="fa-solid fa-money-bill-wave"></i>
            <span>Payment</span>
            <strong data-payment-summary>{{ $selectedPayment['short_label'] }}</strong>
        </div>
    </div>

    <div class="summary-items">
        @forelse(($groupedCartItems ?? collect()) as $sellerId => $sellerCartItems)
            @php
                $seller = $sellerCartItems->first()?->product?->user;
                $sellerSubtotal = $sellerCartItems->sum(fn($item) => (float) ($item->variant?->price ?? $item->product->price ?? 0) * (int) $item->quantity);
                $sellerShipping = $sellerCartItems->sum(fn($item) => (float) ($item->product->shipping_fee ?? 0) * (int) $item->quantity);
                $estimate = ($deliveryEstimates ?? collect())->get($sellerId);
            @endphp
            <div class="summary-shop-group">
                <div class="summary-shop-head">
                    <div>
                        <h4>{{ $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller' }}</h4>
                        <p>{{ $sellerCartItems->count() }} item{{ $sellerCartItems->count() !== 1 ? 's' : '' }} &middot; Delivery {{ $estimate['date_range'] ?? '3-5 days' }}</p>
                    </div>

                    <div class="summary-price">
                        <strong>&#8369; {{ number_format($sellerSubtotal + $sellerShipping, 2) }}</strong>
                        <span>Shop total</span>
                    </div>
                </div>

                @foreach($sellerCartItems as $item)
                    @php
                        $variant = $item->variant;
                        $unitPrice = (float) ($variant?->price ?? $item->product->price ?? 0);
                        $productImage = $variant?->image ?: ($item->product->image ?? null);
                    @endphp
                    <div class="summary-item">
                        <div class="summary-product">
                            <div class="summary-image">
                                <img src="{{ $productImage ? asset('storage/' . $productImage) : asset('assets/images/default-product.png') }}"
                                    alt="{{ $item->product->name ?? 'Product' }}">
                            </div>
                            <div>
                                <h4>{{ $item->product->name ?? 'Product' }}</h4>
                                @if($variant)
                                    <p>Option: {{ $variant->displayName() }}</p>
                                @endif
                                <p>Qty {{ $item->quantity }} &middot; Shipping &#8369; {{ number_format(((float) ($item->product->shipping_fee ?? 0)) * (int) $item->quantity, 2) }}</p>
                            </div>
                        </div>

                        <div class="summary-price">
                            <strong>&#8369; {{ number_format($unitPrice * (int) $item->quantity, 2) }}</strong>
                            <span>&#8369; {{ number_format($unitPrice, 2) }} each</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <p>Your cart is empty.</p>
        @endforelse
    </div>

    <div class="summary-line">
        <span>Subtotal</span>
        <strong>&#8369; {{ number_format($subtotal, 2) }}</strong>
    </div>

    <div class="summary-line">
        <span>Shipping Fee</span>
        <strong>&#8369; {{ number_format($shippingFee, 2) }}</strong>
    </div>

    <div class="summary-total">
        <span>Total</span>
        <strong>&#8369; {{ number_format($total, 2) }}</strong>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-submit-form" data-enable-loading>
        @csrf
        @foreach(($selectedCartItemIds ?? collect()) as $selectedCartItemId)
            <input type="hidden" name="selected_cart_items[]" value="{{ $selectedCartItemId }}">
        @endforeach
        <button
            type="submit"
            class="action-btn primary-btn full-btn"
            data-enable-loading
            data-loading-text="Placing Order..."
            {{ ($hasSavedAddress ?? false) ? '' : 'disabled' }}
        >
            Place Order - <span data-payment-button-label>{{ $selectedPayment['short_label'] }}</span>
        </button>
    </form>
</div>
