@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/product_details.css') }}">
 
<section class="details-page">
    <div class="container">
        <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Products</span>
         <span>&gt;</span>
        <span>Product Details</span>
    </div>

      <div class="details-wrapper">
        <div class="product-main">
          <div class="product-image-box">
            <img src="assets/images/beaded-bracelet.png" alt="Beaded Bracelet">
          </div>

          <div class="product-description">
            <h2>About This Product</h2>
            <h3>Description</h3>

            <ul>
              <li>Handmade beaded bracelet with colorful, vibrant design</li>
              <li>Made from high-quality, assorted beads including wooden and glass beads</li>
              <li>Stretchable, fits most wrist sizes comfortably</li>
              <li>Perfect for adding a pop of color and style to any outfit</li>
            </ul>
          </div>
        </div>

        <div class="product-side">
          <h1>Beaded Bracelet</h1>
          <p class="subtitle">Colorful Design</p>

          <div class="divider"></div>

          <div class="price">P 180.00</div>

          <div class="quantity-box">
            <button>-</button>
            <input type="text" value="1" readonly>
            <button>+</button>
          </div>

          <div class="action-row">
            <a href="cart.php" class="add-cart-btn">ADD TO CART</a>

            <div class="icon-actions">
              <button><i class="fa-solid fa-heart"></i></button>
              <button><i class="fa-regular fa-heart"></i></button>
            </div>
          </div>

          <div class="wishlist-link">
            <i class="fa-solid fa-heart"></i>
            <span>Add to Wishlist</span>
          </div>

          <div class="divider"></div>

          <div class="shop-box">
            <div class="shop-logo">
              <img src="assets/images/shop-likhang.png" alt="Likhang Kamay Crafts">
            </div>

            <div class="shop-info">
              <h4>Likhang Kamay Crafts</h4>
              <div class="shop-rating">
                <span class="stars">★★★★★</span>
                <span class="rating-text">4.9 (150)</span>
              </div>
              <a href="shop_details.php" class="view-shop">View Shop</a>
            </div>
          </div>

          <div class="divider"></div>
        </div>
      </div>

      <div class="related-section">
        <h2>Related Products</h2>

        <div class="related-grid">
          <div class="related-card">
            <div class="related-image">
              <img src="assets/images/related1.png" alt="Herbal Soap">
            </div>
            <div class="related-info">
              <h4>Herbal Soap</h4>
              <p>Likhang Kamay Crafts</p>
              <div class="related-price">P 95.00</div>
              <div class="related-actions">
                <a href="#" class="btn-view">VIEW</a>
                <a href="#" class="btn-cart">ADD TO CART</a>
              </div>
            </div>
          </div>

          <div class="related-card">
            <div class="related-image">
              <img src="assets/images/related2.png" alt="Banana Chips">
            </div>
            <div class="related-info">
              <h4>Banana Chips</h4>
              <p>Brew & Beans Café</p>
              <div class="related-price">P 120.00</div>
              <div class="related-actions">
                <a href="#" class="btn-view">VIEW</a>
                <a href="#" class="btn-cart">ADD TO CART</a>
              </div>
            </div>
          </div>

          <div class="related-card">
            <div class="related-image">
              <img src="assets/images/related3.png" alt="Handwoven Bag">
            </div>
            <div class="related-info">
              <h4>Handwoven Bag</h4>
              <p>Threads & Style PH</p>
              <div class="related-price">P 360.00</div>
              <div class="related-actions">
                <a href="#" class="btn-view">VIEW</a>
                <a href="#" class="btn-cart">ADD TO CART</a>
              </div>
            </div>
          </div>

          <div class="related-card">
            <div class="related-image">
              <img src="assets/images/related4.png" alt="Handmade Necklace">
            </div>
            <div class="related-info">
              <h4>Handmade Necklace</h4>
              <p>Likhang Kamay Crafts</p>
              <div class="related-price">P 220.00</div>
              <div class="related-actions">
                <a href="#" class="btn-view">VIEW</a>
                <a href="#" class="btn-cart">ADD TO CART</a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
@endsection