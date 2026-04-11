@extends('layouts.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('assets/css/home.css') }}">
  <section class="hero">
    <div class="container">
      <div class="hero-box">
        <div class="hero-content">
          <div class="small">Support Local, Shop Local</div>
          <h1>Discover Amazing Local Products Near You</h1>
          <p>LocalLift PH connects local businesses with customers in one easy-to-use marketplace.</p>

          <div class="hero-buttons">
            <a href="products.php" class="btn btn-primary">
              <i class="fa-solid fa-bag-shopping"></i> SHOP NOW
            </a>
            <a href="seller_register.php" class="btn btn-outline">
              <i class="fa-solid fa-store"></i> BECOME A SELLER
            </a>
          </div>
        </div>

        <div class="hero-image">
          <img src="{{ asset('assets/image/heroBanner.png') }}" alt="Hero Banner">
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Featured Categories</h2>
        <a href="categories.php" class="view-all">View All Categories <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="categories">
        <a href="products.php?category=food" class="category-card">
          <div class="cat-icon"><i class="fa-solid fa-utensils"></i></div>
          <h4>Food & Drinks</h4>
        </a>

        <a href="products.php?category=clothing" class="category-card">
          <div class="cat-icon"><i class="fa-solid fa-shirt"></i></div>
          <h4>Clothing & Fashion</h4>
        </a>

        <a href="products.php?category=crafts" class="category-card">
          <div class="cat-icon"><i class="fa-solid fa-palette"></i></div>
          <h4>Handmade Crafts</h4>
        </a>

        <a href="products.php?category=accessories" class="category-card">
          <div class="cat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
          <h4>Accessories</h4>
        </a>

        <a href="products.php?category=souvenirs" class="category-card">
          <div class="cat-icon"><i class="fa-solid fa-gift"></i></div>
          <h4>Souvenirs & Gifts</h4>
        </a>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Featured Shops</h2>
        <a href="{{ url('/shops') }}" class="view-all">View All Shops <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="shops">
        <div class="shop-card">
          <div class="shop-logo">
            <img src="assets/shop1.png" alt="Brew & Beans Café">
          </div>
          <div class="shop-info">
            <h3>Brew & Beans Café</h3>
            <p>Food & Drinks</p>
            <div class="rating"><i class="fa-solid fa-star"></i> 4.8 (120)</div>
            <a href="shop_details.php?id=1" class="mini-btn">VISIT SHOP</a>
          </div>
        </div>

        <div class="shop-card">
          <div class="shop-logo">
            <img src="assets/shop2.png" alt="Threads & Style PH">
          </div>
          <div class="shop-info">
            <h3>Threads & Style PH</h3>
            <p>Clothing & Fashion</p>
            <div class="rating"><i class="fa-solid fa-star"></i> 4.7 (98)</div>
            <a href="shop_details.php?id=2" class="mini-btn">VISIT SHOP</a>
          </div>
        </div>

        <div class="shop-card">
          <div class="shop-logo">
            <img src="assets/shop3.png" alt="Likhang Kamay Crafts">
          </div>
          <div class="shop-info">
            <h3>Likhang Kamay Crafts</h3>
            <p>Handmade Crafts</p>
            <div class="rating"><i class="fa-solid fa-star"></i> 4.9 (150)</div>
            <a href="shop_details.php?id=3" class="mini-btn">VISIT SHOP</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <a href="{{ url('/products') }}" class="view-all">View All Products <i class="fa-solid fa-arrow-right"></i></a>
      </div>

      <div class="products">

        <div class="product-card">
          <div class="product-image">
            <img src="assets/product1.png" alt="Banana Chips">
          </div>
          <div class="product-info">
            <h4>Banana Chips</h4>
            <div class="sub">Crispy & Sweet</div>
            <div class="price"><small>₱</small>120.00</div>
            <div class="product-actions">
              <a href="product_details.php?id=1" class="details-btn">VIEW DETAILS</a>
              <a href="cart.php" class="cart-btn"><i class="fa-solid fa-cart-shopping"></i> ADD TO CART</a>
            </div>
          </div>
        </div>

        <div class="product-card">
          <div class="product-image">
            <img src="assets/product2.png" alt="Handwoven Bag">
          </div>
          <div class="product-info">
            <h4>Handwoven Bag</h4>
            <div class="sub">Native Style</div>
            <div class="price"><small>₱</small>350.00</div>
            <div class="product-actions">
              <a href="product_details.php?id=2" class="details-btn">VIEW DETAILS</a>
              <a href="cart.php" class="cart-btn"><i class="fa-solid fa-cart-shopping"></i> ADD TO CART</a>
            </div>
          </div>
        </div>

        <div class="product-card">
          <div class="product-image">
            <img src="assets/product3.png" alt="Beaded Bracelet">
          </div>
          <div class="product-info">
            <h4>Beaded Bracelet</h4>
            <div class="sub">Colorful Design</div>
            <div class="price"><small>₱</small>180.00</div>
            <div class="product-actions">
              <a href="product_details.php?id=3" class="details-btn">VIEW DETAILS</a>
              <a href="cart.php" class="cart-btn"><i class="fa-solid fa-cart-shopping"></i> ADD TO CART</a>
            </div>
          </div>
        </div>

        <div class="product-card">
          <div class="product-image">
            <img src="assets/product4.png" alt="Herbal Soap">
          </div>
          <div class="product-info">
            <h4>Herbal Soap</h4>
            <div class="sub">All-Natural</div>
            <div class="price"><small>₱</small>95.00</div>
            <div class="product-actions">
              <a href="product_details.php?id=4" class="details-btn">VIEW DETAILS</a>
              <a href="cart.php" class="cart-btn"><i class="fa-solid fa-cart-shopping"></i> ADD TO CART</a>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

@endsection