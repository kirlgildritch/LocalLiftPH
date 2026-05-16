@extends('layouts.app')
@section('title', 'LocalLift PH - Checkout')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}">
    @php
        $checkoutPaymentScript = asset('assets/js/checkout-payment.js') . '?v=' . @filemtime(public_path('assets/js/checkout-payment.js'));
        $paymentMethods = $paymentMethods ?? \App\Models\Order::paymentMethods();
        $selectedPaymentMethod = $selectedPaymentMethod ?? \App\Models\Order::PAYMENT_METHOD_COD;
        $selectedPayment = $paymentMethods[$selectedPaymentMethod] ?? reset($paymentMethods);
        $voucherCode = old('voucher_code', $voucherCode ?? null);
    @endphp

    <section class="checkout-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <a href="{{ route('cart.index') }}">Cart</a>
                <span>&gt;</span>
                <span>Checkout</span>
            </div>

            <div class="checkout-layout">
                <div class="checkout-main">
                    @include('checkout.partials.shipping-address')
                    @include('checkout.partials.shipping-method')
                    @include('checkout.partials.payment-method')
                </div>

                <aside class="checkout-sidebar">
                    @include('checkout.partials.order-summary')
                </aside>
            </div>
        </div>
    </section>

    <script src="{{ $checkoutPaymentScript }}" defer></script>
@endsection
