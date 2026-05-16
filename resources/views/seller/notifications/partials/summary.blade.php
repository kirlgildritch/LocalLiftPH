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
