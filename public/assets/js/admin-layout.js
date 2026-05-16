(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    onReady(function () {
        const toggleButton = document.querySelector('[data-sidebar-toggle]');
        const closeButton = document.querySelector('[data-sidebar-close]');
        const shell = document.querySelector('.admin-shell');
        const sidebar = document.querySelector('.sidebar');
        const notificationMenu = document.querySelector('.admin-notification-menu');
        const notificationFooter = notificationMenu ? notificationMenu.querySelector('.notification-footer') : null;
        const toast = document.getElementById('admin-toast');

        if (!shell) {
            return;
        }

        const adminDropdowns = [
            { container: '.admin-notification-dropdown', trigger: '.admin-notification-btn', menu: '.admin-notification-menu' },
        ];
        const hoverCloseDelay = 180;
        const hoverTimers = new Map();
        const notificationBaseUrl = (shell.dataset.adminNotificationBaseUrl || '/admin/notifications').replace(/\/$/, '');
        const adminNotificationUserId = Number(shell.dataset.adminNotificationUserId || 0);
        const adminNotificationFeedUrl = shell.dataset.adminNotificationFeedUrl || '';
        let adminUnreadCount = Math.max(0, Number(shell.dataset.adminUnreadCount || 0));
        let lastRenderedUnreadCount = adminUnreadCount;
        let notificationFeedTimer = null;

        const isMobileAdminViewport = function () {
            return window.matchMedia('(max-width: 980px)').matches;
        };

        const closeSidebar = function () {
            shell.classList.remove('sidebar-open');
        };

        const clearHoverTimer = function (dropdown) {
            const timer = hoverTimers.get(dropdown);

            if (timer) {
                window.clearTimeout(timer);
                hoverTimers.delete(dropdown);
            }
        };

        const openDesktopDropdown = function (dropdown) {
            clearHoverTimer(dropdown);
            dropdown.classList.add('is-hover-open');
        };

        const queueDesktopDropdownClose = function (dropdown) {
            clearHoverTimer(dropdown);
            const timer = window.setTimeout(function () {
                dropdown.classList.remove('is-hover-open');
                hoverTimers.delete(dropdown);
            }, hoverCloseDelay);

            hoverTimers.set(dropdown, timer);
        };

        const notificationIconClass = function (type) {
            switch (String(type || '').toLowerCase()) {
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

        const buildNotificationUrl = function (id) {
            return notificationBaseUrl + '/' + id + '/open';
        };

        const updateNotificationHeaderMeta = function () {
            const meta = document.querySelector('[data-admin-notification-meta]');

            if (!meta) {
                return;
            }

            meta.textContent = adminUnreadCount > 0
                ? adminUnreadCount + ' unread notification' + (adminUnreadCount === 1 ? '' : 's')
                : "You're all caught up.";
        };

        const renderNotificationBadge = function () {
            const triggerButton = document.querySelector('.admin-notification-btn');
            const existingBadge = document.querySelector('.admin-notif-badge');

            if (!triggerButton) {
                return;
            }

            if (adminUnreadCount <= 0) {
                triggerButton.classList.remove('has-unread');
                existingBadge && existingBadge.remove();
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
            shell.dataset.adminUnreadCount = String(adminUnreadCount);
            updateNotificationHeaderMeta();
        };

        const normalizeNotificationPayload = function (notification) {
            const payload = notification && notification.data && typeof notification.data === 'object'
                ? Object.assign({}, notification.data, {
                    id: notification.id != null ? notification.id : notification.data.id,
                    read_at: notification.read_at,
                })
                : Object.assign({}, notification);

            return {
                id: payload.id,
                type: payload.type || payload.notification_type || 'info',
                title: payload.title || 'Notification',
                message: payload.message || 'You have a new notification.',
                created_at_human: payload.created_at_human || 'Just now',
                read_at: payload.read_at || null,
                url: payload.url || null,
            };
        };

        const createNotificationItem = function (notification) {
            const normalizedNotification = normalizeNotificationPayload(notification);
            const item = document.createElement('a');
            item.href = buildNotificationUrl(normalizedNotification.id);
            item.className = ('notification-item ' + (normalizedNotification.read_at ? '' : 'unread')).trim();

            const iconWrapper = document.createElement('div');
            iconWrapper.className = 'notif-icon';

            const iconElement = document.createElement('i');
            iconElement.className = 'fa-solid ' + notificationIconClass(normalizedNotification.type);
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

        const createEmptyNotificationItem = function () {
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

        const replaceNotificationMenuItems = function (notifications) {
            if (!notificationMenu || !notificationFooter) {
                return;
            }

            notificationMenu.querySelectorAll('.notification-item').forEach(function (item) {
                item.remove();
            });

            if (!Array.isArray(notifications) || notifications.length === 0) {
                notificationFooter.before(createEmptyNotificationItem());
                return;
            }

            notifications.forEach(function (notification) {
                notificationFooter.before(createNotificationItem(notification));
            });
        };

        const prependNotificationItem = function (notification) {
            if (!notificationMenu || !notificationFooter) {
                return;
            }

            const normalizedNotification = normalizeNotificationPayload(notification);
            notificationMenu.querySelector('.notification-item--empty')?.remove();

            const existingItems = Array.from(
                notificationMenu.querySelectorAll('.notification-item:not(.notification-item--empty)')
            );
            const duplicate = existingItems.find(function (item) {
                return item.getAttribute('href') === buildNotificationUrl(normalizedNotification.id);
            });

            if (duplicate) {
                duplicate.remove();
            }

            notificationFooter.before(createNotificationItem(normalizedNotification));

            const refreshedItems = Array.from(
                notificationMenu.querySelectorAll('.notification-item:not(.notification-item--empty)')
            );
            refreshedItems.slice(5).forEach(function (menuItem) {
                menuItem.remove();
            });
        };

        const broadcastAdminNotificationEvent = function (notification) {
            document.dispatchEvent(new CustomEvent('admin:notification-received', {
                detail: normalizeNotificationPayload(notification),
            }));
        };

        const synchronizeNotificationFeed = function (payload) {
            if (!payload || typeof payload !== 'object') {
                return;
            }

            adminUnreadCount = Math.max(0, Number(payload.unreadCount) || 0);
            renderNotificationBadge();

            if (Array.isArray(payload.notifications)) {
                replaceNotificationMenuItems(payload.notifications.map(normalizeNotificationPayload));
            }
        };

        const fetchNotificationFeed = async function () {
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
                // Preserve the current UI state if background sync fails.
            }
        };

        const initializeAdminNotificationFeed = function () {
            renderNotificationBadge();
            void fetchNotificationFeed();

            if (notificationFeedTimer) {
                window.clearInterval(notificationFeedTimer);
            }

            notificationFeedTimer = window.setInterval(function () {
                void fetchNotificationFeed();
            }, 15000);

            window.addEventListener('focus', function () {
                void fetchNotificationFeed();
            });

            document.addEventListener('visibilitychange', function () {
                if (!document.hidden) {
                    void fetchNotificationFeed();
                }
            });

            if (!window.Echo || !adminNotificationUserId) {
                return;
            }

            window.Echo.private('App.Models.User.' + adminNotificationUserId)
                .notification(function (notification) {
                    adminUnreadCount += 1;
                    renderNotificationBadge();
                    prependNotificationItem(notification);
                    broadcastAdminNotificationEvent(notification);
                });
        };

        window.updateAdminUnreadCount = function (count) {
            adminUnreadCount = Math.max(0, Number(count) || 0);
            renderNotificationBadge();
        };

        if (toggleButton) {
            toggleButton.addEventListener('click', function () {
                shell.classList.toggle('sidebar-open');
            });
        }

        if (closeButton) {
            closeButton.addEventListener('click', function () {
                closeSidebar();
            });
        }

        document.addEventListener('click', function (event) {
            if (!sidebar || !isMobileAdminViewport()) {
                return;
            }

            if (!shell.classList.contains('sidebar-open')) {
                return;
            }

            const clickedSidebar = sidebar.contains(event.target);
            const clickedToggle = toggleButton ? toggleButton.contains(event.target) : false;

            if (!clickedSidebar && !clickedToggle) {
                closeSidebar();
            }
        });

        document.querySelectorAll('.sidebar__link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (isMobileAdminViewport()) {
                    closeSidebar();
                }
            });
        });

        adminDropdowns.forEach(function (config) {
            const dropdown = document.querySelector(config.container);
            const triggerElement = dropdown ? dropdown.querySelector(config.trigger) : null;
            const menuElement = dropdown ? dropdown.querySelector(config.menu) : null;

            if (!dropdown || !triggerElement || !menuElement) {
                return;
            }

            triggerElement.addEventListener('click', function (event) {
                if (!isMobileAdminViewport()) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                adminDropdowns.forEach(function (otherConfig) {
                    const otherDropdown = document.querySelector(otherConfig.container);

                    if (otherDropdown && otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('is-open');
                    }
                });

                dropdown.classList.toggle('is-open');
            });

            const bindOpen = function () {
                if (isMobileAdminViewport()) {
                    return;
                }

                openDesktopDropdown(dropdown);
            };

            const bindClose = function () {
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

        window.addEventListener('resize', function () {
            if (!isMobileAdminViewport()) {
                closeSidebar();
            }

            adminDropdowns.forEach(function (config) {
                const dropdown = document.querySelector(config.container);

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

        document.addEventListener('click', function (event) {
            adminDropdowns.forEach(function (config) {
                const dropdown = document.querySelector(config.container);

                if (dropdown && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('is-open');
                }
            });
        });

        initializeAdminNotificationFeed();

        if (toast) {
            window.setTimeout(function () {
                toast.classList.add('toast-hide');

                window.setTimeout(function () {
                    toast.remove();
                }, 400);
            }, 3000);
        }
    });
}());
