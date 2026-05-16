<?php ($wrapperClass = trim($wrapperClass ?? '')); ?>
<div<?php echo e($wrapperClass !== '' ? ' class=' . '"' . e($wrapperClass) . '"' : ''); ?>>
    <div class="form-group form-group-wide">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="<?php echo e($nameValue); ?>" placeholder="<?php echo e($namePlaceholder ?? 'Enter product name'); ?>">
        <?php $__errorArgs = ['name'];
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
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id">
            <option value="">Select category</option>
            <?php $__currentLoopData = ($categories ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($category->id); ?>" <?php echo e((string) $categoryValue === (string) $category->id ? 'selected' : ''); ?>>
                    <?php echo e($category->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['category_id'];
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
        <label for="condition">Condition</label>
        <select id="condition" name="condition">
            <?php if(($includeConditionPlaceholder ?? true)): ?>
                <option value="">Select condition</option>
            <?php endif; ?>
            <option value="new" <?php echo e($conditionValue === 'new' ? 'selected' : ''); ?>>New</option>
            <option value="used" <?php echo e($conditionValue === 'used' ? 'selected' : ''); ?>>Used</option>
        </select>
        <?php $__errorArgs = ['condition'];
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

    <div class="form-group form-group-wide">
        <label for="description"><?php echo e($descriptionLabel ?? 'Description'); ?></label>
        <?php if(($descriptionMode ?? 'textarea') === 'quill'): ?>
            <div id="editor" style="height: <?php echo e($editorHeight ?? '220px'); ?>;"><?php echo $descriptionValue; ?></div>
            <input type="hidden" name="description" id="description">
        <?php else: ?>
            <textarea id="description" name="description" rows="<?php echo e($descriptionRows ?? 7); ?>"
                placeholder="<?php echo e($descriptionPlaceholder ?? 'Describe your product'); ?>"><?php echo e($descriptionValue); ?></textarea>
        <?php endif; ?>
        <?php $__errorArgs = ['description'];
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
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/partials/form/basic-information.blade.php ENDPATH**/ ?>