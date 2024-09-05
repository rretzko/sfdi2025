<?php

namespace App\Services;

use Illuminate\Support\Str;

class SplitNameIntoNamePartsService
{
    private const PREFIXES = [
        'Dr', 'Dr.', 'Md', 'Md.', 'Esq', 'Esq.', 'Hon', 'Hon.', 'Prof', 'Prof.', 'Professor',
        'Miss', 'Mr', 'Mr.', 'Mrs', 'Mrs.', 'Ms', 'Ms.', 'Mx', 'Mx.',
        'Rev', 'Rev.', 'Sr', 'Sr.'
    ];
    private const SUFFIXES = [
        'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VII', 'IX', 'X',
        'Jr', 'Jr.', 'Sr', 'Sr.',
        'Esq', 'Esq.', 'DDS', 'DVM', 'MD', 'MD.', 'PHD', 'PHD.'
    ];
    private array $nameParts =
        [
            'prefix_name' => '',
            'first_name' => '',
            'middle_name' => '',
            'last_name' => '',
            'suffix_name' => '',
        ];

    public function __construct(private readonly string $fullName)
    {
        $this->init();
    }

    public function getNameParts(): array
    {
        return $this->nameParts;
    }

    private function init(): void
    {
        $parts = explode(" ", $this->fullName);

        $matched = match (count($parts)) {
            1 => $this->nameParts['last_name'] = $this->fullName,
            2 => $this->firstAndLast($parts),
            default => $this->multipleParts($parts),
        };

        //placeholder for future development
//        $this->cleanUp();
    }

    private function firstAndLast(array $parts): bool
    {
        $this->nameParts['first_name'] = $parts[0];
        $this->nameParts['last_name'] = $parts[1];

        return true;
    }

//    private function cleanUp(): void
//    {
//        //remove comma from last name (ex. Last, III)
//        Str::remove(',', $this->nameParts['last_name']);
//    }

    private function multipleParts(array $parts): bool
    {
        //register the prefix if found and then remove it from the $parts array
        if ($this->partsHasPrefix($parts[0])) {
            array_shift($parts);
        }

        //register the suffix if found and then remove it from the $parts array
        if ($this->partsHasSuffix(end($parts))) {
            array_pop($parts);
        }

        if (count($parts) > 1) {

            $this->nameParts['first_name'] = array_shift($parts);
            $this->nameParts['last_name'] = array_pop($parts);

            //populate $this->parts['middle_name'] with the remainder of $parts, if any
            if (count($parts)) {
                $this->nameParts['middle_name'] = implode(" ", $parts);
            }

        } elseif (count($parts) === 1) { //unlikely but possible (ex. Cher, Beyonce)

            $this->nameParts['last_name'] = array_shift($parts);

        } else { //unlikely

            $this->nameParts['first_name'] = 'firstName';
            $this->nameParts['last_name'] = 'lastName';
            return false;
        }

        return true;
    }

    private function partsHasPrefix(string $prefix): bool
    {
        if (in_array($prefix, self::PREFIXES, true)) {
            $this->nameParts['prefix_name'] = $prefix;
            return true;
        }

        return false;
    }

    private function partsHasSuffix(string $suffix): bool
    {
        if (in_array($suffix, self::SUFFIXES, true)) {
            $this->nameParts['suffix_name'] = $suffix;
            return true;
        }

        return false;
    }


}
