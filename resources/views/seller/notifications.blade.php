@extends('layouts.seller')

@section('title', 'Seller Notifications')

@push('styles')
    <style>
        .seller-notifications-page {
            display: grid;
            gap: 18px;
            color: #f5f9ff;
        }

        .seller-notifications-toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .seller-notifications-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 999px;
            padding: 10px 14px;
            color: #b8c8e0;
            background: rgba(255, 255, 255, 0.04);
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
            transition: 0.18s ease;
        }

        .seller-notifications-chip:hover {
            color: #f5f9ff;
            background: rgba(66, 165, 245, 0.12);
            transform: translateY(-1px);
        }

        .seller-notifications-chip.is-active {
            background: linear-gradient(180deg, rgba(66, 165, 245, 0.28), rgba(66, 165, 245, 0.18));
            border-color: rgba(66, 165, 245, 0.45);
            color: #ffffff;
        }

        .seller-notification-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .seller-notification-summary__card,
        .seller-notification-panel {
            background: linear-gradient(180deg, rgba(10, 19, 34, 0.96), rgba(7, 14, 24, 0.92));
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 18px;
            box-shadow: 0 20px 44px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(16px);
        }

        .seller-notification-summary__card {
            padding: 20px;
        }

        .seller-notification-summary__label {
            margin: 0 0 8px;
            color: #8fa7c4;
            font-weight: 700;
            font-size: 14px;
        }

        .seller-notification-summary__value {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            color: #42a5f5;
        }

        .seller-notification-panel__header {
            padding: 20px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.12);
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .seller-notification-panel__title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #f5f9ff;
        }

        .seller-notification-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .seller-notification-btn {
            border: 1px solid rgba(187, 222, 251, 0.14);
            background: rgba(255, 255, 255, 0.04);
            color: #e8f2ff;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .seller-notification-btn.primary {
            background: linear-gradient(180deg, #4f7ff2, #3f6fd9);
            border-color: #4f7ff2;
            color: #fff;
        }

        .seller-notification-btn.danger {
            color: #ff8b8b;
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.22);
        }

        .seller-notification-list {
            display: grid;
        }

        .seller-notification-row {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr) auto;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.1);
            align-items: center;
            color: inherit;
        }

        .seller-notification-row.unread {
            background: rgba(66, 165, 245, 0.08);
        }

        .seller-notification-row__icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: rgba(66, 165, 245, 0.14);
            color: #42a5f5;
            display: grid;
            place-items: center;
            font-size: 18px;
            text-decoration: none;
        }

        .seller-notification-row__content {
            min-width: 0;
            text-decoration: none;
            color: inherit;
        }

        .seller-notification-row__content h3 {
            margin: 0 0 5px;
            color: #f5f9ff;
            font-size: 16px;
            font-weight: 800;
        }

        .seller-notification-row__content p {
            margin: 0 0 6px;
            color: #8fa7c4;
            line-height: 1.45;
        }

        .seller-notification-row__content small {
            color: #7d8aa4;
            font-weight: 600;
        }

        .seller-notification-row__header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: start;
        }

        .seller-notification-tag {
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(66, 165, 245, 0.12);
            color: #bbdefb;
            white-space: nowrap;
        }

        .seller-notification-row__actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .seller-notification-status {
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(35, 146, 70, 0.18);
            color: #7fe1a6;
        }

        .seller-notification-status.unread {
            background: rgba(173, 116, 0, 0.18);
            color: #ffd27a;
        }

        .seller-notification-icon-button {
            border: 1px solid rgba(187, 222, 251, 0.14);
            background: rgba(255, 255, 255, 0.04);
            width: 38px;
            height: 38px;
            border-radius: 12px;
            color: #d9e7fb;
            cursor: pointer;
        }

        .seller-notification-icon-button.danger {
            color: #ff8b8b;
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.22);
        }

        .seller-notification-empty {
            padding: 50px 20px;
            text-align: center;
            color: #8fa7c4;
        }

        .seller-notification-empty i {
            font-size: 36px;
            color: #8fa7c4;
            margin-bottom: 12px;
        }

        .seller-notification-pagination {
            padding: 18px 20px;
        }

        .seller-notification-pagination :is(nav, ul, ol) {
            margin: 0;
        }

        .seller-notification-pagination a,
        .seller-notification-pagination span {
            color: #e8f2ff;
        }

        .seller-notification-pagination svg {
            fill: currentColor;
        }

        @media (max-width: 980px) {
            .seller-notification-summary {
                grid-template-columns: 1fr;
            }

            .seller-notification-row {
                grid-template-columns: 42px minmax(0, 1fr);
            }

            .seller-notification-row__actions {
                grid-column: 1 / -1;
                justify-content: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $filter = $filter ?? 'all';
        $filterLabels = [
            'all' => 'All',
            'unread' => 'Unread',
            'orders' => 'Orders',
            'messages' => 'Messages',
            'reviews' => 'Reviews',
            'admin' => 'Admin',
        ];
    @endphp

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <div class="seller-notifications-page" data-seller-notifications-page
                        data-current-filter="{{ $filter }}"
                        data-current-page="{{ $notifications->currentPage() }}"
                        data-per-page="{{ $notifications->perPage() }}">
                        <div class="seller-notifications-toolbar">
                            @foreach ($filterLabels as $key => $label)
                                <a href="{{ route('seller.notifications.index', array_filter(['filter' => $key])) }}"
                                    class="seller-notifications-chip {{ $filter === $key ? 'is-active' : '' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>

                        <div class="seller-notification-summary">
                            <div class="seller-notification-summary__card">
                                <p class="seller-notification-summary__label">Total Notifications</p>
                                <p class="seller-notification-summary__value" data-seller-notification-total>
                                    {{ $notifications->total() }}
                                </p>
                            </div>

                            <div class="seller-notification-summary__card">
                                <p class="seller-notification-summary__label">Unread</p>
                                <p class="seller-notification-summary__value" data-seller-notification-unread>
                                    {{ $unreadCount }}
                                </p>
                            </div>

                            <div class="seller-notification-summary__card">
                                <p class="seller-notification-summary__label">Showing</p>
                                <p class="seller-notification-summary__value" data-seller-notification-showing>
                                    {{ $notifications->count() }}
                                </p>
                            </div>
                        </div>

                        <section class="seller-notification-panel panel">
                            <div class="seller-notification-panel__header">
                                <h3 class="seller-notification-panel__title">Notifications</h3>

                                <div class="seller-notification-actions">
                                    <form method="POST" action="{{ route('seller.notifications.read-all') }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="seller-notification-btn primary" type="submit" @disabled($unreadCount === 0)>
                                            <i class="fa-solid fa-check-double"></i>
                                            Mark all as read
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('seller.notifications.clear-read') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="seller-notification-btn danger" type="submit" @disabled($readCount === 0)>
                                            <i class="fa-solid fa-trash"></i>
                                            Clear read
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="seller-notification-list" data-seller-notification-list>
                                @forelse ($notifications as $notification)
                                    @php
                                        $data = $notification->data ?? [];
                                        $type = $data['type'] ?? $data['category'] ?? 'admin';
                                        $action = $data['action'] ?? 'notification';
                                        $title = $data['title'] ?? 'Notification';
                                        $message = $data['message'] ?? 'You have a new notification.';
                                        $tag = $filterLabels[$type] ?? ucfirst($type);
                                        $icon = match ($action) {
                                            'new_order', 'order_completed', 'order_cancelled', 'buyer_confirmed_receipt', 'pending_order_not_shipped' => 'fa-bag-shopping',
                                            'buyer_message' => 'fa-envelope',
                                            'buyer_review' => 'fa-star',
                                            'product_low_stock', 'product_out_of_stock', 'product_edited' => 'fa-box',
                                            default => 'fa-bell',
                                        };
                                    @endphp

                                    <article class="seller-notification-row {{ $notification->read_at ? '' : 'unread' }}"
                                        data-seller-notification-row
                                        data-seller-notification-id="{{ $notification->id }}"
                                        data-seller-notification-read="{{ $notification->read_at ? '1' : '0' }}">
                                        <a href="{{ route('seller.notifications.open', $notification) }}"
                                            class="seller-notification-row__icon"
                                            @if($action === 'buyer_message' && !empty($data['related_id']))
                                                data-chat-notification-link
                                                data-chat-conversation-id="{{ (int) $data['related_id'] }}"
                                            @endif>
                                            <i class="fa-solid {{ $icon }}"></i>
                                        </a>

                                        <a href="{{ route('seller.notifications.open', $notification) }}"
                                            class="seller-notification-row__content"
                                            @if($action === 'buyer_message' && !empty($data['related_id']))
                                                data-chat-notification-link
                                                data-chat-conversation-id="{{ (int) $data['related_id'] }}"
                                            @endif>
                                            <div class="seller-notification-row__header">
                                                <h3>{{ $title }}</h3>
                                                <span class="seller-notification-tag">{{ $tag }}</span>
                                            </div>
                                            <p>{{ $message }}</p>
                                            <small>{{ $notification->created_at?->format('M d, Y h:i A') }}</small>
                                        </a>

                                        <div class="seller-notification-row__actions">
                                            <span class="seller-notification-status {{ $notification->read_at ? '' : 'unread' }}"
                                                data-seller-notification-status>
                                                {{ $notification->read_at ? 'Read' : 'Unread' }}
                                            </span>

                                            @if (! $notification->read_at)
                                                <form method="POST" action="{{ route('seller.notifications.read', $notification) }}"
                                                    data-seller-notification-read-form>
                                                    @csrf
                                                    @method('PATCH')
                                                    <button class="seller-notification-icon-button" type="submit"
                                                        title="Mark as read">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('seller.notifications.destroy', $notification) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="seller-notification-icon-button danger" type="submit"
                                                    title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </article>
                                @empty
                                    <div class="seller-notification-empty" data-seller-notification-empty>
                                        <i class="fa-regular fa-bell-slash"></i>
                                        <p>No notifications found.</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="seller-notification-pagination">
                                @if ($notifications->hasPages())
                                    {{ $notifications->links() }}
                                @endif
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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

            const matchesFilter = (notification) => {
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

            const formatCount = (value) => String(Math.max(0, Number(value) || 0));
            const notificationConversationId = (notification) => Number(
                notification?.related_id
                || notification?.route_params?.conversation
                || 0
            );
            const isMessageNotification = (notification) => (
                String(notification?.action || '').toLowerCase() === 'buyer_message'
                && notificationConversationId(notification) > 0
            );

            const escapeHtml = (value) => String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const iconClassFor = (notification) => {
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

            const tagFor = (notification) => {
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

            const buildReadForm = (notificationId) => {
                if (!csrfToken) {
                    return '';
                }

                return `
                    <form method="POST" action="/seller-notifications/${notificationId}/read" data-seller-notification-read-form>
                        <input type="hidden" name="_token" value="${escapeHtml(csrfToken)}">
                        <input type="hidden" name="_method" value="PATCH">
                        <button class="seller-notification-icon-button" type="submit" title="Mark as read">
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </form>
                `;
            };

            const markNotificationAsRead = async (form) => {
                const notificationId = form.closest('[data-seller-notification-id]')?.dataset.sellerNotificationId;

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
                document.dispatchEvent(new CustomEvent('seller:notification-read', {
                    detail: payload.notification || { id: notificationId },
                }));
            };

            const buildRowHtml = (notification) => {
                const isUnread = !notification.read_at;
                const iconClass = iconClassFor(notification);
                const tag = tagFor(notification);
                const title = escapeHtml(notification.title || 'Notification');
                const message = escapeHtml(notification.message || 'You have a new notification.');
                const time = escapeHtml(notification.created_at_formatted || notification.created_at_human || 'Just now');
                const openUrl = `/seller-notifications/${notification.id}/open`;
                const deleteUrl = `/seller-notifications/${notification.id}`;
                const readForm = isUnread ? buildReadForm(notification.id) : '';
                const conversationId = notificationConversationId(notification);
                const chatAttributes = isMessageNotification(notification)
                    ? ` data-chat-notification-link data-chat-conversation-id="${escapeHtml(conversationId)}"`
                    : '';

                return `
                    <article class="seller-notification-row ${isUnread ? 'unread' : ''}"
                        data-seller-notification-row
                        data-seller-notification-id="${escapeHtml(notification.id)}"
                        data-seller-notification-read="${isUnread ? '0' : '1'}">
                        <a href="${openUrl}" class="seller-notification-row__icon"${chatAttributes}>
                            <i class="fa-solid ${iconClass}"></i>
                        </a>

                        <a href="${openUrl}" class="seller-notification-row__content"${chatAttributes}>
                            <div class="seller-notification-row__header">
                                <h3>${title}</h3>
                                <span class="seller-notification-tag">${escapeHtml(tag)}</span>
                            </div>
                            <p>${message}</p>
                            <small>${time}</small>
                        </a>

                        <div class="seller-notification-row__actions">
                            <span class="seller-notification-status ${isUnread ? 'unread' : ''}" data-seller-notification-status>
                                ${isUnread ? 'Unread' : 'Read'}
                            </span>
                            ${readForm}
                            <form method="POST" action="${deleteUrl}">
                                <input type="hidden" name="_token" value="${escapeHtml(csrfToken || '')}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="seller-notification-icon-button danger" type="submit" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </article>
                `;
            };

            const updateCounts = (isUnread) => {
                totalValue.textContent = formatCount(Number(totalValue.textContent || 0) + 1);
                if (isUnread) {
                    unreadValue.textContent = formatCount(Number(unreadValue.textContent || 0) + 1);
                }

                if (currentPage === 1) {
                    showingValue.textContent = formatCount(Math.min(perPage, Number(showingValue.textContent || 0) + 1));
                }
            };

            const prependRow = (notification) => {
                if (!notification?.id || currentPage !== 1 || !matchesFilter(notification)) {
                    return;
                }

                const existing = list.querySelector(`[data-seller-notification-id="${notification.id}"]`);
                if (existing) {
                    existing.remove();
                }

                const empty = list.querySelector('[data-seller-notification-empty]');
                empty?.remove();

                list.insertAdjacentHTML('afterbegin', buildRowHtml(notification));

                [...list.querySelectorAll('[data-seller-notification-row]')]
                    .slice(perPage)
                    .forEach((item) => item.remove());
            };

            document.addEventListener('seller:notification-received', (event) => {
                const notification = event.detail;

                if (!notification?.id || currentPage !== 1 || !matchesFilter(notification)) {
                    return;
                }

                updateCounts(!notification.read_at);
                prependRow(notification);
            });

            document.addEventListener('seller:notification-read', (event) => {
                const notificationId = event.detail?.id;
                unreadValue.textContent = formatCount(Number(unreadValue.textContent || 0) - 1);

                if (!notificationId) {
                    return;
                }

                const row = list.querySelector(`[data-seller-notification-id="${notificationId}"]`);
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
                row.querySelector('[data-seller-notification-status]')?.classList.remove('unread');
                const status = row.querySelector('[data-seller-notification-status]');
                if (status) {
                    status.textContent = 'Read';
                }
                row.querySelector('[data-seller-notification-read-form]')?.remove();
            });

            document.addEventListener('submit', function (event) {
                const form = event.target.closest('[data-seller-notification-read-form]');

                if (!form || !list.contains(form)) {
                    return;
                }

                event.preventDefault();

                markNotificationAsRead(form).catch((error) => {
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

                const openChat = () => {
                    document.dispatchEvent(new CustomEvent('chat-widget:open-conversation', {
                        detail: {
                            conversationId,
                        },
                    }));
                };

                if (!readForm) {
                    openChat();
                    return;
                }

                markNotificationAsRead(readForm)
                    .catch((error) => {
                        console.error(error);
                    })
                    .finally(openChat);
            });
        });
    </script>
@endpush
