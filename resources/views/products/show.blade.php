@extends('layouts.app')
@section('title', 'LocalLift PH - Product')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/product_details.css') }}">
    @php
        $ownsProduct = auth()->check() && (int) $product->user_id === (int) auth()->id();
        $averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
    @endphp

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
                <div class="product-feedback product-feedback-success panel">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="product-feedback product-feedback-error panel">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="product-feedback product-feedback-error panel">{{ $errors->first() }}</div>
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
                        <span class="section-kicker">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                        <h1>{{ $product->name }}</h1>
                        <p class="product-subtitle">{{ $product->category?->name ?? 'Uncategorized' }}</p>

                        <div class="product-meta">
                            <span><i class="fa-solid fa-store"></i> {{ $product->user->name ?? 'LocalLift Seller' }}</span>
                            <span><i class="fa-solid fa-box-open"></i>
                                {{ $product->stock > 0 ? 'Ready to ship' : 'Out of stock' }}</span>
                            <span><i class="fa-solid fa-cubes"></i> Stock: {{ $product->stock }}</span>
                            <span><i class="fa-solid fa-star"></i> {{ $averageRating > 0 ? number_format($averageRating, 1) : 'New' }} | {{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                        </div>

                        <div class="product-price">P{{ number_format($product->price, 2) }}</div>

                      
                        <div class="product-feature-grid">
                            <div class="feature-card">
                                <strong>Category</strong>
                                <span>{{ $product->category?->name ?? 'Uncategorized' }}</span>
                            </div>
                            <div class="feature-card">
                                <strong>Availability</strong>
                                <span>{{ $product->stock > 0 ? 'In stock' : 'Currently unavailable' }}</span>
                            </div>
                            <div class="feature-card">
                                <strong>Average Rating</strong>
                                <span>{{ $averageRating > 0 ? number_format($averageRating, 1) . ' out of 5' : 'No ratings yet' }}</span>
                            </div>
                            <div class="feature-card">
                                <strong>Buyer Reviews</strong>
                                <span>{{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
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
                                <strong>P{{ number_format($product->price, 2) }}</strong>
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
                                        <button type="submit" class="action-btn primary-btn">Add to Cart</button>
                                    </form>
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="buy_now" value="1">
                                        <button type="submit" class="action-btn secondary-btn">Buy Now</button>
                                    </form>
                                @endif

                            @else
                                <a href="{{ route('login') }}" class="action-btn primary-btn">Add to Cart</a>
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

                    <div class="panel seller-card">
                        <span class="section-kicker">Seller</span>
                        <div class="seller-row">
                            <span class="seller-avatar">
                                {{ strtoupper(substr($product->user->name ?? 'LS', 0, 2)) }}
                            </span>
                            <div>
                                <h3>{{ $product->user->name ?? 'LocalLift Seller' }}</h3>
                                <p>Local marketplace seller</p>
                            </div>
                        </div>
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
                    <div class="detail-header">
                        <div>
                            <span class="section-kicker">Ratings & Reviews</span>
                            <h2>What buyers are saying</h2>
                        </div>

                        <div class="review-summary-chip">
                            <strong>{{ $averageRating > 0 ? number_format($averageRating, 1) : '0.0' }}</strong>
                            <span>{{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>

                    <div class="review-stars-display" aria-label="Average rating: {{ $averageRating }} out of 5">
                        @for($star = 1; $star <= 5; $star++)
                            <i class="fa-{{ $averageRating >= $star ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                    </div>

                    @if(auth()->check() && auth()->user()->isBuyer() && $reviewableOrderItems->isNotEmpty())
                        @php
                            $selectedReviewableOrderItem = $reviewableOrderItems->firstWhere('id', (int) request('review_order_item'))
                                ?? $reviewableOrderItems->first();
                        @endphp
                        <form action="{{ route('products.reviews.store', $product) }}" method="POST" class="review-form panel">
                            @csrf
                            <input type="hidden" name="order_item_id" value="{{ $selectedReviewableOrderItem?->id }}">

                            <div class="review-form-header">
                                <div>
                                    <strong>Leave a review</strong>
                                    <p>Only buyers with delivered purchases can rate this product.</p>
                                </div>

                                @if($reviewableOrderItems->count() > 1)
                                    <span class="review-order-note">{{ $reviewableOrderItems->count() }} delivered purchases eligible</span>
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
                            </div>

                            <button type="submit" class="action-btn primary-btn review-submit-btn">Submit Review</button>
                        </form>
                    @endif

                    <div class="review-list">
                        @forelse($product->reviews as $review)
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div>
                                        <strong>{{ $review->user->name ?? 'LocalLift Buyer' }}</strong>
                                        <div class="review-card-stars" aria-label="{{ $review->rating }} out of 5 stars">
                                            @for($star = 1; $star <= 5; $star++)
                                                <i class="fa-{{ $review->rating >= $star ? 'solid' : 'regular' }} fa-star"></i>
                                            @endfor
                                        </div>
                                    </div>

                                    <span>{{ $review->created_at->format('M d, Y') }}</span>
                                </div>

                                <p>{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>
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

                    <div class="related-grid product-card-grid">
                        @forelse($relatedProducts as $relatedProduct)
                            <x-product-card :product="$relatedProduct">
                                <x-slot:meta>
                                    <p class="market-product-card__meta-line">
                                        <i class="fa-solid fa-star"></i>
                                        {{ $relatedProduct->reviews_avg_rating ? number_format((float) $relatedProduct->reviews_avg_rating, 1) : 'New' }}
                                        <span>| {{ $relatedProduct->reviews_count }} review{{ $relatedProduct->reviews_count !== 1 ? 's' : '' }}</span>
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
@endsection
