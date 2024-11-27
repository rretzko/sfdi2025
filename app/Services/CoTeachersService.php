<?php

namespace App\Services;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class CoTeachersService
{
    /**
     * @return array
     * @todo process for identifying co-teachers remains to be sussed out.
     */
    public static function getCoTeachersIds(): array
    {
        return [auth()->id()];
    }

    /**
     * @param  Teacher  $latestTeacher
     * @return array
     * @todo process for identifying co-teachers remains to be sussed out.
     */
    public static function getStudentCoTeachersIds(Teacher $latestTeacher, School $school): array
    {
        $schoolId = $school->id;
        $latestTeacherId = $latestTeacher->id;

        $coTeacherIds = DB::table('coteachers')
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($latestTeacherId) {
                $query->where('teacher_id', $latestTeacherId)
                    ->orWhere('coteacher_id', $latestTeacherId);
            })
            ->pluck('teacher_id', 'coteacher_id')
            ->flatten()
            ->unique()
            ->toArray();

        return array_merge($coTeacherIds, [$latestTeacherId]);
    }
}
