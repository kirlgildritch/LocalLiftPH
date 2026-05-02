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
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(SellerDocumentRequest::class);
    }

    public function latestDocumentRequest(): HasOne
    {
        return $this->hasOne(SellerDocumentRequest::class)->latestOfMany('requested_at');
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }
}
