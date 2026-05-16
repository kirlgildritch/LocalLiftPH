<section class="seller-notification-panel panel">
    <div class="seller-notification-panel__header">
        <h3 class="seller-notification-panel__title">Notifications</h3>

        <div class="seller-notification-actions">
            <form method="POST" action="<?php echo e(route('seller.notifications.read-all')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <button class="seller-notification-btn primary" type="submit" <?php if($unreadCount === 0): echo 'disabled'; endif; ?>>
                    <i class="fa-solid fa-check-double"></i>
                    Mark all as read
                </button>
            </form>

            <form method="POST" action="<?php echo e(route('seller.notifications.clear-read')); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="seller-notification-btn danger" type="submit" <?php if($readCount === 0): echo 'disabled'; endif; ?>>
                    <i class="fa-solid fa-trash"></i>
                    Clear read
                </button>
            </form>
        </div>
    </div>

    <div class="seller-notification-list" data-seller-notification-list>
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('seller.notifications.partials.row', ['notification' => $notification], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="seller-notification-empty" data-seller-notification-empty>
                <i class="fa-regular fa-bell-slash"></i>
                <p>No notifications found.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="seller-notification-pagination">
        <?php if($notifications->hasPages()): ?>
            <?php echo e($notifications->links()); ?>

        <?php endif; ?>
    </div>
</section>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/notifications/partials/panel.blade.php ENDPATH**/ ?>