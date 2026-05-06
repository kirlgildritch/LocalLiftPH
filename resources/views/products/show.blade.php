@extends('layouts.app')
@section('title', 'LocalLift PH - Product')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/product_details.css') }}">
@php
$ownsProduct = auth()->check() && (int) $product->user_id === (int) auth()->id();
$averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
$canReportProduct = auth('web')->check() && !$ownsProduct;
$buyerHasReviewedProduct = auth()->check()
&& auth()->user()->isBuyer()
&& $product->reviews->contains('user_id', auth()->id());
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

        @if(session('success'))
        <div class="review-submitted-state" role="status" aria-live="polite">
            <div class="review-submitted-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <div>
                <strong>Your review has been submitted</strong>
                <p>You can see your feedback in the reviews below.</p>
            </div>
        </div>
        @endif


        <div class="product-detail-layout">
            <div class="product-main panel">
                <div class="product-gallery">
                    <div class="product-visual">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('assets/images/default-product.png') }}"
                            alt="{{ $product->name }}">
                    </div>

                    <div class="product-thumbnail-row">
                        <button class="thumb-card active" type="button">Main View</button>
                        <button class="thumb-card" type="button">Details</button>
                        <button class="thumb-card" type="button">Preview</button>
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
                        <a href="{{ route('login') }}" class="report-trigger-button" aria-label="Log in to report product">
                            <i class="fa-solid fa-flag"></i>
                        </a>
                        @endif
                    </div>
                    <h1>{{ $product->name }}</h1>


                    <div class="product-meta">
                        <span><i class="fa-solid fa-store"></i>
                            {{ $product->user->sellerProfile?->store_name ?? 'LocalLift Seller' }}</span>
                        <span><i class="fa-solid fa-box-open"></i>
                            {{ $product->stock > 0 ? 'Ready to ship' : 'Out of stock' }}</span>
                        <span><i class="fa-solid fa-cubes"></i> Stock: {{ $product->stock }}</span>
                        <span><i class="fa-solid fa-star"></i>
                            {{ $averageRating > 0 ? number_format($averageRating, 1) : 'New' }} |
                            {{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                    </div>

                    <div class="product-price">&#8369; {{ number_format($product->price, 2) }}</div>


                    <div class="product-feature-grid">
                        <div class="feature-card">
                            <strong>Category</strong>
                            <span>{{ $product->category?->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="feature-card">
                            <strong>Availability</strong>
                            <span>{{ $product->stock > 0 ? 'In stock' : 'Currently unavailable' }}</span>
                        </div>

                    </div>
                </div>
            </div>

            <aside class="purchase-sidebar">
                <div class="panel purchase-card">
                    <span class="section-kicker">Purchase</span>
                    <h2>Order summary</h2>

                    <div class="quantity-box">
                        <span>Quantity</span>
                        <div class="quantity-control">
                            <button type="button">-</button>
                            <input type="text" value="1" readonly>
                            <button type="button">+</button>
                        </div>
                    </div>

                    <div class="purchase-meta">
                        <div>
                            <span>Price</span>
                            <strong>&#8369; {{ number_format($product->price, 2) }}</strong>
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
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="action-btn primary-btn"><i class="fa-solid fa-cart-shopping"></i></button>
                        </form>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="buy_now" value="1">
                            <button type="submit" class="action-btn secondary-btn">Buy Now</button>
                        </form>
                        @endif

                        @else
                        <a href="{{ route('login') }}" class="action-btn primary-btn"><i class="fa-solid fa-cart-shopping"></i></a>
                        <a href="{{ route('login') }}" class="action-btn secondary-btn">Buy Now</a>
                        @endauth

                        <button type="button" class="icon-btn" aria-label="Add to wishlist">
                            <i class="fa-regular fa-heart"></i>
                        </button>
                    </div>

                    <a href="{{ route('shops.show', $product->user->id) }}"
                        class="action-btn secondary-btn full-btn">View Shop</a>

                    @auth
                    @if(!$ownsProduct)
                    <form action="{{ route('messages.start', $product->user) }}" method="POST" data-chat-start-form>
                        @csrf
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
                        <strong>{{ $averageRating > 0 ? number_format($averageRating, 1) : '0.0' }}</strong>
                        <span>{{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                    </div>
                </div>

                <!-- <div class="review-stars-display" aria-label="Average rating: {{ $averageRating }} out of 5">
                    @for($star = 1; $star <= 5; $star++)
                        <i class="fa-{{ $averageRating >= $star ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor -->

                <div class="review-toolbar">
                    <!-- <div class="review-stars-display" aria-label="Average rating: {{ $averageRating }} out of 5">
                        @for($star = 1; $star <= 5; $star++)
                            <i class="fa-{{ $averageRating >= $star ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                    </div> -->

                    @if(auth()->check() && auth()->user()->isBuyer() && !$buyerHasReviewedProduct && $reviewableOrderItems->isNotEmpty())
                    <a href="#buyer-review-form" class="review-write-chip">
                        <i class="fa-solid fa-pen"></i>
                        Write a review
                    </a>
                    @endif
                </div>


                @if(auth()->check() && auth()->user()->isBuyer() && !$buyerHasReviewedProduct && $reviewableOrderItems->isNotEmpty())
                @php
                $selectedReviewableOrderItem = $reviewableOrderItems->firstWhere('id', (int) request('review_order_item'))
                ?? $reviewableOrderItems->first();
                @endphp

                <form action="{{ route('products.reviews.store', $product) }}" method="POST" enctype="multipart/form-data" class="review-form panel" id="buyer-review-form">
                    @csrf
                    <input type="hidden" name="order_item_id" value="{{ $selectedReviewableOrderItem?->id }}">

                    <div class="review-form-header">
                        <div>
                            <strong>Leave a review</strong>
                            <p>Only buyers with delivered purchases can rate this product.</p>
                        </div>

                        @if($reviewableOrderItems->count() > 1)
                        <span class="review-order-note">{{ $reviewableOrderItems->count() }} completed purchases
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
                                <span>Photos and videos can be previewed before submitting.</span>
                            </div>

                            <div class="review-upload-inputs">
                                <div class="review-upload-input">
                                    <label for="review_image">Upload picture</label>
                                    <input type="file" name="review_image" id="review_image" accept="image/*,video/*" multiple data-review-preview-input>
                                </div>

                                <div class="review-upload-input">
                                    <label for="review_video">Upload video</label>
                                    <input type="file" name="review_video" id="review_video" accept="image/*,video/*" multiple data-review-preview-input>
                                </div>
                            </div>

                            <div class="review-upload-preview" data-review-preview-grid hidden></div>
                        </div>
                    </div>

                    <button type="submit" class="action-btn primary-btn review-submit-btn">Submit Review</button>
                </form>

                @endif

                <!-- <select name="rating" required>
                        <option value="">Select rating</option>
                        @for($rating = 5; $rating >= 1; $rating--)
                        <option value="{{ $rating }}">{{ $rating }} Star{{ $rating !== 1 ? 's' : '' }}</option>
                        @endfor
                    </select>

                    <textarea name="comment" rows="4" placeholder="Share your review...">{{ old('comment') }}</textarea>

                    <input type="file" name="review_image" accept="image/*">
                    <input type="file" name="review_video" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm">

                    <button type="submit" class="action-btn primary-btn review-submit-btn">Submit Review</button>
                </form>
                 -->

                <div class="review-list">
                    @forelse($product->reviews as $review)
                    <article class="review-card">
                        <div class="review-card-header">
                            <div class="review-author">
                                <div class="review-author-avatar">
                                    @if($review->user?->profile_image)
                                    <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name ?? 'Buyer' }}">
                                    @else
                                    <span>{{ strtoupper(mb_substr($review->user->name ?? 'B', 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <strong>
                                        {{ $review->user->name ?? 'LocalLift Buyer' }}
                                        <i class="fa-solid fa-circle-check"></i>
                                    </strong>
                                    <span>{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>

                            <div class="review-card-stars" aria-label="{{ $review->rating }} out of 5 stars">
                                @for($star = 1; $star <= 5; $star++)
                                    <i class="fa-{{ $review->rating >= $star ? 'solid' : 'regular' }} fa-star"></i>
                                    @endfor
                            </div>
                        </div>

                        <p class="review-card-comment">{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>

                        @if($review->image_path || $review->video_path)
                        <div class="review-media-grid">
                            @if($review->image_path)
                            <a href="{{ asset('storage/' . $review->image_path) }}" target="_blank" rel="noopener" class="review-media-item review-media-image">
                                <img src="{{ asset('storage/' . $review->image_path) }}" alt="Review picture from {{ $review->user->name ?? 'buyer' }}">
                            </a>
                            @endif

                            <!-- @if($review->image_path)
                        <img src="{{ asset('storage/' . $review->image_path) }}" alt="Review picture" style="max-width: 180px; border-radius: 12px;">
                        @endif -->

                            <!-- @if($review->video_path)
                            <video controls style="max-width: 260px; border-radius: 12px;">
                                <source src="{{ asset('storage/' . $review->video_path) }}">
                            </video>
                            @endif -->

                            @if($review->video_path)
                            <div class="review-media-item review-media-video-wrap">
                                <video class="review-media-video" controls preload="metadata">
                                    <source src="{{ asset('storage/' . $review->video_path) }}">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($review->seller_reply)
                        <div class="seller-review-reply">
                            <strong>Seller reply</strong>
                            <p>{{ $review->seller_reply }}</p>
                            @if($review->seller_replied_at)
                            <span>{{ $review->seller_replied_at->format('M d, Y') }}</span>
                            @endif
                        </div>
                        @endif
                    </article>
                    @empty

                    <div class="review-empty-state">
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
                    <x-product-card :product="$relatedProduct">
                        <x-slot:meta>
                            <p class="market-product-card__meta-line">
                                <i class="fa-solid fa-star"></i>
                                {{ $relatedProduct->reviews_avg_rating ? number_format((float) $relatedProduct->reviews_avg_rating, 1) : 'New' }}
                                <span>| {{ $relatedProduct->reviews_count }}
                                    review{{ $relatedProduct->reviews_count !== 1 ? 's' : '' }}</span>
                            </p>
                        </x-slot:meta>
                    </x-product-card>
                    @empty
                    <p>No related products available.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('buyer-review-form');

        if (!form || !window.DataTransfer) {
            return;
        }

        const inputs = Array.from(form.querySelectorAll('[data-review-preview-input]'));
        const previewGrid = form.querySelector('[data-review-preview-grid]');
        const selectedFiles = new Map();
        const objectUrls = new Map();

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
                meta.textContent = item.file.name;

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

        inputs.forEach(function (input) {
            selectedFiles.set(input, []);

            input.addEventListener('change', function () {
                selectedFiles.set(input, Array.from(input.files || []));
                syncInputFiles(input);
                renderPreviews();
            });
        });

        form.addEventListener('reset', function () {
            selectedFiles.clear();
            inputs.forEach(function (input) {
                selectedFiles.set(input, []);
            });
            renderPreviews();
        });

        window.addEventListener('beforeunload', revokePreviewUrls);
    });
</script>
@endsection
