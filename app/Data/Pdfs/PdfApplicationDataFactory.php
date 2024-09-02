<?php

namespace App\Data\Pdfs;

use App\Models\Event;
use App\Models\Candidate;
use App\Models\Version;
use App\Models\VersionConfigDate;
use App\Models\Pronoun;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\VoicePart;
use App\Models\User;
use App\Services\CalcGradeFromClassOfService;
use App\Services\ConvertToUsdService;
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

    private function init()
    {
        $this->dto['applicationDeadline'] = $this->getApplicationDeadline();
        $this->dto['auditionFee'] = $this->getAuditionFee();
        $this->dto['auditionPeriod'] = $this->getAuditionPeriod();
        $this->dto['candidateVoicePartDescr'] = $this->getCandidateVoicePartDescr();
        $this->dto['emergencyContactName'] = $this->getEmergencyContact('name');
        $this->dto['emergencyContactMobile'] = $this->getEmergencyContact('phone_mobile');
        $this->dto['ensembleNames'] = $this->getEnsembleNames();
        $this->dto['first'] = $this->user->first_name;
        $this->dto['fullName'] = $this->candidate->program_name;
        $this->dto['fullNameAlpha'] = $this->user->fullNameAlpha;
        $this->dto['grade'] = $this->getGrade();
        $this->dto['logo'] = $this->getLogo();
        $this->dto['logoPdf'] = $this->getLogo();
        $this->dto['organizationName'] = $this->event->organization;
        $this->dto['participationFee'] = $this->getParticipationFee();
        $this->dto['postmarkDeadline'] = $this->getPostmarkDeadline();
        $this->dto['pronounObject'] = $this->getPronoun('object');
        $this->dto['pronounPersonal'] = $this->getPronoun('personal');
        $this->dto['pronounPossessive'] = $this->getPronoun('possessive');
        $this->dto['schoolName'] = $this->school->name;
        $this->dto['schoolShortName'] = $this->school->shortName;
        $this->dto['teacherFullName'] = $this->teacher->user->name;
        $this->dto['versionShortName'] = $this->version->short_name;
    }

    private function getApplicationDeadline(): string
    {
        return 'Friday, September 30';
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

    private function getGrade(): string
    {
        $classOf = $this->student->class_of;

        $service = new CalcGradeFromClassOfService();

        return $service->getGrade($classOf);
    }

    private function getLogo(): string
    {
        return 'logos/nj-mea-logo.jpg';
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

    public function getDto(): array
    {
        return $this->dto;
    }
}
