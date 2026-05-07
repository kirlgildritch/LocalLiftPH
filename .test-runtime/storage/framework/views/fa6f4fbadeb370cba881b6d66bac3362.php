<aside class="sidebar">
    <button class="sidebar-close" type="button" data-close-seller-sidebar aria-label="Close seller navigation">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="sidebar-menu">
        <a href="<?php echo e(url('/seller-dashboard')); ?>" class="<?php echo e(request()->is('seller-dashboard') ? 'active' : ''); ?>">
            <div class="left">
                <i class="fa-solid fa-house"></i> Dashboard
            </div>
        </a>

        <a href="<?php echo e(url('/manage-products')); ?>" class="<?php echo e(request()->is('manage-products') ? 'active' : ''); ?>">
            <div class="left">
                <i class="fa-solid fa-circle-check"></i> My Products
            </div>
        </a>

        <a href="<?php echo e(route('seller.orders')); ?>" class="<?php echo e(request()->is('seller-orders') ? 'active' : ''); ?>">
            <div class="left">
                <i class="fa-solid fa-bag-shopping"></i> Orders
            </div>
        </a>

        <a href="<?php echo e(route('seller.earnings')); ?>" class="<?php echo e(request()->is('seller-earnings') ? 'active' : ''); ?>">
            <div class="left">
                <i class="fa-solid fa-dollar-sign"></i> Earnings
            </div>
        </a>
        <a href="<?php echo e(route('seller.shop.preview')); ?>" class="<?php echo e(request()->is('seller-shop-preview') ? 'active' : ''); ?>">
            <div class="left">
                <i class="fa-solid fa-store"></i> View Shop
            </div>
        </a>


    </div>
</aside><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/partials/sidebar.blade.php ENDPATH**/ ?>