@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/cart.css') }}">

<section class="cart-page">
    <div class="container">
      <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Products</span>
        <span>&gt;</span>
        <span>Cart</span>
    </div>

      
        <div class="cart-wrapper">
            <div class="cart-header">
                <h1>Shopping Cart</h1>
                <div class="free-shipping-note">
                    <span>Amazing! You're eligible for <strong>FREE SHIPPING!</strong></span>
                    <i class="fa-solid fa-circle-check"></i>
                    <i class="fa-solid fa-chevron-right arrow-right"></i>
                </div>
            </div>

          
            <div class="cart-main">
                <div class="cart-table-box">
                    <div class="select-all">
                        <label>
                            <input type="checkbox" checked>
                            <span>Select All</span>
                        </label>
                    </div>

                    <div class="cart-table">
                        <div class="cart-table-head">
                            <div>Select</div>
                            <div>Product</div>
                            <div>Price</div>
                            <div>Subtotal</div>
                        </div>

                        <div class="cart-row">
                            <div class="col-select">
                                <input type="checkbox" checked>
                            </div>

                            <div class="col-product">
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                        <img src="{{ asset('assets/images/cart-item1.png') }}" alt="Beaded Bracelet">
                                    </div>
                                    <div class="cart-product-info">
                                        <h4>Beaded Bracelet</h4>
                                        <p>Likhang Kamay Crafts</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-price">₱180.00</div>

                            <div class="col-subtotal">
                                <div class="qty-box">
                                    <button>-</button>
                                    <input type="text" value="1" readonly>
                                    <button>+</button>
                                </div>
                            </div>
                        </div>

                        <div class="cart-row">
                            <div class="col-select">
                                <input type="checkbox" checked>
                            </div>

                            <div class="col-product">
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                        <img src="{{ asset('assets/images/cart-item2.png') }}" alt="Herbal Soap">
                                    </div>
                                    <div class="cart-product-info">
                                        <h4>Herbal Soap</h4>
                                        <p>Likhang Kamay Crafts</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-price">₱95.00</div>

                            <div class="col-subtotal">
                                <div class="qty-box">
                                    <button>-</button>
                                    <input type="text" value="2" readonly>
                                    <button>+</button>
                                </div>
                            </div>
                        </div>

                        <div class="cart-row">
                            <div class="col-select">
                                <input type="checkbox" checked>
                            </div>

                            <div class="col-product">
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                        <img src="{{ asset('assets/images/cart-item3.png') }}" alt="Banana Chips">
                                    </div>
                                    <div class="cart-product-info">
                                        <h4>Banana Chips</h4>
                                        <p>Brew & Beans Cafe</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-price">₱240.00</div>

                            <div class="col-subtotal">
                                <div class="qty-box">
                                    <button>-</button>
                                    <input type="text" value="2" readonly>
                                    <button>+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="cart-summary">
                    <h3>Cart Summary</h3>

                    <div class="summary-line">
                        <span>Subtotal:</span>
                        <strong>₱515.00</strong>
                    </div>

                    <div class="summary-line">
                        <span>Free Shipping:</span>
                        <strong>₱0.00</strong>
                    </div>

                    <div class="summary-total">
                        <span>Total:</span>
                        <strong>₱515.00</strong>
                    </div>

                    <a href="{{ url('/checkout') }}" class="checkout-btn">CHECKOUT</a>

                    <div class="coupon-box">
                        <input type="text" placeholder="Enter coupon code">
                        <button>Apply</button>
                    </div>
                </aside>
            </div>

            <div class="recommended-section">
                <h2>Recommended Products</h2>

                <div class="recommended-grid">
                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ asset('assets/images/recommend1.png') }}" alt="Woven Tote with Handle">
                        </div>
                        <div class="product-info">
                            <h4>Woven Tote with Handle</h4>
                            <div class="price old-price">₱190.00</div>
                            <div class="product-actions">
                                <a href="{{ url('/products/2') }}" class="btn-view">VIEW</a>
                                <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                            </div>
                        </div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ asset('assets/images/recommend2.png') }}" alt="Beaded Bracelet">
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
                            <img src="{{ asset('assets/images/recommend3.png') }}" alt="Handmade Soap Trio">
                        </div>
                        <div class="product-info">
                            <h4>Handmade Soap Trio</h4>
                            <div class="price">₱290.00</div>
                            <div class="product-actions">
                                <a href="{{ url('/products/5') }}" class="btn-view">VIEW</a>
                                <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                            </div>
                        </div>
                    </div>

                    <div class="product-card">
                        <div class="product-image">
                            <img src="{{ asset('assets/images/recommend4.png') }}" alt="Eco-Friendly Straw Set">
                        </div>
                        <div class="product-info">
                            <h4>Eco-Friendly Straw Set</h4>
                            <div class="price">₱240.00</div>
                            <div class="product-actions">
                                <a href="{{ url('/products/9') }}" class="btn-view">VIEW</a>
                                <a href="{{ url('/cart') }}" class="btn-cart">ADD TO CART</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection