<?php
    $modalId = $modalId ?? 'report-modal';
    $modalContext = $modalContext ?? 'product';
    $triggerLabel = $triggerLabel ?? 'Report listing';
    $sellerId = $sellerId ?? null;
    $productId = $productId ?? null;
    $reportErrors = $errors->getBag('reportSubmission');
    $hasReportErrors = $reportErrors->any();
    $shouldAutoOpen = session('report_modal_open') === $modalContext || $hasReportErrors;
?>

<button type="button" class="report-trigger-button" data-report-open="<?php echo e($modalId); ?>" aria-label="<?php echo e($triggerLabel); ?>" title="<?php echo e($triggerLabel); ?>">
    <i class="fa-solid fa-flag"></i>
</button>

<div class="report-modal-shell" id="<?php echo e($modalId); ?>" <?php if(!$shouldAutoOpen): ?> hidden <?php endif; ?>>
    <div class="report-modal-card">
        <div class="report-modal-header">
            <div>
                <span class="report-modal-kicker">Safety</span>
                <h3>Report <?php echo e($modalContext === 'seller' ? 'Seller' : 'Product'); ?></h3>
            </div>
            <button type="button" class="report-modal-close" data-report-close="<?php echo e($modalId); ?>" aria-label="Close report form">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="<?php echo e(route('reports.store')); ?>" method="POST" class="report-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="modal_context" value="<?php echo e($modalContext); ?>">
            <?php if($productId): ?>
                <input type="hidden" name="product_id" value="<?php echo e($productId); ?>">
            <?php endif; ?>
            <?php if($sellerId): ?>
                <input type="hidden" name="seller_id" value="<?php echo e($sellerId); ?>">
            <?php endif; ?>

            <?php if($hasReportErrors): ?>
                <div class="report-form-feedback report-form-feedback--error">
                    <?php echo e($reportErrors->first()); ?>

                </div>
            <?php endif; ?>

            <label class="report-form-field" for="<?php echo e($modalId); ?>-reason">
                <span>Reason</span>
                <select name="reason" id="<?php echo e($modalId); ?>-reason" required>
                    <option value="">Select a reason</option>
                    <option value="spam" <?php echo e(old('reason') === 'spam' ? 'selected' : ''); ?>>Spam</option>
                    <option value="fake product" <?php echo e(old('reason') === 'fake product' ? 'selected' : ''); ?>>Fake product</option>
                    <option value="inappropriate" <?php echo e(old('reason') === 'inappropriate' ? 'selected' : ''); ?>>Inappropriate</option>
                    <option value="other" <?php echo e(old('reason') === 'other' ? 'selected' : ''); ?>>Other</option>
                </select>
            </label>

            <label class="report-form-field" for="<?php echo e($modalId); ?>-message">
                <span>Message (optional)</span>
                <textarea name="message" id="<?php echo e($modalId); ?>-message" rows="4" placeholder="Add extra details to help the admin team review this report."><?php echo e(old('message')); ?></textarea>
            </label>

            <div class="report-form-actions">
                <button type="submit" class="action-btn primary-btn">Submit Report</button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/partials/report-modal.blade.php ENDPATH**/ ?>