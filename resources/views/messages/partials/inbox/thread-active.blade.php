<div class="inbox-thread-header">
    <button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations">
        <i class="fa-solid fa-arrow-left"></i>
    </button>

    <span class="inbox-thread-avatar-wrap">
        <span class="inbox-thread-avatar">
            @if(!empty($activeConversation['avatar_url']))
                <img src="{{ $activeConversation['avatar_url'] }}" alt="{{ $activeConversation['name'] }}">
            @else
                {{ $activeConversation['avatar_initials'] }}
            @endif
        </span>
        <span class="inbox-presence-dot inbox-presence-dot--avatar" data-presence-dot
            data-conversation-id="{{ $activeConversation['id'] }}"></span>
    </span>

    <div class="inbox-thread-heading">
        <h3>{{ $activeConversation['name'] }}</h3>
        <span class="inbox-thread-status" data-presence-label
            data-conversation-id="{{ $activeConversation['id'] }}"
            data-base-label="{{ $activeConversation['role_label'] }}">{{ $activeConversation['role_label'] }}</span>
    </div>
</div>

<div class="inbox-thread-messages" data-inbox-messages>
    @forelse($activeConversation['messages'] as $message)
        @include('messages.partials.inbox.message-row', [
            'message' => $message,
        ])
    @empty
        @include('messages.partials.inbox.empty-state', [
            'title' => 'No messages yet',
            'message' => 'Send the first message in this conversation.',
        ])
    @endforelse
</div>

@include('messages.partials.inbox.composer', [
    'activeConversation' => $activeConversation,
])
