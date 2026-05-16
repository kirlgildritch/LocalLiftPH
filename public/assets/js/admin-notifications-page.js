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
        const notificationList = document.querySelector('[data-notification-list]');
        const paginationWrapper = document.querySelector('[data-notification-pagination-wrapper]');
        const totalValue = document.querySelector('[data-notification-total]');
        const unreadValue = document.querySelector('[data-notification-unread]');
        const showingValue = document.querySelector('[data-notification-showing]');
        const notificationsPage = document.querySelector('.admin-notifications-page');
        const readAllButton = document.querySelector('[data-notification-read-all-button]');
        const clearReadButton = document.querySelector('[data-notification-clear-read-button]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!notificationList || !paginationWrapper || !totalValue || !unreadValue || !showingValue || !notificationsPage) {
            return;
        }

        const baseUrl = (notificationsPage.dataset.notificationBaseUrl || '/admin/notifications').replace(/\/$/, '');
        const currentPage = Number(notificationList.dataset.currentPage || 1);
        const perPage = Number(notificationList.dataset.perPage || 12);
        const statusFilter = (notificationList.dataset.filterStatus || '').trim().toLowerCase();
        const typeFilter = (notificationList.dataset.filterType || 'all').trim().toLowerCase();
        const searchFilter = (notificationList.dataset.filterSearch || '').trim().toLowerCase();
        let isRefreshingPage = false;
        let adminReadCount = Number(notificationsPage.dataset.notificationReadCount || 0);

        const formatCount = function (value) {
            return String(Math.max(0, Number(value) || 0));
        };

        const iconClassForType = function (type) {
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

        const matchesPageFilters = function (notification) {
            const notificationType = String(notification.type || '').toLowerCase();
            const haystack = (String(notification.title || '') + ' ' + String(notification.message || '')).toLowerCase();

            if (statusFilter === 'read') {
                return false;
            }

            if (typeFilter && typeFilter !== 'all' && notificationType !== typeFilter) {
                return false;
            }

            if (searchFilter && !haystack.includes(searchFilter)) {
                return false;
            }

            return currentPage === 1;
        };

        const buildNotificationUrl = function (id, suffix) {
            return baseUrl + '/' + id + suffix;
        };

        const buildActionForm = function (config) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = config.action;

            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            if (config.method !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = config.method;
                form.appendChild(methodInput);
            }

            const button = document.createElement('button');
            button.type = 'submit';
            button.className = ('notification-icon-button ' + (config.danger ? 'danger' : '')).trim();
            button.title = config.title;

            const iconElement = document.createElement('i');
            iconElement.className = 'fa-solid ' + config.icon;
            button.appendChild(iconElement);
            form.appendChild(button);

            return form;
        };

        const createNotificationRow = function (notification) {
            const row = document.createElement('div');
            row.className = 'notification-row unread';
            row.dataset.notificationRow = '';
            row.dataset.notificationId = notification.id;
            row.dataset.notificationType = notification.type || 'info';
            row.dataset.notificationRead = '0';

            const openUrl = buildNotificationUrl(notification.id, '/open');
            const readUrl = buildNotificationUrl(notification.id, '/read');
            const deleteUrl = baseUrl + '/' + notification.id;

            const iconLink = document.createElement('a');
            iconLink.href = openUrl;
            iconLink.className = 'notification-row__icon';

            const iconElement = document.createElement('i');
            iconElement.className = 'fa-solid ' + iconClassForType(notification.type);
            iconLink.appendChild(iconElement);

            const contentLink = document.createElement('a');
            contentLink.href = openUrl;
            contentLink.className = 'notification-row__content';

            const title = document.createElement('h3');
            title.textContent = notification.title || 'Notification';

            const message = document.createElement('p');
            message.textContent = notification.message || 'You have a new notification.';

            const time = document.createElement('small');
            time.textContent = notification.created_at_human || 'Just now';

            contentLink.appendChild(title);
            contentLink.appendChild(message);
            contentLink.appendChild(time);

            const actions = document.createElement('div');
            actions.className = 'notification-row__right';

            const status = document.createElement('span');
            status.className = 'notification-status unread';
            status.dataset.notificationStatus = '';
            status.textContent = 'Unread';
            actions.appendChild(status);

            const readForm = buildActionForm({
                action: readUrl,
                method: 'PATCH',
                title: 'Mark as read',
                icon: 'fa-check',
            });
            readForm.dataset.notificationMarkReadForm = '';
            actions.appendChild(readForm);

            const deleteForm = buildActionForm({
                action: deleteUrl,
                method: 'DELETE',
                title: 'Delete',
                icon: 'fa-trash',
                danger: true,
            });
            deleteForm.dataset.notificationDeleteForm = '';
            actions.appendChild(deleteForm);

            row.appendChild(iconLink);
            row.appendChild(contentLink);
            row.appendChild(actions);

            return row;
        };

        const updateSummaryCounts = function (matchedCurrentView) {
            const nextTotal = Number(totalValue.textContent || 0) + 1;
            const nextUnread = Number(unreadValue.textContent || 0) + 1;
            totalValue.textContent = formatCount(nextTotal);
            unreadValue.textContent = formatCount(nextUnread);

            if (!matchedCurrentView) {
                return;
            }

            const currentRows = notificationList.querySelectorAll('[data-notification-row]').length;
            showingValue.textContent = formatCount(Math.min(currentRows + 1, perPage));
        };

        const updateBulkActionState = function () {
            const unreadCount = Math.max(0, Number(unreadValue.textContent || 0));

            if (readAllButton) {
                readAllButton.disabled = unreadCount === 0;
            }

            if (clearReadButton) {
                clearReadButton.disabled = adminReadCount === 0;
            }
        };

        const syncUnreadCount = function (nextUnreadCount) {
            const sanitizedUnreadCount = Math.max(0, Number(nextUnreadCount) || 0);
            unreadValue.textContent = formatCount(sanitizedUnreadCount);
            updateBulkActionState();

            if (typeof window.updateAdminUnreadCount === 'function') {
                window.updateAdminUnreadCount(sanitizedUnreadCount);
            }
        };

        const syncReadCount = function (nextReadCount) {
            adminReadCount = Math.max(0, Number(nextReadCount) || 0);
            notificationsPage.dataset.notificationReadCount = String(adminReadCount);
            updateBulkActionState();
        };

        const syncDropdownNotificationState = function (notificationId) {
            const dropdownItem = document.querySelector(
                '.admin-notification-menu .notification-item[href="' + buildNotificationUrl(notificationId, '/open') + '"]'
            );

            dropdownItem?.classList.remove('unread');
        };

        const removeDropdownNotification = function (notificationId) {
            const dropdownItem = document.querySelector(
                '.admin-notification-menu .notification-item[href="' + buildNotificationUrl(notificationId, '/open') + '"]'
            );

            dropdownItem?.remove();

            const dropdownListItems = document.querySelectorAll(
                '.admin-notification-menu .notification-item:not(.notification-item--empty)'
            );

            if (dropdownListItems.length !== 0) {
                return;
            }

            const notificationMenu = document.querySelector('.admin-notification-menu');
            const notificationFooter = notificationMenu?.querySelector('.notification-footer');

            if (!notificationMenu || !notificationFooter || notificationMenu.querySelector('.notification-item--empty')) {
                return;
            }

            const emptyItem = document.createElement('div');
            emptyItem.className = 'notification-item notification-item--empty';

            const iconWrapper = document.createElement('div');
            iconWrapper.className = 'notif-icon';

            const icon = document.createElement('i');
            icon.className = 'fa-solid fa-bell-slash';
            iconWrapper.appendChild(icon);

            const content = document.createElement('div');
            content.className = 'notif-content';

            const title = document.createElement('p');
            const strong = document.createElement('strong');
            strong.textContent = 'No notifications';
            title.appendChild(strong);

            const subtitle = document.createElement('span');
            subtitle.textContent = "You're all caught up.";

            content.appendChild(title);
            content.appendChild(subtitle);

            emptyItem.appendChild(iconWrapper);
            emptyItem.appendChild(content);
            notificationFooter.before(emptyItem);
        };

        const decrementTotalCount = function (amount) {
            totalValue.textContent = formatCount(Number(totalValue.textContent || 0) - (amount || 1));
        };

        const decrementShowingCount = function (amount) {
            showingValue.textContent = formatCount(Number(showingValue.textContent || 0) - (amount || 1));
        };

        const ensureEmptyState = function () {
            const rows = notificationList.querySelectorAll('[data-notification-row]');

            if (rows.length > 0 || notificationList.querySelector('[data-notification-empty]')) {
                return;
            }

            const emptyState = document.createElement('div');
            emptyState.className = 'notification-empty';
            emptyState.dataset.notificationEmpty = '';

            const icon = document.createElement('i');
            icon.className = 'fa-regular fa-bell-slash';

            const message = document.createElement('p');
            message.textContent = 'No notifications found.';

            emptyState.appendChild(icon);
            emptyState.appendChild(message);
            notificationList.appendChild(emptyState);
        };

        const removeRow = function (row) {
            if (!row) {
                return;
            }

            row.remove();
            decrementShowingCount(1);
            ensureEmptyState();
        };

        const markRowAsRead = function (row) {
            if (!row || row.dataset.notificationRead === '1') {
                return false;
            }

            const status = row.querySelector('[data-notification-status]');
            const form = row.querySelector('[data-notification-mark-read-form]');

            row.dataset.notificationRead = '1';
            row.classList.remove('unread');
            status?.classList.remove('unread');

            if (status) {
                status.textContent = 'Read';
            }

            form?.remove();

            return true;
        };

        const requestJson = async function (form) {
            const submitButton = form.querySelector('button[type="submit"]');
            const loadingHelper = window.LocalLiftActionLoading;

            if (submitButton && loadingHelper) {
                loadingHelper.start(submitButton, {
                    label: submitButton.textContent.trim() ? 'Loading...' : '',
                });
            }

            submitButton?.setAttribute('disabled', 'disabled');
            submitButton?.classList.add('is-busy');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: Object.assign({
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }, csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                    body: new FormData(form),
                });

                if (!response.ok) {
                    throw new Error('Request failed.');
                }

                return await response.json();
            } finally {
                if (submitButton && loadingHelper && submitButton.isConnected) {
                    loadingHelper.stop(submitButton);
                }

                submitButton?.removeAttribute('disabled');
                submitButton?.classList.remove('is-busy');
                updateBulkActionState();
            }
        };

        const refreshCurrentPageFromServer = async function () {
            if (currentPage !== 1 || isRefreshingPage) {
                return;
            }

            isRefreshingPage = true;

            try {
                const response = await fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html,application/xhtml+xml',
                    },
                });

                if (!response.ok) {
                    throw new Error('Failed to refresh notifications.');
                }

                const html = await response.text();
                const parser = new DOMParser();
                const documentFragment = parser.parseFromString(html, 'text/html');
                const nextNotificationList = documentFragment.querySelector('[data-notification-list]');
                const nextPaginationWrapper = documentFragment.querySelector('[data-notification-pagination-wrapper]');
                const nextTotal = documentFragment.querySelector('[data-notification-total]');
                const nextUnread = documentFragment.querySelector('[data-notification-unread]');
                const nextShowing = documentFragment.querySelector('[data-notification-showing]');
                const nextNotificationsPage = documentFragment.querySelector('.admin-notifications-page');

                if (nextNotificationList) {
                    notificationList.innerHTML = nextNotificationList.innerHTML;
                }

                if (nextPaginationWrapper) {
                    paginationWrapper.innerHTML = nextPaginationWrapper.innerHTML;
                }

                if (nextTotal) {
                    totalValue.textContent = nextTotal.textContent?.trim() || '0';
                }

                if (nextShowing) {
                    showingValue.textContent = nextShowing.textContent?.trim() || '0';
                }

                if (nextUnread) {
                    syncUnreadCount(Number(nextUnread.textContent?.trim() || 0));
                }

                if (nextNotificationsPage) {
                    syncReadCount(Number(nextNotificationsPage.dataset.notificationReadCount || adminReadCount));
                }
            } finally {
                isRefreshingPage = false;
            }
        };

        notificationList.addEventListener('submit', async function (event) {
            const form = event.target.closest('[data-notification-mark-read-form]');

            if (!form) {
                return;
            }

            event.preventDefault();

            const row = form.closest('[data-notification-row]');
            const status = row?.querySelector('[data-notification-status]');

            if (!row || !status || row.dataset.notificationRead === '1') {
                return;
            }

            try {
                const payload = await requestJson(form);
                syncUnreadCount(Number(payload.unreadCount ?? unreadValue.textContent ?? 0));
                syncReadCount(Number(payload.readCount ?? adminReadCount));
                syncDropdownNotificationState(row.dataset.notificationId);

                if (statusFilter === 'unread') {
                    removeRow(row);
                    await refreshCurrentPageFromServer();
                    return;
                }

                markRowAsRead(row);
                await refreshCurrentPageFromServer();
            } catch (error) {
                // Leave the existing row state unchanged if the request fails.
            }
        });

        document.addEventListener('submit', async function (event) {
            const readAllForm = event.target.closest('[data-notification-read-all-form]');
            const clearReadForm = event.target.closest('[data-notification-clear-read-form]');
            const deleteForm = event.target.closest('[data-notification-delete-form]');

            if (!readAllForm && !clearReadForm && !deleteForm) {
                return;
            }

            event.preventDefault();

            if (readAllForm) {
                try {
                    const payload = await requestJson(readAllForm);
                    const unreadRows = Array.from(
                        notificationList.querySelectorAll('[data-notification-row][data-notification-read="0"]')
                    );

                    unreadRows.forEach(function (row) {
                        if (statusFilter === 'unread') {
                            removeRow(row);
                            syncDropdownNotificationState(row.dataset.notificationId);
                            return;
                        }

                        markRowAsRead(row);
                        syncDropdownNotificationState(row.dataset.notificationId);
                    });

                    syncUnreadCount(Number(payload.unreadCount ?? 0));
                    syncReadCount(Number(payload.readCount ?? adminReadCount));
                    await refreshCurrentPageFromServer();
                } catch (error) {
                    // Preserve the current UI if the bulk request fails.
                }

                return;
            }

            if (clearReadForm) {
                try {
                    const payload = await requestJson(clearReadForm);
                    const deletedCount = Number(payload.deletedCount ?? 0);
                    const readRows = Array.from(
                        notificationList.querySelectorAll('[data-notification-row][data-notification-read="1"]')
                    );

                    readRows.forEach(function (row) {
                        removeDropdownNotification(row.dataset.notificationId);
                        removeRow(row);
                    });

                    decrementTotalCount(Math.min(deletedCount, readRows.length || deletedCount));
                    syncUnreadCount(Number(payload.unreadCount ?? unreadValue.textContent ?? 0));
                    syncReadCount(Number(payload.readCount ?? 0));
                    await refreshCurrentPageFromServer();
                } catch (error) {
                    // Preserve the current UI if the bulk request fails.
                }

                return;
            }

            if (!deleteForm) {
                return;
            }

            const row = deleteForm.closest('[data-notification-row]');

            if (!row) {
                return;
            }

            try {
                const payload = await requestJson(deleteForm);
                syncUnreadCount(Number(payload.unreadCount ?? unreadValue.textContent ?? 0));
                syncReadCount(Number(payload.readCount ?? adminReadCount));
                removeDropdownNotification(row.dataset.notificationId);
                removeRow(row);
                decrementTotalCount(1);
                await refreshCurrentPageFromServer();
            } catch (error) {
                // Preserve the current UI if the delete request fails.
            }
        });

        document.addEventListener('admin:notification-received', function (event) {
            const notification = event.detail;

            if (!notification?.id) {
                return;
            }

            const alreadyRendered = notificationList.querySelector('[data-notification-id="' + notification.id + '"]');
            if (alreadyRendered) {
                return;
            }

            const matchesCurrentView = matchesPageFilters(notification);
            updateSummaryCounts(matchesCurrentView);
            syncReadCount(adminReadCount);

            if (!matchesCurrentView) {
                return;
            }

            notificationList.querySelector('[data-notification-empty]')?.remove();
            notificationList.prepend(createNotificationRow(notification));

            Array.from(notificationList.querySelectorAll('[data-notification-row]'))
                .slice(perPage)
                .forEach(function (item) {
                    item.remove();
                });
        });

        updateBulkActionState();
    });
}());
