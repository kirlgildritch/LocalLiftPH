@extends('layouts.seller')

@section('title', 'Seller Notifications')

@php
    $sellerNotificationsScript = asset('assets/js/seller-notifications-page.js') . '?v=' . @filemtime(public_path('assets/js/seller-notifications-page.js'));
@endphp

@push('styles')
    <style>
        .seller-notifications-page {
            display: grid;
            gap: 18px;
            color: #f5f9ff;
        }

        .seller-notifications-toolbar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .seller-notifications-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 999px;
            padding: 10px 14px;
            color: #b8c8e0;
            background: rgba(255, 255, 255, 0.04);
            text-decoration: none;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
            transition: 0.18s ease;
        }

        .seller-notifications-chip:hover {
            color: #f5f9ff;
            background: rgba(66, 165, 245, 0.12);
            transform: translateY(-1px);
        }

        .seller-notifications-chip.is-active {
            background: linear-gradient(180deg, rgba(66, 165, 245, 0.28), rgba(66, 165, 245, 0.18));
            border-color: rgba(66, 165, 245, 0.45);
            color: #ffffff;
        }

        .seller-notification-summary {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .seller-notification-summary__card,
        .seller-notification-panel {
            background: linear-gradient(180deg, rgba(10, 19, 34, 0.96), rgba(7, 14, 24, 0.92));
            border: 1px solid rgba(187, 222, 251, 0.14);
            border-radius: 18px;
            box-shadow: 0 20px 44px rgba(0, 0, 0, 0.28);
            backdrop-filter: blur(16px);
        }

        .seller-notification-summary__card {
            padding: 20px;
        }

        .seller-notification-summary__label {
            margin: 0 0 8px;
            color: #8fa7c4;
            font-weight: 700;
            font-size: 14px;
        }

        .seller-notification-summary__value {
            margin: 0;
            font-size: 30px;
            font-weight: 800;
            color: #42a5f5;
        }

        .seller-notification-panel__header {
            padding: 20px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.12);
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }

        .seller-notification-panel__title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: #f5f9ff;
        }

        .seller-notification-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .seller-notification-btn {
            border: 1px solid rgba(187, 222, 251, 0.14);
            background: rgba(255, 255, 255, 0.04);
            color: #e8f2ff;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .seller-notification-btn.primary {
            background: linear-gradient(180deg, #4f7ff2, #3f6fd9);
            border-color: #4f7ff2;
            color: #fff;
        }

        .seller-notification-btn.danger {
            color: #ff8b8b;
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.22);
        }

        .seller-notification-list {
            display: grid;
        }

        .seller-notification-row {
            display: grid;
            grid-template-columns: 48px minmax(0, 1fr) auto;
            gap: 14px;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.1);
            align-items: center;
            color: inherit;
        }

        .seller-notification-row.unread {
            background: rgba(66, 165, 245, 0.08);
        }

        .seller-notification-row__icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: rgba(66, 165, 245, 0.14);
            color: #42a5f5;
            display: grid;
            place-items: center;
            font-size: 18px;
            text-decoration: none;
        }

        .seller-notification-row__content {
            min-width: 0;
            text-decoration: none;
            color: inherit;
        }

        .seller-notification-row__content h3 {
            margin: 0 0 5px;
            color: #f5f9ff;
            font-size: 16px;
            font-weight: 800;
        }

        .seller-notification-row__content p {
            margin: 0 0 6px;
            color: #8fa7c4;
            line-height: 1.45;
        }

        .seller-notification-row__content small {
            color: #7d8aa4;
            font-weight: 600;
        }

        .seller-notification-row__header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: start;
        }

        .seller-notification-tag {
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(66, 165, 245, 0.12);
            color: #bbdefb;
            white-space: nowrap;
        }

        .seller-notification-row__actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .seller-notification-status {
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 800;
            background: rgba(35, 146, 70, 0.18);
            color: #7fe1a6;
        }

        .seller-notification-status.unread {
            background: rgba(173, 116, 0, 0.18);
            color: #ffd27a;
        }

        .seller-notification-icon-button {
            border: 1px solid rgba(187, 222, 251, 0.14);
            background: rgba(255, 255, 255, 0.04);
            width: 38px;
            height: 38px;
            border-radius: 12px;
            color: #d9e7fb;
            cursor: pointer;
        }

        .seller-notification-icon-button.danger {
            color: #ff8b8b;
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.22);
        }

        .seller-notification-empty {
            padding: 50px 20px;
            text-align: center;
            color: #8fa7c4;
        }

        .seller-notification-empty i {
            font-size: 36px;
            color: #8fa7c4;
            margin-bottom: 12px;
        }

        .seller-notification-pagination {
            padding: 18px 20px;
        }

        .seller-notification-pagination nav {
            display: grid;
            gap: 14px;
        }

        .seller-notification-pagination nav > div:first-child {
            display: none;
        }

        .seller-notification-pagination nav > div:last-child {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .seller-notification-pagination nav > div:last-child > div:last-child > span,
        .seller-notification-pagination nav > div:last-child > div:last-child > span > span,
        .seller-notification-pagination nav > div:last-child > div:last-child > a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .seller-notification-pagination :is(nav, ul, ol, p) {
            margin: 0;
        }

        .seller-notification-pagination a,
        .seller-notification-pagination span {
            color: #e8f2ff;
        }

        .seller-notification-pagination nav a,
        .seller-notification-pagination nav span[aria-current="page"] > span,
        .seller-notification-pagination nav span[aria-disabled="true"] > span {
            min-width: 42px;
            min-height: 42px;
            padding: 0 14px;
            border-radius: 12px;
            border: 1px solid rgba(187, 222, 251, 0.14);
            background: rgba(255, 255, 255, 0.04);
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            box-sizing: border-box;
        }

        .seller-notification-pagination nav a:hover {
            background: rgba(66, 165, 245, 0.12);
            border-color: rgba(66, 165, 245, 0.3);
        }

        .seller-notification-pagination nav span[aria-current="page"] > span {
            background: linear-gradient(180deg, rgba(66, 165, 245, 0.28), rgba(66, 165, 245, 0.18));
            border-color: rgba(66, 165, 245, 0.45);
            color: #ffffff;
        }

        .seller-notification-pagination nav span[aria-disabled="true"] > span {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .seller-notification-pagination nav p {
            color: #8fa7c4;
            font-size: 14px;
            font-weight: 600;
        }

        .seller-notification-pagination nav p .font-medium {
            color: #f5f9ff;
        }

        .seller-notification-pagination svg {
            width: 20px;
            height: 20px;
            display: block;
            flex: 0 0 auto;
            fill: currentColor;
        }

        @media (max-width: 980px) {
            .seller-notification-summary {
                grid-template-columns: 1fr;
            }

            .seller-notification-row {
                grid-template-columns: 42px minmax(0, 1fr);
            }

            .seller-notification-row__actions {
                grid-column: 1 / -1;
                justify-content: flex-start;
            }

            .seller-notification-pagination nav > div:last-child {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $filter = $filter ?? 'all';
        $filterLabels = [
            'all' => 'All',
            'unread' => 'Unread',
            'orders' => 'Orders',
            'messages' => 'Messages',
            'reviews' => 'Reviews',
            'admin' => 'Admin',
        ];
    @endphp

    <section class="dashboard-wrapper">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    <div class="seller-notifications-page" data-seller-notifications-page
                        data-current-filter="{{ $filter }}"
                        data-current-page="{{ $notifications->currentPage() }}"
                        data-per-page="{{ $notifications->perPage() }}">
                        @include('seller.notifications.partials.toolbar')
                        @include('seller.notifications.partials.summary')
                        @include('seller.notifications.partials.panel')
                    </div>
                </main>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ $sellerNotificationsScript }}" defer></script>
@endpush
