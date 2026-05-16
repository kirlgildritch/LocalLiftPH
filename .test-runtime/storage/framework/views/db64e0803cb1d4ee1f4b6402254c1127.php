<?php $__env->startSection('title', 'LocalLift PH - Checkout'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/checkout.css')); ?>">
    <?php
        $checkoutPaymentScript = asset('assets/js/checkout-payment.js') . '?v=' . @filemtime(public_path('assets/js/checkout-payment.js'));
    ?>

    <section class="checkout-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="<?php echo e(route('home')); ?>">Home</a>
                <span>&gt;</span>
                <a href="<?php echo e(route('cart.index')); ?>">Cart</a>
                <span>&gt;</span>
                <span>Checkout</span>
            </div>

            <div class="checkout-layout">
                <div class="checkout-main">
                    <?php echo $__env->make('checkout.partials.shipping-address', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('checkout.partials.shipping-method', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php echo $__env->make('checkout.partials.payment-method', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>

                <aside class="checkout-sidebar">
                    <?php echo $__env->make('checkout.partials.order-summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </aside>
            </div>
        </div>
    </section>

    <script src="<?php echo e($checkoutPaymentScript); ?>" defer></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/checkout/index.blade.php ENDPATH**/ ?>