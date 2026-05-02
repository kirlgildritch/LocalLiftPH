@extends('layouts.admin')

@section('title', 'Admin Notifications')
@section('eyebrow', 'Notifications')
@section('page-title', 'Admin Notifications')

@push('styles')
    <style>
        .admin-notifications-page {
            display: grid;
            gap: 22px;
        }

        .notification-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .notification-summary__card,
        .notification-panel {
            background: #ffffff;
            border: 1px solid #dbe5f4;
            border-radius: 18px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
        }

        .notification-summary__card {
            padding: 20px;
        }

        .notification-summary__label {
            margin: 0 0 8px;
            color: #6b7894;
            font-weight: 700;
            font-size: 14px;
        }

        .notification-summary__value {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            color: #2563eb;
        }

        .notification-panel__header {
            padding: 20px;
            border-bottom: 1px solid #dbe5f4;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .notification-panel__title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #1f2a44;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .notification-btn-action {
            border: 1px solid #d6e0f0;
            background: #fff;
            color: #1f2a44;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .notification-btn-action.primary {
            background: #3f6fd9;
            border-color: #3f6fd9;
            color: #fff;
        }

        .notification-btn-action.danger {
            color: #dc2626;
        }

        .notification-filters {
            padding: 18px 20px;
            border-bottom: 1px solid #dbe5f4;
            display: grid;
            grid-template-columns: 1fr 170px 170px auto;
            gap: 12px;
        }

        .notification-input,
        .notification-select {
            width: 100%;
            border: 1px solid #d6e0f0;
            border-radius: 12px;
            padding: 12px 14px;
            color: #1f2a44;
            outline: none;
            font: inherit;
            background: #fff;
        }

        .notification-list {
            display: grid;
        }

        .notification-row {
            display: grid;
            grid-template-columns: 48px 1fr auto;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid #dbe5f4;
            align-items: center;
            text-decoration: none;
            color: inherit;
        }

        .notification-row.unread {
            background: #eef4ff;
        }

        .notification-row__icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: #e9f1ff;
            color: #3f6fd9;
            display: grid;
            place-items: center;
            font-size: 18px;
        }

        .notification-row__content h3 {
            margin: 0 0 5px;
            color: #1f2a44;
            font-size: 16px;
            font-weight: 800;
        }

        .notification-row__content p {
            margin: 0 0 6px;
            color: #6b7894;
            line-height: 1.4;
        }

        .notification-row__content small {
            color: #7d8aa4;
            font-weight: 600;
        }

        .notification-row__right {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .notification-status {
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 800;
            background: #e8f7ed;
            color: #239246;
        }

        .notification-status.unread {
            background: #fff4d8;
            color: #ad7400;
        }

        .notification-icon-button {
            border: 1px solid #d6e0f0;
            background: #fff;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            color: #44536f;
            cursor: pointer;
        }

        .notification-icon-button.danger {
            color: #dc2626;
        }

        .notification-empty {
            padding: 50px 20px;
            text-align: center;
            color: #6b7894;
        }

        .notification-empty i {
            font-size: 36px;
            color: #a8b4ca;
            margin-bottom: 12px;
        }

        .notification-pagination {
            padding: 18px 20px;
        }

        @media (max-width: 980px) {
            .notification-summary {
                grid-template-columns: 1fr;
            }

            .notification-filters {
                grid-template-columns: 1fr;
            }

            .notification-row {
                grid-template-columns: 42px 1fr;
            }

            .notification-row__right {
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="admin-notifications-page" data-notification-read-count="{{ $readCount }}">
        <div class="notification-summary">
            <div class="notification-summary__card">
                <p class="notification-summary__label">Total Notifications</p>
                <p class="notification-summary__value" data-notification-total>{{ $notifications->total() }}</p>
            </div>

            <div class="notification-summary__card">
                <p class="notification-summary__label">Unread</p>
                <p class="notification-summary__value" data-notification-unread>{{ $unreadCount }}</p>
            </div>

            <div class="notification-summary__card">
                <p class="notification-summary__label">Showing</p>
                <p class="notification-summary__value" data-notification-showing>{{ $notifications->count() }}</p>
            </div>
        </div>

        <section class="notification-panel">
            <div class="notification-panel__header">
                <h3 class="notification-panel__title">All Notifications</h3>

                <div class="notification-actions">
                    <form method="POST" action="{{ route('admin.notifications.read-all') }}"
                        data-notification-read-all-form>
                        @csrf
                        @method('PATCH')
                        <button class="notification-btn-action primary" type="submit"
                            data-notification-read-all-button
                            @disabled($unreadCount === 0)>
                            <i class="fa-solid fa-check-double"></i>
                            Mark all as read
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.notifications.clear-read') }}"
                        data-notification-clear-read-form>
                        @csrf
                        @method('DELETE')
                        <button class="notification-btn-action danger" type="submit"
                            data-notification-clear-read-button
                            @disabled($readCount === 0)>
                            <i class="fa-solid fa-trash"></i>
                            Clear read
                        </button>
                    </form>
                </div>
            </div>

            <form class="notification-filters" method="GET" action="{{ route('admin.notifications.index') }}">
                <input class="notification-input" type="search" name="search" value="{{ request('search') }}"
                    placeholder="Search notifications...">

                <select class="notification-select" name="status">
                    <option value="">All Status</option>
                    <option value="unread" @selected(request('status') === 'unread')>Unread</option>
                    <option value="read" @selected(request('status') === 'read')>Read</option>
                </select>

                <select class="notification-select" name="type">
                    <option value="all" @selected(request('type', 'all') === 'all')>All Types</option>
                    <option value="reports" @selected(request('type') === 'reports')>Reports</option>
                    <option value="seller_review" @selected(request('type') === 'seller_review')>Seller Review</option>
                    <option value="orders" @selected(request('type') === 'orders')>Orders</option>
                    <option value="products" @selected(request('type') === 'products')>Products</option>
                </select>

                <button class="notification-btn-action primary" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Filter
                </button>
            </form>

            <div class="notification-list"
                data-notification-list
                data-current-page="{{ $notifications->currentPage() }}"
                data-per-page="{{ $notifications->perPage() }}"
                data-filter-status="{{ request('status', '') }}"
                data-filter-type="{{ request('type', 'all') }}"
                data-filter-search="{{ request('search', '') }}">
                @forelse ($notifications as $notification)
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

                    <div class="notification-row {{ $notification->read_at ? '' : 'unread' }}"
                        data-notification-row
                        data-notification-id="{{ $notification->id }}"
                        data-notification-type="{{ $type }}"
                        data-notification-read="{{ $notification->read_at ? '1' : '0' }}">
                        <a href="{{ route('admin.notifications.open', $notification) }}" class="notification-row__icon">
                            <i class="fa-solid {{ $icon }}"></i>
                        </a>

                        <a href="{{ route('admin.notifications.open', $notification) }}" class="notification-row__content">
                            <h3>{{ $title }}</h3>
                            <p>{{ $message }}</p>
                            <small>{{ $notification->created_at?->format('M d, Y h:i A') }}</small>
                        </a>

                        <div class="notification-row__right">
                            <span class="notification-status {{ $notification->read_at ? '' : 'unread' }}"
                                data-notification-status>
                                {{ $notification->read_at ? 'Read' : 'Unread' }}
                            </span>

                            @if (!$notification->read_at)
                                <form method="POST" action="{{ route('admin.notifications.read', $notification) }}"
                                    data-notification-mark-read-form>
                                    @csrf
                                    @method('PATCH')
                                    <button class="notification-icon-button" type="submit" title="Mark as read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}"
                                data-notification-delete-form>
                                @csrf
                                @method('DELETE')
                                <button class="notification-icon-button danger" type="submit" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="notification-empty" data-notification-empty>
                        <i class="fa-regular fa-bell-slash"></i>
                        <p>No notifications found.</p>
                    </div>
                @endforelse
            </div>

            <div data-notification-pagination-wrapper>
                @if ($notifications->hasPages())
                    <div class="notification-pagination">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const notificationList = document.querySelector('[data-notification-list]');
            const paginationWrapper = document.querySelector('[data-notification-pagination-wrapper]');
            const totalValue = document.querySelector('[data-notification-total]');
            const unreadValue = document.querySelector('[data-notification-unread]');
            const showingValue = document.querySelector('[data-notification-showing]');
            const notificationsPage = document.querySelector('.admin-notifications-page');
            const readAllButton = document.querySelector('[data-notification-read-all-button]');
            const clearReadButton = document.querySelector('[data-notification-clear-read-button]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            let isRefreshingPage = false;
            let adminReadCount = Number(notificationsPage?.dataset.notificationReadCount || {{ (int) $readCount }});

            if (!notificationList || !paginationWrapper || !totalValue || !unreadValue || !showingValue) {
                return;
            }

            const currentPage = Number(notificationList.dataset.currentPage || 1);
            const perPage = Number(notificationList.dataset.perPage || 12);
            const statusFilter = (notificationList.dataset.filterStatus || '').trim().toLowerCase();
            const typeFilter = (notificationList.dataset.filterType || 'all').trim().toLowerCase();
            const searchFilter = (notificationList.dataset.filterSearch || '').trim().toLowerCase();

            const formatCount = (value) => String(Math.max(0, value));
            const iconClassForType = (type) => {
                switch ((type || '').toLowerCase()) {
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

            const matchesPageFilters = (notification) => {
                const notificationType = (notification.type || '').toLowerCase();
                const haystack = `${notification.title || ''} ${notification.message || ''}`.toLowerCase();

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

            const buildActionForm = ({ action, method, title, icon, danger = false }) => {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;

                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }

                if (method !== 'POST') {
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = method;
                    form.appendChild(methodInput);
                }

                const button = document.createElement('button');
                button.type = 'submit';
                button.className = `notification-icon-button ${danger ? 'danger' : ''}`.trim();
                button.title = title;

                const iconElement = document.createElement('i');
                iconElement.className = `fa-solid ${icon}`;
                button.appendChild(iconElement);
                form.appendChild(button);

                return form;
            };

            const createNotificationRow = (notification) => {
                const row = document.createElement('div');
                row.className = 'notification-row unread';
                row.dataset.notificationRow = '';
                row.dataset.notificationId = notification.id;
                row.dataset.notificationType = notification.type || 'info';
                row.dataset.notificationRead = '0';

                const openUrl = `/admin/notifications/${notification.id}/open`;
                const readUrl = `/admin/notifications/${notification.id}/read`;
                const deleteUrl = `/admin/notifications/${notification.id}`;

                const iconLink = document.createElement('a');
                iconLink.href = openUrl;
                iconLink.className = 'notification-row__icon';

                const iconElement = document.createElement('i');
                iconElement.className = `fa-solid ${iconClassForType(notification.type)}`;
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

            const updateSummaryCounts = (matchedCurrentView) => {
                const nextTotal = Number(totalValue.textContent || 0) + 1;
                const nextUnread = Number(unreadValue.textContent || 0) + 1;
                totalValue.textContent = formatCount(nextTotal);
                unreadValue.textContent = formatCount(nextUnread);

                if (!matchedCurrentView) {
                    return;
                }

                const currentRows = notificationList.querySelectorAll('[data-notification-row]').length;
                const nextShowing = Math.min(currentRows + 1, perPage);
                showingValue.textContent = formatCount(nextShowing);
            };

            const syncUnreadCount = (nextUnreadCount) => {
                const sanitizedUnreadCount = Math.max(0, Number(nextUnreadCount) || 0);
                unreadValue.textContent = formatCount(sanitizedUnreadCount);
                updateBulkActionState();

                if (typeof window.updateAdminUnreadCount === 'function') {
                    window.updateAdminUnreadCount(sanitizedUnreadCount);
                }
            };

            const syncReadCount = (nextReadCount) => {
                adminReadCount = Math.max(0, Number(nextReadCount) || 0);
                if (notificationsPage) {
                    notificationsPage.dataset.notificationReadCount = String(adminReadCount);
                }
                updateBulkActionState();
            };

            const updateBulkActionState = () => {
                const unreadCount = Math.max(0, Number(unreadValue.textContent || 0));

                if (readAllButton) {
                    readAllButton.disabled = unreadCount === 0;
                }

                if (clearReadButton) {
                    clearReadButton.disabled = adminReadCount === 0;
                }
            };

            const syncDropdownNotificationState = (notificationId) => {
                const dropdownItem = document.querySelector(
                    `.admin-notification-menu .notification-item[href="/admin/notifications/${notificationId}/open"]`
                );

                dropdownItem?.classList.remove('unread');
            };

            const removeDropdownNotification = (notificationId) => {
                const dropdownItem = document.querySelector(
                    `.admin-notification-menu .notification-item[href="/admin/notifications/${notificationId}/open"]`
                );

                dropdownItem?.remove();

                const dropdownListItems = document.querySelectorAll(
                    '.admin-notification-menu .notification-item:not(.notification-item--empty)'
                );

                if (dropdownListItems.length === 0) {
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
                }
            };

            const decrementTotalCount = (amount = 1) => {
                totalValue.textContent = formatCount(Number(totalValue.textContent || 0) - amount);
            };

            const decrementShowingCount = (amount = 1) => {
                showingValue.textContent = formatCount(Number(showingValue.textContent || 0) - amount);
            };

            const removeRow = (row) => {
                if (!row) {
                    return;
                }

                row.remove();
                decrementShowingCount(1);
                ensureEmptyState();
            };

            const markRowAsRead = (row) => {
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

            const ensureEmptyState = () => {
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

            const requestJson = async (form) => {
                const submitButton = form.querySelector('button[type="submit"]');
                submitButton?.setAttribute('disabled', 'disabled');
                submitButton?.classList.add('is-busy');

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
                        throw new Error('Request failed.');
                    }

                    return await response.json();
                } finally {
                    submitButton?.removeAttribute('disabled');
                    submitButton?.classList.remove('is-busy');
                    updateBulkActionState();
                }
            };

            const refreshCurrentPageFromServer = async () => {
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

            notificationList.addEventListener('submit', async (event) => {
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
                    const nextUnreadCount = Number(payload.unreadCount ?? unreadValue.textContent ?? 0);
                    const nextReadCount = Number(payload.readCount ?? adminReadCount);

                    syncUnreadCount(nextUnreadCount);
                    syncReadCount(nextReadCount);
                    syncDropdownNotificationState(row.dataset.notificationId);

                    if (statusFilter === 'unread') {
                        removeRow(row);
                        await refreshCurrentPageFromServer();
                        return;
                    }

                    markRowAsRead(row);
                    await refreshCurrentPageFromServer();
                } catch (error) {}
            });

            document.addEventListener('submit', async (event) => {
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
                        const nextUnreadCount = Number(payload.unreadCount ?? 0);
                        const nextReadCount = Number(payload.readCount ?? adminReadCount);
                        const unreadRows = [...notificationList.querySelectorAll('[data-notification-row][data-notification-read="0"]')];

                        unreadRows.forEach((row) => {
                            if (statusFilter === 'unread') {
                                removeRow(row);
                                syncDropdownNotificationState(row.dataset.notificationId);
                                return;
                            }

                            markRowAsRead(row);
                            syncDropdownNotificationState(row.dataset.notificationId);
                        });

                        syncUnreadCount(nextUnreadCount);
                        syncReadCount(nextReadCount);
                        await refreshCurrentPageFromServer();
                    } catch (error) {}

                    return;
                }

                if (clearReadForm) {
                    try {
                        const payload = await requestJson(clearReadForm);
                        const deletedCount = Number(payload.deletedCount ?? 0);
                        const nextUnreadCount = Number(payload.unreadCount ?? unreadValue.textContent ?? 0);
                        const nextReadCount = Number(payload.readCount ?? 0);
                        const readRows = [...notificationList.querySelectorAll('[data-notification-row][data-notification-read="1"]')];

                        readRows.forEach((row) => {
                            removeDropdownNotification(row.dataset.notificationId);
                            removeRow(row);
                        });

                        decrementTotalCount(Math.min(deletedCount, readRows.length || deletedCount));
                        syncUnreadCount(nextUnreadCount);
                        syncReadCount(nextReadCount);
                        await refreshCurrentPageFromServer();
                    } catch (error) {}

                    return;
                }

                if (deleteForm) {
                    const row = deleteForm.closest('[data-notification-row]');

                    if (!row) {
                        return;
                    }

                    try {
                        const payload = await requestJson(deleteForm);
                        const nextUnreadCount = Number(payload.unreadCount ?? unreadValue.textContent ?? 0);
                        const nextReadCount = Number(payload.readCount ?? adminReadCount);

                        syncUnreadCount(nextUnreadCount);
                        syncReadCount(nextReadCount);
                        removeDropdownNotification(row.dataset.notificationId);
                        removeRow(row);
                        decrementTotalCount(1);
                        await refreshCurrentPageFromServer();
                    } catch (error) {}
                }
            });

            document.addEventListener('admin:notification-received', (event) => {
                const notification = event.detail;

                if (!notification?.id) {
                    return;
                }

                const alreadyRendered = notificationList.querySelector(`[data-notification-id="${notification.id}"]`);
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

                const row = createNotificationRow(notification);
                notificationList.prepend(row);

                const rows = [...notificationList.querySelectorAll('[data-notification-row]')];
                rows.slice(perPage).forEach((item) => item.remove());
            });

            updateBulkActionState();
        });
    </script>
@endpush
