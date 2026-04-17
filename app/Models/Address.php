<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'region',
        'province',
        'city',
        'barangay',
        'street_address',
        'postal_code',
        'label',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
