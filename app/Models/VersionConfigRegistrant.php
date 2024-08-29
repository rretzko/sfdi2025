<?php

namespace App\Models;

use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersionConfigRegistrant extends Model
{
    protected $fillable = [
        'version_id',
        'eapplication',
        'audition_count',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }
}
