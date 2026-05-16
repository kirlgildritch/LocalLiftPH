<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    public const TYPE_FIXED = 'fixed';
    public const TYPE_PERCENT = 'percent';

    protected $fillable = [
        'seller_id',
        'code',
        'name',
        'type',
        'value',
        'minimum_subtotal',
        'maximum_discount',
        'usage_limit',
        'per_user_limit',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_subtotal' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function redemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function isSellerOwned(): bool
    {
        return $this->seller_id !== null;
    }

    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = strtoupper(trim((string) $value));
    }

    public function scopeCode(Builder $query, string $code): Builder
    {
        return $query->where('code', strtoupper(trim($code)));
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
