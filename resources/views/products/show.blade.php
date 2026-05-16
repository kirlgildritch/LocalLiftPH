@extends('layouts.app')
@section('title', 'LocalLift PH - Product')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/product_details.css') }}">

@if(session('error'))
<div style="color:red;">{{ session('error') }}</div>
@endif

@if($errors->any())
<div style="color:red;">
    @foreach($errors->all() as $error)
    <p>{{ $error }}</p>
    @endforeach
</div>
@endif

<section class="product-detail-page">
    <div class="container">
        <div class="checkout-breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span>&gt;</span>
            <a href="{{ route('products.index') }}">Products</a>
            <span>&gt;</span>
            <span>{{ $product->name }}</span>
        </div>

        <div class="product-detail-layout">
            <div class="product-main panel">
                @include('products.partials.show.gallery')
                @include('products.partials.show.summary')
            </div>

            <aside class="purchase-sidebar">
                @include('products.partials.show.purchase-card')
            </aside>
        </div>

        @includeWhen(
            $productPage->hasVariants && $productPage->activeVariants->count() > $productPage->variantPreviewLimit,
            'products.partials.show.variant-modal'
        )

        <div class="detail-sections">
            @include('products.partials.show.description')
        </div>

        <div class="detail-sections">
            @include('products.partials.show.reviews')
            @include('products.partials.show.related-products')
        </div>
    </div>
</section>

<div class="review-lightbox" data-review-lightbox hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="Review media preview">
    <button type="button" class="review-lightbox-close" data-review-lightbox-close aria-label="Close preview">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="review-lightbox-dialog" data-review-lightbox-dialog></div>
</div>

@vite([
    'resources/js/product-gallery.js',
    'resources/js/purchase-variants.js',
    'resources/js/review-upload.js'
])
@endsection
