<?php

namespace App\Services;

class CalcGradeFromClassOfService
{
    public function getGrade(int $classOf)
    {
        $service = new CalcSeniorYearService();
        $srYear = $service->getSeniorYear();

        return (12 - ($classOf - $srYear));
    }
}
