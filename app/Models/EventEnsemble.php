<?php

namespace App\Models;

use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventEnsemble extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'ensemble_name',
        'ensemble_short_name',
        'grades',
        'voice_part_ids',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function voiceParts(): Collection
    {
        $voicePartIds = explode(',', $this->voice_part_ids);

        return VoicePart::find($voicePartIds);
    }
}
