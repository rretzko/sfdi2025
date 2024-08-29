<?php

namespace App\Models;

use App\Models\Application;
use App\Models\Version;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{

    protected $fillable = [
        'id',
        'emergency_contact_id',
        'program_name',
        'ref',
        'school_id',
        'student_id',
        'status',
        'teacher_id',
        'version_id',
        'voice_part_id',
    ];

    public function addApplicationDownloadCount(): void
    {
        $application = Application::firstOrNew(
            [
                'candidate_id' => $this->id,
                'version_id' => $this->version_id,
            ]
        );

        if ($application->exists) {
            $application->increment('downloads');
        } else {
            $application->downloads = 1;
            $application->last_downloaded_at = Carbon::now();
            $application->save();
        }
    }

    public function hasDownloadedApplication(): bool
    {
        return (bool) Application::query()
            ->where('candidate_id', $this->id)
            ->exists();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function version(): BelongsTo
    {
        return $this->belongsTo(Version::class);
    }


}
