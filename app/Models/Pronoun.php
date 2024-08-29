<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pronoun extends Model
{
    protected $fillable = [
        'descr',
        'intensive',
        'personal',
        'possessive',
        'object',
        'order_by',
    ];

    public function intensive(): BelongsTo
    {
        return $this->belongsTo(\string::class, 'intensive');
    }
}
