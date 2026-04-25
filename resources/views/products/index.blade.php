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
                        <h3>Categories</h3>

                        <div class="mobile-category-dropdown">
                            <select onchange="if(this.value) window.location.href=this.value">
                                <option
                                    value="{{ route('products.index', array_filter(['search' => $search, 'sort' => $sort, 'min_price' => $minPrice, 'max_price' => $maxPrice])) }}"
                                    {{ empty($categorySlug) ? 'selected' : '' }}>
                                    All ({{ $categories->sum('products_count') }})
                                </option>
                                @foreach($categories as $categoryOption)
                                    <option
                                        value="{{ route('products.index', array_filter(['search' => $search, 'category' => $categoryOption->slug, 'sort' => $sort, 'min_price' => $minPrice, 'max_price' => $maxPrice])) }}"
                                        {{ $categorySlug === $categoryOption->slug ? 'selected' : '' }}>
                                        {{ $categoryOption->name }} ({{ $categoryOption->products_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-list">
                            <a href="{{ route('products.index', array_filter(['search' => $search, 'sort' => $sort, 'min_price' => $minPrice, 'max_price' => $maxPrice])) }}"
                                class="filter-item {{ empty($categorySlug) ? 'active' : '' }}">
                                <div class="filter-label"><span class="dot"></span> All</div>
                                <span class="count">{{ $categories->sum('products_count') }}</span>
                            </a>
                            @foreach($categories as $categoryOption)
                                <a href="{{ route('products.index', array_filter(['search' => $search, 'category' => $categoryOption->slug, 'sort' => $sort, 'min_price' => $minPrice, 'max_price' => $maxPrice])) }}"
                                    class="filter-item {{ $categorySlug === $categoryOption->slug ? 'active' : '' }}">
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
                            <input type="number" name="min_price" min="0" step="0.01" value="{{ $minPrice }}"
                                placeholder="0">
                            <input type="number" name="max_price" min="0" step="0.01" value="{{ $maxPrice }}"
                                placeholder="1000">
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
                            <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Price High to Low
                            </option>
                        </select>
                    </form>
                </aside>

                <div class="market-main">
                    @if(session('success'))
                        <div
                            style="margin-bottom: 15px; padding: 12px; background: #e8f7ee; color: #1f7a3d; border-radius: 8px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(!empty($search))
                        <div class="panel" style="padding: 10px; margin-bottom: 16px;">
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
                                            <img src="{{ !empty($shop->profile_image) ? asset('storage/' . $shop->profile_image) : asset('assets/images/default-product.png') }}"
                                                alt="{{ $shop->name }}"
                                                style="width: 52px; height: 52px; object-fit: cover; border-radius: 50%;">
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

                    <div class="product-grid product-card-grid" data-skeleton-group data-skeleton-delay="420">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
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

                    @if($products->hasPages())
                        <div class="panel"
                            style="padding: 16px 20px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                            <p style="margin: 0; color: #9fb3c8; font-size: 14px;">
                                Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}
                                products
                            </p>

                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                @if($products->onFirstPage())
                                    <span class="action-btn secondary-btn" style="opacity: 0.5; pointer-events: none;">Previous</span>
                                @else
                                    <a href="{{ $products->previousPageUrl() }}" class="action-btn secondary-btn">Previous</a>
                                @endif

                                <span style="color: #dbeafe; font-size: 14px;">Page {{ $products->currentPage() }} of
                                    {{ $products->lastPage() }}</span>

                                @if($products->hasMorePages())
                                    <a href="{{ $products->nextPageUrl() }}" class="action-btn secondary-btn">Next</a>
                                @else
                                    <span class="action-btn secondary-btn" style="opacity: 0.5; pointer-events: none;">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
