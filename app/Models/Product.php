<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\OrderItem;

class Product extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'name',
        'category_id',
        'description',
        'price',
        'stock',
        'condition',
        'weight',
        'width_cm',
        'length_cm',
        'height_cm',
        'shipping_fee',
        'image',
        'is_active',
        'rejection_reason',
        'status', // pending, approved, rejected
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeVisibleToBuyers(Builder $query): Builder
    {
        return $query->approved()
            ->where('is_active', 1)
            ->whereHas('user.sellerProfile', function (Builder $sellerQuery) {
                $sellerQuery
                    ->where('application_status', Seller::STATUS_APPROVED)
                    ->whereNull('suspended_at');
            });
    }
    public function scopeWithRatings($query)
    {
        return $query->withAvg('reviews', 'rating')
            ->withCount('reviews');
    }
}
