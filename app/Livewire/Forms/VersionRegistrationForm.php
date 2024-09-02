<?php

namespace App\Livewire\Forms;

use App\Models\Address;
use App\Models\Candidate;
use App\Models\EmergencyContact;
use App\Models\Recording;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Version;
use App\Models\VersionConfigAdjudication;
use App\Models\VersionConfigRegistrant;
use App\Models\VersionPitchFile;
use App\Models\VersionTeacherConfig;
use App\Models\VoicePart;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VersionRegistrationForm extends Form
{
    public string $address1 = '';
    public string $address2 = '';
    public Candidate $candidate;
    public int $candidateId = 0;
    public string $city = '';
    public bool $eapplication = false;
    public int $emergencyContactId = 0;
    public string $emergencyContactBestPhone = 'missing';
    public bool $ePay = true;
    public array $fileUploads = [];
    public int $geostateId = 37;
    public bool $hasPitchFiles = true;
    public array $pitchFiles = [];
    public string $postalCode = '';
    public string $programName = '';
    public array $recordings = []; //store Recording object details [fileType][url/approved datetime] = value
    public Student $student;
    public bool $uploadTypesCount = false;
    public Version $version;
    #[Validate('required|int|exists:versions,id')]
    public int $versionId = 0;
    #[Validate('required|int|exists:voice_parts,id')]
    public int $voicePartId = 63;
    public array $voiceParts = [];

    public function setVersion(int $versionId): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();
        $this->versionId = $versionId;
        $this->version = Version::find($versionId);
        $this->eapplication = VersionConfigRegistrant::where('version_id', $this->versionId)->first()->eapplication;
        $vca =VersionConfigAdjudication::where('version_id', $this->versionId)->first();
        $this->uploadTypesCount = $vca->upload_count;
        $this->fileUploads = explode(',',$vca->upload_types);

        $this->voiceParts = $this->getVoiceParts();

        //candidate
        $this->candidate = Candidate::where('student_id', $this->student->id)
            ->where('version_id', $versionId)
            ->first();
        $this->candidateId = $this->candidate->id;
        $this->emergencyContactId = $this->candidate->emergency_contact_id;
        $this->programName = $this->candidate->program_name;
        $this->voicePartId = array_key_exists($this->candidate->voice_part_id, $this->voiceParts)
            ? $this->candidate->voice_part_id
            : 0;

        //address
        $address = Address::where('user_id', $this->student->user_id)->first() ?? new Address();
        $this->address1 =  $address->address1 ?? '';
        $this->address2 = $address->address2 ?? '';
        $this->city = $address->city ?? '';
        $this->geostateId = $address->geostate_id ?? 37;
        $this->postalCode = $address->postal_code ?? '';

        //emergency contact
        if($this->emergencyContactId){
            $this->emergencyContactBestPhone = EmergencyContact::find($this->emergencyContactId)->best_phone ?? 'missing';
        }

        //recordings
        $this->setRecordingsArray();

        //pitch files
        $this->setPitchFiles();

        //ePayment
        $this->setEpayment();
    }

    public function updateAddress1()
    {
        return $this->updateAddress();
    }

    public function updateAddress2()
    {
        return $this->updateAddress();
    }

    public function updateCity()
    {
        return $this->updateAddress();
    }

    public function updateEmergencyContactId()
    {
        $this->emergencyContactBestPhone = EmergencyContact::find($this->emergencyContactId)->best_phone ?? 'missing';
        return $this->candidate->update(['emergency_contact_id' => $this->emergencyContactId]);
    }

    public function updateGeostateId()
    {
        return $this->updateAddress();
    }

    public function updatePostalCode()
    {
        return $this->updateAddress();
    }

    public function updateProgramName()
    {
        return $this->candidate->update(['program_name' => $this->programName]);
    }

    public function updateVoicePartId()
    {
        $updated =$this->candidate->update(['voice_part_id' => $this->voicePartId]);

        if($updated){
            $this->setPitchFiles();
        }

        return $updated;
    }

    private function getVoiceParts(): array
    {
        $version = Version::find($this->versionId);
        $event = $version->event;
        $ensembles = $event->eventEnsembles;

        return $event->voiceParts()
            ->pluck('descr', 'id')
            ->toArray();
    }

    /**
     * Return true if version allows ePayments by the student
     * AND if the teacher has approved epayments by their students for the current version
     * @return void
     */
    private function setEpayment(): void
    {
        $this->ePay = VersionTeacherConfig::where('teacher_id', $this->candidate->teacher_id)
            ->where('version_id', $this->versionId)
            ->first()
            ->epayment_student ?? false;
    }

    private function setPitchFiles(): void
    {
        $all = VoicePart::where('descr', 'all')->first()->id;

        $this->pitchFiles = VersionPitchFile::where('version_id', $this->versionId)
            ->where(function ($query) use ($all){
                $query->where('voice_part_id', $this->candidate->voice_part_id)
                    ->orWhere('voice_part_id', $all);
            })
            ->orderBy('order_by')
            ->get()
            ->toArray();

        $this->hasPitchFiles = (bool)count($this->pitchFiles);
    }

    private function setRecordingsArray(): void
    {
        //clear artifacts
        $this->recordings = [];

        $recordings = Recording::query()
            ->where('candidate_id', $this->candidate->id)
            ->where('version_id', $this->candidate->version_id)
            ->get();

        foreach ($recordings as $recording) {

            $this->recordings[$recording->file_type]['url'] = $recording->url;
            $this->recordings[$recording->file_type]['approved'] =
                ($recording->approved)
                    ? Carbon::parse($recording->approved)->format('D, M j, y g:i a')
                    : '';
        }
    }

    private function updateAddress()
    {
        return Address::updateOrCreate(
            [
                'user_id' => $this->student->user_id,
            ],
            [
                'address1' => $this->address1,
                'address2' => $this->address2,
                'city' => $this->city,
                'geostate_id' => $this->geostateId,
                'postal_code' => $this->postalCode,
            ]
        );

    }
}
