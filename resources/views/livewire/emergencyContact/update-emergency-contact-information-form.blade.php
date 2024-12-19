<?php

use App\Models\School;
use App\Models\Student;
use App\Services\FormatPhoneService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $bestPhone = 'mobile'; //default
    public int $emergencyContactId = 0;
    public array $emergencyContacts = [];
    #[\Livewire\Attributes\Validate('nullable|email:rfc,dns')]
    public string $emergencyContactEmail = '';
    public string $emergencyContactName = '';
    public int $emergencyContactTypeId = 1; //default = mother
    public array $emergencyContactTypes = [];
    public  FormatPhoneService $formatPhoneService;
    public string $phoneHome = '';
    public string $phoneMobile = '';
    public string $phoneWork = '';
    public bool $showForm = false;
    public Student $student;
    public int $studentId = 0;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->student = Student::where('user_id', auth()->id())->first();

        $this->studentId = $this->student->id;
        $this->emergencyContacts = $this->getEmergencyContacts();

        $this->emergencyContactTypes = \App\Models\EmergencyContactType::orderBy('order_by')
            ->pluck('relationship','id')
            ->toArray();

        $this->formatPhoneService = new FormatPhoneService();
    }

    public function removeEmergencyContact(int $emergencyContactId): void
    {
        $emergencyContact = \App\Models\EmergencyContact::find($emergencyContactId) ?? false;

        if($emergencyContact){

            $name = $emergencyContact->name;

            $emergencyContact->delete();

            //refresh EmergencyContacts array
            $this->emergencyContacts = $this->getEmergencyContacts();

            //send page message
            $this->dispatch('emergency-contact-information-removed', name: $name);
        }


    }

    public function setEmergencyContact(int $emergencyContactId): void
    {
        if($emergencyContactId){

            $emergencyContact = \App\Models\EmergencyContact::find($emergencyContactId) ?? false;

            if($emergencyContact){
                $this->bestPhone = $emergencyContact->best_phone;
                $this->emergencyContactEmail = $emergencyContact->email;
                $this->emergencyContactId = $emergencyContact->id;
                $this->emergencyContactName = $emergencyContact->name;
                $this->emergencyContactTypeId = $emergencyContact->emergency_contact_type_id;
                $this->phoneHome = $emergencyContact->phone_home;
                $this->phoneMobile = $emergencyContact->phone_mobile;
                $this->phoneWork = $emergencyContact->phone_work;

                $this->showForm = true;
            }
        }
    }

    /**
     * Update the emergency contact information for the currently authenticated user.
     */
    public function updateEmergencyContactInformation(): void
    {
        $validated = $this->validate([
            'bestPhone' => ['required', 'string', Rule::in(['home','mobile','work'])],
            'emergencyContactEmail' => ['nullable','email:rfc,dns'],
            'emergencyContactName' => ['nullable','string'],
            'emergencyContactTypeId' => ['required','int', 'exists:emergency_contact_types,id'],
            'phoneHome' => ['nullable','string'],
            'phoneMobile' => ['nullable','string'],
            'phoneWork' => ['nullable','string'],
        ]);

        $res = $this->emergencyContactId
            ? $this->updateEmergencyContact($this->emergencyContactId )
            : $this->addEmergencyContact();

        if($res){

            //reset vars to default values
            $this->resetEmergencyContact();

            //refresh EmergencyContacts array
            $this->emergencyContacts = $this->getEmergencyContacts();

            //send page message
            $this->dispatch('emergency-contact-information-updated', name: $this->student->user->name);
        }
    }

    private function addEmergencyContact(): bool
    {
        return (bool)\App\Models\EmergencyContact::create(
            [
                'name' => $this->emergencyContactName,
                'email' => $this->emergencyContactEmail,
                'best_phone' => $this->bestPhone,
                'emergencyContactEmail' => $this->emergencyContactEmail,
                'emergencyContactName' => $this->emergencyContactName,
                'emergency_contact_type_id' => $this->emergencyContactTypeId,
                'phone_home' => $this->formatPhoneService->getPhoneNumber($this->phoneHome),
                'phone_mobile' => $this->formatPhoneService->getPhoneNumber($this->phoneMobile),
                'phone_work' => $this->formatPhoneService->getPhoneNumber($this->phoneWork),
                'student_id' => $this->studentId,
            ]
        );
    }

    private function updateEmergencyContact(): bool
    {
        $emergencyContact = \App\Models\EmergencyContact::find($this->emergencyContactId);

        return (bool)$emergencyContact->update(
            [
                'best_phone' => $this->bestPhone,
                'email' => $this->emergencyContactEmail,
                'name' => $this->emergencyContactName,
                'emergency_contact_type_id' => $this->emergencyContactTypeId,
                'phone_home' => $this->formatPhoneService->getPhoneNumber($this->phoneHome),
                'phone_mobile' => $this->formatPhoneService->getPhoneNumber($this->phoneMobile),
                'phone_work' => $this->formatPhoneService->getPhoneNumber($this->phoneWork),
            ]
        );
    }

    private function getEmergencyContacts(): array
    {
        return \Illuminate\Support\Facades\DB::table('emergency_contacts')
            ->join('emergency_contact_types', 'emergency_contact_types.id', '=', 'emergency_contacts.emergency_contact_type_id')
            ->where('student_id', $this->studentId)
            ->select('emergency_contacts.*',
                'emergency_contact_types.id AS emergencyContactTypeId','emergency_contact_types.relationship')
            ->get()
            ->toArray();
    }

    private function resetEmergencyContact(): void
    {
        $this->reset('bestPhone','emergencyContactId','emergencyContactEmail','emergencyContactName',
        'phoneHome','phoneMobile','phoneWork','showForm');
    }

}; ?>

<section class="">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Emergency Contact Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your emergency contact(s).") }}
        </p>
    </header>

    {{-- ADD BUTTON --}}
    <div class="flex justify-end mr-12">
        <button
            wire:click="$toggle('showForm')"
            class="bg-green-500 text-white text-2xl rounded-lg px-1 mb-2"
            type="button"
            title="Add New Emergency Contact"
        >
            +
        </button>
    </div>

    <style>
        .center-content {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            border-bottom: transparent;
            padding-top: 0.25rem;
        }
    </style>
    <table class="w-full shadow-lg">
        <thead>
        <tr>
            <th class="border border-gray-300 text-center ">Name</th>
            <th class="hidden md:table-cell border border-gray-300 text-center">Email</th>
            <th class="hidden sm:table-cell border border-gray-300 text-center">Phones</th>
            <th class="border border-gray-300 text-center w-1/6 sr-only">Edit</th>
            <th class="border border-gray-300 text-center w-1/6 sr-only">Remove</th>

        </tr>
        </thead>
        <tbody>
        @forelse($emergencyContacts AS $row)
            <tr>
                <td class="border border-gray-300 text-left align-top px-2">
                    {{ $row->name }} <span class="text-xs italic">({{$row->relationship}})</span>
                </td>
                <td class="hidden md:table-cell border border-gray-300 text-left align-top px-2">
                    {{ $row->email }}
                </td>
                <td class="hidden sm:table-cell border border-gray-300 text-left align-top px-2">
                    <div>
                        (c) {{ $row->phone_mobile }}
                        @if($row->best_phone === 'mobile')
                            <span class="text-xs">(Best)</span>
                        @endif
                    </div>
                    <div>
                        (h) {{ $row->phone_home }}
                        @if($row->best_phone === 'home')
                            <span class="text-xs">(Best)</span>
                        @endif
                    </div>
                    <div>
                        (w) {{ $row->phone_work }}
                        @if($row->best_phone === 'work')
                            <span class="text-xs">(Best)</span>
                        @endif
                    </div>
                </td>
                <td class="border border-gray-300 align-top center-content h-10">
                    <button
                        wire:click="setEmergencyContact({{ $row->id }})"
                        class="text-xs text-white bg-indigo-600 rounded-lg px-2"
                        type="button"
                    >
                        Edit
                    </button>
                </td>
                <td class="border border-gray-300 align-top px-2 center-content h-10">
                    <button
                        wire:click="removeEmergencyContact({{ $row->id }})"
                        class="text-xs text-white bg-red-600 rounded-lg px-2"
                        type="button"
                    >
                        Remove
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">No Emergency Contacts found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- SAVED MESSAGE --}}
    <div class="mt-6 ml-4">
        <x-action-message class="me-3" on="emergency-contact-information-updated">
            {{ __('Saved.') }}
        </x-action-message>
    </div>

    {{-- REMOVED MESSAGE --}}
    <div class="mt-6 ml-4">
        <x-action-message class="me-3" on="emergency-contact-information-removed">
            {{ __('Emergency Contact Removed.') }}
        </x-action-message>
    </div>

    @if($showForm)

        <form wire:submit="updateEmergencyContactInformation" class="mt-6 space-y-6 pt-4 border border-white border-t-gray-300">

            {{-- EMERGENCY CONTACT TYPE --}}
            <div>
                <x-input-label for="emergencyContactTypeId" :value="__('Relationship')" />
                <select wire:model="emergencyContactTypeId">
                    @foreach($emergencyContactTypes AS $key => $value)
                        <option value="{{ $key }}">
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                {{-- NAME --}}
                <x-input-label for="emergencyContactName" :value="__('Emergency Contact Name')" />
                <input type="text" wire:model="emergencyContactName" required/>
                <x-input-error class="mt-2" :messages="$errors->get('emergencyContactName')" />
            </div>

            <div>
                {{-- EMAIL --}}
                <x-input-label for="emergencyContactEmail" :value="__('Emergency Contact Email')" />
                <input type="email" wire:model.live="emergencyContactEmail" />
                <x-input-error class="mt-2" :messages="$errors->get('emergencyContactEmail')" />
            </div>

            <fieldset>
                <x-input-label for="" :value="__('Phones')" class="font-semibold uppercase"/>

                {{-- PHONE MOBILE --}}
                <div>
                    <x-input-label for="phoneMobile" :value="__('Cell Phone')" class="px-2"/>
                    <input type="text" wire:model.live="phoneMobile"/>
                    <input type="radio" wire:model.live="bestPhone" value="mobile"
                        @disabled(strlen($phoneMobile) < 1)
                    />
                    <span>Best Phone</span>
                    <x-input-error class="mt-2" :messages="$errors->get('phoneMobile')"/>
                </div>

                {{-- PHONE HOME --}}
                <div>
                    <x-input-label for="phoneHome" :value="__('Home Phone')" class="px-2"/>
                    <input type="text" wire:model.live="phoneHome"/>
                    <input type="radio" wire:model.live="bestPhone" value="home"
                        @disabled(strlen($phoneHome) < 1)
                    />
                    <span>Best Phone</span>
                    <x-input-error class="mt-2" :messages="$errors->get('phoneHome')"/>
                </div>

                {{-- PHONE WORK --}}
                <div>
                    <x-input-label for="phoneWork" :value="__('Work Phone')" class="px-2"/>
                    <input type="text" wire:model.live="phoneWork"/>
                    <input type="radio" wire:model.live="bestPhone" value="work"
                        @disabled(strlen($phoneWork) < 1)
                    />
                    <span>Best Phone</span>
                    <x-input-error class="mt-2" :messages="$errors->get('phoneWork')"/>
                </div>

            </fieldset>

            <div class="flex items-center gap-4">
                <x-primary-button>{{ __('Save') }}</x-primary-button>

                <x-action-message class="me-3" on="emergency-contact-information-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>

        </form>

    @endif

</section>
