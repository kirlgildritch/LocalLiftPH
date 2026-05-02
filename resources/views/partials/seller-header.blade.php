<header class="seller-header-shell">
    <div class="container">
        <div class="seller-header panel">
            <button class="seller-menu-toggle" type="button" id="sellerMenuToggle" aria-label="Open seller navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="{{ route('seller.dashboard') }}" class="seller-brand">
                <span class="seller-brand-icon">
                    <img src="{{ asset('assets/image/Logo.png') }}" alt="Logo">
                </span>
                <span class="seller-brand-copy">
                    <strong>LocalLift</strong>
                    <small>Seller Hub</small>
                </span>
            </a>

            <form class="seller-search" action="{{ route('seller.search') }}" method="GET">
                <i class="fa-solid fa-magnifying-glass seller-search__icon"></i>
                <input
                    type="text"
                    id="sellerSearchInput"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search products, orders, messages, and tools..."
                    aria-label="Search seller dashboard"
                    autocomplete="off"
                >
                <button
                    type="button"
                    id="sellerSearchClearButton"
                    class="seller-search__clear is-hidden"
                    title="Clear search"
                    aria-label="Clear search"
                >
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <button type="submit" class="seller-search__submit" title="Search" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <div id="sellerSearchSuggestions" class="seller-search-suggestions"></div>
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

                        <form action="{{ route('seller.logout') }}" method="POST">
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
        const searchInput = document.getElementById('sellerSearchInput');
        const searchClearButton = document.getElementById('sellerSearchClearButton');
        const suggestionsBox = document.getElementById('sellerSearchSuggestions');
        let activeRequestController = null;
        let activeSuggestionIndex = -1;

        if (searchInput && searchClearButton && suggestionsBox) {
            const hideSuggestions = () => {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
                activeSuggestionIndex = -1;
            };

            const syncClearButton = () => {
                const hasValue = searchInput.value.trim().length > 0;
                searchClearButton.classList.toggle('is-hidden', !hasValue);
            };

            const getSelectableSuggestions = () => Array.from(
                suggestionsBox.querySelectorAll('.seller-suggestion-item:not(.is-empty)')
            );

            const highlightSuggestion = (nextIndex) => {
                const items = getSelectableSuggestions();

                if (!items.length) {
                    activeSuggestionIndex = -1;
                    return;
                }

                activeSuggestionIndex = Math.max(0, Math.min(nextIndex, items.length - 1));

                items.forEach((item, index) => {
                    item.classList.toggle('is-active', index === activeSuggestionIndex);
                });
            };

            const chooseSuggestion = (item) => {
                if (!item) {
                    return;
                }

                searchInput.value = item.dataset.suggestionLabel || item.textContent;
                syncClearButton();
                hideSuggestions();
                searchInput.form.submit();
            };

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const renderSuggestions = (suggestions) => {
                if (!suggestions.length) {
                    hideSuggestions();
                    return;
                }

                suggestionsBox.innerHTML = suggestions.map((item) => {
                    const label = escapeHtml(item.label);

                    if (item.selectable === false) {
                        return `<div class="seller-suggestion-item is-empty">${label}</div>`;
                    }

                    return `<div class="seller-suggestion-item" data-suggestion-label="${label}">${label}</div>`;
                }).join('');

                suggestionsBox.style.display = 'block';

                suggestionsBox.querySelectorAll('.seller-suggestion-item').forEach((item) => {
                    if (item.classList.contains('is-empty')) {
                        return;
                    }

                    item.addEventListener('mouseenter', function () {
                        const items = getSelectableSuggestions();
                        highlightSuggestion(items.indexOf(this));
                    });

                    item.addEventListener('click', function () {
                        chooseSuggestion(this);
                    });
                });

                activeSuggestionIndex = -1;
            };

            searchInput.addEventListener('input', function () {
                const query = this.value.trim();

                syncClearButton();

                if (activeRequestController) {
                    activeRequestController.abort();
                    activeRequestController = null;
                }

                if (query.length < 1) {
                    hideSuggestions();
                    return;
                }

                activeRequestController = new AbortController();

                fetch(@json(route('seller.search.suggestions')) + `?q=${encodeURIComponent(query)}`, {
                    signal: activeRequestController.signal,
                    headers: {
                        'Accept': 'application/json',
                    },
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(`Suggestion request failed with status ${response.status}`);
                        }

                        return response.json();
                    })
                    .then((suggestions) => {
                        renderSuggestions(Array.isArray(suggestions) ? suggestions : []);
                    })
                    .catch((error) => {
                        if (error.name !== 'AbortError') {
                            console.error(error);
                            hideSuggestions();
                        }
                    })
                    .finally(() => {
                        activeRequestController = null;
                    });
            });

            searchInput.addEventListener('keydown', function (event) {
                const items = getSelectableSuggestions();
                const suggestionsVisible = suggestionsBox.style.display === 'block' && items.length > 0;

                if (event.key === 'Escape') {
                    hideSuggestions();
                    return;
                }

                if (!suggestionsVisible) {
                    return;
                }

                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    highlightSuggestion(activeSuggestionIndex + 1);
                    return;
                }

                if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    highlightSuggestion(activeSuggestionIndex <= 0 ? items.length - 1 : activeSuggestionIndex - 1);
                    return;
                }

                if (event.key === 'Enter' && activeSuggestionIndex >= 0) {
                    event.preventDefault();
                    chooseSuggestion(items[activeSuggestionIndex]);
                }
            });

            searchClearButton.addEventListener('click', function () {
                searchInput.value = '';
                syncClearButton();
                hideSuggestions();
                window.location.assign(searchInput.form.action);
            });

            document.addEventListener('click', function (event) {
                if (!searchInput.contains(event.target) && !suggestionsBox.contains(event.target)) {
                    hideSuggestions();
                }
            });

            syncClearButton();
        }

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
