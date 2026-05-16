@php
    $isLoggedIn = auth()->check();
    $currentUser = $isLoggedIn ? auth()->user() : null;
    $previewItems = $isLoggedIn ? ($miniCartItems ?? collect()) : collect();
    $extraCount = $isLoggedIn ? max(($miniCartCount ?? 0) - $previewItems->count(), 0) : 0;
    $frontendHeaderScript = asset('assets/js/header.js') . '?v=' . @filemtime(public_path('assets/js/header.js'));
@endphp

<header class="header" data-frontend-header data-product-suggestions-url="{{ route('products.suggestions') }}">
    <div class="container header-main">
        <a href="{{ url('/') }}" class="logo">
            <div class="logo-icon">
                <img src="{{ asset('assets/image/Logo.png') }}" alt="Logo">
            </div>

            <div class="logo-text">
                LocalLift
                <span>PH</span>
            </div>
        </a>

        <button type="button" class="header-menu-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <form action="{{ url('/products') }}" method="GET" class="search-bar" style="position: relative;">


            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                placeholder="Search for products, shops, and more..." title="Search" autocomplete="off">

            <button type="button" id="searchClearButton" class="search-clear-btn is-hidden" title="Clear search"
                aria-label="Clear search">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <button type="submit" class="search-btn" title="Search" aria-label="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <div id="searchSuggestions" class="search-suggestions"></div>
        </form>

        <div class="header-actions">
            <?php if ($isLoggedIn): ?>
            <div class="buyer-profile-dropdown">
                <button type="button" class="profile-trigger buyer-profile-btn">
                    <?php    if (!empty($currentUser->profile_image)): ?>
                    <img src="{{ asset('storage/' . $currentUser->profile_image) }}" alt="Profile"
                        class="buyer-profile-img">
                    <?php    else: ?>
                    <i class="fa-regular fa-user"></i>
                    <?php    endif; ?>
                    <span>{{ $currentUser->name }}</span>
                </button>

                <div class="buyer-profile-menu">
                    <a href="javascript:void(0)" id="openProfileModal">
                        <i class="fa-regular fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('buyer.orders') }}">
                        <i class="fa-solid fa-box"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="{{ route('buyer.wishlist.index') }}">
                        <i class="fa-regular fa-heart"></i>
                        <span>My Wishlist</span>
                    </a>
                    <a href="{{ route('buyer.addresses') }}">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Addresses</span>
                    </a>
                    <a href="{{ route('seller.center') }}" class="seller-link">
                        <i class="fa-solid fa-store"></i>
                        <span>Start Selling</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <a href="{{ route('login') }}" class="action-link action-link-muted action-link-desktop">Login</a>
            <a href="{{ route('register') }}" class="action-link action-link-primary action-link-desktop">Sign Up</a>
            <a href="{{ route('seller.center') }}" class="action-link action-link-muted action-link-desktop">Start
                Selling</a>
            <?php endif; ?>

            <?php if (!request()->is('cart')): ?>
            <div class="cart-dropdown">
                <a href="{{ url('/cart') }}" class="cart-trigger" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php    if ($isLoggedIn): ?>
                    <span id="header-cart-badge" class="cart-badge {{ ($cartCount ?? 0) > 0 ? '' : 'is-hidden' }}">
                        {{ $cartCount ?? 0 }}
                    </span>
                    <?php    endif; ?>
                </a>

                <div class="cart-menu">
                    <div class="cart-menu-header">
                        <h4>Recently Added Products</h4>
                    </div>

                    <div class="cart-preview-list" id="header-cart-preview-list">
                        <?php    if (!$isLoggedIn): ?>
                        <div class="cart-preview-empty" style="color: gray;">
                            <p>&ensp;&ensp;Please log in to view your cart.</p>
                        </div>
                        <?php    elseif ($previewItems->isEmpty()): ?>
                        <div class="cart-preview-empty">
                            <p>Your cart is empty.</p>
                        </div>
                        <?php    else: ?>
                        <?php        foreach ($previewItems as $item): ?>
                        <div class="cart-preview-item">
                            <img src="{{ !empty($item->product?->image) ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                alt="{{ $item->product->name ?? 'Product' }}">

                            <div class="cart-preview-info">
                                <p>{{ $item->product->name ?? 'Product' }}</p>
                                <small>{{ $item->product->user->name ?? 'LocalLift Seller' }}</small>
                            </div>

                            <span class="cart-preview-price">
                                &#8369; {{ number_format($item->product->price ?? 0, 2) }}
                            </span>
                        </div>
                        <?php        endforeach; ?>
                        <?php    endif; ?>
                    </div>

                    <div class="cart-menu-footer">
                        <?php    if ($isLoggedIn): ?>
                        <span id="header-cart-preview-count">
                            <?php        if (($miniCartCount ?? 0) > $previewItems->count()): ?>
                            {{ $extraCount }} more product{{ $extraCount > 1 ? 's' : '' }} in cart
                            <?php        else: ?>
                            {{ $miniCartCount ?? 0 }} product{{ ($miniCartCount ?? 0) != 1 ? 's' : '' }} in cart
                            <?php        endif; ?>
                        </span>
                        <?php    else: ?>
                        <span>Cart preview unavailable</span>
                        <?php    endif; ?>

                        <a href="{{ route('cart.index') }}" class="view-cart-btn">Open Cart</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <nav class="navbar">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Overview</a>
            <a href="{{ route('shops.index') }}"
                class="{{ request()->routeIs('shops.index') ? 'active' : '' }}">Shops</a>
            <a href="{{ route('products.index') }}"
                class="{{ request()->routeIs('products.index') ? 'active' : '' }}">Products</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>

            <?php if (!$isLoggedIn): ?>
            <div class="navbar-mobile-actions">
                <a href="{{ route('login') }}" class="action-link action-link-muted">Log In</a>
                <a href="{{ route('register') }}" class="action-link action-link-primary">Sign Up</a>
                <a href="{{ route('seller.center') }}" class="action-link action-link-muted">Start Selling</a>
            </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php if ($isLoggedIn): ?>
<div class="profile-modal-overlay" id="profileModal">
    <div class="profile-modal">
        <div class="header-modal">
            <button class="close-modal" id="closeProfileModal">&times;</button>
            <h2>My Profile</h2>
            <div class="divider"></div>
        </div>


        <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="profile_context" value="modal">

            <div class="profile-image-section">
                <?php    if (!empty($currentUser->profile_image)): ?>
                <img src="{{ asset('storage/' . $currentUser->profile_image) }}" alt="Profile" class="profile-preview">
                <?php    else: ?>
                <i class="fa-regular fa-circle-user default-profile-icon"></i>
                <?php    endif; ?>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" name="profile_image" id="profile_image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $currentUser->name) }}">
            </div>




            <h4 class="modal-section-title">Change Email and Password</h4>
            <br>
            <hr class="section-line">

            <div class="form-group">
                <label for="current_email">Email</label>
                <input type="email" name="email" id="current_email" value="{{ old('email', $currentUser->email) }}">
            </div>

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password">
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" id="password">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>

            <button type="submit" class="save-btn">Update Profile</button>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="{{ $frontendHeaderScript }}" defer></script>
