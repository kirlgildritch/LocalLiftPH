@props([
    'product',
    'href' => null,
    'subtitle' => null,
    'fallbackImage' => null,
    'cardClass' => '',
])

@php
$resolvedHref = $href ?: route('products.show', $product->id);
$resolvedSubtitle = $subtitle ?? ($product->user->name ?? 'LocalLift Seller');
$resolvedFallbackImage = $fallbackImage ?: asset('assets/images/default-product.png');
$resolvedImage = $product->image ? asset('storage/' . $product->image) : $resolvedFallbackImage;
@endphp

<a href="{{ $resolvedHref }}" class="market-product-card product-card-link {{ $cardClass }}">
    <div class="market-product-card__image">
    <img src="{{ $resolvedImage }}" alt="{{ $product->name }}">
    </div>
    <div class="market-product-card__body">
        <span class="market-product-card__badge">{{ $product->category?->name ?? 'Uncategorized' }}</span>
    <h4 class="market-product-card__title" title="{{ $product->name }}">{{ $product->name }}</h4>
        @if(filled($resolvedSubtitle))
            <p class="market-product-card__subtitle"><i class="fa-solid fa-store"></i>&nbsp;{{ $resolvedSubtitle }}</p>
        @endif

        @isset($meta)
            <div class="market-product-card__meta">{{ $meta }}</div>
        @endisset

        <div class="market-product-card__price">P{{ number_format($product->price, 2) }}</div>
    </div>
</a>
