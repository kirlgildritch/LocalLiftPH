<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OrderReturnRequestMedia extends Model
{
    protected $fillable = [
        'order_return_request_id',
        'type',
        'path',
    ];

    public function returnRequest(): BelongsTo
    {
        return $this->belongsTo(OrderReturnRequest::class, 'order_return_request_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
