<section class="checkout-card checkout-card--payment panel">
    <div class="card-header">
        <div class="step-title">
            <span class="step-number">1</span>
            <div>
                <span class="toolbar-label">Step</span>
                <h3>Shipping Address</h3>
            </div>
        </div>
        <a href="<?php echo e(route('buyer.addresses', ['return_to' => request()->fullUrl()])); ?>" class="action-link">Edit</a>
    </div>

    <div class="card-body">
        <div class="shipping-address-box">
            <?php if(isset($defaultAddress) && $defaultAddress): ?>
                <p><strong><?php echo e($defaultAddress->full_name ?? auth()->user()->name); ?></strong></p>
                <p><?php echo e($defaultAddress->phone ?? 'No phone number'); ?></p>

                <?php if(!empty($defaultAddress->street_address)): ?>
                    <p><?php echo e($defaultAddress->street_address); ?></p>
                <?php endif; ?>

                <?php if(!empty($defaultAddress->landmark)): ?>
                    <p>Landmark: <?php echo e($defaultAddress->landmark); ?></p>
                <?php endif; ?>

                <p>
                    <?php echo e($defaultAddress->barangay ?? ''); ?>

                    <?php if(!empty($defaultAddress->barangay) && !empty($defaultAddress->city)): ?>, <?php endif; ?>
                    <?php echo e($defaultAddress->city ?? ''); ?>

                    <?php if(!empty($defaultAddress->province)): ?>, <?php echo e($defaultAddress->province); ?><?php endif; ?>
                    <?php if(!empty($defaultAddress->region)): ?>, <?php echo e($defaultAddress->region); ?><?php endif; ?>
                    <?php if(!empty($defaultAddress->postal_code)): ?>, <?php echo e($defaultAddress->postal_code); ?><?php endif; ?>
                </p>
            <?php else: ?>
                <p>Please add a delivery address before placing an order.</p>
                <a href="<?php echo e(route('buyer.addresses', ['return_to' => request()->fullUrl()])); ?>" class="action-btn secondary-btn">
                    Add Delivery Address
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/checkout/partials/shipping-address.blade.php ENDPATH**/ ?>