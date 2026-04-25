<div class="inbox-layout">
    <aside class="inbox-sidebar">
        <div class="inbox-search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Use the list below to open a conversation" value="" readonly>
        </div>

        <div class="inbox-conversation-list">
            @forelse($conversations as $conversation)
                <a href="{{ $conversation['show_url'] }}" class="inbox-conversation-item {{ !empty($conversation['active']) ? 'is-active' : '' }}">
                    <span class="inbox-conversation-avatar">
                        @if(!empty($conversation['avatar_url']))
                            <img src="{{ $conversation['avatar_url'] }}" alt="{{ $conversation['name'] }}">
                        @else
                            {{ $conversation['avatar_initials'] }}
                        @endif
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
                <div class="inbox-empty-state">
                    <h3>No conversations yet</h3>
                    <p>Start a conversation from a product or shop page.</p>
                </div>
            @endforelse
        </div>
    </aside>

    <section class="inbox-thread {{ $activeConversation ? '' : 'is-empty-thread' }}">
        @if($activeConversation)
            <div class="inbox-thread-header">
                <span class="inbox-thread-avatar">
                    @if(!empty($activeConversation['avatar_url']))
                        <img src="{{ $activeConversation['avatar_url'] }}" alt="{{ $activeConversation['name'] }}">
                    @else
                        {{ $activeConversation['avatar_initials'] }}
                    @endif
                </span>

                <div class="inbox-thread-heading">
                    <h3>{{ $activeConversation['name'] }}</h3>
                    <span>{{ $activeConversation['role_label'] }}</span>
                </div>
            </div>

            <div class="inbox-thread-messages" data-inbox-messages>
                @forelse($activeConversation['messages'] as $message)
                    <div class="inbox-message-row {{ !empty($message['is_current_user']) ? 'is-current-user' : '' }}">
                        <div class="inbox-message-bubble">
                            <strong>{{ $message['sender_label'] }}</strong>
                            @if(!empty($message['has_text']))
                                <p>{{ $message['message'] }}</p>
                            @endif
                            @if(!empty($message['has_image']))
                                <img src="{{ $message['image_url'] }}" alt="Shared image" class="inbox-message-image">
                            @endif
                        </div>
                        <span class="inbox-message-meta">
                            {{ $message['time'] }}
                            @if(!empty($message['status_label']))
                                <em>{{ $message['status_label'] }}</em>
                            @endif
                        </span>
                    </div>
                @empty
                    <div class="inbox-empty-state">
                        <h3>No messages yet</h3>
                        <p>Send the first message in this conversation.</p>
                    </div>
                @endforelse
            </div>

            <form action="{{ $activeConversation['send_url'] }}" method="POST" enctype="multipart/form-data" class="inbox-reply-form">
                @csrf
                <input type="text" name="message" placeholder="Type a message..." value="{{ old('message') }}">
                <input type="file" name="image" accept="image/*">
                <button type="submit" class="page-action-btn">Send</button>
            </form>
        @else
            <div class="inbox-thread-header">
                <div class="inbox-thread-heading">
                    <h3>No active chat</h3>
                    <span>Choose a conversation to continue.</span>
                </div>
            </div>

            <div class="inbox-thread-messages is-empty-pane">
                <div class="inbox-empty-state">
                    <h3>No active chat</h3>
                    <p>Select a conversation from the left to continue.</p>
                </div>
            </div>
        @endif
    </section>
</div>
