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
            <?php $__currentLoopData = ($groupedCartItems ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sellerId => $sellerCartItems): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $seller = $sellerCartItems->first()?->product?->user;
                    $estimate = ($deliveryEstimates ?? collect())->get($sellerId);
                    $sellerShipping = $sellerCartItems->sum(fn($item) => (float) ($item->product->shipping_fee ?? 0) * (int) $item->quantity);
                ?>

                <article class="delivery-method-card">
                    <div class="delivery-method-card__icon">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>

                    <div class="delivery-method-card__copy">
                        <strong><?php echo e($seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller'); ?></strong>
                        <span><?php echo e($estimate['label'] ?? 'Standard local courier'); ?></span>
                        <p>
                            Estimated delivery:
                            <b><?php echo e($estimate['date_range'] ?? '3-5 days'); ?></b>
                        </p>
                        <?php if(!empty($estimate['is_fallback'])): ?>
                            <small>Estimate uses standard courier timing because seller location is limited.</small>
                        <?php endif; ?>
                    </div>

                    <div class="delivery-method-card__price">
                        <span>Shipping</span>
                        <strong>&#8369; <?php echo e(number_format($sellerShipping, 2)); ?></strong>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/checkout/partials/shipping-method.blade.php ENDPATH**/ ?>