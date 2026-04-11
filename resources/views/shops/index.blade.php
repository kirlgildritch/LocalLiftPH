@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/shops.css') }}">


<div class="container">
    <div class="checkout-breadcrumb">
        <a href="#">Home</a>
        <span>&gt;</span>
        <span>Shops</span>
    </div>

    <div class="page-wrapper">
      
        <h1 class="page-title">Shops</h1>

        <div class="content">
            <div class="sidebar">
                <div class="sidebar-box">
                    <h3>Categories</h3>
                    <div class="category-list">
                        <div class="category-item">
                            <div class="left"><span class="dot"></span> All</div>
                            <span class="count">32</span>
                        </div>
                        <div class="category-item">
                            <div class="left"><span class="dot"></span> Food & Drinks</div>
                            <span class="count">8</span>
                        </div>
                        <div class="category-item">
                            <div class="left"><span class="dot"></span> Clothing & Fashion</div>
                            <span class="count">6</span>
                        </div>
                        <div class="category-item">
                            <div class="left"><span class="dot"></span> Handmade Crafts</div>
                            <span class="count">9</span>
                        </div>
                        <div class="category-item">
                            <div class="left"><span class="dot"></span> Accessories</div>
                            <span class="count">5</span>
                        </div>
                        <div class="category-item">
                            <div class="left"><span class="dot"></span> Souvenirs & Gifts</div>
                            <span class="count">4</span>
                        </div>
                    </div>
                </div>

                <div class="sidebar-box">
                    <h3>Sort By</h3>
                    <div class="sort-list">
                        <select>
                            <option>Newest</option>
                            <option>Popular</option>
                            <option>Highest Rated</option>
                        </select>
                    </div>
                </div>

                <div class="sidebar-box">
                    <h3>Search Shops</h3>
                    <input type="text" class="search-shop" placeholder="Search shops...">
                </div>
            </div>

            <div class="main-content">
                <div class="main-top">
                    <div class="main-top-left">
                        <label>Sort By:</label>
                        <select>
                            <option>Newest</option>
                            <option>Popular</option>
                            <option>Highest Rated</option>
                        </select>
                    </div>

                    <div class="view-icons">
                        <i class="fa-solid fa-table-cells-large"></i>
                        <i class="fa-solid fa-list"></i>
                    </div>
                </div>

                <div class="shops-grid">
                    <div class="shop-card">
                        <div class="shop-logo">
                            <img src="{{ asset('assets/images/shop3.png') }}" alt="Likhang Kamay Crafts">
                        </div>
                        <h3>Likhang Kamay Crafts</h3>
                        <div class="shop-rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span>4.9 (150)</span>
                        </div>
                        <div class="shop-products">13 Products</div>
                        <a href="{{ route('shops.show') }}" class="visit-btn">VISIT SHOP</a>
                    </div>

                    <div class="shop-card">
                        <div class="shop-logo">
                            <img src="{{ asset('assets/images/shop1.png') }}" alt="Brew & Beans Café">
                        </div>
                        <h3>Brew & Beans Café</h3>
                        <div class="shop-rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span>4.8 (152)</span>
                        </div>
                        <div class="shop-products">11 Products</div>
                        <a href="{{ url('/shops/1') }}" class="visit-btn">VISIT SHOP</a>
                    </div>

                    <div class="shop-card">
                        <div class="shop-logo">
                            <img src="{{ asset('assets/images/shop2.png') }}" alt="Threads & Style PH">
                        </div>
                        <h3>Sweet Delights</h3>
                        <div class="shop-rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span>4.9 (105)</span>
                        </div>
                        <div class="shop-products">10 Products</div>
                        <a href="{{ url('/shops/2') }}" class="visit-btn">VISIT SHOP</a>
                    </div>

                    <div class="shop-card">
                        <div class="shop-logo">
                            <img src="{{ asset('assets/images/shop4.png') }}" alt="Home Décor Haven">
                        </div>
                        <h3>Home Décor Haven</h3>
                        <div class="shop-rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span>4.9 (135)</span>
                        </div>
                        <div class="shop-products">9 Products</div>
                        <a href="{{ url('/shops/4') }}" class="visit-btn">VISIT SHOP</a>
                    </div>

                    <div class="shop-card">
                        <div class="shop-logo">
                            <img src="{{ asset('assets/images/shop5.png') }}" alt="Nature's Best Essentials">
                        </div>
                        <h3>Nature’s Best Essentials</h3>
                        <div class="shop-rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span>4.9 (75)</span>
                        </div>
                        <div class="shop-products">8 Products</div>
                        <a href="{{ url('/shops/5') }}" class="visit-btn">VISIT SHOP</a>
                    </div>

                    <div class="shop-card">
                        <div class="shop-logo">
                            <img src="{{ asset('assets/images/shop6.png') }}" alt="Nature's Best Essentials">
                        </div>
                        <h3>Nature’s Best Essentials</h3>
                        <div class="shop-rating">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span>4.9 (78)</span>
                        </div>
                        <div class="shop-products">9 Products</div>
                        <a href="{{ url('/shops/6') }}" class="visit-btn">VISIT SHOP</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection