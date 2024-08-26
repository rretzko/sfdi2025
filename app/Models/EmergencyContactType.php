<?php

namespace App\Models;

use App\Models\Pronoun;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContactType extends Model
{
    protected $fillable = [
        'relationship',
        'pronoun_id',
        'order_by',
    ];

    public function pronoun(): BelongsTo
    {
        return $this->belongsTo(Pronoun::class);
    }
}
