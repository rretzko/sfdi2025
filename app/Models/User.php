<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\SchoolTeacher;
use App\Services\FullNameAlphaService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAlphaAttribute(): string
    {
        return FullNameAlphaService::getName($this);
    }

    /**
     * Returns true if user is a teacher with at least one school with a verified email address
     * - $this->id is found in teachers' table
     * - $this->id is found in school_teacher's table
     * - row in school_teacher' table contains a work email address (email)
     * - row in school_teacher' table contains a verified work email address (email_verified_at)
     * @return bool
     */
    public function isTeacher(): bool
    {
        return SchoolTeacher::query()
            ->join('teachers', 'teachers.id', '=', 'school_teacher.teacher_id')
            ->where('teachers.user_id', '=', $this->id)
            ->exists();
    }
}
