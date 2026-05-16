@if($productPage->hasVariants)
    <div class="purchase-variants product-variants-panel" data-purchase-variants>
        <div class="product-variants-panel__head">
            <span>Options</span>
            <small>Choose one before adding to cart.</small>
        </div>
        <div class="variant-choice-grid variant-choice-grid--preview">
            @foreach($productPage->activeVariants->take($productPage->variantPreviewLimit) as $variant)
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

            @if($productPage->activeVariants->count() > $productPage->variantPreviewLimit)
                <button type="button" class="variant-choice variant-choice--more" data-open-variant-modal>
                    <strong>View more options</strong>
                    <small>{{ $productPage->activeVariants->count() - $productPage->variantPreviewLimit }} more available</small>
                </button>
            @endif
        </div>
        <small class="quantity-note" data-variant-note>Select a variant before adding to cart.</small>
    </div>
@endif
