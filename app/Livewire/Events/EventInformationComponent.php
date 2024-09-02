<?php

namespace App\Livewire\Events;

use App\Livewire\Forms\VersionRegistrationForm;
use App\Models\Candidate;
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
use App\Services\CoTeachersService;
use App\Services\FindTeacherOpenEventsService;
use App\Services\MakeCandidateRecordsService;
use App\Services\PathToRegistrationService;
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
    public array $programNames = [];
    public bool $requiresHomeAddress = false;

    public School $school;
    public string $schoolName = '';
    public array $showForms = [];
    public Student $student;
    public int $studentId = 0;
    public string $teachersCsv = '';
    public int $teacherId = 0;
    public array $voiceParts = [];

    public function mount()
    {
        $gradeService = new CalcGradeFromClassOfService();

        $this->student = Student::where('user_id', auth()->id())->first();
        $this->studentId = $this->student->id;
        $this->emergencyContacts = $this->getEmergencyContacts();

        $this->defaultVoicePartDescr = VoicePart::find($this->student->voice_part_id)->descr;
        $this->grade = $gradeService->getGrade($this->student->class_of);

        $this->school = $this->student->activeSchool();
        $this->schoolName = $this->school->name;
        $this->teachersCsv = $this->getTeachersCsv();
        $this->teacherId = $this->getTeacherId();

        /**
         * @todo refactor $this->events to $this->versions
         * @todo isolate the instantiation of $this->events
         */
        $this->eventsCsv = $this->getEventsCsv(); //also sets $this->events
        $this->voiceParts = $this->getVoiceParts(); //of event ensemble(s) voice parts
        $this->requiresHomeAddress = $this->getRequiresHomeAddress();

        $this->geostates = Geostate::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.events.event-information-component');
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

        $this->form->recordings[$key]['url'] = 'recordings/'.$fileName;

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

    private function getEventsCsv(): string
    {
        //ensure that events are available
        $this->setEvents();

        //early exit
        if(! count($this->events)){
            return 'No events found. Please see your teacher if you expected to find open events.';
        }

        //isolate version names into an array
        $names = array_column($this->events, 'name');

        //ensure that each event has a candidate row (if eligible)
        $this->setCandidates();

        //hide/display registration forms
        $this->setShowForms();

        return implode(' | ', $names);
    }

    private function getRequiresHomeAddress(): bool
    {
        $version = Version::find($this->form->versionId);

        return (bool)!$version->student_home_address;
    }

    private function getTeachersCsv(): string
    {
        $a = [];

        //isolate the teacher ids based on the user's school
        $schoolTeacherIds = $this->school->teachers->pluck('id')->toArray();

        //filter the initial list to teacher's for the user student
        $studentTeacherIds = StudentTeacher::query()
            ->where('student_teacher.student_id', $this->studentId)
            ->whereIn('student_teacher.teacher_id', $schoolTeacherIds)
            ->pluck('student_teacher.teacher_id')
            ->toArray();

        //identify the teacher's co-teachers
        $studentCoTeacherIds = CoTeachersService::getStudentCoTeachersIds($studentTeacherIds);

        //isolate the teacher objects based on the $studentCoTeacherIds
        $teachers = Teacher::find($studentCoTeacherIds);

        //isolate the teacher names from the $teachers collection
        foreach($teachers AS $teacher){

            $this->coTeacherIds[] = $teacher->id;

            $a[] = $teacher->user->name;
        }

        return implode(', ', $a);
    }

    /**
     * Use the last teacher identified with $user student
     * @return int
     */
    private function getTeacherId(): int
    {
        return StudentTeacher::query()
            ->where('student_id', $this->studentId)
            ->latest('id')
            ->first()
            ->teacher_id;
    }

    private function getVoiceParts(): array
    {
        $version = Version::find($this->form->versionId);
        $event = $version->event;
        $ensembles = $event->eventEnsembles;

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

    /**
     * Register all eligible open events based on the coTeachers available for this student
     * @return void
     */
    private function setEvents(): void
    {
        $service = new FindTeacherOpenEventsService();

        foreach($this->coTeacherIds AS $teacherId){
            $this->events = $service->getTeacherEvents($teacherId);
        }
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

        //default to settting the first row of the array to true
        $defaultVersionId = array_key_first($this->showForms);
        $this->showForms[$defaultVersionId] = true;

        //set the variables in $this->form
        $this->setVersion($defaultVersionId);
    }
}
