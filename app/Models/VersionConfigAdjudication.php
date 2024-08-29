<?php

namespace App\Models;

use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersionConfigAdjudication extends Model
{

    protected $fillable = [
        'averaged_scores',
        'judge_per_room_count',
        'room_monitor',
        'scores_ascending',
        'show_all_scores',
        'upload_count',
        'upload_types',
        'version_id',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }
}
