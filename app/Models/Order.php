<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const SHIPPING_PENDING = 'pending';
    public const SHIPPING_TO_SHIP = 'to_ship';
    public const SHIPPING_SHIPPED = 'shipped';
    public const SHIPPING_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const SHIPPING_DELIVERED = 'delivered';
    public const SHIPPING_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'shipping_fee',
        'total_price',
        'status',
        'shipping_status',
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
            self::SHIPPING_PENDING => [
                'label' => 'Pending',
                'icon' => 'fa-hourglass-half',
            ],
            self::SHIPPING_TO_SHIP => [
                'label' => 'To Ship',
                'icon' => 'fa-receipt',
            ],
            self::SHIPPING_SHIPPED => [
                'label' => 'Shipped',
                'icon' => 'fa-box-open',
            ],
            self::SHIPPING_OUT_FOR_DELIVERY => [
                'label' => 'Out for Delivery',
                'icon' => 'fa-truck-fast',
            ],
            self::SHIPPING_DELIVERED => [
                'label' => 'Delivered',
                'icon' => 'fa-house-circle-check',
            ],
        ];
    }

    public function statusLabel(): string
    {
        return static::legacyStatusLabel($this->status);
    }

    public function shippingStatus(): string
    {
        $shippingStatus = $this->shipping_status ?: static::mapLegacyStatusToShipping($this->status);

        return $shippingStatus ?: self::SHIPPING_PENDING;
    }

    public function shippingStatusLabel(): string
    {
        return static::progressStatuses()[$this->shippingStatus()]['label']
            ?? ucfirst(str_replace('_', ' ', $this->shippingStatus()));
    }

    public function shippingToneClass(): string
    {
        return match ($this->shippingStatus()) {
            self::SHIPPING_PENDING, self::SHIPPING_TO_SHIP => 'processing',
            self::SHIPPING_SHIPPED, self::SHIPPING_OUT_FOR_DELIVERY => 'shipped',
            self::SHIPPING_DELIVERED => 'delivered',
            self::SHIPPING_CANCELLED => 'cancelled',
            default => 'processing',
        };
    }

    public function isCancelled(): bool
    {
        return $this->shippingStatus() === self::SHIPPING_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return $this->shippingStatus() === self::SHIPPING_PENDING
            && !$this->isCancelled()
            && !$this->isCompleted();
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canConfirmReceipt(): bool
    {
        return $this->shippingStatus() === self::SHIPPING_DELIVERED
            && !$this->isCancelled()
            && !$this->isCompleted();
    }

    public static function shippingFlow(): array
    {
        return [
            self::SHIPPING_PENDING => [self::SHIPPING_TO_SHIP, self::SHIPPING_CANCELLED],
            self::SHIPPING_TO_SHIP => [self::SHIPPING_SHIPPED, self::SHIPPING_CANCELLED],
            self::SHIPPING_SHIPPED => [self::SHIPPING_OUT_FOR_DELIVERY, self::SHIPPING_CANCELLED],
            self::SHIPPING_OUT_FOR_DELIVERY => [self::SHIPPING_DELIVERED, self::SHIPPING_CANCELLED],
            self::SHIPPING_DELIVERED => [],
            self::SHIPPING_CANCELLED => [],
        ];
    }

    public function nextShippingStatuses(): array
    {
        return static::shippingFlow()[$this->shippingStatus()] ?? [];
    }

    public function canTransitionShippingTo(string $status): bool
    {
        return in_array($status, $this->nextShippingStatuses(), true);
    }

    public function sellerOwnsAllItems(User|int $seller): bool
    {
        $sellerId = $seller instanceof User ? $seller->getKey() : (int) $seller;

        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        return $items->isNotEmpty()
            && $items->every(fn (OrderItem $item) => (int) ($item->product?->user_id ?? 0) === $sellerId);
    }

    public static function mapLegacyStatusToShipping(?string $status): string
    {
        return match ($status) {
            self::STATUS_COMPLETED, self::STATUS_DELIVERED => self::SHIPPING_DELIVERED,
            self::STATUS_CONFIRMED, self::STATUS_PROCESSING => self::SHIPPING_TO_SHIP,
            self::STATUS_SHIPPED => self::SHIPPING_SHIPPED,
            self::STATUS_CANCELLED => self::SHIPPING_CANCELLED,
            default => self::SHIPPING_PENDING,
        };
    }

    public static function legacyStatusForShipping(string $shippingStatus): string
    {
        return match ($shippingStatus) {
            self::SHIPPING_PENDING => self::STATUS_PENDING,
            self::SHIPPING_TO_SHIP => self::STATUS_PROCESSING,
            self::SHIPPING_SHIPPED, self::SHIPPING_OUT_FOR_DELIVERY => self::STATUS_SHIPPED,
            self::SHIPPING_DELIVERED => self::STATUS_DELIVERED,
            self::SHIPPING_CANCELLED => self::STATUS_CANCELLED,
            default => self::STATUS_PENDING,
        };
    }

    public static function legacyStatusLabel(?string $status): string
    {
        return match ($status) {
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Pending',
        };
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
