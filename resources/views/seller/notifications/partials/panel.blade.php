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
            @include('seller.notifications.partials.row', ['notification' => $notification])
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
