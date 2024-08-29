<div class="flex flex-col">

    {{-- MISSING VOICE PART --}}
    @if(! $form->voicePartId)
        <div class="text-red-600">
            You must select and save a voice part before you can complete your application.
        </div>
    @endif

    {{-- MISSING EMERGENCY CONTACT --}}
    @if(! $form->emergencyContactId)
        <div class="text-red-600">
            You must select and save an emergency contact before you can complete your application.
        </div>
        <div>EC Id: {{ $form->emergencyContactId }}</div>
    @endif

    {{-- MISSING EMERGENCY CONTACT BEST PHONE --}}
    @if($form->emergencyContactId && $form->emergencyContactBestPhone === 'missing')
        <div class="text-red-600">
            Your emergency contact must have a 'best phone' selected before you can complete your application.
        </div>
    @endif

</div>{{-- END OF PRE-CHECK ERRORS --}}
