<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'address1',
        'address2',
        'city',
        'geostate_id',
        'postal_code',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function geostate(): BelongsTo
    {
        return $this->belongsTo(Geostate::class);
    }

    public function getGeostateAbbrAttribute()
    {
        return $this->geostate->abbr;
    }

}
