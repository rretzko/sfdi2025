<?php

namespace App\Models;

use App\Models\Ensembles\Asset;
use App\Models\Ensembles\Members\Member;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ensemble extends Model
{
    use hasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'short_name',
        'abbr',
        'description',
        'active',
    ];

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function countActiveMembers(): int
    {
        return Member::query()
            ->where('ensemble_id', $this->id)
            ->where('status', 'active')
            ->count('id');
    }

    public function countNonActiveMembers(): int
    {
        return Member::query()
            ->where('ensemble_id', $this->id)
            ->whereNot('status', 'active')
            ->count('id');
    }
}
