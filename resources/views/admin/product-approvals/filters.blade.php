<form method="GET" action="{{ route('admin.products') }}" class="toolbar-row moderation-filters">
    <input type="hidden" name="status" value="{{ $currentTab }}">

    <label class="inline-select">
        <span>Category</span>
        <select name="category_id">
            <option value="">All</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) $filters['category_id'] === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="inline-select">
        <span>Seller</span>
        <select name="seller_id">
            <option value="">All</option>
            @foreach ($sellers as $seller)
                @php
                    $shopName = $seller->sellerProfile?->store_name ?: $seller->name;
                @endphp
                <option value="{{ $seller->id }}"
                    @selected((string) $filters['seller_id'] === (string) $seller->id)>
                    {{ $shopName }}
                </option>
            @endforeach
        </select>
    </label>

    <label class="filter-input filter-input--compact">
        <span>Min</span>
        <input type="number" min="0" step="0.01" name="price_min"
            value="{{ $filters['price_min'] }}" placeholder="0.00">
    </label>

    <label class="filter-input filter-input--compact">
        <span>Max</span>
        <input type="number" min="0" step="0.01" name="price_max"
            value="{{ $filters['price_max'] }}" placeholder="0.00">
    </label>

    <label class="inline-select">
        <span>Sort</span>
        <select name="sort">
            <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
            <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest</option>
        </select>
    </label>

    <div class="filter-actions">
        <button class="action-button action-button--primary" type="submit">Apply</button>
        <a class="action-button action-button--light"
            href="{{ route('admin.products', ['status' => $currentTab]) }}">Reset</a>
    </div>
</form>
