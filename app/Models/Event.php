<?php

namespace App\Models;

use App\Models\Ensemble;
use App\Models\EventEnsemble;
use App\Models\Version;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'audition_count',
        'created_by',
        'ensemble_count',
        'frequency',
        'grades',
        'logo_file',
        'logo_file_alt',
        'max_registrant_count',
        'max_upper_voice_count',
        'name',
        'organization',
        'required_height',
        'required_shirt_size',
        'short_name',
        'status',
        'user_id',
    ];

    public function eventEnsembles(): HasMany
    {
        return $this->hasMany(EventEnsemble::class);
    }

    /**
     * Return the event version with the most recent senior_class_of value
     * or a new Version object if none found
     * @return Version
     */
    public function getCurrentVersion(): Version
    {
        return Version::query()
            ->where('event_id', $this->id)
            ->orderByDesc('senior_class_of')
            ->first() ?? new Version();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Version::class)->orderByDesc('senior_class_of');
    }

    /**
     * Return collection of unique VoicePart objects from
     * EventEnsembles owned by the event
     * @return Collection
     */
    public function voiceParts(): Collection
    {
        $voiceParts = collect();

        foreach($this->eventEnsembles AS $ensemble){

            $voiceParts = $voiceParts->merge($ensemble->voiceParts())
                ->unique('id')
                ->sortBy('order_by');
        }

        return $voiceParts;
    }

    /**
     * Return collection of unique VoicePart objects from
     * EventEnsembles owned by the event
     * matching the grade of the user
     * @return Collection
     */
    public function voicePartsByGrade(int $grade): Collection
    {
        $voiceParts = collect();

        foreach($this->eventEnsembles AS $ensemble){
            $grades = explode(',', $ensemble->grades);
            if(in_array($grade, $grades)) {

                $voiceParts = $voiceParts->merge($ensemble->voiceParts())
                    ->unique('id')
                    ->sortBy('order_by');
            }
        }

        return $voiceParts;
    }

}
