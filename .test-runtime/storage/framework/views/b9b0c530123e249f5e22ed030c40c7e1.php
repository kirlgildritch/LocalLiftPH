<?php
    $sellerHeaderNotifications = $sellerHeaderNotifications ?? collect();
    $sellerUnreadNotificationCount = (int) ($sellerUnreadNotificationCount ?? 0);
?>

<header class="seller-header-shell">
    <div class="container">
        <div class="seller-header panel">
            <button class="seller-menu-toggle" type="button" id="sellerMenuToggle" aria-label="Open seller navigation">
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="<?php echo e(route('seller.dashboard')); ?>" class="seller-brand">
                <span class="seller-brand-icon">
                    <img src="<?php echo e(asset('assets/image/Logo.png')); ?>" alt="Logo">
                </span>
                <span class="seller-brand-copy">
                    <strong>LocalLift</strong>
                    <small>Seller Hub</small>
                </span>
            </a>

            <form class="seller-search" action="<?php echo e(route('seller.search')); ?>" method="GET">

                <input type="text" id="sellerSearchInput" name="q" value="<?php echo e(request('q')); ?>"
                    placeholder="Search products, orders, messages, and tools..." aria-label="Search seller dashboard"
                    autocomplete="off">
                <button type="button" id="sellerSearchClearButton" class="seller-search__clear is-hidden"
                    title="Clear search" aria-label="Clear search">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <button type="submit" class="seller-search__submit" title="Search" aria-label="Search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <div id="sellerSearchSuggestions" class="seller-search-suggestions"></div>
            </form>

            <div class="seller-header-actions">
                <div class="notification-dropdown">
                    <button class="notification-btn" id="notificationToggle" type="button" data-seller-notification-button
                        aria-label="View notifications">
                        <i class="fa-regular fa-bell"></i>
                        <span class="notif-badge <?php echo e($sellerUnreadNotificationCount > 0 ? '' : 'is-hidden'); ?>"
                            data-seller-notification-badge>
                            <?php echo e($sellerUnreadNotificationCount > 99 ? '99+' : $sellerUnreadNotificationCount); ?>

                        </span>
                    </button>

                    <div class="notification-menu" id="notificationMenu" data-seller-notification-menu>
                        <div class="notification-header">
                            <div>
                                <h4>Notifications</h4>
                                <p class="notification-header__meta" data-seller-notification-meta>
                                    <?php echo e($sellerUnreadNotificationCount > 0
                                        ? $sellerUnreadNotificationCount . ' unread notification' . ($sellerUnreadNotificationCount === 1 ? '' : 's')
                                        : 'You are all caught up.'); ?>

                                </p>
                            </div>
                        </div>

                        <?php $__empty_1 = true; $__currentLoopData = $sellerHeaderNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $data = $notification->data ?? [];
                                $type = $data['type'] ?? $data['category'] ?? 'admin';
                                $action = $data['action'] ?? 'notification';
                                $title = $data['title'] ?? 'Notification';
                                $message = $data['message'] ?? 'You have a new notification.';
                                $icon = match ($action) {
                                    'new_order', 'order_completed', 'order_cancelled', 'buyer_confirmed_receipt', 'pending_order_not_shipped' => 'fa-bag-shopping',
                                    'buyer_message' => 'fa-envelope',
                                    'buyer_review' => 'fa-star',
                                    'product_low_stock', 'product_out_of_stock', 'product_edited' => 'fa-box',
                                    'product_approved', 'product_rejected', 'product_reported', 'shop_verified', 'shop_flagged', 'warn_seller', 'delist_product', 'ban_product', 'suspend_seller', 'dismiss_report', 'shop_documents_requested' => 'fa-triangle-exclamation',
                                    default => ($type === 'messages' ? 'fa-envelope' : ($type === 'orders' ? 'fa-bag-shopping' : ($type === 'reviews' ? 'fa-star' : 'fa-bell'))),
                                };
                            ?>

                            <div class="notification-item <?php echo e($notification->read_at ? '' : 'unread'); ?>"
                                data-seller-notification-item
                                data-seller-notification-id="<?php echo e($notification->id); ?>"
                                data-seller-notification-read="<?php echo e($notification->read_at ? '1' : '0'); ?>">
                                <div class="notif-icon"><i class="fa-solid <?php echo e($icon); ?>"></i></div>
                                <a href="<?php echo e(route('seller.notifications.open', $notification)); ?>" class="notif-content"
                                    <?php if($action === 'buyer_message' && !empty($data['related_id'])): ?>
                                        data-chat-notification-link
                                        data-chat-conversation-id="<?php echo e((int) $data['related_id']); ?>"
                                    <?php endif; ?>>
                                    <p><strong><?php echo e($title); ?></strong></p>
                                    <span><?php echo e($message); ?></span>
                                    <small><?php echo e($notification->created_at?->diffForHumans() ?? 'Just now'); ?></small>
                                </a>

                                <?php if(! $notification->read_at): ?>
                                    <form method="POST" action="<?php echo e(route('seller.notifications.read', $notification)); ?>"
                                        class="notification-read-form" data-seller-notification-read-form>
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="notification-read-btn" title="Mark as read"
                                            aria-label="Mark as read">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="notification-item notification-item--empty" data-seller-notification-empty>
                                <div class="notif-icon"><i class="fa-regular fa-bell-slash"></i></div>
                                <div class="notif-content">
                                    <p><strong>No notifications</strong></p>
                                    <span>You're all caught up.</span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="notification-footer">
                            <a href="<?php echo e(route('seller.notifications.index')); ?>">View all notifications</a>
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileToggle" type="button">
                        <?php if(auth()->user()->profile_image): ?>
                            <img src="<?php echo e(asset('storage/' . auth()->user()->profile_image)); ?>" alt="Profile"
                                class="header-profile-img">
                        <?php else: ?>
                            <i class="fa-regular fa-circle-user profile-icon"></i>
                        <?php endif; ?>

                        <span>Hi, <?php echo e(auth()->user()->name); ?>!</span>
                    </button>

                    <div class="profile-menu" id="profileMenu">
                        <a href="<?php echo e(route('seller.profile')); ?>">My Profile</a>
                        <a href="<?php echo e(route('seller.settings')); ?>">Settings</a>

                        <form action="<?php echo e(route('seller.logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
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
                if (typeof searchInput.form.requestSubmit === 'function') {
                    searchInput.form.requestSubmit();
                    return;
                }

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

                fetch(<?php echo json_encode(route('seller.search.suggestions'), 15, 512) ?> + `?q=${encodeURIComponent(query)}`, {
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

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const sellerNotificationUserId = <?php echo json_encode(auth('seller')->id(), 15, 512) ?>;
        const sellerNotificationFeedUrl = <?php echo json_encode(route('seller.notifications.feed'), 15, 512) ?>;
        const sellerNotificationBaseUrl = <?php echo json_encode(url('/seller-notifications'), 15, 512) ?>;
        const sellerNotificationButton = document.querySelector('[data-seller-notification-button]');
        const sellerNotificationMenu = document.querySelector('[data-seller-notification-menu]');
        const sellerNotificationBadge = document.querySelector('[data-seller-notification-badge]');
        const sellerNotificationMeta = document.querySelector('[data-seller-notification-meta]');
        const sellerNotificationFooter = sellerNotificationMenu?.querySelector('.notification-footer');
        const sellerNotificationReadButtonSelector = '[data-seller-notification-read-form]';
        let sellerUnreadCount = Number(<?php echo json_encode((int) $sellerUnreadNotificationCount, 15, 512) ?>);
        let sellerNotificationRefreshInFlight = false;
        let sellerNotificationFeedTimer = null;
        let sellerNotificationLastRefreshAt = 0;

        const sellerNotificationCountLabel = (count) => `${count} unread notification${count === 1 ? '' : 's'}`;
        const sellerNotificationConversationId = (notification) => Number(
            notification?.related_id
            || notification?.route_params?.conversation
            || notification?.data?.related_id
            || notification?.data?.route_params?.conversation
            || 0
        );
        const isSellerMessageNotification = (notification) => (
            String(notification?.action || notification?.data?.action || '').toLowerCase() === 'buyer_message'
            && sellerNotificationConversationId(notification) > 0
        );

        const sellerNotificationIconClass = (notification) => {
            const action = (notification?.action || notification?.data?.action || '').toLowerCase();
            const type = (notification?.type || notification?.category || notification?.data?.type || '').toLowerCase();

            switch (action) {
                case 'new_order':
                case 'order_completed':
                case 'order_cancelled':
                case 'buyer_confirmed_receipt':
                case 'pending_order_not_shipped':
                    return 'fa-bag-shopping';
                case 'buyer_message':
                    return 'fa-envelope';
                case 'buyer_review':
                    return 'fa-star';
                case 'product_low_stock':
                case 'product_out_of_stock':
                case 'product_edited':
                    return 'fa-box';
                case 'product_approved':
                case 'product_rejected':
                case 'product_reported':
                case 'shop_verified':
                case 'shop_flagged':
                case 'warn_seller':
                case 'delist_product':
                case 'ban_product':
                case 'suspend_seller':
                case 'dismiss_report':
                case 'shop_documents_requested':
                    return 'fa-triangle-exclamation';
                default:
                    if (type === 'messages') {
                        return 'fa-envelope';
                    }

                    if (type === 'orders') {
                        return 'fa-bag-shopping';
                    }

                    if (type === 'reviews') {
                        return 'fa-star';
                    }

                    return 'fa-bell';
            }
        };

        const sellerNotificationId = (notification) => notification?.id ?? notification?.data?.id ?? null;

        const sellerNotificationOpenUrl = (id) => `${sellerNotificationBaseUrl}/${id}/open`;
        const sellerNotificationReadUrl = (id) => `${sellerNotificationBaseUrl}/${id}/read`;
        const sellerNotificationDeleteUrl = (id) => `${sellerNotificationBaseUrl}/${id}`;

        const updateSellerNotificationBadge = (count) => {
            if (!sellerNotificationBadge) {
                return;
            }

            const sanitizedCount = Math.max(0, Number(count) || 0);
            sellerUnreadCount = sanitizedCount;
            sellerNotificationBadge.textContent = sanitizedCount > 99 ? '99+' : String(sanitizedCount);
            sellerNotificationBadge.classList.toggle('is-hidden', sanitizedCount === 0);

            if (sellerNotificationMeta) {
                sellerNotificationMeta.textContent = sanitizedCount > 0
                    ? sellerNotificationCountLabel(sanitizedCount)
                    : 'You are all caught up.';
            }
        };

        const normalizeSellerNotification = (notification) => {
            const payload = notification?.data && typeof notification.data === 'object'
                ? { ...notification.data, id: notification.id ?? notification.data.id, read_at: notification.read_at ?? notification.data.read_at }
                : { ...notification };

            return {
                ...payload,
                id: payload.id ?? notification?.id,
                type: payload.type || payload.category || 'admin',
                action: payload.action || 'notification',
                title: payload.title || 'Notification',
                message: payload.message || 'You have a new notification.',
                route: payload.route || null,
                route_params: payload.route_params || {},
                related_type: payload.related_type || null,
                related_id: payload.related_id || null,
                read_at: payload.read_at || null,
                created_at_human: payload.created_at_human || 'Just now',
                created_at_formatted: payload.created_at_formatted || '',
            };
        };

        const createSellerNotificationItem = (notification) => {
            const normalized = normalizeSellerNotification(notification);
            const item = document.createElement('div');
            item.className = `notification-item ${normalized.read_at ? '' : 'unread'}`.trim();
            item.dataset.sellerNotificationItem = '';
            item.dataset.sellerNotificationId = normalized.id;
            item.dataset.sellerNotificationRead = normalized.read_at ? '1' : '0';

            const icon = document.createElement('div');
            icon.className = 'notif-icon';

            const iconElement = document.createElement('i');
            iconElement.className = `fa-solid ${sellerNotificationIconClass(normalized)}`;
            icon.appendChild(iconElement);

            const content = document.createElement('a');
            content.href = sellerNotificationOpenUrl(normalized.id);
            content.className = 'notif-content';

            if (isSellerMessageNotification(normalized)) {
                content.dataset.chatNotificationLink = '';
                content.dataset.chatConversationId = String(sellerNotificationConversationId(normalized));
            }

            const title = document.createElement('p');
            const strong = document.createElement('strong');
            strong.textContent = normalized.title;
            title.appendChild(strong);

            const message = document.createElement('span');
            message.textContent = normalized.message;

            const time = document.createElement('small');
            time.textContent = normalized.created_at_human;

            content.appendChild(title);
            content.appendChild(message);
            content.appendChild(time);

            item.appendChild(icon);
            item.appendChild(content);

            if (!normalized.read_at) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = sellerNotificationReadUrl(normalized.id);
                form.className = 'notification-read-form';
                form.dataset.sellerNotificationReadForm = '';

                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);

                const button = document.createElement('button');
                button.type = 'submit';
                button.className = 'notification-read-btn';
                button.title = 'Mark as read';
                button.setAttribute('aria-label', 'Mark as read');

                const iconMark = document.createElement('i');
                iconMark.className = 'fa-solid fa-check';
                button.appendChild(iconMark);
                form.appendChild(button);
                item.appendChild(form);
            }

            return item;
        };

        const ensureSellerNotificationEmptyState = () => {
            if (!sellerNotificationMenu || sellerNotificationMenu.querySelector('[data-seller-notification-item]')) {
                return;
            }

            if (sellerNotificationMenu.querySelector('[data-seller-notification-empty]')) {
                return;
            }

            const emptyItem = document.createElement('div');
            emptyItem.className = 'notification-item notification-item--empty';
            emptyItem.dataset.sellerNotificationEmpty = '';

            const icon = document.createElement('div');
            icon.className = 'notif-icon';

            const iconElement = document.createElement('i');
            iconElement.className = 'fa-regular fa-bell-slash';
            icon.appendChild(iconElement);

            const content = document.createElement('div');
            content.className = 'notif-content';

            const title = document.createElement('p');
            const strong = document.createElement('strong');
            strong.textContent = 'No notifications';
            title.appendChild(strong);

            const message = document.createElement('span');
            message.textContent = "You're all caught up.";

            content.appendChild(title);
            content.appendChild(message);

            emptyItem.appendChild(icon);
            emptyItem.appendChild(content);
            sellerNotificationFooter?.before(emptyItem);
        };

        const replaceSellerNotificationItems = (notifications = []) => {
            if (!sellerNotificationMenu || !sellerNotificationFooter) {
                return;
            }

            sellerNotificationMenu.querySelectorAll('[data-seller-notification-item], [data-seller-notification-empty]').forEach((item) => item.remove());

            if (!notifications.length) {
                ensureSellerNotificationEmptyState();
                return;
            }

            notifications.slice(0, 5).forEach((notification) => {
                sellerNotificationFooter.before(createSellerNotificationItem(notification));
            });
        };

        const prependSellerNotificationItem = (notification) => {
            if (!sellerNotificationMenu || !sellerNotificationFooter) {
                return;
            }

            const normalized = normalizeSellerNotification(notification);

            if (!normalized.id) {
                return;
            }

            sellerNotificationMenu.querySelector('[data-seller-notification-empty]')?.remove();

            const existingItem = sellerNotificationMenu.querySelector(`[data-seller-notification-id="${normalized.id}"]`);
            if (existingItem) {
                existingItem.remove();
            }

            sellerNotificationFooter.before(createSellerNotificationItem(normalized));

            const renderedItems = [...sellerNotificationMenu.querySelectorAll('[data-seller-notification-item]')];
            renderedItems.slice(5).forEach((item) => item.remove());
        };

        const fetchSellerNotificationFeed = async () => {
            if (!sellerNotificationMenu || !sellerNotificationFeedUrl || sellerNotificationRefreshInFlight) {
                return;
            }

            sellerNotificationRefreshInFlight = true;

            try {
                const response = await fetch(sellerNotificationFeedUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    },
                });

                if (!response.ok) {
                    throw new Error('Notification feed request failed.');
                }

                const payload = await response.json();
                updateSellerNotificationBadge(Number(payload.unreadCount ?? 0));
                replaceSellerNotificationItems(Array.isArray(payload.notifications) ? payload.notifications : []);
            } catch (error) {
                console.error(error);
            } finally {
                sellerNotificationRefreshInFlight = false;
            }
        };

        const refreshSellerNotificationFeedIfNeeded = () => {
            const now = Date.now();

            if ((now - sellerNotificationLastRefreshAt) < 2500) {
                return;
            }

            sellerNotificationLastRefreshAt = now;
            void fetchSellerNotificationFeed();
        };

        const markSellerNotificationAsRead = async (form) => {
            const notificationId = form.closest('[data-seller-notification-id]')?.dataset.sellerNotificationId;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    },
                    body: new FormData(form),
                });

                if (!response.ok) {
                    throw new Error('Unable to mark notification as read.');
                }

                const payload = await response.json();
                updateSellerNotificationBadge(Number(payload.unreadCount ?? 0));

                const item = notificationId
                    ? sellerNotificationMenu?.querySelector(`[data-seller-notification-id="${notificationId}"]`)
                    : null;

                if (item) {
                    item.classList.remove('unread');
                    item.dataset.sellerNotificationRead = '1';
                    item.querySelector('[data-seller-notification-read-form]')?.remove();
                }

                document.dispatchEvent(new CustomEvent('seller:notification-read', {
                    detail: payload.notification || normalizeSellerNotification(payload),
                }));
            } catch (error) {
                console.error(error);
            }
        };

        const openSellerChatFromNotification = async (link) => {
            const conversationId = Number(link.dataset.chatConversationId || 0);

            if (!conversationId) {
                return;
            }

            const notificationItem = link.closest('[data-seller-notification-item]');
            const readForm = notificationItem?.querySelector(sellerNotificationReadButtonSelector);

            try {
                if (readForm) {
                    await markSellerNotificationAsRead(readForm);
                }
            } catch (error) {
                console.error(error);
            }

            document.dispatchEvent(new CustomEvent('chat-widget:open-conversation', {
                detail: {
                    conversationId,
                },
            }));
        };

        if (sellerNotificationMenu) {
            updateSellerNotificationBadge(sellerUnreadCount);

            sellerNotificationFeedTimer = window.setInterval(() => {
                void fetchSellerNotificationFeed();
            }, 90000);

            if (window.Echo && sellerNotificationUserId) {
                window.Echo.private(`App.Models.User.${sellerNotificationUserId}`)
                    .notification((notification) => {
                        const normalized = normalizeSellerNotification(notification);

                        if (!normalized.id) {
                            return;
                        }

                        if (sellerNotificationMenu?.querySelector(`[data-seller-notification-id="${normalized.id}"]`)) {
                            return;
                        }

                        updateSellerNotificationBadge(sellerUnreadCount + (normalized.read_at ? 0 : 1));
                        prependSellerNotificationItem(normalized);

                        document.dispatchEvent(new CustomEvent('seller:notification-received', {
                            detail: normalized,
                        }));
                    });
            }

            document.addEventListener('submit', function (event) {
                const form = event.target.closest(sellerNotificationReadButtonSelector);

                if (!form) {
                    return;
                }

                event.preventDefault();
                void markSellerNotificationAsRead(form);
            });

            document.addEventListener('click', function (event) {
                const link = event.target.closest('[data-chat-notification-link]');

                if (!link || !sellerNotificationMenu.contains(link)) {
                    return;
                }

                if (!document.querySelector('[data-chat-widget]')) {
                    return;
                }

                event.preventDefault();
                void openSellerChatFromNotification(link);
            });

            document.addEventListener('seller:notification-read', (event) => {
                const notificationId = event.detail?.id;
                if (!notificationId) {
                    return;
                }

                const notificationItem = sellerNotificationMenu.querySelector(`[data-seller-notification-id="${notificationId}"]`);
                if (!notificationItem) {
                    return;
                }

                notificationItem.classList.remove('unread');
                notificationItem.dataset.sellerNotificationRead = '1';
                notificationItem.querySelector('[data-seller-notification-read-form]')?.remove();
            });

            document.addEventListener('seller:notification-received', () => {
                refreshSellerNotificationFeedIfNeeded();
            });

            sellerNotificationButton?.addEventListener('mouseenter', refreshSellerNotificationFeedIfNeeded);
            sellerNotificationButton?.addEventListener('focus', refreshSellerNotificationFeedIfNeeded);
            sellerNotificationButton?.addEventListener('click', refreshSellerNotificationFeedIfNeeded);
            sellerNotificationMenu.addEventListener('mouseenter', refreshSellerNotificationFeedIfNeeded);
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
<?php /**PATH C:\Users\kirlg\LocalLiftPH\resources\views/partials/seller-header.blade.php ENDPATH**/ ?>