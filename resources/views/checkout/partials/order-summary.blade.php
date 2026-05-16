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
        @forelse(($checkoutSummary['groups'] ?? collect()) as $shopSummary)
            <div class="summary-shop-group">
                <div class="summary-shop-head">
                    <div>
                        <h4>{{ $shopSummary['seller_name'] }}</h4>
                        <p>{{ $shopSummary['item_count'] }} item{{ $shopSummary['item_count'] !== 1 ? 's' : '' }} &middot; Delivery {{ $shopSummary['delivery_range'] }}</p>
                    </div>

                    <div class="summary-price">
                        <strong>&#8369; {{ number_format($shopSummary['shop_total'], 2) }}</strong>
                        <span>Shop total</span>
                    </div>
                </div>

                @foreach($shopSummary['items'] as $itemSummary)
                    <div class="summary-item">
                        <div class="summary-product">
                            <div class="summary-image">
                                <img src="{{ $itemSummary['image_url'] }}" alt="{{ $itemSummary['product_name'] }}">
                            </div>
                            <div>
                                <h4>{{ $itemSummary['product_name'] }}</h4>
                                @if($itemSummary['variant_name'])
                                    <p>Option: {{ $itemSummary['variant_name'] }}</p>
                                @endif
                                <p>Qty {{ $itemSummary['quantity'] }} &middot; Shipping &#8369; {{ number_format($itemSummary['shipping_total'], 2) }}</p>
                            </div>
                        </div>

                        <div class="summary-price">
                            <strong>&#8369; {{ number_format($itemSummary['line_subtotal'], 2) }}</strong>
                            <span>
                                @if($itemSummary['has_discount'])
                                    <span class="checkout-price-original">&#8369; {{ number_format($itemSummary['original_unit_price'], 2) }}</span>
                                @endif
                                <span class="{{ $itemSummary['has_discount'] ? 'checkout-price-sale' : '' }}">&#8369; {{ number_format($itemSummary['unit_price'], 2) }} each</span>
                            </span>
                        </div>
                    </div>
                @endforeach

                @include('vouchers.partials.buyer-voucher-list', [
                    'vouchers' => ($availableSellerVouchers ?? collect())->get($shopSummary['seller_id'], collect()),
                    'title' => 'Seller Vouchers',
                ])
            </div>
        @empty
            <p>Your cart is empty.</p>
        @endforelse
    </div>

    <div class="summary-line">
        <span>Subtotal</span>
        <strong>&#8369; {{ number_format($checkoutSummary['subtotal'] ?? $subtotal, 2) }}</strong>
    </div>

    <div class="summary-line">
        <span>Shipping Fee</span>
        <strong>&#8369; {{ number_format($checkoutSummary['shipping_fee'] ?? $shippingFee, 2) }}</strong>
    </div>

    <div class="summary-line">
        <span>Voucher{{ filled($checkoutSummary['voucher_code'] ?? null) ? ' (' . $checkoutSummary['voucher_code'] . ')' : '' }}</span>
        <strong>
            @if(($checkoutSummary['voucher_discount'] ?? 0) > 0)
                - &#8369; {{ number_format($checkoutSummary['voucher_discount'], 2) }}
            @else
                Optional
            @endif
        </strong>
    </div>

    <div class="summary-total">
        <span>Total</span>
        <strong>&#8369; {{ number_format($checkoutSummary['total'] ?? $total, 2) }}</strong>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-submit-form" data-enable-loading>
        @csrf
        @foreach(($selectedCartItemIds ?? collect()) as $selectedCartItemId)
            <input type="hidden" name="selected_cart_items[]" value="{{ $selectedCartItemId }}">
        @endforeach
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="voucher_code">Voucher / Coupon</label>
            <input type="text" id="voucher_code" name="voucher_code" value="{{ $voucherCode }}" placeholder="Enter code, if any">
            @error('voucher_code')
                <small class="error-text">{{ $message }}</small>
            @enderror
            @if(($checkoutSummary['voucher_discount'] ?? 0) > 0)
                <small class="success-text">{{ $checkoutSummary['voucher_label'] }} applied.</small>
            @endif
        </div>
        <button
            type="submit"
            class="action-btn full-btn"
            formmethod="GET"
            formaction="{{ route('checkout.index') }}"
            data-loading-text="Applying..."
        >
            Apply Voucher
        </button>
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
