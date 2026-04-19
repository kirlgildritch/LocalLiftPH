@extends('layouts.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">

  <section class="hero">
    <div class="container hero-shell">
      <div class="hero-copy">
        <div class="hero-background-carousel" aria-hidden="true">
          <span class="hero-slide"></span>
          <span class="hero-slide"></span>
          <span class="hero-slide"></span>
        </div>

        <div class="hero-overlay"></div>

        <div class="hero-content">
          <div class="hero-pill">
            <span class="status-dot"></span>
            Independent marketplace for local products and trusted sellers
          </div>

          <h1>
            <span class="hero-accent">LocalLift</span>
            for buyers discovering
            standout local products
          </h1>

          <p>
            Explore curated categories, reliable shops, and product collections designed to help local businesses look
            more premium online.
          </p>

          <div class="hero-actions">
            <a href="{{ url('/products') }}" class="btn btn-primary">
              <i class="fa-solid fa-bag-shopping"></i>
              Explore Products
            </a>

            @auth
              <a href="{{ route('seller.login') }}" class="btn btn-outline">
                <i class="fa-solid fa-store"></i>
                Become a Seller
              </a>
            @else
              <a href="{{ route('seller.login') }}" class="btn btn-outline">
                <i class="fa-solid fa-store"></i>
                Become a Seller
              </a>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <div>
          <span class="section-kicker">Browse</span>
          <h2 class="section-title">Featured Categories</h2>
        </div>
        <a href="{{ route('categories.index') }}" class="view-all">View all categories <i
            class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="categories">
        @forelse($featuredCategories ?? collect() as $category)
          <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-card">
            <div class="cat-icon"><i class="fa-solid {{ $category->icon }}"></i></div>
            <h4>{{ $category->name }}</h4>
            <p>{{ $category->count }} products available in this category.</p>
          </a>
        @empty
          <a href="{{ route('categories.index') }}" class="category-card">
            <div class="cat-icon"><i class="fa-solid fa-grid-2"></i></div>
            <h4>Browse Categories</h4>
            <p>Explore available categories once sellers publish active products.</p>
          </a>
        @endforelse
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <div>
          <span class="section-kicker">Products</span>
          <h2 class="section-title">Featured Products</h2>
        </div>
        <a href="{{ url('/products') }}" class="view-all">View all products <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="products">
        @forelse($featuredProducts ?? collect() as $product)
          <div class="product-card">
            <div class="product-image">
              <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('assets/image/heroBanner.png') }}"
                alt="{{ $product->name }}">
            </div>
            <div class="product-info">
              <span class="product-label">{{ $product->category?->name ?? 'Uncategorized' }}</span>
              <h4>{{ $product->name }}</h4>
              <div class="sub">{{ $product->user->name ?? 'LocalLift Seller' }}</div>
              <div class="price"><small>P</small>{{ number_format($product->price, 2) }}</div>
              <div class="product-actions">
                <a href="{{ route('products.show', $product->id) }}" class="details-btn">View Details</a>

                @auth
                  @if((int) $product->user_id === (int) auth()->id())
                    <span class="mini-cart-btn" aria-disabled="true">
                      <i class="fa-solid fa-store"></i> Your Product
                    </span>
                  @else
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                      @csrf
                      <input type="hidden" name="quantity" value="1">
                      <button type="submit" class="mini-cart-btn">
                        <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                      </button>
                    </form>
                  @endif
                @else
                  <a href="{{ route('login') }}" class="mini-cart-btn"><i class="fa-solid fa-cart-shopping"></i> Add to
                    Cart</a>
                @endauth
              </div>
            </div>
          </div>
        @empty
          <div class="product-card">
            <div class="product-info">
              <span class="product-label">No products yet</span>
              <h4>Featured products will appear here</h4>
              <div class="sub">Active seller listings will automatically populate this section.</div>
            </div>
          </div>
        @endforelse
      </div>
    </div>
  </section>
@endsection
