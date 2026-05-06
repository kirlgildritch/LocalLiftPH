<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_item_id',
        'rating',
        'comment',
        'image_path',
        'video_path',
        'seller_reply',
        'seller_replied_at',
    ];

    protected $casts = [
        'seller_replied_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Review $review) {
            $review->media()->get()->each(function (ReviewMedia $media) {
                Storage::disk('public')->delete($media->path);
            });

            foreach ([$review->image_path, $review->video_path] as $path) {
                if ($path) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ReviewMedia::class)->orderBy('sort_order');
    }
}
