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

                                <div class="seller-review-status-stack">
                                    <div class="seller-rating-chip">
                                        <i class="fa-solid fa-star"></i>
                                        {{ $review->rating }}/5
                                    </div>
                                    <span class="seller-reply-status seller-reply-status--{{ $replyState->statusTone }}">
                                        {{ $replyState->statusLabel }}
                                    </span>
                                </div>
                            </div>

                            @if($purchaseDetails)
                            <div class="seller-review-purchase-meta">
                                <i class="fa-solid fa-receipt"></i>
                                <span>{{ $purchaseDetails }}</span>
                            </div>
                            @endif

                            <p>{{ $review->comment ?: 'Verified buyer rating submitted.' }}</p>

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
                                <div class="seller-review-reply-card-header">
                                    <span class="seller-review-reply-icon">
                                        <i class="fa-solid fa-reply"></i>
                                    </span>
                                    <div>
                                        <strong>Your public reply</strong>
                                        <span>Visible below this buyer review.</span>
                                    </div>
                                    @if($review->seller_replied_at)
                                    <time>{{ $review->seller_replied_at->format('M d, Y') }}</time>
                                    @endif
                                </div>

                                <p>{{ $review->seller_reply }}</p>
                            </div>
                            @endif

                            <form action="{{ route('seller.products.reviews.reply', [$product, $review]) }}" method="POST" class="seller-review-reply-form">
                                @csrf
                                @method('PATCH')

                                <div class="seller-review-reply-form-header">
                                    <div>
                                        <label for="seller_reply_{{ $review->id }}">{{ $replyState->formTitle }}</label>
                                        <p>{{ $replyState->formHint }}</p>
                                    </div>
                                    <span>1000 max</span>
                                </div>

                                <div class="seller-review-reply-field">
                                    <textarea name="seller_reply" id="seller_reply_{{ $review->id }}" rows="3" maxlength="1000" required
                                        placeholder="{{ $replyState->placeholder }}">{{ old('seller_reply', $review->seller_reply) }}</textarea>
                                </div>

                                <div class="seller-review-reply-actions">
                                    <small><i class="fa-solid fa-eye"></i> Public response</small>
                                    <button type="submit" class="submitt table-action secondary">
                                        <i class="fa-solid fa-reply"></i>
                                        {{ $replyState->buttonLabel }}
                                    </button>
                                </div>
                            </form>
                        </article>
