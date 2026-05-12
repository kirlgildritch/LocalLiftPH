<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'product_id',
        'message',
        'image_path',
        'video_path',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'video_url',
        'media_url',
        'media_type',
        'has_image',
        'has_video',
        'has_media',
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_path ? asset('storage/' . $this->video_path) : null;
    }

    public function getHasImageAttribute(): bool
    {
        return ! empty($this->image_path);
    }

    public function getHasVideoAttribute(): bool
    {
        return ! empty($this->video_path);
    }

    public function getHasMediaAttribute(): bool
    {
        return $this->has_image || $this->has_video;
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->image_url ?: $this->video_url;
    }

    public function getMediaTypeAttribute(): ?string
    {
        if ($this->has_image) {
            return 'image';
        }

        if ($this->has_video) {
            return 'video';
        }

        return null;
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
