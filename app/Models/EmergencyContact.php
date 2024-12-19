<?php

namespace App\Models;

use App\Models\Students\EmergencyContactType;
use App\Models\Students\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    protected $fillable = [
        'student_id',
        'emergency_contact_type_id',
        'name',
        'email',
        'phone_home',
        'phone_mobile',
        'phone_work',
        'best_phone',
    ];

//    public function bestPhone(): BelongsTo
//    {
//        return $this->belongsTo(\string::class, 'bestPhone');
//    }

    public function emergencyContactType(): BelongsTo
    {
        return $this->belongsTo(EmergencyContactType::class);
    }

    public function hasBestPhone(): bool
    {
        $bestPhone = $this->best_phone;
        $property = 'phone_' . $bestPhone;

        return (bool)strlen($this->$property);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

}
