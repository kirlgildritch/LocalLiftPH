<div class="inbox-thread-header">
    <button type="button" class="inbox-thread-back" data-inbox-back aria-label="Back to conversations">
        <i class="fa-solid fa-arrow-left"></i>
    </button>

    <div class="inbox-thread-heading">
        <h3>No active chat</h3>
        <span>Choose a conversation to continue.</span>
    </div>
</div>

<div class="inbox-thread-messages is-empty-pane">
    @include('messages.partials.inbox.empty-state', [
        'title' => 'No active chat',
        'message' => 'Select a conversation from the left to continue.',
    ])
</div>
