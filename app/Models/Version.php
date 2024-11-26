<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Events\Versions\VersionParticipant;
use App\Models\Events\Versions\VersionPitchFile;
use App\Models\Events\Versions\VersionRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Version extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'epayment_student',
        'epayment_teacher',
        'event_id',
        'fee_participation',
        'fee_on_site_registration',
        'fee_registration',
        'height',
        'name',
        'participation_contract',
        'pitch_files_student',
        'pitch_files_teacher',
        'short_name',
        'senior_class_of',
        'shirt_size',
        'student_home_address',
        'status',
        'upload_type',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getVersionManager(): User
    {
        $versionRole = VersionRole::query()
            ->where('version_id', $this->id)
            ->where('role', 'event manager')
            ->orderBy('id')
            ->first();

        $versionParticipant = VersionParticipant::find($versionRole->version_participant_id);

        return User::find($versionParticipant->user_id);
    }

    public function showPitchFiles(string $type = ''): bool
    {
        return match ($type) {
            'student' => (bool) $this->pitch_files_student,
            'teacher' => (bool) $this->pitch_files_teacher,
            '' => (bool) ($this->pitch_files_student || $this->pitch_files_teacher),
        };
    }

    public function versionParticipants(): HasMany
    {
        return $this->hasMany(VersionParticipant::class);
    }

    public function versionPitchFiles(): HasMany
    {
        return $this->hasMany(VersionPitchFile::class)
            ->orderBy('order_by');
    }
}
