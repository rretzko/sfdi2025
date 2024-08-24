<?php

use App\Models\Student;
use App\Models\VoicePart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $birthday = '';
    public string $classOf = '2024';
    public array $classOfs = [];
    public int $height = 36;
    public array $heights = [];
    public string $shirtSize = 'med';
    public array $shirtSizes = [];
    public int $voicePartId = 63;
    public array $voiceParts = [];

    public \App\Models\Student $student;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();

         $this->birthday = $this->student->birthday;
        $this->classOf = $this->student->class_of;
        $this->height = $this->student->height;
        $this->shirtSize = $this->student->shirt_size;
        $this->voicePartId = $this->student->voice_part_id;

        $this->classOfs = $this->buildClassOfs();
        $this->heights = $this->buildHeights();
        $this->shirtSizes = $this->buildShirtSizes();
        $this->voiceParts = $this->buildVoiceParts();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateStudentInformation(): void
    {

        $validated = $this->validate([
            'birthday' => ['nullable', 'string', 'min:10', 'max:10'],
            'classOf' => ['required', 'string', 'min:4', 'max:4'],
            'height' => ['nullable','int', 'min:36', 'max:94'],
            'shirtSize' => ['nullable', 'string'],
            'voicePartId' => ['required', 'int', 'exists:voice_parts,id'],
        ]);

        $this->student->update([
           'birthday' => $this->birthday,
           'class_of' => $this->classOf,
           'height' => $this->height,
           'shirt_size' => $this->shirtSize,
           'voice_part_id' => $this->voicePartId,
        ]);

        $this->dispatch('student-information-updated', name: $this->student->user->name);
    }

    private function buildClassOfs(): array
    {
        $a = [];
        $service = new \App\Services\CalcSeniorYearService();
        $seniorYear = $service->getSeniorYear();

        for($i=12; $i>3; $i--){
            $addYears = (12 - $i);
            $a[$seniorYear + $addYears] = (12 - $addYears);
        }

        return $a;
    }

    private function buildHeights(): array
    {
        $a = [];

        for($i=36; $i<94; $i++){

            $a[$i] = floor($i / 12) . "' " . ($i % 12) . '"';
        }

        return $a;
    }

    private function buildShirtSizes(): array
    {
        $a = [];
        $sizes = ['2xs', 'sx', 'sm', 'med', 'lg', 'xl', '2xl', '3xl', '4xl'];

        foreach($sizes AS $size){
            $a[$size] = $size;
        }

        return $a;

    }

    private function buildVoiceParts(): array
    {
        return VoicePart::query()
            ->select('voice_parts.id', 'voice_parts.descr', 'voice_parts.order_by')
            ->orderBy('voice_parts.order_by')
            ->pluck('voice_parts.descr', 'voice_parts.id')
            ->toArray();
    }

}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Student Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your student information.") }}
        </p>
    </header>

    <form wire:submit="updateStudentInformation" class="mt-6 space-y-6">
        <div>
            {{-- CLASS OF --}}
            <x-input-label for="classOf" :value="__('Grade')" />
            <select wire:model="classOf" required >
                @foreach($classOfs AS $year => $grade)
                    <option value="{{ $year }}">
                        {{ $grade }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('classOf')" />
        </div>

        {{-- VOICE PART --}}
        <div>
            <x-input-label for="voicePartId" :value="__('Voice Part')" />
            <select wire:model="voicePartId" required >
                @foreach($voiceParts AS $id => $descr)
                    <option value="{{ $id }}">
                        {{ $descr }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('voicePartId')" />
        </div>

        {{-- HEIGHT --}}
        <div>
            <x-input-label for="height" :value="__('Height')" />
            <select wire:model="height"  >
                @foreach($heights AS $id => $footInch)
                    <option value="{{ $id }}">
                        {{ $footInch }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('height')" />
        </div>

        {{-- SHIRT SIZE --}}
        <div>
            <x-input-label for="shirtSize" :value="__('Shirt Size')" />
            <select wire:model="shirtSize"  >
                @foreach($shirtSizes AS $id => $shirtSizeDescr)
                    <option value="{{ $shirtSizeDescr }}">
                        {{ $shirtSizeDescr }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('shirtSize')" />
        </div>

        {{-- BIRTHDAY --}}
        <div>
            <x-input-label for="birthday" :value="__('Birthday')" />
            <input type="date" wire:model="birthday" />
            <x-input-error class="mt-2" :messages="$errors->get('birthday')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="student-information-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
