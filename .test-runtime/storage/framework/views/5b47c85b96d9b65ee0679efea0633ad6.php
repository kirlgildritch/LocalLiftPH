<section class="checkout-card panel">
    <div class="card-header">
        <div class="step-title">
            <span class="step-number">3</span>
            <div>
                <span class="toolbar-label">Step</span>
                <h3>Payment Information</h3>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="payment-method-grid" data-payment-methods>
            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $methodKey => $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="payment-method-card <?php echo e($selectedPaymentMethod === $methodKey ? 'is-selected' : ''); ?>">
                    <input
                        type="radio"
                        name="payment_method"
                        value="<?php echo e($methodKey); ?>"
                        form="checkout-submit-form"
                        data-payment-choice
                        data-payment-label="<?php echo e($method['label']); ?>"
                        data-payment-short-label="<?php echo e($method['short_label']); ?>"
                        data-payment-instructions="<?php echo e($method['instructions']); ?>"
                        <?php echo e($selectedPaymentMethod === $methodKey ? 'checked' : ''); ?>

                    >
                    <span class="payment-method-card__icon">
                        <i class="fa-solid <?php echo e($method['icon']); ?>"></i>
                    </span>
                    <span class="payment-method-card__copy">
                        <strong><?php echo e($method['label']); ?></strong>
                        <small><?php echo e($method['description']); ?></small>
                    </span>
                    <span class="payment-method-card__check">
                        <i class="fa-solid fa-check"></i>
                    </span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="payment-note" data-payment-note>
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <strong data-payment-note-title><?php echo e($selectedPayment['label']); ?></strong>
                <p data-payment-note-copy><?php echo e($selectedPayment['instructions']); ?></p>
            </div>
        </div>
    </div>
</section>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/checkout/partials/payment-method.blade.php ENDPATH**/ ?>