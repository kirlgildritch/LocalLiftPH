@extends('layouts.app')

@php
    $buyerOrderModalsScript = asset('assets/js/buyer-order-modals.js') . '?v=' . @filemtime(public_path('assets/js/buyer-order-modals.js'));
@endphp

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_orders.css') }}">

    <section class="orders-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>My Orders</span>
            </div>

            @include('buyer.orders.partials.index.toolbar', [
                'currentStatus' => $currentStatus,
                'statusCounts' => $statusCounts,
            ])

            <div class="orders-list">
                @forelse($orders as $order)
                    @include('buyer.orders.partials.index.order-card', ['order' => $order])
                @empty
                    @include('buyer.orders.partials.index.empty-state')
                @endforelse
            </div>
        </div>
    </section>

    @include('buyer.partials.cancel-order-modal')
    @include('buyer.partials.return-request-modal')

    <script src="{{ $buyerOrderModalsScript }}" defer></script>
@endsection
