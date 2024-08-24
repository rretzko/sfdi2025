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
    public array $schools = [];
    public Student $student;
    public int $teacherId = 0;
    public array $teachers = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();
        $this->schoolId = $this->student->schools()->wherePivot('active', 1)->first()->id;
        $this->teachers = $this->buildTeachers();
        $this->schools = $this->buildSchools();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateSchoolInformation(): void
    {
        $validated = $this->validate([
            'schoolId' => ['required', 'int', 'exists:schools,id'],
        ]);

        //set all schools to not-active
        //update/create $this->schoolId as active

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

    private function buildTeachers()
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
            <select wire:model="schoolId" required >
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

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
