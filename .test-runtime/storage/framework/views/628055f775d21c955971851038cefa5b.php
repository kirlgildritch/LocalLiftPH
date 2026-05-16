<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php
        $currentRoute = request()->route()?->getName();
        $sellerRouteTitle = match (true) {
            $currentRoute === 'seller.dashboard' => 'Seller Dashboard',
            $currentRoute === 'seller.products.index' => 'Manage Products',
            $currentRoute === 'seller.products.create' => 'Add Product',
            $currentRoute === 'seller.products.edit' => 'Edit Product',
            $currentRoute === 'seller.products.reviews' => 'Product Reviews',
            $currentRoute === 'seller.orders' => 'Seller Orders',
            $currentRoute === 'seller.earnings' => 'Earnings',
            $currentRoute === 'seller.messages',
            $currentRoute === 'seller.messages.show' => 'Seller Messages',
            $currentRoute === 'seller.profile' => 'Seller Profile',
            $currentRoute === 'seller.settings' => 'Seller Settings',
            $currentRoute === 'seller.search' => 'Seller Search',
            $currentRoute === 'seller.shop.preview' => 'Shop Preview',
            default => 'Seller Dashboard',
        };

        $sellerDocumentTitle = trim($__env->yieldContent('title')) ?: ($title ?? $sellerRouteTitle);
        $sellerDashboardCss = asset('assets/css/seller_dashboard.css') . '?v=' . @filemtime(public_path('assets/css/seller_dashboard.css'));
        $sellerMessagesCss = asset('assets/css/messages.css') . '?v=' . @filemtime(public_path('assets/css/messages.css'));
        $sellerLayoutScript = asset('assets/js/seller-layout.js') . '?v=' . @filemtime(public_path('assets/js/seller-layout.js'));
    ?>
    <title><?php echo e($sellerDocumentTitle); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="<?php echo e($sellerDashboardCss); ?>">
    <?php if(empty($disableFloatingChatWidget)): ?>
        <link rel="stylesheet" href="<?php echo e($sellerMessagesCss); ?>">
    <?php endif; ?>
    <link rel="icon" type="image/png" sizes="64x64" href="<?php echo e(asset('assets/image/favicon.png')); ?>">
    <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php endif; ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
        }
    </style>

</head>

<body data-loading-scope="explicit">
    <?php
        $sellerToast = null;

        foreach (['success', 'error', 'warning', 'info'] as $type) {
            if (session()->has($type)) {
                $sellerToast = [
                    'type' => $type,
                    'message' => session($type),
                ];
                break;
            }
        }

        if (! $sellerToast && $errors->any()) {
            $sellerToast = [
                'type' => 'error',
                'message' => $errors->first(),
            ];
        }

        $sellerToastIcon = $sellerToast
            ? match ($sellerToast['type']) {
                'error' => 'fa-circle-xmark',
                'warning' => 'fa-triangle-exclamation',
                'info' => 'fa-circle-info',
                default => 'fa-circle-check',
            }
            : null;
    ?>

    <div class="page-wrapper">
        <?php echo $__env->make('partials.seller-header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <main class="page-content">
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <?php echo $__env->make('partials.seller-footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(auth('seller')->check() && empty($disableFloatingChatWidget)): ?>
            <?php echo $__env->make('messages.partials.floating-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    </div>

    <?php if($sellerToast): ?>
        <div
            id="seller-toast"
            class="toast-message toast-message--<?php echo e($sellerToast['type']); ?>"
            role="status"
            aria-live="polite"
        >
            <i class="fa-solid <?php echo e($sellerToastIcon); ?>"></i>
            <span><?php echo e($sellerToast['message']); ?></span>
        </div>
    <?php endif; ?>

    <script src="<?php echo e($sellerLayoutScript); ?>" defer></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/layouts/seller.blade.php ENDPATH**/ ?>