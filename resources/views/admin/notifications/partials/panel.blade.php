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
            @include('admin.notifications.partials.row', ['notification' => $notification])
        @empty
            @include('admin.notifications.partials.empty')
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
