                    <section class="seller-review-product-summary">
                        <div class="seller-review-product-main">
                            <div class="seller-review-product-thumb">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                <div class="seller-review-product-placeholder">No Image</div>
                                @endif
                            </div>

                            <div class="seller-review-product-copy">
                                <h3>{{ $product->name }}</h3>
                                <p>{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                <strong>&#8369; {{ number_format($product->price, 2) }}</strong>
                            </div>
                        </div>

                        <div class="seller-review-summary-cards">
                            <article class="seller-review-summary-card">
                                <span>Average Rating</span>
                                <strong>{{ $product->reviews_avg_rating ? number_format((float) $product->reviews_avg_rating, 1) : 'New' }}</strong>
                            </article>
                            <article class="seller-review-summary-card">
                                <span>Total Reviews</span>
                                <strong>{{ $product->reviews_count }}</strong>
                            </article>
                        </div>
                    </section>
