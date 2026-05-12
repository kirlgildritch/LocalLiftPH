@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/add_products.css') }}">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

@php
    $variantsEnabled = old('has_variants') === '1';
    $variantRows = collect(old('variants', [
        ['name' => '', 'sku' => '', 'price' => old('price'), 'stock' => old('stock'), 'is_active' => 1],
    ]))->values();
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
                            <div class="form-group form-group-wide">
                                <label for="name">Product Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}">
                                @error('name')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group form-group-wide">
                                <label for="description">Description</label>
                                <div id="editor" style="height: 220px;">{!! old('description') !!}</div>
                                <input type="hidden" name="description" id="description">
                                @error('description')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="price">Price</label>
                                <input type="number" id="price" name="price" step="0.01" min="0"
                                    value="{{ old('price') }}" placeholder="&#8369; ">
                                @error('price')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="stock">Stock Quantity</label>
                                <input type="number" id="stock" name="stock" min="0" value="{{ old('stock') }}">
                                @error('stock')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group form-group-wide">
                                <div class="variant-builder" data-variant-builder data-next-index="{{ $variantRows->count() }}">
                                    <div class="variant-builder-head">
                                        <div>
                                            <label class="variant-toggle-label" for="has_variants">
                                                <input type="checkbox" id="has_variants" name="has_variants" value="1" data-variant-toggle {{ $variantsEnabled ? 'checked' : '' }}>
                                                <span>This product has variants</span>
                                            </label>
                                            <small class="product-media-note">Use variants for size, color, bundles, or any option with different price or stock.</small>
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
                            </div>

                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select name="category_id" id="category_id">
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="condition">Condition</label>
                                <select name="condition" id="condition">
                                    <option value="">Select condition</option>
                                    <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="used" {{ old('condition') === 'used' ? 'selected' : '' }}>Used</option>
                                </select>
                                @error('condition')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group form-group-wide">
                                <label for="media">Product Media</label>
                                <input type="file" id="media" name="media[]" accept="image/*,video/*" multiple data-product-media-input>
                                <small class="product-media-note">Upload one or more images or videos. The first image will be used as the cover image.</small>
                                @error('media')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                                @error('media.*')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                                @error('image')
                                    <small class="error-text">{{ $message }}</small>
                                @enderror
                                <div class="product-media-preview" data-product-media-preview hidden></div>
                            </div>

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

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'align': []
                    }],
                    ['link']
                ]
            }
        });

        const form = document.querySelector('.product-form');
        form.addEventListener('submit', function () {
            document.getElementById('description').value = quill.root.innerHTML;
        });

        const modal = document.getElementById('shippingModal');
        const openButton = document.getElementById('openShippingModal');
        const closeButton = document.getElementById('closeShippingModal');
        const cancelButton = document.getElementById('cancelShippingModal');
        const saveButton = document.getElementById('saveShippingSetup');
        const feePreview = document.getElementById('shippingFeePreview');
        const summaryFee = document.getElementById('shippingSummaryFee');
        const summaryMeta = document.getElementById('shippingSummaryMeta');

        const modalWeight = document.getElementById('modal_weight');
        const modalWidth = document.getElementById('modal_width');
        const modalLength = document.getElementById('modal_length');
        const modalHeight = document.getElementById('modal_height');

        const hiddenWeight = document.getElementById('shipping_weight');
        const hiddenWidth = document.getElementById('shipping_width');
        const hiddenLength = document.getElementById('shipping_length');
        const hiddenHeight = document.getElementById('shipping_height');
        const hiddenFee = document.getElementById('shipping_fee');

        function calculateFee() {
            const weight = parseFloat(modalWeight.value) || 0;
            const width = parseFloat(modalWidth.value) || 0;
            const length = parseFloat(modalLength.value) || 0;
            const height = parseFloat(modalHeight.value) || 0;
            const volumetricWeight = (width * length * height) / 5000;
            const billableWeight = Math.max(weight, volumetricWeight);
            const fee = billableWeight > 0 ? (60 + (billableWeight * 35)) : 0;

            feePreview.innerHTML = `&#8369; ${fee.toFixed(2)}`;

            return fee;
        }

        function updateShippingSummary() {
            const weight = hiddenWeight.value;
            const width = hiddenWidth.value;
            const length = hiddenLength.value;
            const height = hiddenHeight.value;
            const fee = parseFloat(hiddenFee.value || 0);

            if (!weight || !width || !length || !height || !fee) {
                summaryFee.textContent = 'Shipping fee not set';
                summaryMeta.textContent = 'Add package size and weight to calculate shipping.';
                return;
            }

            summaryFee.innerHTML = `&#8369; ${fee.toFixed(2)}`;
            summaryMeta.textContent = `${weight} kg | ${width}cm x ${length}cm x ${height}cm`;
        }

        function openModal() {
            modal.classList.add('show');
        }

        function closeModal() {
            modal.classList.remove('show');
        }

        [modalWeight, modalWidth, modalLength, modalHeight].forEach(input => {
            input.addEventListener('input', calculateFee);
        });

        openButton.addEventListener('click', openModal);
        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        saveButton.addEventListener('click', function () {
            const fee = calculateFee();

            hiddenWeight.value = modalWeight.value;
            hiddenWidth.value = modalWidth.value;
            hiddenLength.value = modalLength.value;
            hiddenHeight.value = modalHeight.value;
            hiddenFee.value = fee.toFixed(2);

            updateShippingSummary();
            closeModal();
        });

        updateShippingSummary();
        calculateFee();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mediaInput = document.querySelector('[data-product-media-input]');
        const previewGrid = document.querySelector('[data-product-media-preview]');
        const objectUrls = [];

        if (!mediaInput || !previewGrid || !window.URL?.createObjectURL) {
            return;
        }

        const revokeObjectUrls = () => {
            while (objectUrls.length > 0) {
                URL.revokeObjectURL(objectUrls.pop());
            }
        };

        const renderPreview = () => {
            revokeObjectUrls();
            const files = Array.from(mediaInput.files || []);

            previewGrid.innerHTML = '';
            previewGrid.hidden = files.length === 0;

            files.forEach((file) => {
                const previewUrl = URL.createObjectURL(file);
                objectUrls.push(previewUrl);

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
                meta.textContent = `${file.name} (${Math.ceil(file.size / 1024)} KB)`;

                card.appendChild(mediaWrap);
                card.appendChild(meta);
                previewGrid.appendChild(card);
            });
        };

        mediaInput.addEventListener('change', renderPreview);
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
