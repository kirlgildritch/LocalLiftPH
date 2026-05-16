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
