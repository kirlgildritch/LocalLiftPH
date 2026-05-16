<div class="panel purchase-card">
    <span class="section-kicker">Purchase</span>
    <h2>Order summary</h2>

    <div class="quantity-box"
        data-purchase-quantity-box
        data-max-stock="<?php echo e($productPage->purchaseMaxStock); ?>"
        data-unit-price="<?php echo e($productPage->displayPrice); ?>"
        data-has-variants="<?php echo e($productPage->hasVariants ? 'true' : 'false'); ?>">
        <span>Quantity</span>
        <div class="quantity-control">
            <button type="button" data-quantity-decrement aria-label="Decrease quantity">-</button>
            <input type="text" value="<?php echo e($productPage->initialQuantity); ?>" readonly data-quantity-display>
            <button type="button" data-quantity-increment aria-label="Increase quantity">+</button>
        </div>
        <small class="quantity-note" data-quantity-note hidden></small>
    </div>

    <div class="purchase-meta">
        <div>
            <span>Price</span>
            <strong data-purchase-total>&#8369; <?php echo e(number_format($productPage->initialPurchaseTotal, 2)); ?></strong>
        </div>
        <div>
            <span>Delivery</span>
            <strong>Nationwide ready</strong>
        </div>
    </div>

    <div class="purchase-actions">
        <?php if(auth()->guard()->check()): ?>
        <?php if($productPage->ownsProduct): ?>
            <span class="action-btn secondary-btn" aria-disabled="true">This is your product</span>
        <?php else: ?>
            <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" style="display:inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="quantity" value="<?php echo e($productPage->initialQuantity); ?>" data-purchase-quantity>
                <input type="hidden" name="product_variant_id" value="" data-purchase-variant-input>
                <button type="submit" class="action-btn primary-btn" data-purchase-submit <?php echo e($productPage->hasVariants ? 'disabled' : ''); ?>><i class="fa-solid fa-cart-shopping"></i></button>
            </form>
            <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST" style="display:inline;">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="quantity" value="<?php echo e($productPage->initialQuantity); ?>" data-purchase-quantity>
                <input type="hidden" name="product_variant_id" value="" data-purchase-variant-input>
                <input type="hidden" name="buy_now" value="1">
                <button type="submit" class="action-btn secondary-btn" data-purchase-submit <?php echo e($productPage->hasVariants ? 'disabled' : ''); ?>>Buy Now</button>
            </form>
        <?php endif; ?>

        <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="action-btn primary-btn"><i class="fa-solid fa-cart-shopping"></i></a>
        <a href="<?php echo e(route('login')); ?>" class="action-btn secondary-btn">Buy Now</a>
        <?php endif; ?>

        <?php if(auth()->guard('web')->check()): ?>
        <?php if(!$productPage->ownsProduct): ?>
            <form action="<?php echo e($productPage->isWishlisted ? route('buyer.wishlist.destroy', $product) : route('buyer.wishlist.store', $product)); ?>" method="POST" class="wishlist-toggle-form">
                <?php echo csrf_field(); ?>
                <?php if($productPage->isWishlisted): ?>
                    <?php echo method_field('DELETE'); ?>
                <?php endif; ?>
                <button type="submit"
                    class="icon-btn wishlist-toggle-btn <?php echo e($productPage->isWishlisted ? 'is-active' : ''); ?>"
                    aria-label="<?php echo e($productPage->isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'); ?>"
                    title="<?php echo e($productPage->isWishlisted ? 'Remove from wishlist' : 'Add to wishlist'); ?>">
                    <i class="fa-<?php echo e($productPage->isWishlisted ? 'solid' : 'regular'); ?> fa-heart"></i>
                </button>
            </form>
        <?php else: ?>
            <button type="button" class="icon-btn wishlist-toggle-btn" aria-label="Wishlist unavailable for your own product" disabled>
                <i class="fa-regular fa-heart"></i>
            </button>
        <?php endif; ?>
        <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="icon-btn wishlist-toggle-btn" aria-label="Log in to add wishlist" title="Log in to add wishlist">
            <i class="fa-regular fa-heart"></i>
        </a>
        <?php endif; ?>
    </div>

    <a href="<?php echo e(route('shops.show', $product->user->id)); ?>" class="action-btn secondary-btn full-btn">View Shop</a>

    <?php if(auth()->guard()->check()): ?>
    <?php if(!$productPage->ownsProduct): ?>
    <form action="<?php echo e(route('messages.start', $product->user)); ?>" method="POST" data-chat-start-form>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
        <button type="submit" class="action-btn secondary-btn full-btn">Message Seller</button>
    </form>
    <?php else: ?>
    <span class="action-btn secondary-btn full-btn" aria-disabled="true">This is your product</span>
    <?php endif; ?>
    <?php else: ?>
    <a href="<?php echo e(route('login')); ?>" class="action-btn secondary-btn full-btn">Message Seller</a>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/purchase-card.blade.php ENDPATH**/ ?>