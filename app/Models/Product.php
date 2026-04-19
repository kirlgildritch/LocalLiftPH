<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Category;
use App\Models\User;
use App\Models\Cart;

class Product extends Model
{
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
}
