@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/add_products.css') }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

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
                            enctype="multipart/form-data">
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

                                <div class="form-group">
                                    <label for="image">Product Image</label>
                                    <input type="file" id="image" name="image">
                                    @error('image')
                                        <small class="error-text">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group form-group-wide">
                                    <label>Shipping Setup</label>
                                    <div class="shipping-summary">
                                        <div class="shipping-summary-copy">
                                            <strong id="shippingSummaryFee">Shipping fee not set</strong>
                                            <span id="shippingSummaryMeta">Add package size and weight to calculate
                                                shipping.</span>
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
                                <button type="submit" class="page-action-btn">Add Product</button>
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
        document.addEventListener('DOMContentLoaded', function() {
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
            form.addEventListener('submit', function() {
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
                summaryMeta.textContent = `${weight} kg • ${width}cm x ${length}cm x ${height}cm`;
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

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            saveButton.addEventListener('click', function() {
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
@endsection
