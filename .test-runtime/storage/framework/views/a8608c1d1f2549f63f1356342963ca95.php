<div class="product-copy">
    <div class="product-copy-top">
        <span class="section-kicker"><?php echo e($product->category?->name ?? 'Uncategorized'); ?></span>
        <?php if($productPage->canReportProduct): ?>
        <?php echo $__env->make('partials.report-modal', [
            'modalId' => 'report-product-modal',
            'modalContext' => 'product',
            'triggerLabel' => 'Report product',
            'productId' => $product->id,
            'sellerId' => $product->user_id,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php elseif(!auth('seller')->check() && !auth('admin')->check()): ?>
        <a href="<?php echo e(route('login')); ?>" class="report-trigger-button" aria-label="Log in to report product">
            <i class="fa-solid fa-flag"></i>
        </a>
        <?php endif; ?>
    </div>

    <h1><?php echo e($product->name); ?></h1>

    <div class="product-meta">
        <span><i class="fa-solid fa-store"></i>
            <?php echo e($product->user->sellerProfile?->store_name ?? 'LocalLift Seller'); ?>

            <?php if (isset($component)) { $__componentOriginalfe2859e26b7d777b13c0d03a650c7378 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seller-trust-badge','data' => ['seller' => $product->user->sellerProfile,'compact' => true,'iconOnly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seller-trust-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['seller' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product->user->sellerProfile),'compact' => true,'icon-only' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $attributes = $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $component = $__componentOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
        </span>
        <span><i class="fa-solid fa-box-open"></i>
            <?php echo e($productPage->displayStock > 0 ? 'Ready to ship' : 'Out of stock'); ?></span>
        <span><i class="fa-solid fa-cubes"></i> Stock: <?php echo e($productPage->displayStock); ?></span>
        <span><i class="fa-solid fa-star"></i>
            <?php echo e($productPage->averageRating > 0 ? number_format($productPage->averageRating, 1) : 'New'); ?> |
            <?php echo e($product->reviews_count); ?> review<?php echo e($product->reviews_count !== 1 ? 's' : ''); ?></span>
    </div>

    <div class="product-price" data-product-display-price>
        <?php if($productPage->hasVariants): ?>
            Starts at &#8369; <?php echo e(number_format($productPage->displayPrice, 2)); ?>

        <?php else: ?>
            &#8369; <?php echo e(number_format($productPage->displayPrice, 2)); ?>

        <?php endif; ?>
    </div>

    <div class="product-feature-grid">
        <div class="feature-card">
            <strong>Category</strong>
            <span><?php echo e($product->category?->name ?? 'Uncategorized'); ?></span>
        </div>
        <div class="feature-card">
            <strong>Availability</strong>
            <span><?php echo e($productPage->displayStock > 0 ? 'In stock' : 'Currently unavailable'); ?></span>
        </div>
    </div>

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
                    data-variant-price="<?php echo e((float) $variant->price); ?>"
                    data-variant-stock="<?php echo e((int) $variant->stock); ?>"
                    <?php echo e((int) $variant->stock <= 0 ? 'disabled' : ''); ?>>
                    <strong><?php echo e($variant->displayName()); ?></strong>
                    <small>&#8369; <?php echo e(number_format($variant->price, 2)); ?> | <?php echo e((int) $variant->stock); ?> left</small>
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
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/summary.blade.php ENDPATH**/ ?>