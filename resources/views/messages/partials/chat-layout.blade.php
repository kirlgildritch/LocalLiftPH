<div class="messages-layout">
    <div class="chat-list-panel panel">
        @forelse($conversations as $conversation)
            @php($otherParticipant = $conversation->otherParticipant(auth()->user()))
            <a href="{{ route('messages.show', $conversation) }}" class="chat-item {{ optional($activeConversation)->id === $conversation->id ? 'active' : '' }}">
                @if(!empty($otherParticipant?->profile_image))
                    <img src="{{ asset('storage/' . $otherParticipant->profile_image) }}" alt="{{ $otherParticipant->name }}">
                @else
                    <span class="chat-avatar-fallback">
                        {{ strtoupper(substr($otherParticipant->name ?? 'LL', 0, 2)) }}
                    </span>
                @endif

                <div class="chat-info">
                    <h4>{{ $otherParticipant->name ?? 'Conversation' }}</h4>
                    <p>{{ \Illuminate\Support\Str::limit($conversation->latestMessage->message ?? 'Start chatting with this seller.', 40) }}</p>
                    <span>{{ optional($conversation->latestMessage?->created_at)->diffForHumans() ?? 'No messages yet' }}</span>
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
                    <span>{{ auth()->user()->isSeller() ? 'Buyer conversation' : 'Seller conversation' }}</span>
                </div>
            </div>

            <div class="chat-body">
                @forelse($activeConversation->messages as $message)
                    <div class="message-row {{ (int) $message->sender_id === (int) auth()->id() ? 'right' : 'left' }}">
                        <div class="message-bubble">
                            <strong>{{ (int) $message->sender_id === (int) auth()->id() ? 'You' : ($message->sender->name ?? 'User') }}</strong>
                            <p>{{ $message->message }}</p>
                        </div>
                        <span class="message-time">{{ $message->created_at->format('M d, h:i A') }}</span>
                    </div>
                @empty
                    <div class="chat-empty-state chat-body-empty">
                        <h3>No messages yet</h3>
                        <p>Send the first message to start this conversation.</p>
                    </div>
                @endforelse
            </div>

            <form class="chat-input-area" action="{{ route('messages.store', $activeConversation) }}" method="POST">
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
