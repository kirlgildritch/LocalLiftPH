<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public const SHIPPING_COMPLETED = 'completed';
    public const SHIPPING_OUT_FOR_DELIVERY = 'out_for_delivery';
    public const SHIPPING_DELIVERED = 'delivered';
    public const SHIPPING_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_COD = 'cod';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_CANCELLED = 'cancelled';

    public const EARNING_PENDING = 'pending';
    public const EARNING_ON_HOLD = 'on_hold';
    public const EARNING_AVAILABLE = 'available';
    public const EARNING_PAID_OUT = 'paid_out';
    public const EARNING_REVERSED = 'reversed';

    protected $fillable = [
        'user_id',
        'seller_id',
        'checkout_group',
        'shipping_fee',
        'total_price',
        'status',
        'shipping_status',
        'payment_method',
        'payment_status',
        'paid_at',
        'seller_earning_status',
        'seller_released_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'seller_released_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
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
            self::SHIPPING_COMPLETED => [
                'label' => 'Completed',
                'icon' => 'fa-house-circle-check',
            ],
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pending',
            self::PAYMENT_PAID => 'Paid',
            self::PAYMENT_CANCELLED => 'Cancelled',
        ];
    }

    public static function earningStatuses(): array
    {
        return [
            self::EARNING_PENDING => 'Pending',
            self::EARNING_ON_HOLD => 'On Hold',
            self::EARNING_AVAILABLE => 'Available',
            self::EARNING_PAID_OUT => 'Paid Out',
            self::EARNING_REVERSED => 'Cancelled',
        ];
    }

    public function statusLabel(): string
    {
        return static::legacyStatusLabel($this->status);
    }

    public function shippingStatus(): string
    {
        $shippingStatus = $this->shipping_status ?: static::mapLegacyStatusToShipping($this->status);

        if (in_array($shippingStatus, [self::SHIPPING_OUT_FOR_DELIVERY, self::SHIPPING_DELIVERED], true)) {
            return self::SHIPPING_COMPLETED;
        }

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
            self::SHIPPING_SHIPPED => 'shipped',
            self::SHIPPING_COMPLETED => 'delivered',
            self::SHIPPING_CANCELLED => 'cancelled',
            default => 'processing',
        };
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method ?? self::PAYMENT_METHOD_COD) {
            self::PAYMENT_METHOD_COD => 'Cash on Delivery',
            default => strtoupper(str_replace('_', ' ', (string) $this->payment_method)),
        };
    }

    public function paymentStatusLabel(): string
    {
        return static::paymentStatuses()[$this->payment_status ?? self::PAYMENT_PENDING]
            ?? ucfirst(str_replace('_', ' ', (string) $this->payment_status));
    }

    public function paymentToneClass(): string
    {
        return match ($this->payment_status ?? self::PAYMENT_PENDING) {
            self::PAYMENT_PAID => 'delivered',
            self::PAYMENT_CANCELLED => 'cancelled',
            default => 'processing',
        };
    }

    public function earningStatusLabel(): string
    {
        return static::earningStatuses()[$this->seller_earning_status ?? self::EARNING_PENDING]
            ?? ucfirst(str_replace('_', ' ', (string) $this->seller_earning_status));
    }

    public function earningToneClass(): string
    {
        return match ($this->seller_earning_status ?? self::EARNING_PENDING) {
            self::EARNING_AVAILABLE, self::EARNING_PAID_OUT => 'delivered',
            self::EARNING_REVERSED => 'cancelled',
            self::EARNING_ON_HOLD => 'shipped',
            default => 'processing',
        };
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function isEarningAvailable(): bool
    {
        return $this->seller_earning_status === self::EARNING_AVAILABLE;
    }

    public function isCancelled(): bool
    {
        return $this->shippingStatus() === self::SHIPPING_CANCELLED;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->shippingStatus(), [self::SHIPPING_PENDING, self::SHIPPING_TO_SHIP], true)
            && !$this->isCancelled()
            && !$this->isCompleted();
    }

    public function isCompleted(): bool
    {
        return $this->shippingStatus() === self::SHIPPING_COMPLETED
            || $this->status === self::STATUS_COMPLETED;
    }

    public function canConfirmReceipt(): bool
    {
        return $this->shippingStatus() === self::SHIPPING_SHIPPED
            && !$this->isCancelled()
            && !$this->isCompleted();
    }

    public static function shippingFlow(): array
    {
        return [
            self::SHIPPING_PENDING => [self::SHIPPING_TO_SHIP, self::SHIPPING_CANCELLED],
            self::SHIPPING_TO_SHIP => [self::SHIPPING_SHIPPED, self::SHIPPING_CANCELLED],
            self::SHIPPING_SHIPPED => [self::SHIPPING_COMPLETED],
            self::SHIPPING_COMPLETED => [],
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

        if ($this->seller_id !== null) {
            return (int) $this->seller_id === $sellerId;
        }

        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->with('product')->get();

        return $items->isNotEmpty()
            && $items->every(fn (OrderItem $item) => (int) ($item->product?->user_id ?? 0) === $sellerId);
    }

    public static function mapLegacyStatusToShipping(?string $status): string
    {
        return match ($status) {
            self::STATUS_COMPLETED, self::STATUS_DELIVERED => self::SHIPPING_COMPLETED,
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
            self::SHIPPING_SHIPPED => self::STATUS_SHIPPED,
            self::SHIPPING_COMPLETED => self::STATUS_COMPLETED,
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
            self::STATUS_DELIVERED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Pending',
        };
    }

    public function checkoutGroupKey(): string
    {
        return $this->checkout_group ?: 'order-' . $this->getKey();
    }

    public function shopDisplayName(): string
    {
        if ($this->relationLoaded('seller') && $this->seller) {
            return $this->seller->sellerProfile?->store_name
                ?? $this->seller->name
                ?? 'LocalLift Seller';
        }

        $firstItem = $this->relationLoaded('items')
            ? $this->items->first()
            : $this->items()->with('product.user.sellerProfile')->first();

        return $firstItem?->product?->user?->sellerProfile?->store_name
            ?? $firstItem?->product?->user?->name
            ?? 'LocalLift Seller';
    }

    public function itemCount(): int
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->get();

        return (int) $items->sum('quantity');
    }

    public function subtotalAmount(): float
    {
        $items = $this->relationLoaded('items')
            ? $this->items
            : $this->items()->get();

        return (float) $items->sum(fn (OrderItem $item) => (float) $item->price * (int) $item->quantity);
    }

    public function cancellationReasonLines(): array
    {
        if (! $this->cancellation) {
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
