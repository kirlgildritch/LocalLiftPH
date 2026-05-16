<section class="inbox-thread {{ $activeConversation ? '' : 'is-empty-thread' }}" data-inbox-thread>
    @if($activeConversation)
        @include('messages.partials.inbox.thread-active', [
            'activeConversation' => $activeConversation,
        ])
    @else
        @include('messages.partials.inbox.thread-empty')
    @endif
</section>
