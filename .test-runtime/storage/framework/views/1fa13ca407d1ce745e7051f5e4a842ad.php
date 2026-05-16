<section class="panel detail-card review-section" id="product-reviews">
    <div class="review-section-head">
        <span class="section-kicker">Ratings & Reviews</span>

        <div class="review-summary-chip">
            <strong data-review-average><?php echo e($productPage->averageRating > 0 ? number_format($productPage->averageRating, 1) : '0.0'); ?></strong>
            <span data-review-count><?php echo e($product->reviews_count); ?> review<?php echo e($product->reviews_count !== 1 ? 's' : ''); ?></span>
        </div>
    </div>

    <div class="review-toolbar">
        <?php if($productPage->canReviewProduct): ?>
        <a href="#buyer-review-form" class="review-write-chip" data-review-write-chip>
            <i class="fa-solid fa-pen"></i>
            Write a review
        </a>
        <?php endif; ?>
    </div>

    <?php if($product->reviews_count > $initialReviewsLimit): ?>
        <div class="review-toggle-bar">
            <a href="<?php echo e($productPage->productReviewsToggleUrl); ?>" class="action-btn secondary-btn review-toggle-btn">
                <?php echo e($showAllReviews ? 'Show Fewer Reviews' : 'View All Reviews'); ?>

            </a>
        </div>
    <?php endif; ?>

    <?php if($productPage->canReviewProduct): ?>
    <form action="<?php echo e(route('products.reviews.store', $product)); ?>" method="POST" enctype="multipart/form-data" class="review-form panel" id="buyer-review-form"
        data-review-max-files="<?php echo e($productPage->reviewMedia->maxFiles); ?>"
        data-review-max-file-bytes="<?php echo e($productPage->reviewMedia->effectiveFileBytes); ?>"
        data-review-max-total-bytes="<?php echo e($productPage->reviewMedia->requestBytes ?? 0); ?>"
        data-review-max-file-label="<?php echo e($productPage->reviewMedia->effectiveFileLabel); ?>"
        data-review-max-total-label="<?php echo e($productPage->reviewMedia->requestLabel); ?>">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="order_item_id" value="<?php echo e($productPage->selectedReviewableOrderItem?->id); ?>">

        <div class="review-form-header">
            <div>
                <strong>Leave a review</strong>
                <p>Only buyers with completed purchases can rate this product.</p>
            </div>

            <?php if($reviewableOrderItems->count() > 1): ?>
            <span class="review-order-note" data-review-order-note><?php echo e($reviewableOrderItems->count()); ?> completed purchases eligible</span>
            <?php endif; ?>
        </div>

        <div class="review-form-grid">
            <div class="review-form-field">
                <label for="rating">Your rating</label>
                <select name="rating" id="rating" required>
                    <option value="">Select rating</option>
                    <?php for($rating = 5; $rating >= 1; $rating--): ?>
                    <option value="<?php echo e($rating); ?>" <?php echo e((int) old('rating') === $rating ? 'selected' : ''); ?>>
                        <?php echo e($rating); ?> Star<?php echo e($rating !== 1 ? 's' : ''); ?>

                    </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="review-form-field review-form-field-full">
                <label for="comment">Your review</label>
                <textarea name="comment" id="comment" rows="4" placeholder="Share what you liked about this product..."><?php echo e(old('comment')); ?></textarea>
            </div>

            <div class="review-form-field review-form-field-full review-upload-section">
                <div class="review-upload-header">
                    <label>Upload media</label>
                    <span data-review-upload-status>Up to <?php echo e($productPage->reviewMedia->maxFiles); ?> files, <?php echo e($productPage->reviewMedia->effectiveFileLabel); ?> each, <?php echo e($productPage->reviewMedia->requestLabel); ?> total per upload.</span>
                </div>

                <div class="review-upload-inputs">
                    <div class="review-upload-input">
                        <label for="review_media">Upload photos or videos</label>
                        <input type="file" name="review_media[]" id="review_media" accept="image/*,video/*" multiple data-review-preview-input>
                    </div>
                </div>

                <div class="review-upload-preview" data-review-preview-grid hidden></div>
            </div>
        </div>

        <button type="submit" class="action-btn primary-btn review-submit-btn">Submit Review</button>
    </form>
    <?php endif; ?>

    <div class="review-list" data-review-list>
        <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('products.partials.review-card', ['review' => $review], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="review-empty-state" data-review-empty-state>
            <h3>No reviews yet</h3>
            <p>This product has not received buyer feedback yet.</p>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/reviews.blade.php ENDPATH**/ ?>