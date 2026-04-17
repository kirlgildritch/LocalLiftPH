<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Cart;
class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}