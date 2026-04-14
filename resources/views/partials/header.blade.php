<div class="topbar">
    <div class="container">
        <div class="topbar-left">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-tiktok"></i></a>
            <a href="#"><i class="fa-solid fa-envelope"></i></a>
        </div>

        <div class="topbar-center">
            <i class="fa-solid fa-truck"></i>
            <span>FREE SHIPPING THIS WEEK! ORDER OVER ₱500</span>
        </div>

        <div class="topbar-right">
            <span>ENGLISH <i class="fa-solid fa-chevron-down"></i></span>
        </div>
    </div>
</div>

<header class="header">
    <div class="container header-main">
        <a href="{{ url('/') }}" class="logo">
            <div class="logo-icon">
                <i class="fa-solid fa-location-dot"></i>
            </div>

            <div class="logo-text">
                LocalLift
                <span>PH</span>
            </div>
        </a>

        <form action="{{ url('/products') }}" method="GET" class="search-bar">
            <input type="text" name="search" placeholder="Search for products, shops, and more...">
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

                <div class="header-icons">

            @auth
                <!-- Profile -->
                <a href="{{ url('/profile') }}" class="icon-box">
                    <i class="fa-regular fa-user"></i>
                    <span>{{ Auth::user()->name }}</span>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="icon-box logout-btn">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <!-- Login -->
                <a href="{{ route('login') }}" class="icon-box">
                    <i class="fa-regular fa-user"></i>
                    <span>Login</span>
                </a>

                <!-- Register -->
                <a href="{{ route('register') }}" class="icon-box">
                    <i class="fa-regular fa-id-badge"></i>
                    <span>Register</span>
                </a>
            @endauth

            <!-- Wishlist -->
            <a href="#" class="icon-box">
                <i class="fa-regular fa-heart"></i>
                <span>Wishlist</span>
            </a>

            <!-- Cart -->
            <a href="{{ url('/cart') }}" class="icon-box">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Cart</span>
            </a>

        </div>
    </div>
</header>

<nav class="navbar">
    <div class="container">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">HOME</a>
        <a href="{{ route('shops.index') }}" class="{{ request()->routeIs('shops.index') ? 'active' : '' }}">SHOPS</a>
        <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">PRODUCTS</a>
        <a href="#">ABOUT</a>
        
        @auth
            @if(auth()->user()->is_seller)
                <a href="{{ route('seller.dashboard') }}" class="seller-link">
                    <i class="fa-solid fa-store"></i>SELLER DASHBOARD
                </a>
            @else
                <a href="{{ route('seller.setup') }}" class="seller-link">
                    <i class="fa-solid fa-store"></i>BECOME A SELLER
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="seller-link">
                <i class="fa-solid fa-store"></i>BECOME A SELLER
            </a>
        @endauth
      
    </div>
</nav>