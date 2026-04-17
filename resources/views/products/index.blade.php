@extends('layouts.app')
@section('title', 'LocalLift PH - Products')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/productsStyle.css') }}">

<section class="market-page products-page">
    <div class="container">
        <div class="page-intro">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>Products</span>
            </div>
        </div>

        <div class="market-layout">
            <aside class="market-sidebar">
                <div class="panel sidebar-panel">
                    <h3>Category</h3>
                    <div class="filter-list">
                        <a href="{{ route('products.index', array_filter(['search' => $search, 'sort' => $sort, 'min_price' => $minPrice, 'max_price' => $maxPrice])) }}" class="filter-item {{ empty($categorySlug) ? 'active' : '' }}">
                            <div class="filter-label"><span class="dot"></span> All</div>
                            <span class="count">{{ $categories->sum('products_count') }}</span>
                        </a>
                        @foreach($categories as $categoryOption)
                            <a
                                href="{{ route('products.index', array_filter(['search' => $search, 'category' => $categoryOption->slug, 'sort' => $sort, 'min_price' => $minPrice, 'max_price' => $maxPrice])) }}"
                                class="filter-item {{ $categorySlug === $categoryOption->slug ? 'active' : '' }}"
                            >
                                <div class="filter-label"><span class="dot"></span> {{ $categoryOption->name }}</div>
                                <span class="count">{{ $categoryOption->products_count }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <form action="{{ route('products.index') }}" method="GET" class="panel sidebar-panel">
                    <h3>Filter By Price</h3>
                    <div class="price-labels">
                        <span>Min</span>
                        <span>Max</span>
                    </div>

                    @if(!empty($search))
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if(!empty($categorySlug))
                        <input type="hidden" name="category" value="{{ $categorySlug }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <div class="price-filter-inputs">
                        <input type="number" name="min_price" min="0" step="0.01" value="{{ $minPrice }}" placeholder="0">
                        <input type="number" name="max_price" min="0" step="0.01" value="{{ $maxPrice }}" placeholder="1000">
                    </div>

                    <button class="action-btn primary-btn full-btn" type="submit">Filter</button>
                </form>

                <form action="{{ route('products.index') }}" method="GET" class="panel sidebar-panel">
                    <h3>Sort Results</h3>
                    @if(!empty($search))
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    @if(!empty($categorySlug))
                        <input type="hidden" name="category" value="{{ $categorySlug }}">
                    @endif
                    @if($minPrice !== null)
                        <input type="hidden" name="min_price" value="{{ $minPrice }}">
                    @endif
                    @if($maxPrice !== null)
                        <input type="hidden" name="max_price" value="{{ $maxPrice }}">
                    @endif
                    <select name="sort" onchange="this.form.submit()">
                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Price Low to High</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price High to Low</option>
                    </select>
                </form>

            </aside>

            <div class="market-main">
                @if(session('success'))
                    <div style="margin-bottom: 15px; padding: 12px; background: #e8f7ee; color: #1f7a3d; border-radius: 8px;">
                        {{ session('success') }}
                    </div>
                @endif
                @if(!empty($search))
                    <div class="panel" style="padding: 16px; margin-bottom: 16px;">
                        <p>Search results for: <strong>{{ $search }}</strong></p>
                    </div>
                @endif
                @if(!empty($search) && isset($shops) && $shops->count())
                    <div class="panel" style="padding: 20px; margin-bottom: 20px;">
                        <h3 style="margin-bottom: 14px;">Matching Shops</h3>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                            @foreach($shops as $shop)
                                <div style="padding: 16px; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px;">
                                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                        <img
                                            src="{{ !empty($shop->profile_image) ? asset('storage/' . $shop->profile_image) : asset('assets/images/default-product.png') }}"
                                            alt="{{ $shop->name }}"
                                            style="width: 52px; height: 52px; object-fit: cover; border-radius: 50%;"
                                        >
                                        <div>
                                            <h4 style="margin: 0;">{{ $shop->name }}</h4>
                                            <small style="color: #9fb3c8;">
                                                {{ $shop->products_count }} product{{ $shop->products_count != 1 ? 's' : '' }}
                                            </small>
                                        </div>
                                    </div>

                                    <a href="{{ route('shops.show', $shop->id) }}" class="action-btn secondary-btn">
                                        Visit Shop
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="product-grid">
                    @forelse($products as $product)
                        <article class="product-card panel">
                            <div class="product-image">
                                <img
                                    src="{{ $product->image ? asset('storage/' . $product->image) : asset('assets/images/default-product.png') }}"
                                    alt="{{ $product->name }}"
                                >
                            </div>

                            <div class="product-info">
                                <span class="product-badge">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                                <h4>{{ $product->name }}</h4>
                                <p>{{ $product->user->name ?? 'LocalLift Seller' }}</p>
                                <div class="price">₱{{ number_format($product->price, 2) }}</div>

                                <div class="product-actions">
                                    <a href="{{ route('products.show', $product->id) }}" class="action-btn secondary-btn">
                                        View
                                    </a>

                                    @auth
                                        @if(auth()->user()->isBuyer())
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST" style="display: inline;" class="add-to-cart-form">
                                                @csrf
                                                <button type="submit" class="action-btn primary-btn">Add to Cart</button>
                                            </form>
                                        @else
                                            <button type="button" class="action-btn primary-btn" disabled>
                                                Add to Cart
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="action-btn primary-btn">Add to Cart</a>
                                    @endauth
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="panel" style="padding: 20px;">
                            <p>
                                @if(!empty($search))
                                    No products found for "<strong>{{ $search }}</strong>".
                                @else
                                    No products available yet.
                                @endif
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
