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

        <div class="categories-hero panel">
            <div class="hero-copy">
                <span class="section-kicker">Browse</span>
                <h1>Explore curated categories</h1>
                <p>
                    Browse product groups designed to feel clearer, more premium, and easier to scan
                    across every screen size.
                </p>
            </div>

            <div class="hero-summary">
                <div class="summary-card">
                    <strong>{{ $categories->count() }}</strong>
                    <span>Category groups available</span>
                </div>
                <div class="summary-card">
                    <strong>{{ $categories->sum('count') }}</strong>
                    <span>Products distributed across the catalog</span>
                </div>
            </div>
        </div>

        <div class="categories-toolbar panel">
            <div class="toolbar-copy">
                <span class="toolbar-label">Catalog</span>
                <h2>All Categories</h2>
            </div>

            <p class="toolbar-text">Clean category cards with the same layout language as the rest of the site.</p>
        </div>

        <div class="categories-grid">
            @foreach($categories as $category)
                <a href="#" class="category-card panel">
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
