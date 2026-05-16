@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/add_products.css') }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

@php
    $variantsEnabled = old('has_variants') === '1';
    $variantRows = collect(old('variants', [
        ['name' => '', 'sku' => '', 'price' => old('price'), 'stock' => old('stock'), 'is_active' => 1],
    ]))->values();
    $sellerProductFormScript = asset('assets/js/seller-product-form.js') . '?v=' . @filemtime(public_path('assets/js/seller-product-form.js'));
@endphp

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">
            @include('seller.partials.sidebar')

            <main class="dashboard-main">
                <section class="seller-page-panel panel">
                    <div class="page-header">
                        <div>
                            <span class="section-kicker">Catalog</span>
                            <h2>Add Product</h2>
                        </div>

                        <a href="{{ route('seller.products.index') }}" class="table-action secondary">
                            <i class="fa-solid fa-arrow-left"></i>
                            Back
                        </a>
                    </div>

                    <form class="product-form" action="{{ route('seller.products.store') }}" method="POST"
                        data-enable-loading enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="weight" id="shipping_weight" value="{{ old('weight') }}">
                        <input type="hidden" name="width_cm" id="shipping_width" value="{{ old('width_cm') }}">
                        <input type="hidden" name="length_cm" id="shipping_length" value="{{ old('length_cm') }}">
                        <input type="hidden" name="height_cm" id="shipping_height" value="{{ old('height_cm') }}">
                        <input type="hidden" name="shipping_fee" id="shipping_fee" value="{{ old('shipping_fee') }}">

                        <div class="form-grid">
                            @include('seller.products.partials.form.basic-information', [
                                'categories' => $categories,
                                'categoryValue' => old('category_id'),
                                'conditionValue' => old('condition'),
                                'descriptionMode' => 'quill',
                                'descriptionValue' => old('description'),
                                'editorHeight' => '220px',
                                'includeConditionPlaceholder' => true,
                                'namePlaceholder' => '',
                                'nameValue' => old('name'),
                            ])

                            @include('seller.products.partials.form.pricing-fields', [
                                'priceLabel' => 'Price',
                                'pricePlaceholder' => '₱ ',
                                'priceValue' => old('price'),
                                'stockLabel' => 'Stock Quantity',
                                'stockValue' => old('stock'),
                            ])

                            <div class="form-group form-group-wide">
                                @include('seller.products.partials.form.variant-builder', [
                                    'builderClass' => '',
                                    'showExistingImageNote' => false,
                                    'showVariantId' => false,
                                    'variantHelpText' => 'Use variants for size, color, bundles, or any option with different price or stock.',
                                    'variantRows' => $variantRows,
                                    'variantsEnabled' => $variantsEnabled,
                                ])
                            </div>

                            @include('seller.products.partials.form.media-picker', [
                                'label' => 'Product Media',
                                'note' => 'Upload one or more images or videos. The first image will be used as the cover image.',
                            ])

                            <div class="form-group form-group-wide">
                                <label>Shipping Setup</label>
                                <div class="shipping-summary">
                                    <div class="shipping-summary-copy">
                                        <strong id="shippingSummaryFee">Shipping fee not set</strong>
                                        <span id="shippingSummaryMeta">Add package size and weight to calculate shipping.</span>
                                    </div>

                                    <button type="button" class="page-action-btn shipping-open-btn"
                                        id="openShippingModal">
                                        Set Shipping Fee
                                    </button>
                                </div>
                                @error('weight')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                                @error('width_cm')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                                @error('length_cm')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                                @error('height_cm')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="page-action-btn" data-enable-loading
                                data-loading-text="Adding Product...">Add Product</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
</section>

<div class="shipping-modal-overlay" id="shippingModal" style="display: none;">
    <div class="shipping-modal panel">
        <div class="shipping-modal-header">
            <div>
                <span class="section-kicker">Shipping Setup</span>
                <h3>Package details</h3>
            </div>
            <button type="button" class="shipping-close-btn" id="closeShippingModal">&times;</button>
        </div>

        <div class="shipping-modal-grid">
            <div class="form-group">
                <label for="modal_weight">Weight (kg)</label>
                <input type="number" id="modal_weight" step="0.01" min="0.01" value="{{ old('weight') }}">
            </div>

            <div class="form-group">
                <label for="modal_width">Width (cm)</label>
                <input type="number" id="modal_width" step="0.01" min="0.01" value="{{ old('width_cm') }}">
            </div>

            <div class="form-group">
                <label for="modal_length">Length (cm)</label>
                <input type="number" id="modal_length" step="0.01" min="0.01" value="{{ old('length_cm') }}">
            </div>

            <div class="form-group">
                <label for="modal_height">Height (cm)</label>
                <input type="number" id="modal_height" step="0.01" min="0.01" value="{{ old('height_cm') }}">
            </div>
        </div>

        <div class="shipping-fee-preview">
            <span>Calculated Shipping Fee</span>
            <strong id="shippingFeePreview">&#8369; 0.00</strong>
            <small>Formula: &#8369; 60 base fee + &#8369; 35 x billable weight.</small>
        </div>

        <div class="shipping-modal-actions">
            <button type="button" class="table-action secondary" id="cancelShippingModal">Cancel</button>
            <button type="button" class="page-action-btn" id="saveShippingSetup">Save</button>
        </div>
    </div>
</div>

<style>
    .ql-container {
        min-height: 160px;
        font-size: 14px;
    }

    .shipping-modal-overlay.show {
        display: flex !important;
    }
</style>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js" defer></script>

<script src="{{ $sellerProductFormScript }}" defer></script>
@endsection
