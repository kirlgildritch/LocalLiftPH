@extends('layouts.admin')

@section('title', 'Manage Sellers')
@section('eyebrow', 'Verification')
@section('page-title', 'Manage Sellers')
@php
    $adminSellersScript = asset('assets/js/admin-sellers-page.js') . '?v=' . @filemtime(public_path('assets/js/admin-sellers-page.js'));
@endphp


@section('content')
    @php
        $avatarClasses = ['gold', 'teal', 'rose', 'slate', 'olive'];
        $publicMediaUrl = fn (?string $path) => \App\Support\PublicAssetUrl::for($path);
        $statusOptions = [
            '' => 'All Statuses',
            'active' => 'Active',
            'pending' => 'Pending Review',
            'rejected' => 'Rejected',
            'flagged' => 'Flagged',
        ];
    @endphp

    <div class="page-stack">
        @include('admin.sellers.partials.stats-grid', ['stats' => $stats])

        @include('admin.sellers.partials.filter-bar', [
            'filters' => $filters,
            'statusOptions' => $statusOptions,
        ])

        @include('admin.sellers.partials.table', [
            'sellers' => $sellers,
            'avatarClasses' => $avatarClasses,
            'publicMediaUrl' => $publicMediaUrl,
        ])
    </div>
@endsection

@push('modals')
    @include('admin.sellers.partials.modals.seller-detail', [
        'documentRequestReasons' => $documentRequestReasons,
    ])
    @include('admin.sellers.partials.modals.document-preview')
@endpush

@push('scripts')
    @php
        $sellerModalData = $sellers->getCollection()->values()->map(function ($seller, $index) use ($avatarClasses, $sellers, $publicMediaUrl) {
            $displayName = $seller->store_name ?: ($seller->full_name ?? $seller->user?->name ?? 'Seller');
            $handle = '@' . \Illuminate\Support\Str::slug($displayName, '');
            $productsCount = $seller->user?->products->count() ?? 0;
            $latestRequest = $seller->latestDocumentRequest;
            $requestReasonLabel = match ($latestRequest?->reason) {
                'proof_of_address' => 'Proof of Address',
                'tax_identification_number' => 'Tax Identification Number',
                'bank_statement' => 'Bank Statement',
                default => $latestRequest?->reason ? ucfirst(str_replace('_', ' ', $latestRequest->reason)) : null,
            };
            $requestStatusLabel = match ($latestRequest?->status) {
                \App\Models\SellerDocumentRequest::STATUS_RESUBMITTED => 'Resubmitted',
                \App\Models\SellerDocumentRequest::STATUS_RESOLVED => 'Resolved',
                \App\Models\SellerDocumentRequest::STATUS_PENDING => 'Pending',
                default => 'None',
            };

            return [
                'id' => $seller->id,
                'name' => $displayName,
                'handle' => $handle,
                'email' => $seller->email ?? $seller->user?->email,
                'date' => optional($seller->submitted_at ?? $seller->created_at)->format('m/d/Y'),
                'products' => $productsCount . ' product' . ($productsCount === 1 ? '' : 's'),
                'valid_id_type' => $seller->valid_id_type ?: 'ID / Passport',
                'valid_id_url' => $publicMediaUrl($seller->valid_id_path),
                'business_permit_url' => $publicMediaUrl($seller->business_permit_path),
                'review_notes' => $seller->review_notes,
                'status' => $seller->application_status,
                'update_url' => route('admin.sellers.status', $seller),
                'avatar' => strtoupper(substr($displayName, 0, 2)),
                'avatar_url' => $publicMediaUrl($seller->shop_logo),
                'avatar_class' => $avatarClasses[(($sellers->firstItem() ?? 1) + $index - 1) % count($avatarClasses)],
                'latest_request_reason' => $latestRequest?->reason,
                'latest_request_reason_label' => $requestReasonLabel,
                'latest_request_notes' => $latestRequest?->admin_notes,
                'latest_request_status' => $latestRequest?->status,
                'latest_request_status_label' => $requestStatusLabel,
                'latest_request_date' => optional($latestRequest?->requested_at)->format('m/d/Y h:i A'),
                'latest_request_document_url' => $publicMediaUrl($latestRequest?->response_document_path),
            ];
        })->values();
    @endphp

    <script id="admin-sellers-modal-data" type="application/json">@json($sellerModalData)</script>
    <script src="{{ $adminSellersScript }}" defer></script>
@endpush

@push('styles')
    <style>
        .seller-filter-bar {
            align-items: stretch;
        }

        .seller-inline-select {
            min-width: 12rem;
        }

        .seller-inline-select select {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text);
            cursor: pointer;
        }

        .seller-table-card {
            display: flex;
            flex-direction: column;
            min-height: 34rem;
        }

        .seller-table-scroll {
            flex: 1 1 auto;
            overflow-x: auto;
        }

        .seller-table-card .pagination-bar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: auto;
            padding-top: 18px;
            align-self: center;
            flex-wrap: wrap;
        }

        .pagination-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            min-width: 42px;
            height: 42px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
            color: #8fa7c4;
            font-weight: 700;
            text-decoration: none;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
            transition: background 0.18s ease, border-color 0.18s ease, color 0.18s ease, transform 0.18s ease;
        }

        .pagination-button:hover {
            background: rgba(66, 165, 245, 0.1);
            border-color: rgba(66, 165, 245, 0.28);
            color: #dfeaff;
        }

        .pagination-button.is-active {
            background: linear-gradient(135deg, #4f8df0, #3e6fdb);
            border-color: rgba(96, 165, 250, 0.4);
            color: #fff;
            box-shadow: 0 12px 24px rgba(62, 111, 219, 0.24);
        }

        .pagination-button.is-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

        .seller-request-card {
            border: 1px solid var(--border);
            border-radius: 16px;
            background: linear-gradient(180deg, #fbfcff 0%, #f5f8fe 100%);
            padding: 1rem 1.1rem;
        }

        .seller-request-empty-state {
            display: grid;
            gap: 0.45rem;
        }

        .seller-request-empty-state strong {
            color: var(--text);
            font-size: 0.98rem;
        }

        .seller-request-empty-state p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .seller-request-details {
            display: grid;
            gap: 0;
        }

        .seller-request-detail-row {
            display: grid;
            grid-template-columns: minmax(110px, 140px) minmax(0, 1fr);
            gap: 0.9rem;
            align-items: start;
            padding: 0.82rem 0;
            border-bottom: 1px solid var(--border);
        }

        .seller-request-detail-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .seller-request-detail-row:first-child {
            padding-top: 0;
        }

        .seller-request-detail-row span:first-child {
            color: var(--muted);
            font-weight: 600;
        }

        .seller-request-detail-row strong {
            color: var(--text);
            line-height: 1.6;
            word-break: break-word;
        }

        .seller-request-status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            width: fit-content;
        }

        .seller-request-status-badge.is-pending {
            background: #fff5dc;
            color: #bf8612;
        }

        .seller-request-status-badge.is-resubmitted {
            background: #e8f0ff;
            color: #3b6fd6;
        }

        .seller-request-status-badge.is-resolved {
            background: #edf8ef;
            color: #3e9c4c;
        }

        .seller-request-status-badge.is-none {
            background: #eef2f8;
            color: #6e7890;
        }

        .seller-request-form-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.8rem;
            align-items: stretch;
        }

        .seller-request-select {
            max-width: none;
            min-height: 48px;
            border-color: #dbe4f1;
            background: #fff;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.75);
        }

        .seller-request-select:focus,
        .seller-request-notes:focus {
            border-color: rgba(59, 111, 214, 0.36);
            box-shadow: 0 0 0 3px rgba(59, 111, 214, 0.08);
            outline: none;
        }

        .seller-request-button {
            min-width: 190px;
        }

        .seller-request-notes {
            min-height: 120px;
            line-height: 1.6;
        }

        .avatar-photo img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            border-radius: inherit;
        }

        .action-button.is-static,
        .button.is-static,
        .action-button:disabled.is-static,
        .button:disabled.is-static {
            opacity: 1;
            cursor: default;
            transform: none;
            box-shadow: none;
            filter: saturate(0.7);
        }

        .seller-request-note {
            margin-top: 0;
        }

        @media (max-width: 720px) {
            .seller-filter-bar > * {
                flex: 1 1 100%;
            }

            .seller-table-card {
                min-height: 0;
            }

            .seller-table-card .pagination-bar {
                width: 100%;
            }

            .seller-request-detail-row {
                grid-template-columns: 1fr;
                gap: 0.35rem;
            }

            .seller-request-form-row {
                grid-template-columns: 1fr;
            }

            .seller-request-button {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
@endpush
