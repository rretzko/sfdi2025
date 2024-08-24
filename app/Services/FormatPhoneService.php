<?php

namespace App\Services;

class FormatPhoneService
{
    public function getPhoneNumber(string $raw): string
    {
        return $this->formatPhoneNumber($raw);
    }

    private function formatPhoneNumber(string $raw): string
    {
        $numerals = $this->stripNonNumericCharacters($raw);

        //early exit
        if (strlen($numerals) < 10) {
            return '';
        }

        $mainNumber = substr($numerals, 0, 10);
        $extension = substr($numerals, 10);

        $formattedExtension = $extension ? 'x'.$extension : '';

        //format (###) ###-#### x###
        return sprintf(
            '(%s) %s-%s %s',
            substr($mainNumber, 0, 3),
            substr($mainNumber, 3, 3),
            substr($mainNumber, 6, 4),
            $formattedExtension
        );
    }

    private function stripNonNumericCharacters(string $raw): string
    {
        return preg_replace('/\D+/', '', $raw);
    }
}
