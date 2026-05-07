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
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/helpbot.css')); ?>">
    <link rel="icon" type="image/png" sizes="64x64" href="<?php echo e(asset('assets/image/favicon.png')); ?>">
    <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php endif; ?>
</head>

<body data-loading-scope="explicit">

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>




</body>

</html>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/layouts/log.blade.php ENDPATH**/ ?>