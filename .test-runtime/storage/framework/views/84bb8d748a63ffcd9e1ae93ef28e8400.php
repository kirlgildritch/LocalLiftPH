<div class="seller-notification-summary">
    <div class="seller-notification-summary__card">
        <p class="seller-notification-summary__label">Total Notifications</p>
        <p class="seller-notification-summary__value" data-seller-notification-total>
            <?php echo e($notifications->total()); ?>

        </p>
    </div>

    <div class="seller-notification-summary__card">
        <p class="seller-notification-summary__label">Unread</p>
        <p class="seller-notification-summary__value" data-seller-notification-unread>
            <?php echo e($unreadCount); ?>

        </p>
    </div>

    <div class="seller-notification-summary__card">
        <p class="seller-notification-summary__label">Showing</p>
        <p class="seller-notification-summary__value" data-seller-notification-showing>
            <?php echo e($notifications->count()); ?>

        </p>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/notifications/partials/summary.blade.php ENDPATH**/ ?>