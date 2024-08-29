<?php

namespace App\Services;

class ConvertToUsdService
{
    static public function penniesToUsd($pennies): float
    {
        return number_format(($pennies / 100), 2);
    }
}
