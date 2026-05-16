<?php
    $sellerProductReviewsStyles = asset('assets/css/seller-product-reviews-page.css') . '?v=' . @filemtime(public_path('assets/css/seller-product-reviews-page.css'));
?>

<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/manage_products.css')); ?>">
<link rel="stylesheet" href="<?php echo e($sellerProductReviewsStyles); ?>">

<section class="dashboard-wrapper seller-product-reviews-page">
    <div class="container">
        <div class="dashboard-layout">
            <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="dashboard-main">
                <section class="seller-page-panel panel seller-product-reviews-panel">
                    <?php echo $__env->make('seller.products.reviews.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('seller.products.reviews.partials.summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('seller.products.reviews.partials.list', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </section>
            </main>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/reviews.blade.php ENDPATH**/ ?>