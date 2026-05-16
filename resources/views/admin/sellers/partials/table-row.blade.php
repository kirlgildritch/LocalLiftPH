@php
    $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
    $handle = '@' . \Illuminate\Support\Str::slug($displayName, '');
    $shopLogoUrl = $publicMediaUrl($seller->shop_logo);
    $products = $seller->user?->products ?? collect();
    $productsCount = $products->count();
    $categoryCounts = $products
        ->groupBy(fn($product) => $product->category?->name ?? 'Uncategorized')
        ->map
        ->count()
        ->sortDesc();
    $distinctCategoryCount = $categoryCounts->count();
    $topCategory = $categoryCounts->keys()->first();
    $categoryLabel = match (true) {
        $productsCount === 0 => 'No products yet',
        $distinctCategoryCount === 1 => $topCategory,
        default => 'Multiple Categories',
    };
    $categoryDetail = $productsCount > 0 && $distinctCategoryCount > 1 ? 'Top: ' . $topCategory : null;
    $hasFlaggedProducts = $products->contains(function ($product) {
        return $product->reports->where('status', \App\Models\Report::STATUS_PENDING)->isNotEmpty();
    });
    $statusLabel = $hasFlaggedProducts ? 'Flagged' : match ($seller->application_status) {
        'approved' => 'Active',
        'rejected' => 'Rejected',
        default => 'Pending Review',
    };
    $statusClass = $hasFlaggedProducts ? 'status-pill--danger' : match ($seller->application_status) {
        'approved' => 'status-pill--success',
        'rejected' => 'status-pill--danger',
        default => 'status-pill--pending',
    };
    $avatarClass = $avatarClasses[(($sellers->firstItem() ?? 1) + $index - 1) % count($avatarClasses)];
@endphp

<tr>
    <td>
        <div class="seller-cell">
            <div class="avatar-photo avatar-photo--{{ $avatarClass }}">
                @if ($shopLogoUrl)
                    <img src="{{ $shopLogoUrl }}" alt="{{ $displayName }}">
                @else
                    {{ strtoupper(substr($displayName, 0, 2)) }}
                @endif
            </div>
            <div class="seller-cell__text">
                <div class="seller-name">{{ $displayName }}</div>
                <div class="sub-line">{{ $handle }}</div>
            </div>
        </div>
    </td>
    <td>
        <div class="seller-cell__text">
            <div class="seller-name">{{ $categoryLabel }}</div>
            @if ($categoryDetail)
                <div class="sub-line">{{ $categoryDetail }}</div>
            @endif
        </div>
    </td>
    <td>{{ $productsCount }}</td>
    <td>
        <span class="status-pill {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </td>
    <td>{{ optional($seller->submitted_at ?? $seller->created_at)->format('m/d/Y') }}</td>
    <td>
        <div class="table-actions__primary">
            <button class="action-button action-button--primary" type="button"
                data-seller-view="{{ $seller->id }}">
                <i class="fa-solid fa-magnifying-glass"></i> View
            </button>
        </div>
    </td>
</tr>
