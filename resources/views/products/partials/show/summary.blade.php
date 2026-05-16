<div class="product-copy">
    <div class="product-copy-top">
        <span class="section-kicker">{{ $product->category?->name ?? 'Uncategorized' }}</span>
        @if($productPage->canReportProduct)
        @include('partials.report-modal', [
            'modalId' => 'report-product-modal',
            'modalContext' => 'product',
            'triggerLabel' => 'Report product',
            'productId' => $product->id,
            'sellerId' => $product->user_id,
        ])
        @elseif(!auth('seller')->check() && !auth('admin')->check())
        <a href="{{ route('login') }}" class="report-trigger-button" aria-label="Log in to report product">
            <i class="fa-solid fa-flag"></i>
        </a>
        @endif
    </div>

    <h1>{{ $product->name }}</h1>

    <div class="product-meta">
        <span><i class="fa-solid fa-store"></i>
            {{ $product->user->sellerProfile?->store_name ?? 'LocalLift Seller' }}
            <x-seller-trust-badge :seller="$product->user->sellerProfile" compact icon-only />
        </span>
        <span><i class="fa-solid fa-box-open"></i>
            {{ $productPage->displayStock > 0 ? 'Ready to ship' : 'Out of stock' }}</span>
        <span><i class="fa-solid fa-cubes"></i> Stock: {{ $productPage->displayStock }}</span>
        <span><i class="fa-solid fa-star"></i>
            {{ $productPage->averageRating > 0 ? number_format($productPage->averageRating, 1) : 'New' }} |
            {{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
    </div>

    <div class="product-price" data-product-display-price>
        @if($productPage->hasDiscount)
            <span class="product-price__original">&#8369; {{ number_format($productPage->displayOriginalPrice, 2) }}</span>
        @endif
        @if($productPage->hasVariants)
            <span class="product-price__sale">Starts at &#8369; {{ number_format($productPage->displayPrice, 2) }}</span>
        @else
            <span class="product-price__sale">&#8369; {{ number_format($productPage->displayPrice, 2) }}</span>
        @endif
        @if($productPage->hasDiscount)
            <span class="product-price__badge">{{ $product->discountLabel() }}</span>
        @endif
    </div>

    <div class="product-feature-grid">
        <div class="feature-card">
            <strong>Category</strong>
            <span>{{ $product->category?->name ?? 'Uncategorized' }}</span>
        </div>
        <div class="feature-card">
            <strong>Availability</strong>
            <span>{{ $productPage->displayStock > 0 ? 'In stock' : 'Currently unavailable' }}</span>
        </div>
    </div>

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
</div>
