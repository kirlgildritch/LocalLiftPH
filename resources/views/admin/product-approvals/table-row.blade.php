@php
    $seller = $product->user;
    $sellerProfile = $seller?->sellerProfile;
    $sellerDisplay = $seller?->name ?? 'Seller';
    $shopName = $sellerProfile?->store_name ?: 'No shop name';
    $status = $statusBadge($product);
    $imageUrl = $publicMediaUrl($product->image);
    $canApprove = ! ($product->status === \App\Models\Product::STATUS_APPROVED && $product->is_active);
    $canReject = $product->status !== \App\Models\Product::STATUS_REJECTED;
@endphp

<tr>
    <td class="checkbox-cell">
        <input class="selection-checkbox product-select" type="checkbox"
            value="{{ $product->id }}" aria-label="Select {{ $product->name }}">
    </td>
    <td>
        <div class="product-cell">
            <button class="product-thumb-button" type="button"
                data-image-preview="{{ $imageUrl }}" data-image-title="{{ $product->name }}">
                @if ($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}">
                @else
                    <span>{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                @endif
            </button>
            <div class="product-cell__text">
                <div class="product-title">{{ $product->name }}</div>
                <div class="sub-line">{{ $product->category->name ?? 'Uncategorized' }}</div>
                <div class="sub-line">{{ 'Price: ' . $money($product->price) }}</div>
            </div>
        </div>
    </td>
    <td>
        <div class="product-cell__text">
            <div class="product-title">{{ $shopName }}</div>
            <div class="sub-line">{{ $sellerDisplay }}</div>
            <div class="sub-line">{{ '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_') }}</div>
        </div>
    </td>
    <td>
        <div class="status-stack">
            <span class="status-pill {{ $status['class'] }}">{{ $status['label'] }}</span>
            @if ($product->pending_reports_count > 0)
                <span class="status-pill status-pill--danger">
                    {{ $product->pending_reports_count }} report{{ $product->pending_reports_count > 1 ? 's' : '' }}
                </span>
            @endif
            <div class="sub-line">
                {{ optional($product->created_at)->format('M d, Y h:i A') ?: 'Unknown' }}</div>
        </div>
    </td>
    <td>
        <div class="table-actions">
            <div class="table-actions__primary">
                @if ($canApprove)
                    <form method="POST" action="{{ route('admin.products.approve', $product) }}">
                        @csrf
                        @method('PATCH')
                        <button class="action-button action-button--success"
                            type="submit">Approve</button>
                    </form>
                @endif

                @if ($canReject)
                    <button class="action-button action-button--danger" type="button"
                        data-reject-url="{{ route('admin.products.reject', $product) }}"
                        data-reject-name="{{ $product->name }}">
                        Reject
                    </button>
                @endif

                <button class="action-button action-button--primary" type="button"
                    data-product-view="{{ $product->id }}">
                    <i class="fa-solid fa-magnifying-glass"></i> View Details
                </button>
            </div>
        </div>
    </td>
</tr>
