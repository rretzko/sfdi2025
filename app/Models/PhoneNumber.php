<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhoneNumber extends Model
{
    protected $fillable = [
        'user_id',
        'phone_number',
        'phone_type', //enum: home, mobile, work
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
