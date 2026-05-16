@php
    $targetName = $report->targetLabel();
    $sellerName = $report->seller?->sellerProfile?->store_name
        ?: $report->seller?->name
        ?: $report->product?->user?->sellerProfile?->store_name
        ?: $report->product?->user?->name
        ?: 'Seller unavailable';
    $statusClass = $reportStatusClass($report);
    $targetType = $report->product ? 'Product' : 'Seller';
@endphp

<tr>
    <td>
        <div class="report-product-cell">
            <div class="report-thumb-icon">
                <i class="fa-solid fa-{{ $report->product ? 'box-open' : 'store' }}"></i>
            </div>
            <div class="report-product-cell__text">
                <div class="report-product-name">{{ $targetName }}</div>
                <div class="sub-line">{{ $targetType }} | {{ $sellerName }}</div>
            </div>
        </div>
    </td>
    <td><span class="type-badge">{{ $report->reasonLabel() }}</span></td>
    <td>
        <div class="muted-row"><i class="fa-solid fa-user"></i>
            {{ $report->user?->name ?? 'Deleted user' }}</div>
    </td>
    <td>{{ $report->created_at?->format('M d, Y') }}</td>
    <td><span class="status-pill status-pill--{{ $statusClass }}">{{ $report->statusLabel() }}</span></td>
    <td>
        <button class="action-button action-button--primary" type="button"
            data-report-view="{{ $report->id }}">
            <i class="fa-solid fa-magnifying-glass"></i> View Details
        </button>
    </td>
</tr>
