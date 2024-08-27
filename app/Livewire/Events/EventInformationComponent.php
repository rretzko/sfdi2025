<?php

namespace App\Livewire\Events;

use App\Models\Candidate;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentTeacher;
use App\Models\Teacher;
use App\Models\VoicePart;
use App\Services\CalcGradeFromClassOfService;
use App\Services\CoTeachersService;
use App\Services\FindTeacherOpenEventsService;
use App\Services\MakeCandidateRecordsService;
use Livewire\Component;

class EventInformationComponent extends Component
{
    public array $coTeacherIds = [];
    public string $defaultVoicePartDescr = '';
    public array $eligibleVersions = [];
    public array $events = [];
    public string $eventsCsv = 'No events found.';
    public int $grade=4;
    public School $school;
    public string $schoolName = '';
    public Student $student;
    public string $teachersCsv = '';
    public int $teacherId = 0;

    public function mount()
    {
        $gradeService = new CalcGradeFromClassOfService();

        $this->student = Student::where('user_id', auth()->id())->first();

        $this->defaultVoicePartDescr = VoicePart::find($this->student->voice_part_id)->descr;
        $this->eligibleVersions = $this->student->getEligibleVersions(); // <======   START HERE
        $this->grade = $gradeService->getGrade($this->student->class_of);
        $this->school = $this->student->activeSchool();
        $this->schoolName = $this->school->name;
        $this->teachersCsv = $this->getTeachersCsv();
        $this->eventsCsv = $this->getEventsCsv();

        //ensure that a row exists for auth()->user() for open events
        $this->setCandidateRow();

    }
    public function render()
    {
        return view('livewire.events.event-information-component');
    }

    private function getEventsCsv(): string
    {
        $service = new FindTeacherOpenEventsService();

        foreach($this->coTeacherIds AS $teacherId){
            $this->events = $service->getTeacherEvents($teacherId);
        }

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
            ->where('student_teacher.student_id', $this->student->id)
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

    private function setCandidateRow()
    {
        foreach($this->events AS $event){

            if(! Candidate::query()
                ->where('version_id', $event->id)
                ->where('student_id', $this->student->id)
                ->exists()){

                $service = new MakeCandidateRecordsService(
                    $this->coTeacherIds,
                    $this->school->id,
                    $this->student->id,
                    $this->teacherId,
                    $event->id);
            }else{
                dd($this->events);
            }
        }
    }
}
