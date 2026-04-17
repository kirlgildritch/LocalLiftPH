@extends('layouts.seller')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/manage_products.css') }}">

<section class="dashboard-wrapper">
    <div class="container">
        <div class="dashboard-layout">

            <!-- SIDEBAR -->
           @include('seller.partials.sidebar')

            <!-- MAIN CONTENT -->
            <main class="dashboard-main">
                <div class="products-header">
                    <h2>My Products</h2>
                    <a href="{{ url('/add-product') }}" class="add-product-btn">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </a>
                </div>

                <div class="divider"></div>

                @if(session('success'))
                    <p class="success-message">{{ session('success') }}</p>
                @endif

                <div class="products-table-wrapper">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Image</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category?->name ?? 'Uncategorized' }}</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                                        @else
                                            <span class="no-image">No Image</span>
                                        @endif
                                    </td>
                                    <td class="action-buttons">
                                        <a href="{{ url('/edit-product/' . $product->id) }}" class="edit-btn">Edit</a>

                                        <form action="{{ url('/delete-product/' . $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-text">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </main>

        </div>
    </div>
</section>
@endsection
