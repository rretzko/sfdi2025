<?php

use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $schoolId = '';
    public bool $schoolInformationUpdated = false;
    public array $schools = [];
    public Student $student;
    public int $studentId = 0;
    public int $teacherId = 0;
    public array $teachers = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();
        $this->studentId = $this->student->id;
        $this->schoolId = $this->student->schools()->wherePivot('active', 1)->first()->id ?? 0;
        $this->teacherId = $this->getTeacherId();
        $this->teachers = $this->buildTeachers();
        $this->schools = $this->buildSchools();
    }

    /**
     * Update the teachers' drop-down box whenever the schoolId changes
     * @return void
     */
    public function updatedSchoolId(): void
    {
        $this->teachers = $this->buildTeachers();

        //set first teacher as default teacher it
        $this->teacherId = array_key_first($this->teachers);
    }

    /**
     * Update the school information for the currently authenticated user.
     */
    public function updateSchoolInformation(): void
    {
        $this->reset('schoolInformationUpdated');

        $validated = $this->validate([
            'schoolId' => ['required', 'int', 'exists:schools,id'],
            'teacherId' => ['required','int', 'exists:teachers,id'],
        ]);

        //set all schools to not-active
        $this->setSchoolsToInActive();

        //update/create $this->schoolId as active
        $this->setSelectedSchoolToActive();

        //update/create student-teacher relationship
        $this->setStudentTeacher();

        //send courtesy  email to teacher
        $this->sendNewStudentEmail();

        $this->schoolInformationUpdated = true;

        $this->dispatch('school-information-updated', name: $this->student->user->name);
    }

    private function buildSchools(): array
    {
        return School::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->pluck('name','id')
            ->toArray();
    }

    private function buildTeachers(): array
    {
        //early exit
        if(! $this->schoolId){
            return [];
        }

        $teacherIds = \Illuminate\Support\Facades\DB::table('school_teacher')
            ->where('school_id', $this->schoolId)
            ->where('active', 1)
            ->select('teacher_id')
            ->pluck('teacher_id')
            ->toArray();

        return \App\Models\Teacher::query()
            ->join('users', 'users.id', '=', 'teachers.user_id')
            ->whereIn('teachers.id', $teacherIds)
            ->select('teachers.id','users.name','users.last_name','users.first_name')
            ->orderBy('users.last_name')
            ->orderBy('users.first_name')
            ->pluck('users.name','teachers.id')
            ->toArray();
    }

    private function sendNewStudentEmail(): void
    {
        //default: send email to founder
        if(\Illuminate\Support\Facades\App::isLocal() || (! $this->teacherId)){
            $teacher = \App\Models\Teacher::find(config('app.founder'));
        }

        if(\Illuminate\Support\Facades\App::isProduction() && $this->teacherId){
                $teacher = \App\Models\Teacher::find($this->teacherId);
        }

        $student = Student::find(auth()->id());
        $studentId = $student->id;
        $studentName = $student->user->name;

        \Illuminate\Support\Facades\Log::info('*** Sending new student email for: ' . $studentName . '(id: ' . $studentId . ') to: ' . $teacher->user->name . '. ***');

        Illuminate\Support\Facades\Mail::to($teacher->user)->send( new \App\Mail\StudentAddedToRosterMail($teacher));

    }

    private function getTeacherId(): int
    {
        return \Illuminate\Support\Facades\DB::table('student_teacher')
            ->where('student_id', $this->studentId)
            ->first()
            ->teacher_id ?? 0;
    }

    private function setSchoolsToInactive(): void
    {
        \Illuminate\Support\Facades\DB::table('school_student')
            ->where('student_id', $this->studentId)
            ->update(['active' => 0]);
    }

    private function setSelectedSchoolToActive():void
    {
        \Illuminate\Support\Facades\DB::table('school_student')
            ->where('student_id', $this->studentId)
            ->where('school_id', $this->schoolId)
            ->exists()
            ? \Illuminate\Support\Facades\DB::table('school_student')
            ->where('student_id', $this->studentId)
            ->where('school_id', $this->schoolId)
            ->update(['active' => 1])
            : \Illuminate\Support\Facades\DB::table('school_student')
            ->insert([
                'student_id' => $this->studentId,
                'school_id' => $this->schoolId,
                'active' => 1,
            ]);
    }

    private function setStudentTeacher(): void
    {
        if(\Illuminate\Support\Facades\DB::table('student_teacher')
            ->where('student_id', $this->studentId)
            ->where('teacher_id', $this->teacherId)
            ->exists()){

            //update the updated_at field to make $this->teacherId the latest teacher
            $studentTeacher = \App\Models\StudentTeacher::query()
                ->where('student_id', $this->studentId)
                ->where('teacher_id', $this->teacherId)
                ->first();
            $studentTeacher->touch();

        }else{

            \Illuminate\Support\Facades\DB::table('student_teacher')
                ->insert([
                    'student_id' => $this->studentId,
                    'teacher_id' => $this->teacherId,
                        ]);
            }
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('School Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your school information.") }}
        </p>
    </header>

    <form wire:submit="updateSchoolInformation" class="mt-6 space-y-6">

        <div>
            {{-- SCHOOL --}}
            <x-input-label for="schoolId" :value="__('School')" />
            <select wire:model.live="schoolId" required >
                @foreach($schools AS $id => $schoolName)
                    <option value="{{ $id }}">
                        {{ $schoolName }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('schoolId')" />
        </div>

        <div>
            {{-- TEACHERS --}}
            <x-input-label for="teacherId" :value="__('Teacher(s)')" />
            <select wire:model="teacherId" required >
                @foreach($teachers AS $id => $teacherName)
                    <option value="{{ $id }}">
                        {{ $teacherName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="school-information-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
