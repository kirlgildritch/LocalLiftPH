@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/add_products.css') }}">

    <section class="dashboard-wrapper edit-product-page">
        <div class="container">
            <div class="dashboard-layout">
                @include('seller.partials.sidebar')

                <main class="dashboard-main">
                    @include('seller.partials.success-toast')

                    @php
                        $descriptionValue = old('description');

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
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Basic Information</h3>
                                        <p>Keep the product details clear and buyer-friendly.</p>
                                    </div>

                                    <div class="form-grid edit-main-grid">
                                        <div class="form-group form-group-wide">
                                            <label for="name">Product Name</label>
                                            <input type="text" id="name" name="name"
                                                value="{{ old('name', $product->name ?? '') }}" placeholder="Enter product name">
                                            @error('name')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="category_id">Category</label>
                                            <select id="category_id" name="category_id">
                                                <option value="">Select category</option>
                                                @isset($categories)
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            @error('category_id')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="condition">Condition</label>
                                            <select id="condition" name="condition">
                                                <option value="new" {{ old('condition', $product->condition ?? '') === 'new' ? 'selected' : '' }}>New</option>
                                                <option value="used" {{ old('condition', $product->condition ?? '') === 'used' ? 'selected' : '' }}>Used</option>
                                            </select>
                                            @error('condition')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group form-group-wide">
                                            <label for="description">Product Description</label>
                                            <textarea id="description" name="description" rows="7"
                                                placeholder="Describe your product">{{ $descriptionValue }}</textarea>
                                            @error('description')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Pricing and Stock</h3>
                                        <p>Keep pricing accurate and inventory up to date.</p>
                                    </div>

                                    <div class="form-grid edit-two-column-grid">
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <input type="number" id="price" name="price"
                                                value="{{ old('price', $product->price ?? '') }}" placeholder="0.00" step="0.01">
                                            @error('price')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="stock">Stock</label>
                                            <input type="number" id="stock" name="stock"
                                                value="{{ old('stock', $product->stock ?? '') }}" placeholder="0">
                                            @error('stock')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </section>

                                <section class="edit-section-card">
                                    <div class="edit-section-heading">
                                        <h3>Product Image</h3>
                                        <p>Replace the current image only if you want to update the listing preview.</p>
                                    </div>

                                    <div class="edit-image-layout">
                                        @if (!empty($product?->image))
                                            <div class="current-image-preview">
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <label for="image">Change Product Image</label>
                                            <input type="file" id="image" name="image">
                                            @error('image')
                                                <span class="error-text">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
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
                                    <button type="submit" class="page-action-btn">Save Changes</button>
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
@endsection
