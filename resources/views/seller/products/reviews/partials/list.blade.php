@if($reviews->isEmpty())
    @include('seller.products.reviews.partials.empty')
@else
    <div class="seller-review-page-list">
        @foreach($reviews as $review)
            @include('seller.products.reviews.partials.card', ['review' => $review])
        @endforeach
    </div>

    @include('seller.products.reviews.partials.pagination')
@endif
