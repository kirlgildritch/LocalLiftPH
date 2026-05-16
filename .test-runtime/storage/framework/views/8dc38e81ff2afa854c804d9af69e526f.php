<div class="variant-row" data-variant-row>
    <?php if(($showVariantId ?? false) && !empty($variantRow['id'])): ?>
        <input type="hidden" name="variants[<?php echo e($index); ?>][id]" value="<?php echo e($variantRow['id']); ?>">
    <?php endif; ?>

    <div class="form-group">
        <label>Variant Name</label>
        <input type="text" name="variants[<?php echo e($index); ?>][name]" value="<?php echo e($variantRow['name'] ?? ''); ?>" placeholder="e.g. Small / Red">
        <?php $__errorArgs = ["variants.$index.name"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="error-text"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>SKU</label>
        <input type="text" name="variants[<?php echo e($index); ?>][sku]" value="<?php echo e($variantRow['sku'] ?? ''); ?>" placeholder="Optional">
    </div>

    <div class="form-group">
        <label>Price</label>
        <input type="number" name="variants[<?php echo e($index); ?>][price]" value="<?php echo e($variantRow['price'] ?? ''); ?>" step="0.01" min="0" placeholder="0.00">
        <?php $__errorArgs = ["variants.$index.price"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="error-text"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Stock</label>
        <input type="number" name="variants[<?php echo e($index); ?>][stock]" value="<?php echo e($variantRow['stock'] ?? ''); ?>" min="0" placeholder="0">
        <?php $__errorArgs = ["variants.$index.stock"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <small class="error-text"><?php echo e($message); ?></small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label>Image</label>
        <input type="file" name="variants[<?php echo e($index); ?>][image]" accept="image/*">
        <?php if(($showExistingImageNote ?? false) && !empty($variantRow['image'])): ?>
            <small class="product-media-note">Current image saved.</small>
        <?php endif; ?>
    </div>

    <div class="variant-row-actions">
        <input type="hidden" name="variants[<?php echo e($index); ?>][is_active]" value="0">
        <label class="variant-active-toggle">
            <input type="checkbox" name="variants[<?php echo e($index); ?>][is_active]" value="1" <?php echo e((bool) ($variantRow['is_active'] ?? true) ? 'checked' : ''); ?>>
            Active
        </label>
        <button type="button" class="table-action danger" data-remove-variant>Remove</button>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/partials/form/variant-row.blade.php ENDPATH**/ ?>