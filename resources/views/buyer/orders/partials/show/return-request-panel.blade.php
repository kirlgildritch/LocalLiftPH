<div class="return-request-panel">
    <div>
        <span class="toolbar-label">Return / Refund</span>
        <h3>{{ $returnRequest->statusLabel() }}</h3>
        <p>{{ $returnRequest->reason }} &middot; {{ \Illuminate\Support\Str::headline($returnRequest->preferred_resolution) }}</p>
        <p>{{ $returnRequest->details }}</p>
        @if(filled($returnRequest->seller_response))
            <strong>Seller response:</strong>
            <p>{{ $returnRequest->seller_response }}</p>
        @endif
        @if($returnRequest->media->isNotEmpty())
            <div class="return-evidence-grid">
                @foreach($returnRequest->media as $media)
                    <a href="{{ $media->url }}" target="_blank" rel="noopener">
                        @if($media->type === 'video')
                            <video src="{{ $media->url }}" muted preload="metadata"></video>
                        @else
                            <img src="{{ $media->url }}" alt="Return evidence">
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    <span class="order-status {{ $returnRequest->toneClass() }}">
        {{ $returnRequest->statusLabel() }}
    </span>
</div>
