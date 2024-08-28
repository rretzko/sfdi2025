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
    <div class="flex flex-col">

        {{-- PROGRAM NAME --}}
        <div class="flex flex-col">
            <label for="form.programName">Name as it should appear in the program</label>
            <input type="text" wire:model="form.programName" class="max-w-md"/>
        </div>

        <div class="flex flex-col">
            <label for="form.voicePartId">Auditioning on Voice Part</label>
            <select wire:model="form.voicePartId">
                @foreach($voiceParts AS $voicePart)
                    <option value="{{ $voicePart['id'] }}">
                        {{ $voicePart['descr'] }}
                    </option>
                @endforeach
            </select>
            <input type="text" wire:model="form.programName" class="max-w-md"/>
        </div>

    </div>

    <div class="mt-8">
        <h2 class=" font-semibold underline">To Dos</h2>
        <ul class="ml-4 list-decimal">
            <li class="line-through">Find School</li>
            <li class="line-through">Find Teachers</li>
            <li class="line-through">Find Student Grade</li>
            <li class="line-through">Find Student Default voice part</li>
            <li class="line-through">Find Open events with these teachers as obligated or better</li>
            <li class="line-through">Validate User properties against event requirements.</li>
            <li class="line-through">Provide actionable information for user to rectify or return to teacher, and/or</li>
            <li class="line-through">Ensure that candidate row exists</li>
            <li>Open populated event registration form.</li>
        </ul>
    </div>

</section>
