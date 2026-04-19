@php
    $isLoggedIn = auth()->check();
    $currentUser = $isLoggedIn ? auth()->user() : null;
    $previewItems = $isLoggedIn ? ($miniCartItems ?? collect()) : collect();
    $extraCount = $isLoggedIn ? max(($miniCartCount ?? 0) - $previewItems->count(), 0) : 0;
    $messagePreviews = $messagePreviewConversations ?? collect();
@endphp

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

        <form action="{{ url('/products') }}" method="GET" class="search-bar" style="position: relative;">
            <i class="fa-solid fa-magnifying-glass"></i>

            <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                placeholder="Search for products, shops, and more..." title="Search" autocomplete="off">

            <button type="submit" class="search-btn" title="Search">Search</button>

            <div id="searchSuggestions" class="search-suggestions"></div>
        </form>

        <div class="header-actions">
            <?php if ($isLoggedIn): ?>
                <div class="message-dropdown">
                    <a href="{{ route('messages.index') }}" class="message-trigger action-link" title="Messages">
                        <i class="fa-regular fa-envelope"></i>
                        <span>Messages</span>
                        <span class="cart-badge {{ ($messageConversationCount ?? 0) > 0 ? '' : 'is-hidden' }}">
                            {{ $messageConversationCount ?? 0 }}
                        </span>
                    </a>

                    <div class="message-menu">
                        <div class="cart-menu-header">
                            <h4>Conversations</h4>
                        </div>

                        <div class="message-preview-list">
                            <?php if ($messagePreviews->isEmpty()): ?>
                                <div class="cart-preview-empty">
                                    <p>No conversations yet.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($messagePreviews as $conversation): ?>
                                    @php($otherParticipant = $conversation->otherParticipant($currentUser))
                                    <a href="{{ route('messages.show', $conversation) }}" class="message-preview-item">
                                        <span class="message-preview-avatar">
                                            {{ strtoupper(substr($otherParticipant->name ?? 'LL', 0, 2)) }}
                                        </span>

                                        <div class="message-preview-copy">
                                            <strong>{{ $otherParticipant->name ?? 'Conversation' }}</strong>
                                            <p>{{ \Illuminate\Support\Str::limit(optional($conversation->latestMessage)->message ?? 'Start chatting with this seller.', 44) }}</p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="cart-menu-footer">
                            <span>{{ $messageConversationCount ?? 0 }} conversation{{ ($messageConversationCount ?? 0) !== 1 ? 's' : '' }}</span>
                            <a href="{{ route('messages.index') }}" class="view-cart-btn">Open Inbox</a>
                        </div>
                    </div>
                </div>

                <div class="buyer-profile-dropdown">
                    <button type="button" class="profile-trigger buyer-profile-btn">
                        <?php if (!empty($currentUser->profile_image)): ?>
                            <img src="{{ asset('storage/' . $currentUser->profile_image) }}" alt="Profile" class="buyer-profile-img">
                        <?php else: ?>
                            <i class="fa-regular fa-user"></i>
                        <?php endif; ?>
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
                        <?php if ($currentUser->isSeller()): ?>
                            <a href="{{ route('seller.dashboard') }}" class="seller-link">
                                <i class="fa-solid fa-store"></i>
                                <span>Seller Dashboard</span>
                            </a>
                        <?php else: ?>
                            <a href="{{ route('seller.setup') }}" class="seller-link">
                                <i class="fa-solid fa-store"></i>
                                <span>Start Selling</span>
                            </a>
                        <?php endif; ?>
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
                <a href="{{ route('login') }}" class="action-link action-link-muted">Log In</a>
                <a href="{{ route('register') }}" class="action-link action-link-primary">Create Account</a>
            <?php endif; ?>

            <?php if (!request()->is('cart')): ?>
                <div class="cart-dropdown">
                    <a href="{{ url('/cart') }}" class="cart-trigger" title="Cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Cart</span>
                        <?php if ($isLoggedIn): ?>
                            <span id="header-cart-badge" class="cart-badge {{ ($cartCount ?? 0) > 0 ? '' : 'is-hidden' }}">
                                {{ $cartCount ?? 0 }}
                            </span>
                        <?php endif; ?>
                    </a>

                    <div class="cart-menu">
                        <div class="cart-menu-header">
                            <h4>Recently Added Products</h4>
                        </div>

                        <div class="cart-preview-list" id="header-cart-preview-list">
                            <?php if (!$isLoggedIn): ?>
                                <div class="cart-preview-empty" style="color: gray;">
                                    <p>&ensp;&ensp;Please log in to view your cart.</p>
                                </div>
                            <?php elseif ($previewItems->isEmpty()): ?>
                                <div class="cart-preview-empty">
                                    <p>Your cart is empty.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($previewItems as $item): ?>
                                    <div class="cart-preview-item">
                                        <img src="{{ !empty($item->product?->image) ? asset('storage/' . $item->product->image) : asset('assets/images/default-product.png') }}"
                                            alt="{{ $item->product->name ?? 'Product' }}">

                                        <div class="cart-preview-info">
                                            <p>{{ $item->product->name ?? 'Product' }}</p>
                                            <small>{{ $item->product->user->name ?? 'LocalLift Seller' }}</small>
                                        </div>

                                        <span class="cart-preview-price">
                                            P{{ number_format($item->product->price ?? 0, 2) }}
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="cart-menu-footer">
                            <?php if ($isLoggedIn): ?>
                                <span id="header-cart-preview-count">
                                    <?php if (($miniCartCount ?? 0) > $previewItems->count()): ?>
                                        {{ $extraCount }} more product{{ $extraCount > 1 ? 's' : '' }} in cart
                                    <?php else: ?>
                                        {{ $miniCartCount ?? 0 }} product{{ ($miniCartCount ?? 0) != 1 ? 's' : '' }} in cart
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span>Cart preview unavailable</span>
                            <?php endif; ?>

                            <a href="{{ route('cart.index') }}" class="view-cart-btn">Open Cart</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <nav class="navbar">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Overview</a>
            <a href="{{ route('shops.index') }}" class="{{ request()->routeIs('shops.index') ? 'active' : '' }}">Shops</a>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.index') ? 'active' : '' }}">Products</a>
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
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

            @if(session('success'))
                <p class="success-message">{{ session('success') }}</p>
            @endif

            <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="profile-image-section">
                    <?php if (!empty($currentUser->profile_image)): ?>
                        <img src="{{ asset('storage/' . $currentUser->profile_image) }}" alt="Profile" class="profile-preview">
                    <?php else: ?>
                        <i class="fa-regular fa-circle-user default-profile-icon"></i>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" accept="image/*">
                </div>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $currentUser->name) }}">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $currentUser->email) }}">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $currentUser->phone ?? '') }}">
                </div>

                <div class="form-group address-group">
                    <div class="address-label-row">
                        <label for="address">Address</label>
                        <a href="{{ route('buyer.addresses') }}" class="edit-address-link">Edit</a>
                    </div>

                    <textarea id="address" rows="3" readonly>
    {{ $defaultAddress
        ? trim(
            ($defaultAddress->street_address ?? '') .
            ', ' . ($defaultAddress->barangay ?? '') .
            ', ' . ($defaultAddress->city ?? '') .
            ', ' . ($defaultAddress->province ?? '') .
            ', ' . ($defaultAddress->region ?? '') .
            ($defaultAddress->postal_code ? ', ' . $defaultAddress->postal_code : ''),
            ', '
        )
        : 'No address added yet.'
    }}
                    </textarea>
                </div>

                <h4 class="modal-section-title">Change Email and Password</h4>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isMobileViewport = () => window.matchMedia('(max-width: 640px)').matches;
        const mobileProfileDropdown = { container: '.buyer-profile-dropdown', trigger: '.profile-trigger' };
        const mobileDropdowns = [mobileProfileDropdown];
        const desktopHoverDropdowns = [
            { container: '.message-dropdown', menu: '.message-menu' },
            { container: '.cart-dropdown', menu: '.cart-menu' },
            { container: '.buyer-profile-dropdown', menu: '.buyer-profile-menu' },
        ];
        const hoverCloseDelay = 180;
        const hoverTimers = new Map();

        const clearHoverTimer = (dropdown) => {
            const timer = hoverTimers.get(dropdown);
            if (timer) {
                window.clearTimeout(timer);
                hoverTimers.delete(dropdown);
            }
        };

        const openDesktopDropdown = (dropdown) => {
            clearHoverTimer(dropdown);
            dropdown.classList.add('is-hover-open');
        };

        const queueDesktopDropdownClose = (dropdown) => {
            clearHoverTimer(dropdown);
            const timer = window.setTimeout(() => {
                dropdown.classList.remove('is-hover-open');
                hoverTimers.delete(dropdown);
            }, hoverCloseDelay);

            hoverTimers.set(dropdown, timer);
        };

        mobileDropdowns.forEach(({ container, trigger }) => {
            const dropdown = document.querySelector(container);
            const triggerElement = dropdown ? dropdown.querySelector(trigger) : null;

            if (!dropdown || !triggerElement) {
                return;
            }

            triggerElement.addEventListener('click', function (event) {
                if (!isMobileViewport()) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                mobileDropdowns.forEach(({ container: otherContainer }) => {
                    const otherDropdown = document.querySelector(otherContainer);
                    if (otherDropdown && otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('is-open');
                    }
                });

                dropdown.classList.toggle('is-open');
            });
        });

        desktopHoverDropdowns.forEach(({ container, menu }) => {
            const dropdown = document.querySelector(container);
            const menuElement = dropdown ? dropdown.querySelector(menu) : null;

            if (!dropdown || !menuElement) {
                return;
            }

            const bindOpen = () => {
                if (isMobileViewport()) {
                    return;
                }

                openDesktopDropdown(dropdown);
            };

            const bindClose = () => {
                if (isMobileViewport()) {
                    return;
                }

                queueDesktopDropdownClose(dropdown);
            };

            dropdown.addEventListener('mouseenter', bindOpen);
            dropdown.addEventListener('mouseleave', bindClose);
            menuElement.addEventListener('mouseenter', bindOpen);
            menuElement.addEventListener('mouseleave', bindClose);
        });

        ['.message-dropdown', '.cart-dropdown'].forEach((container) => {
            const dropdown = document.querySelector(container);
            if (!dropdown) {
                return;
            }

            const trigger = dropdown.querySelector('a');
            if (!trigger) {
                return;
            }

            trigger.addEventListener('click', function () {
                if (!isMobileViewport()) {
                    return;
                }

                mobileDropdowns.forEach(({ container: otherContainer }) => {
                    const otherDropdown = document.querySelector(otherContainer);
                    if (otherDropdown) {
                        otherDropdown.classList.remove('is-open');
                    }
                });
            });
        });

        document.addEventListener('click', function (event) {
            if (!isMobileViewport()) {
                return;
            }

            mobileDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);
                if (dropdown && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('is-open');
                }
            });
        });

        window.addEventListener('resize', function () {
            if (!isMobileViewport()) {
                mobileDropdowns.forEach(({ container }) => {
                    const dropdown = document.querySelector(container);
                    if (dropdown) {
                        dropdown.classList.remove('is-open');
                    }
                });

                return;
            }

            desktopHoverDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);
                if (dropdown) {
                    dropdown.classList.remove('is-hover-open');
                    clearHoverTimer(dropdown);
                }
            });
        });

        const openBtn = document.getElementById('openProfileModal');
        const closeBtn = document.getElementById('closeProfileModal');
        const modal = document.getElementById('profileModal');

        if (openBtn && modal) {
            openBtn.addEventListener('click', function () {
                modal.classList.add('show');
                document.body.classList.add('modal-open');
            });
        }

        if (closeBtn && modal) {
            closeBtn.addEventListener('click', function () {
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            });
        }

        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const suggestionsBox = document.getElementById('searchSuggestions');

        if (!searchInput || !suggestionsBox) return;

        searchInput.addEventListener('input', async function () {
            const query = this.value.trim();

            if (query.length < 1) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
                return;
            }

            try {
                const response = await fetch(`/products/suggestions?q=${encodeURIComponent(query)}`);
                const suggestions = await response.json();

                if (!suggestions.length) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.style.display = 'none';
                    return;
                }

                suggestionsBox.innerHTML = suggestions.map(item => `
                <div class="suggestion-item">${item}</div>
            `).join('');

                suggestionsBox.style.display = 'block';

                document.querySelectorAll('.suggestion-item').forEach(item => {
                    item.addEventListener('click', function () {
                        searchInput.value = this.textContent;
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.style.display = 'none';
                        searchInput.form.submit();
                    });
                });
            } catch (error) {
                console.error(error);
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
            }
        });

        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cartTrigger = document.querySelector('.cart-trigger');
        const previewList = document.getElementById('header-cart-preview-list');
        const previewCount = document.getElementById('header-cart-preview-count');
        const cartBadge = document.getElementById('header-cart-badge');

        if (!previewList || !previewCount) {
            return;
        }

        const escapeHtml = (value) => String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

        const updateMiniCart = (data) => {
            const items = Array.isArray(data.preview_items) ? data.preview_items : [];
            const cartCount = Number(data.cart_count || 0);
            const miniCartCount = Number(data.mini_cart_count || 0);
            const extraCount = Number(data.extra_count || 0);

            if (!items.length) {
                previewList.innerHTML = `
                <div class="cart-preview-empty">
                    <p>Your cart is empty.</p>
                </div>
            `;
            } else {
                previewList.innerHTML = items.map((item) => `
                <div class="cart-preview-item">
                    <img src="${escapeHtml(item.image_url)}" alt="${escapeHtml(item.name)}">
                    <div class="cart-preview-info">
                        <p>${escapeHtml(item.name)}</p>
                        <small>${escapeHtml(item.seller_name)}</small>
                    </div>
                    <span class="cart-preview-price">P${escapeHtml(item.price)}</span>
                </div>
            `).join('');
            }

            if (cartBadge) {
                cartBadge.textContent = String(cartCount);
                cartBadge.classList.toggle('is-hidden', cartCount < 1);
            }

            previewCount.textContent = extraCount > 0
                ? `${extraCount} more product${extraCount > 1 ? 's' : ''} in cart`
                : `${miniCartCount} product${miniCartCount !== 1 ? 's' : ''} in cart`;
        };

        const showSuccessToast = (message) => {
            const toast = document.createElement('div');
            toast.className = 'toast-success';
            toast.innerHTML = `<i class="fa-solid fa-circle-check"></i><span>${escapeHtml(message)}</span>`;
            document.body.appendChild(toast);

            window.setTimeout(() => {
                toast.classList.add('toast-hide');
                window.setTimeout(() => toast.remove(), 400);
            }, 1800);
        };

        const animateCartTrigger = () => {
            if (!cartTrigger) {
                return;
            }

            cartTrigger.classList.remove('cart-bump');
            void cartTrigger.offsetWidth;
            cartTrigger.classList.add('cart-bump');

            window.setTimeout(() => {
                cartTrigger.classList.remove('cart-bump');
            }, 520);
        };

        document.addEventListener('submit', async function (event) {
            const form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            if (!form.matches('form[action*="/cart/add/"]')) {
                return;
            }

            if (form.querySelector('input[name="buy_now"][value="1"]')) {
                return;
            }

            event.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');
            const originalDisabled = submitButton ? submitButton.disabled : false;
            if (submitButton) {
                submitButton.disabled = true;
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to add product to cart.');
                }

                const data = await response.json();
                updateMiniCart(data);
                animateCartTrigger();
                showSuccessToast(data.message || 'Product added to cart successfully.');
            } catch (error) {
                console.error(error);
                window.alert('Unable to update the cart right now. Please try again.');
            } finally {
                if (submitButton) {
                    submitButton.disabled = originalDisabled;
                }
            }
        });
    });
</script>
