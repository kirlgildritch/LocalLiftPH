<section class="checkout-card panel">
    <div class="card-header">
        <div class="step-title">
            <span class="step-number">2</span>
            <div>
                <span class="toolbar-label">Step</span>
                <h3>Shipping Method</h3>
            </div>
        </div>
        <span class="action-link">Auto</span>
    </div>

    <div class="card-body">
        <div class="delivery-method-list">
            @foreach(($groupedCartItems ?? collect()) as $sellerId => $sellerCartItems)
                @php
                    $seller = $sellerCartItems->first()?->product?->user;
                    $estimate = ($deliveryEstimates ?? collect())->get($sellerId);
                    $sellerShipping = $sellerCartItems->sum(fn($item) => (float) ($item->product->shipping_fee ?? 0) * (int) $item->quantity);
                @endphp

                <article class="delivery-method-card">
                    <div class="delivery-method-card__icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>

                    <div class="delivery-method-card__copy">
                        <strong>{{ $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller' }}</strong>
                        <span>{{ $estimate['label'] ?? 'Standard local courier' }}</span>
                        <p>
                            Estimated delivery:
                            <b>{{ $estimate['date_range'] ?? '3-5 days' }}</b>
                        </p>
                        @if(!empty($estimate['is_fallback']))
                            <small>Estimate uses standard courier timing because seller location is limited.</small>
                        @endif
                    </div>

                    <div class="delivery-method-card__price">
                        <span>Shipping</span>
                        <strong>&#8369; {{ number_format($sellerShipping, 2) }}</strong>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
