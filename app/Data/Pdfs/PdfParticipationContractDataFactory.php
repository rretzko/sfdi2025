<?php

namespace App\Data\Pdfs;

use App\Models\Candidate;
use App\Models\EmergencyContact;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Version;
use App\Models\VoicePart;
use App\Services\ConvertToUsdService;

class PdfParticipationContractDataFactory
{
    private array $dto=[];
    public function __construct(private readonly Version $version, private readonly Student $student)
    {
        $this->init();
    }

    public function getDto(): array
    {
        return $this->dto;
    }
/** END OF PUBLIC FUNCTIONS **************************************************/

    private function init()
    {
        $candidate = $this->getCandidate();
        $teacher = Teacher::find($candidate->teacher_id);

        $this->dto['versionName'] = $this->version->name;
        $this->dto['participationFee'] = ConvertToUsdService::penniesToUsd($this->version->fee_participation);
        $this->dto['studentName'] = $this->student->user->name;
        $this->dto['candidateVoicePartDescr'] = VoicePart::find($candidate->voice_part_id)->descr;
        $this->dto['emergencyContactName'] = EmergencyContact::find($candidate->emergency_contact_id)->name;
        $this->dto['teacherFullName'] = $teacher->user->name;
    }

    private function getCandidate(): Candidate
    {
        return Candidate::query()
            ->where('student_id', $this->student->id)
            ->where('version_id', $this->version->id)
            ->first();
    }
}
