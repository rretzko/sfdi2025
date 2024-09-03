<?php

namespace App\Models;

use App\Models\Version;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpaymentCredentials extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'version_id',
        'epayment_id',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Version::class, 'event_id');
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }
}
