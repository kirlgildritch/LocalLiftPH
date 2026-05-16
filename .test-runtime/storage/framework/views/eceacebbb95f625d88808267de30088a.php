<?php
    $data = $notification->data ?? [];
    $type = $data['type'] ?? $data['category'] ?? 'admin';
    $action = $data['action'] ?? 'notification';
    $title = $data['title'] ?? 'Notification';
    $message = $data['message'] ?? 'You have a new notification.';
    $tag = $filterLabels[$type] ?? ucfirst($type);
    $icon = match ($action) {
        'new_order', 'order_completed', 'order_cancelled', 'buyer_confirmed_receipt', 'pending_order_not_shipped' => 'fa-bag-shopping',
        'buyer_message' => 'fa-envelope',
        'buyer_review' => 'fa-star',
        'product_low_stock', 'product_out_of_stock', 'product_edited' => 'fa-box',
        default => 'fa-bell',
    };
?>

<article class="seller-notification-row <?php echo e($notification->read_at ? '' : 'unread'); ?>"
    data-seller-notification-row
    data-seller-notification-id="<?php echo e($notification->id); ?>"
    data-seller-notification-read="<?php echo e($notification->read_at ? '1' : '0'); ?>">
    <a href="<?php echo e(route('seller.notifications.open', $notification)); ?>"
        class="seller-notification-row__icon"
        <?php if($action === 'buyer_message' && !empty($data['related_id'])): ?>
            data-chat-notification-link
            data-chat-conversation-id="<?php echo e((int) $data['related_id']); ?>"
        <?php endif; ?>>
        <i class="fa-solid <?php echo e($icon); ?>"></i>
    </a>

    <a href="<?php echo e(route('seller.notifications.open', $notification)); ?>"
        class="seller-notification-row__content"
        <?php if($action === 'buyer_message' && !empty($data['related_id'])): ?>
            data-chat-notification-link
            data-chat-conversation-id="<?php echo e((int) $data['related_id']); ?>"
        <?php endif; ?>>
        <div class="seller-notification-row__header">
            <h3><?php echo e($title); ?></h3>
            <span class="seller-notification-tag"><?php echo e($tag); ?></span>
        </div>
        <p><?php echo e($message); ?></p>
        <small><?php echo e($notification->created_at?->format('M d, Y h:i A')); ?></small>
    </a>

    <div class="seller-notification-row__actions">
        <span class="seller-notification-status <?php echo e($notification->read_at ? '' : 'unread'); ?>"
            data-seller-notification-status>
            <?php echo e($notification->read_at ? 'Read' : 'Unread'); ?>

        </span>

        <?php if(! $notification->read_at): ?>
            <form method="POST" action="<?php echo e(route('seller.notifications.read', $notification)); ?>"
                data-seller-notification-read-form>
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <button class="seller-notification-icon-button" type="submit" title="Mark as read">
                    <i class="fa-solid fa-check"></i>
                </button>
            </form>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('seller.notifications.destroy', $notification)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button class="seller-notification-icon-button danger" type="submit" title="Delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</article>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/notifications/partials/row.blade.php ENDPATH**/ ?>