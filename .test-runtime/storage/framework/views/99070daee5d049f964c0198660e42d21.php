<div class="variant-builder <?php echo e($builderClass ?? ''); ?>" data-variant-builder data-next-index="<?php echo e($variantRows->count()); ?>">
    <div class="variant-builder-head">
        <div>
            <label class="variant-toggle-label" for="has_variants">
                <input type="checkbox" id="has_variants" name="has_variants" value="1" data-variant-toggle <?php echo e($variantsEnabled ? 'checked' : ''); ?>>
                <span>This product has variants</span>
            </label>
            <small class="product-media-note"><?php echo e($variantHelpText); ?></small>
        </div>
        <button type="button" class="table-action secondary" data-add-variant <?php echo e($variantsEnabled ? '' : 'hidden'); ?>>
            Add Variant
        </button>
    </div>

    <?php $__errorArgs = ['variants'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <small class="error-text"><?php echo e($message); ?></small>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <div class="variant-list" data-variant-list <?php echo e($variantsEnabled ? '' : 'hidden'); ?>>
        <?php $__currentLoopData = $variantRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $variantRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('seller.products.partials.form.variant-row', [
                'index' => $index,
                'showExistingImageNote' => $showExistingImageNote ?? false,
                'showVariantId' => $showVariantId ?? false,
                'variantRow' => $variantRow,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/partials/form/variant-builder.blade.php ENDPATH**/ ?>