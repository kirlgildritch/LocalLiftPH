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

                    <div class="notification-row {{ $notification->read_at ? '' : 'unread' }}"
                        data-notification-row
                        data-notification-id="{{ $notification->id }}"
                        data-notification-type="{{ $type }}"
                        data-notification-read="{{ $notification->read_at ? '1' : '0' }}">
                        <a href="{{ route('admin.notifications.open', $notification) }}" class="notification-row__icon">
                            <i class="fa-solid {{ $icon }}"></i>
                        </a>

                        <a href="{{ route('admin.notifications.open', $notification) }}" class="notification-row__content">
                            <h3>{{ $title }}</h3>
                            <p>{{ $message }}</p>
                            <small>{{ $notification->created_at?->format('M d, Y h:i A') }}</small>
                        </a>

                        <div class="notification-row__right">
                            <span class="notification-status {{ $notification->read_at ? '' : 'unread' }}"
                                data-notification-status>
                                {{ $notification->read_at ? 'Read' : 'Unread' }}
                            </span>

                            @if (!$notification->read_at)
                                <form method="POST" action="{{ route('admin.notifications.read', $notification) }}"
                                    data-notification-mark-read-form>
                                    @csrf
                                    @method('PATCH')
                                    <button class="notification-icon-button" type="submit" title="Mark as read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.notifications.destroy', $notification) }}"
                                data-notification-delete-form>
                                @csrf
                                @method('DELETE')
                                <button class="notification-icon-button danger" type="submit" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
