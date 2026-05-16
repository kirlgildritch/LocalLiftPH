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
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/js/app.js'])
    @endif
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
        $adminLayoutScript = asset('assets/js/admin-layout.js') . '?v=' . @filemtime(public_path('assets/js/admin-layout.js'));

        if ($adminUser && method_exists($adminUser, 'notifications')) {
            $adminNotifications = $adminUser->notifications()->latest()->limit(5)->get();
            $adminUnreadCount = $adminUser->unreadNotifications()->count();
        }
    @endphp

    <div class="admin-shell"
        data-admin-notification-user-id="{{ $adminUser?->id ?? '' }}"
        data-admin-notification-feed-url="{{ route('admin.notifications.feed') }}"
        data-admin-unread-count="{{ (int) $adminUnreadCount }}"
        data-admin-notification-base-url="{{ url('/admin/notifications') }}">
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

                <a class="sidebar__link {{ str_starts_with($currentRoute ?? '', 'admin.payouts') ? 'is-active' : '' }}"
                    href="{{ route('admin.payouts') }}">
                    <i class="fa-solid fa-wallet"></i>
                    <span>Payouts</span>
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

    <script src="{{ $adminLayoutScript }}" defer></script>
</body>

</html>
