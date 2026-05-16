<div class="product-gallery">
    <div class="product-visual" data-product-gallery
        data-product-name="{{ e($product->name) }}"
        data-product-gallery-items='@json($productPage->galleryMedia->values())'>
        <button type="button" class="product-media-arrow product-media-arrow--prev" data-product-gallery-prev aria-label="Previous media">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="product-media-stage" data-product-gallery-viewport>
            @if($productPage->initialMedia)
                <div class="product-media-slide is-active" data-product-gallery-slide>
                    @if(($productPage->initialMedia['type'] ?? 'image') === 'video')
                        <div class="product-media-video-shell" data-product-media-shell>
                            <video src="{{ $productPage->initialMedia['url'] }}" preload="metadata" playsinline class="product-media-content" data-product-media-video></video>
                            <button type="button" class="product-media-play-button" data-product-media-play aria-label="Play video">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        </div>
                    @else
                        <img src="{{ $productPage->initialMedia['url'] }}" alt="{{ $product->name }}" loading="eager">
                    @endif
                </div>
            @endif
        </div>

        <button type="button" class="product-media-arrow product-media-arrow--next" data-product-gallery-next aria-label="Next media">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <span class="product-media-counter" data-product-gallery-counter>
            {{ $productPage->galleryMedia->count() > 0 ? '1 / ' . $productPage->galleryMedia->count() : '1 / 1' }}
        </span>
    </div>
</div>
