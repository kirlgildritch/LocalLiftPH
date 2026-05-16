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
                <button class="seller-notification-icon-button" type="submit" title="Mark as read">
                    <i class="fa-solid fa-check"></i>
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('seller.notifications.destroy', $notification) }}">
            @csrf
            @method('DELETE')
            <button class="seller-notification-icon-button danger" type="submit" title="Delete">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </div>
</article>
