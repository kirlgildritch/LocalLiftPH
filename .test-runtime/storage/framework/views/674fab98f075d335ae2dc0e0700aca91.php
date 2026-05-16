<?php
    $vouchers = collect($vouchers ?? []);
    $title = $title ?? 'Available Vouchers';
    $emptyText = $emptyText ?? null;
?>

<?php if($vouchers->isNotEmpty()): ?>
    <div class="buyer-voucher-list">
        <div class="buyer-voucher-list__head">
            <span class="section-kicker"><?php echo e($title); ?></span>
        </div>

        <div class="buyer-voucher-grid">
            <?php $__currentLoopData = $vouchers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $voucher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="buyer-voucher-card">
                    <div class="buyer-voucher-card__icon">
                        <i class="fa-solid fa-ticket"></i>
                    </div>
                    <div class="buyer-voucher-card__body">
                        <strong><?php echo e($voucher['code']); ?></strong>
                        <span><?php echo e($voucher['label']); ?></span>
                        <small>
                            Min spend PHP <?php echo e(number_format($voucher['minimum_subtotal'], 2)); ?>

                            <?php if($voucher['maximum_discount']): ?>
                                | Cap PHP <?php echo e(number_format($voucher['maximum_discount'], 2)); ?>

                            <?php endif; ?>
                            <?php if($voucher['ends_at']): ?>
                                | Until <?php echo e($voucher['ends_at']->format('M d, Y')); ?>

                            <?php endif; ?>
                        </small>
                    </div>
                    <?php if(!empty($voucher['apply_url'])): ?>
                        <a href="<?php echo e($voucher['apply_url']); ?>" class="buyer-voucher-card__action">Use</a>
                    <?php else: ?>
                        <button
                            type="button"
                            class="buyer-voucher-card__action"
                            data-copy-voucher-code="<?php echo e($voucher['code']); ?>"
                        >
                            Use code
                        </button>
                    <?php endif; ?>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php elseif($emptyText): ?>
    <p class="buyer-voucher-empty"><?php echo e($emptyText); ?></p>
<?php endif; ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/vouchers/partials/buyer-voucher-list.blade.php ENDPATH**/ ?>