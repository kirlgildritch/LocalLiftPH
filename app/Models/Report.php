<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';

    protected $fillable = [
        'user_id',
        'product_id',
        'seller_id',
        'reason',
        'message',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function actions()
    {
        return $this->hasMany(ReportAction::class)->latest('handled_at')->latest('id');
    }

    public function reasonLabel(): string
    {
        return match ($this->reason) {
            'spam' => 'Spam',
            'fake product' => 'Fake Product',
            'inappropriate' => 'Inappropriate',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->reason)),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_DISMISSED => 'Dismissed',
            default => 'Pending',
        };
    }

    public function targetLabel(): string
    {
        if ($this->product) {
            return $this->product->name;
        }

        if ($this->seller) {
            return $this->seller->sellerProfile?->store_name ?: $this->seller->name;
        }

        return 'Unavailable';
    }
}
