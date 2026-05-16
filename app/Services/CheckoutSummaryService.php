<?php

namespace App\Services;

use Illuminate\Support\Collection;

class CheckoutSummaryService
{
    public function build(Collection $cartItems, ?Collection $deliveryEstimates = null, ?array $voucherApplication = null): array
    {
        $deliveryEstimates ??= collect();
        $voucherApplication ??= app(VoucherService::class)->emptyApplication();

        $groups = $cartItems
            ->filter(fn ($item) => $item->product && $item->product->user_id)
            ->groupBy(fn ($item) => (int) $item->product->user_id)
            ->sortKeys()
            ->map(function (Collection $sellerCartItems, int $sellerId) use ($deliveryEstimates) {
                $seller = $sellerCartItems->first()?->product?->user;
                $estimate = $deliveryEstimates->get($sellerId);
                $items = $sellerCartItems->map(fn ($item) => $this->itemSummary($item))->values();
                $subtotal = round((float) $items->sum('line_subtotal'), 2);
                $shipping = round((float) $items->sum('shipping_total'), 2);

                return [
                    'seller_id' => $sellerId,
                    'seller_name' => $seller?->sellerProfile?->store_name ?? $seller?->name ?? 'LocalLift Seller',
                    'item_count' => $sellerCartItems->count(),
                    'delivery_range' => $estimate['date_range'] ?? '3-5 days',
                    'subtotal' => $subtotal,
                    'shipping' => $shipping,
                    'shop_total' => round($subtotal + $shipping, 2),
                    'items' => $items,
                ];
            })
            ->values();

        $subtotal = round((float) $groups->sum('subtotal'), 2);
        $shippingFee = round((float) $groups->sum('shipping'), 2);
        $voucherDiscount = round(min((float) ($voucherApplication['discount'] ?? 0), $subtotal), 2);

        return [
            'groups' => $groups,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'voucher_code' => $voucherApplication['code'] ?? null,
            'voucher_label' => $voucherApplication['label'] ?? null,
            'voucher_discount' => $voucherDiscount,
            'total' => round(max(0, $subtotal + $shippingFee - $voucherDiscount), 2),
        ];
    }

    public function totals(Collection $cartItems): array
    {
        $summary = $this->build($cartItems);

        return [
            'subtotal' => $summary['subtotal'],
            'shippingFee' => $summary['shipping_fee'],
            'total' => $summary['subtotal'] + $summary['shipping_fee'],
        ];
    }

    protected function itemSummary($item): array
    {
        $variant = $item->variant;
        $product = $item->product;
        $quantity = (int) $item->quantity;
        $originalUnitPrice = (float) ($variant?->price ?? $product?->price ?? 0);
        $unitPrice = $product?->discountedPrice($originalUnitPrice) ?? $originalUnitPrice;
        $shippingFee = (float) ($product?->shipping_fee ?? 0);
        $productImage = $variant?->image ?: ($product?->image ?? null);

        return [
            'id' => $item->id,
            'product_name' => $product?->name ?? 'Product',
            'variant_name' => $variant?->displayName(),
            'quantity' => $quantity,
            'original_unit_price' => round($originalUnitPrice, 2),
            'unit_price' => round($unitPrice, 2),
            'has_discount' => (bool) ($product?->hasActiveDiscount() && $unitPrice < $originalUnitPrice),
            'line_subtotal' => round($unitPrice * $quantity, 2),
            'shipping_total' => round($shippingFee * $quantity, 2),
            'image_url' => $productImage ? asset('storage/' . $productImage) : asset('assets/images/default-product.png'),
        ];
    }
}
