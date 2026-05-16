@php
    $sellerHeaderNotifications = $sellerHeaderNotifications ?? collect();
    $sellerUnreadNotificationCount = (int) ($sellerUnreadNotificationCount ?? 0);
    $sellerHeaderScript = asset('assets/js/seller-header.js') . '?v=' . @filemtime(public_path('assets/js/seller-header.js'));
@endphp

<header
    class="seller-header-shell"
    data-seller-header-shell
    data-seller-suggestions-url="{{ route('seller.search.suggestions') }}"
    data-seller-notification-user-id="{{ (int) auth('seller')->id() }}"
    data-seller-notification-feed-url="{{ route('seller.notifications.feed') }}"
    data-seller-notification-base-url="{{ url('/seller-notifications') }}"
    data-seller-unread-count="{{ $sellerUnreadNotificationCount }}">
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

                <input type="text" id="sellerSearchInput" name="q" value="{{ request('q') }}"
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
                        <span class="notif-badge {{ $sellerUnreadNotificationCount > 0 ? '' : 'is-hidden' }}"
                            data-seller-notification-badge>
                            {{ $sellerUnreadNotificationCount > 99 ? '99+' : $sellerUnreadNotificationCount }}
                        </span>
                    </button>

                    <div class="notification-menu" id="notificationMenu" data-seller-notification-menu>
                        <div class="notification-header">
                            <div>
                                <h4>Notifications</h4>
                                <p class="notification-header__meta" data-seller-notification-meta>
                                    {{ $sellerUnreadNotificationCount > 0
                                        ? $sellerUnreadNotificationCount . ' unread notification' . ($sellerUnreadNotificationCount === 1 ? '' : 's')
                                        : 'You are all caught up.' }}
                                </p>
                            </div>
                        </div>

                        @forelse ($sellerHeaderNotifications as $notification)
                            @php
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
                            @endphp

                            <div class="notification-item {{ $notification->read_at ? '' : 'unread' }}"
                                data-seller-notification-item
                                data-seller-notification-id="{{ $notification->id }}"
                                data-seller-notification-read="{{ $notification->read_at ? '1' : '0' }}">
                                <div class="notif-icon"><i class="fa-solid {{ $icon }}"></i></div>
                                <a href="{{ route('seller.notifications.open', $notification) }}" class="notif-content"
                                    @if($action === 'buyer_message' && !empty($data['related_id']))
                                        data-chat-notification-link
                                        data-chat-conversation-id="{{ (int) $data['related_id'] }}"
                                    @endif>
                                    <p><strong>{{ $title }}</strong></p>
                                    <span>{{ $message }}</span>
                                    <small>{{ $notification->created_at?->diffForHumans() ?? 'Just now' }}</small>
                                </a>

                                @if (! $notification->read_at)
                                    <form method="POST" action="{{ route('seller.notifications.read', $notification) }}"
                                        class="notification-read-form" data-seller-notification-read-form>
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="notification-read-btn" title="Mark as read"
                                            aria-label="Mark as read">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <div class="notification-item notification-item--empty" data-seller-notification-empty>
                                <div class="notif-icon"><i class="fa-regular fa-bell-slash"></i></div>
                                <div class="notif-content">
                                    <p><strong>No notifications</strong></p>
                                    <span>You're all caught up.</span>
                                </div>
                            </div>
                        @endforelse

                        <div class="notification-footer">
                            <a href="{{ route('seller.notifications.index') }}">View all notifications</a>
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

<script src="{{ $sellerHeaderScript }}" defer></script>
