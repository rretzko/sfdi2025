<?php

namespace App\ValueObjects;

use App\Models\PhoneNumber;
use App\Models\User;

/**
 * Return a string composed of available phone numbers with abbreviated phone type as a comma-separated string
 * ex (123) 456-7890 (c), (888) 555-1212 x234 (w)
 */
class PhoneStringValueObject
{
    public static function getPhoneString(User $user): string
    {
        $phoneNumberStrings = [];

        $phoneNumbers = PhoneNumber::where('user_id', $user->id)
            ->whereNotNull('phone_number')
            ->orderBy('phone_type')
            ->get();

        foreach($phoneNumbers AS $phoneNumber){

            $abbr = ($phoneNumber->phone_type === 'mobile')
                ? ' (c)'
                : ' (' . substr($phoneNumber->phone_type,0,1) . ')';

            $phoneNumberStrings[] = $phoneNumber->phone_number . $abbr;
        }

        return implode(', ', $phoneNumberStrings);
    }
}
