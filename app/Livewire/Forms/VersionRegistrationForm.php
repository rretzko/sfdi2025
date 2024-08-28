<?php

namespace App\Livewire\Forms;

use App\Models\Candidate;
use App\Models\Student;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VersionRegistrationForm extends Form
{
    public Candidate $candidate;
    public string $programName = '';
    public int $versionId = 0;
    public int $voicePartId = 63;

    public function setVersion(int $versionId): void
    {
        $student = Student::where('user_id', auth()->id())->first();
        $this->versionId = $versionId;
        $this->candidate = Candidate::where('student_id', $student->id)
            ->where('version_id', $versionId)
            ->first();

        $this->programName = $this->candidate->program_name;
        $this->voicePartId = $this->candidate->voice_part_id;
    }
}
