@extends('layouts.admin')

@section('title', 'Product Approvals')
@section('eyebrow', 'Moderation')
@section('page-title', 'Product Approvals')
@php
    $adminProductsScript = asset('assets/js/admin-products-page.js') . '?v=' . @filemtime(public_path('assets/js/admin-products-page.js'));
    $adminProductsStyles = asset('assets/css/admin-products-page.css') . '?v=' . @filemtime(public_path('assets/css/admin-products-page.css'));
@endphp

@section('content')
    @php
        $statusMeta = [
            'pending' => ['label' => 'Pending', 'empty' => 'No pending products.'],
            'approved' => ['label' => 'Approved', 'empty' => 'No approved products.'],
            'rejected' => ['label' => 'Rejected', 'empty' => 'No rejected products.'],
            'reported' => ['label' => 'Reported', 'empty' => 'No reported products.'],
            'delisted' => ['label' => 'Delisted', 'empty' => 'No delisted products.'],
        ];

        $statusBadge = function ($product) {
            if ($product->status === \App\Models\Product::STATUS_APPROVED && ! $product->is_active) {
                return ['label' => 'Delisted', 'class' => 'status-pill--delivered'];
            }

            return match ($product->status) {
                \App\Models\Product::STATUS_APPROVED => ['label' => 'Approved', 'class' => 'status-pill--success'],
                \App\Models\Product::STATUS_REJECTED => ['label' => 'Rejected', 'class' => 'status-pill--cancelled'],
                default => ['label' => 'Pending', 'class' => 'status-pill--pending'],
            };
        };

        $sellerStatusBadge = function ($status) {
            return match ($status) {
                \App\Models\Seller::STATUS_APPROVED => 'status-pill--success',
                \App\Models\Seller::STATUS_REJECTED => 'status-pill--cancelled',
                default => 'status-pill--pending',
            };
        };

        $money = fn ($value) => 'PHP ' . number_format((float) $value, 2);
        $publicMediaUrl = fn (?string $path) => \App\Support\PublicAssetUrl::for($path);

        $productModalData = $products
            ->map(function ($product) use ($statusBadge, $sellerStatusBadge, $money) {
                $seller = $product->user;
                $sellerProfile = $seller?->sellerProfile;
                $status = $statusBadge($product);
                $imageUrl = \App\Support\PublicAssetUrl::for($product->image);
                $mediaItems = ($product->gallery_media ?? collect())->values()->all();
                $sellerDisplay = $seller?->name ?? 'Seller';
                $shopName = $sellerProfile?->store_name ?: 'No shop name';
                $condition = $product->condition ? ucfirst((string) $product->condition) : 'Not set';
                $dimensions = trim(
                    collect([
                        $product->length_cm ? $product->length_cm . ' cm L' : null,
                        $product->width_cm ? $product->width_cm . ' cm W' : null,
                        $product->height_cm ? $product->height_cm . ' cm H' : null,
                    ])
                        ->filter()
                        ->implode(', '),
                ) ?: 'Not set';
                $sellerProducts = $seller?->products
                    ?->map(function ($sellerProduct) use ($statusBadge, $money) {
                        $productStatus = $statusBadge($sellerProduct);

                        return [
                            'id' => $sellerProduct->id,
                            'name' => $sellerProduct->name,
                            'image_url' => \App\Support\PublicAssetUrl::for($sellerProduct->image),
                            'category' => $sellerProduct->category->name ?? 'Uncategorized',
                            'price' => $money($sellerProduct->price),
                            'stock' => (string) $sellerProduct->stock,
                            'status_label' => $productStatus['label'],
                            'status_class' => $productStatus['class'],
                            'date_added' => optional($sellerProduct->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                        ];
                    })
                    ->values()
                    ->all() ?? [];

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'seller' => '@' . \Illuminate\Support\Str::slug($sellerDisplay, '_'),
                    'seller_name' => $sellerDisplay,
                    'shop_name' => $shopName,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'price' => $money($product->price),
                    'shipping_fee' => $money($product->shipping_fee),
                    'description' => $product->description ?: 'No details provided.',
                    'dimensions' => $dimensions,
                    'weight' => $product->weight ? $product->weight . ' kg' : 'Not set',
                    'stock' => (string) $product->stock,
                    'condition' => $condition,
                    'status_label' => $status['label'],
                    'status_class' => $status['class'],
                    'submitted_at' => optional($product->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                    'image_url' => $imageUrl,
                    'media_items' => $mediaItems,
                    'pending_reports_count' => (int) $product->pending_reports_count,
                    'rejection_reason' => $product->rejection_reason ?: 'None',
                    'approve_url' => route('admin.products.approve', $product),
                    'reject_url' => route('admin.products.reject', $product),
                    'can_approve' => ! ($product->status === \App\Models\Product::STATUS_APPROVED && $product->is_active),
                    'can_reject' => $product->status !== \App\Models\Product::STATUS_REJECTED,
                    'avatar' => strtoupper(substr($sellerDisplay, 0, 2)),
                    'seller_avatar_url' => \App\Support\PublicAssetUrl::for($sellerProfile?->shop_logo),
                    'seller_status_label' => ucfirst($sellerProfile?->application_status ?? 'pending'),
                    'seller_status_class' => $sellerStatusBadge($sellerProfile?->application_status),
                    'seller_email' => $sellerProfile?->email ?: $seller?->email ?: 'N/A',
                    'seller_phone' => $sellerProfile?->contact_number ?: $seller?->phone ?: 'N/A',
                    'seller_address' => $sellerProfile?->address ?: $seller?->address ?: 'N/A',
                    'seller_description' => $sellerProfile?->store_description ?: 'No description provided.',
                    'seller_owner_name' => $sellerProfile?->full_name ?: $sellerDisplay,
                    'seller_registered_at' => optional($seller?->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                    'seller_verification_status' => ucfirst($sellerProfile?->application_status ?? 'pending'),
                    'seller_submitted_at' => optional($sellerProfile?->submitted_at ?? $sellerProfile?->created_at)->format('M d, Y h:i A') ?: 'Unknown',
                    'seller_id_type' => $sellerProfile?->valid_id_type ?: 'Government Issued ID',
                    'seller_id_url' => \App\Support\PublicAssetUrl::for($sellerProfile?->valid_id_path),
                    'seller_permit_url' => \App\Support\PublicAssetUrl::for($sellerProfile?->business_permit_path),
                    'seller_products' => $sellerProducts,
                ];
            })
            ->values();

        $tabQuery = request()->except('status');
    @endphp

    <div class="page-stack">
        @include('admin.product-approvals.status-tabs', [
            'statusMeta' => $statusMeta,
            'currentTab' => $currentTab,
            'statusCounts' => $statusCounts,
            'tabQuery' => $tabQuery,
        ])

        <article class="table-card moderation-card">
            <div class="table-card__header moderation-header">
                <div>
                    <h3 class="section-title">{{ $statusMeta[$currentTab]['label'] }}</h3>
                </div>

                @include('admin.product-approvals.filters', [
                    'currentTab' => $currentTab,
                    'categories' => $categories,
                    'sellers' => $sellers,
                    'filters' => $filters,
                ])
            </div>

            @include('admin.product-approvals.bulk-toolbar')

            @include('admin.product-approvals.table', [
                'products' => $products,
                'statusMeta' => $statusMeta,
                'currentTab' => $currentTab,
                'statusBadge' => $statusBadge,
                'money' => $money,
                'publicMediaUrl' => $publicMediaUrl,
            ])
        </article>
    </div>

    @include('admin.product-approvals.bulk-approve-form')
@endsection

@push('modals')
    @include('admin.product-approvals.modals.product-approval')
    @include('admin.product-approvals.modals.reject-reason', [
        'rejectionReasons' => $rejectionReasons,
    ])
    @include('admin.product-approvals.modals.image-preview')
    @include('admin.product-approvals.modals.seller')
    @include('admin.product-approvals.modals.seller-document')
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ $adminProductsStyles }}">
@endpush

@push('scripts')
    <script id="admin-products-modal-data" type="application/json">@json($productModalData)</script>
    <script src="{{ $adminProductsScript }}" defer></script>
@endpush
