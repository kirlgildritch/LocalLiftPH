<?php
// app/Models/User.php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'google_id',
        'profile_image',
        'phone',
        'address',
        'is_seller',
        'is_admin',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_seller' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(Seller::class);
    }

    public function sellerVouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'seller_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(\App\Models\Address::class);
    }

    public function isSeller(): bool
    {
        return (bool) $this->is_seller;
    }

    public function isBuyer(): bool
    {
        return !$this->isSeller() && !$this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin || $this->role === 'admin';
    }

    public function scopeVisibleSellerShops(Builder $query): Builder
    {
        return $query
            ->where('is_seller', true)
            ->whereHas('sellerProfile', function (Builder $sellerQuery) {
                $sellerQuery->visibleToBuyers();
            });
    }

    public function carts(): HasMany
    {
        return $this->hasMany(\App\Models\Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(\App\Models\Wishlist::class);
    }

    public function followedShops(): HasMany
    {
        return $this->hasMany(\App\Models\ShopFollow::class);
    }

    public function shopFollowers(): HasMany
    {
        return $this->hasMany(\App\Models\ShopFollow::class, 'seller_user_id');
    }

    public function recentlyViewedProducts(): HasMany
    {
        return $this->hasMany(\App\Models\RecentlyViewedProduct::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function buyerConversations(): HasMany
    {
        return $this->hasMany(\App\Models\Conversation::class, 'buyer_id');
    }

    public function sellerConversations(): HasMany
    {
        return $this->hasMany(\App\Models\Conversation::class, 'seller_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(\App\Models\Message::class, 'sender_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(\App\Models\Report::class);
    }

    public function sellerReports(): HasMany
    {
        return $this->hasMany(\App\Models\Report::class, 'seller_id');
    }
}
