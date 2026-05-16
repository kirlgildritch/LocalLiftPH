<div class="product-grid product-card-grid" data-market-pagination-grid>
    <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php if (isset($component)) { $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.product-card','data' => ['product' => $product,'buyerLocation' => $buyerLocation]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('product-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['product' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($product),'buyer-location' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($buyerLocation)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $attributes = $__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__attributesOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a)): ?>
<?php $component = $__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a; ?>
<?php unset($__componentOriginal3fd2897c1d6a149cdb97b41db9ff827a); ?>
<?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="panel" style="padding: 20px;">
            <p>
                <?php if(!empty($search)): ?>
                    No products found for "<strong><?php echo e($search); ?></strong>".
                <?php else: ?>
                    No products available yet.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php if($products->hasPages()): ?>
    <div class="panel"
        data-market-pagination-nav
        style="padding: 16px 20px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <p style="margin: 0; color: #9fb3c8; font-size: 14px;">
            Showing <?php echo e($products->firstItem()); ?>-<?php echo e($products->lastItem()); ?> of <?php echo e($products->total()); ?>

            products
        </p>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            <?php if($products->onFirstPage()): ?>
                <span class="action-btn secondary-btn" style="opacity: 0.5; pointer-events: none;">Previous</span>
            <?php else: ?>
                <a href="<?php echo e($products->previousPageUrl()); ?>" class="action-btn secondary-btn" data-market-pagination-link>Previous</a>
            <?php endif; ?>

            <span style="color: #dbeafe; font-size: 14px;">Page <?php echo e($products->currentPage()); ?> of
                <?php echo e($products->lastPage()); ?></span>

            <?php if($products->hasMorePages()): ?>
                <a href="<?php echo e($products->nextPageUrl()); ?>" class="action-btn secondary-btn" data-market-pagination-link>Next</a>
            <?php else: ?>
                <span class="action-btn secondary-btn" style="opacity: 0.5; pointer-events: none;">Next</span>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/results.blade.php ENDPATH**/ ?>