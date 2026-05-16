                        <article class="seller-review-page-card">
                            <div class="seller-review-page-header">
                                <div class="seller-review-author">
                                    <div class="seller-review-author-avatar">
                                        @if($review->user?->profile_image)
                                        <img src="{{ asset('storage/' . $review->user->profile_image) }}" alt="{{ $review->user->name ?? 'Buyer' }}">
                                        @else
                                        <span>{{ strtoupper(mb_substr($review->user->name ?? 'B', 0, 1)) }}</span>
                                        @endif
                                    </div>

                                    <div>
                                        <strong>
                                            {{ $review->user->name ?? 'Buyer' }}
                                            <i class="fa-solid fa-circle-check"></i>
                                        </strong>
                                        <span>{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <div class="seller-rating-chip">
                                    <i class="fa-solid fa-star"></i>
                                    {{ $review->rating }}/5
                                </div>
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
                            <div class="seller-review-media-grid">
                                @foreach($reviewMedia as $media)
                                @if($media->type === 'video')
                                <video controls>
                                    <source src="{{ asset('storage/' . $media->path) }}">
                                </video>
                                @else
                                <img src="{{ asset('storage/' . $media->path) }}" alt="Review picture">
                                @endif
                                @endforeach
                            </div>
                            @endif

                            @if(filled($review->seller_reply))
                            <div class="seller-review-reply-card">
                                <div>
                                    <strong>Your reply</strong><br>
                                    @if($review->seller_replied_at)
                                    <span>{{ $review->seller_replied_at->format('M d, Y') }}</span><br>
                                    @endif
                                </div>

                                <br>
                                <p>{{ $review->seller_reply }}</p>
                            </div>
                            @endif

                            <form action="{{ route('seller.products.reviews.reply', [$product, $review]) }}" method="POST" class="seller-review-reply-form">
                                @csrf
                                @method('PATCH')

                                <div class="seller-review-reply-form-header">
                                    <label for="seller_reply_{{ $review->id }}">
                                        {{ $review->seller_reply ? 'Edit your reply' : 'Reply to this review' }}
                                    </label>
                                    <span>{{ $review->seller_reply ? ':' : ':' }}</span>
                                </div>

                                <div class="seller-review-reply-field">
                                    <textarea name="seller_reply" id="seller_reply_{{ $review->id }}" rows="3" maxlength="1000" required
                                        placeholder="Thank the buyer, answer concerns, or clarify product details...">{{ old('seller_reply', $review->seller_reply) }}</textarea>
                                </div>

                                <div class="seller-review-reply-actions">
                                    <button type="submit" class="submitt table-action secondary">
                                        <i class="fa-solid fa-reply"></i>
                                        {{ $review->seller_reply ? 'Submit Reply' : 'Post Reply' }}
                                    </button>
                                </div>
                            </form>
                        </article>
