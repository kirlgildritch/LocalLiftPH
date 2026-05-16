<?php
    $messageMediaType = $message['media_type'] ?? (!empty($message['has_video']) ? 'video' : (!empty($message['has_image']) ? 'image' : null));
    $messageMediaUrl = $message['media_url'] ?? $message['video_url'] ?? $message['image_url'] ?? null;
?>

<div class="inbox-message-row <?php echo e(!empty($message['is_current_user']) ? 'is-current-user' : ''); ?>">
    <div class="inbox-message-bubble">
        <strong><?php echo e($message['sender_label']); ?></strong>
        <?php if(!empty($message['has_product']) && !empty($message['product'])): ?>
            <a href="<?php echo e($message['product']['url']); ?>" class="inbox-product-card">
                <img src="<?php echo e($message['product']['image_url']); ?>" alt="<?php echo e($message['product']['name']); ?>"
                    class="inbox-product-card-image">

                <span class="inbox-product-card-copy">
                    <span class="inbox-product-card-label">Product</span>
                    <strong><?php echo e($message['product']['name']); ?></strong>
                    <span><?php echo e($message['product']['price_label']); ?></span>
                    <span><?php echo e($message['product']['shop_name']); ?></span>
                </span>
            </a>
        <?php endif; ?>
        <?php if(!empty($message['has_text'])): ?>
            <p><?php echo e($message['message']); ?></p>
        <?php endif; ?>
        <?php if(!empty($messageMediaUrl)): ?>
            <?php if($messageMediaType === 'video'): ?>
                <video src="<?php echo e($messageMediaUrl); ?>" controls preload="metadata" class="inbox-message-media inbox-message-media--video"></video>
            <?php else: ?>
                <img src="<?php echo e($messageMediaUrl); ?>" alt="Shared image" class="inbox-message-media">
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <span class="inbox-message-meta">
        <?php echo e($message['time']); ?>

        <?php if(!empty($message['status_label'])): ?>
            <em><?php echo e($message['status_label']); ?></em>
        <?php endif; ?>
    </span>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/inbox/message-row.blade.php ENDPATH**/ ?>