<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'image_path',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'has_image',
        'has_text',
        'is_seen',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getHasImageAttribute(): bool
    {
        return ! empty($this->image_path);
    }

    public function getHasTextAttribute(): bool
    {
        return filled($this->message);
    }

    public function getIsSeenAttribute(): bool
    {
        return ! is_null($this->read_at);
    }
}
