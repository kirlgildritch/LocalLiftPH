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
        <?php $__empty_1 = true; $__currentLoopData = ($checkoutSummary['groups'] ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shopSummary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="summary-shop-group">
                <div class="summary-shop-head">
                    <div>
                        <h4><?php echo e($shopSummary['seller_name']); ?></h4>
                        <p><?php echo e($shopSummary['item_count']); ?> item<?php echo e($shopSummary['item_count'] !== 1 ? 's' : ''); ?> &middot; Delivery <?php echo e($shopSummary['delivery_range']); ?></p>
                    </div>

                    <div class="summary-price">
                        <strong>&#8369; <?php echo e(number_format($shopSummary['shop_total'], 2)); ?></strong>
                        <span>Shop total</span>
                    </div>
                </div>

                <?php $__currentLoopData = $shopSummary['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemSummary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="summary-item">
                        <div class="summary-product">
                            <div class="summary-image">
                                <img src="<?php echo e($itemSummary['image_url']); ?>" alt="<?php echo e($itemSummary['product_name']); ?>">
                            </div>
                            <div>
                                <h4><?php echo e($itemSummary['product_name']); ?></h4>
                                <?php if($itemSummary['variant_name']): ?>
                                    <p>Option: <?php echo e($itemSummary['variant_name']); ?></p>
                                <?php endif; ?>
                                <p>Qty <?php echo e($itemSummary['quantity']); ?> &middot; Shipping &#8369; <?php echo e(number_format($itemSummary['shipping_total'], 2)); ?></p>
                            </div>
                        </div>

                        <div class="summary-price">
                            <strong>&#8369; <?php echo e(number_format($itemSummary['line_subtotal'], 2)); ?></strong>
                            <span>
                                <?php if($itemSummary['has_discount']): ?>
                                    <span class="checkout-price-original">&#8369; <?php echo e(number_format($itemSummary['original_unit_price'], 2)); ?></span>
                                <?php endif; ?>
                                <span class="<?php echo e($itemSummary['has_discount'] ? 'checkout-price-sale' : ''); ?>">&#8369; <?php echo e(number_format($itemSummary['unit_price'], 2)); ?> each</span>
                            </span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php echo $__env->make('vouchers.partials.buyer-voucher-list', [
                    'vouchers' => ($availableSellerVouchers ?? collect())->get($shopSummary['seller_id'], collect()),
                    'title' => 'Seller Vouchers',
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <div class="summary-line">
        <span>Subtotal</span>
        <strong>&#8369; <?php echo e(number_format($checkoutSummary['subtotal'] ?? $subtotal, 2)); ?></strong>
    </div>

    <div class="summary-line">
        <span>Shipping Fee</span>
        <strong>&#8369; <?php echo e(number_format($checkoutSummary['shipping_fee'] ?? $shippingFee, 2)); ?></strong>
    </div>

    <div class="summary-line">
        <span>Voucher<?php echo e(filled($checkoutSummary['voucher_code'] ?? null) ? ' (' . $checkoutSummary['voucher_code'] . ')' : ''); ?></span>
        <strong>
            <?php if(($checkoutSummary['voucher_discount'] ?? 0) > 0): ?>
                - &#8369; <?php echo e(number_format($checkoutSummary['voucher_discount'], 2)); ?>

            <?php else: ?>
                Optional
            <?php endif; ?>
        </strong>
    </div>

    <div class="summary-total">
        <span>Total</span>
        <strong>&#8369; <?php echo e(number_format($checkoutSummary['total'] ?? $total, 2)); ?></strong>
    </div>

    <form action="<?php echo e(route('checkout.store')); ?>" method="POST" id="checkout-submit-form" data-enable-loading>
        <?php echo csrf_field(); ?>
        <?php $__currentLoopData = ($selectedCartItemIds ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedCartItemId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <input type="hidden" name="selected_cart_items[]" value="<?php echo e($selectedCartItemId); ?>">
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="form-group" style="margin-bottom: 14px;">
            <label for="voucher_code">Voucher / Coupon</label>
            <input type="text" id="voucher_code" name="voucher_code" value="<?php echo e($voucherCode); ?>" placeholder="Enter code, if any">
            <?php $__errorArgs = ['voucher_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <small class="error-text"><?php echo e($message); ?></small>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php if(($checkoutSummary['voucher_discount'] ?? 0) > 0): ?>
                <small class="success-text"><?php echo e($checkoutSummary['voucher_label']); ?> applied.</small>
            <?php endif; ?>
        </div>
        <button
            type="submit"
            class="action-btn full-btn"
            formmethod="GET"
            formaction="<?php echo e(route('checkout.index')); ?>"
            data-loading-text="Applying..."
        >
            Apply Voucher
        </button>
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