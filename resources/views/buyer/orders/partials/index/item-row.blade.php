@php
    $variant = $item->variant;
    $orderProductImage = $variant?->image ?: ($item->product->image ?? null);
    $variantLabel = $item->variant_name ?: $variant?->displayName();
@endphp

<div class="order-card-body">
    <img src="{{ $orderProductImage ? asset('storage/' . $orderProductImage) : asset('assets/images/default-product.png') }}"
        alt="{{ $item->product->name ?? 'Product' }}" class="order-product-img">

    <div class="order-product-info">
        <h3>{{ $item->product->name ?? 'Product no longer available' }}</h3>
        @if($variantLabel)
            <p>Option: {{ $variantLabel }}</p>
        @endif
        <p>Shop: {{ $order->shopDisplayName() }}</p>
        <p>Date: {{ $order->created_at->format('M d, Y') }}</p>
        <p>Payment: {{ $order->paymentMethodLabel() }} | {{ $order->paymentStatusLabel() }}</p>
        <p>Quantity: {{ $item->quantity }}</p>

        @if($order->shippingStatus() === \App\Models\Order::SHIPPING_COMPLETED && $item->product)
            <div class="order-item-actions">
                @if(!$item->review)
                    <a href="{{ route('products.show', $item->product) }}?review_order_item={{ $item->id }}#product-reviews"
                        class="order-btn secondary-btn">
                        Rate Product
                    </a>
                @else
                    <span class="order-btn secondary-btn is-static">Reviewed</span>
                @endif
            </div>
        @endif
    </div>

    <div class="order-product-price">
        P{{ number_format($item->price, 2) }}
    </div>
</div>
