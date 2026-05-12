<article class="shop-review-card">
    <div class="shop-review-card__header">
        <div class="shop-review-card__author">
            <div class="shop-review-card__avatar">
                @if($review->user?->profile_image)
                    <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name ?? 'Buyer' }}">
                @else
                    <span>{{ strtoupper(mb_substr($review->user->name ?? 'B', 0, 1)) }}</span>
                @endif
            </div>

            <div>
                <strong>{{ $review->user->name ?? 'LocalLift Buyer' }}</strong>
                <span>{{ $review->created_at->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="shop-review-card__rating" aria-label="{{ $review->rating }} out of 5 stars">
            @for($star = 1; $star <= 5; $star++)
                <i class="fa-{{ $review->rating >= $star ? 'solid' : 'regular' }} fa-star"></i>
            @endfor
        </div>
    </div>

    <div class="shop-review-card__product">
        <i class="fa-solid fa-bag-shopping"></i>
        <span>{{ $review->product?->name ?? 'Product review' }}</span>
    </div>

    <p>{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>

    @php
        $reviewMedia = $review->media->isNotEmpty()
            ? $review->media
            : collect([
                $review->image_path ? (object) ['type' => 'image', 'path' => $review->image_path] : null,
                $review->video_path ? (object) ['type' => 'video', 'path' => $review->video_path] : null,
            ])->filter();
    @endphp

    @if($reviewMedia->isNotEmpty())
        <div class="shop-review-media-grid">
            @foreach($reviewMedia as $media)
                @if($media->type === 'video')
                    <video controls preload="metadata">
                        <source src="{{ asset('storage/' . $media->path) }}">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ asset('storage/' . $media->path) }}" alt="Review picture from {{ $review->user->name ?? 'buyer' }}">
                @endif
            @endforeach
        </div>
    @endif

    @if($review->seller_reply)
        <div class="shop-review-reply">
            <strong>Seller reply</strong>
            <p>{{ $review->seller_reply }}</p>
        </div>
    @endif
</article>
