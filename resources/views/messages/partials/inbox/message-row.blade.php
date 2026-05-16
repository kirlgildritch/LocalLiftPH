@php
    $messageMediaType = $message['media_type'] ?? (!empty($message['has_video']) ? 'video' : (!empty($message['has_image']) ? 'image' : null));
    $messageMediaUrl = $message['media_url'] ?? $message['video_url'] ?? $message['image_url'] ?? null;
@endphp

<div class="inbox-message-row {{ !empty($message['is_current_user']) ? 'is-current-user' : '' }}">
    <div class="inbox-message-bubble">
        <strong>{{ $message['sender_label'] }}</strong>
        @if(!empty($message['has_product']) && !empty($message['product']))
            <a href="{{ $message['product']['url'] }}" class="inbox-product-card">
                <img src="{{ $message['product']['image_url'] }}" alt="{{ $message['product']['name'] }}"
                    class="inbox-product-card-image">

                <span class="inbox-product-card-copy">
                    <span class="inbox-product-card-label">Product</span>
                    <strong>{{ $message['product']['name'] }}</strong>
                    <span>{{ $message['product']['price_label'] }}</span>
                    <span>{{ $message['product']['shop_name'] }}</span>
                </span>
            </a>
        @endif
        @if(!empty($message['has_text']))
            <p>{{ $message['message'] }}</p>
        @endif
        @if(!empty($messageMediaUrl))
            @if($messageMediaType === 'video')
                <video src="{{ $messageMediaUrl }}" controls preload="metadata" class="inbox-message-media inbox-message-media--video"></video>
            @else
                <img src="{{ $messageMediaUrl }}" alt="Shared image" class="inbox-message-media">
            @endif
        @endif
    </div>
    <span class="inbox-message-meta">
        {{ $message['time'] }}
        @if(!empty($message['status_label']))
            <em>{{ $message['status_label'] }}</em>
        @endif
    </span>
</div>
