<?php

namespace App\Models;

use App\Models\Candidate;
use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recording extends Model
{
    protected $fillable = [
        'approved',
        'approved_by',
        'candidate_id',
        'file_type',
        'uploaded_by',
        'url',
        'version_id',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    protected function casts()
    {
        return [
            'approved' => 'datetime',
        ];
    }
}
