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
              <a href="{{ route('seller.center') }}" class="btn btn-outline">
                <i class="fa-solid fa-store"></i>
                Become a Seller
              </a>
            @else
              <a href="{{ route('seller.center') }}" class="btn btn-outline">
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
        <div class="featured-products-header-actions">
          <div class="featured-products-nav" aria-label="Featured products navigation">
            <button type="button" class="featured-products-arrow" data-featured-products-prev
              aria-label="Scroll featured products left">
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="featured-products-arrow" data-featured-products-next
              aria-label="Scroll featured products right">
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
          <a href="{{ url('/products') }}" class="view-all">View all products <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>

      <div class="featured-products-shell">
        <div class="products-carousel" data-featured-products-track>
          <div class="products product-card-grid">
            @forelse($featuredProducts ?? collect() as $product)
              <x-product-card :product="$product" :fallback-image="asset('assets/image/heroBanner.png')"
                card-class="featured-product-card" />
            @empty
              <div class="market-product-card market-product-card--empty featured-product-card">
                <div class="market-product-card__body">
                  <span class="market-product-card__badge">No products yet</span>
                  <h4 class="market-product-card__title">Featured products will appear here</h4>
                  <p class="market-product-card__subtitle">Active seller listings will automatically populate this section.
                  </p>
                </div>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const track = document.querySelector('[data-featured-products-track]');
      const prevButton = document.querySelector('[data-featured-products-prev]');
      const nextButton = document.querySelector('[data-featured-products-next]');

      if (!track || !prevButton || !nextButton) {
        return;
      }

      const getScrollAmount = () => {
        const firstCard = track.querySelector('.featured-product-card');
        const list = track.querySelector('.products');
        if (!firstCard) {
          return Math.max(track.clientWidth * 0.85, 240);
        }

        const cardWidth = firstCard.getBoundingClientRect().width;
        const listStyles = list ? window.getComputedStyle(list) : null;
        const gap = listStyles ? parseFloat(listStyles.columnGap || listStyles.gap) || 16 : 16;
        return Math.round(cardWidth + gap);
      };

      const updateButtons = () => {
        const maxScrollLeft = track.scrollWidth - track.clientWidth;
        const atStart = track.scrollLeft <= 4;
        const atEnd = track.scrollLeft >= maxScrollLeft - 4;

        prevButton.disabled = atStart;
        nextButton.disabled = atEnd;
      };

      prevButton.addEventListener('click', function () {
        track.scrollBy({ left: -getScrollAmount(), behavior: 'smooth' });
      });

      nextButton.addEventListener('click', function () {
        track.scrollBy({ left: getScrollAmount(), behavior: 'smooth' });
      });

      track.addEventListener('scroll', updateButtons, { passive: true });
      window.addEventListener('resize', updateButtons);
      updateButtons();
    });
  </script>
@endsection