@extends('layouts.app')

@php
    $buyerOrderModalsScript = asset('assets/js/buyer-order-modals.js') . '?v=' . @filemtime(public_path('assets/js/buyer-order-modals.js'));
@endphp

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_orders.css') }}">

    <section class="orders-page">
        <div class="container">
            @php
                $groupPlacedAt = $groupOrders->sortBy('created_at')->first()?->created_at;
            @endphp
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('buyer.orders') }}">My Orders</a>
                <span>&gt;</span>
                <span>Checkout Summary</span>
            </div>

            @include('buyer.orders.partials.show.toolbar', [
                'groupSummary' => $groupSummary,
                'order' => $order,
            ])

            @if($groupOrders->count() === 1)
                @include('buyer.partials.order-progress', ['order' => $order])
            @endif

            @include('buyer.orders.partials.show.detail-summary-card', [
                'groupPlacedAt' => $groupPlacedAt,
                'groupSummary' => $groupSummary,
                'order' => $order,
            ])

            <div class="orders-list" id="rate-products">
                @foreach($groupOrders as $shopOrder)
                    @include('buyer.orders.partials.show.shop-order-card', [
                        'shopOrder' => $shopOrder,
                        'groupOrders' => $groupOrders,
                    ])
                @endforeach
            </div>
        </div>
    </section>

    @include('buyer.partials.cancel-order-modal')
    @include('buyer.partials.return-request-modal')

    <script src="{{ $buyerOrderModalsScript }}" defer></script>
@endsection
