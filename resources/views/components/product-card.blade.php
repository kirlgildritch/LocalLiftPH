@props([
    'product',
    'href' => null,
    'subtitle' => null,
    'fallbackImage' => null,
    'cardClass' => '',
    'buyerLocation' => null,
]) 

@php
    $resolvedHref = $href ?: route('products.show', $product->id);
    $resolvedSubtitle = $subtitle ?? ($product->user->sellerProfile?->store_name ?? 'LocalLift Seller');
    $resolvedFallbackImage = $fallbackImage ?: asset('assets/images/default-product.png');
    $resolvedImage = $product->image ? asset('storage/' . $product->image) : $resolvedFallbackImage;
    $averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
    $sellerProfile = $product->user?->sellerProfile;
    $locationLabel = \App\Support\LocationBrowsing::matchLabel($sellerProfile, $buyerLocation);
@endphp

<a href="{{ $resolvedHref }}" class="market-product-card product-card-link skeleton-shell is-loading {{ $cardClass }}" data-skeleton-item data-skeleton-kind="product-card">
    <div class="market-product-card__image skeleton skeleton-image">
        <img src="{{ $resolvedImage }}" alt="{{ $product->name }}" loading="lazy" decoding="async">
        @if($locationLabel)
            <div class="market-product-card__location" title="{{ $locationLabel }}">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $locationLabel }}</span>
            </div>
        @endif
    </div>
    <div class="market-product-card__body">
        <span class="market-product-card__badge skeleton skeleton-text">{{ $product->category?->name ?? 'Uncategorized' }}</span>
    <h4 class="market-product-card__title skeleton skeleton-text" title="{{ $product->name }}">{{ $product->name }}</h4>

      <div class="ratings skeleton skeleton-text">
            <div class="review-stars-display" aria-label="Average rating: {{ $averageRating }} out of 5">
                        @for($star = 1; $star <= 5; $star++)
                            <i class="fa-{{ $averageRating >= $star ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
            </div>
            <div class="market-product-card__rating-value">
                <strong>{{ $averageRating > 0 ? number_format($averageRating, 1) : '0.0' }}</strong>                           
            </div>
      </div>

        @if(filled($resolvedSubtitle))
            <div class="market-product-card__seller-line skeleton skeleton-text">
                <span class="market-product-card__subtitle" title="{{ $resolvedSubtitle }}">
                    <i class="fa-solid fa-store"></i>&nbsp;{{ $resolvedSubtitle }}
                </span>
                <x-seller-trust-badge :seller="$sellerProfile" compact icon-only />
            </div>
        @endif

        @isset($meta)
            <div class="market-product-card__meta skeleton skeleton-text">{{ $meta }}</div>
        @endisset

        <div class="market-product-card__price skeleton skeleton-text">
            <span class="market-product-card__currency">&#8369;</span>
            {{ number_format($product->price, 2) }}
        </div>
    </div>
</a>
