@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/shop_details.css') }}">



<section class="shop-page">
    <div class="container">
          <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Shops</span>
        <span>&gt;</span>
        <span>Shop Details</span>
    </div>

        <div class="shop-wrapper">
            <div class="shop-top">
                <div class="shop-cover"></div>

                <div class="shop-top-content">
                    <div class="shop-logo-box">
                        <img src="{{ asset('assets/images/shop-logo.png') }}" alt="Likhang Kamay Crafts">
                    </div>

                    <div class="shop-main-info">
                        <h1>Likhang Kamay Crafts</h1>

                        <div class="shop-rating-row">
                            <span class="stars">★★★★★</span>
                            <span class="rating-score">4.9</span>
                            <span class="rating-count">(150)</span>
                            <span class="verified"><i class="fa-solid fa-check"></i></span>
                        </div>

                        <div class="shop-meta">
                            <span>13 Products</span>
                            <span>|</span>
                            <span>Member Since -2025</span>
                        </div>

                        <div class="shop-location">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Pampanga, Philippines</span>
                        </div>

                        <p class="shop-description">
                            Likhang Kamay Crafts offers handmade, artisanal items crafted with care using locally
                            sourced materials. Discover unique and beautiful accessories, home decor, and
                            personal care products.
                        </p>
                    </div>
                </div>
            </div>

            <div class="shop-content">
                <aside class="shop-sidebar">
                    <div class="shop-name-card">
                        <h2>Likhang Kamay<br>Crafts</h2>

                        <div class="shop-sidebar-actions">
                            <a href="#" class="visit-btn">Visit Website</a>
                            <button class="send-btn"><i class="fa-regular fa-paper-plane"></i></button>
                        </div>
                    </div>

                    <div class="filter-card">
                        <h3>CATEGORIES</h3>

                        <div class="category-list">
                            <div class="category-item active">
                                <div class="category-left">
                                    <span class="radio-dot active-dot"></span>
                                    <span>All (13)</span>
                                </div>
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>

                            <div class="category-item">
                                <div class="category-left">
                                    <span class="radio-dot"></span>
                                    <span>Handmade Necklaces</span>
                                </div>
                            </div>

                            <div class="category-item">
                                <div class="category-left">
                                    <span class="radio-dot"></span>
                                    <span>Bracelets</span>
                                </div>
                                <span class="count">3</span>
                            </div>

                            <div class="category-item">
                                <div class="category-left">
                                    <span class="radio-dot"></span>
                                    <span>Soaps & Skincare</span>
                                </div>
                                <span class="count">4</span>
                            </div>

                            <div class="category-item">
                                <div class="category-left">
                                    <span class="radio-dot"></span>
                                    <span>Local Baskets</span>
                                </div>
                                <span class="count">2</span>
                            </div>
                        </div>
                    </div>

                    <div class="filter-simple">
                        <h3>SORT BY</h3>
                        <select>
                            <option>Newest</option>
                            <option>Price Low-High</option>
                            <option>Price High-Low</option>
                        </select>
                    </div>
                </aside>

                <main class="shop-products-area">
                    <div class="tabs-row">
                        <a href="#" class="tab-link active">PRODUCTS</a>
                        <a href="#" class="tab-link">ABOUT</a>
                        <a href="#" class="tab-link">REVIEWS</a>
                    </div>

                    <div class="products-header">
                        <h2>Products</h2>

                        <div class="products-sort">
                            <label>Sort By:</label>
                            <select>
                                <option>Newest</option>
                                <option>Popular</option>
                            </select>
                        </div>
                    </div>

                    <div class="product-grid">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('assets/images/shop-product1.png') }}" alt="Handmade Necklace">
                            </div>
                            <div class="product-info">
                                <h4>Handmade Necklace</h4>
                                <div class="price">₱220.00</div>
                                <div class="product-actions">
                                    <a href="{{ url('/products/6') }}" class="btn-view">VIEW</a>
                                    <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('assets/images/shop-product2.png') }}" alt="Beaded Bracelet">
                            </div>
                            <div class="product-info">
                                <h4>Beaded Bracelet</h4>
                                <div class="price">₱180.00</div>
                                <div class="product-actions">
                                    <a href="{{ url('/products/3') }}" class="btn-view">VIEW</a>
                                    <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('assets/images/shop-product3.png') }}" alt="Herbal Soap">
                            </div>
                            <div class="product-info">
                                <h4>Herbal Soap</h4>
                                <div class="price">₱95.00</div>
                                <div class="product-actions">
                                    <a href="{{ url('/products/4') }}" class="btn-view">VIEW</a>
                                    <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('assets/images/shop-product4.png') }}" alt="Woven Tote with Handle">
                            </div>
                            <div class="product-info">
                                <h4>Woven Tote with Handle</h4>
                                <div class="price">₱250.00</div>
                                <div class="product-actions">
                                    <a href="{{ url('/products/2') }}" class="btn-view">VIEW</a>
                                    <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('assets/images/shop-product5.png') }}" alt="Handmade Soap Trio">
                            </div>
                            <div class="product-info">
                                <h4>Handmade Soap Trio</h4>
                                <div class="price">₱250.00</div>
                                <div class="product-actions">
                                    <a href="{{ url('/products/5') }}" class="btn-view">VIEW</a>
                                    <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ asset('assets/images/shop-product6.png') }}" alt="Eco-Friendly Straw Set">
                            </div>
                            <div class="product-info">
                                <h4>Eco-Friendly Straw Set</h4>
                                <div class="price">₱120.00</div>
                                <div class="product-actions">
                                    <a href="{{ url('/products/9') }}" class="btn-view">VIEW</a>
                                    <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</section>
@endsection