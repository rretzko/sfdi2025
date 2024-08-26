<?php

namespace App\Models;

use App\Models\County;
use App\Models\Ensembles\Ensemble;
use App\Models\Geostate;
use App\Models\Schools\SchoolGrade;
use App\Models\Students\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'abbr',
        'name',
        'postal_code',
        'city',
        'county_id',
    ];

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function ensembles(): HasMany
    {
        return $this->hasMany(Ensemble::class);
    }

    public function getAddressAttribute(): string
    {
        return $this->city.' in '.$this->getCountyNameAttribute().', '.$this->postal_code;
    }

    public function getCountyNameAttribute(): string
    {
        return County::find($this->county_id)->name;
    }

    public function getGeostateAbbrAttribute(): string
    {
        return Geostate::find(County::find($this->county_id)->geostate_id)->abbr;
    }

    public function getGradesAttribute(): array
    {
        return SchoolGrade::query()
            ->where('school_id', $this->id)
            ->orderBy('grade')
            ->pluck('grade')
            ->toArray();
    }

    public function getShortNameAttribute(): string
    {
        $rhs = str_replace('Regional High School', 'RHS', $this->name);
        $rms = str_replace('Regional Middle School', 'RMS', $rhs);
        $shs = str_replace('Senior High School', 'Sr HS', $rms);
        $hs = str_replace('High School', 'HS', $shs);
        $ms = str_replace('Middle School', 'MS', $hs);
        $js1 = str_replace('Junior/Senior', 'J/S', $ms);
        $js2 = str_replace('Junior/senior', 'J/S', $js1);
        $es = str_replace('Elementary School', 'ES', $js2);

        return $es;
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
    }

    public function updateGrades(array $grades): void
    {
        //clear the table of $this grades
        DB::table('school_grades')
            ->where('school_id', $this->id)
            ->delete();

        foreach ($grades as $grade) {

            SchoolGrade::create(
                [
                    'school_id' => $this->id,
                    'grade' => $grade,
                ]
            );
        }
    }
}
