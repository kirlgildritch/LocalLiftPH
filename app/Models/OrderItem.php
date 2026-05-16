<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'variant_name',
        'variant_options',
        'quantity',
        'price',
        'shipping_fee',
    ];

    protected $casts = [
        'variant_options' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function variantDisplayName(): ?string
    {
        if (filled($this->variant_name)) {
            return $this->variant_name;
        }

        if ($this->relationLoaded('variant') && $this->variant) {
            return $this->variant->displayName();
        }

        $options = collect($this->variant_options ?? [])
            ->filter(fn ($value) => filled($value))
            ->map(fn ($value, $key) => is_string($key) ? "{$key}: {$value}" : (string) $value)
            ->values();

        return $options->isNotEmpty() ? $options->join(', ') : null;
    }

    public function purchaseDetailsLabel(bool $includeProduct = false): string
    {
        $details = collect();

        $product = $this->relationLoaded('product') ? $this->product : null;

        if ($includeProduct && $product) {
            $details->push($product->name);
        }

        if ($variantName = $this->variantDisplayName()) {
            $details->push('Option: ' . $variantName);
        }

        $details->push('Qty ' . max(1, (int) $this->quantity));
        $details->push('Paid ₱' . number_format((float) $this->price, 2) . ' each');

        return $details->join(' • ');
    }
}
