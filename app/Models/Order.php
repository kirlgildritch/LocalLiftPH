<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'total_price',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cancellation()
    {
        return $this->hasOne(OrderCancellation::class);
    }

    public static function progressStatuses(): array
    {
        return [
            self::STATUS_PENDING => [
                'label' => 'Pending',
                'icon' => 'fa-receipt',
            ],
            self::STATUS_CONFIRMED => [
                'label' => 'Confirmed',
                'icon' => 'fa-circle-check',
            ],
            self::STATUS_PROCESSING => [
                'label' => 'Processing',
                'icon' => 'fa-box-open',
            ],
            self::STATUS_SHIPPED => [
                'label' => 'Shipped',
                'icon' => 'fa-truck-fast',
            ],
            self::STATUS_DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'fa-house-circle-check',
            ],
        ];
    }

    public function statusLabel(): string
    {
        return static::progressStatuses()[$this->status]['label']
            ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PROCESSING,
        ], true);
    }

    public function cancellationReasonLines(): array
    {
        if (!$this->cancellation) {
            return [];
        }

        $reasons = collect($this->cancellation->reasons ?? [])
            ->filter()
            ->values()
            ->all();

        if (filled($this->cancellation->other_reason)) {
            $reasons[] = $this->cancellation->other_reason;
        }

        return $reasons;
    }
}
