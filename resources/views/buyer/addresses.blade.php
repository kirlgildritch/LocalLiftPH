@extends('layouts.app')

@php
    $buyerAddressesScript = asset('assets/js/buyer-addresses-page.js') . '?v=' . @filemtime(public_path('assets/js/buyer-addresses-page.js'));
@endphp

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/buyer_addresses.css') }}">

    <section class="address-page">
        <div class="container address-shell">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>Addresses</span>
            </div>

            @include('buyer.addresses.partials.hero', ['returnTo' => $returnTo])

            @if(session('address_success'))
                <div class="success-message">{{ session('address_success') }}</div>
            @endif

            <div class="address-list">
                @forelse($addresses as $address)
                    @include('buyer.addresses.partials.address-card', [
                        'address' => $address,
                        'returnTo' => $returnTo,
                    ])
                @empty
                    @include('buyer.addresses.partials.empty-state', ['returnTo' => $returnTo])
                @endforelse
            </div>
        </div>
    </section>

    @include('buyer.addresses.partials.edit-modal', ['returnTo' => $returnTo])

    <script src="{{ asset('assets/js/buyer-address-form.js') }}" defer></script>
    <script src="{{ $buyerAddressesScript }}" defer></script>
@endsection
