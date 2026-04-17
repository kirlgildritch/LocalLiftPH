@extends('layouts.seller')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/add_products.css') }}">

    <div class="add-product-page">
        <div class="container">
            <div class="add-product-card">
                <h1 class="add-product-title">Add Product</h1>
                <hr class="section-line">

                <form class="product-form" action="{{ route('seller.products.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="name">Product Name:</label>
                        <input type="text" id="name" name="name" class="form-control medium-input"
                            value="{{ old('name') }}">
                        @error('name')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description"
                            class="form-textarea">{{ old('description') }}</textarea>
                        @error('description')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" id="price" name="price" class="form-control short-input" placeholder="₱"
                            step="0.01" min="0" value="{{ old('price') }}">
                        @error('price')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select name="category_id" id="category_id" class="form-control medium-input">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="stock">Stock Quantity:</label>
                        <input type="number" id="stock" name="stock" class="form-control short-input" min="0"
                            value="{{ old('stock') }}">
                        @error('stock')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Image:</label>
                        <div class="file-upload-wrap">
                            <label for="image" class="file-upload-btn">Upload Photo</label>
                            <input type="file" id="image" name="image" hidden>
                            <span class="file-note">No file chosen</span>
                        </div>
                        @error('image')
                            <small class="error-text">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="submit-wrap">
                        <button type="submit" class="submit-btn">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
