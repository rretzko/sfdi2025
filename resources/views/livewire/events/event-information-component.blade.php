<section class="">

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Event Registration Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your event registration information.") }}
        </p>
    </header>

    {{-- QUALIFICATIONS --}}
    <div class="mt-4">
        <h3 class="font-semibold underline">Qualifications</h3>

        {{-- CANDIDATE ID (USED FOR TESTING --}}
{{--        <div class="flex flex-row">--}}
{{--            <label class="w-1/4 sm:w-1/6 lg:w-1/12">Candidate Id:</label>--}}
{{--            <div class="font-semibold">{{ $form->candidateId }}</div>--}}
{{--        </div>--}}

        {{-- SCHOOL NAME --}}
        <div class="flex flex-row">
            <label class="w-1/4 sm:w-1/6 lg:w-1/12">School:</label>
            <div class="font-semibold">{{ $schoolName }}</div>
        </div>

        {{-- TEACHERS --}}
        <div class="flex flex-row">
            <label class="w-1/4 sm:w-1/6 lg:w-1/12">Teacher(s):</label>
            <div class="font-semibold">{{ $teachersCsv }}</div>
        </div>

        {{-- GRADE --}}
        <div class="flex flex-row">
            <label class="w-1/4 sm:w-1/6 lg:w-1/12">Grade:</label>
            <div class="font-semibold">{{ $grade }}</div>
        </div>

        {{-- VOICE PART --}}
        <div class="flex flex-row">
            <label class="w-1/4 sm:w-1/6 lg:w-1/12">Voice Part:</label>
            <div class="font-semibold">{{ $defaultVoicePartDescr }}</div>
        </div>

        {{-- EVENTS --}}
        <div class="flex flex-row">
            <label class="w-1/4 sm:w-1/6 lg:w-1/12">Events:</label>
            <div class="font-semibold">{{ $eventsCsv }}</div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="flex flex-row space-x-0.5 my-4 pt-4 border border-transparent border-t-gray-300">
        @if(count($events) > 1)
            @foreach($events AS $event)
                <button
                    wire:click="setVersion({{ $event->id }})"
                    @class([
            "px-2 border border-gray-800 border-b-transparent rounded-l-md rounded-r-md",
            "bg-white" => ($showForms[$event->id]),
            "font-semibold" => ($showForms[$event->id]),
            "bg-gray-300" => (! $showForms[$event->id]),
            "text-gray-500" => (! $showForms[$event->id]),
                ])
                >
                    {{ $event->name }}
                </button>
            @endforeach

        @endif
    </div>

    {{-- REGISTRATION FORM --}}
    @if(isset($form->version))
        <div class="flex flex-col">

        {{-- PROGRAM NAME --}}
        <div class="flex flex-col mb-2">
            <label for="form.programName">Name as it should appear in the program</label>
            <div class="flex flex-row space-x-2">
                <input type="text" wire:model.live.debounce.500ms="form.programName" class="max-w-md" required/>
                {{-- SAVED MESSAGE --}}
                <div class="mt-6 ml-4">
                    <x-action-message class="me-3 text-green-600 self-center" on="program-name-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </div>
            @error('programName')
                <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- VOICE PART --}}
        <div class="flex flex-col mb-2">
            <label for="form.voicePartId">Auditioning on Voice Part</label>
            <div class="flex flex-row space-x-2">
                <select wire:model.live="form.voicePartId" class="w-fit">
                    <option value="0">- select -</option>
                    @foreach($form->voiceParts AS $id => $descr)
                        <option value="{{ $id }}">
                            {{ $descr }}
                        </option>
                    @endforeach
                </select>
                {{-- SAVED MESSAGE --}}
                <div class="mt-6 ml-4">
                    <x-action-message class="me-3 text-green-600 self-center" on="voice-part-id-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </div>
            @error('form.voicePartId')
            <div class="text-red-600">{{ $message }}</div>
            @enderror
        </div>

        {{-- HOME ADDRESS --}}
        @if($requiresHomeAddress)
            @include('components.partials.home-address')
        @endif {{-- end of requiresHomeAddress --}}

        {{-- EMERGENCY CONTACTS --}}
        <fieldset class="flex flex-col my-2 pt-2 border border-transparent border-t-gray-300">
            <label for="" class="font-semibold">Emergency Contact(s)
                <span class="text-xs italic"> (Select one)</span>
            </label>

            @forelse($emergencyContacts AS $key => $emergencyContact)

                <div class="flex flex-row space-x-2" wire:key="ec-{{ $emergencyContact['id'] }}">
                    <input type="radio"
                           wire:model.live="form.emergencyContactId"
                           value="{{ $emergencyContact['id'] }}"
                           class="self-center"
                           @disabled($emergencyContact['bestPhone'] === 'missing')
                    >
                    <label for="form.emergencyContactId"
                        class="@if($emergencyContact['bestPhone'] === 'missing') text-red-600 @endif"
                    >
                        {{ $emergencyContact['name'] }} (Best Phone: {{ $emergencyContact['bestPhone'] }})
                    </label>
                </div>
            @empty
                <div class="text-red-600">
                    No Emergency Contacts found.  Please add your emergency contact information using
                    the "Emergency Contacts" link at the top of this page.
                </div>
            @endforelse
            {{-- SAVED MESSAGE --}}
            <div class="mt-6 ml-4">
                <x-action-message class="me-3 text-green-600 self-center" on="emergency-contact-id-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </fieldset>

        {{-- APPLICATION --}}
        <fieldset class="flex flex-col my-2 pt-2 border border-transparent border-t-gray-300">
            <label for="" class="font-semibold">Application</label>

            {{-- PRE-CHECK ERRORS --}}
            @include('components.partials.pre-check-errors')

            {{-- APPLICATION --}}
            @if($form->eapplication)
                @include("components.partials.eapplications.versions.$form->versionId.eapplication")
            @else
                <div>
                    <button
                        wire:click="downloadApp()"
                        type="button"
                        class="bg-indigo-500 text-white text-xs px-2 rounded-lg shadow-lg"
                    >
                        Click to download your application
                    </button>
                </div>
            @endif

        </fieldset>

        {{-- UPLOADS --}}
        @if($form->uploadTypesCount)
            @include('components.partials.audition-recordings')
        @endif

        {{-- PITCH FILES --}}
        @if($form->hasPitchFiles)
            @include('components.partials.pitch-files')
        @endif

        {{-- ePayment --}}
        <fieldset class="flex flex-col my-2 pt-2 border border-transparent border-t-gray-300 border-b-gray-300">
            <label for="" class="font-semibold">Payments</label>
            @if($form->ePay)
                @if($amountDue)
                    @include('components.partials.payPal' )
                @else
                    <div class="ml-4 py-2">
                        Fee Paid: ${{ $feePaid }}
                    </div>
                @endif
            @else
                <div class="ml-4 py-2">
                    Please see your teacher ({{ $teacherName }}) for all payments.
                </div>
            @endif
        </fieldset>

    </div>
    @endif

</section>