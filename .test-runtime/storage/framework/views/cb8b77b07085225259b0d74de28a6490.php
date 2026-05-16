<?php $__env->startSection('title', 'LocalLift PH - Product'); ?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/product_details.css')); ?>">

<?php if(session('error')): ?>
<div style="color:red;"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<?php if($errors->any()): ?>
<div style="color:red;">
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <p><?php echo e($error); ?></p>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<section class="product-detail-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="<?php echo e(route('home')); ?>">Home</a>
            <span>&gt;</span>
            <a href="<?php echo e(route('products.index')); ?>">Products</a>
            <span>&gt;</span>
            <span><?php echo e($product->name); ?></span>
        </div>

        <div class="product-detail-layout">
            <div class="product-main panel">
                <?php echo $__env->make('products.partials.show.gallery', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('products.partials.show.summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <aside class="purchase-sidebar">
                <?php echo $__env->make('products.partials.show.purchase-card', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </aside>
        </div>

        <?php echo $__env->renderWhen(
            $productPage->hasVariants && $productPage->activeVariants->count() > $productPage->variantPreviewLimit,
            'products.partials.show.variant-modal'
        , array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

        <div class="detail-sections">
            <?php echo $__env->make('products.partials.show.description', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <div class="detail-sections">
            <?php echo $__env->make('products.partials.show.reviews', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('products.partials.show.related-products', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</section>

<div class="review-lightbox" data-review-lightbox hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Review media preview">
    <button type="button" class="review-lightbox-close" data-review-lightbox-close aria-label="Close preview">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="review-lightbox-dialog" data-review-lightbox-dialog></div>
</div>

<?php echo app('Illuminate\Foundation\Vite')([
    'resources/js/product-gallery.js',
    'resources/js/purchase-variants.js',
    'resources/js/review-upload.js'
]); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/show.blade.php ENDPATH**/ ?>