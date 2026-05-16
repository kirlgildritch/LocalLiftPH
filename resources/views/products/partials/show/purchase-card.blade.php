<div class="panel purchase-card">
    <span class="section-kicker">Purchase</span>
    <h2>Order summary</h2>

    <div class="quantity-box"
        data-purchase-quantity-box
        data-max-stock="{{ $productPage->purchaseMaxStock }}"
        data-unit-price="{{ $productPage->displayPrice }}"
        data-has-variants="{{ $productPage->hasVariants ? 'true' : 'false' }}">
        <span>Quantity</span>
        <div class="quantity-control">
            <button type="button" data-quantity-decrement aria-label="Decrease quantity">-</button>
            <input type="text" value="{{ $productPage->initialQuantity }}" readonly data-quantity-display>
            <button type="button" data-quantity-increment aria-label="Increase quantity">+</button>
        </div>
        <small class="quantity-note" data-quantity-note hidden></small>
    </div>

    <div class="purchase-meta">
        <div>
            <span>Price</span>
            <strong data-purchase-total>&#8369; {{ number_format($productPage->initialPurchaseTotal, 2) }}</strong>
        </div>
        <div>
            <span>Delivery</span>
            <strong>Nationwide ready</strong>
        </div>
    </div>

    <div class="purchase-actions">
        @auth
        @if($productPage->ownsProduct)
            <span class="action-btn secondary-btn" aria-disabled="true">This is your product</span>
        @else
            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="quantity" value="{{ $productPage->initialQuantity }}" data-purchase-quantity>
                <input type="hidden" name="product_variant_id" value="" data-purchase-variant-input>
                <button type="submit" class="action-btn primary-btn" data-purchase-submit {{ $productPage->hasVariants ? 'disabled' : '' }}><i class="fa-solid fa-cart-shopping"></i></button>
            </form>
            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="quantity" value="{{ $productPage->initialQuantity }}" data-purchase-quantity>
                <input type="hidden" name="product_variant_id" value="" data-purchase-variant-input>
                <input type="hidden" name="buy_now" value="1">
                <button type="submit" class="action-btn secondary-btn" data-purchase-submit {{ $productPage->hasVariants ? 'disabled' : '' }}>Buy Now</button>
            </form>
        @endif

        @else
        <a href="{{ route('login') }}" class="action-btn primary-btn"><i class="fa-solid fa-cart-shopping"></i></a>
        <a href="{{ route('login') }}" class="action-btn secondary-btn">Buy Now</a>
        @endauth

        @auth('web')
        @if(!$productPage->ownsProduct)
            <form action="{{ $productPage->isWishlisted ? route('buyer.wishlist.destroy', $product) : route('buyer.wishlist.store', $product) }}" method="POST" class="wishlist-toggle-form">
                @csrf
                @if($productPage->isWishlisted)
                    @method('DELETE')
                @endif
                <button type="submit"
                    class="icon-btn wishlist-toggle-btn {{ $productPage->isWishlisted ? 'is-active' : '' }}"
                    aria-label="{{ $productPage->isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}"
                    title="{{ $productPage->isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}">
                    <i class="fa-{{ $productPage->isWishlisted ? 'solid' : 'regular' }} fa-heart"></i>
                </button>
            </form>
        @else
            <button type="button" class="icon-btn wishlist-toggle-btn" aria-label="Wishlist unavailable for your own product" disabled>
                <i class="fa-regular fa-heart"></i>
            </button>
        @endif
        @else
        <a href="{{ route('login') }}" class="icon-btn wishlist-toggle-btn" aria-label="Log in to add wishlist" title="Log in to add wishlist">
            <i class="fa-regular fa-heart"></i>
        </a>
        @endauth
    </div>

    <a href="{{ route('shops.show', $product->user->id) }}" class="action-btn secondary-btn full-btn">View Shop</a>

    @auth
    @if(!$productPage->ownsProduct)
    <form action="{{ route('messages.start', $product->user) }}" method="POST" data-chat-start-form>
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <button type="submit" class="action-btn secondary-btn full-btn">Message Seller</button>
    </form>
    @else
    <span class="action-btn secondary-btn full-btn" aria-disabled="true">This is your product</span>
    @endif
    @else
    <a href="{{ route('login') }}" class="action-btn secondary-btn full-btn">Message Seller</a>
    @endauth
</div>
