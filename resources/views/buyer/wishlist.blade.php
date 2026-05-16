@extends('layouts.app')

@section('title', 'LocalLift PH - My Wishlist')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/wishlist.css') }}">

    <section class="wishlist-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>My Wishlist</span>
            </div>

            <header class="wishlist-header panel">
                <div>
                    <span class="section-kicker">Saved Products</span>
                    <h1>My Wishlist</h1>
                    <p>Keep track of local products you want to revisit before checking out.</p>
                </div>

                <a href="{{ route('products.index') }}" class="action-btn secondary-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Browse Products
                </a>
            </header>

            @if(session('error'))
                <div class="wishlist-alert wishlist-alert--error">{{ session('error') }}</div>
            @endif

            @if($wishlists->count())
                @if(($followedShops ?? collect())->count())
                    <section class="followed-shops-panel panel">
                        <div class="wishlist-section-head">
                            <div>
                                <span class="section-kicker">Followed Shops</span>
                                <h2>Shops you follow</h2>
                            </div>
                            <a href="{{ route('shops.index') }}" class="action-btn secondary-btn">
                                <i class="fa-solid fa-store"></i>
                                Browse Shops
                            </a>
                        </div>

                        <div class="followed-shops-grid">
                            @foreach($followedShops as $follow)
                                @php
                                    $shop = $follow->seller;
                                    $shopName = $shop?->sellerProfile?->store_name ?? $shop?->name ?? 'LocalLift Seller';
                                    $shopLogo = $shop?->sellerProfile?->shop_logo
                                        ? asset('storage/' . $shop->sellerProfile->shop_logo)
                                        : null;
                                @endphp

                                @if($shop)
                                    <article class="followed-shop-card">
                                        <a href="{{ route('shops.show', $shop) }}" class="followed-shop-card__main">
                                            <span class="followed-shop-card__logo">
                                                @if($shopLogo)
                                                    <img src="{{ $shopLogo }}" alt="{{ $shopName }}">
                                                @else
                                                    <i class="fa-solid fa-store"></i>
                                                @endif
                                            </span>
                                            <span>
                                                <strong>{{ $shopName }}</strong>
                                                <small>{{ $shop->products_count ?? 0 }} active product{{ ($shop->products_count ?? 0) === 1 ? '' : 's' }}</small>
                                            </span>
                                        </a>

                                        <form action="{{ route('shops.unfollow', $shop) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="wishlist-remove-btn">
                                                <i class="fa-solid fa-heart-crack"></i>
                                                Unfollow
                                            </button>
                                        </form>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                    </section>
                @endif

                <div class="wishlist-grid">
                    @foreach($wishlists as $wishlist)
                        @php
                            $product = $wishlist->product;
                            $sellerName = $product?->user?->sellerProfile?->store_name ?? $product?->user?->name ?? 'LocalLift Seller';
                            $imageUrl = $product?->image
                                ? asset('storage/' . $product->image)
                                : asset('assets/images/default-product.png');
                            $averageRating = round((float) ($product?->reviews_avg_rating ?? 0), 1);
                        @endphp

                        @if($product)
                            <article class="wishlist-card">
                                <a href="{{ route('products.show', $product) }}" class="wishlist-card__media">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                                </a>

                                <div class="wishlist-card__body">
                                    <span class="wishlist-card__badge">{{ $product->category?->name ?? 'Uncategorized' }}</span>
                                    <a href="{{ route('products.show', $product) }}" class="wishlist-card__title">
                                        {{ $product->name }}
                                    </a>

                                    <div class="wishlist-card__rating">
                                        <span>
                                            <i class="fa-solid fa-star"></i>
                                            {{ $averageRating > 0 ? number_format($averageRating, 1) : 'New' }}
                                        </span>
                                        <span>{{ $product->reviews_count }} review{{ $product->reviews_count !== 1 ? 's' : '' }}</span>
                                    </div>

                                    <p class="wishlist-card__seller">
                                        <i class="fa-solid fa-store"></i>
                                        {{ $sellerName }}
                                    </p>

                                    <div class="wishlist-card__footer">
                                        <strong>&#8369; {{ number_format($product->price, 2) }}</strong>

                                        <form action="{{ route('buyer.wishlist.destroy', $product) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="wishlist-remove-btn">
                                                <i class="fa-solid fa-heart-crack"></i>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>

                @if($wishlists->hasPages())
                    <div class="wishlist-pagination panel">
                        {{ $wishlists->links() }}
                    </div>
                @endif
            @elseif(($followedShops ?? collect())->count())
                <section class="followed-shops-panel panel">
                    <div class="wishlist-section-head">
                        <div>
                            <span class="section-kicker">Followed Shops</span>
                            <h2>Shops you follow</h2>
                        </div>
                        <a href="{{ route('shops.index') }}" class="action-btn secondary-btn">
                            <i class="fa-solid fa-store"></i>
                            Browse Shops
                        </a>
                    </div>

                    <div class="followed-shops-grid">
                        @foreach($followedShops as $follow)
                            @php
                                $shop = $follow->seller;
                                $shopName = $shop?->sellerProfile?->store_name ?? $shop?->name ?? 'LocalLift Seller';
                                $shopLogo = $shop?->sellerProfile?->shop_logo
                                    ? asset('storage/' . $shop->sellerProfile->shop_logo)
                                    : null;
                            @endphp

                            @if($shop)
                                <article class="followed-shop-card">
                                    <a href="{{ route('shops.show', $shop) }}" class="followed-shop-card__main">
                                        <span class="followed-shop-card__logo">
                                            @if($shopLogo)
                                                <img src="{{ $shopLogo }}" alt="{{ $shopName }}">
                                            @else
                                                <i class="fa-solid fa-store"></i>
                                            @endif
                                        </span>
                                        <span>
                                            <strong>{{ $shopName }}</strong>
                                            <small>{{ $shop->products_count ?? 0 }} active product{{ ($shop->products_count ?? 0) === 1 ? '' : 's' }}</small>
                                        </span>
                                    </a>

                                    <form action="{{ route('shops.unfollow', $shop) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="wishlist-remove-btn">
                                            <i class="fa-solid fa-heart-crack"></i>
                                            Unfollow
                                        </button>
                                    </form>
                                </article>
                            @endif
                        @endforeach
                    </div>
                </section>
            @else
                <div class="wishlist-empty panel">
                    <i class="fa-regular fa-heart"></i>
                    <h2>Your wishlist is empty</h2>
                    <p>Save products you like so you can compare them later.</p>
                    <a href="{{ route('products.index') }}" class="action-btn primary-btn">Explore Products</a>
                </div>
            @endif
        </div>
    </section>
@endsection
