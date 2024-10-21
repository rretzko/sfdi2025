<?php

namespace App\Livewire\Forms;

use App\Models\Address;
use App\Models\Candidate;
use App\Models\EmergencyContact;
use App\Models\EpaymentCredentials;
use App\Models\PhoneNumber;
use App\Models\Pronoun;
use App\Models\Recording;
use App\Models\School;
use App\Models\Signature;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Version;
use App\Models\VersionConfigAdjudication;
use App\Models\VersionConfigDate;
use App\Models\VersionConfigRegistrant;
use App\Models\VersionPitchFile;
use App\Models\VersionTeacherConfig;
use App\Models\VoicePart;
use App\Services\CalcGradeFromClassOfService;
use App\Services\ConvertToUsdService;
use App\ValueObjects\AddressValueObject;
use App\ValueObjects\PhoneStringValueObject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;
use Livewire\Form;

class VersionRegistrationForm extends Form
{
    public string $address1 = '';
    public string $address2 = '';
    public string $addressString = '';
    public array $applicationErrors = [];
    public Candidate $candidate;
    public int $candidateId = 0;
    public string $candidateVoicePartDescr = '';
    public string $city = '';
    public string $closeApplicationDateFormatted = '';
    public bool $eapplication = false;
    public string $email = '';
    public int $emergencyContactId = 0;
    public string $emergencyContactName = '';
    public string $emergencyContactString = '';
    public string $emergencyContactBestPhone = 'missing';
    public bool $ePay = true;
    public string $ePayVendor = 'none';
    public string $ePaymentId = '';
    public float $feeAudition = 0.00;
    public float $feeParticipation = 0.00;
    public array $fileUploads = [];
    public string $footInch = '';
    public int $geostateId = 37;
    public int $grade = 4;
    public bool $hasPitchFiles = true;
    public int $height = 36;
    public string $phoneHome = '';
    public string $phoneMobile = '';
    public array $pitchFiles = [];
    public string $postalCode = '';
    public string $programName = '';
    public string $pronounDescr = '';
    public string $pronounPossessive = '';
    public array $recordings = []; //store Recording object details [fileType][url/approved datetime] = value
    public bool $requiresHomeAddress = false;
    public string $schoolName = '';
    public bool $signatureGuardian = false;
    public bool $signatureStudent = false;
    public string $signedAtGuardian = '';
    public string $signedAtStudent = '';
    public Student $student;
    public string $studentFullName = '';
    public string $teacherEmail = '';
    public string $teacherFullName = '';
    public string $teacherPhoneBlock = '';
    public string $uploadType = 'none';
    public bool $uploadTypesCount = false;
    public Version $version;
    #[Validate('required|int|exists:versions,id')]
    public int $versionId = 0;
    public string $versionName = '';
    #[Validate('required|int|exists:voice_parts,id')]
    public int $voicePartId = 63;
    public array $voiceParts = [];

    public function recordingReject(string $fileType): bool
    {
        $recording = Recording::query()
            ->where('candidate_id', $this->candidateId)
            ->where('version_id', $this->versionId)
            ->where('file_type', $fileType)
            ->first();

        $deleted = $recording->delete();

        $this->setRecordingsArray();

        return $deleted;
    }

    public function recordingSave(string $fileType): bool
    {
        return (bool)Recording::create(
            [
                'version_id' => $this->versionId,
                'candidate_id' => $this->candidateId,
                'file_type' => $fileType,
                'uploaded_by' => auth()->id(),
                'url' => $this->recordings[$fileType]['url'],
            ]
        );
    }

    public function setVersion(int $versionId): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();
        $this->studentFullName = $this->student->user->name;
        $this->versionId = $versionId;
        $this->version = Version::find($versionId);
        $this->versionName = $this->version->name;
        $this->eapplication = VersionConfigRegistrant::where('version_id', $this->versionId)->first()->eapplication;
        $vca =VersionConfigAdjudication::where('version_id', $this->versionId)->first();
        $this->uploadType = $this->version->upload_type;
        $this->uploadTypesCount = $vca->upload_count;
        $this->feeAudition = ConvertToUsdService::penniesToUsd($this->version->fee_registration);
        $this->feeParticipation = ConvertToUsdService::penniesToUsd($this->version->fee_participation);
        $this->email = $this->student->user->email;
        $this->fileUploads = explode(',',$vca->upload_types);
        $gradeService = new CalcGradeFromClassOfService();
        $this->grade = $gradeService->getGrade($this->student->class_of);
        $this->height = $this->student->height;
        $this->footInch = $this->getFootInch();
        $this->phoneHome = $this->getStudentPhone('home');
        $this->phoneMobile = $this->getStudentPhone('mobile');
        $this->requiresHomeAddress = $this->version->student_home_address;

        $this->voiceParts = $this->getVoiceParts();

        //candidate
        $this->candidate = Candidate::where('student_id', $this->student->id)
            ->where('version_id', $versionId)
            ->first();
        $this->candidateId = $this->candidate->id;
        $this->candidateVoicePartDescr = VoicePart::find($this->candidate->voice_part_id)->descr;
        $this->emergencyContactId = $this->candidate->emergency_contact_id;
        $this->emergencyContactString = $this->getEmergencyContactString();
        $this->emergencyContactName = EmergencyContact::find($this->emergencyContactId)->name ?? '';
        $this->programName = $this->candidate->program_name;
        $pronoun = Pronoun::find($this->candidate->student->user->pronoun_id);
        $this->pronounDescr = $pronoun->descr;
        $this->pronounPossessive = $pronoun->possessive;

        $this->schoolName = School::find($this->candidate->school_id)->name;
        $teacher = Teacher::find($this->candidate->teacher_id);
        $teacherUser = $teacher->user;
        $this->teacherEmail = $teacherUser->email;
        $this->teacherFullName = $teacherUser->name;
        $this->teacherPhoneBlock = PhoneStringValueObject::getPhoneString($teacherUser);
        $this->voicePartId = array_key_exists($this->candidate->voice_part_id, $this->voiceParts)
            ? $this->candidate->voice_part_id
            : 0;

        $this->closeApplicationDateFormatted = $this->getStudentCloseDate();

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
        $this->ePaymentId = $this->getEpaymentId();
        $this->setEpayment();
        $this->ePayVendor = $this->version->epayment_vendor;

        //address string
        $address = Address::where('user_id', $this->student->user_id)->first();
        $this->addressString = $address ? AddressValueObject::getStringVo($address) : '';

        //signatures
        $this->signatureGuardian = $this->getSignature('guardian');
        $this->signedAtGuardian = $this->getSignedAt('guardian');
        $this->signatureStudent = $this->getSignature('student');
        $this->signedAtStudent = $this->getSignedAt('student');
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

    public function updateSignatureGuardian(): bool
    {
        $signed = Signature::updateOrCreate(
            [
                'candidate_id' => $this->candidateId,
                'user_id' => auth()->id(),
                'version_id' => $this->versionId,
                'role' => 'guardian',
            ],
            [
                'signed' => $this->signatureGuardian,
            ]
        );

        $this->signedAtGuardian = ($this->signatureGuardian)
            ? $this->getSignedAt('guardian')
            : '';

        return (bool)$signed;
    }

    public function updateSignatureStudent(): bool
    {
        $signed = Signature::updateOrCreate(
            [
                'candidate_id' => $this->candidateId,
                'user_id' => auth()->id(),
                'version_id' => $this->versionId,
                'role' => 'student',
            ],
            [
                'signed' => $this->signatureStudent,

            ]
        );

        $this->signedAtStudent = ($this->signatureStudent)
            ? $this->getSignedAt('student')
            : '';

        return (bool)$signed;
    }

    public function updateVoicePartId(): bool
    {
        $updated =$this->candidate->update(['voice_part_id' => $this->voicePartId]);

        if($updated){
            $this->setPitchFiles();
        }

        return $updated;
    }

    private function getEmergencyContactString(): string
    {
        $ec = EmergencyContact::find($this->emergencyContactId) ?? new EmergencyContact();
        $bestPhone = $ec->best_phone;
        $bestPhoneNumber = $bestPhone . '_phone';
        $str = $ec->name;
        $str .= (strlen($ec->email)) ? ', ' . $ec->email : '';
        $str .= ", <span class='font-semibold'>" . $ec->$bestPhoneNumber . ' (' . substr($bestPhone,0,1) . ')</span>';

        return $str;
    }

    private function getEpaymentId(): string
    {
        $eventId = $this->version->event->id;

        // Check for version credentials first
        $versionCredential = EpaymentCredentials::where('version_id', $this->versionId)->first();
        if ($versionCredential) {
            return $versionCredential->epayment_id;
        }

        // Check for event credentials if version credentials do not exist
        $eventCredential = EpaymentCredentials::where('event_id', $eventId)->first();
        if ($eventCredential) {
            return $eventCredential->epayment_id;
        }

        // Return empty string if neither exists
        return '';
    }

    private function getFootInch(): string
    {
        $foot = floor($this->height / 12);
        $inch = ($this->height % 12);

        return $foot . "' " . $inch . '"';
    }

    private function getSignature(string $role): bool
    {
        return (bool)Signature::query()
            ->where('candidate_id', $this->candidateId)
            ->where('version_id', $this->versionId)
            ->where('role', $role)
            ->value('signed') ?? false;
    }

    private function getSignedAt(string $role): string
    {
        $property = 'signature' . ucwords($role);

        //early exit
        if(! $this->$property){ return '';}

        $dt = Signature::query()
            ->where('candidate_id', $this->candidateId)
            ->where('version_id', $this->versionId)
            ->where('role', $role)
            ->value('updated_at') ?? '';

        $fDate = Carbon::parse($dt)->format('l, F j, Y @ g:m:s a');

        return '(signed at: ' . $fDate . ')';
    }

    private function getStudentPhone(string $phoneType): string
    {
        return PhoneNumber::query()
            ->where('user_id', $this->student->user_id)
            ->where('phone_type', $phoneType)
            ->first()
            ->phone_number ?? '';
    }

    private function getStudentCloseDate(): string
    {
        $dateType = 'student_close';

        $studentCloseDate = DB::table('version_config_dates')
            ->where('version_id', $this->versionId)
            ->where('date_type', $dateType)
            ->value('version_date');

        return Carbon::parse($studentCloseDate)->format('l, F dS');
    }

    private function getVoiceParts(): array
    {
        $version = Version::find($this->versionId);
        $event = $version->event;
        $service = new CalcGradeFromClassOfService();
        $grade = $service->getGrade($this->student->class_of);

        return $event->voicePartsByGrade($grade)
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
