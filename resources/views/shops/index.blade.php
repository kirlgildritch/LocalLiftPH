@extends('layouts.app')
@section('title', 'LocalLift PH - Shops')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/shops.css') }}">

    <section class="market-page shops-page">
        <div class="container">
            <div class="page-intro">
                <div class="checkout-breadcrumb">
                    <a href="{{ route('home') }}">Home</a>
                    <span>&gt;</span>
                    <span>Shops</span>
                </div>
            </div>

            <div class="market-layout">
                <aside class="market-sidebar">
                    <div class="panel sidebar-panel">
                        <h3>Categories</h3>
                        <div class="filter-list">
                            <a href="{{ route('shops.index', array_filter(['sort' => $sort])) }}"
                                class="filter-item {{ empty($categorySlug) ? 'active' : '' }}">
                                <div class="filter-label"><span class="dot"></span> All</div>
                                <span class="count">{{ $categories->sum('products_count') }}</span>
                            </a>
                            @foreach($categories as $categoryOption)
                                <a href="{{ route('shops.index', array_filter(['category' => $categoryOption->slug, 'sort' => $sort])) }}"
                                    class="filter-item {{ $categorySlug === $categoryOption->slug ? 'active' : '' }}">
                                    <div class="filter-label"><span class="dot"></span> {{ $categoryOption->name }}</div>
                                    <span class="count">{{ $categoryOption->products_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <div class="market-main">
                    <div class="market-toolbar panel">
                        <div class="toolbar-copy">
                            <span class="toolbar-label">Curated storefronts</span>
                            <h2>Shops</h2>
                        </div>

                        <div class="toolbar-controls">
                            <div class="inline-select">
                                <label>Sort By</label>
                                <form action="{{ route('shops.index') }}" method="GET">
                                    @if(!empty($categorySlug))
                                        <input type="hidden" name="category" value="{{ $categorySlug }}">
                                    @endif
                                    <select name="sort" onchange="this.form.submit()">
                                        <option value="newest" {{ $sort === 'newest' ? 'selected' : '' }}>Newest</option>
                                        <option value="most_products" {{ $sort === 'most_products' ? 'selected' : '' }}>Most
                                            Products</option>
                                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A-Z
                                        </option>
                                        <option value="name_desc" {{ $sort === 'name_desc' ? 'selected' : '' }}>Name Z-A
                                        </option>
                                    </select>
                                </form>
                            </div>

                            <div class="view-icons">
                                <i class="fa-solid fa-table-cells-large"></i>
                                <i class="fa-solid fa-list"></i>
                            </div>
                        </div>
                    </div>

                    <div class="shops-grid">
                        @forelse($shops as $shop)
                            <article class="shop-card panel">
                                <div class="shop-logo">
                                    <img src="{{ !empty($shop->profile_image) ? asset('storage/' . $shop->profile_image) : asset('assets/images/default-product.png') }}"
                                        alt="{{ $shop->name }}">
                                </div>

                                <div class="shop-card-body">
                                    <span class="shop-badge">Local Seller</span>
                                    <h3>{{ $shop->name }}</h3>
                                    <p>Discover products from this local seller on LocalLift.</p>

                                    <div class="shop-rating">
                                        <i class="fa-solid fa-star"></i>
                                        <span>Trusted Local Seller</span>
                                    </div>

                                    <div class="shop-products">
                                        {{ $shop->products_count }} product{{ $shop->products_count != 1 ? 's' : '' }} available
                                    </div>

                                    <a href="{{ route('shops.show', $shop->id) }}" class="action-btn primary-btn">
                                        Visit Shop
                                    </a>
                                </div>
                            </article>
                        @empty
                            <div class="panel" style="padding: 20px;">
                                <p>No shops available yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection