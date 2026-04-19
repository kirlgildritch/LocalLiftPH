@extends('layouts.app')
@section('title', 'LocalLift PH - Shop')

@section('content')
        <link rel="stylesheet" href="{{ asset('assets/css/shop_details.css') }}">
        @php($ownsShop = auth()->check() && (int) $user->id === (int) auth()->id())

        <section class="shop-detail-page">
            <div class="container">
                <div class="checkout-breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&gt;</span>
                    <a href="{{ route('shops.index') }}">Shops</a>
                    <span>&gt;</span>
                    <span>{{ $user->name }}</span>
                </div>

                <div class="shop-hero panel">
                    <div class="shop-hero-copy">
                        <span class="section-kicker">Curated Storefront</span>
                        <h1>{{ $user->name }}</h1>
                        <p>
                            Discover products from this local seller through a cleaner, easier-to-browse storefront.
                        </p>

                        <div class="shop-meta">
                            <span><i class="fa-solid fa-box-open"></i> {{ $products->count() }} products</span>
                            <span><i class="fa-solid fa-location-dot"></i> LocalLift Seller</span>
                        </div>

                        <div class="shop-hero-actions">
                            <a href="#shop-products" class="action-btn secondary-btn">Browse Products</a>
                            @auth
                                @if(!$ownsShop)
                                    <form action="{{ route('messages.start', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="action-btn secondary-btn">Message Seller</button>
                                    </form>
                                @else
                                    <span class="action-btn secondary-btn" aria-disabled="true">This is your shop</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="action-btn secondary-btn">Message Seller</a>
                            @endauth
                        </div>
                    </div>

                    <div class="shop-hero-card">
                        <div class="shop-hero-art">
                            <img src="{{ !empty($user->profile_image) ? asset('storage/' . $user->profile_image) : asset('assets/images/default-product.png') }}"
                                alt="{{ $user->name }}">
                        </div>

                        <div class="shop-highlight-grid">
                            <div class="shop-highlight">
                                <strong>Member Seller</strong>
                                <span>Part of the LocalLift marketplace community.</span>
                            </div>
                            <div class="shop-highlight">
                                <strong>{{ $products->count() }} Active Products</strong>
                                <span>Browse available items from this shop.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shop-detail-layout">
                    <aside class="shop-sidebar">
                        <div class="panel sidebar-panel">
                            <div class="shop-sidebar-brand">
                                <span class="shop-avatar">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </span>
                                <div>
                                    <h2>{{ $user->name }}</h2>
                                    <p>LocalLift seller</p>
                                </div>
                            </div>

                            <div class="shop-sidebar-stats">
                                <div class="stat-chip">
                                    <strong>{{ $products->count() }}</strong>
                                    <span>Active products</span>
                                </div>
                            </div>
                        </div>

                        <div class="panel sidebar-panel">
                            <h3>Categories</h3>
                            <div class="filter-list">
                                <div class="filter-item active">
                                    <div class="filter-label"><span class="dot"></span> All Products</div>
                                    <span class="count">{{ $products->count() }}</span>
                                </div>

                                @php
    $categories = $products->groupBy(fn($product) => $product->category?->name ?? 'Uncategorized');
                                @endphp

                                @foreach($categories as $category => $categoryProducts)
                                    <div class="filter-item">
                                        <div class="filter-label">
                                            <span class="dot"></span> {{ $category }}
                                        </div>
                                        <span class="count">{{ $categoryProducts->count() }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="panel sidebar-panel">
                            <h3>Sort Results</h3>
                            <select>
                                <option>Newest</option>
                                <option>Price Low to High</option>
                                <option>Price High to Low</option>
                            </select>
                        </div>
                    </aside>

                    <div class="shop-main">
                        <div class="panel content-panel">
                            <div class="tab-row">
                                <a href="#shop-products" class="tab-link active">Products</a>
                                <a href="#shop-about" class="tab-link">About</a>
                            </div>

                            <div class="content-header" id="shop-products">
                                <div>
                                    <span class="section-kicker">Shop Catalog</span>
                                    <h2>Available products</h2>
                                </div>

                              
                            </div>

                            <div class="product-grid">
                                @forelse($products as $product)
                                    <article class="product-card">
                                        <div class="product-image">
                                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('assets/images/default-product.png') }}"
                                                alt="{{ $product->name }}" style="width: 100%; height: 220px; object-fit: cover;">
                                        </div>

                                        <div class="product-info">
                                            <span class="product-badge">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                                            <h3>{{ $product->name }}</h3>
                                            <p>{{ $product->description ?: 'No description available.' }}</p>
                                            <div class="price">₱{{ number_format($product->price, 2) }}</div>

                                            <div class="product-actions">
                                                <a href="{{ route('products.show', $product->id) }}"
                                                    class="action-btn secondary-btn">
                                                    View
                                                </a>

                                                @auth
                                                    @if((int) $product->user_id === (int) auth()->id())
                                                        <span class="action-btn primary-btn" aria-disabled="true">Your Product</span>
                                                    @else
                                                        <form action="{{ route('cart.add', $product->id) }}" method="POST"
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
                                    <p>This shop has no products yet.</p>
                                @endforelse
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </section>
@endsection
