<section class="panel detail-card review-section" id="product-reviews">
    <div class="review-section-head">
        <span class="section-kicker">Ratings & Reviews</span>

        <div class="review-summary-chip">
            <strong data-review-average>{{ $productPage->averageRating > 0 ? number_format($productPage->averageRating, 1) : '0.0' }}</strong>
            <span data-review-count>{{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
        </div>
    </div>

    <div class="review-toolbar">
        @if($productPage->canReviewProduct)
        <a href="#buyer-review-form" class="review-write-chip" data-review-write-chip>
            <i class="fa-solid fa-pen"></i>
            Write a review
        </a>
        @endif
    </div>

    @if($product->reviews_count > $initialReviewsLimit)
        <div class="review-toggle-bar">
            <a href="{{ $productPage->productReviewsToggleUrl }}" class="action-btn secondary-btn review-toggle-btn">
                {{ $showAllReviews ? 'Show Fewer Reviews' : 'View All Reviews' }}
            </a>
        </div>
    @endif

    @if($productPage->canReviewProduct)
    <form action="{{ route('products.reviews.store', $product) }}" method="POST" enctype="multipart/form-data" class="review-form panel" id="buyer-review-form"
        data-review-max-files="{{ $productPage->reviewMedia->maxFiles }}"
        data-review-max-file-bytes="{{ $productPage->reviewMedia->effectiveFileBytes }}"
        data-review-max-total-bytes="{{ $productPage->reviewMedia->requestBytes ?? 0 }}"
        data-review-max-file-label="{{ $productPage->reviewMedia->effectiveFileLabel }}"
        data-review-max-total-label="{{ $productPage->reviewMedia->requestLabel }}">
        @csrf
        <input type="hidden" name="order_item_id" value="{{ $productPage->selectedReviewableOrderItem?->id }}">

        <div class="review-form-header">
            <div>
                <strong>Leave a review</strong>
                <p>Only buyers with completed purchases can rate this product.</p>
            </div>

            @if($reviewableOrderItems->count() > 1)
            <span class="review-order-note" data-review-order-note>{{ $reviewableOrderItems->count() }} completed purchases eligible</span>
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
                <textarea name="comment" id="comment" rows="4" placeholder="Share what you liked about this product...">{{ old('comment') }}</textarea>
            </div>

            <div class="review-form-field review-form-field-full review-upload-section">
                <div class="review-upload-header">
                    <label>Upload media</label>
                    <span data-review-upload-status>Up to {{ $productPage->reviewMedia->maxFiles }} files, {{ $productPage->reviewMedia->effectiveFileLabel }} each, {{ $productPage->reviewMedia->requestLabel }} total per upload.</span>
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
