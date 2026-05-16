<form action="{{ $activeConversation['send_url'] }}" method="POST" enctype="multipart/form-data" class="inbox-reply-form" data-inbox-form>
    @csrf
    <input type="text" name="message" placeholder="Type a message..." value="{{ old('message') }}">
    <input type="file" name="image" accept="image/*,video/*">
    <button type="submit" class="page-action-btn">Send</button>
</form>
