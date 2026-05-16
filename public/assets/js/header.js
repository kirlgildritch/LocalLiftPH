document.addEventListener('DOMContentLoaded', function () {
    const headerShell = document.querySelector('[data-frontend-header]');

    if (!headerShell) {
        return;
    }

    const productSuggestionsUrl = headerShell.dataset.productSuggestionsUrl || '/products/suggestions';
    const isCompactViewport = () => window.matchMedia('(max-width: 820px)').matches;
    const body = document.body;
    const headerMain = document.querySelector('.header-main');
    const nav = document.querySelector('.navbar');
    const navToggle = document.querySelector('.header-menu-toggle');
    const navClose = document.querySelector('.navbar-close');
    const mobileProfileDropdown = { container: '.buyer-profile-dropdown', trigger: '.profile-trigger' };
    const mobileDropdowns = [mobileProfileDropdown];
    const desktopHoverDropdowns = [
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

    const closeCompactNav = () => {
        if (!headerMain || !nav || !navToggle) {
            return;
        }

        headerMain.classList.remove('nav-open');
        nav.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        body.classList.remove('frontend-nav-open');
    };

    if (nav && navToggle && headerMain) {
        navToggle.addEventListener('click', function (event) {
            if (!isCompactViewport()) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const shouldOpen = !nav.classList.contains('is-open');
            headerMain.classList.toggle('nav-open', shouldOpen);
            nav.classList.toggle('is-open', shouldOpen);
            navToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
            body.classList.toggle('frontend-nav-open', shouldOpen);
        });
    }

    if (navClose && nav) {
        navClose.addEventListener('click', function (event) {
            if (!isCompactViewport()) {
                return;
            }

            event.preventDefault();
            closeCompactNav();
        });
    }

    mobileDropdowns.forEach(({ container, trigger }) => {
        const dropdown = document.querySelector(container);
        const triggerElement = dropdown ? dropdown.querySelector(trigger) : null;

        if (!dropdown || !triggerElement) {
            return;
        }

        triggerElement.addEventListener('click', function (event) {
            if (!isCompactViewport()) {
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
            if (isCompactViewport()) {
                return;
            }

            openDesktopDropdown(dropdown);
        };

        const bindClose = () => {
            if (isCompactViewport()) {
                return;
            }

            queueDesktopDropdownClose(dropdown);
        };

        dropdown.addEventListener('mouseenter', bindOpen);
        dropdown.addEventListener('mouseleave', bindClose);
        menuElement.addEventListener('mouseenter', bindOpen);
        menuElement.addEventListener('mouseleave', bindClose);
    });

    ['.cart-dropdown'].forEach((container) => {
        const dropdown = document.querySelector(container);
        if (!dropdown) {
            return;
        }

        const trigger = dropdown.querySelector('a');
        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function () {
            if (!isCompactViewport()) {
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
        if (!isCompactViewport()) {
            return;
        }

        mobileDropdowns.forEach(({ container }) => {
            const dropdown = document.querySelector(container);
            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('is-open');
            }
        });

        if (nav && navToggle && headerMain) {
            const clickedInsideNav = nav.contains(event.target);
            const clickedToggle = navToggle.contains(event.target);

            if (!clickedInsideNav && !clickedToggle) {
                closeCompactNav();
            }
        }
    });

    window.addEventListener('resize', function () {
        if (!isCompactViewport()) {
            mobileDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);
                if (dropdown) {
                    dropdown.classList.remove('is-open');
                }
            });

            closeCompactNav();

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

    if (nav) {
        nav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', function () {
                if (isCompactViewport()) {
                    closeCompactNav();
                }
            });
        });
    }

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
        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        });
    }

    const searchInput = document.getElementById('searchInput');
    const searchClearButton = document.getElementById('searchClearButton');
    const suggestionsBox = document.getElementById('searchSuggestions');
    let activeRequestController = null;

    if (searchInput && searchClearButton && suggestionsBox) {
        const hideSuggestions = () => {
            suggestionsBox.innerHTML = '';
            suggestionsBox.style.display = 'none';
        };

        const syncClearButton = () => {
            const hasValue = searchInput.value.trim().length > 0;
            searchClearButton.classList.toggle('is-hidden', !hasValue);
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
                    return `<div class="suggestion-item is-empty">${label}</div>`;
                }

                return `<div class="suggestion-item" data-suggestion-label="${label}">${label}</div>`;
            }).join('');

            suggestionsBox.style.display = 'block';

            suggestionsBox.querySelectorAll('.suggestion-item').forEach((item) => {
                if (item.classList.contains('is-empty')) {
                    return;
                }

                item.addEventListener('click', function () {
                    searchInput.value = this.dataset.suggestionLabel || this.textContent;
                    hideSuggestions();
                    if (typeof searchInput.form.requestSubmit === 'function') {
                        searchInput.form.requestSubmit();
                        return;
                    }

                    searchInput.form.submit();
                });
            });
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

            fetch(`${productSuggestionsUrl}?q=${encodeURIComponent(query)}`, {
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
        const loadingHelper = window.LocalLiftActionLoading;

        if (submitButton) {
            if (loadingHelper) {
                loadingHelper.start(submitButton, {
                    label: submitButton.textContent.trim() ? 'Adding...' : '',
                });
            } else {
                submitButton.disabled = true;
            }
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
                if (loadingHelper && submitButton.isConnected) {
                    loadingHelper.stop(submitButton);
                } else if (!loadingHelper) {
                    submitButton.disabled = false;
                }
            }
        }
    });
});
