<div class="product-gallery">
    <div class="product-visual" data-product-gallery
        data-product-name="<?php echo e(e($product->name)); ?>"
        data-product-gallery-items='<?php echo json_encode($productPage->galleryMedia->values(), 15, 512) ?>'>
        <button type="button" class="product-media-arrow product-media-arrow--prev" data-product-gallery-prev aria-label="Previous media">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="product-media-stage" data-product-gallery-viewport>
            <?php if($productPage->initialMedia): ?>
                <div class="product-media-slide is-active" data-product-gallery-slide>
                    <?php if(($productPage->initialMedia['type'] ?? 'image') === 'video'): ?>
                        <div class="product-media-video-shell" data-product-media-shell>
                            <video src="<?php echo e($productPage->initialMedia['url']); ?>" preload="metadata" playsinline class="product-media-content" data-product-media-video></video>
                            <button type="button" class="product-media-play-button" data-product-media-play aria-label="Play video">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    <?php else: ?>
                        <img src="<?php echo e($productPage->initialMedia['url']); ?>" alt="<?php echo e($product->name); ?>" loading="eager">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="button" class="product-media-arrow product-media-arrow--next" data-product-gallery-next aria-label="Next media">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <span class="product-media-counter" data-product-gallery-counter>
            <?php echo e($productPage->galleryMedia->count() > 0 ? '1 / ' . $productPage->galleryMedia->count() : '1 / 1'); ?>

        </span>
    </div>
</div>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/products/partials/show/gallery.blade.php ENDPATH**/ ?>