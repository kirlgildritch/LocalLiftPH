(function () {
    'use strict';

    const onReady = function (callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    const escapeHtml = function (value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    onReady(function () {
        const csrfTokenNode = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenNode ? csrfTokenNode.getAttribute('content') : '';
        const page = document.querySelector('[data-seller-notifications-page]');
        const list = document.querySelector('[data-seller-notification-list]');
        const totalValue = document.querySelector('[data-seller-notification-total]');
        const unreadValue = document.querySelector('[data-seller-notification-unread]');
        const showingValue = document.querySelector('[data-seller-notification-showing]');

        if (!page || !list || !totalValue || !unreadValue || !showingValue) {
            return;
        }

        const currentFilter = (page.dataset.currentFilter || 'all').toLowerCase();
        const currentPage = Number(page.dataset.currentPage || 1);
        const perPage = Number(page.dataset.perPage || 12);

        const formatCount = function (value) {
            return String(Math.max(0, Number(value) || 0));
        };

        const notificationConversationId = function (notification) {
            return Number(
                (notification && notification.related_id)
                || (notification && notification.route_params && notification.route_params.conversation)
                || 0
            );
        };

        const isMessageNotification = function (notification) {
            return String(notification && notification.action || '').toLowerCase() === 'buyer_message'
                && notificationConversationId(notification) > 0;
        };

        const matchesFilter = function (notification) {
            const type = (notification.type || notification.category || 'admin').toLowerCase();
            const unread = !notification.read_at;

            if (currentFilter === 'unread') {
                return unread;
            }

            if (currentFilter === 'all') {
                return true;
            }

            return type === currentFilter;
        };

        const iconClassFor = function (notification) {
            const action = (notification.action || '').toLowerCase();

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
                default:
                    return 'fa-bell';
            }
        };

        const tagFor = function (notification) {
            const type = (notification.type || notification.category || 'admin').toLowerCase();

            return ({
                all: 'All',
                unread: 'Unread',
                orders: 'Orders',
                messages: 'Messages',
                reviews: 'Reviews',
                admin: 'Admin',
                products: 'Products',
            }[type] || type.charAt(0).toUpperCase() + type.slice(1));
        };

        const buildReadForm = function (notificationId) {
            if (!csrfToken) {
                return '';
            }

            return [
                '<form method="POST" action="/seller-notifications/', notificationId, '/read" data-seller-notification-read-form>',
                '<input type="hidden" name="_token" value="', escapeHtml(csrfToken), '">',
                '<input type="hidden" name="_method" value="PATCH">',
                '<button class="seller-notification-icon-button" type="submit" title="Mark as read">',
                '<i class="fa-solid fa-check"></i>',
                '</button>',
                '</form>',
            ].join('');
        };

        const markNotificationAsRead = async function (form) {
            const row = form.closest('[data-seller-notification-id]');
            const notificationId = row ? row.dataset.sellerNotificationId : '';
            const response = await fetch(form.action, {
                body: new FormData(form),
                headers: Object.assign({
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }, csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                method: 'POST',
            });

            if (!response.ok) {
                throw new Error('Unable to mark notification as read.');
            }

            const payload = await response.json();
            document.dispatchEvent(new CustomEvent('seller:notification-read', {
                detail: payload.notification || { id: notificationId },
            }));
        };

        const buildRowHtml = function (notification) {
            const isUnread = !notification.read_at;
            const iconClass = iconClassFor(notification);
            const tag = tagFor(notification);
            const title = escapeHtml(notification.title || 'Notification');
            const message = escapeHtml(notification.message || 'You have a new notification.');
            const time = escapeHtml(notification.created_at_formatted || notification.created_at_human || 'Just now');
            const openUrl = '/seller-notifications/' + notification.id + '/open';
            const deleteUrl = '/seller-notifications/' + notification.id;
            const readForm = isUnread ? buildReadForm(notification.id) : '';
            const conversationId = notificationConversationId(notification);
            const chatAttributes = isMessageNotification(notification)
                ? ' data-chat-notification-link data-chat-conversation-id="' + escapeHtml(conversationId) + '"'
                : '';

            return [
                '<article class="seller-notification-row ', (isUnread ? 'unread' : ''), '"',
                ' data-seller-notification-row data-seller-notification-id="', escapeHtml(notification.id),
                '" data-seller-notification-read="', (isUnread ? '0' : '1'), '">',
                '<a href="', openUrl, '" class="seller-notification-row__icon"', chatAttributes, '>',
                '<i class="fa-solid ', iconClass, '"></i>',
                '</a>',
                '<a href="', openUrl, '" class="seller-notification-row__content"', chatAttributes, '>',
                '<div class="seller-notification-row__header">',
                '<h3>', title, '</h3>',
                '<span class="seller-notification-tag">', escapeHtml(tag), '</span>',
                '</div>',
                '<p>', message, '</p>',
                '<small>', time, '</small>',
                '</a>',
                '<div class="seller-notification-row__actions">',
                '<span class="seller-notification-status ', (isUnread ? 'unread' : ''), '" data-seller-notification-status>',
                (isUnread ? 'Unread' : 'Read'),
                '</span>',
                readForm,
                '<form method="POST" action="', deleteUrl, '">',
                '<input type="hidden" name="_token" value="', escapeHtml(csrfToken || ''), '">',
                '<input type="hidden" name="_method" value="DELETE">',
                '<button class="seller-notification-icon-button danger" type="submit" title="Delete">',
                '<i class="fa-solid fa-trash"></i>',
                '</button>',
                '</form>',
                '</div>',
                '</article>',
            ].join('');
        };

        const updateCounts = function (isUnread) {
            totalValue.textContent = formatCount(Number(totalValue.textContent || 0) + 1);

            if (isUnread) {
                unreadValue.textContent = formatCount(Number(unreadValue.textContent || 0) + 1);
            }

            if (currentPage === 1) {
                showingValue.textContent = formatCount(Math.min(perPage, Number(showingValue.textContent || 0) + 1));
            }
        };

        const prependRow = function (notification) {
            if (!notification || !notification.id || currentPage !== 1 || !matchesFilter(notification)) {
                return;
            }

            const existing = list.querySelector('[data-seller-notification-id="' + notification.id + '"]');
            if (existing) {
                existing.remove();
            }

            const empty = list.querySelector('[data-seller-notification-empty]');
            if (empty) {
                empty.remove();
            }

            list.insertAdjacentHTML('afterbegin', buildRowHtml(notification));

            Array.from(list.querySelectorAll('[data-seller-notification-row]'))
                .slice(perPage)
                .forEach(function (item) {
                    item.remove();
                });
        };

        document.addEventListener('seller:notification-received', function (event) {
            const notification = event.detail;

            if (!notification || !notification.id || currentPage !== 1 || !matchesFilter(notification)) {
                return;
            }

            updateCounts(!notification.read_at);
            prependRow(notification);
        });

        document.addEventListener('seller:notification-read', function (event) {
            const notificationId = event.detail && event.detail.id;
            unreadValue.textContent = formatCount(Number(unreadValue.textContent || 0) - 1);

            if (!notificationId) {
                return;
            }

            const row = list.querySelector('[data-seller-notification-id="' + notificationId + '"]');
            if (!row) {
                return;
            }

            if (currentFilter === 'unread') {
                row.remove();
                showingValue.textContent = formatCount(Number(showingValue.textContent || 0) - 1);

                if (!list.querySelector('[data-seller-notification-row]')) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'seller-notification-empty';
                    emptyState.dataset.sellerNotificationEmpty = '';
                    emptyState.innerHTML = '<i class="fa-regular fa-bell-slash"></i><p>No notifications found.</p>';
                    list.appendChild(emptyState);
                }
                return;
            }

            row.classList.remove('unread');
            row.dataset.sellerNotificationRead = '1';
            const status = row.querySelector('[data-seller-notification-status]');
            if (status) {
                status.classList.remove('unread');
                status.textContent = 'Read';
            }

            const readForm = row.querySelector('[data-seller-notification-read-form]');
            if (readForm) {
                readForm.remove();
            }
        });

        document.addEventListener('submit', function (event) {
            const form = event.target.closest('[data-seller-notification-read-form]');

            if (!form || !list.contains(form)) {
                return;
            }

            event.preventDefault();

            markNotificationAsRead(form).catch(function (error) {
                console.error(error);
            });
        });

        document.addEventListener('click', function (event) {
            const link = event.target.closest('[data-chat-notification-link]');

            if (!link || !list.contains(link)) {
                return;
            }

            if (!document.querySelector('[data-chat-widget]')) {
                return;
            }

            event.preventDefault();

            const conversationId = Number(link.dataset.chatConversationId || 0);
            if (!conversationId) {
                return;
            }

            const readForm = link.closest('[data-seller-notification-row]')?.querySelector('[data-seller-notification-read-form]');

            const openChat = function () {
                document.dispatchEvent(new CustomEvent('chat-widget:open-conversation', {
                    detail: {
                        conversationId: conversationId,
                    },
                }));
            };

            if (!readForm) {
                openChat();
                return;
            }

            markNotificationAsRead(readForm)
                .catch(function (error) {
                    console.error(error);
                })
                .finally(openChat);
        });
    });
}());
