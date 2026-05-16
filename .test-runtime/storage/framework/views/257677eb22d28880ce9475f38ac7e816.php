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
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/seller/products/reviews/partials/pagination.blade.php ENDPATH**/ ?>