<article class="review-card">
    <div class="review-card-header">
        <div class="review-author">
            <div class="review-author-avatar">
                @if($review->user?->profile_image)
                    <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name ?? 'Buyer' }}">
                @else
                    <span>{{ strtoupper(mb_substr($review->user->name ?? 'B', 0, 1)) }}</span>
                @endif
            </div>
            <div>
                <strong>
                    {{ $review->user->name ?? 'LocalLift Buyer' }}
                    <i class="fa-solid fa-circle-check"></i>
                </strong>
                <span>{{ $review->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="review-card-stars" aria-label="{{ $review->rating }} out of 5 stars">
            @for($star = 1; $star <= 5; $star++)
                <i class="fa-{{ $review->rating >= $star ? 'solid' : 'regular' }} fa-star"></i>
            @endfor
        </div>
    </div>

    @if($review->purchaseDetailsLabel())
        <div class="review-purchase-meta">
            <i class="fa-solid fa-receipt"></i>
            <span>{{ $review->purchaseDetailsLabel() }}</span>
        </div>
    @endif

    <p class="review-card-comment">{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>

    @php
        $reviewMedia = $review->media->isNotEmpty()
            ? $review->media
            : collect([
                $review->image_path ? (object) ['type' => 'image', 'path' => $review->image_path] : null,
                $review->video_path ? (object) ['type' => 'video', 'path' => $review->video_path] : null,
            ])->filter();
    @endphp

    @if($reviewMedia->isNotEmpty())
        <div class="review-media-grid">
            @foreach($reviewMedia as $media)
                @if($media->type === 'video')
                    <div class="review-media-item review-media-video-wrap">
                        <video class="review-media-video" controls preload="metadata" data-review-lightbox-trigger data-review-lightbox-type="video" data-review-lightbox-src="{{ asset('storage/' . $media->path) }}">
                            <source src="{{ asset('storage/' . $media->path) }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    <a href="{{ asset('storage/' . $media->path) }}" target="_blank" rel="noopener" class="review-media-item review-media-image" data-review-lightbox-trigger data-review-lightbox-type="image" data-review-lightbox-src="{{ asset('storage/' . $media->path) }}">
                        <img src="{{ asset('storage/' . $media->path) }}" alt="Review picture from {{ $review->user->name ?? 'buyer' }}">
                    </a>
                @endif
            @endforeach
        </div>
    @endif

    @if($review->seller_reply)
        <div class="seller-review-reply">
            <strong>Seller reply</strong>
            <p>{{ $review->seller_reply }}</p>
            @if($review->seller_replied_at)
                <span>{{ $review->seller_replied_at->format('M d, Y') }}</span>
            @endif
        </div>
    @endif
</article>
