<div class="messages-layout" data-skeleton-group data-skeleton-delay="360">
    @php($isSellerInbox = auth('seller')->check())
    <div class="chat-list-panel panel">
        @forelse($conversations as $conversation)
            @php($otherParticipant = $conversation->otherParticipant(auth()->user()))
            <a href="{{ $isSellerInbox ? route('seller.messages.show', $conversation) : route('messages.show', $conversation) }}" class="chat-item skeleton-shell is-loading {{ optional($activeConversation)->id === $conversation->id ? 'active' : '' }}" data-skeleton-item>
                @if(!empty($otherParticipant?->profile_image))
                    <img src="{{ asset('storage/' . $otherParticipant->profile_image) }}" alt="{{ $otherParticipant->name }}" class="skeleton skeleton-avatar">
                @else
                    <span class="chat-avatar-fallback skeleton skeleton-avatar">
                        {{ strtoupper(substr($otherParticipant->name ?? 'LL', 0, 2)) }}
                    </span>
                @endif

                <div class="chat-info">
                    <h4 class="skeleton skeleton-text">{{ $otherParticipant->name ?? 'Conversation' }}</h4>
                    <p class="skeleton skeleton-text">{{ \Illuminate\Support\Str::limit($conversation->latestMessage->message ?? 'Start chatting with this seller.', 40) }}</p>
                    <span class="skeleton skeleton-text">{{ optional($conversation->latestMessage?->created_at)->diffForHumans() ?? 'No messages yet' }}</span>
                </div> 
            </a>
        @empty
            <div class="chat-empty-state">
                <h3>No conversations yet</h3>
                <p>Start a conversation from a product or shop page.</p>
            </div>
        @endforelse
    </div>

    <div class="chat-window panel">
        @if($activeConversation)
            @php($otherParticipant = $activeConversation->otherParticipant(auth()->user()))
            <div class="chat-window-header">
                <div class="chat-window-heading">
                    <h3>{{ $otherParticipant->name ?? 'Conversation' }}</h3>
                    <span>{{ $isSellerInbox ? 'Buyer conversation' : 'Seller conversation' }}</span>
                </div>
            </div>

            <div class="chat-body">
                @forelse($activeConversation->messages as $message)
                    <div class="message-row skeleton-shell is-loading {{ (int) $message->sender_id === (int) auth()->id() ? 'right' : 'left' }}" data-skeleton-item>
                        <div class="message-bubble skeleton skeleton-text">
                            <strong class="skeleton skeleton-text">{{ (int) $message->sender_id === (int) auth()->id() ? 'You' : ($message->sender->name ?? 'User') }}</strong>
                            <p class="skeleton skeleton-text">{{ $message->message }}</p>
                        </div>
                        <span class="message-time skeleton skeleton-text">{{ $message->created_at->format('M d, h:i A') }}</span>
                    </div>
                @empty
                    <div class="chat-empty-state chat-body-empty">
                        <h3>No messages yet</h3>
                        <p>Send the first message to start this conversation.</p>
                    </div>
                @endforelse
            </div>

            <form class="chat-input-area" action="{{ $isSellerInbox ? route('seller.messages.store', $activeConversation) : route('messages.store', $activeConversation) }}" method="POST">
                @csrf
                <input type="text" name="message" placeholder="Type your message here..." value="{{ old('message') }}">
                <button class="send-btn" type="submit">Send</button>
            </form>
        @else
            <div class="chat-empty-state full-empty-state">
                <h3>No active chat</h3>
                <p>Choose a conversation from the left or start one from a product page.</p>
            </div>
        @endif
    </div>
</div>
