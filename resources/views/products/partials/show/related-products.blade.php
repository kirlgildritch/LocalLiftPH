<section class="panel detail-card">
    <div class="detail-header">
        <div>
            <span class="section-kicker">Related Products</span>
            <h2>You may also like</h2>
        </div>
    </div>

    <div class="related-grid product-card-grid" data-skeleton-group data-skeleton-delay="420">
        @forelse($relatedProducts as $relatedProduct)
        <x-product-card :product="$relatedProduct" />
        @empty
        <p>No related products available.</p>
        @endforelse
    </div>
</section>
