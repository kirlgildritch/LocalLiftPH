@extends('layouts.app')
@section('title', 'LocalLift PH - Product')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/product_details.css') }}">
@php
$ownsProduct = auth()->check() && (int) $product->user_id === (int) auth()->id();
$averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
$canReportProduct = auth('web')->check() && !$ownsProduct;
$galleryMedia = $product->gallery_media ?? collect();
$isWishlisted = (bool) ($isWishlisted ?? false);
$activeVariants = $product->variants->where('is_active', true)->values();
$hasVariants = $activeVariants->isNotEmpty();
$displayStock = $hasVariants ? (int) $activeVariants->sum('stock') : (int) $product->stock;
$displayOriginalPrice = $hasVariants ? (float) $activeVariants->min('price') : (float) $product->price;
$displayPrice = $hasVariants
    ? (float) $activeVariants->map(fn($variant) => $product->discountedPrice((float) $variant->price))->min()
    : $product->discountedPrice($displayOriginalPrice);
$hasDiscount = $product->hasActiveDiscount() && $displayPrice < $displayOriginalPrice;
$productReviewsToggleUrl = $showAllReviews
    ? route('products.show', $product) . '#product-reviews'
    : route('products.show', array_merge(request()->query(), ['product' => $product->getRouteKey(), 'show_reviews' => 'all'])) . '#product-reviews';
$canReviewProduct = auth()->check() && auth()->user()->isBuyer() && $reviewableOrderItems->isNotEmpty();
$selectedReviewableOrderItem = $canReviewProduct
    ? ($reviewableOrderItems->firstWhere('id', (int) request('review_order_item')) ?? $reviewableOrderItems->first())
    : null;
$reviewMediaMaxFiles = \App\Support\ReviewUploadLimit::maxFiles();
$reviewMediaEffectiveFileBytes = \App\Support\ReviewUploadLimit::effectiveSingleFileBytes()
    ?? \App\Support\ReviewUploadLimit::appMaxFileBytes();
$reviewMediaRequestBytes = \App\Support\ReviewUploadLimit::effectiveRequestBytes();
$reviewMediaEffectiveFileLabel = \App\Support\ReviewUploadLimit::humanSize($reviewMediaEffectiveFileBytes);
$reviewMediaRequestLabel = \App\Support\ReviewUploadLimit::humanSize($reviewMediaRequestBytes);
@endphp
<!-- --------------------------------------------- -->
@if(session('error'))
<div style="color:red;">{{ session('error') }}</div>
@endif

@if($errors->any())
<div style="color:red;">
    @foreach($errors->all() as $error)
    <p>{{ $error }}</p>
    @endforeach
</div>
@endif
<!-- --------------------------- -->

<section class="product-detail-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <a href="{{ route('products.index') }}">Products</a>
            <span>&gt;</span>
            <span>{{ $product->name }}</span>
        </div>

        <div class="product-detail-layout">
            <div class="product-main panel">
                <div class="product-gallery">
                    <div class="product-visual" data-product-gallery
                        data-product-name="{{ e($product->name) }}"
                        data-product-gallery-items='@json($galleryMedia->values())'>
                        <button type="button" class="product-media-arrow product-media-arrow--prev" data-product-gallery-prev aria-label="Previous media">
                            <i class="fa-solid fa-chevron-left" title="Report Product"></i>
                        </button>

                        <div class="product-media-stage" data-product-gallery-viewport>
                            @php
                                $initialMedia = $galleryMedia->first();
                            @endphp
                            @if($initialMedia)
                                <div class="product-media-slide is-active" data-product-gallery-slide>
                                    @if(($initialMedia['type'] ?? 'image') === 'video')
                                        <div class="product-media-video-shell" data-product-media-shell>
                                            <video src="{{ $initialMedia['url'] }}" preload="metadata" playsinline class="product-media-content" data-product-media-video></video>
                                            <button type="button" class="product-media-play-button" data-product-media-play aria-label="Play video">
                                                <i class="fa-solid fa-play"></i>
                                            </button>
                                        </div>
                                    @else
                                        <img src="{{ $initialMedia['url'] }}" alt="{{ $product->name }}" loading="eager">
                                    @endif
                                </div>
                            @endif
                        </div>

                        <button type="button" class="product-media-arrow product-media-arrow--next" data-product-gallery-next aria-label="Next media">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>

                        <span class="product-media-counter" data-product-gallery-counter>
                            {{ $galleryMedia->count() > 0 ? '1 / ' . $galleryMedia->count() : '1 / 1' }}
                        </span>
                    </div>
                </div>

                <div class="product-copy">
                    <div class="product-copy-top">
                        <span class="section-kicker">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                        @if($canReportProduct)
                        @include('partials.report-modal', [
                        'modalId' => 'report-product-modal',
                        'modalContext' => 'product',
                        'triggerLabel' => 'Report product',
                        'productId' => $product->id,
                        'sellerId' => $product->user_id,
                        ])
                        @elseif(!auth('seller')->check() && !auth('admin')->check())
                        <a href="{{ route('login') }}" class="report-trigger-button" aria-label="Log in to report product" title="Log in to report product">
                            <i class="fa-solid fa-flag"></i>
                        </a>
                        @endif
                    </div>
                    <h1>{{ $product->name }}</h1>


                    <div class="product-meta">
                        <span><i class="fa-solid fa-store"></i>
                            {{ $product->user->sellerProfile?->store_name ?? 'LocalLift Seller' }}
                            <x-seller-trust-badge :seller="$product->user->sellerProfile" compact icon-only />
                        </span>
                        <span><i class="fa-solid fa-box-open"></i>
                            {{ $displayStock > 0 ? 'Ready to ship' : 'Out of stock' }}</span>
                        <span><i class="fa-solid fa-cubes"></i> Stock: {{ $displayStock }}</span>
                        <span><i class="fa-solid fa-star"></i>
                            {{ $averageRating > 0 ? number_format($averageRating, 1) : 'New' }} |
                            {{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                    </div>

                    <div class="product-price" data-product-display-price>
                        @if($hasDiscount)
                            <span class="product-price__original">&#8369; {{ number_format($displayOriginalPrice, 2) }}</span>
                        @endif
                        @if($hasVariants)
                            <span class="product-price__sale">Starts at &#8369; {{ number_format($displayPrice, 2) }}</span>
                        @else
                            <span class="product-price__sale">&#8369; {{ number_format($displayPrice, 2) }}</span>
                        @endif
                        @if($hasDiscount)
                            <span class="product-price__badge">{{ $product->discountLabel() }}</span>
                        @endif
                    </div>


                    <div class="product-feature-grid">
                        <div class="feature-card">
                            <strong>Category</strong>
                            <span>{{ $product->category?->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="feature-card">
                            <strong>Availability</strong>
                            <span>{{ $displayStock > 0 ? 'In stock' : 'Currently unavailable' }}</span>
                        </div>

                    </div>

                    @if($hasVariants)
                        @php($variantPreviewLimit = 4)
                        <div class="purchase-variants product-variants-panel" data-purchase-variants>
                            <div class="product-variants-panel__head">
                                <span>Options</span>
                                <small>Choose one before adding to cart.</small>
                            </div>
                            <div class="variant-choice-grid variant-choice-grid--preview">
                                @foreach($activeVariants->take($variantPreviewLimit) as $variant)
                                    <button type="button"
                                        class="variant-choice"
                                        data-variant-choice
                                        data-variant-id="{{ $variant->id }}"
                                        data-variant-price="{{ $product->discountedPrice((float) $variant->price) }}"
                                        data-variant-original-price="{{ (float) $variant->price }}"
                                        data-variant-stock="{{ (int) $variant->stock }}"
                                        {{ (int) $variant->stock <= 0 ? 'disabled' : '' }}>
                                        <strong>{{ $variant->displayName() }}</strong>
                                        <small>
                                            @if($product->hasActiveDiscount() && $product->discountedPrice((float) $variant->price) < (float) $variant->price)
                                                <span class="variant-price-original">&#8369; {{ number_format($variant->price, 2) }}</span>
                                            @endif
                                            &#8369; {{ number_format($product->discountedPrice((float) $variant->price), 2) }} | {{ (int) $variant->stock }} left
                                        </small>
                                    </button>
                                @endforeach

                                @if($activeVariants->count() > $variantPreviewLimit)
                                    <button type="button" class="variant-choice variant-choice--more" data-open-variant-modal>
                                        <strong>View more options</strong>
                                        <small>{{ $activeVariants->count() - $variantPreviewLimit }} more available</small>
                                    </button>
                                @endif
                            </div>
                            <small class="quantity-note" data-variant-note>Select a variant before adding to cart.</small>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="purchase-sidebar">
                <div class="panel purchase-card">
                    <span class="section-kicker">Purchase</span>
                    <h2>Order summary</h2>

                    <div class="quantity-box" data-purchase-quantity-box data-max-stock="{{ max(0, $hasVariants ? 0 : $displayStock) }}">
                        <span>Quantity</span>
                        <div class="quantity-control">
                            <button type="button" data-quantity-decrement aria-label="Decrease quantity">-</button>
                            <input type="text" value="{{ $hasVariants ? 0 : ($displayStock > 0 ? 1 : 0) }}" readonly data-quantity-display>
                            <button type="button" data-quantity-increment aria-label="Increase quantity">+</button>
                        </div>
                        <small class="quantity-note" data-quantity-note hidden></small>
                    </div>

                    <div class="purchase-meta">
                        <div>
                            <span>Price</span>
                            <strong data-purchase-total>&#8369; {{ number_format((! $hasVariants && $displayStock > 0) ? $displayPrice : 0, 2) }}</strong>
                        </div>
                        <div>
                            <span>Delivery</span>
                            <strong>Nationwide ready</strong>
                        </div>
                    </div>

                    <div class="purchase-actions">
                        @auth
                        @if($ownsProduct)
                            <span class="action-btn secondary-btn" aria-disabled="true">This is your product</span>
                        @else
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ $hasVariants ? 0 : ($displayStock > 0 ? 1 : 0) }}" data-purchase-quantity>
                                <input type="hidden" name="product_variant_id" value="" data-purchase-variant-input>
                                <button type="submit" class="action-btn primary-btn" data-purchase-submit {{ $hasVariants ? 'disabled' : '' }}><i class="fa-solid fa-cart-shopping"></i></button>
                            </form>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="quantity" value="{{ $hasVariants ? 0 : ($displayStock > 0 ? 1 : 0) }}" data-purchase-quantity>
                                <input type="hidden" name="product_variant_id" value="" data-purchase-variant-input>
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="action-btn secondary-btn" data-purchase-submit {{ $hasVariants ? 'disabled' : '' }}>Buy Now</button>
                            </form>
                        @endif

                        @else
                        <a href="{{ route('login') }}" class="action-btn primary-btn"><i class="fa-solid fa-cart-shopping"></i></a>
                        <a href="{{ route('login') }}" class="action-btn secondary-btn">Buy Now</a>
                        @endauth

                        @auth('web')
                            @if(!$ownsProduct)
                                <form action="{{ $isWishlisted ? route('buyer.wishlist.destroy', $product) : route('buyer.wishlist.store', $product) }}" method="POST" class="wishlist-toggle-form">
                                    @csrf
                                    @if($isWishlisted)
                                        @method('DELETE')
                                    @endif
                                    <button type="submit"
                                        class="icon-btn wishlist-toggle-btn {{ $isWishlisted ? 'is-active' : '' }}"
                                        aria-label="{{ $isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}"
                                        title="{{ $isWishlisted ? 'Remove from wishlist' : 'Add to wishlist' }}">
                                        <i class="fa-{{ $isWishlisted ? 'solid' : 'regular' }} fa-heart"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="icon-btn wishlist-toggle-btn" aria-label="Wishlist unavailable for your own product" disabled>
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="icon-btn wishlist-toggle-btn" aria-label="Log in to add wishlist" title="Log in to add wishlist">
                                <i class="fa-regular fa-heart"></i>
                            </a>
                        @endauth
                    </div>

                    <a href="{{ route('shops.show', $product->user->id) }}"
                        class="action-btn secondary-btn full-btn">View Shop</a>

                    @auth
                    @if(!$ownsProduct)
                    <form action="{{ route('messages.start', $product->user) }}" method="POST" data-chat-start-form>
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="action-btn secondary-btn full-btn">Message Seller</button>
                    </form>
                    @else
                    <span class="action-btn secondary-btn full-btn" aria-disabled="true">This is your product</span>
                    @endif
                    @else
                    <a href="{{ route('login') }}" class="action-btn secondary-btn full-btn">Message Seller</a>
                    @endauth
                </div>


            </aside>
        </div>

        @if($hasVariants && $activeVariants->count() > 4)
            <div class="variant-modal-shell" data-variant-modal hidden aria-hidden="true">
                <div class="variant-modal-backdrop" data-close-variant-modal></div>
                <div class="variant-modal-card" role="dialog" aria-modal="true" aria-labelledby="variant-modal-title">
                    <div class="variant-modal-header">
                        <div>
                            <span class="section-kicker">Options</span>
                            <h3 id="variant-modal-title">Choose product option</h3>
                            <p>Select the exact variant you want to add to cart.</p>
                        </div>
                        <button type="button" class="variant-modal-close" data-close-variant-modal aria-label="Close options">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="variant-choice-grid variant-choice-grid--modal">
                        @foreach($activeVariants as $variant)
                            <button type="button"
                                class="variant-choice"
                                data-variant-choice
                                data-variant-id="{{ $variant->id }}"
                                data-variant-price="{{ $product->discountedPrice((float) $variant->price) }}"
                                data-variant-original-price="{{ (float) $variant->price }}"
                                data-variant-stock="{{ (int) $variant->stock }}"
                                {{ (int) $variant->stock <= 0 ? 'disabled' : '' }}>
                                <strong>{{ $variant->displayName() }}</strong>
                                <small>
                                    @if($product->hasActiveDiscount() && $product->discountedPrice((float) $variant->price) < (float) $variant->price)
                                        <span class="variant-price-original">&#8369; {{ number_format($variant->price, 2) }}</span>
                                    @endif
                                    &#8369; {{ number_format($product->discountedPrice((float) $variant->price), 2) }} | {{ (int) $variant->stock }} left
                                </small>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="detail-sections">
            <div class="panel detail-card">
                <div class="detail-header">
                    <span class="section-kicker">
                        Product Descriptions
                    </span>

                </div>
                <p class="product-description">
                    {!! $product->description ?: 'No description available for this product yet.' !!}
                </p>

            </div>
        </div>
        <div class="detail-sections">
            <section class="panel detail-card review-section" id="product-reviews">
                <!-- <div class="detail-header">
                    <div>
                        <span class="section-kicker">Ratings & Reviews</span>
                    </div> -->
                <div class="review-section-head">
                    <span class="section-kicker">Ratings & Reviews</span>

                    <div class="review-summary-chip">
                        <strong data-review-average>{{ $averageRating > 0 ? number_format($averageRating, 1) : '0.0' }}</strong>
                        <span data-review-count>{{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                    </div>
                </div>

                <div class="review-toolbar">
                    @if($canReviewProduct)
                    <a href="#buyer-review-form" class="review-write-chip" data-review-write-chip>
                        <i class="fa-solid fa-pen"></i>
                        Write a review
                    </a>
                    @endif
                </div>

                @if($product->reviews_count > $initialReviewsLimit)
                    <div class="review-toggle-bar">
                        <a href="{{ $productReviewsToggleUrl }}" class="action-btn secondary-btn review-toggle-btn">
                            {{ $showAllReviews ? 'Show Fewer Reviews' : 'View All Reviews' }}
                        </a>
                    </div>
                @endif

                @if($canReviewProduct)
                <form action="{{ route('products.reviews.store', $product) }}" method="POST" enctype="multipart/form-data" class="review-form panel" id="buyer-review-form"
                    data-review-max-files="{{ $reviewMediaMaxFiles }}"
                    data-review-max-file-bytes="{{ $reviewMediaEffectiveFileBytes }}"
                    data-review-max-total-bytes="{{ $reviewMediaRequestBytes ?? 0 }}"
                    data-review-max-file-label="{{ $reviewMediaEffectiveFileLabel }}"
                    data-review-max-total-label="{{ $reviewMediaRequestLabel }}">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $selectedReviewableOrderItem?->id }}">

                    <div class="review-form-header">
                        <div>
                            <strong>Leave a review</strong>
                            <p>Only buyers with completed purchases can rate this product.</p>
                        </div>

                        @if($reviewableOrderItems->count() > 1)
                        <span class="review-order-note" data-review-order-note>{{ $reviewableOrderItems->count() }} completed purchases
                            eligible</span>
                        @endif
                    </div>

                    <div class="review-form-grid">
                        <div class="review-form-field">
                            <label for="rating">Your rating</label>
                            <select name="rating" id="rating" required>
                                <option value="">Select rating</option>
                                @for($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}" {{ (int) old('rating') === $rating ? 'selected' : '' }}>
                                    {{ $rating }} Star{{ $rating !== 1 ? 's' : '' }}
                                </option>
                                @endfor
                            </select>
                        </div>

                        <div class="review-form-field review-form-field-full">
                            <label for="comment">Your review</label>
                            <textarea name="comment" id="comment" rows="4"
                                placeholder="Share what you liked about this product...">{{ old('comment') }}</textarea>
                        </div>

                        <div class="review-form-field review-form-field-full review-upload-section">
                            <div class="review-upload-header">
                                <label>Upload media</label>
                                <span data-review-upload-status>Up to {{ $reviewMediaMaxFiles }} files, {{ $reviewMediaEffectiveFileLabel }} each, {{ $reviewMediaRequestLabel }} total per upload.</span>
                            </div>

                            <div class="review-upload-inputs">
                                <div class="review-upload-input">
                                    <label for="review_media">Upload photos or videos</label>
                                    <input type="file" name="review_media[]" id="review_media" accept="image/*,video/*" multiple data-review-preview-input>
                                </div>
                            </div>

                            <div class="review-upload-preview" data-review-preview-grid hidden></div>
                        </div>
                    </div>

                    <button type="submit" class="action-btn primary-btn review-submit-btn">Submit Review</button>
                </form>

                @endif

                <div class="review-list" data-review-list>
                    @forelse($reviews as $review)
                        @include('products.partials.review-card', ['review' => $review])
                    @empty

                    <div class="review-empty-state" data-review-empty-state>
                        <h3>No reviews yet</h3>
                        <p>This product has not received buyer feedback yet.</p>
                    </div>
                    @endforelse
                </div>
            </section>

            <section class="panel detail-card">
                <div class="detail-header">
                    <div>
                        <span class="section-kicker">Related Products</span>
                        <h2>You may also like</h2>
                    </div>
                </div>

                <div class="related-grid product-card-grid" data-skeleton-group data-skeleton-delay="420">
                    @forelse($relatedProducts as $relatedProduct)
                    <x-product-card :product="$relatedProduct" />
                    @empty
                    <p>No related products available.</p>
                    @endforelse
                </div>
            </section>

            @if(($recentlyViewedProducts ?? collect())->isNotEmpty())
                <section class="panel detail-card">
                    <div class="detail-header">
                        <div>
                            <span class="section-kicker">Recently Viewed</span>
                            <h2>Viewed by you</h2>
                        </div>
                    </div>

                    <div class="related-grid product-card-grid" data-skeleton-group data-skeleton-delay="420">
                        @foreach($recentlyViewedProducts as $recentProduct)
                            <x-product-card :product="$recentProduct" />
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</section>

<div class="review-lightbox" data-review-lightbox hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Review media preview">
    <button type="button" class="review-lightbox-close" data-review-lightbox-close aria-label="Close preview">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="review-lightbox-dialog" data-review-lightbox-dialog></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const gallery = document.querySelector('[data-product-gallery]');

        if (!gallery) {
            return;
        }

        const viewport = gallery.querySelector('[data-product-gallery-viewport]');
        const prevButton = gallery.querySelector('[data-product-gallery-prev]');
        const nextButton = gallery.querySelector('[data-product-gallery-next]');
        const counter = gallery.querySelector('[data-product-gallery-counter]');
        const galleryName = gallery.dataset.productName || 'Product media';

        let mediaItems = [];

        try {
            mediaItems = JSON.parse(gallery.dataset.productGalleryItems || '[]');
        } catch (error) {
            mediaItems = [];
        }

        if (!viewport || !mediaItems.length) {
            return;
        }

        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        let currentIndex = 0;
        let animationTimer = null;
        let isAnimating = false;

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const setButtonsDisabled = () => {
            const disabled = mediaItems.length < 2 || isAnimating;

            if (prevButton) {
                prevButton.disabled = disabled;
            }

            if (nextButton) {
                nextButton.disabled = disabled;
            }
        };

        const updateCounter = () => {
            if (counter) {
                counter.textContent = `${currentIndex + 1} / ${mediaItems.length}`;
            }
        };

        const buildSlide = (item, index) => {
            const slide = document.createElement('div');
            slide.className = 'product-media-slide is-entering';
            slide.dataset.productGallerySlide = '1';

            if (item.type === 'video') {
                slide.innerHTML = `
                    <div class="product-media-video-shell" data-product-media-shell>
                        <video src="${escapeHtml(item.url)}" preload="metadata" playsinline class="product-media-content" data-product-media-video></video>
                        <button type="button" class="product-media-play-button" data-product-media-play aria-label="Play video">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                `;
            } else {
                slide.innerHTML = `
                    <img src="${escapeHtml(item.url)}" alt="${escapeHtml(galleryName)}" loading="${index === 0 ? 'eager' : 'lazy'}" class="product-media-content">
                `;
            }

            return slide;
        };

        const setupVideoSlide = (slide) => {
            const shell = slide?.querySelector('[data-product-media-shell]');
            const video = slide?.querySelector('[data-product-media-video]');
            const playButton = slide?.querySelector('[data-product-media-play]');

            if (!shell || !video || !playButton) {
                return;
            }

            video.controls = false;
            shell.classList.toggle('is-playing', !video.paused && !video.ended);

            const syncState = () => {
                shell.classList.toggle('is-playing', !video.paused && !video.ended);
            };

            if (!video.dataset.galleryVideoBound) {
                video.dataset.galleryVideoBound = '1';

                playButton.addEventListener('click', async function () {
                    try {
                        await video.play();
                    } catch (error) {
                        // Ignore autoplay restrictions; the user can tap again.
                    }
                });

                video.addEventListener('play', syncState);
                video.addEventListener('pause', syncState);
                video.addEventListener('ended', syncState);
            }

            syncState();
        };

        const swapSlide = (nextIndex, direction) => {
            if (isAnimating || nextIndex === currentIndex || !mediaItems[nextIndex]) {
                return;
            }

            const currentSlide = viewport.querySelector('[data-product-gallery-slide].is-active');
            const currentVideo = currentSlide?.querySelector('video');

            if (currentVideo) {
                currentVideo.pause();
            }

            const slide = buildSlide(mediaItems[nextIndex], nextIndex);

            if (currentSlide) {
                currentSlide.classList.remove('is-active');
                currentSlide.classList.add('is-leaving');
                currentSlide.classList.add(direction === 'next' ? 'from-left' : 'from-right');
            }

            viewport.appendChild(slide);
            setupVideoSlide(slide);
            isAnimating = !prefersReducedMotion;
            setButtonsDisabled();

            requestAnimationFrame(function () {
                slide.classList.add('is-active');
            });

            animationTimer = window.setTimeout(function () {
                currentSlide?.remove();
                slide.classList.remove('is-entering');
                currentIndex = nextIndex;
                updateCounter();
                isAnimating = false;
                setButtonsDisabled();
            }, prefersReducedMotion ? 0 : 280);
        };

        const go = (delta) => {
            if (mediaItems.length < 2) {
                return;
            }

            const nextIndex = (currentIndex + delta + mediaItems.length) % mediaItems.length;
            swapSlide(nextIndex, delta > 0 ? 'next' : 'prev');
        };

        if (prevButton) {
            prevButton.addEventListener('click', function () {
                go(-1);
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', function () {
                go(1);
            });
        }

        viewport.querySelectorAll('[data-product-media-shell]').forEach((slide) => {
            setupVideoSlide(slide.closest('[data-product-gallery-slide]'));
        });

        setButtonsDisabled();
        updateCounter();

        window.addEventListener('beforeunload', function () {
            window.clearTimeout(animationTimer);
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('buyer-review-form');

        if (!form || !window.DataTransfer) {
            return;
        }

        const inputs = Array.from(form.querySelectorAll('[data-review-preview-input]'));
        const previewGrid = form.querySelector('[data-review-preview-grid]');
        const uploadStatus = form.querySelector('[data-review-upload-status]');
        const submitButton = form.querySelector('.review-submit-btn');
        const selectedFiles = new Map();
        const objectUrls = new Map();
        const maxFiles = Math.max(1, Number(form.dataset.reviewMaxFiles || 5));
        const maxFileBytes = Math.max(0, Number(form.dataset.reviewMaxFileBytes || 0));
        const maxTotalBytes = Math.max(0, Number(form.dataset.reviewMaxTotalBytes || 0));
        const maxFileLabel = form.dataset.reviewMaxFileLabel || '';
        const maxTotalLabel = form.dataset.reviewMaxTotalLabel || '';
        const maxImageDimension = 1600;
        const imageQuality = 0.82;
        const targetVideoBitrate = 900000;
        const targetAudioBitrate = 96000;

        const setUploadStatus = function (message) {
            if (uploadStatus) {
                uploadStatus.textContent = message;
            }
        };

        const setSubmitIdle = function () {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit Review';
            }
        };

        const setSubmitBusy = function (message) {
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = message;
            }
        };

        const bytesToSize = function (bytes) {
            if (bytes < 1024 * 1024) {
                return Math.max(1, Math.round(bytes / 1024)) + ' KB';
            }

            return (bytes / 1024 / 1024).toFixed(1) + ' MB';
        };

        const totalSelectedBytes = function () {
            return inputs.reduce(function (total, input) {
                return total + (selectedFiles.get(input) || []).reduce(function (size, file) {
                    return size + (file.size || 0);
                }, 0);
            }, 0);
        };

        const clearSelectedFiles = function () {
            selectedFiles.clear();
            inputs.forEach(function (input) {
                selectedFiles.set(input, []);
                syncInputFiles(input);
            });
            renderPreviews();
        };

        const batchTotalBytes = function (files) {
            return files.reduce(function (total, file) {
                return total + (file.size || 0);
            }, 0);
        };

        const compressedFileName = function (file, mimeType) {
            const extension = mimeType === 'image/webp' ? 'webp' : 'jpg';
            return file.name.replace(/\.[^.]+$/, '') + '.' + extension;
        };

        const loadImage = function (file) {
            return new Promise(function (resolve, reject) {
                const url = URL.createObjectURL(file);
                const image = new Image();

                image.onload = function () {
                    URL.revokeObjectURL(url);
                    resolve(image);
                };

                image.onerror = function () {
                    URL.revokeObjectURL(url);
                    reject(new Error('Unable to read selected image.'));
                };

                image.src = url;
            });
        };

        const compressImage = async function (file) {
            if (!file.type.startsWith('image/') || file.type === 'image/gif') {
                return file;
            }

            if (maxFileBytes > 0 && file.size <= maxFileBytes) {
                return file;
            }

            try {
                const image = await loadImage(file);
                const scale = Math.min(maxImageDimension / image.width, maxImageDimension / image.height, 1);
                const width = Math.max(1, Math.round(image.width * scale));
                const height = Math.max(1, Math.round(image.height * scale));
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                if (!context) {
                    return file;
                }

                canvas.width = width;
                canvas.height = height;
                context.drawImage(image, 0, 0, width, height);

                const outputType = file.type === 'image/png' || file.type === 'image/webp'
                    ? 'image/webp'
                    : 'image/jpeg';

                const blob = await new Promise(function (resolve) {
                    canvas.toBlob(resolve, outputType, imageQuality);
                });

                if (!blob || blob.size >= file.size) {
                    return file;
                }

                return new File([blob], compressedFileName(file, outputType), {
                    type: outputType,
                    lastModified: Date.now(),
                });
            } catch (error) {
                return file;
            }
        };

        const getCompressedVideoName = function (file, mimeType) {
            const extension = mimeType.includes('webm') ? 'webm' : 'mp4';
            const baseName = file.name.replace(/\.[^.]+$/, '') || 'review-video';

            return baseName + '.' + extension;
        };

        const getSupportedVideoMimeType = function () {
            if (!window.MediaRecorder || typeof MediaRecorder.isTypeSupported !== 'function') {
                return null;
            }

            const candidates = [
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=vp8,opus',
                'video/webm',
            ];

            return candidates.find(function (candidate) {
                return MediaRecorder.isTypeSupported(candidate);
            }) || null;
        };

        const compressVideo = async function (file) {
            if (!file.type.startsWith('video/')) {
                return file;
            }

            if (maxFileBytes > 0 && file.size <= maxFileBytes) {
                return file;
            }

            const mimeType = getSupportedVideoMimeType();
            const canCapture = typeof HTMLVideoElement !== 'undefined'
                && (HTMLVideoElement.prototype.captureStream || HTMLVideoElement.prototype.mozCaptureStream);

            if (!mimeType || !canCapture) {
                return file;
            }

            const objectUrl = URL.createObjectURL(file);
            const video = document.createElement('video');
            video.src = objectUrl;
            video.preload = 'metadata';
            video.muted = true;
            video.playsInline = true;
            video.crossOrigin = 'anonymous';

            const cleanup = function () {
                URL.revokeObjectURL(objectUrl);
                video.pause();
                video.removeAttribute('src');
                video.load();
            };

            try {
                await new Promise(function (resolve, reject) {
                    video.onloadedmetadata = function () {
                        resolve();
                    };

                    video.onerror = function () {
                        reject(new Error('Unable to read selected video.'));
                    };
                });

                const stream = video.captureStream ? video.captureStream() : video.mozCaptureStream();

                if (!stream) {
                    cleanup();
                    return file;
                }

                const chunks = [];
                const compressedBlob = await new Promise(async function (resolve, reject) {
                    let resolved = false;
                    const recorder = new MediaRecorder(stream, {
                        mimeType: mimeType,
                        videoBitsPerSecond: targetVideoBitrate,
                        audioBitsPerSecond: targetAudioBitrate,
                    });

                    const finish = function (value, isError) {
                        if (resolved) {
                            return;
                        }

                        resolved = true;

                        if (recorder.state !== 'inactive') {
                            recorder.stop();
                        }

                        stream.getTracks().forEach(function (track) {
                            track.stop();
                        });

                        cleanup();

                        if (isError) {
                            reject(value);
                            return;
                        }

                        resolve(value);
                    };

                    recorder.ondataavailable = function (event) {
                        if (event.data && event.data.size > 0) {
                            chunks.push(event.data);
                        }
                    };

                    recorder.onerror = function () {
                        finish(new Error('Unable to compress selected video.'), true);
                    };

                    recorder.onstop = function () {
                        if (!resolved) {
                            finish(new Blob(chunks, { type: mimeType.split(';')[0] || 'video/webm' }), false);
                        }
                    };

                    video.onended = function () {
                        if (recorder.state !== 'inactive') {
                            recorder.stop();
                        }
                    };

                    try {
                        recorder.start(250);
                        await video.play();
                    } catch (error) {
                        finish(error, true);
                    }
                });

                if (!(compressedBlob instanceof Blob) || compressedBlob.size === 0 || compressedBlob.size >= file.size) {
                    return file;
                }

                return new File([compressedBlob], getCompressedVideoName(file, compressedBlob.type || mimeType), {
                    type: compressedBlob.type || mimeType.split(';')[0] || 'video/webm',
                    lastModified: Date.now(),
                });
            } catch (error) {
                cleanup();
                return file;
            }
        };

        const prepareFiles = async function (files) {
            const preparedFiles = [];

            for (const file of files) {
                if (file.type.startsWith('video/')) {
                    preparedFiles.push(await compressVideo(file));
                    continue;
                }

                preparedFiles.push(await compressImage(file));
            }

            return preparedFiles;
        };

        const syncInputFiles = function (input) {
            const transfer = new DataTransfer();
            const files = selectedFiles.get(input) || [];

            files.forEach(function (file) {
                transfer.items.add(file);
            });

            input.files = transfer.files;
        };

        const revokePreviewUrls = function () {
            objectUrls.forEach(function (url) {
                URL.revokeObjectURL(url);
            });
            objectUrls.clear();
        };

        const renderPreviews = function () {
            if (!previewGrid) {
                return;
            }

            revokePreviewUrls();
            previewGrid.innerHTML = '';

            const items = inputs.flatMap(function (input) {
                return (selectedFiles.get(input) || []).map(function (file, index) {
                    return { input: input, file: file, index: index };
                });
            });

            previewGrid.hidden = items.length === 0;

            items.forEach(function (item) {
                const previewUrl = URL.createObjectURL(item.file);
                objectUrls.set(item.input.id + '-' + item.index + '-' + item.file.name, previewUrl);

                const card = document.createElement('div');
                card.className = 'review-upload-preview-card';

                const mediaWrap = document.createElement('div');
                mediaWrap.className = 'review-upload-preview-media';

                if (item.file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = previewUrl;
                    video.controls = true;
                    video.muted = true;
                    video.preload = 'metadata';
                    mediaWrap.appendChild(video);
                } else {
                    const image = document.createElement('img');
                    image.src = previewUrl;
                    image.alt = item.file.name;
                    mediaWrap.appendChild(image);
                }

                const meta = document.createElement('div');
                meta.className = 'review-upload-preview-meta';
                meta.textContent = item.file.name + ' (' + bytesToSize(item.file.size) + ')';

                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'review-upload-remove';
                removeButton.setAttribute('aria-label', 'Remove ' + item.file.name);
                removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                removeButton.addEventListener('click', function () {
                    const files = selectedFiles.get(item.input) || [];
                    files.splice(item.index, 1);
                    selectedFiles.set(item.input, files);
                    syncInputFiles(item.input);
                    renderPreviews();
                });

                card.appendChild(mediaWrap);
                card.appendChild(meta);
                card.appendChild(removeButton);
                previewGrid.appendChild(card);
            });
        };

        const firstErrorMessage = function (payload) {
            if (!payload || typeof payload !== 'object') {
                return 'Unable to submit your review right now.';
            }

            if (payload.message) {
                return payload.message;
            }

            const errors = payload.errors || {};
            const firstKey = Object.keys(errors)[0];

            if (firstKey && Array.isArray(errors[firstKey]) && errors[firstKey][0]) {
                return errors[firstKey][0];
            }

            return 'Unable to submit your review right now.';
        };

        inputs.forEach(function (input) {
            selectedFiles.set(input, []);

            input.addEventListener('change', async function () {
                const currentFiles = selectedFiles.get(input) || [];
                const pickedFiles = Array.from(input.files || []);
                const totalSelected = inputs.reduce(function (total, previewInput) {
                    return total + (selectedFiles.get(previewInput) || []).length;
                }, 0);
                const remainingSlots = Math.max(maxFiles - totalSelected, 0);
                const selectedBatch = pickedFiles.slice(0, remainingSlots);

                if (pickedFiles.length === 0 || remainingSlots === 0) {
                    syncInputFiles(input);
                    return;
                }

                const baseTotalBytes = totalSelectedBytes();
                const rawBatchTotalBytes = batchTotalBytes(selectedBatch);
                const batchNeedsOptimization = selectedBatch.some(function (file) {
                    return maxFileBytes > 0 && file.size > maxFileBytes;
                }) || (maxTotalBytes > 0 && baseTotalBytes + rawBatchTotalBytes > maxTotalBytes);

                input.disabled = true;

                let preparedFiles = selectedBatch;

                if (batchNeedsOptimization) {
                    setUploadStatus('Optimizing selected media before upload...');
                    preparedFiles = await prepareFiles(selectedBatch);
                }

                let runningTotalBytes = baseTotalBytes;
                const rejectedMessages = [];
                const nextFiles = preparedFiles.filter(function (newFile) {
                    if (maxFileBytes > 0 && newFile.size > maxFileBytes) {
                        rejectedMessages.push(newFile.name + ' exceeds the current file limit of ' + maxFileLabel + '.');
                        return false;
                    }

                    return !currentFiles.some(function (currentFile) {
                        const isDuplicate = currentFile.name === newFile.name
                            && currentFile.size === newFile.size
                            && currentFile.lastModified === newFile.lastModified;

                        if (isDuplicate) {
                            rejectedMessages.push(newFile.name + ' is already selected.');
                        }

                        return isDuplicate;
                    });
                }).filter(function (newFile) {
                    if (maxTotalBytes > 0 && runningTotalBytes + newFile.size > maxTotalBytes) {
                        rejectedMessages.push('The selected files exceed the current upload limit of ' + maxTotalLabel + ' per submission.');
                        return false;
                    }

                    runningTotalBytes += newFile.size;

                    return true;
                });

                selectedFiles.set(input, currentFiles.concat(nextFiles));
                syncInputFiles(input);
                renderPreviews();
                input.disabled = false;

                if (rejectedMessages.length > 0) {
                    setUploadStatus(rejectedMessages[0]);
                    return;
                }

                setUploadStatus(batchNeedsOptimization
                    ? 'Ready to submit. Media is optimized for faster upload.'
                    : 'Ready to submit. Files are already within the current upload limit.');
            });
        });

        form.addEventListener('submit', function (event) {
            const currentTotalBytes = totalSelectedBytes();

            if (maxTotalBytes > 0 && currentTotalBytes > maxTotalBytes) {
                event.preventDefault();
                setSubmitIdle();
                setUploadStatus('The selected files exceed the current upload limit of ' + maxTotalLabel + ' per submission.');
                return;
            }

            event.preventDefault();
            setSubmitBusy('Uploading...');
            setUploadStatus('Uploading your review media... 0%');

            const formData = new FormData(form);
            const request = new XMLHttpRequest();

            request.open('POST', form.action, true);
            request.setRequestHeader('Accept', 'application/json');
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            request.upload.addEventListener('progress', function (progressEvent) {
                if (!progressEvent.lengthComputable) {
                    return;
                }

                const percent = Math.max(0, Math.min(100, Math.round((progressEvent.loaded / progressEvent.total) * 100)));
                setUploadStatus('Uploading your review media... ' + percent + '%');
                setSubmitBusy('Uploading ' + percent + '%');
            });

            request.addEventListener('load', function () {
                let payload = null;

                try {
                    payload = JSON.parse(request.responseText || '{}');
                } catch (error) {
                    payload = null;
                }

                if (request.status >= 200 && request.status < 300) {
                    setSubmitBusy('Refreshing...');
                    setUploadStatus((payload?.message || 'Review submitted successfully.') + ' Refreshing page...');
                    window.setTimeout(function () {
                        window.location.reload();
                    }, 250);
                    return;
                }

                setSubmitIdle();
                setUploadStatus(firstErrorMessage(payload));
            });

            request.addEventListener('error', function () {
                setSubmitIdle();
                setUploadStatus('Upload failed. Please check your connection and try again.');
            });

            request.addEventListener('abort', function () {
                setSubmitIdle();
                setUploadStatus('Upload canceled.');
            });

            request.send(formData);
        });

        form.addEventListener('reset', function () {
            clearSelectedFiles();
        });

        window.addEventListener('beforeunload', revokePreviewUrls);
    });

    document.addEventListener('DOMContentLoaded', function () {
        const lightbox = document.querySelector('[data-review-lightbox]');
        const dialog = document.querySelector('[data-review-lightbox-dialog]');
        const closeButton = document.querySelector('[data-review-lightbox-close]');
        let previousOverflow = '';

        if (!lightbox || !dialog || !closeButton) {
            return;
        }

        const closeLightbox = function () {
            lightbox.hidden = true;
            lightbox.setAttribute('aria-hidden', 'true');
            dialog.innerHTML = '';
            document.body.style.overflow = previousOverflow;
        };

        const openLightbox = function (type, src, alt) {
            previousOverflow = document.body.style.overflow;
            dialog.innerHTML = '';

            if (type === 'video') {
                const video = document.createElement('video');
                video.src = src;
                video.controls = true;
                video.autoplay = true;
                video.className = 'review-lightbox-media';
                dialog.appendChild(video);
            } else {
                const image = document.createElement('img');
                image.src = src;
                image.alt = alt || 'Review picture';
                image.className = 'review-lightbox-media';
                dialog.appendChild(image);
            }

            lightbox.hidden = false;
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            closeButton.focus();
        };

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-review-lightbox-trigger]');

            if (!trigger) {
                return;
            }

            event.preventDefault();

            const type = trigger.dataset.reviewLightboxType || 'image';
            const src = trigger.dataset.reviewLightboxSrc || trigger.getAttribute('href') || trigger.currentSrc || trigger.src;
            const alt = trigger.querySelector('img')?.alt || trigger.alt || '';

            if (src) {
                openLightbox(type, src, alt);
            }
        });

        closeButton.addEventListener('click', closeLightbox);

        lightbox.addEventListener('click', function (event) {
            if (event.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !lightbox.hidden) {
                closeLightbox();
            }
        });
    });
</script>
<script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const quantityBox = document.querySelector('[data-purchase-quantity-box]');

                                if (!quantityBox) {
                                    return;
                                }

                                let maxStock = Math.max(0, Number(quantityBox.dataset.maxStock || 0));
                                let minQuantity = maxStock > 0 ? 1 : 0;
                                const display = quantityBox.querySelector('[data-quantity-display]');
                                const decrementButton = quantityBox.querySelector('[data-quantity-decrement]');
                                const incrementButton = quantityBox.querySelector('[data-quantity-increment]');
                                const note = quantityBox.querySelector('[data-quantity-note]');
                                const totalDisplay = document.querySelector('[data-purchase-total]');
                                const quantityInputs = Array.from(document.querySelectorAll('[data-purchase-quantity]'));
                                const variantInputs = Array.from(document.querySelectorAll('[data-purchase-variant-input]'));
                                const submitButtons = Array.from(document.querySelectorAll('[data-purchase-submit]'));
                                const variantButtons = Array.from(document.querySelectorAll('[data-variant-choice]'));
                                const variantNote = document.querySelector('[data-variant-note]');
                                const productDisplayPrice = document.querySelector('[data-product-display-price]');
                                const variantModal = document.querySelector('[data-variant-modal]');
                                const openVariantModalButtons = Array.from(document.querySelectorAll('[data-open-variant-modal]'));
                                const closeVariantModalButtons = Array.from(document.querySelectorAll('[data-close-variant-modal]'));
                                const hasVariants = {{ json_encode($hasVariants) }};
                                let unitPrice = {{ json_encode($displayPrice) }};

                                if (!display || !decrementButton || !incrementButton || !note || !totalDisplay || !quantityInputs.length) {
                                    return;
                                }

                                const formatPeso = (value) => `₱ ${Number(value).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                })}`;

                                const clampQuantity = (value) => {
                                    if (maxStock <= 0) {
                                        return 0;
                                    }

                                    return Math.min(maxStock, Math.max(minQuantity, value));
                                };

                                const updateQuantity = (nextQuantity) => {
                                    const quantity = clampQuantity(nextQuantity);

                                    display.value = quantity;
                                    totalDisplay.innerHTML = '&#8369; ' + Number(unitPrice * quantity).toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2,
                                    });
                                    quantityInputs.forEach((input) => {
                                        input.value = String(quantity);
                                    });

                                    decrementButton.disabled = quantity <= minQuantity;
                                    incrementButton.disabled = maxStock <= 0 || quantity >= maxStock;

                                    if (maxStock <= 0) {
                                        note.hidden = false;
                                        note.textContent = hasVariants ? 'Choose an available variant.' : 'Out of stock.';
                                        return;
                                    }

                                    if (quantity >= maxStock) {
                                        note.hidden = false;
                                        note.textContent = 'Max stock reached.';
                                        return;
                                    }

                                    note.hidden = true;
                                    note.textContent = '';
                                };

                                const setPurchasingEnabled = (enabled) => {
                                    submitButtons.forEach((button) => {
                                        button.disabled = !enabled;
                                    });
                                };

                                const openVariantModal = () => {
                                    if (!variantModal) {
                                        return;
                                    }

                                    variantModal.hidden = false;
                                    variantModal.setAttribute('aria-hidden', 'false');
                                    document.body.classList.add('modal-open');
                                };

                                const closeVariantModal = () => {
                                    if (!variantModal) {
                                        return;
                                    }

                                    variantModal.hidden = true;
                                    variantModal.setAttribute('aria-hidden', 'true');
                                    document.body.classList.remove('modal-open');
                                };

                                const selectVariant = (button) => {
                                    const selectedVariantId = button.dataset.variantId || '';

                                    variantButtons.forEach((variantButton) => {
                                        variantButton.classList.toggle('is-selected', variantButton.dataset.variantId === selectedVariantId);
                                    });

                                    maxStock = Math.max(0, Number(button.dataset.variantStock || 0));
                                    minQuantity = maxStock > 0 ? 1 : 0;
                                    unitPrice = Number(button.dataset.variantPrice || 0);

                                    variantInputs.forEach((input) => {
                                        input.value = button.dataset.variantId || '';
                                    });

                                    if (variantNote) {
                                        variantNote.hidden = true;
                                        variantNote.textContent = '';
                                    }

                                    if (productDisplayPrice) {
                                        productDisplayPrice.innerHTML = '&#8369; ' + unitPrice.toLocaleString('en-US', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2,
                                        });
                                    }

                                    setPurchasingEnabled(maxStock > 0);
                                    updateQuantity(maxStock > 0 ? 1 : 0);

                                    if (button.closest('[data-variant-modal]')) {
                                        closeVariantModal();
                                    }
                                };

                                decrementButton.addEventListener('click', function () {
                                    updateQuantity(Number(display.value || minQuantity) - 1);
                                });

                                incrementButton.addEventListener('click', function () {
                                    updateQuantity(Number(display.value || minQuantity) + 1);
                                });

                                variantButtons.forEach((button) => {
                                    button.addEventListener('click', function () {
                                        if (!button.disabled) {
                                            selectVariant(button);
                                        }
                                    });
                                });

                                openVariantModalButtons.forEach((button) => {
                                    button.addEventListener('click', openVariantModal);
                                });

                                closeVariantModalButtons.forEach((button) => {
                                    button.addEventListener('click', closeVariantModal);
                                });

                                document.addEventListener('keydown', function (event) {
                                    if (event.key === 'Escape' && variantModal && !variantModal.hidden) {
                                        closeVariantModal();
                                    }
                                });

                                if (hasVariants) {
                                    setPurchasingEnabled(false);
                                }

                                updateQuantity(Number(display.value || minQuantity));
                            });
</script>
@endsection
