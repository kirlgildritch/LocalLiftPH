@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/add_products.css') }}">

    <section class="dashboard-wrapper edit-product-page">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
@php
    $productGallery = $product->gallery_media ?? collect();
    $descriptionValue = old('description');
    $hasExistingVariants = $product->variants->where('is_active', true)->isNotEmpty();
    $variantsEnabled = old('has_variants', $hasExistingVariants ? '1' : null) === '1';
    $variantRows = old('variants');
    $sellerProductFormScript = asset('assets/js/seller-product-form.js') . '?v=' . @filemtime(public_path('assets/js/seller-product-form.js'));

    if ($variantRows === null) {
        $variantRows = $product->variants->map(fn ($variant) => [
            'id' => $variant->id,
            'name' => $variant->name,
            'sku' => $variant->sku,
            'price' => $variant->price,
            'stock' => $variant->stock,
            'image' => $variant->image,
            'is_active' => $variant->is_active,
        ])->values()->all();
    }

    $variantRows = collect($variantRows ?: [
        ['name' => '', 'sku' => '', 'price' => $product->price, 'stock' => $product->stock, 'is_active' => 1],
    ])->values();

                        if ($descriptionValue === null) {
                            $descriptionValue = html_entity_decode(
                                trim(
                                    preg_replace(
                                        "/\n{3,}/",
                                        "\n\n",
                                        strip_tags(
                                            str_ireplace(
                                                ['<br>', '<br/>', '<br />', '</p>', '</div>', '</li>'],
                                                ["\n", "\n", "\n", "\n", "\n", "\n"],
                                                $product->description ?? ''
                                            )
                                        )
                                    )
                                ),
                                ENT_QUOTES,
                                'UTF-8'
                            );
                        }
                    @endphp

                    <section class="seller-page-panel panel edit-product-panel">
                        <div class="edit-product-shell">
                            <div class="page-header edit-product-header">
                                <div>
                                    <span class="section-kicker">Catalog</span>
                                    <h2>Edit Product</h2>
                                    <p>Update your product details, pricing, stock, and shipping information.</p>
                                </div>

                                <a href="{{ route('seller.products.index') }}" class="table-action secondary edit-back-btn">
                                    <i class="fa-solid fa-arrow-left"></i>
                                    Back
                                </a>
                            </div>

                            <form class="product-form edit-product-form" action="{{ route('seller.products.update', $product) }}"
                                data-enable-loading
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Basic Information</h3>
                                        <p>Keep the product details clear and buyer-friendly.</p>
                                    </div>

                                    @include('seller.products.partials.form.basic-information', [
                                        'categories' => $categories ?? collect(),
                                        'categoryValue' => old('category_id', $product->category_id ?? ''),
                                        'conditionValue' => old('condition', $product->condition ?? ''),
                                        'descriptionLabel' => 'Product Description',
                                        'descriptionMode' => 'textarea',
                                        'descriptionPlaceholder' => 'Describe your product',
                                        'descriptionRows' => 7,
                                        'descriptionValue' => $descriptionValue,
                                        'includeConditionPlaceholder' => false,
                                        'namePlaceholder' => 'Enter product name',
                                        'nameValue' => old('name', $product->name ?? ''),
                                        'wrapperClass' => 'form-grid edit-main-grid',
                                    ])
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Pricing and Stock</h3>
                                        <p>Keep pricing accurate and inventory up to date.</p>
                                    </div>

                                    @include('seller.products.partials.form.pricing-fields', [
                                        'priceValue' => old('price', $product->price ?? ''),
                                        'stockValue' => old('stock', $product->stock ?? ''),
                                        'wrapperClass' => 'form-grid edit-two-column-grid',
                                    ])

                                    @include('seller.products.partials.form.variant-builder', [
                                        'builderClass' => 'edit-variant-builder',
                                        'showExistingImageNote' => true,
                                        'showVariantId' => true,
                                        'variantHelpText' => 'When variants are enabled, the product card uses the lowest active variant price and total active stock.',
                                        'variantRows' => $variantRows,
                                        'variantsEnabled' => $variantsEnabled,
                                    ])
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Product Media</h3>
                                        <p>Keep the current gallery and add more images or videos for the carousel.</p>
                                    </div>

                                    @if($productGallery->isNotEmpty())
                                        <div class="product-media-gallery" data-existing-media-gallery data-existing-media-delete-url="{{ route('seller.products.media.destroy', $product) }}">
                                            @foreach($productGallery as $media)
                                                <div class="product-media-gallery-card" data-existing-media-card data-media-path="{{ $media['path'] ?? '' }}">
                                                    <div class="product-media-gallery-media">
                                                        @if(($media['type'] ?? 'image') === 'video')
                                                            <video src="{{ $media['url'] }}" controls preload="metadata"></video>
                                                        @else
                                                            <img src="{{ $media['url'] }}" alt="{{ $product->name }}">
                                                        @endif
                                                    </div>
                                                    @if(!empty($media['path']))
                                                        <button type="button" class="product-media-preview-remove" data-remove-existing-media aria-label="Remove saved media {{ $loop->iteration }}">
                                                            <i class="fa-solid fa-xmark"></i>
                                                        </button>
                                                    @endif
                                                    <div class="product-media-gallery-meta">
                                                        {{ ucfirst($media['type'] ?? 'image') }} {{ $loop->iteration }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @include('seller.products.partials.form.media-picker', [
                                        'label' => 'Add More Media',
                                        'note' => 'Upload one or more images or videos. The first image becomes the cover image.',
                                    ])
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Shipping Details</h3>
                                        <p>Package measurements are used to calculate the shipping fee.</p>
                                    </div>

                                    <div class="form-grid edit-two-column-grid">
                                        <div class="form-group">
                                            <label for="weight">Weight (kg)</label>
                                            <input type="number" id="weight" name="weight"
                                                value="{{ old('weight', $product->weight ?? '') }}" step="0.01">
                                            @error('weight')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="shipping_fee">Shipping Fee</label>
                                            <input type="number" id="shipping_fee" name="shipping_fee"
                                                value="{{ old('shipping_fee', $product->shipping_fee ?? '') }}" step="0.01" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="length_cm">Length (cm)</label>
                                            <input type="number" id="length_cm" name="length_cm"
                                                value="{{ old('length_cm', $product->length_cm ?? '') }}" step="0.01">
                                            @error('length_cm')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="width_cm">Width (cm)</label>
                                            <input type="number" id="width_cm" name="width_cm"
                                                value="{{ old('width_cm', $product->width_cm ?? '') }}" step="0.01">
                                            @error('width_cm')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="height_cm">Height (cm)</label>
                                            <input type="number" id="height_cm" name="height_cm"
                                                value="{{ old('height_cm', $product->height_cm ?? '') }}" step="0.01">
                                            @error('height_cm')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </section>

                                <div class="form-actions edit-form-actions">
                                    <a href="{{ route('seller.products.index') }}" class="table-action secondary">Cancel</a>
                                    <button type="submit" class="page-action-btn" data-enable-loading
                                        data-loading-text="Saving...">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </section>

    <style>
        .edit-product-panel {
            padding: 28px;
        }

        .edit-product-shell {
            width: min(100%, 1000px);
            margin: 0 auto;
            display: grid;
            gap: 24px;
        }

        .edit-product-header {
            align-items: center;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(187, 222, 251, 0.12);
        }

        .edit-product-header h2 {
            margin-bottom: 10px;
        }

        .edit-product-header p,
        .edit-section-heading p {
            margin: 0;
            color: #8fa7c4;
            line-height: 1.75;
        }

        .edit-back-btn {
            gap: 10px;
        }

        .edit-product-form {
            gap: 22px;
        }

        .edit-section-card {
            display: grid;
            gap: 18px;
            padding: 24px;
            border: 1px solid rgba(187, 222, 251, 0.12);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.03);
        }

        .edit-section-heading {
            display: grid;
            gap: 8px;
        }

        .edit-section-heading h3 {
            margin: 0;
            font-size: 1.2rem;
            letter-spacing: -0.02em;
        }

        .edit-main-grid,
        .edit-two-column-grid {
            gap: 18px;
        }

        .edit-product-form .form-group {
            gap: 10px;
        }

        .edit-product-form input,
        .edit-product-form select,
        .edit-product-form textarea {
            min-height: 54px;
            border-radius: 18px;
            background: rgba(10, 19, 34, 0.72);
        }

        .edit-product-form textarea {
            min-height: 180px;
            padding-top: 14px;
        }

        .edit-product-form input[readonly] {
            opacity: 0.9;
            cursor: not-allowed;
        }

        .edit-image-layout {
            display: grid;
            gap: 18px;
        }

        .current-image-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 220px;
            max-height: 220px;
            padding: 18px;
            border: 1px dashed rgba(66, 165, 245, 0.3);
            border-radius: 22px;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02)),
                rgba(4, 11, 22, 0.84);
            overflow: hidden;
        }

        .current-image-preview img {
            width: 100%;
            max-width: 420px;
            max-height: 220px;
            object-fit: contain;
            display: block;
            border-radius: 16px;
        }

        .edit-form-actions {
            justify-content: flex-end;
            padding-top: 4px;
        }

        @media (max-width: 980px) {
            .edit-product-header {
                align-items: flex-start;
            }
        }

        @media (max-width: 720px) {
            .edit-product-panel {
                padding: 20px 18px;
            }

            .edit-product-shell {
                gap: 20px;
            }

            .edit-section-card {
                padding: 18px;
                border-radius: 20px;
            }

            .edit-product-form .form-actions {
                width: 100%;
            }

            .edit-product-form .form-actions .table-action,
            .edit-product-form .form-actions .page-action-btn,
            .edit-back-btn {
                width: 100%;
            }

            .current-image-preview {
                min-height: 190px;
                max-height: 190px;
            }

            .current-image-preview img {
                max-height: 190px;
            }
        }
    </style>

    <script src="{{ $sellerProductFormScript }}" defer></script>
@endsection
