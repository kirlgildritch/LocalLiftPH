<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
