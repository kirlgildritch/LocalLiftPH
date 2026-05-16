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
        <?php if($productPage->hasDiscount): ?>
            <span class="product-price__original">&#8369; <?php echo e(number_format($productPage->displayOriginalPrice, 2)); ?></span>
        <?php endif; ?>
        <?php if($productPage->hasVariants): ?>
            <span class="product-price__sale">Starts at &#8369; <?php echo e(number_format($productPage->displayPrice, 2)); ?></span>
        <?php else: ?>
            <span class="product-price__sale">&#8369; <?php echo e(number_format($productPage->displayPrice, 2)); ?></span>
        <?php endif; ?>
        <?php if($productPage->hasDiscount): ?>
            <span class="product-price__badge"><?php echo e($product->discountLabel()); ?></span>
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

</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/summary.blade.php ENDPATH**/ ?>