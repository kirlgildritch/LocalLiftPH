@if($reviews->isEmpty())
    @include('seller.products.reviews.partials.empty')
@else
    <div class="seller-review-page-list">
        @foreach($reviewCards as $reviewCard)
            @include('seller.products.reviews.partials.card', [
                'review' => $reviewCard->review,
                'replyState' => $reviewCard->sellerReply,
                'reviewMedia' => $reviewCard->media,
                'purchaseDetails' => $reviewCard->purchaseDetails,
            ])
        @endforeach
    </div>

    @include('seller.products.reviews.partials.pagination')
@endif
