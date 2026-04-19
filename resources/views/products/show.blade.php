@extends('layouts.app')
@section('title', 'LocalLift PH - Product')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/product_details.css') }}">
    @php($ownsProduct = auth()->check() && (int) $product->user_id === (int) auth()->id())

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
                        </div>

                        <div class="product-price">₱{{ number_format($product->price, 2) }}</div>

                        <p class="product-description">
                            {{ $product->description ?: 'No description available for this product yet.' }}
                        </p>

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
                                <strong>₱{{ number_format($product->price, 2) }}</strong>
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
                                <form action="{{ route('messages.start', $product->user) }}" method="POST">
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
                <section class="panel detail-card">
                    <div class="detail-header">
                        <div>
                            <span class="section-kicker">Related Products</span>
                            <h2>You may also like</h2>
                        </div>
                    </div>

                    <div class="related-grid">
                        @forelse($relatedProducts as $relatedProduct)
                            <article class="related-card">
                                <div class="related-image">
                                    <img src="{{ $relatedProduct->image ? asset('storage/' . $relatedProduct->image) : asset('assets/images/default-product.png') }}"
                                        alt="{{ $relatedProduct->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>

                                <div class="related-info">
                                    <span class="product-badge">{{ $relatedProduct->category?->name ?? 'Uncategorized' }}</span>
                                    <h3>{{ $relatedProduct->name }}</h3>
                                    <p>{{ $relatedProduct->user->name ?? 'LocalLift Seller' }}</p>
                                    <div class="price">₱{{ number_format($relatedProduct->price, 2) }}</div>

                                    <div class="card-actions">
                                        <a href="{{ route('products.show', $relatedProduct->id) }}"
                                            class="action-btn secondary-btn">
                                            View
                                        </a>

                                        @auth
                                            @if((int) $relatedProduct->user_id === (int) auth()->id())
                                                <span class="action-btn primary-btn" aria-disabled="true">Your Product</span>
                                            @else
                                                <form action="{{ route('cart.add', $relatedProduct->id) }}" method="POST"
                                                    style="display:inline;" class="add-to-cart-form">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="action-btn primary-btn">Add to Cart</button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="action-btn primary-btn">Add to Cart</a>
                                        @endauth
                                    </div>
                                </div>
                            </article>
                        @empty
                            <p>No related products available.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </section>
@endsection
