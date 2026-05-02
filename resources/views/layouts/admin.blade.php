<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin.css') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/image/favicon.png') }}">
    @vite(['resources/js/app.js'])
    @stack('styles')
</head>

<body>
    @php
        $currentRoute = request()->route()?->getName();
        $adminToast = null;

        foreach (['success', 'error', 'warning', 'info'] as $type) {
            if (session()->has($type)) {
                $adminToast = [
                    'type' => $type,
                    'message' => session($type),
                ];
                break;
            }
        }

        if (!$adminToast && $errors->any()) {
            $adminToast = [
                'type' => 'error',
                'message' => $errors->first(),
            ];
        }

        $adminToastIcon = $adminToast
            ? match ($adminToast['type']) {
                'error' => 'fa-circle-xmark',
                'warning' => 'fa-triangle-exclamation',
                'info' => 'fa-circle-info',
                default => 'fa-circle-check',
            }
            : null;

        $adminUser = auth('admin')->user();
        $adminNotifications = collect();
        $adminUnreadCount = 0;

        if ($adminUser && method_exists($adminUser, 'notifications')) {
            $adminNotifications = $adminUser->notifications()->latest()->limit(5)->get();
            $adminUnreadCount = $adminUser->unreadNotifications()->count();
        }
    @endphp

    <div class="admin-shell">
        <aside class="sidebar">
            <div class="sidebar__brand">
                <div class="sidebar__brand-copy">
                    <div class="sidebar__logo"><img src="{{ asset('assets/image/Logo.png') }}" alt="Logo"></div>
                    <div>
                        <p class="sidebar__eyebrow">Marketplace</p>
                        <h1>Admin Dashboard</h1>
                    </div>
                </div>
                <button class="sidebar__close" type="button" data-sidebar-close aria-label="Close navigation">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <nav class="sidebar__nav" aria-label="Admin Navigation">
                <a class="sidebar__link {{ $currentRoute === 'admin.dashboard' ? 'is-active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>Overview</span>
                </a>

                <a class="sidebar__link {{ $currentRoute === 'admin.products' ? 'is-active' : '' }}"
                    href="{{ route('admin.products') }}">
                    <i class="fa-solid fa-box-open"></i>
                    <span>Product Approvals</span>
                </a>

                <a class="sidebar__link {{ $currentRoute === 'admin.sellers' ? 'is-active' : '' }}"
                    href="{{ route('admin.sellers') }}">
                    <i class="fa-solid fa-user-check"></i>
                    <span>Seller Reviews</span>
                </a>

                <a class="sidebar__link {{ $currentRoute === 'admin.orders' ? 'is-active' : '' }}"
                    href="{{ route('admin.orders') }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>Orders</span>
                </a>

                <a class="sidebar__link {{ $currentRoute === 'admin.reports' ? 'is-active' : '' }}"
                    href="{{ route('admin.reports') }}">
                    <i class="fa-solid fa-flag"></i>
                    <span>Reports</span>
                </a>

                <a class="sidebar__link {{ str_starts_with($currentRoute ?? '', 'admin.notifications') ? 'is-active' : '' }}"
                    href="{{ route('admin.notifications.index') }}">
                    <i class="fa-solid fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </nav>

            <form method="POST" action="{{ route('admin.logout') }}" class="sidebar__logout">
                @csrf
                <button type="submit" class="sidebar__link">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </button>
            </form>
        </aside>

        <div class="admin-main">
            <header class="topbar">
                <button class="topbar__menu" type="button" data-sidebar-toggle aria-label="Toggle navigation">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div>
                    <p class="topbar__eyebrow">@yield('eyebrow', 'Admin workspace')</p>
                    <h2>@yield('page-title', 'Dashboard')</h2>
                </div>

                <div class="topbar__meta">
                    <div class="notification-dropdown admin-notification-dropdown">
                        <button class="notification-btn admin-notification-btn" type="button"
                            aria-label="View notifications">
                            <i class="fa-regular fa-bell"></i>
                            @if ($adminUnreadCount > 0)
                                <span
                                    class="notif-badge admin-notif-badge">{{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}</span>
                            @endif
                        </button>

                        <div class="notification-menu admin-notification-menu">
                            <div class="notification-header">
                                <h4>Notifications</h4>
                                <p class="notification-header__meta" data-admin-notification-meta>
                                    {{ $adminUnreadCount > 0 ? $adminUnreadCount . ' unread' : "You're all caught up." }}
                                </p>
                            </div>

                            @forelse ($adminNotifications as $notification)
                                @php
                                    $data = $notification->data ?? [];
                                    $type = $data['type'] ?? 'info';
                                    $title = $data['title'] ?? 'Notification';
                                    $message = $data['message'] ?? 'You have a new notification.';
                                    $icon = match ($type) {
                                        'reports', 'report' => 'fa-flag',
                                        'seller_review', 'seller' => 'fa-user-check',
                                        'orders', 'order' => 'fa-receipt',
                                        'products', 'product' => 'fa-box-open',
                                        default => 'fa-bell',
                                    };
                                @endphp

                                <a href="{{ route('admin.notifications.open', $notification) }}"
                                    class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
                                    <div class="notif-icon"><i class="fa-solid {{ $icon }}"></i></div>
                                    <div class="notif-content">
                                        <p><strong>{{ $title }}</strong></p>
                                        <span>{{ $message }}</span>
                                        <small>{{ $notification->created_at?->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <div class="notification-item notification-item--empty">
                                    <div class="notif-icon"><i class="fa-solid fa-bell-slash"></i></div>
                                    <div class="notif-content">
                                        <p><strong>No notifications</strong></p>

                                    </div>
                                </div>
                            @endforelse

                            <div class="notification-footer">
                                <a href="{{ route('admin.notifications.index') }}">View All Notifications</a>
                            </div>
                        </div>
                    </div>

                    <div class="user-chip">
                        <span class="user-chip__avatar">AD</span>
                        <span>{{ auth('admin')->user()?->name ?? 'Admin User' }}</span>
                    </div>
                </div>
            </header>

            <main class="page">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')

    @if ($adminToast)
        <div id="admin-toast" class="toast-message toast-message--{{ $adminToast['type'] }}" role="status"
            aria-live="polite">
            <i class="fa-solid {{ $adminToastIcon }}"></i>
            <span>{{ $adminToast['message'] }}</span>
        </div>
    @endif

    <script>
        const toggleButton = document.querySelector('[data-sidebar-toggle]');
        const closeButton = document.querySelector('[data-sidebar-close]');
        const shell = document.querySelector('.admin-shell');
        const sidebar = document.querySelector('.sidebar');
        const notificationMenu = document.querySelector('.admin-notification-menu');
        const notificationFooter = notificationMenu?.querySelector('.notification-footer');
        const adminDropdowns = [
            { container: '.admin-notification-dropdown', trigger: '.admin-notification-btn', menu: '.admin-notification-menu' },
        ];
        const hoverCloseDelay = 180;
        const hoverTimers = new Map();

        const isMobileAdminViewport = () => window.matchMedia('(max-width: 980px)').matches;
        const closeSidebar = () => shell?.classList.remove('sidebar-open');
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
        const adminNotificationUserId = @json($adminUser?->id);
        const adminNotificationFeedUrl = @json(route('admin.notifications.feed'));
        let adminUnreadCount = {{ (int) $adminUnreadCount }};
        let lastRenderedUnreadCount = {{ (int) $adminUnreadCount }};
        let notificationFeedTimer = null;

        const notificationIconClass = (type) => {
            switch (type) {
                case 'reports':
                case 'report':
                    return 'fa-flag';
                case 'seller_review':
                case 'seller':
                    return 'fa-user-check';
                case 'orders':
                case 'order':
                    return 'fa-receipt';
                case 'products':
                case 'product':
                    return 'fa-box-open';
                default:
                    return 'fa-bell';
            }
        };

        const renderNotificationBadge = () => {
            const triggerButton = document.querySelector('.admin-notification-btn');
            const existingBadge = document.querySelector('.admin-notif-badge');

            if (!triggerButton) {
                return;
            }

            if (adminUnreadCount <= 0) {
                triggerButton.classList.remove('has-unread');
                existingBadge?.remove();
                lastRenderedUnreadCount = 0;
                updateNotificationHeaderMeta();
                return;
            }

            const badge = existingBadge || document.createElement('span');
            badge.className = 'notif-badge admin-notif-badge';
            badge.textContent = adminUnreadCount > 99 ? '99+' : String(adminUnreadCount);

            if (!badge.parentElement) {
                triggerButton.appendChild(badge);
            }

            triggerButton.classList.add('has-unread');
            if (adminUnreadCount > lastRenderedUnreadCount) {
                triggerButton.classList.remove('is-pulsing');
                void triggerButton.offsetWidth;
                triggerButton.classList.add('is-pulsing');
            }

            lastRenderedUnreadCount = adminUnreadCount;
            updateNotificationHeaderMeta();
        };

        const updateNotificationHeaderMeta = () => {
            const meta = document.querySelector('[data-admin-notification-meta]');

            if (!meta) {
                return;
            }

            meta.textContent = adminUnreadCount > 0
                ? `${adminUnreadCount} unread notification${adminUnreadCount === 1 ? '' : 's'}`
                : "You're all caught up.";
        };

        const buildNotificationUrl = (id) => `/admin/notifications/${id}/open`;

        const normalizeNotificationPayload = (notification) => {
            const payload = notification?.data && typeof notification.data === 'object'
                ? { ...notification.data, id: notification.id ?? notification.data.id, read_at: notification.read_at }
                : { ...notification };

            return {
                id: payload.id,
                type: payload.type || payload.notification_type || 'info',
                title: payload.title || 'Notification',
                message: payload.message || 'You have a new notification.',
                created_at_human: payload.created_at_human || 'Just now',
                read_at: payload.read_at ?? null,
                url: payload.url || null,
            };
        };

        const createNotificationItem = (notification) => {
            const normalizedNotification = normalizeNotificationPayload(notification);
            const item = document.createElement('a');
            item.href = buildNotificationUrl(normalizedNotification.id);
            item.className = `notification-item ${normalizedNotification.read_at ? '' : 'unread'}`.trim();

            const icon = notificationIconClass(normalizedNotification.type);

            const iconWrapper = document.createElement('div');
            iconWrapper.className = 'notif-icon';

            const iconElement = document.createElement('i');
            iconElement.className = `fa-solid ${icon}`;
            iconWrapper.appendChild(iconElement);

            const content = document.createElement('div');
            content.className = 'notif-content';

            const titleRow = document.createElement('p');
            const titleStrong = document.createElement('strong');
            titleStrong.textContent = normalizedNotification.title;
            titleRow.appendChild(titleStrong);

            const messageRow = document.createElement('span');
            messageRow.textContent = normalizedNotification.message;

            const timeRow = document.createElement('small');
            timeRow.textContent = normalizedNotification.created_at_human;

            content.appendChild(titleRow);
            content.appendChild(messageRow);
            content.appendChild(timeRow);

            item.appendChild(iconWrapper);
            item.appendChild(content);

            return item;
        };

        const createEmptyNotificationItem = () => {
            const item = document.createElement('div');
            item.className = 'notification-item notification-item--empty';

            const iconWrapper = document.createElement('div');
            iconWrapper.className = 'notif-icon';

            const iconElement = document.createElement('i');
            iconElement.className = 'fa-solid fa-bell-slash';
            iconWrapper.appendChild(iconElement);

            const content = document.createElement('div');
            content.className = 'notif-content';

            const titleRow = document.createElement('p');
            const titleStrong = document.createElement('strong');
            titleStrong.textContent = 'No notifications';
            titleRow.appendChild(titleStrong);

            const messageRow = document.createElement('span');
            messageRow.textContent = "You're all caught up.";

            content.appendChild(titleRow);
            content.appendChild(messageRow);

            item.appendChild(iconWrapper);
            item.appendChild(content);

            return item;
        };

        const replaceNotificationMenuItems = (notifications = []) => {
            if (!notificationMenu || !notificationFooter) {
                return;
            }

            notificationMenu.querySelectorAll('.notification-item').forEach((item) => item.remove());

            if (!notifications.length) {
                notificationFooter.before(createEmptyNotificationItem());
                return;
            }

            notifications.forEach((notification) => {
                notificationFooter.before(createNotificationItem(notification));
            });
        };

        const prependNotificationItem = (notification) => {
            if (!notificationMenu || !notificationFooter) {
                return;
            }

            const normalizedNotification = normalizeNotificationPayload(notification);

            notificationMenu.querySelector('.notification-item--empty')?.remove();

            const existingItems = [...notificationMenu.querySelectorAll('.notification-item:not(.notification-item--empty)')];
            const duplicate = existingItems.find((item) => item.getAttribute('href') === buildNotificationUrl(normalizedNotification.id));

            if (duplicate) {
                duplicate.remove();
            }

            const item = createNotificationItem(normalizedNotification);
            notificationFooter.before(item);

            const refreshedItems = [...notificationMenu.querySelectorAll('.notification-item:not(.notification-item--empty)')];
            refreshedItems.slice(5).forEach((menuItem) => menuItem.remove());
        };

        const broadcastAdminNotificationEvent = (notification) => {
            document.dispatchEvent(new CustomEvent('admin:notification-received', {
                detail: normalizeNotificationPayload(notification),
            }));
        };

        const synchronizeNotificationFeed = (payload) => {
            if (!payload || typeof payload !== 'object') {
                return;
            }

            adminUnreadCount = Math.max(0, Number(payload.unreadCount) || 0);
            renderNotificationBadge();

            if (Array.isArray(payload.notifications)) {
                replaceNotificationMenuItems(payload.notifications.map(normalizeNotificationPayload));
            }
        };

        const fetchNotificationFeed = async () => {
            if (!adminNotificationFeedUrl) {
                return;
            }

            try {
                const response = await fetch(adminNotificationFeedUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    return;
                }

                synchronizeNotificationFeed(await response.json());
            } catch (error) {
                // Keep the existing UI state when background sync fails.
            }
        };

        const initializeAdminNotificationFeed = () => {
            renderNotificationBadge();
            void fetchNotificationFeed();

            if (notificationFeedTimer) {
                window.clearInterval(notificationFeedTimer);
            }

            notificationFeedTimer = window.setInterval(() => {
                void fetchNotificationFeed();
            }, 15000);

            window.addEventListener('focus', () => {
                void fetchNotificationFeed();
            });

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) {
                    void fetchNotificationFeed();
                }
            });

            if (!window.Echo || !adminNotificationUserId) {
                return;
            }

            window.Echo.private(`App.Models.User.${adminNotificationUserId}`)
                .notification((notification) => {
                    adminUnreadCount += 1;
                    renderNotificationBadge();
                    prependNotificationItem(notification);
                    broadcastAdminNotificationEvent(notification);
                });
        };

        window.updateAdminUnreadCount = (count) => {
            adminUnreadCount = Math.max(0, Number(count) || 0);
            renderNotificationBadge();
        };

        if (toggleButton && shell) {
            toggleButton.addEventListener('click', () => {
                shell.classList.toggle('sidebar-open');
            });
        }

        if (closeButton && shell) {
            closeButton.addEventListener('click', () => {
                closeSidebar();
            });
        }

        document.addEventListener('click', (event) => {
            if (!shell || !sidebar || !isMobileAdminViewport()) {
                return;
            }

            if (!shell.classList.contains('sidebar-open')) {
                return;
            }

            const clickedSidebar = sidebar.contains(event.target);
            const clickedToggle = toggleButton?.contains(event.target);

            if (!clickedSidebar && !clickedToggle) {
                closeSidebar();
            }
        });

        document.querySelectorAll('.sidebar__link').forEach((link) => {
            link.addEventListener('click', () => {
                if (isMobileAdminViewport()) {
                    closeSidebar();
                }
            });
        });

        adminDropdowns.forEach(({ container, trigger, menu }) => {
            const dropdown = document.querySelector(container);
            const triggerElement = dropdown ? dropdown.querySelector(trigger) : null;
            const menuElement = dropdown ? dropdown.querySelector(menu) : null;

            if (!dropdown || !triggerElement || !menuElement) {
                return;
            }

            triggerElement.addEventListener('click', (event) => {
                if (!isMobileAdminViewport()) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                adminDropdowns.forEach(({ container: otherContainer }) => {
                    const otherDropdown = document.querySelector(otherContainer);
                    if (otherDropdown && otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('is-open');
                    }
                });

                dropdown.classList.toggle('is-open');
            });

            const bindOpen = () => {
                if (isMobileAdminViewport()) {
                    return;
                }

                openDesktopDropdown(dropdown);
            };

            const bindClose = () => {
                if (isMobileAdminViewport()) {
                    return;
                }

                queueDesktopDropdownClose(dropdown);
            };

            dropdown.addEventListener('mouseenter', bindOpen);
            dropdown.addEventListener('mouseleave', bindClose);
            menuElement.addEventListener('mouseenter', bindOpen);
            menuElement.addEventListener('mouseleave', bindClose);
        });

        window.addEventListener('resize', () => {
            if (!isMobileAdminViewport()) {
                closeSidebar();
            }

            adminDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);

                if (!dropdown) {
                    return;
                }

                if (!isMobileAdminViewport()) {
                    dropdown.classList.remove('is-open');
                    return;
                }

                dropdown.classList.remove('is-hover-open');
                clearHoverTimer(dropdown);
            });
        });

        document.addEventListener('click', (event) => {
            adminDropdowns.forEach(({ container }) => {
                const dropdown = document.querySelector(container);
                if (dropdown && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('is-open');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('admin-toast');

            initializeAdminNotificationFeed();

            if (!toast) {
                return;
            }

            window.setTimeout(() => {
                toast.classList.add('toast-hide');

                window.setTimeout(() => {
                    toast.remove();
                }, 400);
            }, 3000);
        });
    </script>
</body>

</html>
