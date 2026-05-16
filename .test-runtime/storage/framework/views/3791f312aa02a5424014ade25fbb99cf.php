<aside class="inbox-sidebar">
    <div class="inbox-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Use the list below to open a conversation" value="" readonly>
    </div>

    <div class="inbox-conversation-list" data-inbox-conversation-list>
        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e($conversation['show_url']); ?>" class="inbox-conversation-item <?php echo e(!empty($conversation['active']) ? 'is-active' : ''); ?>">
                <span class="inbox-conversation-avatar-wrap">
                    <span class="inbox-conversation-avatar">
                        <?php if(!empty($conversation['avatar_url'])): ?>
                            <img src="<?php echo e($conversation['avatar_url']); ?>" alt="<?php echo e($conversation['name']); ?>">
                        <?php else: ?>
                            <?php echo e($conversation['avatar_initials']); ?>

                        <?php endif; ?>
                    </span>
                    <span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot
                        data-conversation-id="<?php echo e($conversation['id']); ?>"></span>
                </span>

                <span class="inbox-conversation-copy">
                    <span class="inbox-conversation-topline">
                        <strong><?php echo e($conversation['name']); ?></strong>
                        <?php if(($conversation['unread_count'] ?? 0) > 0): ?>
                            <span class="inbox-unread-badge"><?php echo e($conversation['unread_count']); ?></span>
                        <?php endif; ?>
                    </span>

                    <p><?php echo e($conversation['preview']); ?></p>
                    <small><?php echo e($conversation['updated_at']); ?></small>
                </span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php echo $__env->make('messages.partials.inbox.empty-state', [
                'title' => 'No conversations yet',
                'message' => 'Start a conversation from a product or shop page.',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    </div>
</aside>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/inbox/conversation-list.blade.php ENDPATH**/ ?>