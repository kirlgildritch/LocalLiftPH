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
            <?php $__currentLoopData = $productPage->activeVariants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                    class="variant-choice"
                    data-variant-choice
                    data-variant-id="<?php echo e($variant->id); ?>"
                    data-variant-price="<?php echo e((float) $variant->price); ?>"
                    data-variant-stock="<?php echo e((int) $variant->stock); ?>"
                    <?php echo e((int) $variant->stock <= 0 ? 'disabled' : ''); ?>>
                    <strong><?php echo e($variant->displayName()); ?></strong>
                    <small>&#8369; <?php echo e(number_format($variant->price, 2)); ?> | <?php echo e((int) $variant->stock); ?> left</small>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/variant-modal.blade.php ENDPATH**/ ?>