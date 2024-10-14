<?php

namespace App\Data\Pdfs;

use App\Models\Address;
use App\Models\Event;
use App\Models\Candidate;
use App\Models\Version;
use App\Models\VersionConfigDate;
use App\Models\Geostate;
use App\Models\PhoneNumber;
use App\Models\Pronoun;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\VoicePart;
use App\Models\User;
use App\Services\CalcGradeFromClassOfService;
use App\Services\ConvertToUsdService;
use App\ValueObjects\AddressValueObject;
use App\ValueObjects\PhoneStringValueObject;
use Illuminate\Support\Carbon;

class PdfApplicationDataFactory
{
    private array $dto = [];
    private Event $event;
    private School $school;
    private Student $student;
    private Teacher $teacher;
    private Version $version;
    private User $user;

    public function __construct(private readonly Candidate $candidate)
    {
        $this->school = School::find($this->candidate->school_id);
        $this->student = Student::find($this->candidate->student_id);
        $this->teacher = Teacher::find($this->candidate->teacher_id);
        $this->user = User::find($this->student->user_id);
        $this->version = Version::find($candidate->version_id);
        $this->event = Event::find($this->version->event_id);

        $this->init();
    }
    public function getDto(): array
    {
        return $this->dto;
    }

    private function init()
    {
        $this->dto['addressString'] = $this->getAddressString();
        $this->dto['auditionFee'] = $this->getAuditionFee();
        $this->dto['auditionPeriod'] = $this->getAuditionPeriod();
        $this->dto['candidateVoicepartDescr'] = $this->getCandidateVoicePartDescr(); //synonym to candidateVoicePartDescr below
        $this->dto['candidateVoicePartDescr'] = $this->getCandidateVoicePartDescr();
        $this->dto['closeApplicationDateFormatted'] = $this->getStudentCloseDate();
        $this->dto['email'] = $this->student->user->email;
        $this->dto['emergencyContact'] = $this->getEmergencyContact('name'); //synonym to emergencyContactName below
        $this->dto['emergencyContactName'] = $this->getEmergencyContact('name');
        $this->dto['emergencyContactMobile'] = $this->getEmergencyContact('phone_mobile');
        $this->dto['ensembleNames'] = $this->getEnsembleNames();
        $this->dto['first'] = $this->user->first_name;
        $this->dto['footInch'] = $this->getFootInch();
        $this->dto['fullName'] = $this->candidate->program_name;
        $this->dto['fullNameAlpha'] = $this->user->fullNameAlpha;
        $this->dto['grade'] = $this->getGrade();
        $this->dto['height'] = $this->student->height;
        $this->dto['logo'] = $this->getLogo();
        $this->dto['logoPdf'] = $this->getLogo();
        $this->dto['organizationName'] = $this->event->organization;
        $this->dto['participationFee'] = $this->getParticipationFee();
        $this->dto['phoneMobile'] = $this->getStudentPhone('mobile'); //synonym to studentPhoneMobile below
        $this->dto['postmarkDeadline'] = $this->getPostmarkDeadline();
        $this->dto['pronounDescr'] = $this->getPronoun('descr');
        $this->dto['pronounObject'] = $this->getPronoun('object');
        $this->dto['pronounPersonal'] = $this->getPronoun('personal');
        $this->dto['pronounPossessive'] = $this->getPronoun('possessive');
        $this->dto['schoolName'] = $this->school->name;
        $this->dto['schoolShortName'] = $this->school->shortName;
        $this->dto['studentPhoneHome'] = $this->getStudentPhone('home');
        $this->dto['studentPhoneMobile'] = $this->getStudentPhone('mobile');
        $this->dto['teacherEmail'] = $this->teacher->user->email;
        $this->dto['teacherFullName'] = $this->teacher->user->name;
        $this->dto['teacherPhoneBlock'] = $this->getTeacherPhoneBlock();
        $this->dto['versionName'] = $this->version->name;
        $this->dto['versionShortName'] = $this->version->short_name;
    }

    private function getAddressString(): string
    {
        $address = Address::where('user_id', $this->student->user_id)->first();

        //early exit
        if(! $address){ return ''; }

        return AddressValueObject::getStringVo($address);
    }

    private function getAuditionFee(): string
    {
        $fee = $this->version->fee_registration;

        return ConvertToUsdService::penniesToUsd($fee);
    }

    private function getAuditionPeriod(): string
    {
        $vcds = VersionConfigDate::where('version_id', $this->version->id)->get();

        $openDate = $vcds->where('date_type', 'adjudication_open')
            ->first()
            ->version_date;

        $openMd = ($openDate)
            ? Carbon::parse($openDate)->format('F j')
            : '*** Open Date Not Found ***';

        $closeDate = $vcds->where('date_type', 'adjudication_close')
            ->first()
            ->version_date;

        $closeMd = ($closeDate)
            ? Carbon::parse($closeDate)->format('F j')
            : '*** Close Date Not Found ***';

        return $openMd.' - '.$closeMd;
    }

    private function getCandidateVoicePartDescr(): string
    {
        return VoicePart::find($this->candidate->voice_part_id)
            ->descr;
    }

    private function getEmergencyContact(string $property): string
    {
        $emergencyContacts = $this->student->emergencyContacts ?? collect();

        if ($emergencyContacts->count()) {
            return $emergencyContacts->first()->$property;
        }

        return 'none found';
    }

    private function getEnsembleNames(): array
    {
        return $this->event->eventEnsembles->pluck('name')->toArray();
    }

    private function getFootInch(): string
    {
        $height = $this->student->height;
        $foot = floor($height / 12);
        $inch = ($height % 12);

        return $foot . "' " . $inch . '"';

    }

    private function getGrade(): string
    {
        $classOf = $this->student->class_of;

        $service = new CalcGradeFromClassOfService();

        return $service->getGrade($classOf);
    }

    private function getLogo(): string
    {
        return match($this->version->event->organization){
            'CJMEA' => 'logos/cjmea-logo.jpg',
            'NJMEA' =>'logos/nj-mea-logo.jpg',
            default =>'',
        };
    }

    private function getParticipationFee(): string
    {
        $fee = $this->version->fee_participation;

        return ConvertToUsdService::penniesToUsd($fee);
    }

    private function getPostmarkDeadline(): string
    {
        $postmarkDeadlineDate = VersionConfigDate::query()
            ->where('version_id', $this->version->id)
            ->where('date_type', 'postmark_deadline')
            ->first();

        return ($postmarkDeadlineDate)
            ? Carbon::parse($postmarkDeadlineDate->version_date)->format('F j, Y')
            : '*** Postmark Deadline Not Found ***';
    }

    private function getPronoun(string $column): string
    {
        return Pronoun::find($this->user->pronoun_id)->$column;
    }

    private function getStudentCloseDate(): string
    {
        $studentCloseDate = VersionConfigDate::query()
            ->where('version_id', $this->version->id)
            ->where('date_type', 'student_close')
            ->first()
            ->version_date;

        return Carbon::parse($studentCloseDate)->format('l, F jS');
    }

    private function getStudentPhone(string $type): string
    {
        return  PhoneNumber::query()
            ->where('user_id', $this->student->user_id)
            ->where('phone_type', $type)
            ->first()
            ->phone_number ?? '';
    }

    private function getTeacherPhoneBlock(): string
    {
        return PhoneStringValueObject::getPhoneString($this->teacher->user);
    }
}
