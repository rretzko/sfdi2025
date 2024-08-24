<?php

namespace App\Models;

use App\Models\Schools\GradesITeach;
use App\Models\Schools\School;
use App\Models\Schools\SchoolTeacher;
use App\Models\Students\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
    ];

    public function getGradesITeachArray(School $school): array
    {
        return GradesITeach::query()
            ->where('school_id', $school->id)
            ->where('teacher_id', $this->id)
            ->orderBy('grade')
            ->pluck('grade')
            ->toArray();
    }

    public function getGradesITeachCsv(School $school): string
    {
        return implode(', ', $this->getGradesITeachArray($school));
//        return implode(', ', GradesITeach::query()
//            ->where('school_id', $school->id)
//            ->where('teacher_id', $this->id)
//            ->pluck('grade')
//            ->toArray());
    }

    public function getSubjects(School $school): array
    {
        return DB::table('teacherSubjects')
            ->where('teacher_id', $this->id)
            ->where('school_id', $school->id)
            ->pluck('subject', 'id')
            ->toArray();

    }

    public function isVerified(): bool
    {
        return SchoolTeacher::query()
            ->join('teachers', 'teachers.id', '=', 'school_teacher.teacher_id')
            ->where('teachers.user_id', '=', $this->id)
            ->whereNotNull('school_teacher.email')
            ->whereNotNull('school_teacher.email_verified_at')
            ->exists();
    }

    public function schools(): BelongsToMany|null
    {
        return $this->belongsToMany(School::class)
            ->withPivot('active');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
