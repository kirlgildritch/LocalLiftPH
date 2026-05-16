<section class="inbox-thread <?php echo e($activeConversation ? '' : 'is-empty-thread'); ?>" data-inbox-thread>
    <?php if($activeConversation): ?>
        <?php echo $__env->make('messages.partials.inbox.thread-active', [
            'activeConversation' => $activeConversation,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('messages.partials.inbox.thread-empty', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
</section>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/inbox/thread.blade.php ENDPATH**/ ?>