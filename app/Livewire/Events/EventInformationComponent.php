<?php

namespace App\Livewire\Events;

use App\Livewire\Forms\VersionRegistrationForm;
use App\Models\Candidate;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTeacher;
use App\Models\Teacher;
use App\Models\Version;
use App\Models\VoicePart;
use App\Services\CalcGradeFromClassOfService;
use App\Services\CoTeachersService;
use App\Services\FindTeacherOpenEventsService;
use App\Services\MakeCandidateRecordsService;
use Livewire\Component;

class EventInformationComponent extends Component
{
    public VersionRegistrationForm $form;
    public array $coTeacherIds = [];
    public string $defaultVoicePartDescr = '';
    public array $eligibleVersions = [];
    public array $events = []; //synonym for versions
    public string $eventsCsv = 'No events found.';
    public int $grade=4;
    public array $programNames = [];
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

        $this->defaultVoicePartDescr = VoicePart::find($this->student->voice_part_id)->descr;
        $this->eligibleVersions = $this->student->getEligibleVersions(); // <======   START HERE
        $this->grade = $gradeService->getGrade($this->student->class_of);
        $this->school = $this->student->activeSchool();
        $this->schoolName = $this->school->name;
        $this->teachersCsv = $this->getTeachersCsv();
        $this->teacherId = $this->getTeacherId();
        $this->eventsCsv = $this->getEventsCsv();
        $this->voiceParts = $this->getVoiceParts(); //of event ensemble(s) voice parts

        //ensure that a row exists for auth()->user() for open events
        $this->setCandidateRow();

    }
    public function render()
    {
        return view('livewire.events.event-information-component');
    }

    public function setVersion(int $versionId): void
    {
        $this->form->setVersion($versionId);
    }

    private function getEventsCsv(): string
    {
        $service = new FindTeacherOpenEventsService();

        foreach($this->coTeacherIds AS $teacherId){
            $this->events = $service->getTeacherEvents($teacherId);
        }

        //hide/display registration forms
        $this->setShowForms();

        //early exit
        if(! count($this->events)){
            return 'No events found. Please see your teacher if you expected to find open events.';
        }

        //isolate version names into an array
        $names = array_column($this->events, 'name');

        return implode(' | ', $names);
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

        dd($event->voiceParts());
    }

    private function setCandidateRow(): void
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

    private function setShowForms(): void
    {
        foreach($this->events AS $version){

            $this->showForms[$version->id] = false;
        }

        //default to settting the first row of the array to true
        $defaultVersionId = array_key_first($this->showForms);
        $this->showForms[$defaultVersionId] = true;
        $this->setVersion($defaultVersionId);
    }
}
