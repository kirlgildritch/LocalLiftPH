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
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/reviews/partials/summary.blade.php ENDPATH**/ ?>