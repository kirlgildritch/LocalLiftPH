<div class="inbox-layout" data-chat-page data-fetch-url="{{ $chatData['meta']['widget_route'] ?? '' }}"
    data-list-url="{{ $isSellerInbox ? route('seller.messages') : route('messages.index') }}"
    data-mobile-view="{{ request()->route('conversation') ? 'thread' : 'list' }}">
    <script type="application/json" data-chat-page-state>@json($chatData)</script>

    @include('messages.partials.inbox.conversation-list', [
        'conversations' => $conversations,
    ])

    @include('messages.partials.inbox.thread', [
        'activeConversation' => $activeConversation,
    ])
</div>
