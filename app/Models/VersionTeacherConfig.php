<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VersionTeacherConfig extends Model
{
    protected $fillable = [
        'epayment_student',
        'teacher_id',
        'version_id',
    ];
}
