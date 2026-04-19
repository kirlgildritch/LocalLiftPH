<header class="seller-header-shell">
    <div class="container">
        <div class="seller-header panel">
            <button class="seller-menu-toggle" type="button" id="sellerMenuToggle" aria-label="Open seller navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="{{ url('/seller-dashboard') }}" class="seller-brand">
                <span class="seller-brand-icon">
                    <i class="fa-solid fa-store"></i>
                </span>
                <span class="seller-brand-copy">
                    <strong>LocalLift</strong>
                    <small>Seller Hub</small>
                </span>
            </a>

            <form class="seller-search" action="#" method="GET">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search products, orders, and seller tools..."
                    aria-label="Search seller dashboard">
            </form>

            <div class="seller-header-actions">
                <div class="notification-dropdown">
                    <button class="notification-btn" id="notificationToggle" type="button">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notif-badge">3</span>
                    </button>

                    <div class="notification-menu" id="notificationMenu">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                        </div>

                        @if(session('approved_product'))
                            <div class="notification-item">
                                <strong>Product Approved</strong>
                                <p>Your product "{{ session('approved_product') }}" is now live.</p>
                            </div>
                        @endif

                        <a href="#" class="notification-item unread">
                            <div class="notif-icon"><i class="fa-regular fa-envelope"></i></div>
                            <div class="notif-content">
                                <p><strong>New Message</strong></p>
                                <span>Mark Reyes sent you a message.</span>
                                <small>10 mins ago</small>
                            </div>
                        </a>

                        <a href="#" class="notification-item">
                            <div class="notif-icon"><i class="fa-solid fa-box"></i></div>
                            <div class="notif-content">
                                <p><strong>Order Shipped</strong></p>
                                <span>Your order #1021 has been marked as shipped.</span>
                                <small>1 hour ago</small>
                            </div>
                        </a>

                        <div class="notification-footer">
                            <a href="#">View All Notifications</a>
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileToggle" type="button">
                        @if(auth()->user()->profile_image)
                            <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="Profile"
                                class="header-profile-img">
                        @else
                            <i class="fa-regular fa-circle-user profile-icon"></i>
                        @endif

                        <span>Hi, {{ auth()->user()->name }}!</span>
                    </button>

                    <div class="profile-menu" id="profileMenu">
                        <a href="{{ route('seller.profile') }}">My Profile</a>
                        <a href="{{ route('seller.settings') }}">Settings</a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.getElementById('sellerMenuToggle');
        const sidebars = document.querySelectorAll('.sidebar');
        const body = document.body;
        const isMobileSellerViewport = () => window.matchMedia('(max-width: 980px)').matches;
        const sellerDropdowns = [
            { container: '.notification-dropdown', trigger: '.notification-btn' },
            { container: '.profile-dropdown', trigger: '.profile-btn' },
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

        if (!menuToggle || !sidebars.length) {
            return;
        }

        const closeSidebar = () => {
            body.classList.remove('seller-sidebar-open');
            sidebars.forEach((sidebar) => sidebar.classList.remove('is-open'));
        };

        menuToggle.addEventListener('click', function () {
            const shouldOpen = !body.classList.contains('seller-sidebar-open');
            body.classList.toggle('seller-sidebar-open', shouldOpen);
            sidebars.forEach((sidebar) => sidebar.classList.toggle('is-open', shouldOpen));
        });

        sellerDropdowns.forEach(({ container, trigger }) => {
            const dropdown = document.querySelector(container);
            const triggerElement = dropdown ? dropdown.querySelector(trigger) : null;

            if (!dropdown || !triggerElement) {
                return;
            }

            triggerElement.addEventListener('click', function (event) {
                if (!isMobileSellerViewport()) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                sellerDropdowns.forEach(({ container: otherContainer }) => {
                    const otherDropdown = document.querySelector(otherContainer);
                    if (otherDropdown && otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('is-open');
                    }
                });

                dropdown.classList.toggle('is-open');
            });

            const menuElement = dropdown.querySelector('.notification-menu, .profile-menu');

            if (menuElement) {
                const bindOpen = () => {
                    if (isMobileSellerViewport()) {
                        return;
                    }

                    openDesktopDropdown(dropdown);
                };

                const bindClose = () => {
                    if (isMobileSellerViewport()) {
                        return;
                    }

                    queueDesktopDropdownClose(dropdown);
                };

                dropdown.addEventListener('mouseenter', bindOpen);
                dropdown.addEventListener('mouseleave', bindClose);
                menuElement.addEventListener('mouseenter', bindOpen);
                menuElement.addEventListener('mouseleave', bindClose);
            }
        });

        document.addEventListener('click', function (event) {
            if (window.innerWidth > 980) {
                return;
            }

            const clickedInsideSidebar = Array.from(sidebars).some((sidebar) => sidebar.contains(event.target));
            const clickedToggle = menuToggle.contains(event.target);

            sellerDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);
                if (dropdown && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('is-open');
                }
            });

            if (!clickedInsideSidebar && !clickedToggle) {
                closeSidebar();
            }
        });

        document.addEventListener('click', function (event) {
            const closeTrigger = event.target.closest('[data-close-seller-sidebar]');
            const sidebarLink = event.target.closest('.sidebar-menu a');

            if (closeTrigger || (window.innerWidth <= 980 && sidebarLink)) {
                closeSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 980) {
                closeSidebar();
            }

            if (!isMobileSellerViewport()) {
                sellerDropdowns.forEach(({ container }) => {
                    const dropdown = document.querySelector(container);
                    if (dropdown) {
                        dropdown.classList.remove('is-open');
                    }
                });

                return;
            }

            sellerDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);
                if (dropdown) {
                    dropdown.classList.remove('is-hover-open');
                    clearHoverTimer(dropdown);
                }
            });
        });
    });
</script>
