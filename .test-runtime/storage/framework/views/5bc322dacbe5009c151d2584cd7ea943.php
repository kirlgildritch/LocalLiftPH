<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'LocalLift PH'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/product_cards.css')); ?>">
    <?php if(empty($disableFloatingChatWidget)): ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets/css/messages.css')); ?>">
    <?php endif; ?>
    <link rel="icon" type="image/png" sizes="64x64" href="<?php echo e(asset('assets/image/favicon.png')); ?>">
    <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php endif; ?>
    <?php
        $appLayoutScript = asset('assets/js/app-layout.js') . '?v=' . @filemtime(public_path('assets/js/app-layout.js'));
    ?>
</head>

<body data-loading-scope="explicit">

    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if(auth('web')->check() && empty($disableFloatingChatWidget)): ?>
        <?php echo $__env->make('messages.partials.floating-chat', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>

    <?php if(session('success')): ?>
        <div id="toast-success" class="toast-success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?>

    <script src="<?php echo e($appLayoutScript); ?>" defer></script>
</body>

</html>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/layouts/app.blade.php ENDPATH**/ ?>