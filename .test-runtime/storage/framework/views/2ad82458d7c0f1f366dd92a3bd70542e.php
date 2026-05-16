<?php ($wrapperClass = trim($wrapperClass ?? '')); ?>
<div<?php echo e($wrapperClass !== '' ? ' class=' . '"' . e($wrapperClass) . '"' : ''); ?>>
    <div class="form-group">
        <label for="price"><?php echo e($priceLabel ?? 'Price'); ?></label>
        <input type="number" id="price" name="price" value="<?php echo e($priceValue); ?>"
            placeholder="<?php echo e($pricePlaceholder ?? '0.00'); ?>" step="0.01" min="0">
        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="error-text"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="discount_type">Discount Type</label>
        <select id="discount_type" name="discount_type">
            <option value="">No discount</option>
            <option value="percent" <?php echo e(($discountTypeValue ?? '') === 'percent' ? 'selected' : ''); ?>>Percentage</option>
            <option value="fixed" <?php echo e(($discountTypeValue ?? '') === 'fixed' ? 'selected' : ''); ?>>Fixed amount</option>
        </select>
        <?php $__errorArgs = ['discount_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="error-text"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="discount_value">Discount Value</label>
        <input type="number" id="discount_value" name="discount_value"
            value="<?php echo e($discountValue ?? ''); ?>" placeholder="Optional" step="0.01" min="0">
        <?php $__errorArgs = ['discount_value'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="error-text"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="form-group">
        <label for="stock"><?php echo e($stockLabel ?? 'Stock'); ?></label>
        <input type="number" id="stock" name="stock" value="<?php echo e($stockValue); ?>"
            placeholder="<?php echo e($stockPlaceholder ?? '0'); ?>" min="0">
        <?php $__errorArgs = ['stock'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <span class="error-text"><?php echo e($message); ?></span>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/partials/form/pricing-fields.blade.php ENDPATH**/ ?>