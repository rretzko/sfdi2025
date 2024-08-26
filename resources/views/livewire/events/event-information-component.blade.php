<section class="">

    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Event Registration Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your event registration information.") }}
        </p>
    </header>

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
    </div>

    <div class="mt-8">
        <h2 class=" font-semibold underline">To Dos</h2>
        <ul class="ml-4 list-decimal">
            <li class="line-through">Find School</li>
            <li class="line-through">Find Teachers</li>
            <li class="line-through">Find Student Grade</li>
            <li class="line-through">Find Student Default voice part</li>
            <li>Find Open events with these teachers as obligated or better</li>
            <li>Validate User properties against event requirements.</li>
            <li>Provide actionable information for user to rectify or return to teacher, and/or</li>
            <li>Ensure that candidate row exists</li>
            <li>Open populated event registration form.</li>
        </ul>
    </div>

</section>
