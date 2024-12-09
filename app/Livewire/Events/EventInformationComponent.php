<?php

namespace App\Livewire\Events;

use App\Livewire\Forms\VersionRegistrationForm;
use App\Models\Address;
use App\Models\Candidate;
use App\Models\EmergencyContact;
use App\Models\Epayment;
use App\Models\EpaymentCredentials;
use App\Models\Event;
use App\Models\Geostate;
use App\Models\Recording;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTeacher;
use App\Models\Teacher;
use App\Models\Version;
use App\Models\VersionConfigRegistrant;
use App\Models\VoicePart;
use App\Services\CalcGradeFromClassOfService;
use App\Services\ConvertToUsdService;
use App\Services\CoTeachersService;
use App\Services\FindStudentOpenRehearsalsService;
use App\Services\FindTeacherOpenEventsService;
use App\Services\MakeCandidateRecordsService;
use App\Services\PathToRegistrationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class EventInformationComponent extends Component
{
    use WithFileUploads;

    public VersionRegistrationForm $form;
    public array $auditionFiles = [];
    public array $coTeacherIds = [];
    public string $defaultVoicePartDescr = '';
    public array $eligibleVersions = [];
    public array $emergencyContacts = [];
    public array $events = []; //synonym for versions
    public string $eventsCsv = 'No events found.';
    public array $geostates = [];
    public int $grade = 4;
    public Teacher $latestTeacher;
    public string $logo = '';
    public array $participationContracts = [];
    public array $programNames = [];
    public array $rehearsals = [];
    public bool $requiresHomeAddress = false;

    public School $school;
    public string $schoolAndTeacherMissingMessage = 'Use the "School" tab above to select a school and teacher before continuing.';
    public string $schoolName = '';
    public array $showForms = [];
    public string $squareId = '';

    public Student $student;
    public int $studentId = 0;
    public string $teachersCsv = '';
    public int $teacherId = 0;
    public array $voiceParts = [];

    //participationFees
    public float $participationFee = 0.00;

    //epayment
    public float $amountDue = 0.00;
    public string $customProperties = '';
    public string $email = '';
    public string $epaymentId = '';
    public float $feePaid = 0.00;
public bool $sandbox = false; //false;
    public string $sandboxId = 'sb-qw0iu20847075@business.example.com'; //sandbox account
    public string $sandboxPersonalEmail = 'sb-ndsz820837854@personal.example.com'; //dRkJ4(f)
    public string $teacherName = '';
    public int $versionId = 0;
    public string $versionShortName = '';

    //SQUARE
    public string $firstName = '';
    public string $lastName = '';
    public string $phone = '';
    public array $addressLines = [];
    public string $city = '';
    public string $geostateAbbr = 'NJ';

    public function mount()
    {
        $gradeService = new CalcGradeFromClassOfService();

        $this->student = Student::where('user_id', auth()->id())->first();
        $this->studentId = $this->student->id;
        $this->emergencyContacts = $this->getEmergencyContacts();

        $this->defaultVoicePartDescr = VoicePart::find($this->student->voice_part_id)->descr;
        $this->grade = $gradeService->getGrade($this->student->class_of);

        $this->school = $this->student->activeSchool();
        $this->schoolName = $this->school->name ?? '' ;

        $this->teacherId = $this->getTeacherId();
        if($this->teacherId) {
            $this->latestTeacher = Teacher::find($this->teacherId);
        }
        $this->teachersCsv = $this->getTeachersCsv();

        /**
         * @todo refactor $this->events to $this->versions
         * @todo isolate the instantiation of $this->events
         */
        $this->eventsCsv = $this->getEventsCsv(); //also sets $this->events
        $this->voiceParts = $this->getVoiceParts(); //of event ensemble(s) voice parts
        $this->requiresHomeAddress = $this->getRequiresHomeAddress();

        $this->geostates = Geostate::orderBy('name')->pluck('name', 'id')->toArray();

        //ePayment
        $this->setEpaymentVars();

        //square ID
        $this->squareId = $this->getSquareId();

        //rehearsals
        $this->rehearsals = $this->setRehearsals();

        //participation contracts
        $this->participationContracts = $this->setParticipationContracts();

    }

    public function render()
    {
        return view('livewire.events.event-information-component',
        [
            'applicationErrors' => $this->setApplicationErrors(),
        ]);
    }

    public function clickDownloadContract(int $versionId)
    {
        return $this->redirect("pdf/participationContracts/$versionId/$this->studentId" );
    }

    public function downloadApp()
    {
        return $this->redirect('pdf/application/' . $this->form->candidate->id);
    }

    public function recordingReject(string $fileType): void
    {
//        $this->reset('showSuccessIndicator', 'successMessage');

        $url = $this->form->recordings[$fileType]['url'];

        //if the db record has been deleted, delete the s3 storage file
        if ($this->form->recordingReject($fileType)) {

            //delete the file from s3 storage
            Storage::disk('s3')->delete($url);
        }
    }

    public function setVersion(int $versionId): void
    {
        $this->form->setVersion($versionId);

        //reset epayment variables to conform to the new event version
        $this->setEpaymentVars();

        //set all values to false
        $this->showForms = array_map(function(){
            return false;
        }, $this->showForms);

        //set the selected $versionId key to true
        $this->showForms[$versionId] = true;
    }

    public function updated($property)
    {
        if(substr($property, 0, 5) === 'form.'){

            //remove 'form.' from $property
            $formProperty = substr($property,5);

            //prepend 'update' and capitalize $formProperty to create the method name
            $method = 'update' . ucwords($formProperty);

            //if successful update, dispatch notice for $user
            if($this->form->$method()){
                $target = Str::kebab($formProperty) . '-updated';
                $this->dispatch($target, true);
            }

        }

    }

    public function updatedAuditionFiles($value, $key): void
    {
        $fileName = $this->makeFileName($key);

        $this->auditionFiles[$key]->storePubliclyAs('recordings', $fileName, 's3');

        //store the url reference for saving
        $this->form->recordings[$key]['url'] = 'recordings/'.$fileName;

        $this->form->recordingSave($key);

    }

    private function getAmountDue(): float
    {
        //early exit
        if(! isset($this->form->version)){
            return 0.00;
        }

        $feeRegistration = $this->form->version->fee_registration;
        $feePaid = $this->getFeePaid();

        $amountDueInPennies = ($feeRegistration - $feePaid);

        return ConvertToUsdService::penniesToUsd($amountDueInPennies);
    }

    private function getCandidateid(int $versionId): int|null
    {
        return Candidate::query()
            ->where('version_id', $versionId)
            ->where('student_id', $this->studentId)
            ->value('id');
    }

    private function getCustomProperties(string $feeType='registration'): string
    {
        $separator = ' | ';

        $properties = [];

        if(isset($this->form->candidate)) {

            $properties = [
                (string) $this->form->candidate->student->user_id,
                (string) $this->form->versionId,
                (string) $this->form->candidate->school_id,
                (string) $this->amountDue,
                (string) $this->form->candidateId,
                (string) $feeType,
                auth()->user()->name, //additional identification info
            ];
        }

        return implode($separator, $properties);
    }

    private function getCustomRehearsalProperties(int $versionId, int $amountDue): string
    {
        $separator = ' | ';

        $candidateId = $this->getCandidateId($versionId);
        $candidate = Candidate::find($candidateId);
        $feeType = 'participation';

        $properties = [
            (string) $candidate->student->user_id,
            (string) $versionId,
            (string) $candidate->school_id,
            (string) $amountDue,
            (string) $candidateId,
            (string) $feeType,
            auth()->user()->name, //additional identification info
        ];


        return implode($separator, $properties);
    }

    private function getEmergencyContacts(): array
    {
        $a = [];

        foreach($this->student->emergencyContacts AS $emergencyContact){

            $a[] = [
                'id' => $emergencyContact->id,
                'name' => $emergencyContact->name,
                'bestPhone' => $emergencyContact->hasBestPhone() ? $emergencyContact->best_phone : 'missing',
            ];
        }

        return $a;
    }

    private function getEpaymentId(): string
    {
        $ePaymentCredentials = EpaymentCredentials::query()
            ->where('version_id', $this->form->versionId)
            ->first();

        if (!$ePaymentCredentials && isset($this->form->version)) {

            $ePaymentCredentials = EpaymentCredentials::query()
                ->where('event_id', $this->form->version->event_id)
                ->first();
        }

        return ($ePaymentCredentials)
            ? $ePaymentCredentials->epayment_id
            : '';
    }

    private function getEventsCsv(): string
    {
        //ensure that events are available
        $this->setEvents();

        //early exit
        if(is_null($this->events) || (! count($this->events))) {
            return ($this->school->id)
                ? 'No events found. Please see your teacher if you expected to find open events.'
                : $this->schoolAndTeacherMissingMessage;
        }

        //isolate version names into an array
        $names = array_column($this->events, 'name');

        //ensure that each event has a candidate row (if eligible)
        $this->setCandidates();

        //hide/display registration forms
        $this->setShowForms();

        return implode(' | ', $names);
    }

    private function getFeePaid(): float
    {
        return Epayment::query()
            ->where('candidate_id', $this->form->candidateId)
            ->where('version_id' , $this->form->versionId)
            ->sum('amount');
    }

    private function getParticipationFeePaid(int $versionId): float
    {
        $candidateId = $this->getCandidateId($versionId);

        return DB::table('epayments')
            ->where('version_id', $versionId)
            ->where('candidate_id', $candidateId)
            ->where('fee_type', 'participation')
            ->sum('amount');
    }

    private function getRehearsalEnsembleName(int $versionId): string
    {
        $eventId = Version::find($versionId)->event_id;

        return DB::table('audition_results')
            ->join('candidates', 'candidates.id', '=', 'audition_results.candidate_id')
            ->join('versions', 'audition_results.version_id', '=', 'versions.id')
            ->join('events', 'events.id', '=', 'versions.event_id')
            ->join('event_ensembles', 'event_ensembles.abbr', '=', 'audition_results.acceptance_abbr')
            ->where('audition_results.version_id', $versionId)
            ->where('audition_results.accepted', 1)
            ->where('candidates.student_id', $this->studentId)
            ->where('event_ensembles.event_id', $eventId)
            ->value('event_ensembles.ensemble_name');
    }

    private function getRequiresHomeAddress(): bool
    {
        $version = Version::find($this->form->versionId) ?? new Version();

        return (bool)! $version->student_home_address ?? 0;
    }

    private function getSquareId(): string
    {
        $candidatedId = Candidate::query()
            ->where('version_id', $this->versionId)
            ->where('student_id', auth()->id())
            ->value('id');

        return substr($candidatedId, 2);
    }

    private function getTeachersCsv(): string
    {
        if(! isset($this->latestTeacher)){
            return '';
        }

        $this->coTeacherIds = CoTeachersService::getStudentCoTeachersIds($this->latestTeacher, $this->school);

        if(count($this->coTeacherIds) === 1){
            return '';
        }

        $teacherNames = [];
        $teachers = Teacher::find($this->coTeacherIds);

        //isolate the teacher names from the $teachers collection
        foreach($teachers AS $teacher){
            $teacherNames[] = $teacher->user->name;
        }

        return implode(', ', $teacherNames);
    }

    /**
     * Use the last teacher identified with $user student
     * @return int
     */
    private function getTeacherId(): int
    {
        return StudentTeacher::query()
            ->where('student_id', $this->studentId)
            ->latest('updated_at')
            ->first()
            ->teacher_id ?? 0;
    }

    private function getVoiceParts(): array
    {
        $version = Version::find($this->form->versionId);
        $event = $version ? $version->event : new Event();
        $service = new CalcGradeFromClassOfService();
        $grade = $service->getGrade($this->student->class_of);

        return $event->voiceParts()
            ->pluck('descr', 'id')
            ->toArray();
    }

    private function makeFileName(string $uploadType): string
    {
        //ex: 661234_scales.mp3
        $fileName = $this->form->candidateId;
        $fileName .= '_';
        $fileName .= $uploadType;
        $fileName .= '.';
        $fileName .= pathInfo($this->auditionFiles[$uploadType]->getClientOriginalName(), PATHINFO_EXTENSION);

        return $fileName;
    }

    private function setApplicationErrors(): array
    {
        //clear artifacts
        $a = [];

//      MISSING VOICE PART
        if(! $this->form->voicePartId){
            $a[] = 'You must select and save a voice part before you can complete your application.';
        }

//      MISSING EMERGENCY CONTACT
        if(! $this->form->emergencyContactId){
            $a[] = 'You must select and save an emergency contact before you can complete your application.';
        }

//      MISSING EMERGENCY CONTACT BEST PHONE --}}
        $ec = $this->form->emergencyContactId ? EmergencyContact::find($this->form->emergencyContactId) : new EmergencyContact();
        $bestPhoneType = $ec ? $ec->best_phone : 'mobile';
        $property = 'phone_' . $bestPhoneType;
        $phoneNumber = $ec ? $ec->$property : '';
        if(! $phoneNumber){
            $a[] = "Your emergency contact must have a 'best phone' selected before you can complete your application.";
        }

//      MISSING HOME ADDRESS
        if($this->form->requiresHomeAddress && (! $this->form->addressString)){
            $a[] = 'Your home address is required by the event.';
        }

        return $a;
    }

    private function setCandidates(): void
    {
        foreach($this->events AS $event){

            if(! Candidate::query()
                ->where('version_id', $event->id)
                ->where('student_id', $this->studentId)
                ->exists()){

                $service = new MakeCandidateRecordsService(
                    $this->coTeacherIds,
                    $this->school->id,
                    $this->studentId,
                    $this->teacherId,
                    $event->id);
            }//else{

//                dd($this->events);
//            }
        }

    }

    private function setEpaymentVars(): void
    {
        $this->sandbox = false;

        if(isset($this->form->version)) {
            //PAYPAL
            $this->amountDue = $this->getAmountDue();
            $this->customProperties = $this->getCustomProperties();
            $this->email = auth()->user()->email;
            $this->epaymentId = $this->getEpaymentId();
            $this->feePaid = ConvertToUsdService::penniesToUsd($this->getFeePaid());
            $this->teacherName = $this->form->teacherFullName;
            $this->versionShortName = $this->form->version->short_name;
            $this->versionId = $this->form->versionId;
            //SQUARE
            $user = auth()->user();
            $address = Address::where('user_id', $user->id)->first();
            $student = Student::where('user_id', $user->id)->first();
            $this->firstName = $user->first_name;
            $this->lastName = $user->last_name;
            $this->email = $user->email;
            $this->phone = ($student->phoneMobile) ?: ($student->phoneHome ?: '');
            $this->addressLines = ($address)
                ? [$address->address1, $address->address2]
                : [];
            $this->city = ($address)
                ? $address->city
                : '';
            $this->geostateAbbr = ($address)
                ? $address->geostateAbbr
                : 'NJ';
        }
    }

    private function setEpaymentVarsRehearsals(array $rehearsals): void
    {
        //reinforcement
        $this->sandbox = false;

        /** @todo This assumes that student will be engaged in ONLY one rehearsal at a time, which will eventually be wrong. */
        $data = $rehearsals[array_key_first($rehearsals)];
        $versionId = $data['versionId'];
        $version = Version::find($versionId);

        //PAYPAL
        $this->amountDue = $data['participationAmountDue'];
        $this->customProperties = $this->getCustomProperties();
        $this->email = auth()->user()->email;
        $this->epaymentId = $this->getEpaymentId();
        $this->form->ePaymentId = $this->epaymentId;
        $this->feePaid = ConvertToUsdService::penniesToUsd($data['participationFeePaid']);
        //$this->teacherName = $this->form->teacherFullName;
        $this->versionShortName = "$version->short_name participation";
        $this->versionId = $version->id;
        $this->customProperties = $this->getCustomRehearsalProperties($versionId, $data['participationAmountDue']);
        //SQUARE
        $user = auth()->user();
        $address = Address::where('user_id', $user->id)->first();
        $student = Student::where('user_id', $user->id)->first();
        $this->firstName = $user->first_name;
        $this->lastName = $user->last_name;
        $this->email = $user->email;
        $this->phone = ($student->phoneMobile) ?: ($student->phoneHome ?: '');
        $this->addressLines = ($address)
            ? [$address->address1, $address->address2]
            : [];
        $this->city = ($address)
            ? $address->city
            : '';
        $this->geostateAbbr = ($address)
            ? $address->geostateAbbr
            : 'NJ';
    }

    /**
     * Register all eligible open events based on the coTeachers available for this student
     * @return void
     */
    private function setEvents(): void
    {
        $service = new FindTeacherOpenEventsService();

        foreach($this->coTeacherIds AS $teacherId){
            $this->events = array_merge($this->events, $service->getTeacherEvents($teacherId));
        }
    }

    private function setParticipationContracts(): array
    {
        $contracts = [];

        $service = new FindStudentOpenRehearsalsService($this->studentId);

        foreach($service->getRehearsals() AS $versionId){

            $version = Version::find($versionId);
            $participationContract = $version->participation_contract;

            $contracts[$versionId] = [
                'participationContract' => $participationContract,
            ];

        }

        return $contracts;
    }

    /**
     * @return array
     */
    private function setRehearsals(): array
    {
        $rehearsals = [];

        $service = new FindStudentOpenRehearsalsService($this->studentId);

        foreach($service->getRehearsals() AS $versionId) {

            //test for student acceptance into $versionId
            $candidateId = $this->getCandidateId($versionId);

            if ($candidateId) {
                $version = Version::find($versionId);

                $participationFee = $version->fee_participation;
                $participationFeePaid = $this->getParticipationFeePaid($versionId);
                $participationAmountDue = ($participationFee - $participationFeePaid);
                $shortName = $version->short_name;
                $ePayVendor = $version->epayment_vendor;

                $rehearsals[$versionId] = [
                    'versionId' => $versionId,
                    'versionShortName' => $shortName,
                    'ensembleName' => $this->getRehearsalEnsembleName($versionId),
                    'participationFee' => ConvertToUsdService::penniesToUsd($participationFee),
                    'participationFeePaid' => ConvertToUsdService::penniesToUsd($this->getParticipationFeePaid($versionId)),
                    'participationAmountDue' => ConvertToUsdService::penniesToUsd($participationAmountDue),
                    'ePayVendor' => $ePayVendor,
                ];

                $this->customProperties = $this->getCustomRehearsalProperties($versionId, $participationAmountDue);
            }
        }

        if(count($rehearsals)){
            $this->setEpaymentVarsRehearsals($rehearsals);
        }

        return $rehearsals;
    }

    /**
     * $this->showForms array is used to control which event form will be displayed
     * and will default to the first event version found
     * @return void
     */
    private function setShowForms(): void
    {
        //set $this->showForms key as the version id
        foreach($this->events AS $version){

            $this->showForms[$version->id] = false;
        }

        //if a versionId has not been set, default to settting the first row of the array to true
        $defaultVersionId = array_key_first($this->showForms);
        ($this->form->versionId)
            ? $this->showForms[$this->form->versionId] = true
            : $this->showForms[$defaultVersionId] = true;

        //set the variables in $this->form
        $this->setVersion($defaultVersionId);
    }
}
