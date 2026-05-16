<section class="panel detail-card">
    <div class="detail-header">
        <div>
            <span class="section-kicker">Recently Viewed</span>
            <h2>Viewed by you</h2>
        </div>
    </div>

    <div class="related-grid product-card-grid" data-skeleton-group data-skeleton-delay="420">
        @foreach($recentlyViewedProducts as $recentProduct)
            <x-product-card :product="$recentProduct" />
        @endforeach
    </div>
</section>
