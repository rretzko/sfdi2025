<?php

namespace App\Services;

use App\Models\User;

class FullNameAlphaService
{
    public static function getName(User $user): string
    {
        if ($user->isTeacher()) {

            return self::getTeacherName($user);
        }

        return self::getStudentName($user);
    }

    private static function getTeacherName(User $user): string
    {
        $prefix = strlen($user->prefix_name) ? ' ('.$user->prefix_name.')' : '';
        $first = $user->first_name;
        $middle = strlen($user->middle_name) ? ' '.$user->middle_name : '';
        $last = $user->last_name;
        $suffix = strlen($user->suffix_name) ? ', '.$user->suffix_name : '';

        return $last.$suffix.', '.$first.$middle.$prefix;
    }

    private static function getStudentName(User $user): string
    {
        $first = $user->first_name;
        $middle = strlen($user->middle_name) ? ' '.$user->middle_name : '';
        $last = $user->last_name;
        $suffix = strlen($user->suffix_name) ? ', '.$user->suffix_name : '';

        return $last.$suffix.', '.$first.$middle;
    }
}
