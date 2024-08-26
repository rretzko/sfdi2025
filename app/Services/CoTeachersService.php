<?php

namespace App\Services;

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
     * @return array
     * @todo process for identifying co-teachers remains to be sussed out.
     */
    public static function getStudentCoTeachersIds(array $teacherIds): array
    {
        $a = [];

        foreach($teacherIds AS $teacherId){

            $a[] = $teacherId;
        }

        return $a;
    }
}
