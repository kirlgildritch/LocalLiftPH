@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/categories_page.css') }}">

    <section class="categories-page">
        <div class="container">
            <div class="checkout-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>&gt;</span>
                <span>Categories</span>
            </div>

            <div class="categories-toolbar panel">
                <div class="toolbar-copy">
                    <span class="toolbar-label">Catalog</span>
                    <h2>All Categories</h2>
                </div>


            </div>

            <div class="categories-grid">
                @foreach($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="category-card panel">
                        <div class="category-icon">
                            <i class="fa-solid {{ $category->icon }}"></i>
                        </div>
                        <div class="category-copy">
                            <span class="category-badge">Category</span>
                            <h3>{{ $category->name }}</h3>
                            <p>{{ $category->count }} products ready to browse</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection