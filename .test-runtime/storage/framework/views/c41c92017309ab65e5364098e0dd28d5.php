<?php $__env->startSection('content'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets/css/manage_products.css')); ?>">

<section class="dashboard-wrapper seller-product-reviews-page">
    <div class="container">
        <div class="dashboard-layout">
            <?php echo $__env->make('seller.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <main class="dashboard-main">
                <section class="seller-page-panel panel seller-product-reviews-panel">
                    <div class="page-header seller-product-reviews-header">
                        <div>
                            <span class="section-kicker">Catalog</span>
                            <h2>Product Reviews</h2>
                            <p>See all buyer feedback for this product in one place.</p>
                        </div>

                        <a href="<?php echo e(route('seller.products.index')); ?>" class="table-action secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back to Products
                        </a>
                    </div>

                    <section class="seller-review-product-summary">
                        <div class="seller-review-product-main">
                            <div class="seller-review-product-thumb">
                                <?php if($product->image): ?>
                                <img src="<?php echo e(asset('storage/' . $product->image)); ?>" alt="<?php echo e($product->name); ?>">
                                <?php else: ?>
                                <div class="seller-review-product-placeholder">No Image</div>
                                <?php endif; ?>
                            </div>

                            <div class="seller-review-product-copy">
                                <h3><?php echo e($product->name); ?></h3>
                                <p><?php echo e($product->category?->name ?? 'Uncategorized'); ?></p>
                                <strong>&#8369; <?php echo e(number_format($product->price, 2)); ?></strong>
                            </div>
                        </div>

                        <div class="seller-review-summary-cards">
                            <article class="seller-review-summary-card">
                                <span>Average Rating</span>
                                <strong><?php echo e($product->reviews_avg_rating ? number_format((float) $product->reviews_avg_rating, 1) : 'New'); ?></strong>
                            </article>
                            <article class="seller-review-summary-card">
                                <span>Total Reviews</span>
                                <strong><?php echo e($product->reviews_count); ?></strong>
                            </article>
                        </div>
                    </section>

                    <?php if($reviews->isEmpty()): ?>
                    <div class="seller-review-empty-state">
                        <h3>No reviews yet</h3>
                        <p>This product has not received buyer feedback yet.</p>
                    </div>
                    <?php else: ?>
                    <div class="seller-review-page-list">
                        <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <article class="seller-review-page-card">
                            <div class="seller-review-page-header">
                                <div class="seller-review-author">
                                    <div class="seller-review-author-avatar">
                                        <?php if($review->user?->profile_image): ?>
                                        <img src="<?php echo e(asset('storage/' . $review->user->profile_image)); ?>" alt="<?php echo e($review->user->name ?? 'Buyer'); ?>">
                                        <?php else: ?>
                                        <span><?php echo e(strtoupper(mb_substr($review->user->name ?? 'B', 0, 1))); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <strong>
                                            <?php echo e($review->user->name ?? 'Buyer'); ?>

                                            <i class="fa-solid fa-circle-check"></i>
                                        </strong>
                                        <span><?php echo e($review->created_at->format('M d, Y')); ?></span>
                                    </div>
                                </div>

                                <div class="seller-rating-chip">
                                    <i class="fa-solid fa-star"></i>
                                    <?php echo e($review->rating); ?>/5
                                </div>
                            </div>

                            <p><?php echo e($review->comment ?: 'Verified buyer rating submitted.'); ?></p>

                            <?php
                            $reviewMedia = $review->media->isNotEmpty()
                            ? $review->media
                            : collect([
                            $review->image_path ? (object) ['type' => 'image', 'path' => $review->image_path] : null,
                            $review->video_path ? (object) ['type' => 'video', 'path' => $review->video_path] : null,
                            ])->filter();
                            ?>

                            <?php if($reviewMedia->isNotEmpty()): ?>
                            <div class="seller-review-media-grid">
                                <?php $__currentLoopData = $reviewMedia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($media->type === 'video'): ?>
                                <video controls>
                                    <source src="<?php echo e(asset('storage/' . $media->path)); ?>">
                                </video>
                                <?php else: ?>
                                <img src="<?php echo e(asset('storage/' . $media->path)); ?>" alt="Review picture">
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php endif; ?>

                            <?php if(filled($review->seller_reply)): ?>
                            <div class="seller-review-reply-card">
                                <div>
                                    <strong>Your reply</strong><br>
                                    <?php if($review->seller_replied_at): ?>
                                    <span><?php echo e($review->seller_replied_at->format('M d, Y')); ?></span><br>
                                    <?php endif; ?>
                                </div>

                                <br>
                                <p><?php echo e($review->seller_reply); ?></p>
                            </div>
                            <?php endif; ?>

                            <form action="<?php echo e(route('seller.products.reviews.reply', [$product, $review])); ?>" method="POST" class="seller-review-reply-form">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>

                                <div class="seller-review-reply-form-header">
                                    <label for="seller_reply_<?php echo e($review->id); ?>">
                                        <?php echo e($review->seller_reply ? 'Edit your reply' : 'Reply to this review'); ?>

                                    </label>
                                    <span><?php echo e($review->seller_reply ? ':' : ':'); ?></span>
                                </div>

                                <div class="seller-review-reply-field">
                                    <textarea name="seller_reply" id="seller_reply_<?php echo e($review->id); ?>" rows="3" maxlength="1000" required
                                        placeholder="Thank the buyer, answer concerns, or clarify product details..."><?php echo e(old('seller_reply', $review->seller_reply)); ?></textarea>
                                </div>

                                <div class="seller-review-reply-actions">
                                    <button type="submit" class="submitt table-action secondary">
                                        <i class="fa-solid fa-reply"></i>
                                        <?php echo e($review->seller_reply ? 'Submit Reply' : 'Post Reply'); ?>

                                    </button>
                                </div>
                            </form>
                        </article>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <?php if($reviews->hasPages()): ?>
                    <div class="seller-review-pagination">
                        <?php if($reviews->onFirstPage()): ?>
                        <span class="table-action secondary is-disabled">Previous</span>
                        <?php else: ?>
                        <a href="<?php echo e($reviews->previousPageUrl()); ?>" class="table-action secondary">Previous</a>
                        <?php endif; ?>

                        <span class="seller-review-pagination-meta">
                            Page <?php echo e($reviews->currentPage()); ?> of <?php echo e($reviews->lastPage()); ?>

                        </span>

                        <?php if($reviews->hasMorePages()): ?>
                        <a href="<?php echo e($reviews->nextPageUrl()); ?>" class="table-action secondary">Next</a>
                        <?php else: ?>
                        <span class="table-action secondary is-disabled">Next</span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </section>
            </main>
        </div>
    </div>
</section>

<style>
    .seller-product-reviews-page .container {
        max-width: 1180px;
        margin: 0 auto;
        padding-inline: 24px;
    }

    .seller-product-reviews-page .dashboard-main {
        width: 100%;
        min-width: 0;
    }

    .seller-product-reviews-panel {
        display: grid;
        gap: 24px;
        width: 100%;
        padding: 28px;
        border-radius: 24px;
    }

    .seller-product-reviews-header {
        align-items: flex-start;
        gap: 18px;
    }

    .seller-product-reviews-header p {
        margin: 10px 0 0;
        color: #8fa7c4;
        line-height: 1.75;
    }

    .seller-review-product-summary {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        gap: 24px;
        padding: 22px;
        border: 1px solid rgba(187, 222, 251, 0.12);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
    }

    .seller-review-product-main {
        display: flex;
        align-items: center;
        gap: 18px;
        min-width: 0;
        flex: 1;
    }

    .seller-review-product-thumb {
        width: 90px;
        height: 90px;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid rgba(187, 222, 251, 0.12);
        background: rgba(255, 255, 255, 0.03);
        flex-shrink: 0;
    }

    .seller-review-product-thumb img,
    .seller-review-product-placeholder {
        width: 100%;
        height: 100%;
    }

    .seller-review-product-thumb img {
        object-fit: cover;
        display: block;
    }

    .seller-review-product-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #8fa7c4;
        font-size: 12px;
        font-weight: 600;
    }

    .seller-review-product-copy {
        display: grid;
        gap: 8px;
        min-width: 0;
    }

    .seller-review-product-copy h3,
    .seller-review-empty-state h3,
    .seller-review-page-header strong {
        margin: 0;
        color: #f5f9ff;
    }

    .seller-review-product-copy p,
    .seller-review-empty-state p,
    .seller-review-page-card p,
    .seller-review-page-header span,
    .seller-review-reply-card span {
        margin: 0;
        color: #8fa7c4;
    }

    .seller-review-summary-cards {
        display: grid;
        grid-template-columns: repeat(2, minmax(140px, 1fr));
        gap: 12px;
        flex-shrink: 0;
    }

    .seller-review-summary-card {
        display: grid;
        gap: 8px;
        padding: 18px;
        border: 1px solid rgba(187, 222, 251, 0.12);
        border-radius: 20px;
        background: rgba(7, 15, 27, 0.42);
    }

    .seller-review-summary-card span {
        color: #8fa7c4;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .seller-review-summary-card strong {
        color: #f5f9ff;
        font-size: 1.4rem;
    }

    .seller-review-empty-state,
    .seller-review-page-card {
        padding: 22px;
        border: 1px solid rgba(187, 222, 251, 0.12);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
    }

    .seller-review-page-list {
        display: grid;
        gap: 18px;
    }

    .seller-review-page-card {
        display: grid;
        gap: 16px;
    }

    .seller-review-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .seller-review-author {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .seller-review-author-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        overflow: hidden;
        flex: 0 0 auto;
        border: 1px solid rgba(147, 197, 253, 0.24);
        background: rgba(96, 165, 250, 0.12);
    }

    .seller-review-author-avatar img,
    .seller-review-author-avatar span {
        width: 100%;
        height: 100%;
    }

    .seller-review-author-avatar img {
        display: block;
        object-fit: cover;
    }

    .seller-review-author-avatar span {
        display: flex;
        align-items: center;
        justify-content: center;
        color: #bfdbfe;
        font-weight: 700;
    }

    .seller-review-author strong {
        display: flex;
        align-items: center;
        gap: 6px;
        line-height: 1.25;
    }

    .seller-review-author span {
        display: block;
        margin-top: 4px;
        font-size: 0.9rem;
    }

    .seller-rating-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        flex: 0 0 auto;
        padding: 8px 12px;
        border-radius: 999px;
        border: 1px solid rgba(250, 204, 21, 0.24);
        background: rgba(250, 204, 21, 0.12);
        color: #fef3c7;
        font-weight: 700;
    }

    .seller-rating-chip i {
        color: #facc15;
    }

    .seller-review-media-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        width: 100%;
    }

    .seller-review-media-grid img,
    .seller-review-media-grid video {
        display: block;
        width: 100%;
        aspect-ratio: 4 / 3;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid rgba(187, 222, 251, 0.12);
        background: rgba(7, 15, 27, 0.48);
    }

    .seller-review-media-grid video {
        object-fit: contain;
    }

    .seller-review-reply-card {
        padding: 16px;
        border: 1px solid rgba(96, 165, 250, 0.18);
        border-radius: 16px;
        background: rgba(96, 165, 250, 0.08);
    }

    .seller-review-reply-card strong {
        color: #f5f9ff;
    }

    .seller-review-reply-card p {
        margin-top: 8px;
        color: #dbeafe;
    }

    .seller-review-reply-form {
        display: grid;
        gap: 12px;
        margin-top: 4px;
        padding: 18px;
        border: 1px solid rgba(147, 197, 253, 0.18);
        border-radius: 18px;
        background: rgba(7, 15, 27, 0.34);
    }

    .seller-review-reply-form-header {
        display: flex;
        align-items: center;
        gap: 4px;
        color: #f5f9ff;
        font-weight: 700;
    }

    .seller-review-reply-field {
        position: relative;
    }

    .seller-review-reply-form textarea {
        display: block;
        width: 100%;
        min-height: 128px;
        padding: 16px;
        border: 1px solid rgba(147, 197, 253, 0.28);
        border-radius: 14px;
        background: rgba(7, 15, 27, 0.72);
        color: #f5f9ff;
        line-height: 1.6;
        resize: vertical;
        outline: none;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
    }

    .seller-review-reply-form textarea:focus {
        border-color: rgba(96, 165, 250, 0.82);
        background: rgba(9, 22, 38, 0.9);
        box-shadow:
            0 0 0 4px rgba(96, 165, 250, 0.14),
            inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .seller-review-reply-actions {
        display: flex;
        justify-content: flex-end;
    }

    .seller-review-pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .seller-review-pagination-meta {
        color: #8fa7c4;
        font-weight: 600;
    }

    .is-disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    @media (max-width: 900px) {
        .seller-product-reviews-panel {
            padding: 22px;
        }

        .seller-review-product-summary,
        .seller-review-page-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .seller-review-summary-cards {
            width: 100%;
        }

        .seller-review-media-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .seller-product-reviews-page .container {
            padding-inline: 14px;
        }

        .seller-product-reviews-panel {
            gap: 18px;
            padding: 18px;
            border-radius: 18px;
        }

        .seller-product-reviews-header {
            flex-direction: column;
        }

        .seller-review-summary-cards {
            grid-template-columns: 1fr;
        }

        .seller-review-product-summary,
        .seller-review-page-card,
        .seller-review-reply-form {
            padding: 16px;
        }

        .seller-review-product-main {
            align-items: flex-start;
            width: 100%;
        }

        .seller-review-product-thumb {
            width: 72px;
            height: 72px;
            border-radius: 14px;
        }

        .seller-review-media-grid {
            grid-template-columns: 1fr;
        }

        .seller-review-reply-actions {
            justify-content: stretch;
        }

        .seller-review-reply-actions .table-action {
            width: 100%;
            justify-content: center;
        }

        .seller-review-pagination {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .seller-review-pagination > * {
            justify-self: stretch;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.seller', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/reviews.blade.php ENDPATH**/ ?>