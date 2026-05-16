<div class="inbox-layout" data-chat-page data-fetch-url="<?php echo e($chatData['meta']['widget_route'] ?? ''); ?>"
    data-list-url="<?php echo e($isSellerInbox ? route('seller.messages') : route('messages.index')); ?>"
    data-mobile-view="<?php echo e(request()->route('conversation') ? 'thread' : 'list'); ?>">
    <script type="application/json" data-chat-page-state><?php echo json_encode($chatData, 15, 512) ?></script>

    <?php echo $__env->make('messages.partials.inbox.conversation-list', [
        'conversations' => $conversations,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('messages.partials.inbox.thread', [
        'activeConversation' => $activeConversation,
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/page-inbox.blade.php ENDPATH**/ ?>