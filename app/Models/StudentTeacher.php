<?php

namespace App\Models;

use App\Models\Schools\Teacher;
use App\Models\Students\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTeacher extends Model
{
    protected $table = 'student_teacher';

    protected $fillable = [
        'student_id',
        'teacher_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
