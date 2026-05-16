<div class="order-summary panel">
    <span class="section-kicker">Final Review</span>
    <h3>Review Your Order</h3>

    <div class="review-checklist">
        <div>
            <i class="fa-solid fa-location-dot"></i>
            <span>Delivering to</span>
            <strong><?php echo e($defaultAddress?->city ?? 'Saved address'); ?><?php echo e(filled($defaultAddress?->province) ? ', ' . $defaultAddress->province : ''); ?></strong>
        </div>
        <div>
            <i class="fa-solid fa-calendar-check"></i>
            <span>Estimated delivery</span>
            <strong><?php echo e($overallDeliveryEstimate['date_range'] ?? '3-5 days'); ?></strong>
        </div>
        <div>
            <i class="fa-solid fa-money-bill-wave"></i>
            <span>Payment</span>
            <strong data-payment-summary><?php echo e($selectedPayment['short_label']); ?></strong>
        </div>
    </div>

    <div class="summary-items">
        <?php $__empty_1 = true; $__currentLoopData = ($groupedCartItems ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sellerId => $sellerCartItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $seller = $sellerCartItems->first()?->product?->user;
                $sellerSubtotal = $sellerCartItems->sum(fn($item) => (float) ($item->variant?->price ?? $item->product->price ?? 0) * (int) $item->quantity);
                $sellerShipping = $sellerCartItems->sum(fn($item) => (float) ($item->product->shipping_fee ?? 0) * (int) $item->quantity);
                $estimate = ($deliveryEstimates ?? collect())->get($sellerId);
            ?>
            <div class="summary-shop-group">
                <div class="summary-shop-head">
                    <div>
                        <h4><?php echo e($seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller'); ?></h4>
                        <p><?php echo e($sellerCartItems->count()); ?> item<?php echo e($sellerCartItems->count() !== 1 ? 's' : ''); ?> &middot; Delivery <?php echo e($estimate['date_range'] ?? '3-5 days'); ?></p>
                    </div>

                    <div class="summary-price">
                        <strong>&#8369; <?php echo e(number_format($sellerSubtotal + $sellerShipping, 2)); ?></strong>
                        <span>Shop total</span>
                    </div>
                </div>

                <?php $__currentLoopData = $sellerCartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $variant = $item->variant;
                        $unitPrice = (float) ($variant?->price ?? $item->product->price ?? 0);
                        $productImage = $variant?->image ?: ($item->product->image ?? null);
                    ?>
                    <div class="summary-item">
                        <div class="summary-product">
                            <div class="summary-image">
                                <img src="<?php echo e($productImage ? asset('storage/' . $productImage) : asset('assets/images/default-product.png')); ?>"
                                    alt="<?php echo e($item->product->name ?? 'Product'); ?>">
                            </div>
                            <div>
                                <h4><?php echo e($item->product->name ?? 'Product'); ?></h4>
                                <?php if($variant): ?>
                                    <p>Option: <?php echo e($variant->displayName()); ?></p>
                                <?php endif; ?>
                                <p>Qty <?php echo e($item->quantity); ?> &middot; Shipping &#8369; <?php echo e(number_format(((float) ($item->product->shipping_fee ?? 0)) * (int) $item->quantity, 2)); ?></p>
                            </div>
                        </div>

                        <div class="summary-price">
                            <strong>&#8369; <?php echo e(number_format($unitPrice * (int) $item->quantity, 2)); ?></strong>
                            <span>&#8369; <?php echo e(number_format($unitPrice, 2)); ?> each</span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <div class="summary-line">
        <span>Subtotal</span>
        <strong>&#8369; <?php echo e(number_format($subtotal, 2)); ?></strong>
    </div>

    <div class="summary-line">
        <span>Shipping Fee</span>
        <strong>&#8369; <?php echo e(number_format($shippingFee, 2)); ?></strong>
    </div>

    <div class="summary-total">
        <span>Total</span>
        <strong>&#8369; <?php echo e(number_format($total, 2)); ?></strong>
    </div>

    <form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkout-submit-form" data-enable-loading>
        <?php echo csrf_field(); ?>
        <?php $__currentLoopData = ($selectedCartItemIds ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedCartItemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="selected_cart_items[]" value="<?php echo e($selectedCartItemId); ?>">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <button
            type="submit"
            class="action-btn primary-btn full-btn"
            data-enable-loading
            data-loading-text="Placing Order..."
            <?php echo e(($hasSavedAddress ?? false) ? '' : 'disabled'); ?>

        >
            Place Order - <span data-payment-button-label><?php echo e($selectedPayment['short_label']); ?></span>
        </button>
    </form>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/checkout/partials/order-summary.blade.php ENDPATH**/ ?>