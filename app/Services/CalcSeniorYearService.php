<?php

namespace App\Services;

class CalcSeniorYearService
{
    public function getSeniorYear(): int
    {
        $mth = (int) date('n');
        $yr = (int) date('Y');

        return ($mth < 7)
            ? $yr
            : ($yr + 1);
    }

    public function getSeniorYearsArray(): array
    {
        $seniorYear = $this->getSeniorYear();
        $firstGrade = ($seniorYear + 12);
        $yearOne = 1960;

        $a = [];

        for ($i = $firstGrade; $i > $yearOne; $i--) {

            $a[$i] = $i;
        }

        return $a;
    }
}
