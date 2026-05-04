<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seller extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const SHOP_STATUS_OPEN = 'open';
    public const SHOP_STATUS_TEMPORARILY_CLOSED = 'temporarily_closed';
    public const SHOP_STATUS_VACATION = 'vacation';

    protected $fillable = [
        'user_id',
        'seller_type',
        'full_name',
        'age',
        'email',
        'store_name',
        'store_description',
        'contact_number',
        'address',
        'payout_method',
        'payout_account_name',
        'payout_account_number',
        'low_stock_threshold',
        'hide_out_of_stock',
        'shop_status',
        'shop_status_until',
        'shop_logo',
        'valid_id_type',
        'valid_id_number',
        'valid_id_path',
        'business_permit_path',
        'application_status',
        'review_notes',
        'submitted_at',
        'reviewed_at',
        'suspended_at',
        'suspension_reason',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'suspended_at' => 'datetime',
        'hide_out_of_stock' => 'boolean',
        'low_stock_threshold' => 'integer',
        'shop_status_until' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(SellerDocumentRequest::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(SellerPayout::class);
    }

    public function latestDocumentRequest(): HasOne
    {
        return $this->hasOne(SellerDocumentRequest::class)->latestOfMany('requested_at');
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function shopStatusLabel(): string
    {
        return match ($this->effectiveShopStatus()) {
            self::SHOP_STATUS_TEMPORARILY_CLOSED => 'Temporarily Closed',
            self::SHOP_STATUS_VACATION => 'Vacation',
            default => 'Open',
        };
    }

    public function normalizedShopStatus(): string
    {
        return match ($this->shop_status) {
            'paused' => self::SHOP_STATUS_TEMPORARILY_CLOSED,
            default => $this->shop_status ?: self::SHOP_STATUS_OPEN,
        };
    }

    public function temporaryClosureExpired(): bool
    {
        return $this->shop_status_until !== null
            && now()->toDateString() > $this->shop_status_until->toDateString();
    }

    public function effectiveShopStatus(): string
    {
        $status = $this->normalizedShopStatus();

        if ($status === self::SHOP_STATUS_TEMPORARILY_CLOSED && $this->temporaryClosureExpired()) {
            return self::SHOP_STATUS_OPEN;
        }

        return $status;
    }

    public function isVisibleToBuyers(): bool
    {
        return match ($this->effectiveShopStatus()) {
            self::SHOP_STATUS_OPEN => true,
            default => false,
        };
    }
}
