<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'option_values',
        'sku',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'option_values' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function displayName(): string
    {
        return $this->name ?: collect($this->option_values ?? [])->filter()->implode(' / ');
    }
}
