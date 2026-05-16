<form action="<?php echo e($activeConversation['send_url']); ?>" method="POST" enctype="multipart/form-data" class="inbox-reply-form" data-inbox-form>
    <?php echo csrf_field(); ?>
    <input type="text" name="message" placeholder="Type a message..." value="<?php echo e(old('message')); ?>">
    <input type="file" name="image" accept="image/*,video/*">
    <button type="submit" class="page-action-btn">Send</button>
</form>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/messages/partials/inbox/composer.blade.php ENDPATH**/ ?>