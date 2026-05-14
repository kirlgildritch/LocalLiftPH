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

                                    <div class="variant-builder edit-variant-builder" data-variant-builder data-next-index="{{ $variantRows->count() }}">
                                        <div class="variant-builder-head">
                                            <div>
                                                <label class="variant-toggle-label" for="has_variants">
                                                    <input type="checkbox" id="has_variants" name="has_variants" value="1" data-variant-toggle {{ $variantsEnabled ? 'checked' : '' }}>
                                                    <span>This product has variants</span>
                                                </label>
                                                <small class="product-media-note">When variants are enabled, the product card uses the lowest active variant price and total active stock.</small>
                                            </div>
                                            <button type="button" class="table-action secondary" data-add-variant {{ $variantsEnabled ? '' : 'hidden' }}>
                                                Add Variant
                                            </button>
                                        </div>

                                        @error('variants')
                                            <small class="error-text">{{ $message }}</small>
                                        @enderror

                                        <div class="variant-list" data-variant-list {{ $variantsEnabled ? '' : 'hidden' }}>
                                            @foreach($variantRows as $index => $variantRow)
                                                <div class="variant-row" data-variant-row>
                                                    @if(!empty($variantRow['id']))
                                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variantRow['id'] }}">
                                                    @endif

                                                    <div class="form-group">
                                                        <label>Variant Name</label>
                                                        <input type="text" name="variants[{{ $index }}][name]" value="{{ $variantRow['name'] ?? '' }}" placeholder="e.g. Small / Red">
                                                        @error("variants.$index.name")
                                                            <small class="error-text">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>SKU</label>
                                                        <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variantRow['sku'] ?? '' }}" placeholder="Optional">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Price</label>
                                                        <input type="number" name="variants[{{ $index }}][price]" value="{{ $variantRow['price'] ?? '' }}" step="0.01" min="0" placeholder="0.00">
                                                        @error("variants.$index.price")
                                                            <small class="error-text">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Stock</label>
                                                        <input type="number" name="variants[{{ $index }}][stock]" value="{{ $variantRow['stock'] ?? '' }}" min="0" placeholder="0">
                                                        @error("variants.$index.stock")
                                                            <small class="error-text">{{ $message }}</small>
                                                        @enderror
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Image</label>
                                                        <input type="file" name="variants[{{ $index }}][image]" accept="image/*">
                                                        @if(!empty($variantRow['image']))
                                                            <small class="product-media-note">Current image saved.</small>
                                                        @endif
                                                    </div>

                                                    <div class="variant-row-actions">
                                                        <input type="hidden" name="variants[{{ $index }}][is_active]" value="0">
                                                        <label class="variant-active-toggle">
                                                            <input type="checkbox" name="variants[{{ $index }}][is_active]" value="1" {{ (bool) ($variantRow['is_active'] ?? true) ? 'checked' : '' }}>
                                                            Active
                                                        </label>
                                                        <button type="button" class="table-action danger" data-remove-variant>Remove</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
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

                                    <div class="form-group form-group-wide">
                                        <label for="media">Add More Media</label>
                                        <input type="file" id="media" name="media[]" accept="image/*,video/*" multiple data-product-media-input>
                                        <small class="product-media-note">Upload one or more images or videos. The first image becomes the cover image.</small>
                                        @error('media')
                                            <span class="error-text">{{ $message }}</span>
                                        @enderror
                                        @error('media.*')
                                            <span class="error-text">{{ $message }}</span>
                                        @enderror
                                        @error('image')
                                            <span class="error-text">{{ $message }}</span>
                                        @enderror
                                        <div class="product-media-preview" data-product-media-preview hidden></div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('.edit-product-form');
            const mediaInput = document.querySelector('[data-product-media-input]');
            const previewGrid = document.querySelector('[data-product-media-preview]');
            const existingGallery = document.querySelector('[data-existing-media-gallery]');
            const selectedFiles = [];
            const objectUrls = new Map();
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (!mediaInput || !previewGrid || !window.URL?.createObjectURL || !window.DataTransfer || !form) {
                return;
            }

            const revokeObjectUrls = () => {
                objectUrls.forEach(function (url) {
                    URL.revokeObjectURL(url);
                });
                objectUrls.clear();
            };

            const syncInputFiles = () => {
                const transfer = new DataTransfer();

                selectedFiles.forEach(function (file) {
                    transfer.items.add(file);
                });

                mediaInput.files = transfer.files;
            };

            const formatFileSize = (bytes) => {
                if (bytes < 1024 * 1024) {
                    return `${Math.max(1, Math.round(bytes / 1024))} KB`;
                }

                return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
            };

            const renderPreview = () => {
                revokeObjectUrls();

                previewGrid.innerHTML = '';
                previewGrid.hidden = selectedFiles.length === 0;

                selectedFiles.forEach((file, index) => {
                    const previewUrl = URL.createObjectURL(file);
                    objectUrls.set(`${file.name}-${index}-${file.lastModified}`, previewUrl);

                    const card = document.createElement('div');
                    card.className = 'product-media-preview-card';

                    const mediaWrap = document.createElement('div');
                    mediaWrap.className = 'product-media-preview-media';

                    if (file.type.startsWith('video/')) {
                        const video = document.createElement('video');
                        video.src = previewUrl;
                        video.controls = true;
                        video.muted = true;
                        video.preload = 'metadata';
                        mediaWrap.appendChild(video);
                    } else {
                        const image = document.createElement('img');
                        image.src = previewUrl;
                        image.alt = file.name;
                        mediaWrap.appendChild(image);
                    }

                    const meta = document.createElement('div');
                    meta.className = 'product-media-preview-meta';
                    meta.textContent = `${file.name} (${formatFileSize(file.size)})`;

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'product-media-preview-remove';
                    removeButton.setAttribute('aria-label', `Remove ${file.name}`);
                    removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    removeButton.addEventListener('click', function () {
                        selectedFiles.splice(index, 1);
                        syncInputFiles();
                        renderPreview();
                    });

                    card.appendChild(mediaWrap);
                    card.appendChild(meta);
                    card.appendChild(removeButton);
                    previewGrid.appendChild(card);
                });
            };

            const syncExistingGalleryVisibility = () => {
                if (!existingGallery) {
                    return;
                }

                const hasVisibleCards = Array.from(existingGallery.querySelectorAll('[data-existing-media-card]'))
                    .some((card) => !card.hidden);

                existingGallery.hidden = !hasVisibleCards;
            };

            mediaInput.addEventListener('change', function () {
                const nextFiles = Array.from(mediaInput.files || []);

                if (nextFiles.length === 0) {
                    syncInputFiles();
                    return;
                }

                nextFiles.forEach(function (file) {
                    const alreadySelected = selectedFiles.some(function (currentFile) {
                        return currentFile.name === file.name
                            && currentFile.size === file.size
                            && currentFile.lastModified === file.lastModified;
                    });

                    if (!alreadySelected) {
                        selectedFiles.push(file);
                    }
                });

                syncInputFiles();
                renderPreview();
            });

            existingGallery?.addEventListener('click', async function (event) {
                const removeButton = event.target.closest('[data-remove-existing-media]');
                const deleteUrl = existingGallery.dataset.existingMediaDeleteUrl || '';

                if (!removeButton || !deleteUrl) {
                    return;
                }

                const card = removeButton.closest('[data-existing-media-card]');
                const mediaPath = card?.dataset.mediaPath || '';

                if (!card || !mediaPath) {
                    return;
                }

                removeButton.disabled = true;

                try {
                    const response = await fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            ...(csrfToken ? {
                                'X-CSRF-TOKEN': csrfToken,
                            } : {}),
                        },
                        body: JSON.stringify({
                            path: mediaPath,
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Unable to remove saved media.');
                    }

                    card.remove();
                    syncExistingGalleryVisibility();
                } catch (error) {
                    removeButton.disabled = false;
                    window.alert('Unable to remove this saved media right now. Please try again.');
                }
            });

            syncExistingGalleryVisibility();
            window.addEventListener('beforeunload', revokeObjectUrls);
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const builder = document.querySelector('[data-variant-builder]');

            if (!builder) {
                return;
            }

            const toggle = builder.querySelector('[data-variant-toggle]');
            const list = builder.querySelector('[data-variant-list]');
            const addButton = builder.querySelector('[data-add-variant]');
            let nextIndex = Number(builder.dataset.nextIndex || 0);

            const variantTemplate = (index) => `
                <div class="variant-row" data-variant-row>
                    <div class="form-group">
                        <label>Variant Name</label>
                        <input type="text" name="variants[${index}][name]" placeholder="e.g. Small / Red">
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" name="variants[${index}][sku]" placeholder="Optional">
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="variants[${index}][price]" step="0.01" min="0" placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="variants[${index}][stock]" min="0" placeholder="0">
                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="variants[${index}][image]" accept="image/*">
                    </div>
                    <div class="variant-row-actions">
                        <input type="hidden" name="variants[${index}][is_active]" value="0">
                        <label class="variant-active-toggle">
                            <input type="checkbox" name="variants[${index}][is_active]" value="1" checked>
                            Active
                        </label>
                        <button type="button" class="table-action danger" data-remove-variant>Remove</button>
                    </div>
                </div>
            `;

            const syncVisibility = () => {
                const enabled = toggle.checked;
                list.hidden = !enabled;
                addButton.hidden = !enabled;

                if (enabled && !list.querySelector('[data-variant-row]')) {
                    list.insertAdjacentHTML('beforeend', variantTemplate(nextIndex++));
                }
            };

            toggle.addEventListener('change', syncVisibility);
            addButton.addEventListener('click', function () {
                list.insertAdjacentHTML('beforeend', variantTemplate(nextIndex++));
            });

            list.addEventListener('click', function (event) {
                const removeButton = event.target.closest('[data-remove-variant]');

                if (!removeButton) {
                    return;
                }

                const rows = list.querySelectorAll('[data-variant-row]');

                if (rows.length <= 1) {
                    rows[0]?.querySelectorAll('input[type="text"], input[type="number"]').forEach((input) => {
                        input.value = '';
                    });
                    return;
                }

                removeButton.closest('[data-variant-row]')?.remove();
            });

            syncVisibility();
        });
    </script>
@endsection
