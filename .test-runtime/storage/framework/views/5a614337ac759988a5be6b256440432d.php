<div class="inbox-thread-header">
    <button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations">
        <i class="fa-solid fa-arrow-left"></i>
    </button>

    <span class="inbox-thread-avatar-wrap">
        <span class="inbox-thread-avatar">
            <?php if(!empty($activeConversation['avatar_url'])): ?>
                <img src="<?php echo e($activeConversation['avatar_url']); ?>" alt="<?php echo e($activeConversation['name']); ?>">
            <?php else: ?>
                <?php echo e($activeConversation['avatar_initials']); ?>

            <?php endif; ?>
        </span>
        <span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot
            data-conversation-id="<?php echo e($activeConversation['id']); ?>"></span>
    </span>

    <div class="inbox-thread-heading">
        <h3><?php echo e($activeConversation['name']); ?></h3>
        <span class="inbox-thread-status" data-presence-label
            data-conversation-id="<?php echo e($activeConversation['id']); ?>"
            data-base-label="<?php echo e($activeConversation['role_label']); ?>"><?php echo e($activeConversation['role_label']); ?></span>
    </div>
</div>

<div class="inbox-thread-messages" data-inbox-messages>
    <?php $__empty_1 = true; $__currentLoopData = $activeConversation['messages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php echo $__env->make('messages.partials.inbox.message-row', [
            'message' => $message,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <?php echo $__env->make('messages.partials.inbox.empty-state', [
            'title' => 'No messages yet',
            'message' => 'Send the first message in this conversation.',
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</div>

<?php echo $__env->make('messages.partials.inbox.composer', [
    'activeConversation' => $activeConversation,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/inbox/thread-active.blade.php ENDPATH**/ ?>