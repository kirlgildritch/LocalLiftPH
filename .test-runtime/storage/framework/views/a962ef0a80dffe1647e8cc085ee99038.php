<?php
    $isLoggedIn = auth()->check();
    $currentUser = $isLoggedIn ? auth()->user() : null;
    $previewItems = $isLoggedIn ? ($miniCartItems ?? collect()) : collect();
    $extraCount = $isLoggedIn ? max(($miniCartCount ?? 0) - $previewItems->count(), 0) : 0;
    $frontendHeaderScript = asset('assets/js/header.js') . '?v=' . @filemtime(public_path('assets/js/header.js'));
?>

<header class="header" data-frontend-header data-product-suggestions-url="<?php echo e(route('products.suggestions')); ?>">
    <div class="container header-main">
        <a href="<?php echo e(url('/')); ?>" class="logo">
            <div class="logo-icon">
                <img src="<?php echo e(asset('assets/image/Logo.png')); ?>" alt="Logo">
            </div>

            <div class="logo-text">
                LocalLift
                <span>PH</span>
            </div>
        </a>

        <button type="button" class="header-menu-toggle" aria-label="Toggle navigation" aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
        </button>

        <form action="<?php echo e(url('/products')); ?>" method="GET" class="search-bar" style="position: relative;">


            <input type="text" id="searchInput" name="search" value="<?php echo e(request('search')); ?>"
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
                    <img src="<?php echo e(asset('storage/' . $currentUser->profile_image)); ?>" alt="Profile"
                        class="buyer-profile-img">
                    <?php    else: ?>
                    <i class="fa-regular fa-user"></i>
                    <?php    endif; ?>
                    <span><?php echo e($currentUser->name); ?></span>
                </button>

                <div class="buyer-profile-menu">
                    <a href="javascript:void(0)" id="openProfileModal">
                        <i class="fa-regular fa-user"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="<?php echo e(route('buyer.orders')); ?>">
                        <i class="fa-solid fa-box"></i>
                        <span>My Orders</span>
                    </a>
                    <a href="<?php echo e(route('buyer.wishlist.index')); ?>">
                        <i class="fa-regular fa-heart"></i>
                        <span>My Wishlist</span>
                    </a>
                    <a href="<?php echo e(route('buyer.addresses')); ?>">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Addresses</span>
                    </a>
                    <a href="<?php echo e(route('seller.center')); ?>" class="seller-link">
                        <i class="fa-solid fa-store"></i>
                        <span>Start Selling</span>
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="logout-btn">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="action-link action-link-muted action-link-desktop">Login</a>
            <a href="<?php echo e(route('register')); ?>" class="action-link action-link-primary action-link-desktop">Sign Up</a>
            <a href="<?php echo e(route('seller.center')); ?>" class="action-link action-link-muted action-link-desktop">Start
                Selling</a>
            <?php endif; ?>

            <?php if (!request()->is('cart')): ?>
            <div class="cart-dropdown">
                <a href="<?php echo e(url('/cart')); ?>" class="cart-trigger" title="Cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <?php    if ($isLoggedIn): ?>
                    <span id="header-cart-badge" class="cart-badge <?php echo e(($cartCount ?? 0) > 0 ? '' : 'is-hidden'); ?>">
                        <?php echo e($cartCount ?? 0); ?>

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
                            <img src="<?php echo e(!empty($item->product?->image) ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png')); ?>"
                                alt="<?php echo e($item->product->name ?? 'Product'); ?>">

                            <div class="cart-preview-info">
                                <p><?php echo e($item->product->name ?? 'Product'); ?></p>
                                <small><?php echo e($item->product->user->name ?? 'LocalLift Seller'); ?></small>
                            </div>

                            <span class="cart-preview-price">
                                &#8369; <?php echo e(number_format($item->product->price ?? 0, 2)); ?>

                            </span>
                        </div>
                        <?php        endforeach; ?>
                        <?php    endif; ?>
                    </div>

                    <div class="cart-menu-footer">
                        <?php    if ($isLoggedIn): ?>
                        <span id="header-cart-preview-count">
                            <?php        if (($miniCartCount ?? 0) > $previewItems->count()): ?>
                            <?php echo e($extraCount); ?> more product<?php echo e($extraCount > 1 ? 's' : ''); ?> in cart
                            <?php        else: ?>
                            <?php echo e($miniCartCount ?? 0); ?> product<?php echo e(($miniCartCount ?? 0) != 1 ? 's' : ''); ?> in cart
                            <?php        endif; ?>
                        </span>
                        <?php    else: ?>
                        <span>Cart preview unavailable</span>
                        <?php    endif; ?>

                        <a href="<?php echo e(route('cart.index')); ?>" class="view-cart-btn">Open Cart</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <nav class="navbar">
            <a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'active' : ''); ?>">Overview</a>
            <a href="<?php echo e(route('shops.index')); ?>"
                class="<?php echo e(request()->routeIs('shops.index') ? 'active' : ''); ?>">Shops</a>
            <a href="<?php echo e(route('products.index')); ?>"
                class="<?php echo e(request()->routeIs('products.index') ? 'active' : ''); ?>">Products</a>
            <a href="<?php echo e(route('about')); ?>" class="<?php echo e(request()->routeIs('about') ? 'active' : ''); ?>">About</a>

            <?php if (!$isLoggedIn): ?>
            <div class="navbar-mobile-actions">
                <a href="<?php echo e(route('login')); ?>" class="action-link action-link-muted">Log In</a>
                <a href="<?php echo e(route('register')); ?>" class="action-link action-link-primary">Sign Up</a>
                <a href="<?php echo e(route('seller.center')); ?>" class="action-link action-link-muted">Start Selling</a>
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


        <form action="<?php echo e(route('buyer.profile.update')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <input type="hidden" name="profile_context" value="modal">

            <div class="profile-image-section">
                <?php    if (!empty($currentUser->profile_image)): ?>
                <img src="<?php echo e(asset('storage/' . $currentUser->profile_image)); ?>" alt="Profile" class="profile-preview">
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
                <input type="text" name="name" id="name" value="<?php echo e(old('name', $currentUser->name)); ?>">
            </div>




            <h4 class="modal-section-title">Change Email and Password</h4>
            <br>
            <hr class="section-line">

            <div class="form-group">
                <label for="current_email">Email</label>
                <input type="email" name="email" id="current_email" value="<?php echo e(old('email', $currentUser->email)); ?>">
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

<script src="<?php echo e($frontendHeaderScript); ?>" defer></script>
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/partials/header.blade.php ENDPATH**/ ?>