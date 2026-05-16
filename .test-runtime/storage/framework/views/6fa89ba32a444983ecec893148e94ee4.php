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

                                <div class="seller-review-status-stack">
                                    <div class="seller-rating-chip">
                                        <i class="fa-solid fa-star"></i>
                                        <?php echo e($review->rating); ?>/5
                                    </div>
                                    <span class="seller-reply-status seller-reply-status--<?php echo e($replyState->statusTone); ?>">
                                        <?php echo e($replyState->statusLabel); ?>

                                    </span>
                                </div>
                            </div>

                            <?php if($purchaseDetails): ?>
                            <div class="seller-review-purchase-meta">
                                <i class="fa-solid fa-receipt"></i>
                                <span><?php echo e($purchaseDetails); ?></span>
                            </div>
                            <?php endif; ?>

                            <p><?php echo e($review->comment ?: 'Verified buyer rating submitted.'); ?></p>

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
                                <div class="seller-review-reply-card-header">
                                    <span class="seller-review-reply-icon">
                                        <i class="fa-solid fa-reply"></i>
                                    </span>
                                    <div>
                                        <strong>Your public reply</strong>
                                        <span>Visible below this buyer review.</span>
                                    </div>
                                    <?php if($review->seller_replied_at): ?>
                                    <time><?php echo e($review->seller_replied_at->format('M d, Y')); ?></time>
                                    <?php endif; ?>
                                </div>

                                <p><?php echo e($review->seller_reply); ?></p>
                            </div>
                            <?php endif; ?>

                            <form action="<?php echo e(route('seller.products.reviews.reply', [$product, $review])); ?>" method="POST" class="seller-review-reply-form">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>

                                <div class="seller-review-reply-form-header">
                                    <div>
                                        <label for="seller_reply_<?php echo e($review->id); ?>"><?php echo e($replyState->formTitle); ?></label>
                                        <p><?php echo e($replyState->formHint); ?></p>
                                    </div>
                                    <span>1000 max</span>
                                </div>

                                <div class="seller-review-reply-field">
                                    <textarea name="seller_reply" id="seller_reply_<?php echo e($review->id); ?>" rows="3" maxlength="1000" required
                                        placeholder="<?php echo e($replyState->placeholder); ?>"><?php echo e(old('seller_reply', $review->seller_reply)); ?></textarea>
                                </div>

                                <div class="seller-review-reply-actions">
                                    <small><i class="fa-solid fa-eye"></i> Public response</small>
                                    <button type="submit" class="submitt table-action secondary">
                                        <i class="fa-solid fa-reply"></i>
                                        <?php echo e($replyState->buttonLabel); ?>

                                    </button>
                                </div>
                            </form>
                        </article>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/reviews/partials/card.blade.php ENDPATH**/ ?>