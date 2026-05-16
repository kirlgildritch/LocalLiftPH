<aside class="inbox-sidebar">
    <div class="inbox-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" placeholder="Use the list below to open a conversation" value="" readonly>
    </div>

    <div class="inbox-conversation-list" data-inbox-conversation-list>
        @forelse($conversations as $conversation)
            <a href="{{ $conversation['show_url'] }}" class="inbox-conversation-item {{ !empty($conversation['active']) ? 'is-active' : '' }}">
                <span class="inbox-conversation-avatar-wrap">
                    <span class="inbox-conversation-avatar">
                        @if(!empty($conversation['avatar_url']))
                            <img src="{{ $conversation['avatar_url'] }}" alt="{{ $conversation['name'] }}">
                        @else
                            {{ $conversation['avatar_initials'] }}
                        @endif
                    </span>
                    <span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot
                        data-conversation-id="{{ $conversation['id'] }}"></span>
                </span>

                <span class="inbox-conversation-copy">
                    <span class="inbox-conversation-topline">
                        <strong>{{ $conversation['name'] }}</strong>
                        @if(($conversation['unread_count'] ?? 0) > 0)
                            <span class="inbox-unread-badge">{{ $conversation['unread_count'] }}</span>
                        @endif
                    </span>

                    <p>{{ $conversation['preview'] }}</p>
                    <small>{{ $conversation['updated_at'] }}</small>
                </span>
            </a>
        @empty
            @include('messages.partials.inbox.empty-state', [
                'title' => 'No conversations yet',
                'message' => 'Start a conversation from a product or shop page.',
            ])
        @endforelse
    </div>
</aside>
