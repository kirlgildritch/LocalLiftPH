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
    <link rel="icon" href="{{ asset('assets/image/Logo.png') }}">
    @stack('styles')
</head>

<body>
    @php($currentRoute = request()->route()?->getName())

    <div class="admin-shell">
        <aside class="sidebar">
            <div class="sidebar__brand">
                <div class="sidebar__logo"><img src="{{ asset('assets/image/Logo.png') }}" alt="Logo"></div>
                <div>
                    <p class="sidebar__eyebrow">Marketplace</p>
                    <h1>Admin Dashboard</h1>
                </div>
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
                    <div class="topbar__note">@yield('page-description', 'Clean, consistent admin management screens.')
                    </div>
                    <div class="user-chip">
                        <span class="user-chip__avatar">AD</span>
                        <span>{{ auth()->user()?->name ?? 'Admin User' }}</span>
                    </div>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="topbar__logout" aria-label="Log out">
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </header>

            <main class="page">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('modals')
    @stack('scripts')
    <script>
        const toggleButton = document.querySelector('[data-sidebar-toggle]');
        const shell = document.querySelector('.admin-shell');

        if (toggleButton && shell) {
            toggleButton.addEventListener('click', () => {
                shell.classList.toggle('sidebar-open');
            });
        }
    </script>
</body>

</html>