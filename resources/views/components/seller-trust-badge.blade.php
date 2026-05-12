@props([
    'seller' => null,
    'compact' => false,
    'iconOnly' => false,
])

@if($seller?->hasVerifiedSellerBadge())
    <span
        {{ $attributes->class([
            'seller-trust-badge',
            'seller-trust-badge--compact' => $compact,
            'seller-trust-badge--icon-only' => $iconOnly,
        ]) }}
        title="Verified seller"
        aria-label="Verified seller">
        <i class="fa-solid fa-circle-check"></i>
        @unless($iconOnly)
            <span>Verified Seller</span>
        @endunless
    </span>
@endif
