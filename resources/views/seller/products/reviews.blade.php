@extends('layouts.seller')

@php
    $sellerProductReviewsStyles = asset('assets/css/seller-product-reviews-page.css') . '?v=' . @filemtime(public_path('assets/css/seller-product-reviews-page.css'));
@endphp

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/manage_products.css') }}">
<link rel="stylesheet" href="{{ $sellerProductReviewsStyles }}">

<section class="dashboard-wrapper seller-product-reviews-page">
    <div class="container">
        <div class="dashboard-layout">
            @include('seller.partials.sidebar')

            <main class="dashboard-main">
                <section class="seller-page-panel panel seller-product-reviews-panel">
                    @include('seller.products.reviews.partials.header')
                    @include('seller.products.reviews.partials.summary')
                    @include('seller.products.reviews.partials.list')
                </section>
            </main>
        </div>
    </div>
</section>
@endsection
