<div class="seller-notifications-toolbar">
    <?php $__currentLoopData = $filterLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('seller.notifications.index', array_filter(['filter' => $key]))); ?>"
            class="seller-notifications-chip <?php echo e($filter === $key ? 'is-active' : ''); ?>">
            <?php echo e($label); ?>

        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/notifications/partials/toolbar.blade.php ENDPATH**/ ?>