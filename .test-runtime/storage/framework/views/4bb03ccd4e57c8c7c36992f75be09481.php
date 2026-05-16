<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'seller' => null,
    'compact' => false,
    'iconOnly' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'seller' => null,
    'compact' => false,
    'iconOnly' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($seller?->hasVerifiedSellerBadge()): ?>
    <span
        <?php echo e($attributes->class([
            'seller-trust-badge',
            'seller-trust-badge--compact' => $compact,
            'seller-trust-badge--icon-only' => $iconOnly,
        ])); ?>

        title="Verified seller"
        aria-label="Verified seller">
        <i class="fa-solid fa-circle-check"></i>
        <?php if (! ($iconOnly)): ?>
            <span>Verified Seller</span>
        <?php endif; ?>
    </span>
<?php endif; ?>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/components/seller-trust-badge.blade.php ENDPATH**/ ?>