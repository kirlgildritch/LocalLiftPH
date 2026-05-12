<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderReturnRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'order_id',
        'user_id',
        'seller_id',
        'reason',
        'preferred_resolution',
        'details',
        'status',
        'seller_response',
        'requested_at',
        'reviewed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(OrderReturnRequestMedia::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_COMPLETED => 'Resolved',
            default => 'Pending Review',
        };
    }

    public function toneClass(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'shipped',
            self::STATUS_REJECTED => 'cancelled',
            self::STATUS_COMPLETED => 'delivered',
            default => 'processing',
        };
    }
}
