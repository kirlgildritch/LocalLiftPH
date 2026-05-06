<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerDocumentRequest extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RESUBMITTED = 'resubmitted';
    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'seller_id',
        'reason',
        'admin_notes',
        'status',
        'requested_at',
        'responded_at',
        'resolved_at',
        'response_document_path',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
