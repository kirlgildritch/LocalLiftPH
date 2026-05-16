<?php if($productPage->hasVariants): ?>
    <div class="purchase-variants product-variants-panel" data-purchase-variants>
        <div class="product-variants-panel__head">
            <span>Options</span>
            <small>Choose one before adding to cart.</small>
        </div>
        <div class="variant-choice-grid variant-choice-grid--preview">
            <?php $__currentLoopData = $productPage->activeVariants->take($productPage->variantPreviewLimit); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                    class="variant-choice"
                    data-variant-choice
                    data-variant-id="<?php echo e($variant->id); ?>"
                    data-variant-price="<?php echo e($product->discountedPrice((float) $variant->price)); ?>"
                    data-variant-original-price="<?php echo e((float) $variant->price); ?>"
                    data-variant-stock="<?php echo e((int) $variant->stock); ?>"
                    <?php echo e((int) $variant->stock <= 0 ? 'disabled' : ''); ?>>
                    <strong><?php echo e($variant->displayName()); ?></strong>
                    <small>
                        <?php if($product->hasActiveDiscount() && $product->discountedPrice((float) $variant->price) < (float) $variant->price): ?>
                            <span class="variant-price-original">&#8369; <?php echo e(number_format($variant->price, 2)); ?></span>
                        <?php endif; ?>
                        &#8369; <?php echo e(number_format($product->discountedPrice((float) $variant->price), 2)); ?> | <?php echo e((int) $variant->stock); ?> left
                    </small>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($productPage->activeVariants->count() > $productPage->variantPreviewLimit): ?>
                <button type="button" class="variant-choice variant-choice--more" data-open-variant-modal>
                    <strong>View more options</strong>
                    <small><?php echo e($productPage->activeVariants->count() - $productPage->variantPreviewLimit); ?> more available</small>
                </button>
            <?php endif; ?>
        </div>
        <small class="quantity-note" data-variant-note>Select a variant before adding to cart.</small>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/variant-picker.blade.php ENDPATH**/ ?>