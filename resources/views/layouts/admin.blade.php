<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/admin.css') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/image/favicon.png') }}">
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

        if (! $adminToast && $errors->any()) {
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
                    <div class="topbar__note">
                    </div>
                    <div class="user-chip">
                        <span class="user-chip__avatar">AD</span>
                        <span>{{ auth()->user()?->name ?? 'Admin User' }}</span>
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
        <div
            id="admin-toast"
            class="toast-message toast-message--{{ $adminToast['type'] }}"
            role="status"
            aria-live="polite"
        >
            <i class="fa-solid {{ $adminToastIcon }}"></i>
            <span>{{ $adminToast['message'] }}</span>
        </div>
    @endif

    <script>
        const toggleButton = document.querySelector('[data-sidebar-toggle]');
        const closeButton = document.querySelector('[data-sidebar-close]');
        const shell = document.querySelector('.admin-shell');
        const sidebar = document.querySelector('.sidebar');

        const isMobileAdminViewport = () => window.matchMedia('(max-width: 980px)').matches;
        const closeSidebar = () => shell?.classList.remove('sidebar-open');

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

        window.addEventListener('resize', () => {
            if (!isMobileAdminViewport()) {
                closeSidebar();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('admin-toast');

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
