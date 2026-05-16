<div class="variant-modal-shell" data-variant-modal hidden aria-hidden="true">
    <div class="variant-modal-backdrop" data-close-variant-modal></div>
    <div class="variant-modal-card" role="dialog" aria-modal="true" aria-labelledby="variant-modal-title">
        <div class="variant-modal-header">
            <div>
                <span class="section-kicker">Options</span>
                <h3 id="variant-modal-title">Choose product option</h3>
                <p>Select the exact variant you want to add to cart.</p>
            </div>
            <button type="button" class="variant-modal-close" data-close-variant-modal aria-label="Close options">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="variant-choice-grid variant-choice-grid--modal">
            @foreach($productPage->activeVariants as $variant)
                <button type="button"
                    class="variant-choice"
                    data-variant-choice
                    data-variant-id="{{ $variant->id }}"
                    data-variant-price="{{ $product->discountedPrice((float) $variant->price) }}"
                    data-variant-original-price="{{ (float) $variant->price }}"
                    data-variant-stock="{{ (int) $variant->stock }}"
                    {{ (int) $variant->stock <= 0 ? 'disabled' : '' }}>
                    <strong>{{ $variant->displayName() }}</strong>
                    <small>
                        @if($product->hasActiveDiscount() && $product->discountedPrice((float) $variant->price) < (float) $variant->price)
                            <span class="variant-price-original">&#8369; {{ number_format($variant->price, 2) }}</span>
                        @endif
                        &#8369; {{ number_format($product->discountedPrice((float) $variant->price), 2) }} | {{ (int) $variant->stock }} left
                    </small>
                </button>
            @endforeach
        </div>
    </div>
</div>
