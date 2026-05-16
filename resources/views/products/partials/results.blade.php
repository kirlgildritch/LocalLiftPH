<div class="product-grid product-card-grid" data-market-pagination-grid>
    @forelse($products as $product)
        <x-product-card :product="$product" :buyer-location="$buyerLocation" />
    @empty
        <div class="panel" style="padding: 20px;">
            <p>
                @if(!empty($search))
                    No products found for "<strong>{{ $search }}</strong>".
                @else
                    No products available yet.
                @endif
            </p>
        </div>
    @endforelse
</div>

@if($products->hasPages())
    <div class="panel"
        data-market-pagination-nav
        style="padding: 16px 20px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
        <p style="margin: 0; color: #9fb3c8; font-size: 14px;">
            Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }}
            products
        </p>

        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @if($products->onFirstPage())
                <span class="action-btn secondary-btn" style="opacity: 0.5; pointer-events: none;">Previous</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="action-btn secondary-btn" data-market-pagination-link>Previous</a>
            @endif

            <span style="color: #dbeafe; font-size: 14px;">Page {{ $products->currentPage() }} of
                {{ $products->lastPage() }}</span>

            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="action-btn secondary-btn" data-market-pagination-link>Next</a>
            @else
                <span class="action-btn secondary-btn" style="opacity: 0.5; pointer-events: none;">Next</span>
            @endif
        </div>
    </div>
@endif
