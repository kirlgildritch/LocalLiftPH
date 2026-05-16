<?php if($reviews->isEmpty()): ?>
    <?php echo $__env->make('seller.products.reviews.partials.empty', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
    <div class="seller-review-page-list">
        <?php $__currentLoopData = $reviewCards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reviewCard): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('seller.products.reviews.partials.card', [
                'review' => $reviewCard->review,
                'replyState' => $reviewCard->sellerReply,
                'reviewMedia' => $reviewCard->media,
                'purchaseDetails' => $reviewCard->purchaseDetails,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php echo $__env->make('seller.products.reviews.partials.pagination', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/reviews/partials/list.blade.php ENDPATH**/ ?>