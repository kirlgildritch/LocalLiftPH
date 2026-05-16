<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'product',
    'href' => null,
    'subtitle' => null,
    'fallbackImage' => null,
    'cardClass' => '',
    'buyerLocation' => null,
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
    'product',
    'href' => null,
    'subtitle' => null,
    'fallbackImage' => null,
    'cardClass' => '',
    'buyerLocation' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?> 

<?php
    $resolvedHref = $href ?: route('products.show', $product->id);
    $resolvedSubtitle = $subtitle ?? ($product->user->sellerProfile?->store_name ?? 'LocalLift Seller');
    $resolvedFallbackImage = $fallbackImage ?: asset('assets/images/default-product.png');
    $resolvedImage = $product->image ? asset('storage/' . $product->image) : $resolvedFallbackImage;
    $averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
    $sellerProfile = $product->user?->sellerProfile;
    $locationLabel = \App\Support\LocationBrowsing::matchLabel($sellerProfile, $buyerLocation);
?>

<a href="<?php echo e($resolvedHref); ?>" class="market-product-card product-card-link <?php echo e($cardClass); ?>">
    <div class="market-product-card__image">
        <img src="<?php echo e($resolvedImage); ?>" alt="<?php echo e($product->name); ?>" loading="lazy" decoding="async">
        <?php if($locationLabel): ?>
            <div class="market-product-card__location" title="<?php echo e($locationLabel); ?>">
                <i class="fa-solid fa-location-dot"></i>
                <span><?php echo e($locationLabel); ?></span>
            </div>
        <?php endif; ?>
    </div>
    <div class="market-product-card__body">
        <span class="market-product-card__badge"><?php echo e($product->category?->name ?? 'Uncategorized'); ?></span>
    <h4 class="market-product-card__title" title="<?php echo e($product->name); ?>"><?php echo e($product->name); ?></h4>

      <div class="ratings">
            <div class="review-stars-display" aria-label="Average rating: <?php echo e($averageRating); ?> out of 5">
                        <?php for($star = 1; $star <= 5; $star++): ?>
                            <i class="fa-<?php echo e($averageRating >= $star ? 'solid' : 'regular'); ?> fa-star"></i>
                        <?php endfor; ?>
            </div>
            <div class="market-product-card__rating-value">
                <strong><?php echo e($averageRating > 0 ? number_format($averageRating, 1) : '0.0'); ?></strong>                           
            </div>
      </div>

        <?php if(filled($resolvedSubtitle)): ?>
            <div class="market-product-card__seller-line">
                <span class="market-product-card__subtitle" title="<?php echo e($resolvedSubtitle); ?>">
                    <i class="fa-solid fa-store"></i>&nbsp;<?php echo e($resolvedSubtitle); ?>

                </span>
                <?php if (isset($component)) { $__componentOriginalfe2859e26b7d777b13c0d03a650c7378 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.seller-trust-badge','data' => ['seller' => $sellerProfile,'compact' => true,'iconOnly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('seller-trust-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['seller' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sellerProfile),'compact' => true,'icon-only' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $attributes = $__attributesOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__attributesOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378)): ?>
<?php $component = $__componentOriginalfe2859e26b7d777b13c0d03a650c7378; ?>
<?php unset($__componentOriginalfe2859e26b7d777b13c0d03a650c7378); ?>
<?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($meta)): ?>
            <div class="market-product-card__meta"><?php echo e($meta); ?></div>
        <?php endif; ?>

        <div class="market-product-card__price">
            <span class="market-product-card__currency">&#8369;</span>
            <?php echo e(number_format($product->price, 2)); ?>

        </div>
    </div>
</a>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/components/product-card.blade.php ENDPATH**/ ?>